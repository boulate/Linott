<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head><!-- Permet de se connecter à la base Mysql-->
<script>
function message()
{
	message = document.getElementById("message").value;
	if (message != "")
	{
		alert(message);		
	}
	closeWindows();
}

function closeWindows()
{
	window.parent.opener.location.reload();window.close();
}
</script> 
</head>

<body onLoad="message();">

<?php 
include("connexion_base.php"); 
include("importer_configuration.php");
session_start();
//echo "debut script --- ";
$message="";
$majHeuresSup=0;
$heuresSupToSuppr=0;

$idUtilisateur		=	$_SESSION['idUtilisateurs'];
$nomUtilisateur		=	$_SESSION['login'];
$nombre_periodes	= 	$_SESSION['nombre_lignes_total']; 
$total_journee 		= 	($_POST['total_journee_instant']);
$date 			=	($_POST['AltFieldDateMysql']);
$heureContrat		=	$_SESSION['nbr_heures'];

// Permet de gérer le cas ou le nom de l'axe2 soit "récupération" ou "Récupération"
$nomAxe2 = "";

echo "nombre de periodes: $nombre_periodes </br>";
echo "date: $date </br>";
echo "Total de la journée: $total_journee </br>";
echo "Heures contrat: $heureContrat </br>";

function valider_fiche($bdd, $idUtilisateur, $nomUtilisateur, $nombre_periodes, $total_journee, $date, $heureContrat)
{
global $activerAxe3;
global $activerSections;
global $nomAxe2;
$nomAxe2 = "";

$i = 0;
	$idUtilisateur		=	$_SESSION['idUtilisateurs'];
	$nomUtilisateur		=	$_SESSION['login'];
	$nombre_periodes	= 	$_SESSION['nombre_lignes_total']; 
	$total_journee 		= 	($_POST['total_journee_instant']);
	$date 			=	($_POST['AltFieldDateMysql']);
	$heureContrat		=	$_SESSION['nbr_heures'];

	// Penser à gérer les absences.
	$absence="";
	if ($absence = 1)
	{
		$motifAbsence="";
	}

	// Report de chaque periode.
	while ($nombre_periodes > $i)
	{
		echo "</br><br>";
		$i++;
		$numero_ligne=$i;
		echo "numero de ligne: $i<br>";
		// Tant que i est plus petit que nombre_periodes / 2 on est dans le matin, ensuite on est dans l'apres midi.
		if ($i <= $nombre_periodes/2)
			{
			$heure_debut_periode = ($_POST['deMatin_periode' . $i]);
			echo "Heure debut periode $i: $heure_debut_periode</br>";

			$heure_fin_periode= ($_POST['aMatin_periode' . $i]);
			echo "Heure Fin periode $i: $heure_fin_periode</br>";

			// Si duree_periode = 0, on le vide (en cas d'heures entrees puis effaces par l'utilisateur).
			$duree_periode= ($_POST['totalMatin_periode' . $i]);
			if ($duree_periode == "0")
			$duree_periode="";
			echo "Duree de la periode: $duree_periode</br>";

			// Si axex_periode contient le text par défaut, on le vide.
			$axe1_periode= ($_POST['choix_axe1_periode' . $i]);
			if ($axe1_periode == "Choisir axe 1")
			$axe1_periode="";
			echo "Axe 1 de la periode: $axe1_periode</br>";
			// on recupere l'id renseigne
			$id_axe1_periode= ($_POST['id_choix_axe1_periode' . $i]);
			echo "ID axe 1 de la periode: $id_axe1_periode</br>";

			// Si axex_periode contient le text par défaut, on le vide.
			$axe2_periode=($_POST['choix_axe2_periode' . $i]);
			if ($axe2_periode == "Choisir axe 2")
			$axe2_periode="";
			$nomAxe2 = $axe2_periode;
			echo "Axe 2 de la periode: $axe2_periode</br>";
			// on recupere l'id renseigne
			$id_axe2_periode= ($_POST['id_choix_axe2_periode' . $i]);
			echo "ID axe 2 de la periode: $id_axe2_periode</br>";

			// Si axe3 periode contient le texte par défaut, on le vide.
			$axe3_periode= ($_POST['choix_axe3_periode' . $i]);
			if ($axe3_periode == "Choisir axe 3")
			$axe3_periode="";
			echo "Axe3 de la periode: $axe3_periode</br>";
			// on recupere l'id renseigne
			$id_axe3_periode= 	($_POST['id_choix_axe3_periode'	. $i]);
				if ($activerAxe3 != "checked")
				{	
					$id_axe3_periode="1";
				}
			echo "ID axe3 de la periode: $id_axe3_periode</br>";

			// On recupère l'ID horaire
			$id_periode= 		($_POST['id_horaire_periode' 		. $i]);
			echo "ID de la période: $id_periode";
			}
		else
			{
			$heure_debut_periode = ($_POST['deAprem_periode' . $i]);
			echo "Heure debut periode $i: $heure_debut_periode</br>";

			$heure_fin_periode= ($_POST['aAprem_periode' . $i]);
			echo "Heure Fin periode $i: $heure_fin_periode</br>";

			// Si duree_periode = 0, on le vide (en cas d'heures entrees puis effaces par l'utilisateur).
			$duree_periode= ($_POST['totalAprem_periode' . $i]);
			if ($duree_periode == "0")
			$duree_periode="";
			echo "Duree de la periode: $duree_periode</br>";

			// Si axex_periode contient le text par défaut, on le vide.
			$axe1_periode= ($_POST['choix_axe1_periode' . $i]);
			if ($axe1_periode == "Choisir axe 1")
			$axe1_periode="";
			echo "Axe 1 de la periode: $axe1_periode</br>";
			// on recupere l'id renseigne
			$id_axe1_periode= ($_POST['id_choix_axe1_periode' . $i]);
			echo "ID axe 1 de la periode: $id_axe1_periode</br>";

			// Si axex_periode contient le text par défaut, on le vide.
			$axe2_periode=($_POST['choix_axe2_periode' . $i]);
			if ($axe2_periode == "Choisir axe 2")
			$axe2_periode="";
			$nomAxe2 = $axe2_periode;
			echo "Axe 2 de la periode: $axe2_periode</br>";
			// on recupere l'id renseigne
			$id_axe2_periode= ($_POST['id_choix_axe2_periode' . $i]);
			echo "ID axe 2 de la periode: $id_axe2_periode</br>";

			// Si axe3 periode contient le texte par défaut, on le vide.
			$axe3_periode= ($_POST['choix_axe3_periode' . $i]);
			if ($axe3_periode == "Choisir axe 3")
			$axe3_periode="";
			echo "Axe3 de la periode: $axe3_periode</br>";
			// on recupere l'id renseigne
			$id_axe3_periode= ($_POST['id_choix_axe3_periode' . $i]);
				if ($activerAxe3 != "checked")
				{	
					$id_axe3_periode="1";
				}
			echo "ID axe3 de la periode: $id_axe3_periode</br>";

			// On recupère l'ID horaire
			$id_periode= 		($_POST['id_horaire_periode' 		. $i]);
			echo "ID de la période: $id_periode";
			}

		// Si id_periode est vide (donc si c'est une nouvelle entree), on remplace le vide par NULL pour que la base puisse incrémenter l'id toute seule.
		if (empty($id_periode))
		{
			$id_periode = "NULL";
		}

		if ( ( $duree_periode != "" ) && ($id_periode == "NULL" ) ) 
		{
			// Attention: Bien renseigner $bdd pour qu'il soit visible également dans ma fonction.
			verifier_incoherences($bdd, $id_periode, $date, $heure_debut_periode, $heure_fin_periode, $duree_periode, $idUtilisateur, $nomUtilisateur, 1, $id_axe1_periode, $id_axe2_periode, $id_axe3_periode, $numero_ligne);
			//renseigner_base($bdd, $id_periode, $date, $heure_debut_periode, $heure_fin_periode, $duree_periode, 1, Guillaume, 1, $id_axe1_periode, $id_axe2_periode, $id_axe3_periode, $numero_ligne);
		}
		
		if ( $id_periode != "NULL")
		{
			echo "<br>***Periode $numero_ligne n'est pas une nouvelle période: update de la période si elle a été modifiée.";
			global $message;
			//$message=$message . " --- Ligne $numero_ligne n'est pas une nouvelle période: update de la période si elle a été modifiée.";
			verifier_incoherences($bdd, $id_periode, $date, $heure_debut_periode, $heure_fin_periode, $duree_periode, $idUtilisateur, $nomUtilisateur, 1, $id_axe1_periode, $id_axe2_periode, $id_axe3_periode, $numero_ligne);
		}
	}
}

