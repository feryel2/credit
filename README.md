# Plateforme de gestion de location d'équipements

Application PHP 8 native en architecture MVC, sans framework, avec PDO SQLite pour la persistance.

## Fonctionnalités
- Gestion des utilisateurs par rôle : responsable inventaire, agent de location et client.
- CRUD des catégories d'équipements et des équipements.
- Gestion des états : disponible, en location, en maintenance, endommagé.
- Demandes de location avec validation des dates, des quantités et du stock disponible sur toute la période.
- Calcul côté serveur du tarif journalier, de la durée (dates incluses) et du montant.
- Validation ou refus des demandes par le personnel; enregistrement du retour et de son état.
- Alertes visibles lorsque le stock atteint le seuil défini.
- Génération de contrats et factures PDF; reçu de retour disponible après restitution (ce reçu n'est pas une preuve de paiement).
- Enregistrement manuel des paiements par le personnel, avec paiements partiels, solde restant et reçu PDF par paiement.
- Recherche multicritère et jointures SQL.
- Front-office et back-office responsives.

## Règles de location
- Une demande client reste en attente et ne réserve pas définitivement le stock.
- Le stock est vérifié à nouveau lors de l'approbation; seules les locations validées qui chevauchent les dates demandées diminuent la quantité disponible.
- Le tarif vient du catalogue et ne peut pas être remplacé par une valeur envoyée par le navigateur.
- Le client peut modifier uniquement ses propres demandes en attente. Le personnel valide/refuse les demandes et enregistre les retours.
- Les frais de retour sont calculés automatiquement sur le montant de location : 0 % en bon état, 10 % en état partiel et 25 % en cas de dommage.
- Les reçus de paiement correspondent aux paiements enregistrés manuellement; aucun paiement en ligne ni débit bancaire n'est effectué par l'application.

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
