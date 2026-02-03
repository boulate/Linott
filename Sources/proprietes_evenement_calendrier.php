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
		$idEvent 			=	$_GET['idEvent']; 
		$idUtilisateur			=	$_SESSION['idUtilisateurs'];
		$admin				=	$_SESSION['admin'];
		$couleur			=	$_SESSION['couleur'];
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

function modifier_event(typeEvent)
{
	//alert(typeEvent);

	idEvent = document.getElementById('idEvent').value;

	idUtilisateurEvent = document.getElementById('idUtilisateurEvent').value;
	loginUtilisateurEvent = document.getElementById('loginUtilisateurEvent').value;
	couleurEvent = document.getElementById('couleurEvent').value;
	dateEvent = document.getElementById('dateEvent').value;
	periode = document.getElementById('periodeEvent').value;
	valide = document.getElementById('validationEvent').value;
	bloquant = document.getElementById('bloquant').value;
	indisponible = document.getElementById('indisponible').value;


	if ( typeEvent == "event")
	{
		if (document.getElementById('check_bloquant').checked == true)
		{
			bloquant = 1;
		}
		else bloquant = 0;

		if (document.getElementById('check_indisponible').checked == true)
		{
			indisponible = 1;
		}
		else indisponible = 0;

		infosEvent = document.getElementById('description_evenement').value;
		typeModif = "event";
	}
	if ( typeEvent == "absence")
	{
		infosEvent = document.getElementById('type_absence').value;
		typeModif = "event";
	}
	if ( typeEvent == "commentaire" )
	{
		infosEvent = document.getElementById('commentaire').value;
		typeModif = "commentaire";
	}
	if ( typeEvent == "astreinte")
	{
		infosEvent = document.getElementById('astreinte').value;
		typeModif = "astreinte";
	}

			var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhr.open("GET", "modifier_infos_evenement.php?idEvenement="+idEvent+"&modifDescriptionEvent="+infosEvent+"&typeModif="+typeModif+"&bloquant="+bloquant+"&indisponible="+indisponible, false);
			xhr.onreadystatechange = function() 
			{ 
				if(xhr.readyState == 4)
				{ 
					//alert("bloquant: "+bloquant+" , indisponible: "+indisponible)
					//alert(idEvent+", "+idUtilisateurEvent+", "+loginUtilisateurEvent+", "+couleurEvent+", "+dateEvent+", "+periode+", "+typeEvent+", "+valide+", "+infosEvent+", "+bloquant);
					if ( typeModif == "event" )
					{
						window.parent.opener.renseignerCongeCalendrier(idEvent, idUtilisateurEvent, loginUtilisateurEvent, couleurEvent, dateEvent, periode, typeEvent, valide, infosEvent, bloquant);
					}
					alert(xhr.responseText);

				}
				//alert(xhr.readyState); 
			} 
			xhr.send(null);

	if ( window.parent.opener.document.getElementById('renseigner_automatiquement_conge_valide').value == "checked" )
	{
		validation = document.getElementById('validationEvent').value;
		idEvenementBDD = document.getElementById('idEvent').value;
		AncienneDescriptionEvent = document.getElementById('descriptionEventHidden').value; 
		// Si on valide le congé, on le renseigne en base pour qu'il soit automatiquement dans la fiche d'heures.
		if ( validation == "V")
		{
			modifier_conge_fiche_heures("modifier", idEvenementBDD, AncienneDescriptionEvent);
		}
	}

	window.location.reload();
}



