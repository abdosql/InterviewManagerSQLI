# Projet de Gestion des Entretiens chez SQLI

## Table des matières
1. [Objectifs du projet](#1-objectifs-du-projet)
2. [Rôles des utilisateurs](#2-rôles-des-utilisateurs)
   - [Évaluateur](#a-Évaluateur)
   - [Responsable d’entretien (RH)](#b-responsable-d’entretien-rh)
3. [Architecture globale](#3-architecture-globale)
   - [API (Lecture)](#a-api-lecture)
   - [Symfony (Écriture)](#b-symfony-écriture)
4. [Synchronisation](#4-synchronisation)
5. [Prestation d'entretien](#5-prestation-d'entretien)
6. [Technologies utilisées](#technologies-utilisées)
7. [Diagramme de l'architecture](#diagramme-de-l'architecture)

---

### 1. Objectifs du projet
- **Construire un système pour gérer les entretiens** au sein de SQLI. Cela inclut la planification, l'exécution, l'évaluation et la gestion des résultats des entretiens.

### 2. Rôles des utilisateurs
#### a. Évaluateur
- **Lister les entretiens** : Accéder à une liste des entretiens qui lui sont affectés.
- **Accéder aux détails des candidats** : Voir les informations pertinentes sur les candidats qu'il va évaluer.
- **Saisir les appréciations** : Enregistrer des commentaires et des notes après chaque entretien.
- **Valider ou non une candidature** : Prendre une décision finale sur le candidat après l'entretien.

#### b. Responsable d’entretien (RH)
- **Planifier les entrevues** : Programmer des entretiens pour les évaluateurs.
- **Consulter l’agenda des évaluateurs** : Voir les disponibilités et les plannings des évaluateurs.
- **Modifier les détails des entretiens** : Replanifier, annuler ou ajuster les horaires des entretiens.
- **Changer le statut des entretiens** : Mettre à jour l'état d'un entretien (prévu, en cours, terminé, etc.).
- **Voir la prestation d’entretien** : Évaluer la qualité et la performance de l'entretien, y compris les évaluations et les notes données par les évaluateurs.

### 3. Architecture globale
#### a. API (Lecture)
- **Utiliser API Platform 3** : Une plateforme pour construire des API RESTful.
- **Connectée à MongoDB** : La base de données utilisée pour stocker les informations sur les entretiens.
- **Opérations de lecture uniquement** : Aucune opération d'écriture, seulement des requêtes GET pour récupérer les données.
- **Fonctionnalités** :
  - Récupérer la liste des entretiens.
  - Voir les détails d’un entretien.

#### b. Symfony (Écriture)
- **Projet en Symfony 6.3** : Utiliser le framework Symfony pour le développement backend.
- **Base de données MySQL** : Utiliser MySQL pour les opérations d'écriture.
- **Aucune opération de lecture** : Toutes les lectures sont effectuées via l'API.
- **Fonctionnalités** :
  - Connexion des utilisateurs.
  - Planification des entretiens.
  - Changement du statut des entretiens.
  - Saisie des appréciations après les entretiens.

### 4. Synchronisation
- **Avec MongoDB de manière asynchrone** : La synchronisation ne se fait pas en temps réel.
- **Utilisation de Messenger et RabbitMQ** : Pour gérer la communication et la synchronisation asynchrone.

### 5. Prestation d'entretien
- **Évaluation de la qualité de l'entretien** : Analyse de la manière dont l'entretien a été mené.
- **Retour des évaluateurs** : Commentaires et notes des évaluateurs sur la performance des candidats.
- **Résultats de l'entretien** : Décision finale concernant le candidat, qu'il soit recommandé pour l'embauche ou non.

### Technologies utilisées
- **API Platform 3**
- **MongoDB**
- **Symfony 6.3**
- **MySQL**
- **RabbitMQ**
- **Messenger**
