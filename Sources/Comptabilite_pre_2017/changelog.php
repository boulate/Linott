<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >


<HEAD>
	<TITLE>Linott: Changelog</TITLE>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>

<BODY>
<pre>

 V 1.0
 - Ajout du graphique de consultation des 6 derniers mois d'heures en cliquant sur le total de la journée.
 - Ajout du graphique de consultation d'évolution du nombre d'heures par semaine en cliquant sur le total de la semaine.
 - Résolution d'un bug dans la zone Administration empêchant certains projets d'être modifiés.
 - Gestion des axes dans la zone administration (comme les gestions projets).
 - Suppression des tables 'typesAbsences' et 'absences' de la base et suppression de leurs dépendances.
 
 V 1.1 (Machine virtuelle V1.1)
 - Les utilisateurs peuvent maintenant choisir d'afficher ou de masquer les axes qu'ils n'utilisent pas.
 - Affichage du total (tous utilisateurs) dans les statistiques par axes (en bleu, tout en bas de la page).
 - La fenêtre choix axe x du récapitulatif statistique propose maintenant tous les axes (même les axes masqués dans les préférences utilisateurs).
 
 V 1.2
 - Ajout d'une table permettant l'enregistrement des configurations en base.
 - Option permettant de définir le nombre de congés payés annuelles
 - Option permettant de définir le nombre de RTT
 
 V 1.3
 - Option permettant de désactiver l'affichage des heures supplémentaires dans la page principale.
 - Option permettant d'ajouter les codes comptables devant les noms dans le choix des axes.
 - Option permettant d'ajouter les codes comptables devant les noms dans le récapitulatif Axes.
 
 V 1.4
 - Option permettant de choisir le premier mois de l'année comptable concernant les congés.
 - Option permettant de choisir le type de RTT (annuel ou trimestriel)
 - Option permettant de désactiver simplement l'axe3 (anciennement projets) et de le renseigner automatiquement le cas échéant.
 
 V 2.0
 - Possibilité d'export des récapitulatifs statistiques au format .csv (avec prise en compte des filtres en cours).
 - Modification du squelette du logiciel pour le rendre plus généraliste: La notion de projet devient une notion d' axe 3.
 
 V 2.0.1
 - Correction d'un bug de passage à la nouvelle année: La validation de la semaine se faisait en A-1.
 - Correction d'un bug de passage à la nouvelle année prenant en compte les totaux des semaines A-1.
 
 V 2.0.2
 - Correction d'un bug empêchant l'affichage de l'axe 3 dans la page consultation de fiche de la section administration.
 
 V 2.1
 - Correction d'un bug empêchant dans certaines conditions la prise en compte de la configuration Mois de départ de l'année comptable.
 - Option permettant de désactiver l'affichage des jours de congés restants dans la page principale.
 - Option permettant de désactiver l'affichage des jours de RTT restants dans la page principale.
 - Correction d'un bug affectant le filtre période (mm/aa - mm/aa) dans les statistiques par axes et par absences.
 - Modification du fonctionnement des statistiques par axes: Affichage par défaut de l'année civile en cours au lieu de tout l'historique (il est toujours possible de visionner une année passée en filtrant par période: 01/aa - 12/aa).
 - Modification du fonctionnement des statistiques par absences: Affichage par défaut de l'année comptable en cours (prenant le mois de départ d'année comptable défini dans les configurations) au lieu de tout l'historique. il est toujours possible de visionner une année passée en filtrant par période, ex: 06/aa - 05/aa+1.
 
 V 2.1.1
 - Correction d'un bug empechant de renseigner en une seule fois les périodes du matin et de l'après midi aux personnes ayant choisi de désactiver l'axe 3.
 - Correction de la facon dont les récupération sont prises en compte. Ce n'est plus l'ID axe 2 qui est pris en compte mais le nom du congé pausé (qui doit être récupération ou Récupération pour être pris en compte).
 
 V 2.2
 - Correction d'un bug dans le module d'export qui prenait en compte toutes les années et pas seulement l'année en cours si aucune date n'était précisé dans le module récapitulatif: axes.
 - Correction d'un bug déclenchant un mauvais affichage du nombre d'heures rachetées dans le récapitulatif d'absences en cas de selection d'une période ou d'un mois défini.
 - Prise en compte de la configuration Mois de départ de l'année comptable pour l'affichage du nombre d'heures rachetées sur la période.
 - Simplification de la mise à jour: La page update.php permet maintenant de mettre à jour la base de données pour intégrer les nouvelles options.
 - Modification de base: La taille du champ valeur de la table Configuration passe de 16 à 255 pour permettre les nouvelles options de configuration.
 - Favicon active à présent sur toutes les pages.
 - La partie configuration a été revue pour plus de clarté.
 - Option permettant de désactiver la case à cocher et le menu déroulant visant à remplir automatiquement les heures d'absences d'une journée.
 - Option permettant de définir les Axes2 à ignorer dans les totaux d'heures ainsi que dans les récapitulatifs et les exports (utile par exemple si vous souhaitez exclure les heures de congés, RTT, etc. du total si vous êtes annualisés).
 
 V 2.2.1
 - Les Axe2 à exclure des totaux sont maintenant exclus égalemement du total de la journée.
 
 V 2.2.2
 - Correction d'un bug survenu suite à l'ajout de la fonction exclure axe2 qui affichait parfois le total de la journée du mois précédent.
 
 V 2.3
 - Les codes section peuvent maintenant contenir 1 ou 2 chiffres.
 - L'axe 3 a maintenant un code comptable (pour coller aux nouvelles exigences analytiques).
 - Ajout d'une nouvelle fonctionnalité (via Récapitulatifs / Axes / Exporter les données) permettant de choisir le type d'export et les données exportées.
 - Correction d'un bug pouvant engendrer une erreur d'affichage du total d'heures supplémentaires dans la consultation de fiches de la zone administration.
 - La gestion du nombre de RTT passe en annuel (et non plus en trimestriel). Il est toujours possible de choisir une gestion trimestrielle mais le nombre de RTT est annuel.
 - Correction d'un bug dans le raccourcis permettant de renseigner une journée de congé d'un clic. Celui ci pouvait parfois renseigner le mauvais ID comptable Axe1 et Axe2.
 - Option permettant de choisir le mois de départ des RTT.
 - Correction de l'affichage des RTT restants pour s'adapter au type de gestion (Trimestre ou Annuel) et au mois de départ de prise en compte des RTT.
 - Les fenêtres choix axe1 et choix axe2 n'affichent plus que les axe1 ou axe2 si des codes sections spécifiques ont été ajoutés aux Axe2.

 V 2.3.1
 - Correction d'un bug d'affichage au moment de la selection de la case à cocher Absent toute la journée.
 - Ajout du README.php et changelog.php.

 V 2.4
 - Le logiciel a enfin un nom: Linott. 
 - Modification du bandeau principal, du nom des bases de données, des requetes sur les bases et des liens pour correspondre à ce nouveau nom.
 - Correction d'un bug d'authentification. (modifications nécéssaires dans la BDD)
 - Correction d'un bug d'affichage dans la section Gérer les axes 3 de l'administration.
 - L'outil permettant de trouver le plus vieux jours non validé cherche maintenant uniquement dans l'année en cours de selection, et pas depuis le 01/01/13.

 V 3.0
 - Le logiciel Linott propose maintenant un calendrier permettant de gérer les absences, astreintes, réunions, évènements, etc..
 - Possibilité de choisir plusieurs jours dans le mois et d'y définir un évènement à appliquer (pour poser 3 semaines de congé en quelques clics par exemple).
 - Validation, refus ou mise en attente des demandes de congés en un clic.
 - Historique des modifications pour chaque évènement (changement de statut, validation, etc.).
 - Ajout d'une case à cocher permettant d'afficher ou de masquer les événements de type "astreintes".
 - Possibilité pour un administrateur de bloquer les demandes de congés sur une journée lors d'évènements importants.
 - Le logiciel s'inteface automatiquement avec les serveurs de l'académie pour importer les dates de vacances: un simple clic sur le bouton "importer" tous les 2 ans est nécéssaire.
 - Vous pouvez choisir la zone scolaire dans la page de configuration.
 - Calcul automatique des jours fériés.
 - Selectionnez simplement les couleurs pour chaque utilisateur (zone administration) grâce à une palette de choix intégrée.

