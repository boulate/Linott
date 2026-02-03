<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<?php session_start(); ?>
<head>
<!--	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<link rel="stylesheet" href="CSS/Delta/css/normalise.css"> 
	<!-- Mon thème -->
	<link rel="stylesheet" href="style.css" />
	<link rel="icon" type="image/png" href="favicon.png" />

	<!-- On recupere la variable $periode de la page mere.-->
	<?php 
		$periode = $_GET['periode']; ?>


	<SCRIPT language="javascript">

function getXMLHttpRequest() 
{
    var xhr = null;
     
    if (window.XMLHttpRequest || window.ActiveXObject) {
        if (window.ActiveXObject) {
            try {
                xhr = new ActiveXObject("Msxml2.XMLHTTP");
            } catch(e) {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
        } else {
            xhr = new XMLHttpRequest();
        }
    } else {
        alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
        return null;
    }
     
    return xhr;
}

function cocher_axes_visibles()
{
	// On coche toutes les cases.		
	var elements = document.getElementsByTagName('input');
	for (var i = 0; i < elements.length; i++) 
	{
		if (elements[i].type == 'checkbox') 
		{	
			//alert(elements[i].id);
			elements[i].checked = true;
		}
	}
			
	// Page à aller chercher: importer_preferences_utilisateurs.php		
	var xhr_importer_preferences_utilisateurs = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhr_importer_preferences_utilisateurs.open("GET", "importer_preferences_utilisateurs.php?type=afficher_axes", false);
	xhr_importer_preferences_utilisateurs.onreadystatechange = function() 
	{ 
		if(xhr_importer_preferences_utilisateurs.readyState == 4)
		{ 
			//alert(xhr_importer_preferences_utilisateurs.responseText);
 			decouperChaine_importer_preferences_utilisateurs(xhr_importer_preferences_utilisateurs);
		}
		//alert(xhr_importer_preferences_utilisateurs.readyState); 
	} 

	xhr_importer_preferences_utilisateurs.send(null);
}
function decouperChaine_importer_preferences_utilisateurs(xhr)
{
	// On transforme l'objet recu en chaine de caractères..
	xhrText=xhr.responseText;
	//alert(xhrText);

	// On decoupe cette chaine de caractères d'abord pour avoir les differentes dates.
	donnees=xhrText.split(";");
	
		//// Nous allons gérer les variables "colonne = xxx" en les transformant en "xxx".
	// On cree le tableau donneesHeures dans lequel on "pushera" ensuite nos donneesHeures.
	tableDonnees		=	new Array ();
		
	// On "push" ensuite la 2eme colonne de notre réponse (donc la valeur de la variable) dans notre tableau "donneesHeures".
	tableDonnees.push(donnees[0]);
	tableDonnees.push(donnees[1]);	
	tableDonnees.push(donnees[2]);	

	preferences_masque_id_axe1		=	tableDonnees[0];
	preferences_masque_id_axe2		=	tableDonnees[1];
	preferences_masque_id_axe3		=	tableDonnees[2];

		//on enleve ensuite les axe2 à cacher:
		axes2_a_cacher		=	preferences_masque_id_axe2.split(",");
		for (var i = 0; i < axes2_a_cacher.length; i++)
		{
			//alert(axes2_a_cacher[i]);
			document.getElementById('checkAffichage'+axes2_a_cacher[i]).checked = false;
		}

}

function retourFiche()
{
	location.href="preferences.php";
}

function valider_affichage_axe()
{
	// Page à aller chercher: modifier_preferences_utilisateurs.php
}

function demarrage()
{
	change_couleur_bouton_menu_general();
	cocher_axes_visibles();
}

	</SCRIPT>

</head>


<BODY onLoad="demarrage();">
<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id=tableGlobale>
<tr ><td></td><td id="tableGlobale">

<?php include("menu_general.php") ?>

	<br/>
	<br/>
    <!-- Table permettant les bords à 3% -->
    <table border=0 width=100%>
    <tr>
    <td width=3%></td>
    <td>


<table width=95% border=0 align=center><tr><td><br>
	<form name="valider_fiche" method="post" action="valider_preferences.php?pref=axe2">

	<?php include("connexion_base.php"); ?> 
		<?php
		// On fait une boucle "while" pour afficher chaque section. Chaque section contient une autre boucle "while" pour afficher chaque axe2.
		$reponse_section = $bdd->query('SELECT * FROM Section ORDER BY codeSection');
		

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
		
		echo "<div id='div_chck'>";
		
		while ($donnees_section = $reponse_section->fetch())
			{
				// On recherche dans le tableau "couleur[]" la couleur correspondant à compteur_couleur et on l'insère. On incrémente ensuite le compteur_couleur.
				$nom_section=$donnees_section['nomSection'];
				$id_section=$donnees_section['idSection'];
				echo "<table width=100% border=0><tr><th bgcolor=$couleur[$compteur_couleur] id=fenetre_choix>$nom_section</th><td></td></tr></table>"; ?>

				
						<table width=100% id="tableau_fenetre_choix" bgcolor="<?php echo $couleur[$compteur_couleur] ?>">
						<?php
						// Voici la boucle permettant d'afficher chaque axe2 dans chaque section.						
						$reponse_axe2 = $bdd->query("SELECT * FROM Axe2 where Section_idSection = '$id_section' ORDER BY codeAxe2"); 
						while ($donnees_axe2 = $reponse_axe2->fetch())
						{
								$nom_axe2=$donnees_axe2['nomAxe2'];
								$id_axe2=$donnees_axe2['idAxe2'];
							
						
								// A chaque tour de la boucle while dans laquelle on est, on affiche un bouton radio avec le nom de l'axe2. Label et ID pour que le text soit cliquable.
								// Grace a la fonction "onClick", on remonte la valeur selectionne a la fonction javascript Reporter pour que la page mere se mette a jour.
								?> <tr><td><input type="checkbox" name="<?php echo $id_axe2 ?>" id="checkAffichage<?php echo $id_axe2 ?>" value="<?php echo $nom_axe2 ?>" /> <label for="<?php echo $id_axe2 ?>"><?php echo $nom_axe2 ?></label></td></tr>
						<?php	
						}
						$reponse_axe2->closeCursor(); // Termine le traitement de la requête 
					?>
						<tr><td></td></tr>
						</table><br>	
					<?php $compteur_couleur++;
			}
			$reponse_section->closeCursor();
		echo "</div>";
		?>
</td></tr></table>
<table width=100%>
	<tr>
		<td align=right>

			<br />
			<input type="submit" name="valider" id="valider" value="Valider" /><br /><br /><br />

		</td>
	</tr>
</table>
</form>

    <!-- Fin de la table permettant les bords à 3% --> 
    </td>
    <td width=3%></td>
    </tr></table>
	
<!-- Fin de la table de mise en page globale -->
</td><td></td></tr>
</table>
</body>
</html>