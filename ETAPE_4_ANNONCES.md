# Étape 4 — Les annonces (cœur de Bénéto)

Ajoute : publier / modifier / supprimer une annonce (bénévole), recherche par
thème et ville (apprenant), et modération (admin : valider / refuser).

## ⚠️ IMPORTANT pour l'extraction (éviter d'effacer vendor)
NE supprime PAS ton dossier `beneto` avant d'extraire. Extrais le zip PAR-DESSUS
et accepte "Remplacer les fichiers". Le dossier `vendor` restera intact.

## À faire
1. Extraire ce zip par-dessus ton dossier `beneto` (remplacer les fichiers).
2. Docker allumé, puis :
   ```
   docker compose up -d
   docker compose exec app php bin/console doctrine:migrations:diff
   docker compose exec app php bin/console doctrine:migrations:migrate
   docker compose exec app php bin/console cache:clear
   ```
   (la migration ajoute les contraintes de validation ; réponds `yes`)
   Si "vendor" manquant / erreur autoload :
   ```
   docker compose exec app composer install
   ```

## Tester le parcours complet
1. Connecte-toi avec un compte **bénévole** → menu "Mes annonces" → "Publier une annonce".
   Crée une annonce (titre, description, thème, ville). Elle passe "en attente".
2. Connecte-toi avec ton compte **admin** (celui où tu as mis ROLE_ADMIN) →
   menu "Modération" → clique "Valider".
3. Va sur "Annonces" (accessible à tous) → recherche par thème / ville →
   ton annonce validée apparaît → clique "Voir l'annonce" → bouton "Contacter".
4. Reviens en bénévole → "Mes annonces" → teste "Modifier" et "Supprimer".

## Compétences validées
- Développement des composants métier (CRUD, recherche) → C3.
- Accès aux données via repository + **recherche paramétrée** (anti-injection SQL) → C8.
- Contrôle d'accès par rôle (bénévole/admin, propriétaire) → C3/C7.
- Modération = fonctionnalité métier complète.
