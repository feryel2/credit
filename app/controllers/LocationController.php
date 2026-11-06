<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/Equipment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../services/PdfService.php';

class LocationController extends Controller
{
    private function validDate(string $date): bool
    {
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    private function durationInDays(string $startDate, string $endDate): int
    {
        return (int)(new DateTimeImmutable($startDate))->diff(new DateTimeImmutable($endDate))->days + 1;
    }

    private function calculateAdditionalFees(float $subtotal, string $returnCondition): float
    {
        return match ($returnCondition) {
            'good' => 0.0,
            'partial' => round($subtotal * 0.10, 2),
            'damaged' => round($subtotal * 0.25, 2),
            default => 0.0,
        };
    }

    private function addError(array &$errors, string $field, string $message): void
    {
        $errors[$field][] = $message;
    }

    public function index(): void
    {
        $user = $this->requireRole(['responsable_inventaire', 'agent_location', 'client']);

        $pdo = Database::getConnection();
        $locations = Location::all($pdo);

        if (($user['role'] ?? '') === 'client') {
            $locations = array_values(array_filter($locations, static fn (array $location): bool => (int)($location['client_id'] ?? 0) === (int)($user['id'] ?? 0)));
        }

        foreach ($locations as &$location) {
            $location['payments'] = Payment::forLocation($pdo, (int)$location['id']);
            $location['paid_amount'] = Payment::totalForLocation($pdo, (int)$location['id']);
            $location['balance_due'] = max(0, round((float)$location['total_amount'] - $location['paid_amount'], 2));
        }
        unset($location);

        $this->render('locations/index', ['locations' => $locations]);
    }

    public function create(): void
    {
        $user = $this->requireAuth();
        $this->requireRole(['responsable_inventaire', 'agent_location', 'client']);

        $pdo = Database::getConnection();
        $equipments = Equipment::getAvailable($pdo);
        $clients = User::allClients($pdo);
        $selectedEquipmentId = (int)($_GET['equipment_id'] ?? 0);

        if ($this->isPost()) {
            $equipmentId = (int)($_POST['equipment_id'] ?? 0);
            $clientId = ($user['role'] ?? '') === 'client'
                ? (int)$user['id']
                : (int)($_POST['client_id'] ?? 0);
            $startDate = trim($_POST['start_date'] ?? '');
            $endDate = trim($_POST['end_date'] ?? '');
            $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
            $errors = [];
            $equipment = $equipmentId > 0 ? Equipment::findById($pdo, $equipmentId) : null;

            $data = [
                'equipment_id' => $equipmentId,
                'client_id' => $clientId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration_days' => 1,
                'quantity' => $quantity === false ? 0 : (int)$quantity,
                'unit_price' => (float)($equipment['daily_rate'] ?? 0),
                'total_amount' => 0,
                'status' => (($user['role'] ?? '') === 'client') ? 'pending' : 'accepted',
                'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3))),
                'notes' => trim($_POST['notes'] ?? ''),
                'return_date' => null,
                'return_condition' => 'good',
                'additional_fees' => 0,
                'return_notes' => null,
            ];

            if (!$equipment) {
                $this->addError($errors, 'equipment_id', 'Sélectionnez un équipement existant.');
            } elseif ($equipment['status'] !== 'available' || (int)$equipment['stock_level'] < 1) {
                $this->addError($errors, 'equipment_id', 'Cet équipement n’est pas disponible à la location.');
            }

            if (!User::findClientById($pdo, $clientId)) {
                $this->addError($errors, 'client_id', 'Sélectionnez un client valide.');
            }

            if (!$this->validDate($startDate) || !$this->validDate($endDate)) {
                $this->addError($errors, 'start_date', 'Saisissez des dates de location valides.');
            } elseif ($endDate < $startDate) {
                $this->addError($errors, 'end_date', 'La date de fin doit être égale ou postérieure à la date de début.');
            } elseif ($startDate < date('Y-m-d')) {
                $this->addError($errors, 'start_date', 'La date de début ne peut pas être dans le passé.');
            }

            if ($quantity === false || $quantity < 1) {
                $this->addError($errors, 'quantity', 'La quantité doit être un nombre entier supérieur à zéro.');
            }

            if ($this->validDate($startDate) && $this->validDate($endDate) && $endDate >= $startDate) {
                $data['duration_days'] = $this->durationInDays($startDate, $endDate);
                $data['total_amount'] = round(
                    $data['unit_price'] * $data['duration_days'] * $data['quantity'],
                    2
                );
            }

            if (!$errors) {
                $pdo->beginTransaction();
                $available = Equipment::availableQuantity($pdo, $equipmentId, $startDate, $endDate);
                if ($data['quantity'] > $available) {
                    $this->addError($errors, 'quantity', "Stock insuffisant pour ces dates : $available unité(s) disponible(s).");
                }
                if ($errors) {
                    $pdo->rollBack();
                }
            }

            if ($errors) {
                $this->render('locations/form', ['errors' => $errors, 'location' => $data, 'equipments' => $equipments, 'clients' => $clients, 'selectedEquipment' => $equipment, 'mode' => 'create']);
                return;
            }

            $locationId = Location::create($pdo, $data);
            if ($pdo->inTransaction()) {
                $pdo->commit();
            }
            $this->redirect('/locations');
        }

        $this->render('locations/form', ['location' => ['equipment_id' => $selectedEquipmentId], 'equipments' => $equipments, 'clients' => $clients, 'selectedEquipment' => Equipment::findById($pdo, $selectedEquipmentId), 'mode' => 'create']);
    }

    public function edit(): void
    {
        $user = $this->requireAuth();
        $role = $user['role'] ?? '';

        $id = (int)($_GET['id'] ?? 0);
        $pdo = Database::getConnection();
        $location = Location::findById($pdo, $id);

        if (!$location) {
            $this->redirect('/locations');
        }

        if ($role === 'client') {
            $this->requireRole('client');
            if ((int)$location['client_id'] !== (int)$user['id'] || $location['status'] !== 'pending') {
                $this->redirect('/locations');
            }
        } else {
            $this->requireRole(['responsable_inventaire', 'agent_location']);
            if (in_array($location['status'], ['returned', 'rejected'], true)) {
                $this->redirect('/locations');
            }
        }

        $equipments = Equipment::all($pdo);
        $clients = User::allClients($pdo);

        if ($this->isPost()) {
            $isStaff = $role !== 'client';
            $equipmentId = (int)($_POST['equipment_id'] ?? 0);
            $clientId = $isStaff ? (int)($_POST['client_id'] ?? 0) : (int)$user['id'];
            $startDate = trim($_POST['start_date'] ?? '');
            $endDate = trim($_POST['end_date'] ?? '');
            $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
            $equipment = $equipmentId > 0 ? Equipment::findById($pdo, $equipmentId) : null;
            $data = [
                'equipment_id' => $equipmentId,
                'client_id' => $clientId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration_days' => 1,
                'quantity' => $quantity === false ? 0 : (int)$quantity,
                'unit_price' => $location['status'] === 'accepted'
                    && $equipmentId === (int)$location['equipment_id']
                    ? (float)$location['unit_price']
                    : (float)($equipment['daily_rate'] ?? 0),
                'total_amount' => 0,
                'status' => $isStaff ? trim($_POST['status'] ?? $location['status']) : $location['status'],
                'invoice_number' => $location['invoice_number'],
                'notes' => trim($_POST['notes'] ?? ''),
                'return_date' => $location['return_date'],
                'return_condition' => $location['return_condition'] ?? 'good',
                'additional_fees' => (float)($location['additional_fees'] ?? 0),
                'return_notes' => $location['return_notes'] ?? '',
            ];
            $errors = [];

            $isRegisteringReturn = $isStaff
                && $location['status'] === 'accepted'
                && $data['status'] === 'returned';
            if ($isRegisteringReturn) {
                $data['equipment_id'] = (int)$location['equipment_id'];
                $data['client_id'] = (int)$location['client_id'];
                $data['start_date'] = $location['start_date'];
                $data['end_date'] = $location['end_date'];
                $data['duration_days'] = (int)$location['duration_days'];
                $data['quantity'] = (int)$location['quantity'];
                $equipment = Equipment::findById($pdo, $data['equipment_id']);
                $data['unit_price'] = (float)($location['unit_price'] ?? 0);
                $data['notes'] = $location['notes'] ?? '';
            }

            if (!$equipment) {
                $this->addError($errors, 'equipment_id', 'Sélectionnez un équipement existant.');
            } elseif ($equipment['status'] !== 'available' && $equipmentId !== (int)$location['equipment_id']) {
                $this->addError($errors, 'equipment_id', 'Cet équipement n’est pas disponible à la location.');
            }
            if (!User::findClientById($pdo, $clientId)) {
                $this->addError($errors, 'client_id', 'Sélectionnez un client valide.');
            }
            if (!$this->validDate($startDate) || !$this->validDate($endDate)) {
                $this->addError($errors, 'start_date', 'Saisissez des dates de location valides.');
            } elseif ($endDate < $startDate) {
                $this->addError($errors, 'end_date', 'La date de fin doit être égale ou postérieure à la date de début.');
            } elseif ($role === 'client' && $startDate < date('Y-m-d')) {
                $this->addError($errors, 'start_date', 'La date de début ne peut pas être dans le passé.');
            }
            if ($quantity === false || $quantity < 1) {
                $this->addError($errors, 'quantity', 'La quantité doit être un nombre entier supérieur à zéro.');
            }
            if (!in_array($data['status'], ['pending', 'accepted', 'rejected', 'returned'], true)) {
                $this->addError($errors, 'status', 'Statut de location invalide.');
            }

            $allowedTransitions = match ($location['status']) {
                'pending' => ['pending', 'accepted', 'rejected'],
                'accepted' => ['accepted', 'returned'],
                default => [],
            };
            if ($isStaff && !in_array($data['status'], $allowedTransitions, true)) {
                $this->addError($errors, 'status', 'Cette transition de statut n’est pas autorisée.');
            }

            if ($this->validDate($startDate) && $this->validDate($endDate) && $endDate >= $startDate) {
                $data['duration_days'] = $this->durationInDays($startDate, $endDate);
                $data['total_amount'] = round(
                    $data['unit_price'] * $data['duration_days'] * $data['quantity'],
                    2
                );
            }

            if ($isRegisteringReturn) {
                $data['total_amount'] = round($data['unit_price'] * $data['duration_days'] * $data['quantity'], 2);
                $data['return_date'] = trim($_POST['return_date'] ?? '') ?: date('Y-m-d');
                $data['return_condition'] = trim($_POST['return_condition'] ?? 'good');
                $data['return_notes'] = trim($_POST['return_notes'] ?? '');
                if (!$this->validDate($data['return_date']) || $data['return_date'] > date('Y-m-d')) {
                    $this->addError($errors, 'return_date', 'La date de retour doit être valide et ne pas être dans le futur.');
                }
                if (!in_array($data['return_condition'], ['good', 'partial', 'damaged'], true)) {
                    $this->addError($errors, 'return_condition', 'État de retour invalide.');
                }
                $data['additional_fees'] = $this->calculateAdditionalFees($data['total_amount'], $data['return_condition']);
                $data['total_amount'] = round($data['total_amount'] + $data['additional_fees'], 2);
            }

            if (!$errors && in_array($data['status'], ['pending', 'accepted'], true)) {
                $pdo->beginTransaction();
                if (!$equipment || $equipment['status'] !== 'available') {
                    $this->addError($errors, 'equipment_id', 'Cet équipement n’est pas disponible à la location.');
                } else {
                    $available = Equipment::availableQuantity($pdo, $equipmentId, $startDate, $endDate, $id);
                    if ($data['quantity'] > $available) {
                        $this->addError($errors, 'quantity', "Stock insuffisant pour ces dates : $available unité(s) disponible(s).");
                    }
                }
                if ($errors) {
                    $pdo->rollBack();
                }
            }

            if ($errors) {
                $this->render('locations/form', ['errors' => $errors, 'location' => $data, 'workflowStatus' => $location['status'], 'equipments' => $equipments, 'clients' => $clients, 'selectedEquipment' => $equipment, 'mode' => 'edit']);
                return;
            }

            if (!$pdo->inTransaction()) {
                $pdo->beginTransaction();
            }
            Location::update($pdo, $id, $data);
            $pdo->commit();
            $this->redirect('/locations');
        }

        $this->render('locations/form', ['location' => $location, 'equipments' => $equipments, 'clients' => $clients, 'selectedEquipment' => Equipment::findById($pdo, (int)$location['equipment_id']), 'mode' => 'edit']);
    }

    public function delete(): void
    {
        $this->requireRole(['responsable_inventaire', 'agent_location']);

        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $pdo = Database::getConnection();
            Location::delete($pdo, $id);
        }
        $this->redirect('/locations');
    }

