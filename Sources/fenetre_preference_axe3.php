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

		//on enleve ensuite les axe3 à cacher:
		axe3_a_cacher		=	preferences_masque_id_axe3.split(",");
		for (var i = 0; i < axe3_a_cacher.length; i++)
		{
			//alert(axe3_a_cacher[i]);
			document.getElementById('checkAffichage'+axe3_a_cacher[i]).checked = false;
		}
}

function valider_affichage_axe()
{
	// Page à aller chercher: modifier_preferences_utilisateurs.php
}

function retourFiche()
{
	location.href="preferences.php";
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
	<form name="valider_fiche" method="post" action="valider_preferences.php?pref=axe3">

	<?php include("connexion_base.php"); 
			echo "<div id='div_chck'>";
		
				echo "<table width=100% border=0><tr><th id=ongletVert>Axe3</th><td></td></tr></table>"; 
	?>

				
						<table width=100% id="tableOngletsVert">
	<?php
						// Voici la boucle permettant d'afficher chaque axe3 dans chaque section.						
						$reponse_axe3 = $bdd->query('SELECT * FROM Axe3 WHERE active=1 ORDER BY nomAxe3'); 
						while ($donnees_axe3 = $reponse_axe3->fetch())
						{
								$nom_axe3=$donnees_axe3['nomAxe3'];
								$id_axe3=$donnees_axe3['idAxe3'];
							
						
								// A chaque tour de la boucle while dans laquelle on est, on affiche un bouton checkbox avec le nom de l'axe3. Label et ID pour que le text soit cliquable.
	?>					<tr><td><input type="checkbox" name="<?php echo $id_axe3 ?>" id="checkAffichage<?php echo $id_axe3 ?>" value="<?php echo $nom_axe3 ?>" /> <label for="<?php echo $id_axe3 ?>"><?php echo $nom_axe3 ?></label></td></tr>
	<?php	
						}
						$reponse_axe3->closeCursor(); // Termine le traitement de la requête 
	?>
						<tr><td></td></tr>
						</table><br>	
	 		
			</div>
	
</td></tr></table>
<table width=100%>
	<tr>
		<td align=right>

			<br />
			<input type="submit" name="valider" id="valider" value="Valider" />

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