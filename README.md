# ProjArt-fromneibaf
Projet du groupe N°5 fromneibaf pour le module ProjArt de la classe M49-2 de la HEIG-VD

## Concept
Le but de l’application est d’être un compagnon pour les étudiants, en leur mettant à disposition, de manière facilement accessible, toutes les fonctionnalités utilisées fréquemment.

## Fonctionnalités
Les fonctionnalités seront les suivantes  : 

- Horaire des différentes classes accessible à tous, sous forme de liste ou de calendrier.
- Journal de classe des différents rendus et examens à venir, sous forme de liste ou de calendrier. Un utilisateur connecté à son compte peut ajouter ou supprimer les éléments, et biffer un rendu qu’il aurait déjà terminé
- Page de consultation et insertion des notes, avec calcul automatique des moyennes suivant la pondération et calcul de la note nécessaire à réaliser au prochain examen pour obtenir une moyenne suffisante.
- Liste de news de L’AGE et des futurs événements à venir.
- Informations utiles :
    - Menu du jour à la cafétéria
    - Lien vers les notes officielles de GAPS
    - Lien vers les absences et vers le formulaire d’absence

## Installation
Voici les différentes étapes d'installation à suivre pour exécuter le projet :
1. Faire un "git pull" du projet
2. Se rendre dans le répertoire /laravel/
3. Lancer la commmande "composer install"
4. Lancer la commmande "npm install", puis "npm run dev".
5. Répéter l'étape 4 en ré-exécutant ces deux commandes.
6. Dupliquer le fichier ".env.example" et renommer en ".env"
7. Lancer la commande "php artisan key:generate"
8. Modifier le fichier ".env" pour y ajouter les informations de connexion à votre base de données.
9. Vérifier que tout fonctionne en lançant le serveur grâce à la commande "php artisan serve", puis dans un autre terminal, "npm run watch".
