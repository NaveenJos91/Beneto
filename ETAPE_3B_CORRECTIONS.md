# Étape 3B — Corrections : 504, mot de passe fort, RGPD, éco-conception

## Ce qui change
- Correction du **504** (bcrypt + délais Nginx augmentés).
- Mot de passe : **double saisie** + règles fortes (8 caractères min, minuscule,
  majuscule, chiffre, caractère spécial).
- **RGPD** : case de consentement obligatoire, page "Politique de confidentialité",
  suppression de compte depuis le profil (droit à l'effacement).
- **Éco-conception** : mesures documentées (rendu serveur, polices système, pas de JS lourd,
  OPcache, requêtes optimisées).
- Correction de l'affichage des **cases à cocher** (alignées avec leur libellé).

## À faire (dans l'ordre)
1. **Remplace** ton dossier `beneto` par le contenu de ce zip (écrase).
2. Applique la nouvelle config Nginx et Symfony :
   ```
   docker compose up -d --build
   docker compose restart web
   docker compose exec app php bin/console cache:clear
   ```
   (aucune nouvelle dépendance à installer, tout est déjà là)

3. Teste :
   - **http://localhost:8080/inscription** : la 1re soumission peut prendre quelques
     secondes (compilation), c'est normal, ça ne fera plus de 504.
   - Vérifie : double mot de passe, règles de robustesse, case RGPD obligatoire,
     cases apprenant/bénévole bien alignées.
   - Après connexion → **Mon profil** : bouton de suppression de compte (RGPD).
   - Pied de page → **Politique de confidentialité**.

## Si le compte de test précédent bloque
Si l'inscription dit "un compte existe déjà avec cet email" (créé lors du 504) :
va dans phpMyAdmin (base `beneto`, table `users`), supprime la ligne, et réessaie.

## Compétences validées par cette étape
- Sécurité mot de passe renforcée (hashage bcrypt, robustesse) → C3.
- **Conformité RGPD** (consentement, information, droit à l'effacement) → C2.
- **Éco-conception** (au moins une mesure, ici plusieurs documentées) → C2.
