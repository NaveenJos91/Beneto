# Étape 2 — Créer la base de données (entités + tables)

On a ajouté 4 entités : **User** (utilisateur), **Ville**, **Theme** (thématique) et **Annonce**.
Il faut maintenant installer Doctrine et créer les tables dans MySQL.

## À faire (dans l'ordre)

1. **Remplace ton ancien dossier** par le contenu de ce nouveau zip (écrase les fichiers).
   Garde le même dossier `beneto`.

2. Assure-toi que **Docker Desktop tourne** (baleine verte).

3. Ouvre un terminal dans le dossier `beneto` et lance l'app en arrière-plan :
   ```
   docker compose up -d --build
   ```
   (le `-d` libère le terminal pour taper les commandes suivantes)

4. Installe les nouvelles dépendances (Doctrine) dans le conteneur :
   ```
   docker compose exec app composer update
   ```
   (patiente, ça télécharge Doctrine ; internet requis)

5. Génère la migration (le script qui crée les tables à partir des entités) :
   ```
   docker compose exec app php bin/console doctrine:migrations:diff
   ```

6. Applique la migration (crée réellement les tables dans MySQL) :
   ```
   docker compose exec app php bin/console doctrine:migrations:migrate
   ```
   (réponds `yes` si on te le demande)

7. Va sur **http://localhost:8081** (phpMyAdmin), connecte-toi (`beneto` / `beneto`),
   ouvre la base `beneto` à gauche : tu dois voir les tables **users, ville, theme, annonce**
   (+ une table technique `doctrine_migration_versions`). 🎉

## Si un souci
- Étape 5 dit "No changes detected" : dis-le moi, on créera les tables autrement.
- Une erreur rouge quelconque : copie-moi le message, je te débloque.
- Note : garder le terminal/Docker allumé n'est plus obligatoire entre les commandes
  car on est en mode `-d` (arrière-plan). Pour tout arrêter : `docker compose down`.
