# ProjArt-fromneibaf
Projet du groupe N°5 fromneibaf pour le module ProjArt de la classe M49-2 de la HEIG-VD

Le site a été dévelopée dans une optique **Mobile First**, il est donc coneillé d'y accéder via un smartphone et même si possible de le télécharger pour accéder à sa version PWA.

Le projet est disponible ici : [Packige](https://fromneibaf-pingouin.heig-vd.ch/).


## Concept
Le but de l’application est d’être un compagnon pour les étudiants, en leur mettant à disposition, de manière facilement accessible, toutes les fonctionnalités utilisées fréquemment.

## Fonctionnalités
Les fonctionnalités seront les suivantes  : 

- Horaire des différentes classes accessible à tous, sous forme de liste ou de calendrier.
- Journal de classe des différents rendus et examens à venir, sous forme de liste. Un utilisateur connecté à son compte peut ajouter ou supprimer les éléments, et biffer un rendu qu’il aurait déjà terminé.
- Page de consultation et insertion des notes, avec calcul automatique des moyennes de cours et de modules suivant la pondération
- Liste de news de L’AGE et des futurs événements à venir.
- Informations utiles :
    - Menu du jour à la cafétéria
    - Lien vers les notes officielles de GAPS
    - Lien vers les absences et vers le formulaire d’absence
    - Liens personnels
    - ...

## Installation
*Avant d'entamer l'installation du projet, assurez-vous d'avoir **Composer** et  **php 8.1** ou une version supérieure*

Voici les différentes étapes d'installation à suivre pour exécuter le projet :
1. Faire un `git pull` du projet
2. Se rendre dans le repository en local puis dans le répertoire /Packige/
3. Lancer la commmande `composer install`
4. Lancer la commmande `npm install`, puis `npm run dev`.
5. Répéter l'étape 4 en ré-exécutant ces deux commandes.
6. Dupliquer le fichier ".env.example" et renommer en ".env"
7. Lancer la commande `php artisan key:generate`
8. Créer une base de données MySQL vide
9. Vérifier dans le fichier **php.ini** que la ligne `extension=pdo_mysql` ne soie pas en commentaire.
10. Modifier le fichier ".env" pour y ajouter les informations de connexion à votre base de données précédement créée.
11. Toujours dans le terminal à la position /Packige/, lancer les commandes `php artisan migrate` puis `php artisan db:seed`
12. Vérifier que tout fonctionne en lançant le serveur grâce à la commande `php artisan serve`, puis dans un autre terminal, `npm run watch`.

## Informations de connexion
### Utilisateurs
Le seeding de la base de données y ajoute 2 utilisateurs de test :
- Nom :         Evan You
- Email :       evan.you@heig-vd.ch
- Password :    password
- Classe :     M49-2
---
- Nom :         John Doe
- Email :       john.doe@heig-vd.ch
- Password :    password
- Classes :     M49-2 & M50-2

### Classes
La création d'un nouvel utilisateur nécessite un code de classe. Les code sont les suivants
- M48 :       123456
- M49-1 :     123456
- M49-2 :     123456
- M50-1 :     123456
- M50-2 :     123456
- M50-3 :     123456