    public function payment(): void
    {
        $user = $this->requireRole(['responsable_inventaire', 'agent_location']);
        $id = (int)($_GET['id'] ?? 0);
        $pdo = Database::getConnection();
        $location = Location::findById($pdo, $id);

        if (!$location || !in_array($location['status'], ['accepted', 'returned'], true)) {
            http_response_code(404);
            echo 'Location validée introuvable.';
            return;
        }

        $paidAmount = Payment::totalForLocation($pdo, $id);
        $balanceDue = max(0, round((float)$location['total_amount'] - $paidAmount, 2));
        $errors = [];

        if ($this->isPost()) {
            $amountInput = trim($_POST['amount'] ?? '');
            $amount = is_numeric($amountInput) ? round((float)$amountInput, 2) : 0.0;
            $method = trim($_POST['payment_method'] ?? '');

            if ($amount <= 0) {
                $errors['amount'][] = 'Le montant doit être supérieur à zéro.';
            } elseif ($amount > $balanceDue) {
                $errors['amount'][] = 'Le montant dépasse le solde restant de ' . number_format($balanceDue, 2, ',', ' ') . ' €.';
            }
            if (!in_array($method, ['cash', 'card', 'transfer', 'other'], true)) {
                $errors['payment_method'][] = 'Sélectionnez un mode de paiement valide.';
            }

            if (!$errors) {
                $pdo->beginTransaction();
                $paidAmount = Payment::totalForLocation($pdo, $id);
                $balanceDue = max(0, round((float)$location['total_amount'] - $paidAmount, 2));
                if ($amount > $balanceDue) {
                    $errors['amount'][] = 'Le solde a changé. Montant maximum restant : ' . number_format($balanceDue, 2, ',', ' ') . ' €.';
                    $pdo->rollBack();
                } else {
                    $balanceAfter = round($balanceDue - $amount, 2);
                    Payment::create($pdo, $id, (int)$user['id'], $amount, $method, $balanceAfter);
                    $pdo->commit();
                    $this->redirect('/locations');
                }
            }
        }

        $this->render('locations/payment', [
            'location' => $location,
            'paidAmount' => $paidAmount,
            'balanceDue' => $balanceDue,
            'errors' => $errors,
        ]);
    }

