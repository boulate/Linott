<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<?php session_start(); ?>

<head>
	<title>Linott: Gestion des fiches actions</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
		<!-- Mon thème -->
	<link rel="stylesheet" href="../style.css" />

	<!-- Integration de jquery calendar http://jqueryui.com/datepicker/ -->
	<link rel="stylesheet" href="../CSS/jquery-ui.css" />
	<script src="../jquery-1.8.3.js"></script>
	<script src="../jquery-ui.js"></script>

	<link rel="stylesheet" href="../CSS/Delta/css/normalise.css"> 
	<link rel="stylesheet" href="../CSS/Delta/theme/jquery-ui.css">
	<script src="../CSS/Delta/js/modernizr-2.0.6.min.js"></script>
	<link rel="icon" type="image/png" href="../favicon.png" />
	

<script>
	function retourFiche()
	{
		location.href="../compta.php";
	}
	function demarrage()
	{
		change_couleur_bouton_menu_general();
	}
</script>
</head>

<body onLoad="demarrage();">
<table width=100% id="tableGlobale" border=0>
<tr>
	<td>
	</td>

	<td id="tableGlobale">

<?php include("../menu_general.php") ?>

	<br/>
	<br/>

			<table border=0 align=right width=100%>
				<tr>
					<td align=left>

						<!-- Connection a la base de donnee -->
						<?php include("connexion_base.php"); 
								$loginSession	=	$_SESSION['login'];
								$prenomSession	=	$_SESSION['prenom'];
								$nomSession		=	$_SESSION['nom'];
								$idSession		=	$_SESSION['idUtilisateurs']
						?>




						<?php
							$champ		= ($_POST['champ']);
							$operateur	= ($_POST['operateur']);
							$critere	= ($_POST['critere']);
							$redacteur	= ($_POST['redacteur']);


						if ($redacteur != "") 
						{
							$select_critere		= "SELECT * FROM fiche WHERE redacteur = '$redacteur' AND date_cloture = ''";							
						}
						elseif ($operateur == "like") 
						{
							$select_critere		= "SELECT * FROM fiche WHERE $champ $operateur '%$critere%' ORDER BY id_fiche DESC ";
						}
						elseif ($operateur == "avg" ) 
						{
							$select_critere		= "SELECT avg($champ) FROM fiche ORDER BY id_fiche DESC ";
						}
						elseif ($operateur == "sum" ) 
						{
							$select_critere		= "SELECT sum($champ) FROM fiche ORDER BY id_fiche DESC ";
						}
						else 
						{
							if ( $critere != "")
							{
								$select_critere		= "SELECT * FROM fiche WHERE $champ $operateur '$critere' ORDER BY id_fiche DESC ";
							}
							else
							{
								$select_critere = "SELECT * FROM fiche ORDER BY id_fiche DESC";
							}
						}

						$reponse = $bdd->query($select_critere) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$select_critere. '<br>'. mysql_error());
						?>


						<table border=0 width=100%>
						 <tr>
						  <th id="ongletBleuFavoris" colspan=2>Filtrage:</th>
						  <td>
						  </td>
						 </tr>
						</table>
						<?php include("filtre.php"); ?>
						<br>
					</td>

					<td>
    					<?php include("menu.php"); ?>
						<?php include("header_afficher_fiches.php"); ?>
					</td>
				</tr>
			</table>



			<table border=0 width=100%>
				<tr>
					<td>

					</td>
				</tr>
			</table>

    <!-- Table permettant les bords à 3% -->
    <table border=0 width=100%>
    <tr>
    	<td width=3%>
    	</td>
    	
    	<td>
			<table id="testScroll" align=center>
				<tr>
					<td>
						<table align=center id="tableVertListeFiches" width=10700px border=0> 
							<thead>
							<tr>
						<!-- ATTENTION: CES DONNEES SONT A L'ENVERS POUR PERMETTRE AU SCROLLBAR D'ETRE A DROITE !!! -->
						        <th width=300px   id="titreTableau">Personne ayant fait la dernière modif</th>
						        <th width=300px   id="titreTableau">Date de dernière modification</th>
						        <th width=300px   id="titreTableau">Visa de la Direction</th>
						        <th width=300px   id="titreTableau">Visa du responsable</th>
						        <th width=300px   id="titreTableau">Date de clôture</th>