function supprimer_event()
{
	idEvent = document.getElementById('idEvent').value;
	typeEvent = document.getElementById('typeEvent').value;
	dateLisible = document.getElementById('dateLisible').value;
	jourSemaine = document.getElementById('jourSemaine').value;
	

	if ( typeEvent == "event")
	{
		infosEvent = document.getElementById('description_evenement').value;
	}
	if ( typeEvent == "absence")
	{
		infosEvent = document.getElementById('type_absence').value;
	}
	if ( typeEvent == "astreinte")
	{
		infosEvent = document.getElementById('astreinte').value;
	}

		if (confirm("Voulez vous vraiment supprimer l'évèmenent '"+infosEvent+"' du "+jourSemaine+" "+dateLisible+" ?")) 
			{
				window.parent.opener.gestion_conge_fiche_heures("supprimer", idEvent);

				var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
				xhr.open("GET", "modifier_infos_evenement.php?idEvenement="+idEvent+"&supprimerEvent=1", false);
				xhr.onreadystatechange = function() 
				{ 
					if(xhr.readyState == 4)
					{ 
						if ( xhr.responseText.indexOf("Erreur") != -1 )
						{
							alert(xhr.responseText);
						}
						else
						{
							//alert(idEvent);
							window.parent.opener.document.getElementById('congeId'+idEvent).innerHTML = "";
							closeWindows();
						}
					}
					//alert(xhr.readyState); 
				} 
				xhr.send(null);
			}
}

function modifier_conge_fiche_heures(queFaire, idConge, AncienneDescriptionEvent)
{
			var xhrRenseigneAutomatiqueConge = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhrRenseigneAutomatiqueConge.open("GET", "renseigner_automatiquement_conge.php?idEvenement="+idEvenementBDD+"&queFaire="+queFaire+"&AncienneDescriptionEvent="+AncienneDescriptionEvent, false);
			//alert("modifier_utilisateur.php?idAModifier="+idUtilisateur+"&nom="+nom+"&prenom="+prenom+"&nbrHeures="+nbrHeures+"&login="+login+"&admin="+admin+"&active="+active);
			xhrRenseigneAutomatiqueConge.onreadystatechange = function() 
			{ 
				if(xhrRenseigneAutomatiqueConge.readyState == 4)
				{ 
					if (xhrRenseigneAutomatiqueConge.responseText.indexOf("ATTENTION") != -1 )
					{
						alert(xhrRenseigneAutomatiqueConge.responseText);
					}
				}
				//alert(xhr.readyState); 
			} 

			xhrRenseigneAutomatiqueConge.send(null);
}


function modifier_utilisateurs()
{
	idEvent = document.getElementById('idEvent').value;

	// On recupere la liste des groupes
	listeCheckboxGroupes = document.getElementsByName('checkboxGroupes');
	var tableIdGroupesCoches = new Array();
	var tableIdUtilisateursCoches = new Array();

	nombreCheckboxGroupesNonCochees = 0;
	nombreCheckboxUtilisateursNonCochees = 0;

	for (var i = 0; i < listeCheckboxGroupes.length; i++)
	{
		if ( listeCheckboxGroupes[i].checked == true )
		{
			tableIdGroupesCoches.push(listeCheckboxGroupes[i].value);
		}
		if ( listeCheckboxGroupes[i].checked == false )
		{
			nombreCheckboxGroupesNonCochees++;
		}
	}
	listeIdGroupesCoches = tableIdGroupesCoches.toString();

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
		if ( listeCheckboxUtilisateurs[i].checked == false )
		{
			nombreCheckboxUtilisateursNonCochees++;
		}
	}
	listeIdUtilisateursCoches = tableIdUtilisateursCoches.toString();

	if (nombreCheckboxGroupesNonCochees == 0)
	{
		listeIdGroupesCoches = "ALL";
	}	
	if (nombreCheckboxUtilisateursNonCochees == 0)
	{
		listeIdUtilisateursCoches = "ALL";
	}


	typeModif = "utilisateursConcernes";

	var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhr.open("GET", "modifier_infos_evenement.php?idEvenement="+idEvent+"&utilisateursConcernes="+listeIdUtilisateursCoches+"&groupesConcernes="+listeIdGroupesCoches+"&typeModif="+typeModif, false);
	//alert("modifier_infos_evenement.php?idEvenement="+idEvent+"&utilisateursConcernes="+listeIdUtilisateursCoches+"&groupesConcernes="+listeIdGroupesCoches);
	xhr.onreadystatechange = function() 
	{ 
		if(xhr.readyState == 4)
		{ 
			//alert(xhr.responseText);
		}
		//alert(xhr.readyState); 
	} 
	xhr.send(null);
	window.location.reload();
}

