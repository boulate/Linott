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


function renseigner_action(id_action, date_creation, id_fiche_parent, besoin_action_oui_non, besoin_actions, type_action_CPA, responsable_action, delai_action, date_realisation_action, justificatifs, efficacite, active)
{
	//alert(besoin_actions);
	if (typeof numAction == 'undefined')
	{
		numAction = 1;
	}
	else
	{
		numAction++;
	}

	//alert("numAction: "+numAction);

	listeDiv 	= document.getElementsByTagName('div');
	nombreDiv 		= listeDiv.length;
	totalAction 	= document.getElementById('nombre_actions');
	//alert(totalAction);

	// for (i = 0 ; i < nombreDiv ; i++)
	// {
	// 		if (	(listeDiv[i].id.indexOf("numero_action") != -1) )
	// 		{
	// 			nombreActions++;
	// 		}
	// }
	// alert("nombre d'actions: "+nombreActions);


	if (numAction > 1)
	{
		table = document.getElementById('tableVertActions');
		nouvelleLigne = table.insertRow(-1); 
		nouvelleColonne = nouvelleLigne.insertCell(0);

		nombreActions = numAction-1;
		//alert("debut test");
		var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhr.open("GET", "nouvelle_action.php?nombreActions="+nombreActions, false);
		xhr.onreadystatechange = function() 
		{ 
			if(xhr.readyState == 4)
			{ 
				//alert(xhr.responseText);
				nouvelleColonne.innerHTML +=		xhr.responseText;
				//decouperImporterUtilisateur(xhr);
			}
			//alert(xhr.readyState); 
		} 
		xhr.send(null);
	}

// id_action, date_creation, id_fiche_parent, besoin_action_oui_non, besoin_actions, type_action_CPA, responsable_action, delai_action, date_realisation_action, justificatifs, efficacite, active

	//alert(id_action);
	document.getElementById('id_action_'+numAction).value = id_action;
	document.getElementById('id_fiche_parent_'+numAction).value = id_fiche_parent ;
	if (besoin_action_oui_non == "Oui")
	{
		document.getElementById('besoin_action_oui_'+numAction).checked = true;
	}
	if (besoin_action_oui_non == "Non")
	{
		document.getElementById('besoin_action_non_'+numAction).checked = true ;
	}
	document.getElementById('CPA_'+numAction).value = type_action_CPA;
	document.getElementById('besoin_actions_'+numAction).value = besoin_actions;
	document.getElementById('ID_user_'+numAction).value = responsable_action;
	document.getElementById('delai_action_'+numAction).value = delai_action;
	document.getElementById('date_realisation_action_'+numAction).value = date_realisation_action;
	document.getElementById('efficacite_action_'+numAction).value = efficacite;

}

function ajouter_action()
{

	listeDiv 	= document.getElementsByTagName('div');
	nombreDiv 		= listeDiv.length;
	nombreActions 	= 0;

	for (i = 0 ; i < nombreDiv ; i++)
	{
			if (	(listeDiv[i].id.indexOf("numero_action") != -1) )
			{
				nombreActions++;
			}
	}
	//alert("nombre d'actions: "+nombreActions);

	table = document.getElementById('tableVertActions');
	nouvelleLigne = table.insertRow(-1); 
	nouvelleColonne = nouvelleLigne.insertCell(0);
	//alert(table);
	//alert("debut test");
	var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhr.open("GET", "nouvelle_action.php?nombreActions="+nombreActions+"&idFicheParent="+idFiche, false);
	xhr.onreadystatechange = function() 
	{ 
		if(xhr.readyState == 4)
		{ 
			//alert(xhr.responseText);
			nouvelleColonne.innerHTML +=		xhr.responseText;
			//decouperImporterUtilisateur(xhr);
		}
		//alert(xhr.readyState); 
	} 

	xhr.send(null);	
}

