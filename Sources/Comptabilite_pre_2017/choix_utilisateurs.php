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
		$idUtilisateur		=	$_SESSION['idUtilisateurs'];
		$admin				=	$_SESSION['admin'];

		$idUtilisateurs = $_GET['listeUtilisateursConcernes'];
		$idGroupes = $_GET['listeGroupesConcernes'];

		$tableUtilisateurs =  explode ("," , $idUtilisateurs);
		$tableGroupes =  explode ("," , $idGroupes);

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

function valider_choix()
{
	// On recupere la liste des groupes
	listeCheckboxGroupes = document.getElementsByName('checkboxGroupes');
	var tableIdGroupesCoches = new Array();
	var tableIdUtilisateursCoches = new Array();

	for (var i = 0; i < listeCheckboxGroupes.length; i++)
	{
		if ( listeCheckboxGroupes[i].checked == true )
		{
			tableIdGroupesCoches.push(listeCheckboxGroupes[i].value);
		}
	}
	listeIdGroupesCoches = tableIdGroupesCoches.toString();
	window.opener.choix_utilisateurs_evenement(listeIdGroupesCoches, "groupe");


	// On recupere la liste des utilisateurs
	listeCheckboxUtilisateurs = document.getElementsByName('checkboxUtilisateurs');
	var tableIdUtilisateursCoches = new Array();
	var tableIdUtilisateursCoches = new Array();

	for (var i = 0; i < listeCheckboxUtilisateurs.length; i++)
	{
		if ( listeCheckboxUtilisateurs[i].checked == true )
		{
			tableIdUtilisateursCoches.push(listeCheckboxUtilisateurs[i].value);
		}
	}
	listeIdUtilisateursCoches = tableIdUtilisateursCoches.toString();
	window.opener.choix_utilisateurs_evenement(listeIdUtilisateursCoches, "utilisateur");


closeWindows();

}

</SCRIPT>

</head>


<body>
<?php
	require("connexion_base.php"); 


	echo "<input type=hidden id='idGroupes' value='$idGroupes'>";
	echo "<input type=hidden id='idUtilisateurs' value='$idUtilisateurs'>";
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
										echo "Selection des utilisateurs et groupes pour le nouvel événement" 
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
											<th id="ongletBleuPropriete">Groupes</th>
											<td></td>
										</tr>
									</table>
									<table border=0 width=100% id="tableOngletsBleu">

										<tr>				  			
								  			<td width=3%> 
								  			</td>

								  			<td>
												<br />

												<?php
													$sql_event = "SELECT * FROM Groupes order by nom";
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
														$idGroupeBDD	= 	$donnees['id'];
														$nomGroupe		=	$donnees['nom'];
														

														$checked = "";
														foreach ($tableGroupes as $idGroupe) 
														{															
															if ( $idGroupeBDD == $idGroupe )
															{
																$checked = "checked";
															}
														}
														echo "<input type='checkbox' name='checkboxGroupes' id='checkbox_idGroupe_$idGroupeBDD' value='$idGroupeBDD' $checked><label for=checkbox_idGroupe_$idGroupeBDD> $nomGroupe </label> <br /><br />";

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
														echo "<input type='checkbox' name='checkboxUtilisateurs' id='checkbox_idUtilisateur_$idBase' value='$idBase' $checked><label for=checkbox_idUtilisateur_$idBase> $prenom $nom </label> <br />";

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
									
								</td>
								
								<td>
								</td>

								<td align="right">
									<input type=button value="Valider" onClick='valider_choix();'>									
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