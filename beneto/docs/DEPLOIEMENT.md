# Procédure de déploiement — Bénéto

## 1. Principe
Bénéto est entièrement **conteneurisé avec Docker**. La même configuration permet :
- de lancer l'application **en local** (démonstration, développement), y compris **hors ligne** ;
- de la déployer **sur un serveur** avec un comportement identique.

## 2. Prérequis serveur
- Un serveur Linux (VPS) avec Docker et Docker Compose installés.
- Un utilisateur de déploiement **non-root**, membre du groupe docker.
- Accès **SSH par clé uniquement** (mot de passe désactivé).
- Pare-feu limité aux ports nécessaires (22, 80, 443).

## 3. Étapes de déploiement
1. Copier le projet sur le serveur (git clone du dépôt).
2. Créer sur le serveur un fichier `.env` avec les vraies valeurs
   (mots de passe forts, APP_ENV=prod, APP_DEBUG=0). Ce fichier n'est **jamais versionné**.
3. Lancer : `docker compose up -d --build`
4. Exécuter les migrations : `docker compose exec app php bin/console doctrine:migrations:migrate`
5. Insérer les données de départ : `docker compose exec app php bin/console app:seed`
6. Vérifier que l'application répond sur l'adresse du serveur.

## 4. Intégration continue (CI)
Le fichier `.github/workflows/ci.yml` définit un workflow GitHub Actions qui,
à chaque push : installe les dépendances, vérifie la syntaxe PHP, et lance
les tests automatisés. Si un test échoue, le workflow passe au rouge :
le problème est détecté **avant** d'arriver en production.

## 5. Procédure de rollback (retour arrière)
En cas de problème après une mise à jour :
1. Identifier le dernier commit stable : `git log --oneline`
2. Revenir dessus : `git checkout <sha_du_commit_stable>`
3. Reconstruire et relancer : `docker compose up -d --build`
Durée estimée : ~3 minutes. Les données ne sont pas perdues :
elles vivent dans les volumes Docker (MySQL, MongoDB), indépendants du code.

## 6. Sécurité du déploiement
- Aucun secret dans le dépôt : le `.env` est dans le `.gitignore` ;
  un `.env.example` documente les variables attendues sans leurs valeurs.
- Mots de passe de production différents de ceux de développement.
- En production : APP_DEBUG=0 (pas de pages d'erreur détaillées exposées).

## 7. Démonstration hors ligne (oral)
Les images Docker étant déjà téléchargées sur la machine, l'application
tourne en local **sans connexion internet** :
`docker compose up -d` puis http://localhost:8080