function enregistrer_actions()
{
	listeDiv 		= document.getElementsByTagName('div');
	nombreDiv 		= listeDiv.length;
	nombreActions 	= 0;

	for (i = 0 ; i < nombreDiv ; i++)
	{
		if (	(listeDiv[i].id.indexOf("numero_action") != -1) )
		{
			nombreActions++;
		}
	}

	for (j = 1 ; j <= nombreActions ; j ++)
	{
		numeroAction 		= j;

		idAction 			= document.getElementById('id_action_'+j).value;
		idFicheParent 		= document.getElementById('idFiche').value;

		//alert(idAction);
		//alert(idFicheParent);
		//alert("Stop");

		besoinActionOuiNon = "0";
			if ( document.getElementById('besoin_action_non_'+j).checked == true )
			{
				besoinActionOuiNon = "Non";
			}
		
			if ( document.getElementById('besoin_action_oui_'+j).checked == true )
			{
				besoinActionOuiNon = "Oui";
			}
		CPA 				= document.getElementById('CPA_'+j).value;
		besoinActions 		= document.getElementById('besoin_actions_'+j).value.replace("\'", "\\'");
		//besoinActions 		= document.getElementById('besoin_actions_'+j).value;
		responsableAction 	= document.getElementById('ID_user_'+j).value;
		delaiAction 		= document.getElementById('delai_action_'+j).value;
		dateRealisation 	= document.getElementById('date_realisation_action_'+j).value;
		efficaciteAction 	= document.getElementById('efficacite_action_'+j).value.replace("\'", "\\'");

//		alert(besoinActions);
//		alert("numero d'action: "+numeroAction+", besoinActionOui: "+besoinActionOui+", besoinActionNon: "+besoinActionNon+", CPA: "+CPA+", besoin d'action: "+besoinActions+", responsableAction: "+responsableAction+", delaiAction: "+delaiAction+", dateRealisation: "+dateRealisation+", efficaciteAction: "+efficaciteAction);
	
		//alert("debut test");
		var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhr.open("GET", "creer_action.php?idAction="+idAction+"&idFicheParent="+idFicheParent+"&numeroAction="+numeroAction+"&besoinActionOuiNon="+besoinActionOuiNon+"&CPA="+CPA+"&besoinActions="+besoinActions+"&responsableAction="+responsableAction+"&delaiAction="+delaiAction+"&dateRealisation="+dateRealisation+"&efficaciteAction="+efficaciteAction, false);
		//alert("creer_action.php?numeroAction="+numeroAction+"&besoinActionOuiNon="+besoinActionOuiNon+"&CPA="+CPA+"&besoinActions="+besoinActions+"&responsableAction="+responsableAction+"&delaiAction="+delaiAction+"&dateRealisation="+dateRealisation+"&efficaciteAction="+efficaciteAction);
		xhr.onreadystatechange = function() 
		{ 
			if(xhr.readyState == 4)
			{ 
				//alert(xhr.responseText);
			}
			//alert(xhr.readyState); 
		} 

		xhr.send(null);	
	}
}

function importer_action()
{
	idFiche = document.getElementById('id_fiche_action').value;

	var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhr.open("GET", "importer_action.php?idFiche="+idFiche, false);
	xhr.onreadystatechange = function() 
	{ 
		if(xhr.readyState == 4)
		{ 
			//alert(xhr.responseText);
			decouperImporterAction(xhr);
		}
		//alert(xhr.readyState); 
	} 

	xhr.send(null);	
}

