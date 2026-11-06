# Documentation du projet - Plateforme de gestion de location d’équipements

## 1. Vue d’ensemble

Ce projet est une application PHP 8 en architecture MVC simple, sans framework, qui permet de gérer :

- les catégories d’équipements,
- les équipements,
- les utilisateurs,
- les locations,
- la génération de documents PDF,
- la recherche et le filtrage des données.

La base de données utilisée est SQLite, via PDO.

---

## 2. Structure du projet

```text
pi/
├── app/
│   ├── config/
│   │   └── config.php
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   ├── DashboardController.php
│   │   ├── EquipmentController.php
│   │   ├── HomeController.php
│   │   └── LocationController.php
│   ├── core/
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Router.php
│   │   └── Session.php
│   ├── models/
│   │   ├── Category.php
│   │   ├── Equipment.php
│   │   ├── Location.php
│   │   └── User.php
│   └── services/
│       └── PdfService.php
├── public/
│   ├── assets/
│   └── index.php
├── storage/
├── views/
│   ├── auth/
│   ├── categories/
│   ├── dashboard/
│   ├── equipment/
│   ├── home/
│   ├── layouts/
│   └── locations/
├── composer.json
├── README.md
├── DOCUMENTATION_PROJET.md
├── info.php
└── storage/
```

---

## 3. Description des dossiers

### app/config
Contient la configuration globale du projet.

Fichier principal :
- `config.php`

Il définit :
- `APP_ROOT`
- `PROJECT_ROOT`
- `BASE_URL`
- `DB_PATH`
- `STORAGE_PATH`
- `ASSET_PATH`

### app/core
Contient les éléments de base du framework MVC minimal.

#### `Database.php`
- gère la connexion PDO SQLite,
- initialise les tables si elles n’existent pas,
- insère des données de démonstration si la base est vide.

#### `Controller.php`
- contient les méthodes communes :
  - `render()` : affiche une vue,
  - `redirect()` : redirection,
  - `isPost()` : vérifie si la requête est POST,
  - `validate()` : validation des champs.

#### `Router.php`
- gère les routes,
- supporte les méthodes GET et POST,
- appelle le contrôleur et la méthode associés.

#### `Session.php`
- gère la session utilisateur.

### app/models
Contient les classes de données.

Exemples :
- `Equipment.php` : gestion des équipements,
- `Category.php` : gestion des catégories,
- `Location.php` : gestion des locations,
- `User.php` : gestion des utilisateurs.

Chaque modèle contient des méthodes de type :
- `all()`
- `findById()`
- `create()`
- `update()`
- `delete()`

### app/controllers
Contient la logique d’affaires.

Chaque contrôleur correspond à un module :
- `AuthController.php` : connexion / déconnexion,
- `DashboardController.php` : tableau de bord,
- `CategoryController.php` : CRUD catégories,
- `EquipmentController.php` : CRUD équipements,
- `HomeController.php` : accueil et catalogue public,
- `LocationController.php` : locations et PDF.

### app/services
Contient les services utiles au projet.

Fichier principal :
- `PdfService.php`

Il permet de générer un fichier PDF simple à partir de texte.

### views
Contient tous les fichiers HTML/PHP des pages.

Exemples :
- `views/auth/login.php`
- `views/dashboard/index.php`
- `views/equipment/index.php`
- `views/locations/index.php`
- `views/home/catalog.php`
- `views/layouts/main.php`

### public
Contient le point d’entrée HTTP.

Fichier principal :
- `public/index.php`

Ce fichier charge les contrôleurs, initialise le routeur et envoie les requêtes vers les bons handlers.

---

## 4. Comment fonctionne le projet

Voici le flux complet d’exécution d’une page dans ce projet :

1. L’utilisateur ouvre une URL dans le navigateur, par exemple `/equipments`.
2. Le fichier [public/index.php](public/index.php) est chargé.
3. Ce fichier charge la configuration, les classes de base (`Database`, `Router`, `Controller`) et les contrôleurs.
4. Le routeur lit la requête actuelle (`GET` ou `POST`) et compare l’URL demandée avec les routes enregistrées.
5. Si une route correspond, le routeur appelle le contrôleur et la méthode associée.
6. Le contrôleur récupère les données nécessaires via les modèles (`Equipment`, `Category`, `Location`, `User`).
7. Les modèles utilisent `PDO` pour lire ou écrire dans la base SQLite.
8. Le contrôleur prépare les données pour la vue.
9. La vue est affichée avec le layout principal (`views/layouts/main.php`).
10. L’utilisateur voit le résultat dans le navigateur.

