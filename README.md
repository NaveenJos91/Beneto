# Bénéto — plateforme de tutorat bénévole local

Application web développée avec **Symfony (Twig)** et **MySQL**, entièrement **conteneurisée avec Docker**.
Projet réalisé dans le cadre du titre Concepteur Développeur d'Applications (CDA).

## Prérequis
- **Docker Desktop** installé (Windows, macOS ou Linux). C'est la SEULE chose à installer.
  Rien d'autre (ni PHP, ni Composer, ni MySQL) : tout tourne dans les conteneurs.

## Lancer le projet (une seule commande)
1. Ouvrir un terminal dans le dossier du projet (là où se trouve `docker-compose.yml`).
2. Lancer :
   ```
   docker compose up --build
   ```
   (si ça ne marche pas, essayer `docker-compose up --build`)
3. Au **premier lancement**, patienter quelques minutes : Docker télécharge les images
   et installe automatiquement les dépendances Symfony. C'est normal.
4. Quand ça affiche que les services tournent, ouvrir dans le navigateur :
   - Application : **http://localhost:8080**
   - phpMyAdmin (visualiser la base) : **http://localhost:8081**  (utilisateur `beneto`, mot de passe `beneto`)

## Arrêter le projet
Dans le terminal : `Ctrl + C`, puis pour tout éteindre proprement :
```
docker compose down
```

## Les 4 conteneurs (architecture)
- **web** : serveur Nginx, sert l'application (port 8080).
- **app** : Symfony (PHP-FPM), exécute la logique de l'application.
- **db** : base de données MySQL.
- **phpmyadmin** : interface web pour consulter la base (port 8081).

## Pourquoi Docker ?
Docker garantit un environnement identique partout. La **même** configuration permet
de lancer toute l'application en local pour une démonstration (sans dépendre du réseau)
**et** de la déployer en ligne à l'identique. Local et production se comportent pareil.
