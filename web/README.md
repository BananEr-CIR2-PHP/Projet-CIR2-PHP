##### Projet-CIR2-PHP #####

Projet CIR2 PHP site médical
Ce projet permet à l'utilisateur de créer un compte à but médical afin de prendre facilement ses rendez-vous médicaux.
Il permet aussi de visualiser les disponibilités des médecins pour prendre le créneau le plus intéressant pour l'utilisateur.


--------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------


### CONNEXION ET INSCRIPTION ###

# Structure des fichiers #

login.php : Page principale de connexion.
request.php : Fichier de traitement des requêtes de connexion et de déconnexion.
register.php : Page d'inscription des nouveaux utilisateurs.
database.php : Fichier contenant les fonctions de connexion à la base de données.

# Utilisation #

Connexion :

Ouvrez login.php dans votre navigateur.
Entrez votre adresse email et votre mot de passe.
Cliquez sur "Se connecter".

Inscription :

Ouvrez register.php dans votre navigateur.
Remplissez le formulaire avec votre nom, prénom, téléphone, email, confirmation d'email et mot de passe.
Cliquez sur "S'inscrire".

Déconnexion :

Cliquez sur le bouton "Se déconnecter" dans la barre de navigation.


--------------------------------------------------------------------------------------------------------------------------


### HISTORIQUE DES RENDEZ-VOUS ###

# Structure du Projet #

historique.php : Page principale affichant l'historique des rendez-vous.
request.php : Fichier de traitement des requêtes de déconnexion et de prise de rendez-vous.
database.php : Fichier contenant les fonctions de connexion à la base de données.
time.php : Fichier contenant des fonctions pour la gestion des dates et heures.

# Utilisation #

Consultation de l'historique des rendez-vous :

Ouvrez historique.php dans votre navigateur.
Si vous n'êtes pas connecté, vous serez redirigé vers login.php.
Une fois connecté, vous verrez la liste de vos rendez-vous passés et à venir.

Prendre un rendez-vous :

Cliquez sur le bouton "Prendre rendez-vous" en haut de la page.
Vous serez redirigé vers medecins.php pour choisir un médecin et un créneau horaire.


--------------------------------------------------------------------------------------------------------------------------


### LISTE DES MEDECINS DISPONIBLES ###

# Structure du Projet #

medecins.php : Page principale affichant la liste des médecins.
request.php : Fichier de traitement des requêtes de déconnexion et de prise de rendez-vous.
database.php : Fichier contenant les fonctions de connexion à la base de données.

# Utilisation #

Consultation de la liste des médecins :

Ouvrez medecins.php dans votre navigateur.
Si vous n'êtes pas connecté, vous serez redirigé vers login.php.
Utilisez les champs de recherche pour filtrer les médecins par nom, spécialité ou établissement.
Cliquez sur "Rechercher" pour afficher les résultats correspondants.

Prendre un rendez-vous :

Cliquez sur le bouton "Prendre rendez-vous" à côté du médecin souhaité.
Vous serez redirigé vers creneaux_rdv.php pour choisir un créneau horaire.


--------------------------------------------------------------------------------------------------------------------------


### CALENDRIER DES CRENEAUX DISPONIBLES ###

# Structure du Projet #

creneaux_rdv.php : Page principale affichant les créneaux de rendez-vous disponibles pour un médecin.
request.php : Fichier de traitement des requêtes de déconnexion et de prise de rendez-vous.
database.php : Fichier contenant les fonctions de connexion à la base de données.
time.php : Fichier contenant des fonctions pour la gestion des dates et heures.

# Utilisation #

Consultation des créneaux de rendez-vous :

Ouvrez creneaux_rdv.php dans votre navigateur.
Si vous n'êtes pas connecté, vous serez redirigé vers login.php.
Si aucun médecin n'est sélectionné, vous serez redirigé vers medecins.php.
Utilisez les boutons de navigation pour changer de semaine et consulter les créneaux disponibles.

Prendre un rendez-vous :

Cliquez sur un créneau disponible dans le tableau.
Confirmez les détails du rendez-vous dans la fenêtre modale qui s'affiche.
Cliquez sur "Prendre rendez-vous" pour finaliser la réservation.


--------------------------------------------------------------------------------------------------------------------------


### DEPENDANCES ###

Bootstrap 5.3.3


--------------------------------------------------------------------------------------------------------------------------


### AUTEURS ###

Erwan Langlais
Alexis Rochon--Sanz