### Exemple concret : ajout d’un équipement

1. L’utilisateur ouvre `/equipments/create`.
2. Le contrôleur `EquipmentController::create()` est appelé.
3. Il charge les catégories disponibles.
4. Le formulaire est affiché.
5. L’utilisateur envoie les données du formulaire avec `POST`.
6. `EquipmentController::create()` valide les données.
7. Si tout est correct, `Equipment::create()` insère l’équipement dans SQLite.
8. Le contrôleur redirige vers `/equipments`.
9. La liste des équipements est rechargée.

### Exemple concret : création d’une location

1. L’utilisateur ouvre `/locations/create`.
2. `LocationController::create()` charge les équipements disponibles et les clients.
3. Le formulaire est affiché.
4. L’utilisateur envoie les informations de location.
5. Le contrôleur valide les champs obligatoires.
6. `Location::create()` enregistre la location en base.
7. `PdfService::generate()` crée un fichier PDF de contrat.
8. L’utilisateur est redirigé vers la liste des locations.

### Rôle de chaque couche

- `public/index.php` : point d’entrée
- `Router` : route les requêtes
- `Controller` : traite la logique et prépare la vue
- `Model` : accès aux données
- `Database` : connexion à SQLite
- `View` : affichage HTML
- `Service` : traitements annexes comme les PDF

---

## 5. Explication des fichiers clés

## 4.1 public/index.php
C’est le point d’entrée principal du projet.

Il fait notamment :
- charge la configuration,
- charge les classes utilitaires,
- charge les contrôleurs,
- crée un objet `Router`,
- enregistre toutes les routes,
- exécute la requête demandée.

### Routes enregistrées

- `GET /` → `DashboardController::index()`
- `GET /login` → `AuthController::login()`
- `POST /login` → `AuthController::login()`
- `GET /logout` → `AuthController::logout()`
- `GET /dashboard` → `DashboardController::index()`
- `GET /categories` → `CategoryController::index()`
- `GET /categories/create` → `CategoryController::create()`
- `POST /categories/create` → `CategoryController::create()`
- `GET /categories/edit` → `CategoryController::edit()`
- `POST /categories/edit` → `CategoryController::edit()`
- `GET /categories/delete` → `CategoryController::delete()`
- `GET /equipments` → `EquipmentController::index()`
- `GET /equipments/create` → `EquipmentController::create()`
- `POST /equipments/create` → `EquipmentController::create()`
- `GET /equipments/edit` → `EquipmentController::edit()`
- `POST /equipments/edit` → `EquipmentController::edit()`
- `GET /equipments/delete` → `EquipmentController::delete()`
- `GET /locations` → `LocationController::index()`
- `GET /locations/create` → `LocationController::create()`
- `POST /locations/create` → `LocationController::create()`
- `GET /locations/edit` → `LocationController::edit()`
- `POST /locations/edit` → `LocationController::edit()`
- `GET /locations/delete` → `LocationController::delete()`
- `GET /catalog` → `HomeController::catalog()`
- `GET /home` → `HomeController::index()`

---

## 4.2 app/core/Router.php
La classe `Router` contient :

- `$routes` : liste des routes,
- `get()` : enregistrer une route GET,
- `post()` : enregistrer une route POST,
- `dispatch()` : trier et lancer la route correspondante,
- `callHandler()` : exécuter le contrôleur avec sa méthode.

### Exemple logique
```php
foreach ($this->routes[$method] ?? [] as [$route, $handler]) {
    if ($route === $path) {
        $this->callHandler($handler);
        return;
    }
}
```

Cela permet de faire un routage simple et lisible.

---

## 4.3 app/core/Controller.php
La classe abstraite `Controller` sert de base pour tous les contrôleurs.

### Méthodes principales

#### `render()`
Charge une vue et un layout.

```php
protected function render(string $view, array $data = [], string $layout = 'main'): void
```

#### `redirect()`
Redirige vers une URL.

```php
protected function redirect(string $path): void
```

