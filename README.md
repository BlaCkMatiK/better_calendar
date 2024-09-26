

# Better Calendar

## Contexte du projet

Dans le cadre du **Workshop d'Innovation Numérique**, l'objectif est de concevoir et de développer une **solution globale** pour améliorer la vie des apprenants au sein du campus. Cette solution doit être **évolutive**, avec des fonctionnalités adaptatives répondant à des besoins spécifiques identifiés au sein du campus. 

Le projet **Better Calendar** fait partie de cette solution et se concentre sur la gestion des cours, la réservation de salles et de matériel, tout en offrant des fonctionnalités utiles à la communauté des apprenants. L'application est conçue pour être intuitive, accessible via des plateformes web et mobiles, et facile à gérer pour les administrateurs.

### Objectifs du projet

L'application **Better Calendar** vise à offrir une **gestion complète des ressources** (salles, matériel, événements) et des **informations utiles** au sein du campus. Chaque fonctionnalité a pour but de répondre à des besoins précis :

- Affichage des cours à venir sur une période hebdomadaire ou mensuelle (BDE, EPSI/WIS, campus, etc.).
- Gestion du planning des étudiants (par semaine ou par mois).
- Réservation de salles et de matériel.
- Accès à des informations utiles comme la disponibilité des salles et la gestion des incidents techniques.

Chaque fonctionnalité est développée avec une **partie utilisateur** et une **partie administration** pour la gestion des ressources et des cours.

## Fonctionnalités clés

### 1. Gestion des cours

- **Affichage des cours à venir** : Les utilisateurs peuvent voir les cours prévus au sein du campus. L'affichage comprend les salles, les profs, les horaires ainsi que le type du cours (Présentiel, Distanciel, ...)
- **Vue hebdomadaire ou mensuelle** : Le calendrier offre plusieurs options de visualisation pour les cours.
- **Ajout d'cours par les administrateurs** : Les administrateurs peuvent ajouter, modifier ou supprimer des cours dans le calendrier.

### 2. Gestion des salles et du matériel

- **Réservation de salles** : Les utilisateurs peuvent réserver des salles en fonction de la disponibilité. Les informations sur la capacité des salles et leur occupation sont mises à jour en temps réel.
- **Réservation de matériel** : Les utilisateurs peuvent emprunter du matériel (ex. ordinateurs portables, caméras, équipements audio, etc.) et voir la disponibilité en temps réel.
- **Suivi des emprunts** : Les administrateurs peuvent suivre les emprunts et retours de matériel pour assurer une gestion efficace des ressources.
 
## Technologies utilisées

L'application **Better Calendar** est développée avec les technologies suivantes :

- **PHP** : Langage côté serveur pour gérer la logique de l'application.
- **MySQL** : Base de données pour stocker les informations sur les utilisateurs, les réservations, les événements et les signalements d'incidents.
- **phpMyAdmin** : Utilisé pour la gestion graphique de la base de données MySQL.
- **HTML/CSS** : Langages de structure et de style pour l'interface utilisateur.
- **Bootstrap** : Framework CSS pour assurer une interface responsive et moderne.
- **JavaScript** : Pour l'interactivité côté client, comme la gestion des calendriers et des formulaires.
- **VSCode** : Utilisé comme éditeur de code pour le développement du projet.
- **Ubuntu** : Système d'exploitation pour l'hébergement du serveur de développement.
- **Variables d'environnement** : Utilisées pour la gestion sécurisée des identifiants de connexion à la base de données.

## Application mobile (Kotlin)

En complément de la version web, une **application mobile Android** a été développée en **Kotlin** pour offrir une expérience fluide aux utilisateurs mobiles. L'application utilise un **WebView** pour charger l'interface web de **Better Calendar**.

### Code de l'application Android

```kotlin
package com.workshop.bettercalendar

import android.os.Bundle
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.appcompat.app.AppCompatActivity

class MainActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val webView: WebView = findViewById(R.id.webview)
        webView.webViewClient = WebViewClient()

        webView.settings.javaScriptEnabled = true

        webView.loadUrl("https://workshop.romain-igounet.fr/")
    }
}
```

### Fonctionnement de l'application mobile

- L'application **Kotlin** permet aux utilisateurs d'accéder à la version web de **Better Calendar** via un **WebView**.
- La navigation dans l'application est fluide et identique à la version web.
- Le paramètre `javaScriptEnabled` permet l'exécution des scripts JavaScript pour assurer une expérience utilisateur complète.

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/BlaCkMatiK/better_calendar.git
```

### 2. Configurer la base de données

- Créez une base de données MySQL via **phpMyAdmin** ou la ligne de commande :
  ```sql
  CREATE DATABASE workshop;
  ```
- Importez le schéma SQL fourni pour configurer les tables nécessaires.

### 3. Configurer les variables d'environnement

Créez un fichier `.env` pour stocker les identifiants de connexion à la base de données :

```bash
DB_HOST=localhost
DB_DATABASE=workshop
DB_USERNAME=utilisateur
DB_PASSWORD=motdepasse
```

### 4. Installer les dépendances

Installez les dépendances PHP avec **Composer** :

```bash
composer install
```

### 5. Lancer l'application

Démarrez le serveur Apache sur **Ubuntu** :

```bash
sudo systemctl start apache2
```

Accédez à l'application via `http://localhost/`.

### 6. Déployer l'application Android

- Ouvrez le projet **Kotlin** dans **Android Studio**.
- Exécutez l'application sur un émulateur ou un appareil Android physique.

## Sécurité

L'application intègre plusieurs mesures de sécurité :

- **Variables d'environnement** : Utilisées pour protéger les identifiants de connexion et les informations sensibles.
- **Protection des sessions** : Les cookies de session sont sécurisés avec les drapeaux **HTTP-Only** et **Secure**.
- **Protection CSRF** : Les formulaires incluent des tokens pour éviter les attaques Cross-Site Request Forgery.
- **Sanitisation des données** : Toutes les données d'entrée des utilisateurs sont filtrées pour éviter les failles de sécurité comme les injections SQL et XSS.

## Licence

Ce projet est sous licence **MIT**. Vous êtes libre d'utiliser, de modifier et de distribuer ce projet tant que vous respectez les termes de cette licence.
