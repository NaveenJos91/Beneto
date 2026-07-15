# Guide de lancement pas à pas (débutant)

## Étape 1 — Installer Docker Desktop
- Va sur https://www.docker.com/products/docker-desktop/ et installe Docker Desktop.
- Lance Docker Desktop et attends qu'il indique "running" (l'icône baleine en haut).

## Étape 2 — Ouvrir un terminal dans le dossier du projet
- **Windows** : ouvre le dossier `beneto` dans l'explorateur, clic droit → "Ouvrir dans le terminal"
  (ou installe et utilise "Git Bash").
- **macOS** : ouvre Terminal, tape `cd ` (avec un espace) puis glisse le dossier dans la fenêtre, Entrée.

## Étape 3 — Lancer l'application
Tape exactement :
```
docker compose up --build
```
Le premier lancement prend quelques minutes (téléchargements + installation). Laisse tourner.

## Étape 4 — Voir Bénéto
Ouvre ton navigateur sur **http://localhost:8080**
Tu dois voir la page d'accueil de Bénéto. 🎉

## Si un souci
- "port is already allocated" : un autre programme utilise le port. Change `8080` en `8090`
  dans `docker-compose.yml` (ligne `- "8080:80"`) et relance.
- La commande `docker compose` n'existe pas : essaie `docker-compose up --build`.
- Rien ne s'affiche : attends que le terminal indique bien que les conteneurs sont démarrés,
  puis recharge la page.
