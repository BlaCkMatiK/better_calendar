Script de présentation vidéo - Infrastructure technique de Better Calendar
Introduction : (Face caméra) "Bonjour à tous, aujourd'hui, nous allons vous présenter l'infrastructure technique de Better Calendar, une application conçue pour améliorer la gestion des ressources sur notre campus. 

Ce projet a été développé en PHP, sur une infrastructure sécurisée et évolutive, en utilisant plusieurs technologies. Dans cette vidéo, nous allons explorer en détail les éléments clés qui composent notre infrastructure."

1. Hébergement et Système d'exploitation : (Transition vers un schéma ou diapositive montrant le serveur Ubuntu) "Notre application est hébergée sur un serveur Ubuntu 22.04.4, qui constitue la base de notre système.

2. Conteneurisation avec Docker et Orchestration Docker Swarm : (Transition vers une animation ou un schéma expliquant Docker) "Nous avons mis en place une architecture de conteneurisation avec Docker swarm, ce qui nous permet d’isoler chaque service dans des conteneurs distincts. ceux qui facilite la gestion, l'évolutivité de notre infrastructure.

Docker Swarm pour orchestrer ces conteneurs, en garantissant une haute disponibilité et une meilleure gestion des ressources. Docker Swarm nous permet de scaler facilement nos services si la charge augmente."

3. Services Conteneurisés : (Visuel des différents services dans Docker Swarm) "Parmi les services déployés dans notre cluster Docker Swarm, nous avons :

Traefik, qui agit comme un reverse proxy et routeur dynamique. Il gère le trafic externe et les certificats SSL via Let's Encrypt.
Apache avec PHP, notre serveur web qui exécute l'application PHP.
MySQL, notre base de données relationnelle où sont stockées toutes les informations sur les utilisateurs, les réservations, et les événements.
PhpMyAdmin, pour gérer graphiquement la base de données MySQL.
Nextcloud, pour la gestion des fichiers liés aux événements et cours."
4. Sécurité : (Visuel sur la sécurité, avec des icônes pour chaque mesure) "La sécurité est une priorité dans Better Calendar. Voici quelques-unes des mesures que nous avons mises en place :

Utilisation de variables d'environnement pour protéger les identifiants sensibles, tels que les informations de connexion à la base de données.
Protection des sessions avec des cookies sécurisés (HTTPOnly et Secure).
Protection CSRF sur les formulaires pour éviter les attaques de type Cross-Site Request Forgery.
Sanitisation des données d'entrée pour éviter les injections SQL et les attaques XSS.
ModSecurity, un Web Application Firewall (WAF), intégré à Traefik pour filtrer les requêtes HTTP et bloquer les attaques courantes."
5. Middleware et Optimisations : (Visuel illustrant le WAF et le Rate Limiting) "En plus de ces mesures, nous avons également mis en place des middleware importants :

ModSecurity agit comme un pare-feu d'application web pour bloquer les tentatives d'attaques.
Rate Limiting permet de limiter le nombre de requêtes par utilisateur, évitant ainsi les abus de trafic ou les attaques par déni de service."



6.Analyse de code et sécurité : (Schéma ou visuel montrant les outils utilisés comme SonarQube et OWASP ZAP)
"Pour garantir la qualité et la sécurité de notre code, nous avons effectué des analyses avec SonarQube et OWASP ZAP.
SonarQube nous a aidés à identifier les bugs, vulnérabilités et problèmes de maintenabilité dans notre code. Cela nous permet de respecter les meilleures pratiques de développement et d’améliorer en continu la qualité de notre application.
OWASP ZAP est un outil de test d'intrusion qui nous a permis d'analyser la sécurité de notre application web. Grâce à cet outil, nous avons réalisé un scan complet pour détecter des vulnérabilités potentielles. Le scan a révélé quelques petits problèmes de configuration, mais aucune faille critique."



Conclusion : (Face caméra ou vue d'ensemble de l'équipe)
"En résumé, l’infrastructure technique de Better Calendar repose sur une architecture conteneurisée, évolutive et sécurisée, intégrant Docker Swarm pour l’orchestration, Traefik pour la gestion du trafic, et des outils robustes de sécurité comme ModSecurity, SonarQube et OWASP ZAP.

Nous sommes fiers de ce que nous avons accompli et de la fiabilité que cette infrastructure apporte à l'application. Merci d’avoir suivi cette présentation, et à bientôt pour d’autres innovations avec Better Calendar !
