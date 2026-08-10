# Plateforme de gestion de location d'équipements

Application PHP 8 native en architecture MVC, sans framework, avec PDO SQLite pour la persistance.

## Fonctionnalités
- Gestion des utilisateurs par rôle : responsable inventaire, agent de location et client.
- CRUD des catégories d'équipements et des équipements.
- Gestion des états : disponible, en location, en maintenance, endommagé.
- Demandes de location, validation de disponibilité et calcul du montant.
- Généraion de factures et reçus au format PDF.
- Recherche multicritère et jointures SQL.
- Front-office et back-office responsives.

## Structure
- app/config : configuration
- app/core : base MVC
- app/models : entités
- app/controllers : logique métier
- app/services : génération PDF
- views : templates d'affichage
- public : point d'entrée HTTP
- storage : base SQLite et documents générés

## Lancement
1. Installer PHP 8.3+
2. Vérifier que l'extension PDO SQLite est activée.
3. Exécuter :
   php -S localhost:8000 -t public
4. Ouvrir : http://localhost:8000/

## Comptes de démonstration
- admin@location.local / password123 (Responsable inventaire)
- agent@location.local / password123 (Agent de location)
- client@location.local / password123 (Client)