function verifier_incoherences($bdd, $id_periode, $date, $heureDebut, $heureFin, $dureePeriode, $idUtilisateur, $nomUtilisateur, $idSection, $idAxe1, $idAxe2, $idAxe3, $numero_ligne)
{
	$requete_incoherences = "SELECT * FROM Periodes where date = '$date' and Utilisateurs_idUtilisateurs = $idUtilisateur";
	try
	{
		$reponse_incoherences = $bdd->query($requete_incoherences) or die('Erreur SQL !<br>' .$requete_incoherences. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	$incoherence=0;
	while ($donnees = $reponse_incoherences->fetch())
	{

		// Declaration des variables
		$oldIdPeriode		= $donnees['idHoraires'];
		$oldDate		= $donnees['date'];
		$oldHoraireDebut	= $donnees['horaireDebut'];
		$oldHoraireFin		= $donnees['horaireFin'];
		$oldTotalHoraire 	= $donnees['totalHoraire'];
		$oldIdUtilisateur 	= $donnees['Utilisateurs_idUtilisateurs'];
		$oldLoginUtilisateur 	= $donnees['Utilisateurs_login'];
		$oldSection 		= $donnees['Section_idSection'];
		$oldAxe1 		= $donnees['Axe1_idAxe1'];
		$oldAxe2 		= $donnees['Axe2_idAxe2'];
		$oldAxe3 		= $donnees['Axe3_idAxe3'];
		$oldNumeroLigne	= $donnees['numeroLigne'];

		if ( ($heureDebut == $oldHoraireDebut) && ( $id_periode != $oldIdPeriode) )
		{
			$message_incoherence = "Ligne $numero_ligne: Il existe déjà une période ayant cette heure de départ. ";
			$incoherence++;
			break;
		}
		// Si la nouvelle heure de départ est comprise dans un période déjà renseignées.
		if ( ($heureDebut > $oldHoraireDebut) && ($heureDebut < $oldHoraireFin) && ( $id_periode != $oldIdPeriode) )
		{
			$message_incoherence = "Ligne $numero_ligne: L'heure de départ est comprise dans une période déjà renseignée. ";
			$incoherence++;
			break;
		}
		if ( ($heureDebut >= 24) || ($heureDebut < 0) )
		{
			$message_incoherence = "Ligne $numero_ligne: L'heure de départ doit être comprise entre 00h00 et 23h59";
			$incoherence++;
			break;			
		}
		if ( ($heureFin >= 24) || ($heureFin < 0) )
		{
			$message_incoherence = "Ligne $numero_ligne: L'heure de fin doit être comprise entre 00h00 et 23h59";
			$incoherence++;
			break;	
		}
		if ( $heureDebut > $heureFin )
		{
			$message_incoherence = "Ligne $numero_ligne: L'heure de fin ne doit pas être inférieure à l'heure de début.";
			$incoherence++;
			break;	
		}
	}
	// Si aucune incohérence n'a été trouvée, on lance l'enregistrement. Sinon, on affiche le message_incoherence.
	if ($incoherence == 0)
	{	
		global $nomAxe2;
		// Si c'est une recup, on enleve l'équivalent en heure sup.
		//if ($idAxe2 == 49)
		if ( ($nomAxe2 == "récupération") || ($nomAxe2 == "Récupération") )
		{
			echo "$nomAxe2";
			global $heuresSupToSuppr;
			$heuresSupToSuppr	=	$heuresSupToSuppr + $dureePeriode	;
		}
		if ($id_periode == "NULL")
		{
			echo "Aucune incohérence. On lance l'enregistrement en base.<br>";
			renseigner_base($bdd, $id_periode, $date, $heureDebut, $heureFin, $dureePeriode, $idUtilisateur, $nomUtilisateur, $idSection, $idAxe1, $idAxe2, $idAxe3, $numero_ligne);			
		}
		if ($id_periode != "NULL")
		{
			update_periode($bdd, $id_periode, $date, $heureDebut, $heureFin, $dureePeriode, $idUtilisateur, $nomUtilisateur, $idSection, $idAxe1, $idAxe2, $idAxe3, $numero_ligne);			
		}

	}
	else
	{
		echo $message_incoherence;
		global $message;
		$message=$message . " --- " . $message_incoherence;
	}
}

function renseigner_base($bdd, $id_periode, $date, $heureDebut, $heureFin, $dureePeriode, $idUtilisateur, $nomUtilisateur, $idSection, $idAxe1, $idAxe2, $idAxe3, $numero_ligne)
{
	$sql= "	INSERT INTO Periodes (idHoraires, date, horaireDebut, horaireFin, totalHoraire, Utilisateurs_idUtilisateurs, Utilisateurs_login, Section_idSection, Axe1_idAxe1, Axe2_idAxe2, Axe3_idAxe3, numeroLigne) 
	VALUES 			 ($id_periode, '$date', '$heureDebut', '$heureFin', '$dureePeriode', '$idUtilisateur', '$nomUtilisateur', '$idSection', '$idAxe1', '$idAxe2', '$idAxe3', '$numero_ligne')";

	echo "***	INSERT INTO Periodes (idHoraires, date, horaireDebut, horaireFin, totalHoraire, Utilisateurs_idUtilisateurs, Utilisateurs_login, Section_idSection, Axe1_idAxe1, Axe2_idAxe2, Axe3_idAxe3, numeroLigne) 
	VALUES 			 ($id_periode, '$date', '$heureDebut', '$heureFin', '$dureePeriode', '$idUtilisateur', '$nomUtilisateur', '$idSection', '$idAxe1', '$idAxe2', '$idAxe3', '$numero_ligne')";

	try
	{
		$reponse = $bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	// global nous permet de reprendre la variable globale de la page.
	global $message;
	//$message="Période $numero_ligne ajoutée";
	
	global $majHeuresSup;
	$majHeuresSup++;

	// Ce i++ semble ne servir à rien. A vérifier.
	$i++;
}


function update_periode($bdd, $id_periode, $date, $heureDebut, $heureFin, $dureePeriode, $idUtilisateur, $nomUtilisateur, $idSection, $idAxe1, $idAxe2, $idAxe3, $numero_ligne)
{
	$requete_update = "SELECT * FROM Periodes where idHoraires = '$id_periode'";
	try
	{
		$reponse_update = $bdd->query($requete_update) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	while ($donnees = $reponse_update->fetch())
	{

		// Declaration des variables
		$oldIdPeriode		= $donnees['idHoraires'];
		$oldDate		= $donnees['date'];
		$oldHoraireDebut	= $donnees['horaireDebut'];
		$oldHoraireFin		= $donnees['horaireFin'];
		$oldTotalHoraire 	= $donnees['totalHoraire'];
		$oldIdUtilisateur 	= $donnees['Utilisateurs_idUtilisateurs'];
		$oldLoginUtilisateur 	= $donnees['Utilisateurs_login'];
		$oldSection 		= $donnees['Section_idSection'];
		$oldAxe1 		= $donnees['Axe1_idAxe1'];
		$oldAxe2 		= $donnees['Axe2_idAxe2'];
		$oldAxe3 		= $donnees['Axe3_idAxe3'];
		$oldNumeroLigne	= $donnees['numeroLigne'];

		if ($oldIdPeriode == $id_periode)
		{ 
			if ($oldDate != $date)
			{
				$sql="UPDATE `Periodes` SET `date` = '$date' WHERE `idHoraires` = '$id_periode'";
				$toChange="Date: $date. ";
				commande_update($bdd, $toChange, $sql);
			}
			if ($oldHoraireDebut != $heureDebut)
			{
				$sql="UPDATE `Periodes` SET `horaireDebut` = '$heureDebut' WHERE `idHoraires` = '$id_periode'";
				$toChange="Heure de départ: $heureDebut. ";
				commande_update($bdd, $toChange, $sql);
			}
			if ($oldHoraireFin != $heureFin)
			{ 
				$sql="UPDATE `Periodes` SET `horaireFin` = '$heureFin' WHERE `idHoraires` = '$id_periode'";
				//$sql="UPDATE Periodes SET 'horaireDebut' = $heureDebut WHERE 'idHoraires' = '$id_periode'";
				$toChange="Heure de fin: $heureFin. ";
				commande_update($bdd, $toChange, $sql);
			}
			if ($oldTotalHoraire != $dureePeriode)
			{
				$sql="UPDATE `Periodes` SET `totalHoraire` = '$dureePeriode' WHERE `idHoraires` = '$id_periode'";
				$toChange="Durée de la période: $dureePeriode. ";
				commande_update($bdd, $toChange, $sql);
			}
//			if ($oldSection != )
//			{
//				$sql="UPDATE Periodes SET 'date' = $date WHERE 'idHoraires' = '$id_periode'";
//				$toChange="Date";
//				commande_update($bdd, $toChange, $sql);
//			}
			if ($oldAxe1 != $idAxe1)
			{
				$sql="UPDATE `Periodes` SET `Axe1_idAxe1` = '$idAxe1' WHERE `idHoraires` = '$id_periode'";
				$toChange="Axe 1: $idAxe1. ";
				commande_update($bdd, $toChange, $sql);
			}
			if ($oldAxe2 != $idAxe2)
			{
				$sql="UPDATE `Periodes` SET `Axe2_idAxe2` = '$idAxe2' WHERE `idHoraires` = '$id_periode'";
				$toChange="Axe 2: idAxe2. ";
				commande_update($bdd, $toChange, $sql);
			}
			if ($oldAxe3 != $idAxe3)
			{
				$sql="UPDATE `Periodes` SET `Axe3_idAxe3` = '$idAxe3' WHERE `idHoraires` = '$id_periode'";
				$toChange="Axe3: $idAxe3. ";
				commande_update($bdd, $toChange, $sql);	
			}
		}
	}
global $majHeuresSup;
$majHeuresSup++;
}


function commande_update($bdd, $toChange, $sql)
{
	echo "Changement de: $toChange";
	try
	{
		$reponse = $bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
}



include("gestion_heures_sup.php");
// Fin de la fiche
valider_fiche($bdd);

if ($majHeuresSup > 0)
{
	majHeuresSup("valider_fiche");
}


echo $message;
?> 

<br>
<input type="text" id=message value="<?php echo $message;?>">
<br>


</body>