function toutCocher()
{
	// On recupere la liste des groupes
	listeCheckboxGroupes = document.getElementsByName('checkboxGroupes');
	var tableIdGroupesCoches = new Array();
	var tableIdUtilisateursCoches = new Array();

	nombreCheckboxGroupesNonCochees = 0;
	nombreCheckboxUtilisateursNonCochees = 0;

	for (var i = 0; i < listeCheckboxGroupes.length; i++)
	{
		if (document.getElementById('toutCocher').checked == true)
		{
			listeCheckboxGroupes[i].checked = true;
		}
		else
		{
			listeCheckboxGroupes[i].checked = false;
		}
	}
	listeIdGroupesCoches = tableIdGroupesCoches.toString();

	// On recupere la liste des utilisateurs
	listeCheckboxUtilisateurs = document.getElementsByName('checkboxUtilisateurs');
	var tableIdUtilisateursCoches = new Array();

	for (var i = 0; i < listeCheckboxUtilisateurs.length; i++)
	{
		if ( document.getElementById('toutCocher').checked == true )
		{
			listeCheckboxUtilisateurs[i].checked = true;
		}
		else
		{
			listeCheckboxUtilisateurs[i].checked = false;			
		}
	}
}
</SCRIPT>

</head>


<body>
<?php
	require("connexion_base.php"); 
	include("importer_configuration.php");

	echo "<input type=hidden id=idEvent value=$idEvent>";


	$sql_event = "SELECT * FROM CalendrierConges WHERE id = $idEvent";
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
		$idUtilisateurEvent		=	$donnees['Utilisateurs_idUtilisateurs'];
		$loginUtilisateurEvent	=	$donnees['Utilisateurs_login'];
		$dateEvent				=	$donnees['date'];
		$periodeEvent			=	$donnees['periode'];
		$typeEvent				=	$donnees['type'];
		$validationEvent		=	$donnees['valide'];
		$descriptionEvent		=	$donnees['description'];
			  						$descriptionEvent = htmlentities($descriptionEvent);
		$bloquant				=	$donnees['bloquant'];
		$commentaire 			=	$donnees['commentaire'];
		$groupesConcernes		= 	$donnees['id_groupes_concernes'];
		$utilisateursConcernes	= 	$donnees['id_utilisateurs_concernes'];
		$dateCreation			=	$donnees['date_creation'];
		$indisponible 			=	$donnees['indisponible'];

		$dateLisible			=	date('d/m/Y', strtotime($dateEvent));
		$jourSemaine			= 	date('D', strtotime($dateEvent));
		$utilisateurEventMaj	=	ucfirst($loginUtilisateurEvent);
		$typeEventMaj			=	ucfirst($typeEvent);

		$tableUtilisateurs =  explode ("," , $utilisateursConcernes);
		$tableGroupes =  explode ("," , $groupesConcernes);
	}
	$reponse_event->closeCursor();

	if ($jourSemaine == "Mon")
	{
		$jourSemaine = "Lundi";
	}
	if ($jourSemaine == "Tue")
	{
		$jourSemaine = "Mardi";
	}
	if ($jourSemaine == "Wed")
	{
		$jourSemaine = "Mercredi";
	}
	if ($jourSemaine == "Thu")
	{
		$jourSemaine = "Jeudi";
	}
	if ($jourSemaine == "Fri")
	{
		$jourSemaine = "Vendredi";
	}
	if ($jourSemaine == "Sat")
	{
		$jourSemaine = "Samedi";
	}
	if ($jourSemaine == "Sun")
	{
		$jourSemaine = "Dimanche";
	}
?>	
<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id=tableGlobale>
<tr ><td></td><td id="tableGlobale">

