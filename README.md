Gestionnaire de Club de Lecture (Application PHP)

Une application web complète pour gérer un club de lecture, développée en PHP natif (Procédural) et MySQL, respectant l'architecture MVC simplifiée (séparation du HTML/CSS et du Backend).

Fonctionnalités développées

Authentification & Sécurité: Inscription, connexion, gestion des sessions et mots de passe hashés. Sécurisation totale des requêtes (PDO / anti-injection SQL).

Gestion des Rôles: Barrières de sécurité strictes selon 3 rôles (Admin, Modérateur, Membre). Page d'administration dédiée.

Bibliothèque (CRUD): Ajout, modification et suppression de livres avec système d'upload d'images de couverture.

Système d'Avis interactif: Les membres peuvent noter et commenter. Modification possible par l'auteur, et modération par les Admins.

Suivi de Progression: Sauvegarde du pourcentage de lecture par membre et calcul automatique de la moyenne du club affichée via une jauge dynamique.

Gestion des Évènements (Sessions): Création de rencontres liées aux livres, système d'inscriptions et interface de pointage des présences (Appel).

Ressources Sécurisées (PDF): Upload de documents par les Modérateurs et script de téléchargement sécurisé (download.php) vérifiant les droits d'accès.

Tableau de Bord: Interface affichant les statistiques clés de la base de données en temps réel.

Technologies utilisées

Backend: PHP (PDO)

Base de données: MySQL (Fichier .sql inclus dans le dépôt)

Frontend: HTML & CSS
