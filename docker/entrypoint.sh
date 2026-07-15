#!/bin/sh
set -e
cd /var/www

# Installe les dépendances Composer la première fois (dossier vendor absent)
if [ ! -d vendor ]; then
  echo ">> Installation des dependances Composer (premier lancement, patientez)..."
  composer install --no-interaction --prefer-dist
fi

# Droits d'ecriture pour le cache et les logs de Symfony
mkdir -p var
chmod -R 777 var

exec "$@"
