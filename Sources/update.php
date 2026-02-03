<?php 
session_start();
include("connexion_base.php"); 

#$nomBase 			=  "Linott";
$nomBase			=  $name_BDD;
echo "nom base: $name_BDD";

// Ce script permet de vérifier les tables et colonnes en place pour les inserer ou les modifier en cas de besoin.

$loginSession		=	$_SESSION['login'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];

$version_Patch = "3.0";

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
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'activer_axe3', '0')";
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////
	// Si nous avons toujours les "vieilles" section, c'est que nous sommes dans une version pré 2017.
	// Nous allons à partir de cette date introduire une notion de version dans Linott.
	// Elle se fera à partir du constat suivant : Si pas de "version_Linott" renseignée, c'est que nous sommes inférieurs à la 3.0 (v01/2017)
	// ==> Donc nous devons faire les choses suivantes pour nous adapter.

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "version" de la table "Configuration" existe. Si ce n'est pas le cas on la crée. Cela a été introduit à partir de Linott V3.0 (01/01/2017)
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'version_Linott'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"version_Linott\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> Le champ \"version_Linott\" dans la table configuration n'existe pas. Nous allons la creer à 0.";
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'version_Linott', '0')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// Verification et renseignement de la version actuellement en base
	$version_Linott=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'version_Linott'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$version_Linott=$donnees['valeur'];
		echo "<br> Linott était en version $version_Linott.";
	}


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie la valeur de "version_Linott" pour désactiver l'axe 3 par défaut si c'est une migration depuis une version < 3.0 (01/01/2017) pour lesquelles l'axe3 était activé par défaut.
	if ( $version_Linott < 3 )
	{
		// Si cette option n'existe pas, ca veut dire que Linott est configuré pour utiliser l'ANCIENNE compta analytique des aasqa pré 2017.
		// On la reconfigure donc pour que par défaut l'axe 3 ne soit pas affiché :
		echo "<br> Nous passons donc la valeur \"activer_axe3\" à zero car elle est désactivée par défaut sur la compta post 2017 (à partir de Linott V3.0).";
		$sql_modification = "UPDATE Configuration SET valeur = 0 WHERE nom = 'activer_axe3'";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}



	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie que la valeur "activerSections" de la table "Configuration" existe. Si ce n'est pas le cas on la crée.
	$existe=0;
	$sql		= "SELECT valeur FROM Configuration where nom like 'activerSections'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		echo "<br> Il y a bien l'option \"activerSections\" dans la table Configuration.";
		$existe=1;
	}
	$reponse->closeCursor();
	if ( $existe == 0 )
	{
		echo "<br> La valeur \"activerSections\" dans la table configuration n'existe pas. Nous allons la creer.";
		
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'activerSections', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();

	}




	///////////////////////////////////////////   ///////////////////////////////////////////		
	// Si nous sommes en version <3 (pré version 2017), on copie donc la table Section vers Section_pre_2017 et on créé la table section version 2017
	if ( $version_Linott < 3 )
        {
                echo "<br> Nous sommes en version < 3. Nous copions donc la vielle table 'Section' vers 'Section_pre_2017'...";
     
                $sql_modification = "ALTER TABLE Section RENAME TO Section_pre_2017";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();

		echo "<br> La table Section doit être recréée vide pour y insérer les nouveaux codes V3 (compta v 2017).";
                $sql_modification = "
			CREATE TABLE IF NOT EXISTS `Section` (
			`idSection` int(11) NOT NULL,
			  `codeSection` varchar(8) NOT NULL,
			  `nomSection` varchar(255) NOT NULL
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			ALTER TABLE `Section` ADD PRIMARY KEY (`idSection`);
			ALTER TABLE `Section` MODIFY `idSection` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
		";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();

		echo "<br> On renseigne dans la nouvelle table Section les sections compta 2017";
                $sql_modification = "
			INSERT INTO `Section` (`idSection`, `codeSection`, `nomSection`) VALUES
			('', 'A1', 'Observatoires (polluants classiques)'),
			('', 'A2', 'Observatoires (orientations nationales)'),
			('', 'A3', 'Observatoires (autres ayant un caractère récurrent)'),
			('', 'B', 'Accompagnement technique des acteurs'),
			('', 'C', 'Communication'),
			('', 'D', 'Amélioration des connaissances'),
			('', 'E', 'Gouvernance et administration'),
			('', 'F', 'Prestations soumises a concurrence'),
			('', 'G', 'Dispositif national'),
			('', 'H', 'Structure'),
			(11, 'I', 'Financements non lignes'),
			(12, 'Z', 'Absences');
		";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();
        }




	///////////////////////////////////////////   ///////////////////////////////////////////		
	// Si la table "Axe1_pre_2017" n'existe pas, cela signifie que nous sommes encore sur un système de compta pré version 2017
	// On copie donc la table Axe1 vers Axe1_pre_2017
	if ( $version_Linott < 3 )
        {
                echo "<br> Nous sommes en version < 3. Nous copions donc la vielle table 'Axe1' vers 'Axe1_pre_2017'...";
     
                $sql_modification = "ALTER TABLE Axe1 RENAME TO Axe1_pre_2017";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();

		echo "<br> La table Axe1 doit être recréée vide pour y insérer les nouveaux codes V3 (compta v 2017).";
                $sql_modification = "
			CREATE TABLE IF NOT EXISTS `Axe1` (
			`idAxe1` int(11) NOT NULL,
			  `codeAxe1` varchar(45) NOT NULL,
			  `nomAxe1` varchar(255) NOT NULL,
			  `Section_idSection` int(11) NOT NULL
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			ALTER TABLE `Axe1` ADD PRIMARY KEY (`idAxe1`), ADD KEY `fk_idSection` (`Section_idSection`);
			ALTER TABLE `Axe1` MODIFY `idAxe1` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
			ALTER TABLE `Axe1` ADD CONSTRAINT `Axe1_ibfk_1` FOREIGN KEY (`Section_idSection`) REFERENCES `Section` (`idSection`);
		";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();

		echo "<br> On renseigne dans la nouvelle table Axe1 les Axe1 compta 2017";
                $sql_modification = "
			INSERT INTO `Axe1` (`idAxe1`, `codeAxe1`, `nomAxe1`, `Section_idSection`) VALUES
			(2, '11', 'Mesures', 1),
			(3, '12', 'Cadastre spatialisé', 1),
			(4, '13', 'Mise à disposition données ', 1),
			(5, '14', 'Pesticides', 2),
			(6, '15', 'Pollens', 2),
			(7, '16', 'Odeurs', 2),
			(8, '17', 'Observatoires (hors nationaux) ayant un caractère récurrent', 3),
			(9, '18', 'PPA et PLQA (avec déclinaison par territoire)', 4),
			(10, '19', 'Villes respirables (avec déclinaison par territoire)', 4),
			(11, '20', 'Amenagement territoire et urbanisme', 4),
			(12, '21', 'Transversalite air-climat-energie', 4),
			(13, '22', 'Prévision quotidienne et cartes risques', 4),
			(14, '23', 'Dispositif préfectoral (déclenchements suivi et bilans)', 4),
			(15, '24', 'Programme CARA', 4),
			(16, '25', 'Situation incidentelles et accidentelles', 4),
			(17, '26', 'Air - sante', 4),
			(18, '30', 'Actions récurrentes', 5),
			(19, '31', 'Projets rendre plus accessibles l''information (prestation gratuite de formation des acteurs)', 5),
			(20, '32', 'Projets donner aux citoyens les clés de l''action', 5),
			(21, '33', 'Projets s''inscrire dans une démarche évolutive', 5),
			(22, '34', 'Journées nationales (dont journées nationales de l''air)', 5),
			(23, '40', 'Santé-environnement', 6),
			(24, '41', 'Zoom territoires (points noirs, études particulières)', 6),
			(25, '42', 'Observatoires émergents (non récurrents - déclinaison par observatoires)', 6),
			(26, '43', 'Innovation', 6),
			(27, '51', 'Vie associative régionale', 7),
			(28, '52', 'Administration', 7),
			(29, '53', 'QSE/RSE', 7),
			(30, '54', 'Prestations de services soumises à concurrence', 8),
			(31, '60', 'ATMO France ', 9),
			(32, '61', 'Mutualisations inter-AASQA', 9),
			(33, '62', 'Ministère délivrant l''agrément', 9),
			(34, '63', 'LCSQA', 9),
			(35, '70', 'Structure (locaux, logistique)', 10),
			(36, '80', 'Financements non lignés (projets associatif…)', 11),
			(37, '99', 'Congés et autres absences', 12);
		";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();        


	}



	///////////////////////////////////////////   ///////////////////////////////////////////		
	// Si la table "Axe2_pre_2017" n'existe pas, cela signifie que nous sommes encore sur un système de compta pré version 2017
	// On copie donc la table Axe2 vers Axe2_pre_2017
	if ( $version_Linott < 3 )
        {
                echo "<br> Nous sommes en version < 3. Nous copions donc la vielle table 'Axe2' vers 'Axe2_pre_2017'...";
     
                $sql_modification = "ALTER TABLE Axe2 RENAME TO Axe2_pre_2017";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();

		echo "<br> La table Axe2 doit être recréée vide pour y insérer les nouveaux codes V3 (compta v 2017).";
                $sql_modification = "
			CREATE TABLE IF NOT EXISTS `Axe2` (
			`idAxe2` int(11) NOT NULL,
			  `codeAxe2` varchar(45) NOT NULL,
			  `nomAxe2` varchar(255) NOT NULL,
			  `Section_idSection` int(11) NOT NULL
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			ALTER TABLE `Axe2` ADD PRIMARY KEY (`idAxe2`), ADD KEY `fk_idSection` (`Section_idSection`);
			ALTER TABLE `Axe2` MODIFY `idAxe2` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
			ALTER TABLE `Axe2` ADD CONSTRAINT `Axe2_ibfk_1` FOREIGN KEY (`Section_idSection`) REFERENCES `Section` (`idSection`);
		";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();

		echo "<br> On renseigne dans la nouvelle table Axe2 les Axe2 compta 2017";
                $sql_modification = "
			INSERT INTO `Axe2` (`idAxe2`, `codeAxe2`, `nomAxe2`, `Section_idSection`) VALUES
			(1, '1100', 'Mesures - Compte d''attente à réaffecter', 1),
			(2, '1110', 'Mesures fixes Europe hors MERA', 1),
			(3, '1120', 'Mesures obligations nationales (dispositif préfectoral, indices, modélisation)', 1),
			(4, '1130', 'Autres mesures fixes', 1),
			(5, '1140', 'Campagnes d''évaluation moyens mobiles', 1),
			(6, '1150', 'Mesures MERA', 1),
			(7, '1160', 'Campagnes d''évaluation par prélèvement', 1),
			(8, '1170', 'Qualité, Sécurité,audits, Inter-calibrations', 1),
			(9, '1210', 'Mise à jour et bancarisation', 1),
			(10, '1220', 'Développement - Qualité-Audits', 1),
			(11, '1310', 'Production cartographies réglementaires (dont QA/QC)', 1),
			(12, '1320', 'Statistiques et rapportages', 1),
			(13, '1330', 'SI - Bancarisation-Mise à disposition géostandards', 1),
			(14, '1340', 'Production - Bilans et rapports annuels', 1),
			(15, '1410', 'Mesure/Communication', 2),
			(16, '1420', 'Modélisation', 2),
			(17, '1510', 'Mesure/Communication', 2),
			(18, '1520', 'Modélisation', 2),
			(19, '1600', 'Odeurs', 2),
			(20, '1710', 'Radioactivité', 3),
			(21, '1720', 'Air intérieur', 3),
			(22, '1730', 'H2S', 3),
			(23, '1740', 'Formaldéhyde', 3),
			(24, '1750', 'NH3', 3),
			(25, '1760', 'Bio-Indication', 3),
			(26, '1770', 'Dioxine Furane', 3),
			(27, '1780x', 'Autres (déclinaisons possibles)', 3),
			(28, '1800', 'PPA et PLQA (avec déclinaison par territoire)', 4),
			(29, '1900', 'Villes respirables (avec déclinaison par territoire)', 4),
			(30, '2010x', 'SCOT (avec déclinaison par territoire)', 4),
			(31, '2020x', 'PLU/PLUI (avec déclinaison par territoire)', 4),
			(32, '2030x', 'PDU (avec déclinaison par territoire)', 4),
			(33, '2040x', 'Porter à connaissance - fiches territoires', 4),
			(34, '2050x', 'Modélisation fine échelle hors plan (avec déclinaison par territoire)', 4),
			(35, '2060x', 'Cartes stratégiques', 4),
			(36, '2110', 'SRCAE/SRADETT', 4),
			(37, '2120x', 'PCEAT (déclinaison possible)', 4),
			(38, '2130x', 'Autres plans (déclinaison possible)', 4),
			(39, '2200', 'Prévision quotidienne et cartes risques', 4),
			(40, '2300', 'Dispositif préfectoral (déclenchements suivi et bilans)', 4),
			(41, '2400', 'Programme CARA', 4),
			(42, '2510', 'Veille', 4),
			(43, '2520x', 'Incidents- accident (par dossier)', 4),
			(44, '2610', 'PRSE', 4),
			(45, '2620', 'EPLS/CLS', 4),
			(46, '3010', 'WEB', 5),
			(47, '3020', 'bilan QA et rapport annuel', 5),
			(48, '3030', 'newsletters et bulletins information', 5),
			(49, '3100', 'Déclinaisons par projet', 5),
			(50, '3200', 'Déclinaisons par projet', 5),
			(51, '3300', 'Déclinaisons par projet', 5),
			(52, '3400', 'Déclinaisons par projet', 5),
			(53, '4000', 'Déclinaison par projet', 6),
			(54, '4100', 'Déclinaison par projet', 6),
			(55, '4210', 'PUF', 6),
			(56, '4220', 'Ammoniaque', 6),
			(57, '4300', 'Déclinaison par projet', 6),
			(58, '5110', 'Statutaire', 7),
			(59, '5120', 'Autres commissions et animation territoriale', 7),
			(60, '5210', 'RH et IRP', 7),
			(61, '5220', 'Gestion administrative et financière', 7),
			(62, '5230', 'Management et communication interne', 7),
			(63, '5240', 'Informatique interne - Support aux utilisateurs', 7),
			(64, '5250', 'Formations générales (Management, Développement Personnel…)', 7),
			(65, '5300', 'Déclinaison par thématique (RSE, QSE, Écoconduite…)', 7),
			(66, '5400', 'Déclinaison par projet', 8),
			(67, '6010', 'Commissions Statutaires', 9),
			(68, '6020', 'par projet', 9),
			(69, '6110', 'Labo chimie', 9),
			(70, '6120', 'Labo niv2 et tests analyseurs', 9),
			(71, '6130', 'ICARE / Modélisation', 9),
			(72, '6140x', 'PASS (SPOT, DIDON…)', 9),
			(73, '6150', 'JTA', 9),
			(74, '6160x', 'par projet (achats groupés…)', 9),
			(75, '6210x', 'CPS', 9),
			(76, '6220', 'PNSQA', 9),
			(77, '6230x', 'Commissions', 9),
			(78, '6310', 'par programme', 9),
			(79, '6320', 'séminaires', 9),
			(80, '7000', 'Structure (Locaux, logistique)', 10),
			(81, '8000', 'Financements non lignés (Projets associatif…)', 11),
			(82, '9901', 'Congé', 12),
			(83, '9902', 'RTT', 12),
			(84, '9903', 'Récupération', 12),
			(85, '9904', 'Maladie', 12),
			(86, '9905', 'Congé convention collective', 12),
			(87, '9906', 'Congé parental', 12),
			(88, '9907', 'Jour férié', 12),
			(89, '9908', 'Congé sans solde', 12),
			(90, '9909', 'Congé maternité et paternité', 12),
			(91, '9910', 'Repos compensatoire', 12),
			(92, '9900', 'Absence', 12);
		";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();
        }


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// Si la table "Periodes_pre_2017" n'existe pas, cela signifie que nous sommes encore sur un système de compta pré version 2017
	// On copie donc la table Periodes vers Periodes_pre_2017
	if ( $version_Linott < 3 )
        {
                echo "<br> Nous sommes en version < 3. Nous copions donc la vielle table 'Axe2' vers 'Axe2_pre_2017'...";
     
                $sql_modification = "ALTER TABLE Periodes RENAME TO Periodes_pre_2017";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();

		echo "<br> La table Periodes doit être recréée vide pour y insérer les nouveaux codes correspondant aux axes V3 = année 2017.";
                $sql_modification = "
			CREATE TABLE IF NOT EXISTS `Periodes` (
			`idHoraires` int(11) NOT NULL,
			  `date` date NOT NULL,
			  `horaireDebut` decimal(4,2) NOT NULL,
			  `horaireFin` decimal(4,2) NOT NULL,
			  `totalHoraire` decimal(4,2) DEFAULT NULL,
			  `Utilisateurs_idUtilisateurs` int(11) NOT NULL,
			  `Utilisateurs_login` varchar(45) NOT NULL,
			  `Section_idSection` int(11) NOT NULL,
			  `Axe1_idAxe1` int(11) NOT NULL,
			  `Axe2_idAxe2` int(11) NOT NULL,
			  `Axe3_idAxe3` int(11) NOT NULL,
			  `numeroLigne` int(11) NOT NULL
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			ALTER TABLE `Periodes` ADD PRIMARY KEY (`idHoraires`), ADD KEY `fk_idUtilisateurs` (`Utilisateurs_idUtilisateurs`), ADD KEY `fk_loginUtilisateurs` (`Utilisateurs_login`), ADD KEY `fk_idSection` (`Section_idSection`), ADD KEY `fk_idAxe1` (`Axe1_idAxe1`), ADD KEY `fk_idAxe2` (`Axe2_idAxe2`), ADD KEY `fk_idAxe3` (`Axe3_idAxe3`);
			ALTER TABLE `Periodes` MODIFY `idHoraires` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
			ALTER TABLE `Periodes` ADD CONSTRAINT `Periodes_ibfk_1` FOREIGN KEY (`Utilisateurs_idUtilisateurs`) REFERENCES `Utilisateurs` (`idUtilisateurs`),
			ADD CONSTRAINT `Periodes_ibfk_2` FOREIGN KEY (`Utilisateurs_login`) REFERENCES `Utilisateurs` (`login`) ON UPDATE CASCADE,
			ADD CONSTRAINT `Periodes_ibfk_3` FOREIGN KEY (`Section_idSection`) REFERENCES `Section` (`idSection`),
			ADD CONSTRAINT `Periodes_ibfk_4` FOREIGN KEY (`Axe1_idAxe1`) REFERENCES `Axe1` (`idAxe1`),
			ADD CONSTRAINT `Periodes_ibfk_5` FOREIGN KEY (`Axe2_idAxe2`) REFERENCES `Axe2` (`idAxe2`),
			ADD CONSTRAINT `Periodes_ibfk_6` FOREIGN KEY (`Axe3_idAxe3`) REFERENCES `Axe3` (`idAxe3`);
		";
                echo "<br>$sql_modification";
                $reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
                $reponse_modification->closeCursor();
        }

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On verifie la version de Linott pour appliquer une "migration" des codes comptables congés de la version pre 2017 à la version post 2017.
	if ( $version_Linott < "3.0" )
	{
		// Si cette option n'existe pas, ca veut dire que Linott est configuré pour utiliser l'ANCIENNE compta analytique des aasqa pré 2017.
		// On la reconfigure donc pour que par défaut l'axe 3 ne soit pas affiché :
		echo "Nous faisons la migration des idAxes relatifs aux congés dans la table \"Periodes\" de la version pré 2017 à la version post 2017";
		$sql_modification = "
			CREATE TABLE Periodes_table_transition AS
			(
				SELECT `idHoraires`, `date`, `horaireDebut`, `horaireFin`, `totalHoraire`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, Periodes_pre_2017.`Section_idSection`, `Axe1_idAxe1`, `Axe2_idAxe2`, `Axe3_idAxe3`, `numeroLigne`, codeAxe2 as codeAxe2_pre_2017
				FROM Periodes_pre_2017
				JOIN Axe2_pre_2017
				ON Axe2_idAxe2 = idAxe2
				WHERE Axe1_idAxe1 IN (SELECT idAxe1 FROM Axe1_pre_2017 WHERE codeAxe1 = 50)
				AND Axe2_idAxe2 IN (SELECT idAxe2 FROM Axe2_pre_2017 WHERE codeAxe2 like '50%')
			);
			ALTER TABLE Periodes_table_transition ADD COLUMN idAxe1_post_2017 int(11);
			UPDATE Periodes_table_transition SET idAxe1_post_2017 = (SELECT idAxe1 FROM Axe1 WHERE codeAxe1 = 99);
			ALTER TABLE Periodes_table_transition ADD COLUMN codeAxe2_post_2017 varchar(45);
			UPDATE Periodes_table_transition SET CodeAxe2_post_2017 = CONCAT(99, RIGHT(codeAxe2_pre_2017, 2));
			ALTER TABLE Periodes_table_transition ADD COLUMN idAxe2_post_2017 int(11);
			UPDATE Periodes_table_transition SET idAxe2_post_2017 = (SELECT idAxe2 FROM Axe2 WHERE codeAxe2 = codeAxe2_post_2017);

			INSERT INTO Periodes ( SELECT `idHoraires`, `date`, `horaireDebut`, `horaireFin`, `totalHoraire`, `Utilisateurs_idUtilisateurs`, `Utilisateurs_login`, `Section_idSection`, `idAxe1_post_2017`, `idAxe2_post_2017`, `Axe3_idAxe3`, `numeroLigne` FROM Periodes_table_transition ) ;
		";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}

	

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On vérifie la valeur de "version_Linott" pour désactiver les préférences d'affichages utilisateurs des axes1 si nous passons d'une version < 3.0 (01/01/2017) car tous les axes changent.
	if ( $version_Linott < "3.0" )
	{
		// Si cette option n'existe pas, ca veut dire que Linott est configuré pour utiliser l'ANCIENNE compta analytique des aasqa pré 2017.
		// On la reconfigure donc pour que par défaut l'axe 3 ne soit pas affiché :
		echo "<br> Nous passons donc la valeur \"preferences_masque_id_axe1\" du la table \"Utilisateurs\" à NULL pour tous les utilisateurs car les axes ne sont pas du tout les mêmes compta post 2017 (à partir de Linott V3.0).";
		$sql_modification = "UPDATE Utilisateurs SET preferences_masque_id_axe1 = NULL";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();

		echo "<br> Nous passons donc la valeur \"preferences_masque_id_axe2\" du la table \"Utilisateurs\" à NULL pour tous les utilisateurs car les axes ne sont pas du tout les mêmes compta post 2017 (à partir de Linott V3.0).";
		$sql_modification = "UPDATE Utilisateurs SET preferences_masque_id_axe2 = NULL";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();

		echo "<br> Nous supprimons les jours types de tous les utilisateurs car les axes ne sont pas du tout les mêmes compta post 2017 (à partir de Linott V3.0).";
		$sql_modification = "TRUNCATE JoursTypes";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// mettre en place l'option permettant de filtrer les axe1 ou axe2 selon les choix faits pendant la selection afin de réduire les possibilité et ainsi limiter le choix à quelquechose de lisible.
	if ( $version_Linott < "3.0" )
	{
		// Si cette option n'existe pas, ca veut dire que Linott est configuré pour utiliser l'ANCIENNE compta analytique des aasqa pré 2017.
		// On la reconfigure donc pour que par défaut l'axe 3 ne soit pas affiché :
		echo "Nous ajoutons le champ \"filtrer_choix_axes\" à \"1\" dans la table Configuration car cette option apparait à partir de la version 3.0";
		$sql_modification = "INSERT INTO Configuration (id, nom, valeur) VALUES ('', 'filtrer_choix_axes', '1')";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}


	///////////////////////////////////////////   ///////////////////////////////////////////		
	// remise à zero des axes exclus si passage à linott 3.0 (car axes complement différents.
	if ( $version_Linott < "3.0" )
	{
		echo "<br>Nous remettons à zero les axes exclus car nous migrons vers linott 3.0";
		$sql_modification = "UPDATE Configuration SET valeur = '' WHERE nom = 'axe2_exclus_totaux'";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	///////////////////////////////////////////   ///////////////////////////////////////////		
	// On change la valeur "version" de la table "Configuration" vers la valeur "version" renseignée en debut de fichier update.
	$sql		= "SELECT valeur FROM Configuration where nom like 'version_Linott'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$version_Linott=$donnees['valeur'];
		echo "<br> Linott était en version $version_Linott.";
	}
	$reponse->closeCursor();
	if ( $version_Linott < $version_Patch )
	{
		// Si cette option n'existe pas, ca veut dire que Linott est configuré pour utiliser l'ANCIENNE compta analytique des aasqa pré 2017.
		// On la reconfigure donc pour que par défaut l'axe 3 ne soit pas affiché :
		echo "<br> Nous passons donc la valeur \"version_Linott\" à $version_Patch .";
		$sql_modification = "UPDATE Configuration SET valeur = $version_Patch WHERE nom = 'version_Linott'";
		//echo $sql_modification;
		$reponse_modification = $bdd->query($sql_modification) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql_modification. '<br>'. mysql_error());
		$reponse_modification->closeCursor();
	}
	// On verifie que le changement de version en base est bien effectué, sinon on retourner un message d'erreur.
	$sql		= "SELECT valeur FROM Configuration where nom like 'version_Linott'";
	//echo "$sql";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		$version_Linott=$donnees['valeur'];
		echo "<br> Linott semble être maintenant en version $version_Linott. Ce patch était bien sensé passer le logiciel en version $version_Patch .";
	}
	$reponse->closeCursor();
	if ( $version_Linott =! $version_Patch )
	{
		// Si cette option n'existe pas, ca veut dire que Linott est configuré pour utiliser l'ANCIENNE compta analytique des aasqa pré 2017.
		// On la reconfigure donc pour que par défaut l'axe 3 ne soit pas affiché :
		echo "<br> ATTENTION, la mise à jour de version c'est mal déroulé.";
	}
	else
	{
		echo "<br> La mise à jour et le changement de version se sont déroulés avec succès.";
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