    public function download(): void
    {
        $user = $this->requireAuth();
        $this->requireRole(['responsable_inventaire', 'agent_location', 'client']);
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'contract';
        if ($type === 'details') {
            $type = 'contract';
        }

        if (!in_array($type, ['contract', 'invoice', 'receipt', 'return_receipt'], true)) {
            http_response_code(400);
            echo 'Type de document invalide.';
            return;
        }

        $pdo = Database::getConnection();
        $location = Location::findById($pdo, $id);

        if (!$location || $id <= 0) {
            http_response_code(404);
            echo 'Location introuvable.';
            return;
        }

        if (($user['role'] ?? '') === 'client' && (int)($location['client_id'] ?? 0) !== (int)($user['id'] ?? 0)) {
            http_response_code(403);
            echo 'Accès interdit.';
            return;
        }

        if (!in_array($location['status'], ['accepted', 'returned'], true)) {
            http_response_code(409);
            echo 'Le document est disponible après validation de la location.';
            return;
        }

        $client = User::findById($pdo, (int)($location['client_id'] ?? 0));
        $equipment = Equipment::findById($pdo, (int)($location['equipment_id'] ?? 0));
        $subtotal = round((float)$location['unit_price'] * (int)$location['duration_days'] * (int)$location['quantity'], 2);
        $paidAmount = Payment::totalForLocation($pdo, $id);
        $balanceDue = max(0, round((float)$location['total_amount'] - $paidAmount, 2));
        $commonLines = [
            'Numero de location: ' . ($location['invoice_number'] ?? $id),
            'Client: ' . (($client['name'] ?? 'N/A') . ' (' . ($client['email'] ?? 'N/A') . ')'),
            'Equipement: ' . (($equipment['name'] ?? 'N/A') . ' - ' . ($equipment['code'] ?? 'N/A')),
            'Periode: ' . ($location['start_date'] ?? 'N/A') . ' au ' . ($location['end_date'] ?? 'N/A'),
            'Duree: ' . ($location['duration_days'] ?? 1) . ' jour(s)',
            'Quantite: ' . ($location['quantity'] ?? 1),
            'Tarif journalier: ' . ($location['unit_price'] ?? 0) . ' EUR',
        ];

        if ($type === 'invoice') {
            $title = 'Facture de location';
            $filename = 'facture-location-' . $id . '.pdf';
            $lines = array_merge($commonLines, [
                'Sous-total location: ' . $subtotal . ' EUR',
                'Frais de retour: ' . ($location['additional_fees'] ?? 0) . ' EUR',
                'Montant total du: ' . ($location['total_amount'] ?? 0) . ' EUR',
                'Montant regle: ' . $paidAmount . ' EUR',
                'Solde restant: ' . $balanceDue . ' EUR',
                'Statut: ' . $location['status'],
            ]);
        } elseif ($type === 'receipt') {
            $paymentId = (int)($_GET['payment_id'] ?? 0);
            $payment = Payment::findForLocation($pdo, $paymentId, $id);
            if (!$payment) {
                http_response_code(404);
                echo 'Paiement introuvable pour cette location.';
                return;
            }
            $methodLabels = ['cash' => 'Especes', 'card' => 'Carte', 'transfer' => 'Virement', 'other' => 'Autre'];
            $title = 'Recu de paiement';
            $filename = 'recu-paiement-' . $paymentId . '.pdf';
            $lines = array_merge($commonLines, [
                'Paiement enregistre le: ' . $payment['paid_at'],
                'Mode de paiement declare: ' . $methodLabels[$payment['payment_method']],
                'Montant recu: ' . $payment['amount'] . ' EUR',
                'Solde restant apres ce paiement: ' . $payment['balance_after'] . ' EUR',
                'Enregistrement manuel du paiement par le personnel.',
            ]);
        } elseif ($type === 'return_receipt') {
            if ($location['status'] !== 'returned') {
                http_response_code(409);
                echo 'Le recu de retour est disponible apres enregistrement du retour.';
                return;
            }
            $title = 'Recu de retour';
            $filename = 'recu-retour-location-' . $id . '.pdf';
            $lines = array_merge($commonLines, [
                'Date effective du retour: ' . ($location['return_date'] ?? 'Non renseignee'),
                'Etat constate: ' . ($location['return_condition'] ?? 'Non renseigne'),
                'Frais de retour: ' . ($location['additional_fees'] ?? 0) . ' EUR',
                'Notes de retour: ' . (($location['return_notes'] ?? '') ?: 'Aucune'),
                'Ce document confirme le retour du materiel et ne constitue pas une preuve de paiement.',
            ]);
        } else {
            $title = 'Contrat de location';
            $filename = 'contrat-location-' . $id . '.pdf';
            $lines = array_merge($commonLines, [
                'Montant de la location: ' . $subtotal . ' EUR',
                'Statut: ' . $location['status'],
                'Notes: ' . (($location['notes'] ?? '') ?: 'Aucune'),
                'Le materiel doit etre restitue a la date de fin convenue.',
            ]);
        }

        PdfService::generate($title, $lines, $filename);
        $path = STORAGE_PATH . '/documents/' . $filename;

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        readfile($path);
        exit;
    }
}
