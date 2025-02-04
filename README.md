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

bash
symfony server:start
Accédez à l'application dans votre navigateur à l'adresse suivante :

cpp
http://localhost:8000

Structure du projet
Voici la structure principale du projet :

/mini-bibliotheque
│
├── /assets              # Contient les fichiers CSS et JavaScript
├── /config              # Contient la configuration de Symfony
├── /public              # Contient les fichiers publics comme index.php
├── /src                 # Contient les contrôleurs, entités et services
│   ├── /Controller      # Contient les contrôleurs pour les livres, emprunts et utilisateurs
│   ├── /Entity          # Contient les entités (modèles de données)
├── /templates           # Contient les templates Twig pour l'affichage
│   ├── /books           # Templates pour la gestion des livres
│   ├── /borrowings      # Templates pour la gestion des emprunts
│   └── /users           # Templates pour la gestion des utilisateurs
├── /translations        # Contient les fichiers de traduction
└── /var                 # Contient les fichiers temporaires et le cache