function decouperImporterAction(xhr)
{
	// On transforme l'objet recu en chaine de caractères..
	xhrText=xhr.responseText;
	//alert(xhrText);

	// On decoupe cette chaine de caractères d'abord pour séparer les différentes actions.
	tableActions=xhrText.split("/ENDOFLINE/");

	// Je mets length-1 car mon /ENDOFLINE/ sur la derniere ligne fait croire à javascript qu'une derniere ligne va suivre.
	for ( i=0 ; i < tableActions.length-1 ; i++ )
	{
		donneesAction = tableActions[i].split(";;;");

		id_action 				= donneesAction[0];
		date_creation 			= donneesAction[1];
		id_fiche_parent 		= donneesAction[2];
		besoin_action_oui_non 	= donneesAction[3];
		besoin_actions 			= donneesAction[4];
		type_action_CPA 		= donneesAction[5];
		responsable_action 		= donneesAction[6];
		delai_action 			= donneesAction[7];
		date_realisation_action = donneesAction[8];
		justificatifs 			= donneesAction[9];
		efficacite 				= donneesAction[10];
		active 					= donneesAction[11];


		//alert(id_action+date_creation+id_fiche_parent+besoin_action_oui_non+besoin_actions+type_action_CPA+responsable_action+delai_action+date_realisation_action+justificatifs+efficacite+active);
		renseigner_action(id_action, date_creation, id_fiche_parent, besoin_action_oui_non, besoin_actions, type_action_CPA, responsable_action, delai_action, date_realisation_action, justificatifs, efficacite, active);

	}

}

</script>

<html>
<body>

<input type=hidden id="id_fiche_action" value="<?php echo "$id_fiche" ?>">
<table border=0 width=100% id='tableOngletsVert'>
	<tr>
		<td width=15%>
		<p>
			<?php
			// Declaration des variables non_conformite
			$aucune_non_conformite="Aucune non conformite";
			$non_conformite="Non conformite";
			$non_conformite_majeure="Non conformite majeure";
				// si le "en cours" = "celui en base", alors selected="selected"
					if ($aucune_non_conformite == $incidence_qualite) 
					{
						$selected_aucune = "checked=\"checked\"";
					}
					elseif ($non_conformite == $incidence_qualite) 
					{
						$selected_non_conformite = "checked=\"checked\"";
					}
					elseif ($non_conformite_majeure == $incidence_qualite) 
					{
						$selected_majeure = "checked=\"checked\"";
					}
					else 
					{
						$selected = "";
					}
			?>
			<input type="radio" name="incidence_qualite" value="<?php echo $aucune_non_conformite ?>" <?php echo $selected_aucune ?> id="1" /> <label for="1"><?php echo $aucune_non_conformite ?></label>
			<br><input type="radio" name="incidence_qualite" value="<?php echo $non_conformite ?>" <?php echo $selected_non_conformite ?> id="2" /> <label for="2"><?php echo $non_conformite ?></label>
			<br><input type="radio" name="incidence_qualite" value="<?php echo $non_conformite_majeure ?>" <?php echo $selected_majeure ?> id="3" /> <label for="3"><?php echo $non_conformite_majeure ?></label>
			<br></p>
		</td>
		
		<td width=30%>
		Commentaire :<br />
		<textarea name="commentaire_indice_qualite" rows=3 cols=40><?php echo $commentaire_indice_qualite ?></textarea>
		</td>

		<td width=15%>
		
		</td>

		<td width=40%>
		<p>
			Conséquences sur produit/service :
			<?php
			// Declaration des variables action_produit
			$action_produit_oui="Oui";
			$action_produit_non="Non";
					// si le "en cours" = "celui en base", alors selected="selected"
						if ($action_produit_oui == $action_sur_produit) 
						{
							$selected_oui = "checked=\"checked\"";
							$selected_non = "";							
						}
						elseif ($action_produit_non == $action_sur_produit) 
						{
							$selected_oui = "";
							$selected_non = "checked=\"checked\"";
						}
						else 
						{
							$selected_oui = "";
							$selected_non = "";							
						}
			?>
			<br><input type="radio" name="action_sur_produit" value="Non" <?php echo $selected_non ?> id="non" /> <label for="<?php echo $action_produit_non ?>"><?php echo $action_produit_non ?></label>
			<br><input type="radio" name="action_sur_produit" value="Oui" <?php echo $selected_oui ?> id="oui" /> <label for="<?php echo $action_produit_oui ?>"><?php echo $action_produit_oui ?></label>
			<br></p>
		</td>

		<td>

		</td>
	</tr>
	
	<tr>
		<td><br /></td>
	</tr>
