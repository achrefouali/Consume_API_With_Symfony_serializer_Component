# Readme ATS
========================
## Présentation

## Installation
### Installer les bibliothèques :

Ouvrir une invite de commande dans le dossier du projet et taper les commandes suivantes :

    composer install

### Mise en place :
 1. Copier et renommer le fichier parameters.yml.dist à parameters.yml sous app/config

Création de la base de données  :
     
     php bin/console doctrine:database:create
     php bin/console doctrine:schema:update --force 
    
Charger les ressources :

     php bin/console assets:install 
     php bin/console assetic:dump 

Vider le cache :

     php bin/console cache:clear -e prod
    
## API 

### Utiliser la commande :

 1. Collecte de donnée à travers une commande :
  
    
    
     php bin/console ats:cron:importRss 
    
	


