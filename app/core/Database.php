<?php

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        if (!is_dir(STORAGE_PATH)) {
            mkdir(STORAGE_PATH, 0777, true);
        }

        $dsn = 'sqlite:' . DB_PATH;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        self::$pdo = new PDO($dsn, null, null, $options);
        self::initialize();
        return self::$pdo;
    }

    private static function migrateLocationsTable(): void
    {
        $columns = self::$pdo->query('PRAGMA table_info(locations)')->fetchAll(PDO::FETCH_ASSOC);
        $existingColumns = array_map(static fn (array $column): string => $column['name'], $columns);

        $newColumns = [
            'return_date' => 'DATE',
            'return_condition' => 'VARCHAR(100) DEFAULT "good"',
            'additional_fees' => 'DECIMAL(10,2) DEFAULT 0',
            'return_notes' => 'TEXT',
        ];

        foreach ($newColumns as $column => $definition) {
            if (in_array($column, $existingColumns, true)) {
                continue;
            }

            self::$pdo->exec('ALTER TABLE locations ADD COLUMN ' . $column . ' ' . $definition);
        }
    }

    public static function initialize(): void
    {
        $sql = [
            'CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(150) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                role VARCHAR(50) NOT NULL,
                phone VARCHAR(50),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );',
            'CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(150) NOT NULL,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );',
            'CREATE TABLE IF NOT EXISTS equipments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER,
                name VARCHAR(150) NOT NULL,
                code VARCHAR(100) NOT NULL,
                serial_number VARCHAR(100),
                purchase_price DECIMAL(10,2) DEFAULT 0,
                daily_rate DECIMAL(10,2) DEFAULT 0,
                status VARCHAR(40) DEFAULT "available",
                stock_level INTEGER DEFAULT 0,
                alert_threshold INTEGER DEFAULT 0,
                condition_note TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(category_id) REFERENCES categories(id)
            );',
            'CREATE TABLE IF NOT EXISTS locations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                equipment_id INTEGER NOT NULL,
                client_id INTEGER NOT NULL,
                start_date DATE,
                end_date DATE,
                duration_days INTEGER DEFAULT 1,
                quantity INTEGER DEFAULT 1,
                unit_price DECIMAL(10,2) DEFAULT 0,
                total_amount DECIMAL(10,2) DEFAULT 0,
                status VARCHAR(40) DEFAULT "pending",
                invoice_number VARCHAR(100),
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(equipment_id) REFERENCES equipments(id),
                FOREIGN KEY(client_id) REFERENCES users(id)
            );',
            'CREATE TABLE IF NOT EXISTS payments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                location_id INTEGER NOT NULL,
                recorded_by INTEGER NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                payment_method VARCHAR(30) NOT NULL,
                balance_after DECIMAL(10,2) NOT NULL,
                paid_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(location_id) REFERENCES locations(id),
                FOREIGN KEY(recorded_by) REFERENCES users(id)
            );',
        ];

        foreach ($sql as $statement) {
            self::$pdo->exec($statement);
        }

        self::migrateLocationsTable();

        $count = self::$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ((int)$count === 0) {
            self::$pdo->exec("INSERT INTO users (name, email, password_hash, role, phone) VALUES
                ('Responsable Inventaire', 'admin@location.local', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'responsable_inventaire', '0600000001'),
                ('Agent Location', 'agent@location.local', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'agent_location', '0600000002'),
                ('Client Test', 'client@location.local', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'client', '0600000003')");
        }

        $categoryCount = self::$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
        if ((int)$categoryCount === 0) {
            self::$pdo->exec("INSERT INTO categories (name, description) VALUES
                ('Audio-Visuel', 'Matériel de sonorisation et projection'),
                ('Travail sur site', 'Équipements de chantier et de manutention'),
                ('Vidéographie', 'Caméras et accessoires de tournage')");
        }

        $equipmentCount = self::$pdo->query('SELECT COUNT(*) FROM equipments')->fetchColumn();
        if ((int)$equipmentCount === 0) {
            self::$pdo->exec("INSERT INTO equipments (category_id, name, code, serial_number, purchase_price, daily_rate, status, stock_level, alert_threshold, condition_note) VALUES
                (1, 'Projecteur Full HD', 'PRJ-001', 'SN-PRJ-001', 1450.00, 75.00, 'available', 8, 2, 'Très bon état'),
                (1, 'Enceinte portable', 'AUD-010', 'SN-AUD-010', 890.00, 42.00, 'available', 12, 3, 'État neuf'),
                (2, 'Perforateur sans fil', 'TRV-115', 'SN-TRV-115', 620.00, 35.00, 'maintenance', 5, 2, 'En cours de vérification'),
                (3, 'Caméra 4K', 'VID-300', 'SN-VID-300', 2100.00, 110.00, 'rented', 4, 2, 'Pas de défaut majeur')");
        }

        self::seedDemoCatalog();
    }

    private static function seedDemoCatalog(): void
    {
        $pdo = self::$pdo;
        $ownsTransaction = !$pdo->inTransaction();
        if ($ownsTransaction) {
            $pdo->beginTransaction();
        }

        try {
            $categoryDefinitions = [
                'Audio-Visuel' => 'Materiel de sonorisation et projection',
                'Travail sur site' => 'Equipements de chantier et de manutention',
                'Vidéographie' => 'Cameras et accessoires de tournage',
                'Evenementiel' => 'Sonorisation, eclairage et materiel pour vos evenements',
                'Outillage professionnel' => 'Outillage pour chantier, atelier et renovation',
            ];
            $findCategory = $pdo->prepare('SELECT id FROM categories WHERE name = :name LIMIT 1');
            $insertCategory = $pdo->prepare('INSERT INTO categories (name, description, created_at) VALUES (:name, :description, :created_at)');
            $categoryIds = [];

            foreach ($categoryDefinitions as $name => $description) {
                $findCategory->execute([':name' => $name]);
                $categoryId = $findCategory->fetchColumn();
                if ($categoryId === false) {
                    $insertCategory->execute([
                        ':name' => $name,
                        ':description' => $description,
                        ':created_at' => date('Y-m-d H:i:s'),
                    ]);
                    $categoryId = $pdo->lastInsertId();
                }
                $categoryIds[$name] = (int)$categoryId;
            }

            $demoEquipments = [
                ['Audio-Visuel', 'Projecteur laser 4K', 'PRJ-002', 2350, 120, 3, 1],
                ['Audio-Visuel', 'Enceinte sono 500 W', 'AUD-020', 1250, 68, 6, 2],
                ['Audio-Visuel', 'Microphone sans fil duo', 'AUD-021', 420, 24, 8, 2],
                ['Travail sur site', 'Perforateur SDS-Plus', 'TRV-120', 390, 29, 6, 2],
                ['Travail sur site', 'Scie circulaire professionnelle', 'TRV-121', 260, 22, 5, 1],
                ['Travail sur site', 'Niveau laser rotatif', 'TRV-122', 780, 48, 3, 1],
                ['Travail sur site', 'Nettoyeur haute pression', 'TRV-123', 540, 38, 4, 1],
                ['Vidéographie', 'Caméra hybride 4K', 'VID-310', 1650, 95, 2, 1],
                ['Vidéographie', 'Stabilisateur caméra', 'VID-311', 620, 44, 3, 1],
                ['Vidéographie', 'Trépied vidéo professionnel', 'VID-312', 280, 19, 7, 2],
                ['Evenementiel', 'Kit lumière LED de scène', 'EVT-001', 940, 56, 4, 1],
                ['Evenementiel', 'Console de mixage numérique', 'EVT-002', 690, 45, 3, 1],
                ['Evenementiel', 'Écran de projection 120 pouces', 'EVT-003', 510, 32, 5, 1],
                ['Evenementiel', 'Kit de microphones événementiel', 'EVT-004', 580, 39, 4, 1],
                ['Outillage professionnel', 'Perceuse-visseuse sans fil', 'OUT-001', 310, 24, 6, 2],
                ['Outillage professionnel', 'Aspirateur de chantier', 'OUT-002', 460, 31, 4, 1],
                ['Outillage professionnel', 'Échelle télescopique', 'OUT-003', 340, 26, 3, 1],
            ];

            $findEquipment = $pdo->prepare('SELECT 1 FROM equipments WHERE code = :code LIMIT 1');
            $insertEquipment = $pdo->prepare('INSERT INTO equipments (category_id, name, code, serial_number, purchase_price, daily_rate, status, stock_level, alert_threshold, condition_note, created_at) VALUES (:category_id, :name, :code, :serial_number, :purchase_price, :daily_rate, :status, :stock_level, :alert_threshold, :condition_note, :created_at)');

            foreach ($demoEquipments as [$categoryName, $name, $code, $purchasePrice, $dailyRate, $stockLevel, $alertThreshold]) {
                $findEquipment->execute([':code' => $code]);
                if ($findEquipment->fetchColumn() !== false) {
                    continue;
                }

                $insertEquipment->execute([
                    ':category_id' => $categoryIds[$categoryName],
                    ':name' => $name,
                    ':code' => $code,
                    ':serial_number' => 'SN-' . $code,
                    ':purchase_price' => $purchasePrice,
                    ':daily_rate' => $dailyRate,
                    ':status' => 'available',
                    ':stock_level' => $stockLevel,
                    ':alert_threshold' => $alertThreshold,
                    ':condition_note' => 'Equipement de demonstration en bon etat',
                    ':created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            if ($ownsTransaction) {
                $pdo->commit();
            }
        } catch (Throwable $exception) {
            if ($ownsTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }
}