V 3.1
 - Linott propose maintenant la gestion des groupes d'utilisateurs.
 - Ajout d'une option de configuration permettant de réactiver l'affichage du total journalier instantanné (attention: Ne prends pas en compte les axes exclus).
 - Les logs de la fenetre "propriété d'événements" donnent maintenant la date de création de l'événement.
 - Il est maintenant possible de définir les utilisateurs ou groupes concernés par un événement.
 - Il est maintenant possible de définir un événement comme rendant "indisponible" les utilisateurs et groupes concernés.
 - Ajout d'une case à cocher "qui est là" permettant d'afficher clairement les personnes absentes ou présentes ainsi qu'un taux de présence par jour.
 - Ajout d'une case à cocher permettant de masquer tous les événements ne concernant pas l'utilisateur en cours.

V 3.1.1
 - Créer un événement, une absence ou une astreinte dans le calendrier ne vous fait plus revenir au mois courant.
 - Correction d'un bug lors du des renseignements des logins/mot de passe si l'utilisateur rentrait login contenant une majuscule.
 - Correction d'un bug pouvant survenir lors de l'utilisation de " et ' dans les noms d'événements.

v 4.0
 - Ajout d'une option d'administration permettant le renseignement automatique des heures d'absences dans la fiche d'heures au moment de la validation d'une absence dans le calendrier.
 - Il est maintenant possible de définir le nombre de jours de congés pour chaque utilisateur.
 - Il est maintenant possible de définir le nombre de jours de RTT pour chaque utilisateur.
 - Calendrier: Les événements concernant l'utilisateur sont maintenant précédés de son propre nom plutôt que du nom de la personne ayant créé l'événement.