</table>
<!-- <br /> -->
<table border=0 width=100% id='tableVert'>
	<tr>
		<td width=33% align=center>
		Conséquences sur le produits, le service ou les travaux :
		</td>
		<td width=33% align=center>
		Conséquences sur la satisfaction client :
		</td>
		<td width=33% align=center>
		Conséquences sur la diffusion du rapport :
		</td>
	</tr>
	<tr>
		<td align=center>
			<!-- <textarea name="besoin_actions" rows=3 cols=50></textarea> -->
			<textarea name="consequences_produit" rows=3 cols=35><?php echo $consequences_produit ?></textarea>
		</td>
		<td align=center>
			<!-- <textarea name="besoin_actions" rows=3 cols=50></textarea> -->
			<textarea name="consequences_satisfaction_client" rows=3 cols=35><?php echo $consequences_satisfaction_client ?></textarea>
		</td>
		<td align=center>
			<!-- <textarea name="besoin_actions" rows=3 cols=50></textarea> -->
			<textarea name="consequences_diffusion" rows=3 cols=35><?php echo $consequences_diffusion ?></textarea>
		</td>
	</tr>
	<tr>
		<td><br /></td>
	</tr>
</table>
<br />
<table border=0 width=100% id='tableVert'>
	<tr>			
		<td width=3%> 
		</td>

		<td width="27%">
			<table border=0 width=100% >
				<tr>
					<td align=left colspan=3>
						Faut il informer le responsable de l'intervention?<br />
					</td>
				</tr>
				<tr>

					<td>
						<?php

							// Declaration des variables action_produit
							$information_responsable_non="Non";
							$information_responsable_oui="Oui";

							// si le "en cours" = "celui en base", alors selected="selected"
							if ($information_responsable_oui == $information_responsable) 
							{
								$selected_oui = "checked=\"checked\"";
								$selected_non = "";

							}
							elseif ($information_responsable_non == $information_responsable) 
							{
								$selected_oui = "";
								$selected_non = "checked=\"checked\"";
							}
							else 
							{
								$selected_oui = "";
								$selected_non = "";
							}

						?>
						<input type="radio" name="information_responsable" value="Non" <?php echo $selected_non ?> id="non" /> <label for="<?php echo $information_responsable_non ?>"><?php echo $information_responsable_non ?></label>
						<br><input type="radio" name="information_responsable" value="Oui" <?php echo $selected_oui ?> id="oui" /> <label for="<?php echo $information_responsable_oui ?>"><?php echo $information_responsable_oui ?></label>
						
					</td>
					<td>
						<br />Date d'information : <input type="text" name="date_information_responsable" value="<?php echo $date_information_responsable ?>" />
					</td>
				</tr>
			</table>	
		</td>

		<td width="3%">
			<table border=0 width=100% >
				<tr>
					<td>
					</td>
				</tr>
			</table>	
		</td>

		<td width="27%">
			<table border=0 width=100% >
				<tr>
					<td align=left colspan=3>
						Faut il informer le client?<br />
					</td>
				</tr>
				<tr>

					<td>
						<?php
							// Declaration des variables action_produit
							$information_client_non="Non";
							$information_client_oui="Oui";


							// si le "en cours" = "celui en base", alors selected="selected"
							if ($information_client_oui == $information_client) 
							{
								$selected_oui = "checked=\"checked\"";
								$selected_non = "";

							}
							elseif ($information_client_non == $information_client) 
							{
								$selected_oui = "";
								$selected_non = "checked=\"checked\"";
							}
							else 
							{
								$selected_oui = "";
								$selected_non = "";
							}
						?>
						<input type="radio" name="information_client" value="Non" id="non" <?php echo $selected_non ?> /> <label for="<?php echo $information_client_non ?>"><?php echo $information_client_non ?></label>
						<br><input type="radio" name="information_client" value="Oui" id="oui" <?php echo $selected_oui ?> /> <label for="<?php echo $information_client_oui ?>"><?php echo $information_client_oui ?></label>
						<br>
					</td>
					<td>
						<br />Date d'information : <input type="text" name="date_information_client" value="<?php echo $date_information_client ?>" />
					</td>
				</tr>
			</table>	
		</td>

		<td width=3%> 
		</td>

		<td width="34%">
			<table border=0 width=100% >
				<tr>
					<td align=left colspan=3>
						La poursuite des travaux est elle autorisée?<br />
					</td>
				</tr>
				<tr>

					<td>
						<?php
						// Declaration des variables action_produit
							$poursuite_travaux_non="Non";
							$poursuite_travaux_oui="Oui";

													// si le "en cours" = "celui en base", alors selected="selected"
							if ($poursuite_travaux_oui == $poursuite_travaux) 
							{
								$selected_oui = "checked=\"checked\"";
								$selected_non = "";

							}
							elseif ($poursuite_travaux_non == $poursuite_travaux) 
							{
								$selected_oui = "";
								$selected_non = "checked=\"checked\"";
							}
							else 
							{
								$selected_oui = "";
								$selected_non = "";
							}
						?>
						<input type="radio" name="poursuite_travaux" value="Non" id="non" <?php echo $selected_non ?> /> <label for="<?php echo $poursuite_travaux_non ?>"><?php echo $poursuite_travaux_non ?></label>
						<br><input type="radio" name="poursuite_travaux" value="Oui" id="oui"  <?php echo $selected_oui ?> /> <label for="<?php echo $poursuite_travaux_oui ?>"><?php echo $poursuite_travaux_oui ?></label>
						<br>
					</td>
					<td>
						<br />Autorisation donnée par : 

					<?php			
						$reponse_utilisateurs = $bdd->query('SELECT * FROM Linott.Utilisateurs ORDER BY prenom');
					?>

					<label for="autorite_poursuite_travaux"></label>
					<select name="autorite_poursuite_travaux" id="id_autorite_poursuite_travaux">			
					<?php			
					while ($donnees = $reponse_utilisateurs->fetch())
					{
					$nom_utilisateur=$donnees['prenom'] . " " . $donnees['nom'];
					$id_utilisateur=$donnees['idUtilisateurs'];
					
						if ($nom_utilisateur != $responsable_action)
						{
		           			echo "<option value='$nom_utilisateur'> $nom_utilisateur </option>";
						}
						if ($nom_utilisateur == $responsable_action)
						{
		           			echo "<option value='$nom_utilisateur' selected> $nom_utilisateur </option>";
						}
					}
					$reponse_utilisateurs->closeCursor(); // Termine le traitement de la requête
					?>
					</select>



					</td>
				</tr>
			</table>	
		</td>


		<td width=3%> 
		</td>
	</tr>