<!-- 						    <th width=300px   id="titreTableau">Verification de l'efficacité</th>
						        <th width=300px   id="titreTableau">Justificatifs</th>
						        <th width=300px   id="titreTableau">Date de réalisation de l'action</th>
						        <th width=300px   id="titreTableau">Délai fin d'action</th>
						        <th width=300px   id="titreTableau">Responsable de l'action</th>
						        <th width=300px   id="titreTableau">Type d'action</th>
						        <th width=300px   id="titreTableau">Besoin d'action</th> -->
						        <th width=300px   id="titreTableau">Personne ayant autorisé la poursuite</th>
						        <th width=300px   id="titreTableau">Poursuite des travaux</th>
						        <th width=300px   id="titreTableau">Date d'information du client</th>
						        <th width=300px   id="titreTableau">Information client</th>
						        <th width=300px   id="titreTableau">Date d'information du responsable</th>
						        <th width=300px   id="titreTableau">Information responsable</th>
						        <th width=300px   id="titreTableau">Conséquences sur la diffusion</th>
						        <th width=300px   id="titreTableau">Conséquences sur la satisfaction client</th>
						        <th width=300px   id="titreTableau">Conséquences sur le produit</th>
						        <th width=300px   id="titreTableau">Action sur le produit</th>
						        <th width=300px   id="titreTableau">Commentaire incidence qualité</th>
						        <th width=300px   id="titreTableau">Incidence qualité</th>
						        <th width=300px   id="titreTableau">Commentaire actions curatives</th>
						        <th width=300px   id="titreTableau">Actions curatives</th>
						        <th width=300px   id="titreTableau">Conséquences</th>
						        <th width=300px   id="titreTableau">Causes</th>
						        <th width=300px   id="titreTableau">Numéro de série</th>
						        <th width=300px   id="titreTableau">Nom du matériel/logiciel</th>
						        <th width=300px   id="titreTableau">Marque de l'appareil</th>
						        <th width=300px   id="titreTableau">Type d'appareil</th>
						        <th width=300px   id="titreTableau">Site concerné</th>
						        <th width=300px   id="titreTableau">Date d'apparition</th>
						        <th width=300px   id="titreTableau">Sous processus</th>
						        <th width=300px   id="titreTableau">Commentaire sur la non conformité</th>
						        <th width=300px   id="titreTableau">Type de non conformité</th>
						        <th width=300px   id="titreTableau">Date de cloture</th>
						        <th width=300px   id="titreTableau">Faits</th>						        
						        <th width=200px   id="titreTableau">Rédacteur</th>						        
						        <th width=250px   id="titreTableau">Processus concernés</th>
						        <th width=200px   id="titreTableau">Date d'émission</th>
   						        <th width=200px   id="titreTableau">Type de fiche</th>
						        <th width=200px   id="titreTableau">Référence fiche</th>
   						        <th width=50px 	  id="titreTableau">État</th>

						<!-- ATTENTION: CES DONNEES SONT A L'ENVERS POUR PERMETTRE AU SCROLLBAR D'ETRE A DROITE !!! -->

							</tr>
							</thead>

							<tbody>
							<?php 
						//Recuperation de toutes les donnees de fiche.
								while ($donnees = $reponse->fetch())
								{
									// Déclaration des variables
										$nom = $donnees['nom'];
										$date = $donnees['date'];
										$num_fiche_jour = $donnees['num_fiche_jour'];
										$type_fiche_action = $donnees['type_fiche_action'];
										$redacteur = $donnees['redacteur'];
										$id_nature = $donnees['id_nature'];
										$types_natures = $donnees['types_natures'];
										$noms_natures = $donnees['noms_natures'];
										$date_anomalie = $donnees['date_anomalie'];
										$marque_appareil = $donnees['marque_appareil'];
										$site = $donnees['site'];
										$type_appareil = $donnees['type_appareil'];
										$materiel_logiciel = $donnees['materiel_logiciel'];
										$numero_serie = $donnees['numero_serie'];
										$faits = $donnees['faits'];
										$causes = $donnees['causes'];
										$consequences = $donnees['consequences'];
										$actions_court_terme = $donnees['actions_court_terme'];
										$commentaire_actions_court_terme = $donnees['commentaire_actions_court_terme'];
										$incidence_qualite = $donnees['incidence_qualite'];
										$commentaire_indice_qualite = $donnees['commentaire_indice_qualite'];
										$action_sur_produit = $donnees['action_sur_produit'];

										$consequences_produit = $donnees['consequences_produit'];
										$consequences_satisfaction_client = $donnees['consequences_satisfaction_client'];
										$consequences_diffusion = $donnees['consequences_diffusion'];
										$information_responsable = $donnees['information_responsable'];
										$date_information_responsable = $donnees['date_information_responsable'];
										$information_client = $donnees['information_client'];
										$date_information_client = $donnees['date_information_client'];
										$poursuite_travaux = $donnees['poursuite_travaux'];
										$autorite_poursuite_travaux = $donnees['autorite_poursuite_travaux'];
										$besoin_action_oui_non = $donnees['besoin_action_oui_non'];
										$besoin_actions = $donnees['besoin_actions'];

										// Plus là depuis version 2.0
										//$commentaire_action_sur_produit = $donnees['commentaire_action_sur_produit'];
										$type_action_CPA = $donnees['type_action_CPA'];
										$responsable_action = $donnees['responsable_action'];
										$delai_action = $donnees['delai_action'];
										$date_realisation_action = $donnees['date_realisation_action'];
										$justificatifs = $donnees['justificatifs'];
										$efficacite = $donnees['efficacite'];
										$date_cloture = $donnees['date_cloture'];
										$visa_responsable=$donnees['visa_responsable'];
										$visa_direction=$donnees['visa_direction'];
										$date_derniere_modification=$donnees['date_derniere_modification'];
										$nom_derniere_modification=$donnees['nom_derniere_modification'];


										// Gestion de l'état.
										if ($faits == "" && ($causes == "")	)
										{
											$couleurFond = " bgcolor=red ";
											$logoEtat	=	"../Images/carre_rouge.png";
										}
										if (	($faits != "")	&& ($causes != "") && ($date_cloture == "")	)
										{
											$couleurFond = " bgcolor=orange ";
											$logoEtat	=	"../Images/carre_orange.png"	;										
										}
										if (	($faits != "")	&& ($causes != "") && ($date_cloture != "")	)
										{
											$couleurFond = " ";
											$logoEtat	=	"../Images/carre_vert.png"	;										
										}

							
								?>
						  
						  
							<tr>
								<!-- ATTENTION: CES DONNEES SONT A L'ENVERS POUR PERMETTRE AU SCROLLBAR D'ETRE A DROITE !!! -->
						            <td width=300px ><?php echo $nom_derniere_modification ?></td>
						            <td width=300px ><?php echo $date_derniere_modification ?></td>
						            <td width=300px ><?php echo $visa_direction ?></td>
						            <td width=300px ><?php echo $visa_responsable ?></td>
						            <td width=300px ><?php echo $date_cloture ?></td>
<!-- 						            <td width=300px ><?php echo $efficacite ?></td>
						            <td width=300px ><?php echo $justificatifs ?></td>
						            <td width=300px ><?php echo $date_realisation_action ?></td>
						            <td width=300px ><?php echo $delai_action ?></td>
						            <td width=300px ><?php echo $responsable_action ?></td>
						            <td width=300px ><?php echo $type_action_CPA ?></td>
						            <td width=300px ><?php echo $besoin_action_oui_non ?></td> -->
						            <td width=300px ><?php echo $autorite_poursuite_travaux ?></td>
						            <td width=300px ><?php echo $poursuite_travaux ?></td>
						            <td width=300px ><?php echo $date_information_client ?></td>
						            <td width=300px ><?php echo $information_client ?></td>
						            <td width=300px ><?php echo $date_information_responsable ?></td>
						            <td width=300px ><?php echo $information_responsable ?></td>
						            <td width=300px ><?php echo $consequences_diffusion ?></td>
						            <td width=300px ><?php echo $consequences_satisfaction_client ?></td>
						            <td width=300px ><?php echo $consequences_produit ?></td>
						            <td width=300px ><?php echo $action_sur_produit ?></td>
						            <td width=300px ><?php echo $commentaire_indice_qualite ?></td>
						            <td width=300px ><?php echo $incidence_qualite ?></td>
						            <td width=300px ><?php echo $commentaire_actions_court_terme ?></td>
						            <td width=300px ><?php echo $actions_court_terme ?></td>
						            <td width=300px ><?php echo $consequences ?></td>
						            <td width=300px ><?php echo $causes ?></td>
						            <td width=300px ><?php echo $numero_serie ?></td>
						            <td width=300px ><?php echo $materiel_logiciel ?></td>
						            <td width=300px ><?php echo $marque_appareil ?></td>
						            <td width=300px ><?php echo $type_appareil ?></td>
						            <td width=300px ><?php echo $site ?></td>
						            <td width=300px ><?php echo $date_anomalie ?></td>
						            <td width=300px ><?php echo $noms_natures ?></td>
						            <td width=300px ><?php echo $commentaire_indice_qualite ?></td>
						            <td width=300px ><?php echo $incidence_qualite ?></td>
						            <td width=300px ><?php echo $date_cloture ?></td>
						            <td width=300px ><?php echo $faits ?></td>						            
						            <td width=200px ><?php echo $redacteur ?></td>
						            <td width=250px ><?php echo $types_natures ?></td>
						            <td width=200px ><?php echo $date ?></td>
						            <td width=200px ><?php echo $type_fiche_action ?></td>						            
						            <td width=200px ><?php echo "<A HREF=\"editer_fiche.php?nom=$nom\">$nom</A>"?> </td>
   						            <td width=50px align=center >	<?php echo "<img src='$logoEtat'> " ?> 	</td>

								<!-- ATTENTION: CES DONNEES SONT A L'ENVERS POUR PERMETTRE AU SCROLLBAR D'ETRE A DROITE !!! -->

							</tr>
								<?php
									}
									$reponse->closeCursor(); // Termine le traitement de la requête
								?>
							</tbody>
						</table>

					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<?php 
							//echo "Critères: Champ=\"$champ\", Operateur=\"$operateur\", filtre=\"$critere\<br>";
							//echo "Requete SQL envoyee au serveur: $select_critere";
						?>
					</td>
				</tr>
				<tr>
					<td>
						<br />
					</td>
				</tr>
				<tr>
					<td>
						Légende d'état:<br />
						<img src='../Images/carre_rouge.png'> : Les faits et causes ne sont pas renseignés.<br />
						<img src='../Images/carre_orange.png'> : Les faits et causes sont renseignés, mais la fiche n'est pas encore cloturée.<br />
						<img src='../Images/carre_vert.png'> : La fiche est cloturée.<br />
					</td>
				</tr>
			</table>

    <!-- Fin de la table permettant les bords à 3% --> 
		</td>

		<td width=3%>
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