V 4.1
 - Correction d'un bug permettant à certains axes 2 ayant un code comptable en 5xxx pouvaient apparaitre dans les menus de renseignement rapide des absences.
 - Les utilisateurs désactivés n'apparaissent plus dans la liste de "choix des utilisateurs concernés" des fenêtres de propriétés d'évènements.
 - Modification des procédures de configuration du logiciel.

V 4.2
 - Option permettant d'afficher le total annuel sur la fiche d'heures.
 - Option permettant aux administrateurs de supprimer des événements ne leur appartenant pas dans le calendrier.
 - Ajout de nouveaux types d'exports.
 - Correction de deux types d'exports ne prenant pas en compte les choix de la page "stats axes".
 - Amélioration visuelle de la page d'export.
 - Amélioration du visuel calendrier.
 - Visuel permettant de faire ressortir la journée en cours sur le calendrier.

V 4.3
 - Option permettant de définir le mois de départ du décompte d'heure annuel présent sur la fiche d'heures.
 - Correction d'un bug qui empechait l'affichage correcte du total annuel si le mois de départ était égal ou supérieur à 10.
 - Ajout d'un nouveau type d'export détaillant chaque période renseignée par chaque utilisateur pour chaque jour (demande ORA)
 - Modification du moteur d'export pour le rendre plus rapide.

V 4.4 (07/01/16)
 - Ajout d'un menu permettant de renseigner des "jours types" pour les personnes remplissant frequemment les mêmes types de journées (option désactivable dans l'administration).
 - Ajout d'une option permettant d'activer un total journalier "pré-validation" afin de vérifier son total journalier avant de valider.

V 4.4.1 (02/02/16)
 - Correction d'un bug empechant les jours types de fonctionner pour les organismes travaillant sur 2 axes seulement.
 - Correction d'un bug empechant l'import de jours types contenant des "/" dans leurs noms.

</pre>
</BODY>

</HTML>
