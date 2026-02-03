<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<?php session_start(); ?>
<head>
<?php
	// Permet de rediriger vers l'acceuil si utilisateur non enregistré.
	$prenom = $_SESSION['prenom'];
	if (!$prenom)
	{
		header('Location: index.php'); 
	} 
	
	$idSession = $_SESSION['idUtilisateurs	'];
?>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="style.css" />
	<link rel="stylesheet" href="CSS/Delta/css/normalise.css"> 
	<link rel="icon" type="image/png" href="favicon.png" />


<!-- J'importe mon fichier permettant de vérifier les inputs -->
<script src="verifier_input_javascript.js"></script>

<script>
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

function retourFiche()
{
	location.href="compta.php";
}

function rafraichir_page()
{
	//document.location.href='administration.php'
}

function modifierMotDePasse()
{

	newPass1=document.getElementById('changementPassword1').value;
	newPass2=document.getElementById('changementPassword2').value;

	checkInput(newPass2);
	
	if (newPass1 == newPass2)
	{
		//alert(nomAxe3);
		var xhrModifierPassword = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente

		xhrModifierPassword.open("GET", "modifier_password.php?newPassword="+newPass1+"&from=preferences", true);
		//alert("modifier_password.php?idUtilisateur="+idUtilisateur+"&newPassword="+newPass1+"&loginUtilisateur="+loginUtilisateur);
		xhrModifierPassword.onreadystatechange = function()
		{ 
			if(xhrModifierPassword.readyState == 4)
			{ 
				alert(xhrModifierPassword.responseText);
			}
			//alert(xhr.readyState); 
		} 
		xhrModifierPassword.send(null);

	}
	if (newPass1 != newPass2)
	{
		alert("Les deux mots de passes rentrés ne correspondent pas.")
	}

}

function demarrage()
{
	change_couleur_bouton_menu_general();
}

</script> 
</head>

<BODY onLoad="demarrage();">
<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id=tableGlobale>
<tr ><td></td><td id="tableGlobale">


	<!-- Permet de se connecter à la base Mysql-->
	<?php include("connexion_base.php");

	$loginSession	=	$_SESSION['login'];
	?>
<!-- 	<table border=0 width=100%>
		<tr>
			<td width=18%><input type=text id="retour" value="retour"  onClick="retourFiche()" /></td>		
			</td>

			<th>
				Préférences utilisateur
			</th>

			<td width=26% align=right>
				<input type="button" value="Déconnexion" onClick="javascript:document.location.href='deconnexion.php'" />
			</td>
		</tr>
</table> -->

	<?php include("menu_general.php") ?>

	<br/>
	<br/>
    <!-- Table permettant les bords à 3% -->
    <table border=0 width=100%>
    <tr>
    <td width=3%></td>
    <td>
	<table border=0 width=100%>
		<tr>
			<td>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertFavoris">Affichage des axes</th>
						<td></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><td><table border=0 width=95%>
						  <tr><td></td></tr>
						  <tr>
								<th colspan=3 align=center id="titreTableau">Vous permet de désactiver les axes que vous n'utilisez pas.<br><br> </th>
						  </tr>
						  <tr>
						  		<td align=center>
						  			<!-- Choix axe 1 sur cette periode -->
									<input type="button" id="affichage_axe1" name="affichage_axe1"  value="Axes 1 à afficher" onClick=javascript:document.location.href='fenetre_preference_axe1.php' readonly> 
								</td>
						
								<td align=center>
									<!-- Choix axe 2 sur cette periode -->
									<input type="button" id="affichage_axe2" name="affichage_axe2"  value="Axes 2 à afficher" onClick=javascript:document.location.href='fenetre_preference_axe2.php' readonly> 
								</td>
						  		<td align=center>
						  			<!-- Choix Axe3 sur cette periode -->
									<input type="button" id="affichage_axe3" name="affichage_axe3"  value="Axes 3 à afficher" onClick=javascript:document.location.href='fenetre_preference_axe3.php' readonly> 					
								</td>

						  </tr>

						  <tr><td><br /></td></tr>
					</table></td></tr>
				</table>

				<br /><br />

				<table border=0 width=100%>
					<tr>
						<th id="ongletVertFavoris">Modifier mot de passe</th><td></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><td><table border=0 width=95%>
						  <tr><td></td></tr>
						  <tr>
								<th colspan=3 align=center id="titreTableau">Vous permet de modifier votre mot de passe.<br><br> </th>
						  </tr>
						  <tr>
						  		<td align=center>
									Mot de passe: 
								</td>
						
								<td align=center>
									Vérifier mot de passe: 
								</td>
						  		<td align=center>
								</td>

						  </tr>
						  <tr>
						  		<td align=center>
									<input type="password" id="changementPassword1" name="changementPassword1"  value=""> 
								</td>
						
								<td align=center>
									<input type="password" id="changementPassword2" name="changementPassword2"  value=""> 
								</td>
						  		<td align=center>
						  			<!-- Choix Axe3 sur cette periode -->
									<input type="button" id="modifierMotDePasse" name="modifierMotDePasse"  value="Modifier" onClick='javascript:modifierMotDePasse();' readonly> 					
								</td>

						  </tr>

						  <tr><td><br /></td></tr>
					</table></td></tr>
				</table>



			</td>
		</tr>
	</table>
	<br/>


	
    <!-- Fin de la table permettant les bords à 3% --> 
    </td>
    <td width=3%></td>
    </tr></table>
	
<!-- Fin de la table de mise en page globale -->
</td><td></td></tr>
</table>
</body>
</html>