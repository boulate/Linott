<?php session_start(); ?>

<?php
echo "Connexion a la base de donnee...<br><br>";
include("connexion_base.php");


// Indique la date de creation de la fiche:
$date=date("Y-m-d");
echo "<br>- Date de creation de la fiche:<br> $date <br>";

// Indique le type d'action qui a ete coche
$type_action = $_GET['type_action_cochee'];
echo "<br>- ID de(s) type(s) d'action(s) coche(s) : <br>";
echo "$type_action";
echo "<br>";


// Indique le redacteur qui a ete renseigne
$id_redacteur = $_SESSION['idUtilisateurs'];
$redacteur = $_SESSION['prenom'] . " " . $_SESSION['nom'];

//$redacteur=$_GET['redacteur'];
echo "<br>- Redacteur de la fiche: <br> $redacteur <br>";
echo "<br>- ID du redacteur de la fiche: <br> $id_redacteur <br>";


// Indique l'ID de la nature qui a ete cochee
echo "<br>- ID de la Nature de la fiche : <br>";
if(isset($_GET['nature_cochee']))
{
	foreach($_GET['nature_cochee'] AS $nom=>$last_id_nature)
	$id_nature = "-$last_id_nature-$id_nature";	
}
echo "$id_nature ";
echo "<br>";

// Indique la/les nature(s) cochee(s)
$requete_id_nature_or = "ID_nature = 0";
if(isset($_GET['nature_cochee']))
{
	foreach($_GET['nature_cochee'] AS $nom=>$last_id_nature)
	{
	$requete_id_nature_or = "$requete_id_nature_or or ID_nature = $last_id_nature";
	}	
}
	$requete_nom_nature = "SELECT * from nature WHERE $requete_id_nature_or";
	$reponse_nom_nature = $bdd->query($requete_nom_nature) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());

// On affiche tous les nom de natures correspondant a leurs ID (tant qu'il y en a).
while ($donnees_nom_nature = $reponse_nom_nature->fetch())
{
		$last_nom_nature = $donnees_nom_nature['Nature'];
		$noms_natures = "$last_nom_nature<br>$noms_natures";
}
echo "<br>- Les noms de nature sont:<br>$noms_natures ";


// Indique le/les types de nature(s) cochee(s)
$requete_id_nature_or = "ID_nature = 0";
if(isset($_GET['nature_cochee']))
{
	foreach($_GET['nature_cochee'] AS $nom=>$last_id_nature)
	{
	$requete_id_nature_or = "$requete_id_nature_or or ID_nature = $last_id_nature";
	}	
}
	$requete_type_nature = "SELECT * from nature WHERE $requete_id_nature_or";
	$reponse_type_nature = $bdd->query($requete_type_nature) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());

// On affiche tous les nom de natures correspondant a leurs ID (tant qu'il y en a).
while ($donnees_type_nature = $reponse_type_nature->fetch())
{
		$last_type_nature = $donnees_type_nature['Type'];
		$types_natures = "$last_type_nature<br>$types_natures";
}
echo "<br>- Les types de natures sont:<br>$types_natures ";


// Date de la non conformite
$date_anomalie=$_GET['date_anomalie'];
echo "<br>- Date de l'anomalie: <br> $date_anomalie<br>";

// Marque de l'appareil
$marque=$_GET['marque'];
echo "<br>- Marque de l'appareil : <br> $marque <br>";

// Site
$site=$_GET['site'];
echo "<br>- Site concerne : <br> $site <br>";


// Type d'appareil
$type=$_GET['type'];
echo "<br>- Type de l'appareil : <br> $type <br>";


// Materiel / logiciel
$materiel=$_GET['materiel'];
echo "<br>- Materiel concerne : <br> $materiel <br>";


// Numero de serie
$num_serie=$_GET['num_serie'];
echo "<br>- Numero de serie de l'appareil : <br> $num_serie <br>";

