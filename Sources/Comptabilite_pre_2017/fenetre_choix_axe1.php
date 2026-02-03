<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<!--	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<link rel="stylesheet" href="CSS/Delta/css/normalise.css"> 
	<!-- Mon thème -->
	<link rel="stylesheet" href="style.css" />

	<!-- On recupere la variable $periode de la page mere et l'id utilisateur-->
	<?php 
		session_start();
		$periode 		=	$_GET['periode']; 
		$idUtilisateur		=	$_SESSION['idUtilisateurs'];	
	?>


	<SCRIPT language="javascript">
		// Cette fonction nous permet de reporter le choix fait dans le bouton radio a notre page mere. (grace a la variable "onclick").
		function Reporter(l) 
		{

			nomPopup=window.name;

			if (nomPopup == "popup_axe1_de_stats")
			{
				// on prend le nom
				var choix_value=l.value;
				window.opener.updateChamp('filtre_axe1', choix_value);
				// on prend l'id			
				var choix_id=l.id;
				window.opener.updateChamp('id_filtre_axe1', choix_id);
				//alert('otot');
				window.close();
			}
			if (nomPopup == "popup_axe1_periodes")
			{
				// on prend le nom
				var choix_value=l.value;
				window.opener.document.forms['valider_fiche'].elements["choix_axe1_periode<?php echo "$periode"?>"].value=choix_value;
				// on prend l'id			
				var choix_id=l.id;
				window.opener.document.forms['valider_fiche'].elements["id_choix_axe1_periode<?php echo "$periode"?>"].value=choix_id;
				window.close();				
			}
				
		}	
	</SCRIPT>

</head>


<body>
<table width=95% border=0 align=center><tr><td><br>
	<?php 	require("connexion_base.php"); 
			include("importer_configuration.php");
	?>
		<?php
		
		// On execute cette boucle de filtre des axes QUE si on a une période de définie (et donc qu'on est dans la déclaration d'heures).
		if(isset($periode))
		{
			// On défini les id à exclure dans le choix suivant les préférences de l'utilisateur
			$reponse_id_exclus = $bdd->query("SELECT preferences_masque_id_axe1 FROM Utilisateurs where idUtilisateurs = $idUtilisateur");

			
			while ($donnees_id_exclus = $reponse_id_exclus->fetch())
			{	
				$id_exclus = $donnees_id_exclus['preferences_masque_id_axe1'];
			}
			$reponse_id_exclus->closeCursor();
			if ($id_exclus != "") 
			{
				$andExclus = "AND idAxe1 NOT IN ($id_exclus)";
			}
			if ($id_exclus == "")
			{
				$andExclus = "";
			}
		}
		
		// On fait une boucle "while" pour afficher chaque section. Chaque section contient une autre boucle "while" pour afficher chaque axe1.
		//$reponse_section = $bdd->query('SELECT * FROM Section ORDER BY codeSection');
		// Modif permettant de n'afficher QUE les sections utilisées.
		$reponse_section = $bdd->query('SELECT * FROM Section where idSection in (select distinct(Section_idSection) from Axe1) ORDER BY codeSection');


		// Déclaration du tableau des couleurs pour les sections.
		$couleur=array();
			$couleur[0]="#BCF5A9";		
			$couleur[1]="#A9F5F2";
			$couleur[2]="#A9A9F5";
			$couleur[3]="#D0A9F5";
			$couleur[4]="#F5A9F2";
			$couleur[5]="#F5BCA9";
			$couleur[6]="#F5D0A9";
		$compteur_couleur=0;

		while ($donnees_section = $reponse_section->fetch())
			{
				// On recherche dans le tableau "couleur[]" la couleur correspondant à compteur_couleur et on l'insère. On incrémente ensuite le compteur_couleur.
				$nom_section=$donnees_section['nomSection'];
				$id_section=$donnees_section['idSection'];
				echo "<table width=100% border=0><tr><th bgcolor=$couleur[$compteur_couleur] id=fenetre_choix>$nom_section</th><td></td></tr></table>"; ?>

				
						<table width=100% id="tableau_fenetre_choix" bgcolor="<?php echo $couleur[$compteur_couleur] ?>">
						<?php
						// Voici la boucle permettant d'afficher chaque axe1 dans chaque section.						
						$reponse_axe1 = $bdd->query("SELECT * FROM Axe1 where Section_idSection = '$id_section' $andExclus ORDER BY codeAxe1"); 
						while ($donnees_axe1 = $reponse_axe1->fetch())
						{
								$nom_axe1=$donnees_axe1['nomAxe1'];
								$id_axe1=$donnees_axe1['idAxe1'];
								

								$code_axe1="";
								if ( $afficherCodesComptablesSelectionAxes == "checked" )
								{
									$code_axe1=$donnees_axe1['codeAxe1'];
									$code_axe1="$code_axe1 - ";
								}



								// A chaque tour de la boucle while dans laquelle on est, on affiche un bouton radio avec le nom de l'axe1. Label et ID pour que le text soit cliquable.
								// Grace a la fonction "onClick", on remonte la valeur selectionne a la fonction javascript Reporter pour que la page mere se mette a jour.
								?> <tr><td><input type="radio" name="myRadios" onClick="Reporter(this)" id="<?php echo $id_axe1 ?>" value="<?php echo $nom_axe1 ?>" /> <label for="<?php echo $id_axe1 ?>"><?php echo "$code_axe1 $nom_axe1"; ?></label></td></tr>
						<?php	
						}
						$reponse_axe1->closeCursor(); // Termine le traitement de la requête 

					?>
						<tr><td></td></tr>
						</table><br>	
					<?php $compteur_couleur++;
			}
			$reponse_section->closeCursor();

		?>
</td></tr></table>
</body>
