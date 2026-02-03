<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
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

			if (nomPopup == "popup_axe3_de_stats")
			{
				// on prend le nom
				var choix_value=l.value;
				window.opener.updateChamp('filtre_axe3', choix_value);
				// on prend l'id			
				var choix_id=l.id;
				window.opener.updateChamp('id_filtre_axe3', choix_id);
				window.close();
			}
			if (nomPopup == "popup_axe3_periodes")
			{
				// on prend le nom
				var choix_value=l.value;
				window.opener.document.forms['valider_fiche'].elements["choix_axe3_periode<?php echo "$periode"?>"].value=choix_value;
				// on prend l'id			
				var choix_id=l.id;
				window.opener.document.forms['valider_fiche'].elements["id_choix_axe3_periode<?php echo "$periode"?>"].value=choix_id;

				window.close();			
			}





			// // on prend le nom
			// var choix_value=l.value;
			// window.opener.document.forms['valider_fiche'].elements["choix_axe3_periode<?php echo "$periode"?>"].value=choix_value;
			// // on prend l'id			
			// var choix_id=l.id;
			// window.opener.document.forms['valider_fiche'].elements["id_choix_axe3_periode<?php echo "$periode"?>"].value=choix_id;
			// window.close();
			//		}
		}	
	</SCRIPT>

</head>


<body>
<table width=95% border=0 align=center><tr><td><br>
	<?php include("connexion_base.php"); ?> 

	<table width=100% border=0><tr><th bgcolor=#B0DD8D id=fenetre_choix>Choix de l'axe3:</th><td></td></tr>
	</table>
	
	<table width=100% id="tableau_fenetre_choix_axe3" bgcolor=#B0DD8D>
	
	<tr><td width=5%></td>	<td></td>	<td width=5%></td></tr>
		
		<?php
		
		// On execute cette boucle de filtre des axes QUE si on a une période de définie (et donc qu'on est dans la déclaration d'heures).
		if(isset($periode))
		{
			// On défini les id à exclure dans le choix suivant les préférences de l'utilisateur
			$reponse_id_exclus = $bdd->query("SELECT preferences_masque_id_axe3 FROM Utilisateurs where idUtilisateurs = $idUtilisateur");
			while ($donnees_id_exclus = $reponse_id_exclus->fetch())
			{	
				$id_exclus = $donnees_id_exclus['preferences_masque_id_axe3'];
			}
			$reponse_id_exclus->closeCursor();
			if ($id_exclus != "")
			{	
				$andExclus = "AND idAxe3 NOT IN ($id_exclus)";
			}
			if ($id_exclus == "")
			{
				$andExclus = "";
			}
		}
		
		
		
		
		
		// On fait une boucle "while" pour afficher chaque axe3.
		$reponse_axe3 = $bdd->query("SELECT * FROM Axe3 WHERE active=1 $andExclus ORDER BY nomAxe3");
		
		$i=1;
		while ($donnees_axe3 = $reponse_axe3->fetch())
			{
				$nom_axe3=$donnees_axe3['nomAxe3'];
				$id_axe3=$donnees_axe3['idAxe3'];
				// echo "$nom_axe3<br/>";

					// A chaque tour de la boucle while dans laquelle on est, on affiche un bouton radio avec le nom de l'axe1. Label et ID pour que le text soit cliquable.
					// Grace a la fonction "onClick", on remonte la valeur selectionne a la fonction javascript Reporter pour que la page mere se mette a jour.
		?> 
				<tr><td></td><td><input type="radio" name="myRadios" onClick="Reporter(this)" id="<?php echo $id_axe3 ?>" value="<?php echo $nom_axe3 ?>" /> <label for="<?php echo $id_axe3 ?>"><?php echo $nom_axe3 ?></label> </td><td></td></tr>

		<?php 
			}
			$reponse_axe3->closeCursor();

		?>
	<tr><td><br></td></tr></table>	
		
</td></tr>
</table>

</body>
