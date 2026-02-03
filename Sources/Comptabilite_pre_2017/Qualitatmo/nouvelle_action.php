<?php session_start(); ?>


<?php

function generer_nouvelle_action()
{

include("connexion_base.php");
		$loginSession	=	$_SESSION['login'];
		$prenomSession	=	$_SESSION['prenom'];
		$nomSession		=	$_SESSION['nom'];
		$idSession		=	$_SESSION['idUtilisateurs'];

$nombreActions = $_GET['nombreActions'];
$numeroAction = $nombreActions + 1;
$nomProchaineTable = "tableVertAction".($numeroAction + 1);

	echo "
		<table>
			<tr>
				<th colspan=7 align=left >
					<div id='numero_action'>Action n°$numeroAction</div>
					<input type='hidden' id='id_action_$numeroAction' value=''>
					<input type='hidden' id='id_fiche_parent_$numeroAction' value=''>
				</th>
			</tr>

			<tr>
				<td width=12% align=left>
				Besoin d'action :
				</td>
				<td width=10% align=center>
				Type d'action :
				</td>
				<td width=20% align=center	>
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

			</tr>
			<tr>
				<td>";
								// Declaration des variables action_produit
								$besoin_action_oui='Oui';
								$besoin_action_non='Non';

				echo "<input type='radio' name='besoin_action_oui_non_$numeroAction' value='Non' id='besoin_action_non_$numeroAction' /> <label for=$besoin_action_non>$besoin_action_non</label>
					<br><input type='radio' name='besoin_action_oui_non_$numeroAction' value='Oui' id='besoin_action_oui_$numeroAction' /> <label for=$besoin_action_oui>$besoin_action_oui</label>
					<br>
				</td>

				<td align=center>";

				// Declaration des variables CPA
				$cpa_vide="";
				$cpa_c="Correctif";
				$cpa_p="Preventif";
				$cpa_a="Amelioration";

	echo "			<select name='CPA' id='CPA_$numeroAction'>
						<option value='$cpa_vide'></option>
						<option value='$cpa_c'>$cpa_c</option>
						<option value='$cpa_p'>$cpa_p</option>
						<option value='$cpa_a'>$cpa_a</option>
					</select>
				</td>

				<td align=center>
					<textarea name='besoin_actions' id='besoin_actions_$numeroAction' rows=3 cols=25></textarea>
				</td>

				<td align=center>";

				// <!----------------------------- Nouvelle version se connectant à Compta ---------------------------------->
				// <!-- Gestion des Responsables -->
				$reponse_utilisateurs = $bdd->query('SELECT * FROM Linott.Utilisateurs ORDER BY prenom');

				echo "<label for='responsable_action'></label>
				<select name='responsable_action' id='ID_user_$numeroAction'>	";		
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

	echo "						</select>
				</td>

				<td align=center>
					<input type='text' name='delai_action' id='delai_action_$numeroAction' />
				</td>

				<td align=center>
					<input type='text' name='date_realisation_action' id='date_realisation_action_$numeroAction' />
				</td>

				<td align=center>
					<textarea name='efficacite_action' id='efficacite_action_$numeroAction' rows=3 cols=25></textarea>
				</td>
			</tr>
		</table>
	";

}

generer_nouvelle_action(); 

?>