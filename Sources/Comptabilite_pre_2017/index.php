<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- Mon thème -->
	<link rel="stylesheet" href="style.css" />
	
	<link rel="stylesheet" href="CSS/Delta/css/normalise.css">
<head>
<link rel="icon" type="image/png" href="favicon.png" />
</head>

<body>

<?php
	$error = $_GET['error'];
	$login = $_GET['login'];
?>

<form name="valider_login" method="post" action="authentification.php">


<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id=tableGlobale><tr ><td></td><td id="tableGlobale">

<table border=0 width=100%>
	<tr>
		<th>
			Bienvenue sur Linott.
			<br>
			<img src="Logo_Linott.png" /> 
			
		</th>
	</tr>
	<tr>
		<td align=center>
			Linott Is Not Only a Time Tracker...
			<br><br><br>
		</td>
	</tr>

</table>
<table border=0 width=100%>
	<tr>
		<td align=center height=50px>
			Utilisateur:<br>
			<input type=username id="login" name="login">
		</td>
	</tr>
	<tr>
		<td align=center height=50px>
			Mot de passe:<br>
			<input type=password id="password" name="password">
		</td>
	</tr>
	<tr>
		<td align=center>
			<input type="submit" name="valider" id="valider" value="valider">
		</td>
	</tr>
</table>

<table border=0 width=100%>
	<tr>
		<td align=center>
			<FONT color="red">
				<?php 
					// Si on est de retour sur cette page à cause d'une erreur de login, on affiche l'erreur.
					if ( (isset($error)) && ($error != "vide") )
					{
						echo $error;
					}
				?>
			</font>
		</td>
	</tr>
</table>

<!-- Fin de la table de mise en page globale -->
</td><td></td></tr></table>
</form>

</body>