</table>
<br />

<?php
	$requete_nombre_actions = "SELECT count(id) as nbr from actions WHERE id_fiche_parent = $id_fiche";
	//echo "$requete_nombre_actions";
	try
	{
		$reponse_nombre_actions = $bdd->query($requete_nombre_actions) or die('Erreur SQL !<br>' .$requete_nombre_actions. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}	
	// Si un axe3 ressort du test ci dessus, c'est qu'il n'y a aucune modif à faire car aucune différence entre la page en cours et la base.
	while ($donnees = $reponse_nombre_actions->fetch())
  	{
		$nbr_actions = $donnees['nbr'];
	}
	$reponse_nombre_actions->closeCursor();

	echo "<input type=hidden id=nombre_actions value=$nbr_actions>";


	// for ( $i = 1; $i <= $nbr_actions ; $i++)
	// {
	// 	echo $i;
	// }
?>


<table border=0 width="100%" id="tableVertActions">
	<tr>
		<td>
			<table border=0 width=100% id='tableAction1'>
				<tr>
					<th colspan=7 align=left >
						<div id="numero_action">Action n°1</div>
						<input type="hidden" id="id_action_1" value="">
						<input type="hidden" id="id_fiche_parent_1" value="">
					</th>
				</tr>
				<tr>
					<td width=12% align=center>
					Besoin d'action :
					</td>
					<td width=10% align=center>
					Type d'action :
					</td>
					<td width=20% align=center>
					Description de l'action :
					</td>
					<td width=10% align=center>
					Responsable :
					</td>
					<td width=12% align=center>
					Délai prévi. :
					</td>
					<td width=11% align=center>
					Date réalisation :<br />
					</td>
					<td width=25% align=center>
					Verification efficacite :<br />
					</td>
					<!--
					<td width=25%>
					Realisation
					</td>
					-->
				</tr>
				<tr>
					<td>
						<?php
						// Declaration des variables action_produit
						$besoin_action_oui="Oui";
						$besoin_action_non="Non";
						?>
						<input type="radio" name="besoin_action_oui_non_1" value="Non" id="besoin_action_non_1" /> <label for="<?php echo $besoin_action_non ?>"><?php echo $besoin_action_non ?></label>
						<br><input type="radio" name="besoin_action_oui_non_1" value="Oui" id="besoin_action_oui_1" /> <label for="<?php echo $besoin_action_oui ?>"><?php echo $besoin_action_oui ?></label>
						<br>
					</td>

					<td align=center>
					<?php
					// Declaration des variables CPA
					$cpa_vide="";
					$cpa_c="Correctif";
					$cpa_p="Preventif";
					$cpa_a="Amelioration";
					?>

						<select name="CPA" id="CPA_1">
							<option value="<?php echo $cpa_vide ?>"></option>
							<option value="<?php echo $cpa_c ?>"><?php echo $cpa_c ?></option>
							<option value="<?php echo $cpa_p ?>"><?php echo $cpa_p ?></option>
							<option value="<?php echo $cpa_a ?>"><?php echo $cpa_a ?></option>
						</select>
					</td>

					<td align=center>
						<!-- <textarea name="besoin_actions" rows=3 cols=50></textarea> -->
						<textarea name="besoin_actions" id="besoin_actions_1" rows=3 cols=25></textarea>
					</td>

					<td align=center>

								<!----------------------------- Ancienne version ---------------------------------->
								<!-- Gestion des Responsables -->
			<!-- 					<?php			
								$reponse_utilisateurs = $bdd->query('SELECT * FROM utilisateurs ORDER BY username');

								?>
								<label for="responsable"></label>
								<select name="responsable" id="ID_user">			
								<?php			
								while ($donnees = $reponse_utilisateurs->fetch())
								{
								$nom_utilisateur=$donnees['username'];
								$id_utilisateur=$donnees['ID_users'];
								?>
					           			<option value="<?php echo $nom_utilisateur ?>"><?php echo $nom_utilisateur ?></option>	
								<?php
								}
								$reponse_utilisateurs->closeCursor(); // Termine le traitement de la requête
								?>
								</select> 
			-->



								<!----------------------------- Nouvelle version se connectant à Compta ---------------------------------->
								<!-- Gestion des Responsables -->
								<?php			
								$reponse_utilisateurs = $bdd->query('SELECT * FROM Linott.Utilisateurs ORDER BY prenom');

								?>
								<label for="responsable_action"></label>
								<select name="responsable_action" id="ID_user_1">			
								<?php			
								while ($donnees = $reponse_utilisateurs->fetch())
								{
								$nom_utilisateur=$donnees['prenom'] . " " . $donnees['nom'];
								$id_utilisateur=$donnees['idUtilisateurs'];
								
									if ($idSession != $id_utilisateur)
									{
					           			echo "<option value='$nom_utilisateur'>$nom_utilisateur</option>";
									}
									if ($idSession == $id_utilisateur)
									{
					           			echo "<option value='$nom_utilisateur' selected>$nom_utilisateur</option>";
									}
								}
								$reponse_utilisateurs->closeCursor(); // Termine le traitement de la requête
								?>
								</select>
					</td>

					<td align=center>
						<input type="text" name="delai_action" id="delai_action_1"/>
					</td>

					<td align=center>
						<input type="text" name="date_realisation_action" id="date_realisation_action_1"/>
					</td>

					<td align=center>
			<!--		<textarea name="efficacite_action" rows=3 cols=50></textarea> -->
						<textarea name="efficacite_action" id="efficacite_action_1" rows=3 cols=25></textarea>
					</td>
				</tr>
			</table>

		</td>
	</tr>
</table>


<table align=left>
	<tr>
		<td>
			<input type=button value="nouvelle action" onclick="ajouter_action();">	
		</td>
	</tr>
</table>









<br />
<br />
<br />
<table border=0 width=100% id='tableVert'>
	<tr>			
		<!--
		<td>
			Elements justificatifs :<br />
			<textarea name="elements_justificatifs" rows=3 cols=50></textarea>
		</td>
		-->
		<td align=center>
			Cloture le:<br>
			<input type="text" name="date_cloture" value="<?php echo $date_cloture ?>"/>
		</td>
		<td align=center>	
			Visa du responsable :<br />
			<input type="text" name="visa_responsable" value="<?php echo $visa_responsable ?>"/>
		</td>
		<td align=center>	
			Visa de la direction :<br />
			<input type="text" name="visa_direction"/ value="<?php echo $visa_direction ?>">*<br />
			* Requis en cas de<br /> 
			non conformite majeure.
		</td>
	</tr>		

</table>


<?php 
// PLUS UTILE SI JE PASSE PAR IMPORTER_ACTION.PHP
	$requete_insert_actions = "SELECT * from actions WHERE id_fiche_parent = $id_fiche";
	//echo "$requete_insert_actions";
	try
	{
		$reponse_insert_actions = $bdd->query($requete_insert_actions) or die('Erreur SQL !<br>' .$requete_insert_actions. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}	
	// Si un axe3 ressort du test ci dessus, c'est qu'il n'y a aucune modif à faire car aucune différence entre la page en cours et la base.
	while ($donnees = $reponse_insert_actions->fetch())
  	{
		$id_action 					= $donnees['id'];
		$date_creation 				= $donnees['date_creation'];
		$id_fiche_parent 			= $donnees['id_fiche_parent'];
		$besoin_action_oui_non 		= $donnees['besoin_action_oui_non'];
		$besoin_actions 			= htmlspecialchars($donnees['besoin_actions']);
		$type_action_CPA 			= $donnees['type_action_CPA'];
		$responsable_action 		= $donnees['responsable_action'];
		$delai_action 				= $donnees['delai_action'];
		$date_realisation_action 	= $donnees['date_realisation_action'];
		$justificatifs 				= str_replace("'", "\'", $donnees['justificatifs']);
		$efficacite 				= str_replace("'", "\'", $donnees['efficacite']);
		$active 					= $donnees['active'];
		
		//echo "$id_action, $date_creation, $id_fiche_parent, $besoin_action_oui_non, $besoin_actions, $type_action_CPA, $responsable_action, $delai_action, $date_realisation_action, $justificatifs, $efficacite, $active";

		//echo "<script>renseigner_action('$id_action', '$date_creation', '$id_fiche_parent', '$besoin_action_oui_non', '$besoin_actions', '$type_action_CPA', '$responsable_action', '$delai_action', '$date_realisation_action', '$justificatifs', '$efficacite', '$active')</script>";

	}
	$reponse_insert_actions->closeCursor();

?>


</body>
</html>