<!-- Table permettant les bords à 3% -->
<table border=0 width=100%>
<tr>
<td width=3%>
	<?php 
		$sql_couleur = "SELECT couleur FROM Utilisateurs WHERE idUtilisateurs = $idUtilisateurEvent";
		//echo "$sql_couleur";
		try
		{
			$reponse_couleur = $bdd->query($sql_couleur) or die('Erreur SQL !<br>' .$sql_couleur. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		while ($donnees = $reponse_couleur->fetch())
		{
			$couleurEvent		=	$donnees['couleur'];
		}
		$reponse_couleur->closeCursor();

		echo "<input type=hidden id=idEvent value='$idEvent'>"; 
		echo "<input type=hidden id=idUtilisateurEvent value='$idUtilisateurEvent'>"; 
		echo "<input type=hidden id=loginUtilisateurEvent value='$loginUtilisateurEvent'>"; 
		echo "<input type=hidden id=couleurEvent value='$couleurEvent'>"; 
		echo "<input type=hidden id=dateEvent value='$dateEvent'>"; 
		echo "<input type=hidden id=periodeEvent value='$periodeEvent'>"; 
		echo "<input type=hidden id=typeEvent value='$typeEvent'>"; 
		echo "<input type=hidden id=validationEvent value='$validationEvent'>"; 
		echo "<input type=hidden id=descriptionEventHidden value='$descriptionEvent'>"; 
		echo "<input type=hidden id=bloquant value='$bloquant'>"; 
		echo "<input type=hidden id=indisponible value='$indisponible'>"; 



		echo "<input type=hidden id=dateLisible value='$dateLisible'>"; 
		echo "<input type=hidden id=jourSemaine value='$jourSemaine'>"; 

		echo "<input type=hidden value='$utilisateursConcernes'";
		echo "<input type=hidden value='$groupesConcernes'";


	?>


</td>
<td>
	<table width=100%>
		<tr>
			<th id="titreTableau" align=center>
				<?php
					echo "$typeEventMaj: $descriptionEvent de $utilisateurEventMaj le $jourSemaine $dateLisible" 
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
						<th id="ongletVertPropriete">Propriétés</th>
						<td></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr>
						<td width=3%>
			  			</td>
						
						<td>
							<table border=0 width=95%>
								<tr>
									<td></td>
								</tr>
								<tr>
									<td align=left> Login de l'utilisateurs ayant soumis l'évènement: <b><?php echo $utilisateurEventMaj ?></b>
									</td>
								</tr>
								<tr>
									<td align=left> Date de l'évènement: <b><?php echo "$jourSemaine $dateLisible"; ?></b>
									</td>
								</tr>
								<tr>
									<td align=left> 
										<?php
									  		if ( $periodeEvent == "JO" )
									  		{
									  			$periodeEvent = "Journée entière";
									  		}
									  		if ( $periodeEvent == "MA" )
									  		{
									  			$periodeEvent = "Matin";
									  		}
									  		if ( $periodeEvent == "AM" )
									  		{
									  			$periodeEvent = "Après midi";
									  		}
									  	echo "Période de l'évènement: <b>$periodeEvent</b>";
									  	?>
									</td>
								</tr>
								<tr>
						  			<td>
						  			</td>
						  		</tr>
						  	</table>
						</td>
					</tr>
				</table>
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
						<th id="ongletBleuPropriete">Modifier</th>
						<td></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsBleu">
			  		<tr>
					<tr>
						<td>
						</td>
					</tr>					  			
			  			<td width=3%>
			  			</td>

			  			<td>
			  				<table width="100%" border="0">
			  					<tr>

					  				<?php

					  					if ( $typeEvent == "astreinte" )
					  					{	
					  						echo "<td align=left width=32%> Personne d'astreinte:</td>";

					  						echo "<td>";
					  						$reponse_utilisateurs = $bdd->query('SELECT * FROM Utilisateurs WHERE active = \'1\' ORDER BY prenom ');

											echo "<select name=astreinte id=astreinte onClick=javascript:conge_coche(this)>";
											while ($donnees = $reponse_utilisateurs->fetch())
											{
												$nom=$donnees['nom'];
												$prenom=$donnees['prenom'];
												$id=$donnees['idUtilisateurs'];
												if ( $descriptionEvent == $id )
												{
													echo 	"<option value='$id' selected=selected>$prenom $nom</option>";
												}
												else
												{
													echo 	"<option value='$id'>$prenom $nom</option>";
												}
											
											}
											$reponse_utilisateurs->closeCursor(); // Termine le traitement de la requête
											echo "</select></td>";
			  					
					  						echo "<td align=right width=32%></td>";
					  					}
					  					if ( $typeEvent == "event" )
					  					{
					  						echo "<td align=left width=32%> Type d'évènement:</td>";
					  						echo "<td align=left width=32%><input type='text' id=description_evenement value=\"$descriptionEvent\" /></td>";
					  						echo "<td align=right width=32%></td>";
					  					}
					  					if ( $typeEvent == "absence")
					  					{
					  						echo "<td align=left width=32%> Type d'absence: </td>";


											// Je mets dans un champ hidden la valeur de l'ID axe1 correspondant au code comptable 99 = "congés et autres absences"
											echo '<input type="hidden" id="id_axe1_code_99" value="' . $idAxe1Code99 . '">';
											echo '<input type="hidden" id="id_axe2_code_9900" value="' . $idAxe2Code9900 . '">';

											$reponse_absence = $bdd->query('SELECT * FROM Axe2 WHERE codeAxe2 like "99%%" ORDER BY nomAxe2 ');
					  						echo "<td align=left width=32%>";

											echo "<select name=type_absence id=type_absence onClick=javascript:conge_coche(this)> ";
											echo "<option value=non_choisi>Choisir le type d'absence</option>";		
											while ($donnees = $reponse_absence->fetch())
											{
												$type_absence=$donnees['nomAxe2'];
												$id_absence=$donnees['idAxe2'];
											
												if ($type_absence == $descriptionEvent)
												{
													echo 	"<option value='$type_absence' selected=selected> $type_absence </option>";
												}
												else
												{
													echo 	"<option value='$type_absence'> $type_absence </option>";
												}
											}
											$reponse_absence->closeCursor(); // Termine le traitement de la requête
											echo "</select>";
											echo "</td>";

					  						echo "<td align=right width=32%></td>";
					  					}
					  				?>
			  						<td width=3%></td>
			  					</tr>

			  							<?php
					  					if ( $typeEvent == "event" )
					  					{			
					  							$checked = "";
					  							if ( $indisponible == 1 )
					  							{
					  								$checked = "checked";
					  							}
					  						echo "
							  				<tr>
								  				<td> </td>

						  						<td align='center'> <br />Cet évènement rend indisponible : </td>

						  						<td> <br /><input type='checkbox' onclick='' value='check_indisponible' id='check_indisponible' name='check_indisponible' $checked> </td>

						  						<td> </td>
			  								</tr>
					  						";


					  						if ( $admin == 1 )
					  						{
					  							$checked = "";
					  							if ( $bloquant == 1 )
					  							{
					  								$checked = "checked";
					  							}
					  							echo "
									  				<tr>
										  				<td> </td>

								  						<td align='center'> <br />Cet évènement est bloquant: </td>

								  						<td> <br /><input type='checkbox' onclick='' value='check_bloquant' id='check_bloquant' name='check_bloquant' $checked> </td>

								  						<td> </td>
					  								</tr>
							  					";
					  						}
					  						else echo "<input type=hidden id='check_bloquant'>";
								
					  					}

			  							?>

			  					<tr>
			  						<td align='right' colspan=3>
			  							<?php echo "<input type='button' value='Modifier' onClick='modifier_event(\"$typeEvent\");'>" ?>
			  						</td>
			  						<td>
			  						</td>
			  					</tr>




			  				</table>
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
						<th id="ongletVertPropriete">Infos</th>
						<td></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr>
						<td width=3%>
			  			</td>
						
						<td>
							<table border=0 width=100%>
								<tr>
						  			<td width=68%>
						  				<br />Informations complémentaires:
						  			</td>
						  			<td>
						  			</td>
						  		</tr>								
						  		<tr>
						  			<td>
						  				<textarea rows="6" cols="65" id="commentaire"><?php echo $commentaire ?></textarea>
						  			</td>
						  			<td>
						  			</td>
						  		</tr>
						  		<tr>
						  			<td >
						  			</td>
						  			<td align=right width=32%>
						  				<input type='button' value='Enregistrer' onClick='modifier_event("commentaire");'>
						  				<br />
						  				<br />
						  			</td>
						  		</tr>
						  	</table>
						</td>
					<td width=3%></td>						  	
					</tr>
				</table>
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
						<th id="ongletBleuUtilisateurs">Utilisateurs</th>
						<td></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsBleu">
					<tr>
						<td width=3%>
			  			</td>
						
						<td>
							<table border=0 width=100%>
								<tr>
						  			<td width=30% id="titreTableau">
						  				<br /><b>Groupes concernés:</b>
						  			</td>
						  			<td width=30% id="titreTableau">
						  				<br /><b>Utilisateurs concernés</b>
						  			</td>
						  			<td>
						  			</td>
						  		</tr>								
						  		<tr>
						  			<td>
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
												if ( $groupesConcernes == "ALL" )
												{
													$checked = "checked";
												}
												echo "<input type='checkbox' name='checkboxGroupes' id='checkbox_idGroupe_$idGroupeBDD' value='$idGroupeBDD' $checked><label for=checkbox_idGroupe_$idGroupeBDD> $nomGroupe </label> <br />";
											}
											$reponse_event->closeCursor();
										?>
									</td>
						  			<td>										
						  			<?php
										$sql_event = "SELECT * FROM Utilisateurs WHERE active = 1 order by nom";
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
											$idBase	= 	$donnees['idUtilisateurs'];
											$nom			=	$donnees['nom'];
											$prenom 	= $donnees['prenom'];
											
$checked = "";
											foreach ($tableUtilisateurs as $idUtilisateur) 
											{															
												if ( $idBase == $idUtilisateur )
												{
													$checked = "checked";
												}
											}
											if ( $utilisateursConcernes == "ALL" )
											{
												$checked = "checked";
											}
											echo "<input type='checkbox' name='checkboxUtilisateurs' id='checkbox_idUtilisateur_$idBase' value='$idBase' $checked><label for=checkbox_idUtilisateur_$idBase> $prenom $nom </label> <br />";										}
										$reponse_event->closeCursor();
									?>
						  			</td>
						  			<td align="center">
						  				<input type="checkbox" name="toutCocher" id="toutCocher" onClick="toutCocher()"><label for="toutCocher"> Tout cocher / décocher</label>
						  			</td>
						  		</tr>
						  		<tr>
						  			<td >
						  			</td>
						  			<td>
						  			</td>	
						  			<td align=right width=32%>
						  				<input type='button' value='modifier' onClick='modifier_utilisateurs();'>
						  				<br />
						  				<br />
						  			</td>
						  		</tr>
						  	</table>
						</td>
					<td width=3%></td>						  	
					</tr>
				</table>
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
						<th id="ongletVertPropriete">Historique</th>
						<td></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
			  		<tr>
					<tr>
						<td>
						</td>
					</tr>					  			
			  			<td width=3%>
			  			</td>

			  			<td>
			  				<?php
			  					$dateCreationLisible			=	date('d/m/Y à H:i:s', strtotime($dateCreation));
								
								// Si l'événement a une date de création, on l'affiche.
								if ( ! strstr($dateCreationLisible, "30/11/-0001") )
								{
			  						echo "Le $dateCreationLisible : <b>Création</b> de l'évènement<br />";
			  					}


								$sql_historique_valid = "	SELECT * FROM Log WHERE Log LIKE \"%UPDATE CalendrierConges SET valide = '_' WHERE id = '$idEvent'%\" OR Log LIKE \"%UPDATE CalendrierConges SET description = '%' WHERE id = '$idEvent' %\" ORDER BY date	";
								//echo "$sql_historique_valid";
								try
								{
									$reponse_historique_valid = $bdd->query($sql_historique_valid) or die('Erreur SQL !<br>' .$sql_historique_valid. '<br>'. mysql_error());
								}
								catch(Exception $e)
								{
									// En cas d'erreur précédemment, on affiche un message et on arrête tout
									die('Erreur : '.$e->getMessage());
								}
								while ($donnees = $reponse_historique_valid->fetch())
								{
									$idLog					=	$donnees['idLog'];
									$idModificateur			=	$donnees['Utilisateurs_idUtilisateurs'];
									$loginModificateur		=	$donnees['Utilisateurs_login'];
									$dateLog				=	$donnees['Date'];
									$typeLog				=	$donnees['Type'];
									$Log					=	$donnees['Log'];

									$dateLisible			=	date('d/m/Y à H:i:s', strtotime($dateLog));
									$jourSemaine			= 	date('D', strtotime($dateLog));
									$ModificateurMaj		=	ucfirst($loginModificateur);

									$etatValidation			=	"";

									if (	strstr($Log, "UPDATE CalendrierConges SET description = '" )	)
									{

										$marqueurDebutDescription = "UPDATE CalendrierConges SET description = ";
										$debutDescription = strpos( $Log, $marqueurDebutDescription ) + strlen( $marqueurDebutDescription );
										$marqueurFinDescription = " WHERE id = '$idEvent'";
										$finDescription = strpos( $Log, $marqueurFinDescription );
										$description = substr( $Log, $debutDescription, $finDescription - $debutDescription ); 

										// Si c'est une astreinte, on prend le nom correspondant à l'id renseigné.
										if ( $typeEvent == "astreinte")
										{
											$sql_utilisateur = "SELECT nom, prenom FROM Utilisateurs WHERE idUtilisateurs = $description";
											//echo "$sql_utilisateur";
											try
											{
												$reponse_sql_utilisateur = $bdd->query($sql_utilisateur) or die('Erreur SQL !<br>' .$sql_utilisateur. '<br>'. mysql_error());
											}
											catch(Exception $e)
											{
												// En cas d'erreur précédemment, on affiche un message et on arrête tout
												die('Erreur : '.$e->getMessage());
											}
											while ($donnees = $reponse_sql_utilisateur->fetch())
											{
												$description = "<b>".ucfirst($donnees['prenom'])." ".ucfirst($donnees['nom'])."</b>";
											}
											$reponse_sql_utilisateur->closeCursor();
										}

										$etatValidation = "Modification de l'évènement en $description";
									}

									if (	strstr($Log, "valide = 'A'" )	)
									{
										$etatValidation = "<b>mis en attente</b>";
									}
									if (	strstr($Log, "valide = 'V'" )	)
									{
										$etatValidation = "<b>validé</b>";
									}
									if (	strstr($Log, "valide = 'R'" )	)
									{
										$etatValidation = "<b>refusé</b>";
									}												


									echo "Le $dateLisible : $etatValidation par $ModificateurMaj<br />";
								}
								$reponse_historique_valid->closeCursor();
			  				?>
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
	<table align=center>
		<tr>
			<td>
				<input type=button value="Supprimer" onClick='supprimer_event();'>
			</td>
		</tr>
	</table>

</table></table>

<!-- Fin de la table permettant les bords à 3% --> 
</td>
<td width=3% id="toto"></td>
</tr></table>
	
<!-- Fin de la table de mise en page globale -->
</td><td></td></tr>
</table>

</body>
</html>
