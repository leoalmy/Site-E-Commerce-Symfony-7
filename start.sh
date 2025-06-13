#!/bin/bash

# Attendre que MySQL soit prêt (optionnel mais recommandé)
# echo "Attente de la base de données..."
# until mysqladmin ping -h db --silent; do
#   echo -n "."; sleep 1
# done

# Compiler les assets Symfony
php /var/www/html/Site-E-Commerce-Symfony-7/bin/console asset-map:compile


# Lancer Apache en foreground (sinon le conteneur se termine)
apache2-foreground
