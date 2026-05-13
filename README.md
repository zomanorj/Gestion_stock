# 📦 GestiStock - Application de Gestion de Stock

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Stable-blue?style=for-the-badge)]()

**Application web de gestion de stock développée avec Laravel 12**

[![Fonctionnalités](https://img.shields.io/badge/Fonctionnalités-Complètes-brightgreen?style=flat-square)]()
[![Sécurité](https://img.shields.io/badge/Sécurité-RBAC-orange?style=flat-square)]()
[![Responsive](https://img.shields.io/badge/Responsive-Oui-informational?style=flat-square)]()

</div>

---

## 📋 Table des Matières

- [À propos](#-à-propos)
- [Fonctionnalités](#-fonctionnalités)
- [Capture d'écran](#-capture-décran)
- [Stack Technique](#-stack-technique)
- [Installation](#-installation)
- [Comptes de Démo](#-comptes-de-démo)
- [Structure du Projet](#-structure-du-projet)
- [Architecture & Bonnes Pratiques](#-architecture--bonnes-pratiques)
- [Auteur](#-auteur)
- [License](#-license)

---

## 🎯 À propos

**GestiStock** est une application web complète de gestion de stock conçue pour les petites et moyennes entreprises. Elle permet de gérer efficacement les produits, catégories, fournisseurs et les mouvements de stock (entrées/sorties) avec une interface intuitive et des fonctionnalités avancées comme les alertes de stock faible et les exports de données.

Développée dans le cadre d'un projet académique (Licence 3 Informatique), cette application met en œuvre les bonnes pratiques du développement web moderne avec Laravel 12.

---

## ✨ Fonctionnalités

### 🔐 Authentification & Sécurité
- ✅ **Inscription / Connexion** sécurisée avec Laravel Breeze
- ✅ **Gestion des rôles** : Admin (accès total) et Gestionnaire (lecture + mouvements)
- ✅ **Protection des routes** par middleware selon les permissions
- ✅ **Déconnexion** sécurisée

### 📊 Gestion des Produits
- ✅ **CRUD complet** des produits (création, lecture, modification, suppression)
- ✅ **Upload d'images** pour les produits
- ✅ **Gestion des catégories** et des fournisseurs
- ✅ **Suivi des quantités** en temps réel

### 📦 Mouvements de Stock
- ✅ **Entrées de stock** (achats, retours, productions)
- ✅ **Sorties de stock** (ventes, pertes, consommations)
- ✅ **Historique complet** des mouvements
- ✅ **Transactions DB** pour garantir l'intégrité des données

### 📈 Tableau de Bord
- ✅ **Statistiques en temps réel** (valeur du stock, produits critiques, etc.)
- ✅ **Graphique Chart.js** de l'évolution des stocks
- ✅ **Derniers mouvements** affichés
- ✅ **Indicateurs clés** de performance

### 🚨 Alertes & Notifications
- ✅ **Alertes automatiques** de stock faible
- ✅ **Seuils configurables** par produit
- ✅ **Liste des produits critiques**

### 📤 Exports de Données
- ✅ **Export Excel** des produits et mouvements (avec filtre par dates)
- ✅ **Export PDF** des rapports d'alertes et fiches produits
- ✅ **Génération de rapports** professionnels

### 🎨 Interface Utilisateur
- ✅ **Design responsive** Bootstrap 5
- ✅ **Icônes Bootstrap Icons**
- ✅ **Navigation intuitive** avec sidebar
- ✅ **Formulaires validés** côté serveur

---

## 📸 Capture d'écran

### Tableau de Bord
![Dashboard](screenshots/dashboard.png)
*[screenshot_dashboard] - Vue d'ensemble avec statistiques et graphique*

### Gestion des Produits
![Produits](screenshots/produits.png)
*[screenshot_produits] - Liste des produits avec actions CRUD*

### Mouvements de Stock
![Mouvements](screenshots/mouvements.png)
*[screenshot_mouvements] - Historique des entrées et sorties*

### Alertes Stock Faible
![Alertes](screenshots/alertes.png)
*[screenshot_alertes] - Produits en rupture ou stock critique*

---

## 🛠 Stack Technique

| Catégorie | Technologie | Version |
|-----------|-------------|---------|
| **Framework Backend** | Laravel | 12.x |
| **Langage** | PHP | 8.2+ |
| **Base de données** | MySQL | 8.0+ (XAMPP) |
| **Frontend** | Bootstrap | 5.x |
| **Icônes** | Bootstrap Icons | 1.x |
| **Graphiques** | Chart.js | 4.x |
| **Authentification** | Laravel Breeze | 2.x |
| **Permissions** | Spatie Laravel Permission | 6.x |
| **Export Excel** | Maatwebsite Excel | 3.x |
| **Export PDF** | Barryvdh DomPDF | 2.x |
| **Serveur de dev** | Laravel Artisan | Intégré |

---

## 🚀 Installation

### Prérequis

- **XAMPP** (ou équivalent : WAMP, MAMP) avec :
  - PHP 8.2+
  - MySQL 8.0+
  - Apache
- **Composer** (gestionnaire de dépendances PHP)
- **Node.js & npm** (pour les assets frontend)
- **Git**

### Étape 1 : Cloner le projet

```bash
# Cloner le dépôt
git clone https://github.com/votre-utilisateur/gestistock.git

# Accéder au répertoire
cd gestistock
```

### Étape 2 : Installer les dépendances

```bash
# Installer les dépendances PHP
composer install

# Installer les dépendances Node.js
npm install && npm run build
```

### Étape 3 : Configurer l'environnement

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

### Étape 4 : Configurer la base de données

1. **Démarrer XAMPP** (Apache et MySQL)
2. **Créer la base de données** via phpMyAdmin :
   - Nom : `gestistock`
   - Encodage : `utf8mb4_unicode_ci`

3. **Modifier le fichier `.env`** :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestistock
DB_USERNAME=root
DB_PASSWORD=
```

### Étape 5 : Exécuter les migrations et seeders

```bash
# Exécuter les migrations (crée les tables)
php artisan migrate

# Exécuter les seeders (peuple la base avec les rôles et comptes de démo)
php artisan db:seed
```

### Étape 6 : Lancer l'application

```bash
# Option 1 : Serveur de développement Laravel (recommandé)
php artisan serve

# L'application est accessible à : http://localhost:8000
```

**Ou avec XAMPP :**
- Copier le dossier `gestistock` dans `C:\xampp\htdocs\`
- Accéder à : `http://localhost/gestistock/public`

---

## 👤 Comptes de Démo

Deux comptes sont pré-configurés pour tester les différentes fonctionnalités :

| Rôle | Email | Mot de passe | Permissions |
|------|-------|--------------|-------------|
| **Administrateur** | `admin@gestistock.com` | `password` | Accès total (CRUD complet, gestion utilisateurs) |
| **Gestionnaire** | `gestionnaire@gestistock.com` | `password` | Lecture + mouvements de stock (entrées/sorties) |

> ⚠️ **Important** : Ces comptes sont destinés uniquement à des fins de démonstration. Pensez à modifier les mots de passe en production.

---

## 📁 Structure du Projet

```
gestistock/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Contrôleurs (ProductController, etc.)
│   │   ├── Middleware/           # Middleware personnalisé
│   │   ├── Requests/             # Form Requests (validation)
│   │   └── Kernel.php            # Kernel HTTP
│   ├── Models/                   # Modèles Eloquent
│   │   ├── Product.php           # Modèle Produit
│   │   ├── Category.php          # Modèle Catégorie
│   │   ├── Supplier.php          # Modèle Fournisseur
│   │   ├── StockMovement.php     # Modèle Mouvement
│   │   └── User.php              # Modèle Utilisateur
│   ├── Providers/                # Service Providers
│   └── View/                     # View Components
│
├── bootstrap/                    # Fichiers de bootstrap
│
├── config/                       # Fichiers de configuration
│
├── database/
│   ├── migrations/               # Migrations de base de données
│   ├── seeders/                  # Seeders (données de test)
│   └── factories/                # Factories (génération de données)
│
├── public/                       # Point d'entrée (index.php)
│   └── assets/                   # Assets compilés
│
├── resources/
│   ├── views/                    # Vues Blade
│   │   ├── dashboard.blade.php   # Tableau de bord
│   │   ├── products/             # Vues produits
│   │   ├── categories/           # Vues catégories
│   │   ├── suppliers/            # Vues fournisseurs
│   │   ├── movements/            # Vues mouvements
│   │   └── layouts/              # Layouts (app.blade.php)
│   ├── js/                       # JavaScript
│   └── css/                      # CSS/SCSS
│
├── routes/
│   ├── web.php                   # Routes web
│   ├── api.php                   # Routes API (si applicable)
│   └── console.php               # Commandes Artisan
│
├── storage/                      # Fichiers générés (logs, uploads)
│   └── app/
│       └── public/
│           └── products/         # Images des produits
│
├── tests/                        # Tests unitaires et fonctionnels
│
├── .env                          # Variables d'environnement
├── .env.example                  # Exemple d'environnement
├── artisan                       # CLI Laravel
├── composer.json                 # Dépendances PHP
├── package.json                  # Dépendances Node.js
└── README.md                     # Ce fichier
```

---

## 🏗 Architecture & Bonnes Pratiques

Ce projet met en œuvre les bonnes pratiques du développement Laravel :

### Architecture MVC
- **Modèles** : Utilisation d'Eloquent ORM avec relations (hasMany, belongsTo)
- **Vues** : Templates Blade avec héritage de layout
- **Contrôleurs** : Contrôleurs ressources RESTful

### Validation des Données
- **Form Requests** : Classes dédiées pour la validation (`StoreProductRequest`, `UpdateProductRequest`)
- **Règles de validation** : Définies de manière centralisée et réutilisable

### Sécurité
- **Middleware** : Protection des routes par rôle (`role:admin`, `role:gestionnaire`)
- **CSRF Protection** : Tokens CSRF automatiques
- **XSS Protection** : Échappement automatique dans Blade
- **SQL Injection** : Prévention via Eloquent ORM

### Gestion des Rôles
- **Spatie Permission** : Système RBAC (Role-Based Access Control)
- **Permissions granulaires** : `product.create`, `product.update`, `movement.create`, etc.

### Intégrité des Données
- **Transactions DB** : Utilisation de `DB::transaction()` pour les mouvements de stock critiques
- **Foreign Keys** : Contraintes d'intégrité référentielle

### Performance
- **Eager Loading** : Chargement anticipé des relations pour éviter le problème N+1
- **Query Scopes** : Scopes locaux pour les requêtes courantes
- **Caching** : Possibilité de mettre en cache les données statiques

---

## 👨‍💻 Auteur

**Étudiant en Licence 3 Informatique**  
🎓 Formation en développement web et conception d'applications

📧 **Contact** : votre.email@universite.fr  
💼 **Recherche de stage** : Stage de 3 mois en développement web/fullstack  
📅 **Disponibilité** : À partir de [Mois/Année]

---

## 📄 License

Ce projet est open-source sous licence [MIT](LICENSE).

---

<div align="center">

**⚡ Développé avec Laravel 12 par un étudiant passionné**

[⬆ Retour en haut](#-gestistock---application-de-gestion-de-stock)

</div>