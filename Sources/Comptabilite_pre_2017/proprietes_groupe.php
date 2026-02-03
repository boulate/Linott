<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<!--	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<link rel="stylesheet" href="CSS/Delta/css/normalise.css"> 
	<!-- Mon thème -->
	<link rel="stylesheet" href="style.css" />
	<link rel="stylesheet" href="calendrier.css" />


	<!-- On recupere la variable $periode de la page mere et l'id utilisateur-->
	<?php 
		session_start();
		$idGroupe			=	$_GET['idGroupe'];

		$idUtilisateur		=	$_SESSION['idUtilisateurs'];
		$admin				=	$_SESSION['admin'];
	?>


	<SCRIPT>

function closeWindows()
{
	//window.parent.opener.location.reload();
	window.close();
}

function getXMLHttpRequest() // Fonction AJAX utilisée par toutes les fonctions allant chercher des données sur une autre page PHP (validation semaine, importer fiche, importer date, importer heures, etc.)
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

function valider_configuration()
{
	idGroupe = document.getElementById('idGroupe').value;


	listeCheckbox = document.getElementsByName('checkbox');


	var tableIdCoches = new Array();
	for (var i = 0; i < listeCheckbox.length; i++)
	{
		if ( listeCheckbox[i].checked == true )
		{
			tableIdCoches.push(listeCheckbox[i].value);
		}
	}

	listeIdCoches = tableIdCoches.toString();

	//alert(listeIdCoches);

	var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhr.open("GET", "modifier_groupe.php?idGroupe="+idGroupe+"&listeIdUtilisateurs="+listeIdCoches, false);
	xhr.onreadystatechange = function() 
	{ 
		if(xhr.readyState == 4)
		{ 
			//alert(xhr.responseText);
		}
		//alert(xhr.readyState); 
	} 
	xhr.send(null);
closeWindows();
}

function supprimer_groupe()
{
	idGroupe 	= document.getElementById('idGroupe').value;
	nomGroupe 	= document.getElementById('nomGroupe').value;

		if (confirm("Voulez vous vraiment supprimer le groupe '"+nomGroupe+"' ?")) 
			{
				var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
				xhr.open("GET", "modifier_groupe.php?idGroupe="+idGroupe+"&supprimerGroupe=1", false);
				xhr.onreadystatechange = function() 
				{ 
					if(xhr.readyState == 4)
					{ 
						alert(xhr.responseText);
					}
					//alert(xhr.readyState); 
				} 
				xhr.send(null);
			}
	window.parent.opener.location.reload();
	closeWindows();
}
</SCRIPT>

</head>


<body>
<?php
	require("connexion_base.php"); 

	$sql_event = "SELECT * FROM Groupes WHERE id = $idGroupe";
	//echo "$sql_event";
	try
	{
		$reponse_event = $bdd->query($sql_event) or die('Erreur SQL !<br>' .$sql_event. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	while ($donnees = $reponse_event->fetch())
	{
		$nomGroupe			=	$donnees['nom'];
		$listeUtilisateurs 	= 	$donnees['idUtilisateurs'];

		$tableUtilisateurs =  explode ("," , $listeUtilisateurs);
	}
	$reponse_event->closeCursor();

	echo "<input type=hidden id='idGroupe' value='$idGroupe'>";
	echo "<input type=hidden id='nomGroupe' value='$nomGroupe'>";
?>

<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width="100%" id="tableGlobale">
	<tr>	
		<td>
		</td>	

		<td id="tableGlobale">

			<!-- Table permettant les bords à 3% -->
			<table border=0 width=100%>
				<tr>
					<td width=3%>	
					</td>
					
					<td>
						<table width=100%>
							<tr>
								<th id="titreTableau" align=center>
									<?php
										echo "Modification du groupe : $nomGroupe" 
									?> 
									<br />
									<br />
								</th>
							</tr>
						<table>

						<table border=0 width=100%>
							<tr>
								<td>
									<table border=0 width=100%>
										<tr>
											<th id="ongletVertPropriete">Utilisateurs</th>
											<td></td>
										</tr>
									</table>
									<table border=0 width=100% id="tableOngletsVert">

										<tr>				  			
								  			<td width=3%> 
								  			</td>

								  			<td>
												<br />

												<?php
													$sql_event = "SELECT idUtilisateurs, prenom, nom FROM Utilisateurs order by nom";
													//echo "$sql_event";
													try
													{
														$reponse_event = $bdd->query($sql_event) or die('Erreur SQL !<br>' .$sql_event. '<br>'. mysql_error());
													}
													catch(Exception $e)
													{
														// En cas d'erreur précédemment, on affiche un message et on arrête tout
														die('Erreur : '.$e->getMessage());
													}
													while ($donnees = $reponse_event->fetch())
													{
														$idBase			= 	$donnees['idUtilisateurs'];
														$nom			=	$donnees['nom'];
														$prenom			=	$donnees['prenom'];

														$checked = "";
														foreach ($tableUtilisateurs as $idUtilisateur) 
														{															
															if ( $idBase == $idUtilisateur )
															{
																$checked = "checked";
															}
														}
														echo "<input type='checkbox' name='checkbox' id='checkbox_id_$idBase' value='$idBase' $checked><label for=checkbox_id_$idBase> $prenom $nom </label> <br />";

													}
													$reponse_event->closeCursor();
												?>
								  			</td>

								  			<td width=3%>
								  			</td>

										</tr>

										<tr>
											<td>
											</td>
										</tr>
								  	<table>
								</td>
							</tr>
						</table>
						<br />
						<br />

						<table width="90%" align="center">
							<tr>
								<td align="left">
									<input type=button value="Supprimer" onClick='supprimer_groupe();'>
								</td>
								
								<td>
								</td>

								<td align="right">
									<input type=button value="Valider" onClick='valider_configuration();'>									
								</td>
							</tr>
						</table>


					<!-- Fin de la table permettant les bords à 3% --> 
					</td>
					
					<td width=3% id="toto">
					</td>
				</tr>
			</table>
			
		<!-- Fin de la table de mise en page globale -->
		</td>
		
		<td>
		</td>
	</tr>
</table>

</body>
</html>