#### `isPost()`
Retourne `true` si la requête est de type POST.

#### `validate()`
Valide les données selon des règles simples :
- `required`
- `email`
- `numeric`
- `int`

---

## 4.4 app/core/Database.php
C’est un point central du projet.

### Fonctionnalités

- créer la connexion SQLite,
- préparer la base,
- créer les tables si elles n’existent pas,
- insérer les comptes par défaut,
- insérer des catégories et équipements d’exemple.

### Tables créées

- `users`
- `categories`
- `equipments`
- `locations`

### Comptes créés automatiquement

- `admin@location.local` / `password123`
- `agent@location.local` / `password123`
- `client@location.local` / `password123`

---

## 4.5 app/models/Equipment.php
Ce modèle permet de gérer les équipements.

### Méthodes importantes

- `all(PDO $pdo)` : liste tous les équipements avec leur catégorie.
- `search(PDO $pdo, array $filters)` : recherche multi-critère.
- `findById(PDO $pdo, int $id)` : recherche un équipement par ID.
- `create(PDO $pdo, array $data)` : ajoute un équipement.
- `update(PDO $pdo, int $id, array $data)` : modifie un équipement.
- `delete(PDO $pdo, int $id)` : supprime un équipement.
- `getAvailable(PDO $pdo)` : récupère les équipements disponibles.

### Filtres de recherche
Le modèle supporte :
- `category_id`
- `status`
- `query`

---

## 4.6 app/models/Location.php
Ce modèle gère les locations.

### Champs principaux

- `equipment_id`
- `client_id`
- `start_date`
- `end_date`
- `duration_days`
- `quantity`
- `unit_price`
- `total_amount`
- `status`
- `invoice_number`
- `notes`

### Fonctionnement
Dans `LocationController::create()`, le système :
- récupère les informations du formulaire,
- crée la location en base,
- génère un PDF de contrat,
- redirige vers la liste des locations.

---

## 4.7 app/services/PdfService.php
Ce service génère un PDF simple basé sur un texte brut.

### Fonctionnement
- vérifie la présence du dossier `storage/documents`,
- crée le dossier si nécessaire,
- construit un contenu PDF simple,
- sauvegarde le fichier dans `storage/documents`.

### Exemple de génération
```php
PdfService::generate('Contrat de location', [...], 'contrat-1.pdf');
```

---

## 5. Contrôleurs détaillés

## 5.1 AuthController
Responsable de l’authentification.

### Login
- lit `email` et `password` depuis le formulaire,
- cherche l’utilisateur par email,
- vérifie le mot de passe avec `password_verify()`,
- stocke l’utilisateur dans la session,
- redirige vers `/`.

### Logout
- vide la session,
- redirige vers `/login`.

---

## 5.2 DashboardController
Ce contrôleur prépare le tableau de bord principal.

Il récupère :
- les équipements,
- les locations,
- les catégories.

Puis envoie ces données à la vue `dashboard/index.php`.

---

## 5.3 EquipmentController
Gère le CRUD des équipements.

### `index()`
- récupère les filtres de recherche,
- appelle `Equipment::search()`,
- affiche la liste filtrée.

### `create()`
- charge les catégories,
- valide les données,
- crée l’équipement,
- redirige vers `/equipments`.

### `edit()`
- charge l’équipement à modifier,
- valide les données,
- met à jour l’enregistrement.

### `delete()`
- supprime l’équipement par ID,
- redirige vers `/equipments`.

---

## 5.4 CategoryController
Gère les catégories.

La logique est similaire à celle du CRUD des équipements :
- liste,
- création,
- modification,
- suppression.

---

## 5.5 HomeController
Utilisé pour la partie publique.

### `index()`
Affiche la page d’accueil du site.

### `catalog()`
Affiche le catalogue des équipements disponibles.

---

## 5.6 LocationController
C’est le contrôleur le plus complet.

### `index()`
Liste toutes les locations.

### `create()`
- charge les équipements disponibles,
- charge les clients,
- valide le formulaire,
- crée la location,
- génère le contrat PDF,
- redirige.

### `edit()`
Permet de modifier une location existante.

### `delete()`
Supprime une location.

---

## 6. Rôles et logique utilisateur

Le système a des comptes démonstration avec plusieurs rôles :

