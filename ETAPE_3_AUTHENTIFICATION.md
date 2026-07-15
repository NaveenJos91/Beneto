# Étape 3 — Inscription, connexion, profil (authentification)

On ajoute : la création de compte (avec choix apprenant / bénévole / les deux),
la connexion, la déconnexion, et une page profil.

## À faire (dans l'ordre EXACT)

1. **Remplace** ton dossier `beneto` par le contenu de ce zip (écrase les fichiers).

2. Docker Desktop allumé, puis dans un terminal dans le dossier `beneto` :
   ```
   docker compose up -d --build
   ```

3. **Installe les composants de sécurité** (IMPORTANT : à faire avant tout le reste) :
   ```
   docker compose exec app composer require symfony/security-bundle symfony/form symfony/validator symfony/security-csrf
   ```

4. Vide le cache (par précaution après l'ajout de la sécurité) :
   ```
   docker compose exec app php bin/console cache:clear
   ```

5. **Ajoute les villes et thèmes de départ** :
   ```
   docker compose exec app php bin/console app:seed
   ```

6. Teste dans le navigateur :
   - Va sur **http://localhost:8080/inscription**
   - Crée un compte (choisis un rôle ou les deux), puis connecte-toi sur **/connexion**.
   - Va sur **Mon profil** : tu vois tes infos et tes rôles.

## Se donner le rôle Administrateur (pour ton compte de démo)
Le rôle admin ne se choisit pas à l'inscription (sécurité). Pour te le donner :
1. Va sur phpMyAdmin (http://localhost:8081), base `beneto`, table `users`.
2. Trouve ta ligne, colonne `roles`, et mets la valeur :
   ```
   ["ROLE_ADMIN"]
   ```
   (ou `["ROLE_BENEVOLE","ROLE_ADMIN"]` pour cumuler)
3. Déconnecte-toi puis reconnecte-toi pour activer le rôle.

## Si un souci
- Une erreur au démarrage mentionnant "SecurityBundle" : c'est que l'étape 3 (composer require)
  n'a pas été faite AVANT de charger une page. Refais l'étape 3 puis l'étape 4.
- Toute autre erreur rouge : copie-moi le message.

## Ce que cette étape valide (grille du jury)
- Mots de passe **hashés** automatiquement (bcrypt/argon2) → sécurité C3.
- **Validation des entrées** (email, champs requis) → C3.
- **Contrôle d'accès par rôle** (apprenant/bénévole/admin) → C3.
- Base du **RGPD** (compte utilisateur, email unique) → C2.