///////// FICHE ACTION
// Description des faits
$faits=addslashes($_GET['description_faits']);
echo "<br> Description des Faits : <br> $faits <br>";

$causes=addslashes($_GET['description_causes']);
echo "<br>- Description des causes : <br> $causes <br>";

$consequences=addslashes($_GET['description_consequences']);
echo "<br>- Description des consequences : <br> $consequences <br>";

$actions_court_terme=addslashes($_GET['actions_court_terme']);
echo "<br>- Actions a court terme : <br> $actions_court_terme <br>";

$commentaire_actions_court_terme=addslashes($_GET['commentaire_actions_court_terme']);
echo "<br>- Commentaire actions a court terme : <br> $commentaire_actions_court_terme <br>";

// incidence sur la qualite
$incidence_qualite=$_GET['incidence_qualite'];
echo "<br>- Incidence sur la qualite : <br> $incidence_qualite <br>";

// Commentaire_indice_qualite
$commentaire_indice_qualite=addslashes($_GET['commentaire_indice_qualite']);
echo "<br>- Commentaire_indice_qualite : <br> $commentaire_indice_qualite <br>";

// Action sur produit
$action_sur_produit=$_GET['action_sur_produit'];
echo "<br>- action_sur_produit : <br> $action_sur_produit <br>";

// Enlevé sur la version 2.0
// Commentaire action sur produit
//$commentaire_action_sur_produit=addslashes($_GET['commentaire_action_sur_produit']);
//echo "<br>- Commentaire de l'action_sur_produit : <br> $commentaire_action_sur_produit <br>";

$consequences_produit=addslashes($_GET['consequences_produit']);
echo "<br>- Conséquences sur le produit: : <br> $consequences_produit <br>";

$consequences_satisfaction_client=addslashes($_GET['consequences_satisfaction_client']);
echo "<br>- Conséquences sur la satisfaction client: : <br> $consequences_satisfaction_client <br>";

$consequences_diffusion=addslashes($_GET['consequences_diffusion']);
echo "<br>- Conséquences sur la diffusion: : <br> $consequences_diffusion <br>";


$information_responsable=$_GET['information_responsable'];
echo "<br>- information_responsable : <br> $information_responsable <br>";


$date_information_responsable=$_GET['date_information_responsable'];
echo "<br>- date_information_responsable : <br> $date_information_responsable <br>";


$information_client=$_GET['information_client'];
echo "<br>- information_client : <br> $information_client <br>";


$date_information_client=$_GET['date_information_client'];
echo "<br>- date_information_client : <br> $date_information_client <br>";


$poursuite_travaux=$_GET['poursuite_travaux'];
echo "<br>- poursuite_travaux : <br> $poursuite_travaux <br>";


$autorite_poursuite_travaux=$_GET['autorite_poursuite_travaux'];
echo "<br>- autorite_poursuite_travaux : <br> $autorite_poursuite_travaux <br>";



// Actions
// Oui ou non.
$besoin_action_oui_non=$_GET['besoin_action_oui_non'];
echo "<br>- besoin_action_oui_non : <br> $besoin_action_oui_non <br>";


$actions=addslashes($_GET['besoin_actions']);
echo "<br>- Actions realisees: <br> $actions <br>";

// C, P ou A?
$cpa=$_GET['CPA'];
echo "<br>- Type d'action (Corrective, Preventive, Amelioration): <br> $cpa <br>";

// responsable
$responsable_action=$_GET['responsable_action'];
echo "<br>- responsable_action = $responsable_action <br>";

// Delai previsionnel
$delai_action=$_GET['delai_action'];
echo "<br>- delai_action = $delai_action";
// Realisation
$date_realisation_action=$_GET['date_realisation_action'];
echo "<br>- date_realisation_action = $date_realisation_action <br>";

// Elements justificatifs
$justificatifs=addslashes($_GET['elements_justificatifs']);
echo "<br>- Elements justificatifs : <br> $justificatifs <br>";