- `responsable_inventaire`
- `agent_location`
- `client`

Le projet ne met pas en place de système de droits très complexe, mais les rôles sont stockés en base dans `users.role`.

---

## 7. Fonctionnalités du projet

### Fonctionnalités principales
- gestion des équipements,
- gestion des catégories,
- gestion des utilisateurs,
- gestion des locations,
- sauvegarde SQLite,
- génération de PDF,
- filtres et recherche,
- dashboard d’administration,
- catalogue public.

### Fonctionnalités avancées
- validation des formulaires côté contrôleur,
- préparation des données avant affichage,
- écriture des documents dans `storage/`,
- données d’exemple automatiquement injectées.

---

## 8. Extensions PHP nécessaires

Pour que le projet fonctionne correctement, il faut que l’environnement PHP ait les extensions suivantes :

- `pdo`
- `pdo_sqlite`
- `sqlite3` (selon l’environnement)
- `mbstring` (si utilisé dans certains contextes)
- `json` (souvent déjà présent)

### Important
Le projet utilise SQLite via PDO. Donc la clé est :

- `PDO SQLite driver`

Sans cette extension, le projet affiche l’erreur :

```text
PDOException: could not find driver
```

---

## 9. Problème courant rencontré et solution

### Problème
Le projet peut échouer si l’on utilise un PHP installé via WinGet ou un autre runtime qui n’a pas `pdo_sqlite`.

### Erreur observée
```text
Fatal error: Uncaught PDOException: could not find driver
```

### Solution utilisée
Lancer le projet avec le PHP de XAMPP :

```powershell
C:\xampp\php\php.exe -S localhost:8000 -t public
```

### Vérification de l’extension
Commande utile :

```powershell
C:\xampp\php\php.exe -m | findstr /i "pdo sqlite"
```

Résultat attendu :

```text
PDO
pdo_sqlite
```

---

## 10. Comment lancer le projet

### Option 1 : avec XAMPP
```powershell
cd C:\xampp\htdocs\pi
C:\xampp\php\php.exe -S localhost:8000 -t public
```

### Option 2 : depuis le dossier du projet
```powershell
cd C:\xampp\htdocs\pi
php -S localhost:8000 -t public
```

> Attention : cette deuxième option ne fonctionne correctement que si le PHP actif contient bien `pdo_sqlite`.

### URL d’accès
```text
http://localhost:8000/
```

---

## 11. Fichiers générés automatiquement

Lors de l’exécution, le projet peut générer :

- `storage/database.sqlite`
- `storage/documents/*.pdf`

Ces fichiers sont créés automatiquement à la première utilisation.

---

## 12. Points faibles / améliorations possibles

Le projet est fonctionnel et bien structuré pour un niveau d’apprentissage, mais il reste simple. Des améliorations possibles seraient :

- ajout d’un système d’authentification plus robuste,
- contrôle d’accès par rôle,
- validation plus avancée des données,
- gestion des erreurs plus détaillée,
- meilleure génération PDF,
- UI plus moderne,
- support de plusieurs langues,
- tests automatisés.

---

## 13. Résumé rapide

Ce projet est une plateforme MVC PHP native destinée à la gestion de locations d’équipements, avec :

- MVC simple,
- SQLite,
- PDO,
- vues PHP,
- contrôleurs métiers,
- modèles de données,
- services PDF,
- routage manuel,
- données de démonstration.

---

## 14. Conclusion

Le projet est une bonne base pour un système de gestion de location d’équipements, facile à lire, à modifier et à étendre.

Il est particulièrement adapté pour :
- un projet de cours,
- un prototype de gestion,
- une application légère sans dépendances lourdes.

---

## 15. Commandes utiles

### Vérifier les extensions PHP
```powershell
C:\xampp\php\php.exe -m
```

### Vérifier les modules liés à SQLite
```powershell
C:\xampp\php\php.exe -m | findstr /i "pdo sqlite"
```

### Lancer le projet
```powershell
C:\xampp\php\php.exe -S localhost:8000 -t public
```

### Vérifier qu’il répond
```powershell
Invoke-WebRequest -Uri http://localhost:8000/
```

---

Si vous voulez, je peux aussi vous préparer une version encore plus détaillée sous forme de rapport académique, avec diagramme UML et explication de chaque méthode en français plus formel.
