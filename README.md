# Mini Bibliothèque Partagée

## Description

Ce projet est une application Symfony permettant de gérer une mini-bibliothèque partagée. Il permet à un utilisateur de visualiser, ajouter, modifier et supprimer des livres, ainsi que de gérer les emprunts de ces livres. Les utilisateurs peuvent consulter les livres disponibles, les emprunter et les retourner. L'application utilise JSON pour stocker les données des livres, des utilisateurs et des emprunts.

## Fonctionnalités

- **Gestion des livres** : Ajout, modification, suppression, affichage.
- **Gestion des emprunts** : Emprunter, retourner et suivre l'état des emprunts.
- **Gestion des utilisateurs** : Visualisation des utilisateurs, rôle (admin/user), ajout et modification des utilisateurs.
- **Interface conviviale** : Affichage des informations sous forme de tableaux avec des actions pour chaque élément (modifier, supprimer, voir les détails).

## Prérequis

- PHP >= 7.4
- Composer
- Symfony >= 5.0
- Une base de données de type JSON pour stocker les informations.

## Installation

1. Clonez le projet depuis GitHub :
   ```bash
   git clone https://github.com/nom-utilisateur/mini-bibliotheque.git
   cd mini-bibliotheque
   
Installez les dépendances avec Composer :
composer install

Démarrez le serveur de développement Symfony :
symfony server:start

Accédez à l'application dans votre navigateur à l'adresse suivante :
http://localhost:8000
