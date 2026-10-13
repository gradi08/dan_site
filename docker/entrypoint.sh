#!/bin/sh
set -e

# Les hébergeurs type Render/Railway imposent le port via $PORT.
if [ -n "$PORT" ]; then
    sed -ri "s/Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
    sed -ri "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf
fi

# Création du schéma (idempotent) ; SEED_DEMO=true ajoute les données de démo si la base est vide.
if [ "${SEED_DEMO:-false}" = "true" ]; then
    su www-data -s /bin/sh -c "php bin/setup.php --seed"
else
    su www-data -s /bin/sh -c "php bin/setup.php"
fi

exec "$@"
