<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/Equipment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/PdfService.php';

class LocationController extends Controller
{
    public function index(): void
    {
        $pdo = Database::getConnection();
        $locations = Location::all($pdo);
        $this->render('locations/index', ['locations' => $locations]);
    }

    public function create(): void
    {
        $pdo = Database::getConnection();
        $equipments = Equipment::getAvailable($pdo);
        $clients = User::all($pdo);

        if ($this->isPost()) {
            $equipmentId = (int)($_POST['equipment_id'] ?? 0);
            $clientId = (int)($_POST['client_id'] ?? 0);
            $startDate = trim($_POST['start_date'] ?? '');
            $endDate = trim($_POST['end_date'] ?? '');
            $duration = max(1, (int)($_POST['duration_days'] ?? 1));
            $unitPrice = (float)($_POST['unit_price'] ?? 0);

            $data = [
                'equipment_id' => $equipmentId,
                'client_id' => $clientId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration_days' => $duration,
                'quantity' => (int)($_POST['quantity'] ?? 1),
                'unit_price' => $unitPrice,
                'total_amount' => $unitPrice * $duration,
                'status' => 'pending',
                'invoice_number' => 'INV-' . date('YmdHis'),
                'notes' => trim($_POST['notes'] ?? ''),
            ];

            $errors = $this->validate([
                'equipment_id' => ['required'],
                'client_id' => ['required'],
                'start_date' => ['required'],
                'end_date' => ['required'],
            ], $data);

            if ($errors) {
                $this->render('locations/form', ['errors' => $errors, 'location' => $data, 'equipments' => $equipments, 'clients' => $clients, 'mode' => 'create']);
                return;
            }

            $locationId = Location::create($pdo, $data);
            PdfService::generate('Contrat de location', '<p>Contrat n° ' . $data['invoice_number'] . '</p><p>Client: ' . $clientId . '</p>', 'contrat-' . $locationId . '.html');
            $this->redirect('/locations');
        }

        $this->render('locations/form', ['location' => [], 'equipments' => $equipments, 'clients' => $clients, 'mode' => 'create']);
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $pdo = Database::getConnection();
        $location = Location::findById($pdo, $id);
        $equipments = Equipment::getAvailable($pdo);
        $clients = User::all($pdo);

        if (!$location) {
            $this->redirect('/locations');
        }

        if ($this->isPost()) {
            $data = [
                'equipment_id' => (int)($_POST['equipment_id'] ?? 0),
                'client_id' => (int)($_POST['client_id'] ?? 0),
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'duration_days' => (int)($_POST['duration_days'] ?? 1),
                'quantity' => (int)($_POST['quantity'] ?? 1),
                'unit_price' => (float)($_POST['unit_price'] ?? 0),
                'total_amount' => (float)($_POST['total_amount'] ?? 0),
                'status' => $_POST['status'] ?? 'pending',
                'invoice_number' => $_POST['invoice_number'] ?? $location['invoice_number'],
                'notes' => trim($_POST['notes'] ?? ''),
            ];

            Location::update($pdo, $id, $data);
            $this->redirect('/locations');
        }

        $this->render('locations/form', ['location' => $location, 'equipments' => $equipments, 'clients' => $clients, 'mode' => 'edit']);
    }

    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $pdo = Database::getConnection();
            Location::delete($pdo, $id);
        }
        $this->redirect('/locations');
    }
}