// Verification de l'efficacite des actions menees
$efficacite=addslashes($_GET['efficacite_action']);
echo "<br>- Verification de l'efficacite des actions menees : <br> $efficacite <br>";

// Cloture le :
$date_cloture=$_GET['date_cloture'];
echo "<br>- date_cloture = $date_cloture";

// Visas
$visa_responsable=$_GET['visa_responsable'];
echo "<br>- visa_responsable = $visa_responsable";

$visa_direction=$_GET['visa_direction'];
echo "<br>- visa_direction = $visa_direction";



// Nom de la fiche:
$requete_fiche_jour="SELECT * from fiche WHERE date LIKE \"$date\" ORDER BY num_fiche_jour";
$reponse_fiche_jour = $bdd->query($requete_fiche_jour) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse_fiche_jour->fetch())
	{
	$num_fiche_jour=$donnees['num_fiche_jour'];
	}
$nouveau_num_fiche_jour = $num_fiche_jour+1;
echo "<br>- Nouveau numero de fiche :<br> $nouveau_num_fiche_jour<br>";
$date_nom_fiche = date("Ymd");
$heure_nom_fiche = date("H:i");
$nom_fiche = "$date_nom_fiche-$heure_nom_fiche-$nouveau_num_fiche_jour";
echo "<br>- Nom de la fiche: <br> $nom_fiche <br>";


// Renseignement de la fiche en base.
// Envoi de la requete
$sql= "INSERT INTO 
fiche (
	nom,
	date,
	num_fiche_jour,
	type_fiche_action,
	id_redacteur,
	redacteur,
	id_nature,
	noms_natures,
	types_natures,
	date_anomalie,
	marque_appareil,
	site,
	type_appareil,
	materiel_logiciel,
	numero_serie,
	faits,
	causes,
	consequences,
	actions_court_terme,
	commentaire_actions_court_terme,
	incidence_qualite,
	commentaire_indice_qualite,	
	action_sur_produit,
	consequences_produit,
	consequences_satisfaction_client,
	consequences_diffusion,
	information_responsable,
	date_information_responsable,
	information_client,
	date_information_client,
	poursuite_travaux,
	autorite_poursuite_travaux,
	besoin_action_oui_non,
	besoin_actions,
	type_action_CPA,
	responsable_action,
	delai_action,	
	date_realisation_action,
	justificatifs,	
	efficacite,	
	date_cloture,	
	visa_responsable,	
	visa_direction) 

VALUES (
	'$nom_fiche',	
	'$date',	
	'$nouveau_num_fiche_jour',	
	'$type_action',	
	'$id_redacteur',		
	'$redacteur',	
	'$id_nature',	
	'$noms_natures',	
	'$types_natures',	
	'$date_anomalie',	
	'$marque',		
	'$site',	
	'$type',	
	'$materiel',		
	'$num_serie',	
	'$faits',	
	'$causes',	
	'$consequences',	
	'$actions_court_terme',	
	'$commentaire_actions_court_terme',	
	'$incidence_qualite',	
	'$commentaire_indice_qualite',	
	'$action_sur_produit',	
	'$consequences_produit',
	'$consequences_satisfaction_client',
	'$consequences_diffusion',
	'$information_responsable',
	'$date_information_responsable',
	'$information_client',
	'$date_information_client',
	'$poursuite_travaux',
	'$autorite_poursuite_travaux',
	'$besoin_action_oui_non',		
	'$actions',		
	'$cpa',			
	'$responsable_action',	
	'$delai_action',	
	'$date_realisation_action',	
	'$justificatifs',	
	'$efficacite',	
	'$date_cloture', 
	'$visa_responsable',	
	'$visa_direction'
)";

$bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());

print("<center><h1>La fiche \"$nom_fiche\" a bien ete cree.<h1></center><br />");
?>

