<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

<head>
	<title>Gestion des fiches de conformite</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />



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

function enregistrer_actions()
{
//alert("toto");

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
		besoinActions 		= document.getElementById('besoin_actions_'+j).value;
		responsableAction 	= document.getElementById('ID_user_'+j).value;
		delaiAction 		= document.getElementById('delai_action_'+j).value;
		dateRealisation 	= document.getElementById('date_realisation_action_'+j).value;
		efficaciteAction 	= document.getElementById('efficacite_action_'+j).value;

//		alert("numero d'action: "+numeroAction+", besoinActionOui: "+besoinActionOui+", besoinActionNon: "+besoinActionNon+", CPA: "+CPA+", besoin d'action: "+besoinActions+", responsableAction: "+responsableAction+", delaiAction: "+delaiAction+", dateRealisation: "+dateRealisation+", efficaciteAction: "+efficaciteAction);
	
		//alert("debut test");
		var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhr.open("GET", "creer_action.php?numeroAction="+numeroAction+"&besoinActionOuiNon="+besoinActionOuiNon+"&CPA="+CPA+"&besoinActions="+besoinActions+"&responsableAction="+responsableAction+"&delaiAction="+delaiAction+"&dateRealisation="+dateRealisation+"&efficaciteAction="+efficaciteAction, false);
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

</script>

</head>

<body>
<table border=0 width=100% id='tableOngletsVert'>
	<tr>
		<td width=15%>
			<p>
			<?php
				// Declaration des variables non_conformite
				$aucune_non_conformite="Aucune non conformite";
				$non_conformite="Non conformite";
				$non_conformite_majeure="Non conformite majeure";
			?>
			<input type="radio" name="incidence_qualite" value="<?php echo $aucune_non_conformite ?>" id="1" /> <label for="1"><?php echo $aucune_non_conformite ?></label>
			<br><input type="radio" name="incidence_qualite" value="<?php echo $non_conformite ?>" id="2" /> <label for="2"><?php echo $non_conformite ?></label>
			<br><input type="radio" name="incidence_qualite" value="<?php echo $non_conformite_majeure ?>" id="3" /> <label for="3"><?php echo $non_conformite_majeure ?></label>
			<br></p>
		</td>
		
		<td width=30% align=center>
			Commentaire :<br />
			<textarea name="commentaire_indice_qualite" rows=3 cols=40></textarea>
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
			?>
			<br><input type="radio" name="action_sur_produit" value="Non" id="non" /> <label for="<?php echo $action_produit_non ?>"><?php echo $action_produit_non ?></label>
			<br><input type="radio" name="action_sur_produit" value="Oui" id="oui" /> <label for="<?php echo $action_produit_oui ?>"><?php echo $action_produit_oui ?></label>
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
			<textarea name="consequences_produit" rows=3 cols=35></textarea>
		</td>
		<td align=center>
			<!-- <textarea name="besoin_actions" rows=3 cols=50></textarea> -->
			<textarea name="consequences_satisfaction_client" rows=3 cols=35></textarea>
		</td>
		<td align=center>
			<!-- <textarea name="besoin_actions" rows=3 cols=50></textarea> -->
			<textarea name="consequences_diffusion" rows=3 cols=35></textarea>
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
						$information_responsable_oui="Oui";
						$information_responsable_non="Non";
						?>
						<input type="radio" name="information_responsable" value="Non" id="non" /> <label for="<?php echo $information_responsable_non ?>"><?php echo $information_responsable_non ?></label>
						<br><input type="radio" name="information_responsable" value="Oui" id="oui" /> <label for="<?php echo $information_responsable_oui ?>"><?php echo $information_responsable_oui ?></label>
						<br>
					</td>
					<td>
						<br />Date d'information : <input type="text" name="date_information_responsable"/>
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
						$information_client_oui="Oui";
						$information_client_non="Non";
						?>
						<input type="radio" name="information_client" value="Non" id="non" /> <label for="<?php echo $information_client_non ?>"><?php echo $information_client_non ?></label>
						<br><input type="radio" name="information_client" value="Oui" id="oui" /> <label for="<?php echo $information_client_oui ?>"><?php echo $information_client_oui ?></label>
						<br>
					</td>
					<td>
						<br />Date d'information : <input type="text" name="date_information_client"/>
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
						$poursuite_travaux_oui="Oui";
						$poursuite_travaux_non="Non";
						?>
						<input type="radio" name="poursuite_travaux" value="Non" id="non" /> <label for="<?php echo $poursuite_travaux_non ?>"><?php echo $poursuite_travaux_non ?></label>
						<br><input type="radio" name="poursuite_travaux" value="Oui" id="oui" /> <label for="<?php echo $poursuite_travaux_oui ?>"><?php echo $poursuite_travaux_oui ?></label>
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
						
							if ($idSession != $id_utilisateur)
							{
			           			echo "<option value='$nom_utilisateur'> $nom_utilisateur </option>";
							}
							if ($idSession == $id_utilisateur)
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
<table border=0 width="100%" id="tableVertActions">
	<tr>
		<td>
			<table border=0 width=100% id='tableAction1'>
				<tr>
					<th colspan=7 align=left >
						<div id="numero_action">Action n°1</div>
						<input type="hidden" id="id_action_1" value="">
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
			Cloturé le :<br>
			<input type="text" name="date_cloture"/>
		</td>
		<td align=center>	
			Visa du responsable :<br />
			<input type="text" name="visa_responsable"/>
		</td>
		<td align=center>	
			Visa de la direction :<br />
			<input type="text" name="visa_direction"/>*<br />
			* Requis en cas de<br /> 
			non conformite majeure.
		</td>
	</tr>	

</table>




</body>
</html>

