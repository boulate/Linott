<?php 
session_start();
include("connexion_base.php"); 

#$nomBase 			=  "Linott";
$nomBase			=  $name_BDD;
echo "nom base: $name_BDD";

// Ce script permet de vérifier les tables et colonnes en place pour les inserer ou les modifier en cas de besoin.

$loginSession		=	$_SESSION['login'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];

require("checkAdmin.php");

	///////////////////////////////////////////   ///////////////////////////////////////////
	// Pour la mise à jour 2.0 (passage de "Projet" à "Axe3"), on vérifie la présence de l'ancien "Projet". Si il existe encore, on lance toutes les modifs.
	$existe=0;
	$sql		= "SHOW TABLES FROM `$nomBase` LIKE 'Projet'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql);
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Attention: La table Projet existe!!! La base est donc en ancienne version (<2.0). On doit lancer toutes les modifications pour le passer en mode 'Axe3'.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 1 )
	{
		echo "<br>Nous lancons donc la migration vers Axe3...";
		
		$sql_modification = "	ALTER TABLE `Utilisateurs` CHANGE `preferences_masque_id_projet` `preferences_masque_id_axe3` VARCHAR( 2048 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;

					RENAME TABLE `$nomBase`.`Projet` TO `$nomBase`.`Axe3` ;

					ALTER TABLE `Axe3` CHANGE `nomProjet` `nomAxe3` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

					ALTER TABLE `Periodes` DROP FOREIGN KEY `Periodes_ibfk_6` ;
					ALTER TABLE Periodes DROP INDEX fk_idProjet ;

					ALTER TABLE `Axe3` CHANGE `idProjet` `idAxe3` INT( 11 ) NOT NULL AUTO_INCREMENT ;

					ALTER TABLE `Periodes` CHANGE `Projet_idProjet` `Axe3_idAxe3` INT( 11 ) NOT NULL ;

					ALTER TABLE `$nomBase`.`Periodes` ADD INDEX `fk_idAxe3` ( `Axe3_idAxe3` ) ;
					ALTER TABLE `Periodes` ADD FOREIGN KEY ( `Axe3_idAxe3` ) REFERENCES `$nomBase`.`Axe3` (`idAxe3`) ON DELETE RESTRICT ON UPDATE RESTRICT ;";
		echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	if ( $existe == 0 )
	{
		echo "<br> La table Projet n'existe pas, nous considerons donc que nous sommes bien en AXE3 et nous verifions la presence d'Axe3:";
		
			$sql		= "SHOW TABLES FROM `$nomBase` LIKE 'Axe3'";
			//echo "$sql";
			$reponse 	= $bdd->query($sql);
			while ($donnees = $reponse->fetch())
			{
				echo " OK!, Axe 3 existe.";
				$existe=1;
			}
			$reponse->closeCursor();
			if ( $existe == 0 )
			{
				echo "<br>ATTENTION: Le script ne detecte pas la presence d'une table Axe3";
			}
	}

	
	///////////////////////////////////////////   ///////////////////////////////////////////
	// On vérifie que la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SHOW TABLES FROM `$nomBase` LIKE 'Configuration'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La table 'Configuration' n'existe pas. Nous allons la creer:";
		
		$sql_modification = "	CREATE TABLE IF NOT EXISTS `Configuration` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`nom` varchar(64) NOT NULL,
					`valeur` varchar(255) NOT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1";
		echo "<br>$sql_modification";
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}

	///////////////////////////////////////////   ///////////////////////////////////////////	
	// On verifie la taille du champ "valeur" qui était avant la 2.2 à 16. Elle passe maintenant à 255.
	$existe=0;
	$sql		= "	SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE table_name = 'Configuration' AND COLUMN_NAME = 'valeur'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$taille=$donnees['CHARACTER_MAXIMUM_LENGTH'];
	}
	$reponse->closeCursor();
	if ( $taille != 255 )
	{
		echo "<br> La taille du champ 'valeur' de la table 'Configuration' ne semble pas donne. Elle est de $taille, nous la passons a 255.";
		
		$sql_modification = "ALTER TABLE `Configuration` CHANGE `valeur` `valeur` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
		echo "<br>$sql_modification";
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	else echo "<br> La taille du champ 'valeur' de la table 'Configuration' semble bonne."; 

		

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "affiche_axe3" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'affiche_axe3'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"affiche_axe3\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"affiche_axe3\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'affiche_axe3', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}



	///////////////////////////////////////////   ///////////////////////////////////////////	
	// On vérifie que la valeur "afficher_heures_sup" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_heures_sup'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"afficher_heures_sup\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"afficher_heures_sup\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'afficher_heures_sup', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}



	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "nombre_jours_conges" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'nombre_jours_conges'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"nombre_jours_conges\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"nombre_jours_conges\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'nombre_jours_conges', '30')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}

	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie si la valeur "nombre_RTT_trimestre" de la table "Configuration" existe. Si elle existe on la copie dans "nombre_jours_RTT" et on l'efface.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'nombre_RTT_trimestre'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"nombre_RTT_trimestre\" dans la table Configuration.";

		echo "On modifie sont nom pour la renommer \"nombre_jours_RTT\" suite a la mise a jour de la version 2.3:<br>";
		$sql_modification = "UPDATE Configuration SET nom = \"nombre_jours_RTT\" WHERE nom = \"nombre_RTT_trimestre\"";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();

		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"nombre_RTT_trimestre\" dans la table configuration n'existe pas. C'est normal car depuis la version 2.3 elle se nomme \"nombre_jours_RTT\".";
	}
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "nombre_jours_RTT" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'nombre_jours_RTT'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"nombre_jours_RTT\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"nombre_jours_RTT\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'nombre_jours_RTT', '24')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "majoration_samedi" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'majoration_samedi'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"majoration_samedi\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"majoration_samedi\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'majoration_samedi', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "majoration_dimanche" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'majoration_dimanche'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"majoration_dimanche\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"majoration_dimanche\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'majoration_dimanche', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "majoration_ferie" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'majoration_ferie'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"majoration_ferie\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"majoration_ferie\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'majoration_ferie', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	
	
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "mois_raz_conge" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'mois_raz_conge'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"mois_raz_conge\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"mois_raz_conge\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'mois_raz_conge', '6')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "afficher_codes_comptables_selection_axes" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_codes_comptables_selection_axes'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"afficher_codes_comptables_selection_axes\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"afficher_codes_comptables_selection_axes\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'afficher_codes_comptables_selection_axes', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "afficher_codes_comptables_recapitulatif" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_codes_comptables_recapitulatif'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"afficher_codes_comptables_recapitulatif\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"afficher_codes_comptables_recapitulatif\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'afficher_codes_comptables_recapitulatif', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "activer_axe3" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'activer_axe3'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"activer_axe3\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"activer_axe3\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'activer_axe3', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "mois_depart_annee" de la table "Configuration" existe. Si c'est la cas on modifie le nom en "mois_depart_annee_conge", sinon on passe à la vérification de la valeur "mois_depart_annee_conge";
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'mois_depart_annee'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"mois_depart_annee\" dans la table Configuration.";
		
		echo "On modifie sont nom pour la renommer \"mois_depart_annee_conge\" suite a la mise a jour de la version 2.2:<br>";
			$sql_modification = "UPDATE Configuration SET nom = \"mois_depart_annee_conge\" WHERE nom = \"mois_depart_annee\"";
			//echo $sql_modification;
			$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
			$reponse_modification->closeCursor();
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"mois_depart_annee\" dans la table configuration n'existe pas. C'est normal car depuis la version 2.2 ce doit etre \"mois_depart_annee_conge\"";
	}
	
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "mois_depart_annee_conge" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'mois_depart_annee_conge'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"mois_depart_annee_conge\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"mois_depart_annee_conge\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'mois_depart_annee_conge', '6')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	
	
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "periode_gestion_RTT" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'periode_gestion_RTT'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"periode_gestion_RTT\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"periode_gestion_RTT\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'periode_gestion_RTT', 'Trimestre')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	
	
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "afficher_RTT" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_RTT'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"afficher_RTT\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"afficher_RTT\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'afficher_RTT', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "afficher_conges" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_conges'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"afficher_conges\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"afficher_conges\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'afficher_conges', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "axe2_exclus_totaux" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'axe2_exclus_totaux'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"axe2_exclus_totaux\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"axe2_exclus_totaux\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'axe2_exclus_totaux', '')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "afficher_raccourcis_absences" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_raccourcis_absences'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"afficher_raccourcis_absences\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"afficher_raccourcis_absences\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'afficher_raccourcis_absences', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}

	
	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "afficher_calcul_rapide_journee" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_calcul_rapide_journee'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"afficher_calcul_rapide_journee\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"afficher_calcul_rapide_journee\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'afficher_calcul_rapide_journee', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "afficher_total_annuel" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_total_annuel'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"afficher_total_annuel\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"afficher_total_annuel\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'afficher_total_annuel', '0')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "renseigner_automatiquement_conge_valide" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'renseigner_automatiquement_conge_valide'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"renseigner_automatiquement_conge_valide\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"renseigner_automatiquement_conge_valide\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'renseigner_automatiquement_conge_valide', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "autoriser_admin_suppr_event" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'autoriser_admin_suppr_event'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"autoriser_admin_suppr_event\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"autoriser_admin_suppr_event\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'autoriser_admin_suppr_event', '0')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}




	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la colonne "codeAxe3" de la table "Axe3" existe. Si ce n'est pas le cas, on la crée.
	$existe=0;
	$sql		= "EXPLAIN Axe3";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$nomColonne = $donnees['Field'];
		
		if ( $nomColonne == "codeAxe3" )
		{
			echo "<br> Il y a bien la colonne \"codeAxe3\" dans la table Axe3.";
			$existe=1;
		}
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La colonne \"codeAxe3\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "ALTER TABLE `Axe3` ADD `codeAxe3` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `idAxe3` ";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "mois_depart_annee_RTT" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'mois_depart_annee_RTT'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"mois_depart_annee_RTT\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"mois_depart_annee_RTT\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'mois_depart_annee_RTT', '6')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}



	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "zone_vacances" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'zone_vacances'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"zone_vacances\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"zone_vacances\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO `$nomBase`.`Configuration` (`id` ,`nom` ,`valeur`) VALUES (NULL , 'zone_vacances', 'A')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "description" de la table "CalendrierConges" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "EXPLAIN CalendrierConges";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$nomColonne = $donnees['Field'];
		
		if ( $nomColonne == "description" )
		{
			echo "<br> Il y a bien la colonne \"description\" dans la table CalendrierConges.";
			$existe=1;
		}
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"description\" dans la table CalendrierConges n'existe pas. Nous allons la creer.";
		
		$sql_modification = "ALTER TABLE `CalendrierConges` ADD `description` TEXT NULL";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "bloquant" de la table "CalendrierConges" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "EXPLAIN CalendrierConges";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$nomColonne = $donnees['Field'];
		
		if ( $nomColonne == "bloquant" )
		{
			echo "<br> Il y a bien la colonne \"bloquant\" dans la table CalendrierConges.";
			$existe=1;
		}
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"bloquant\" dans la table CalendrierConges n'existe pas. Nous allons la creer.";
		
		$sql_modification = "ALTER TABLE `CalendrierConges` ADD `bloquant` TINYINT( 1 ) NOT NULL DEFAULT '0'";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la colonne "periode" de la table "CalendrierConges" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "EXPLAIN CalendrierConges";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$nomColonne = $donnees['Field'];
		
		if ( $nomColonne == "periode" )
		{
			echo "<br> Il y a bien la colonne \"periode\" dans la table CalendrierConges.";
			$existe=1;
		}
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"periode\" dans la table CalendrierConges n'existe pas. Nous allons la creer.";
		
		$sql_modification = "ALTER TABLE `CalendrierConges` ADD `periode` CHAR( 2 ) NOT NULL AFTER `date`";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la colonne "commentaire" de la table "CalendrierConges" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "EXPLAIN CalendrierConges";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$nomColonne = $donnees['Field'];
		
		if ( $nomColonne == "commentaire" )
		{
			echo "<br> Il y a bien la colonne \"commentaire\" dans la table CalendrierConges.";
			$existe=1;
		}
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"commentaire\" dans la table CalendrierConges n'existe pas. Nous allons la creer.";
		
		$sql_modification = "ALTER TABLE `CalendrierConges` ADD `commentaire` TEXT NULL ";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la colonne "id_utilisateurs_concernes" de la table "CalendrierConges" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "EXPLAIN CalendrierConges";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$nomColonne = $donnees['Field'];
		
		if ( $nomColonne == "id_utilisateurs_concernes" )
		{
			echo "<br> Il y a bien la colonne \"id_utilisateurs_concernes\" dans la table CalendrierConges.";
			$existe=1;
		}
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"id_utilisateurs_concernes\" dans la table CalendrierConges n'existe pas. Nous allons la creer.";
		
		$sql_modification = "ALTER TABLE `CalendrierConges` ADD `id_utilisateurs_concernes` TEXT NULL , ADD `id_groupes_concernes` TEXT NULL";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la colonne "" de la table "CalendrierConges" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "EXPLAIN CalendrierConges";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$nomColonne = $donnees['Field'];
		
		if ( $nomColonne == "date_creation" )
		{
			echo "<br> Il y a bien la colonne \"date_creation\" dans la table CalendrierConges.";
			$existe=1;
		}
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"date_creation\" dans la table CalendrierConges n'existe pas. Nous allons la creer.";
		
		$sql_modification = "ALTER TABLE `CalendrierConges` ADD `date_creation` DATETIME NOT NULL";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}




	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la colonne "afficher_astreintes" de la table "utilisateurs" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "EXPLAIN Utilisateurs";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$nomColonne = $donnees['Field'];
		
		if ( $nomColonne == "afficher_astreintes" )
		{
			echo "<br> Il y a bien la colonne \"afficher_astreintes\" dans la table Utilisateurs.";
			$existe=1;
		}
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"afficher_astreintes\" dans la table Utilisateurs n'existe pas. Nous allons la creer.";
		
		$sql_modification = "ALTER TABLE `Utilisateurs` ADD `afficher_astreintes` TINYINT( 1 ) NOT NULL DEFAULT '1'";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la colonne "nbrConges" de la table "utilisateurs" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "EXPLAIN Utilisateurs";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$nomColonne = $donnees['Field'];
		
		if ( $nomColonne == "nbrConges" )
		{
			echo "<br> Il y a bien la colonne \"nbrConges\" dans la table Utilisateurs.";
			$existe=1;
		}
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"nbrConges\" dans la table Utilisateurs n'existe pas. Nous allons la creer.";
		
		$sql_modification = "ALTER TABLE `Utilisateurs` ADD `nbrConges` DECIMAL( 4, 2 ) NULL AFTER `nbrHeuresSemaine`";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la colonne "nbrRTT" de la table "utilisateurs" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "EXPLAIN Utilisateurs";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$nomColonne = $donnees['Field'];
		
		if ( $nomColonne == "nbrRTT" )
		{
			echo "<br> Il y a bien la colonne \"nbrRTT\" dans la table Utilisateurs.";
			$existe=1;
		}
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"nbrRTT\" dans la table Utilisateurs n'existe pas. Nous allons la creer.";
		
		$sql_modification = "ALTER TABLE `Utilisateurs` ADD `nbrRTT` DECIMAL( 4, 2 ) NULL AFTER `nbrConges`";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}

	// On met à jour les champs nbrConges vides dans la table Utilisateurs pour qu'ils correspondent à celui défini par défaut dans l'administration
	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'nombre_jours_conges'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$nbrJoursConges		= $donnees['valeur'];
	}
	$reponse->closeCursor();

	
	$sql_modification = "	UPDATE `Utilisateurs` SET `nbrConges` = '$nbrJoursConges' Where `nbrConges` IS NULL ";
	//echo $sql_modification;
	$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
	$reponse_modification->closeCursor();



	// On met à jour les champs nbrRTT vides dans la table Utilisateurs pour qu'ils correspondent à celui défini par défaut dans l'administration
	///////////////////////////////////////////   ///////////////////////////////////////////
	$sql		= "SELECT valeur FROM Configuration where nom like 'nombre_jours_RTT'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$nbrJoursRTT		= $donnees['valeur'];
	}
	$reponse->closeCursor();

	$sql_modification = "	UPDATE `Utilisateurs` SET `nbrRTT` = '$nbrJoursRTT' Where `nbrRTT` IS NULL ";
	//echo $sql_modification;
	$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
	$reponse_modification->closeCursor();


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "mois_depart_decompte_heures" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'mois_depart_decompte_heures'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"mois_depart_decompte_heures\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"mois_depart_decompte_heures\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'mois_depart_decompte_heures', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la table "JoursTypes" existe. Si ce n'est pas le cas on la crée.
        $existe=0;
        $sql            = "SHOW TABLES FROM `$nomBase` LIKE 'JoursTypes'";
        //echo "$sql";
        $reponse        = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
        while ($donnees = $reponse->fetch())
        {
                echo "<br> Il y a bien la table JoursTypes.";
                $existe=1;
        }
        $reponse->closeCursor();
        if ( $existe == 0 )
        {
                echo "<br> La table 'JoursTypes' n'existe pas. Nous allons la creer:";
     
                $sql_modification = "
					CREATE TABLE IF NOT EXISTS `JoursTypes` (
					`id` int(11) NOT NULL,
					  `idUtilisateur` int(11) NOT NULL,
					  `nom` varchar(64) NOT NULL,
					  `periode1` varchar(64) DEFAULT NULL,
					  `periode2` varchar(64) DEFAULT NULL,
					  `periode3` varchar(64) DEFAULT NULL,
					  `periode4` varchar(64) DEFAULT NULL,
					  `periode5` varchar(64) DEFAULT NULL,
					  `periode6` varchar(64) DEFAULT NULL,
					  `periode7` varchar(64) DEFAULT NULL,
					  `periode8` varchar(64) DEFAULT NULL,
					  `periode9` varchar(64) DEFAULT NULL,
					  `periode10` varchar(64) DEFAULT NULL,
					  `periode11` varchar(64) DEFAULT NULL,
					  `periode12` varchar(64) DEFAULT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;

					ALTER TABLE `JoursTypes`
					 ADD PRIMARY KEY (`id`);

					ALTER TABLE `JoursTypes`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
				";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();
        }


    ///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "afficher_jours_types" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'afficher_jours_types'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"afficher_jours_types\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"afficher_jours_types\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'afficher_jours_types', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}

	echo "<br><br>Fin de la mise a jour. Envoyez la copie de ce rapport a guillaume.boulaton@atmosfair-bourgogne.fr si vous avez le moindre probleme.
<br>
<br>Vous pouvez lancer la mise a jour du logiciel en vous connectant sur votre serveur ou votre machine virtuelle en tant que 'root' et en lancant la commande ci dessous:<br>
cd /var/www/Linott && chmod +x mise_a_jour_Linott.sh && bash mise_a_jour_Linott.sh";


// ALTER TABLE `Log` CHANGE `Date` `Date` DATETIME NOT NULL ;

// ALTER TABLE `CalendrierConges` CHANGE `valide` `valide` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;


// CREATE TABLE IF NOT EXISTS `zonesVacances` (
// 					`zone` varchar(24) NOT NULL,
// 					`dateDebut` date NOT NULL,
// 					`dateFin` date NOT NULL
// 					) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

// ALTER TABLE `zonesVacances` ADD PRIMARY KEY ( `zone` , `dateDebut` , `dateFin` ) ;

// CREATE TABLE IF NOT EXISTS `Groupes` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `nom` char(45) NOT NULL,
//   `idUtilisateurs` text,
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

// UPDATE CalendrierConges SET id_utilisateurs_concernes = "ALL" Where `id_utilisateurs_concernes` IS NULL AND `type` = "event" ;
// UPDATE CalendrierConges SET id_groupes_concernes = "ALL" Where `id_groupes_concernes` IS NULL AND `type` = "event" ;
// UPDATE CalendrierConges SET id_utilisateurs_concernes = `description` Where `id_utilisateurs_concernes` IS NULL AND `type` = "astreinte"  ;

// ALTER TABLE `CalendrierConges` ADD `indisponible` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `description` ;
