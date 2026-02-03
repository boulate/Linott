<?php include("connexion_base.php"); 
include("importer_configuration.php");

session_start();
//$nombre_periodes		=	$_SESSION['nombre_lignes_total'];
$idUtilisateur			=	$_SESSION['idUtilisateurs'];
//$nomUtilisateur		=	$_SESSION['login']; 
$nbrConges 				= 	$_SESSION['nbrConges'];
$nbrRTT 				=	$_SESSION['nbrRTT'];

$dateToImport			=	$_GET['dateToImport'];
//$dateToImport="2014-02-11";

//$idUtilisateur="1";

// Pour la consultation de fiche. Si un ID utilisateur différent est renseigné dans l'adresse, c'est lui le idUtilisateur en cours.
$idConsultUser 		= 	$_GET['idConsultUser'];
if ( $idConsultUser != "")
{
	$idUtilisateur = $idConsultUser;

	$sql 		=	"SELECT nbrConges, nbrRTT FROM Utilisateurs WHERE idUtilisateurs = $idConsultUser";
	//echo "<br>$sql <br>";
	try
	{
		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	while ($donnees = $reponse->fetch())
	{
		$nbrConges	=	$donnees['nbrConges'];
		$nbrRTT		=	$donnees['nbrRTT'];
	}
	$reponse->closeCursor();



}

function calcul_heures($interval, $bdd, $dateToImport, $idUtilisateur)
{
	global $axe2_exclus_totaux;
	$listeExclure = "";
	if ($axe2_exclus_totaux != "")
	{
		// Permet d'exclure les axe2 sensés ne pas être comptés.
		$sql 		=	"SELECT idAxe2 FROM Axe2 WHERE codeAxe2 IN ($axe2_exclus_totaux)";
		//echo "<br>$sql <br>";
		try
		{
			$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		while ($donnees = $reponse->fetch())
		{
			$exclure	=	$donnees['idAxe2'];
			if ( $exclure != "" )
			{
				if ( $listeExclure == "" )
				{
					$listeExclure = " \"$exclure\" ";
				}
				if ( $listeExclure != "" )
				{
					$listeExclure = " \"$exclure\",$listeExclure ";
				}
			}
		}
		$reponse->closeCursor();
	}
	//echo $listeExclure;
	$andAxe2Exclus = "";
	if ($listeExclure != "")
	{
		$andAxe2Exclus = "AND Axe2_idAxe2 NOT IN ($listeExclure)";
	}


	// Retourne les 7 derniers jours au lieu de la semaine, etc.
	$sql 		=	"SELECT totalHoraire FROM Periodes WHERE  $interval('$dateToImport') = $interval(date) AND MONTH(date) = MONTH('$dateToImport') AND YEAR(date) = YEAR('$dateToImport') AND Utilisateurs_idUtilisateurs = $idUtilisateur $andAxe2Exclus";
	//echo "<br>$sql <br>";
	try
	{
		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	$total=0;
	while ($donnees = $reponse->fetch())
		{
			$heures	=	$donnees['totalHoraire'];
			$total	=	$total + $heures;
		}
	return $total;
	echo "total = $total";
}

function calcul_total_annuel($bdd, $dateToImport, $idUtilisateur)
{
	$formatDate = new DateTime($dateToImport);
	$annee= $formatDate->format('Y');
	$anneeMoinsUn = $annee - 1;
	$anneePlusUn = $annee + 1;
	//echo "$annee, $anneeMoinsUn, $anneePlusUn";
	global $moisDepartDecompteHeures;
	
	// On rajoute le "0" devant le mois si il est <= 9
	$moisDepart = $moisDepartDecompteHeures;
	if ($moisDepartDecompteHeures <= 9)
	{
		$moisDepart = "0" . $moisDepartDecompteHeures;
	}

	if ($dateToImport >= "$annee-$moisDepart-01")
	{
		//	echo "<br>Nous sommes après le mois définie comme $moisDepart, donc à l'année A + année suivante<br>";
		$andDate	=	"and date >= '$annee-$moisDepart-01' and date < '$anneePlusUn-$moisDepart-01'";
		//echo "<br>condition respectée: $dateToImport >= $annee-$moisDepart-01<br>";
		$dateDepartRTT = "$annee-$moisDepart-01";
		#echo "<br>andDate: $andDate<br>";

	}
	if ($dateToImport < "$annee-$moisDepart-01")
	{
	//	echo "<br>Nous sommes avant le mois définie comme $moisDepart, donc à l'année A + année précédente<br>";
		$andDate	=	"and date < '$annee-$moisDepart-01' and date >= '$anneeMoinsUn-$moisDepart-01'";
		//echo "<br>condition respectée: $dateToImport < $annee-$moisDepart-01<br>";
		//echo "<br>dateToImport: $dateToImport, AND = and date < '$annee-$moisDepart-01' and date >= '$anneeMoinsUn-$moisDepart-01'<br>";
		$dateDepartRTT = "$anneeMoinsUn-$moisDepart-01";
		#echo "<br>andDate: $andDate<br>";


	}



	global $axe2_exclus_totaux;
	$listeExclure = "";
	if ($axe2_exclus_totaux != "")
	{
		// Permet d'exclure les axe2 sensés ne pas être comptés.
		$sql 		=	"SELECT idAxe2 FROM Axe2 WHERE codeAxe2 IN ($axe2_exclus_totaux)";
		//echo "<br>$sql <br>";
		try
		{
			$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		while ($donnees = $reponse->fetch())
		{
			$exclure	=	$donnees['idAxe2'];
			if ( $exclure != "" )
			{
				if ( $listeExclure == "" )
				{
					$listeExclure = " \"$exclure\" ";
				}
				if ( $listeExclure != "" )
				{
					$listeExclure = " \"$exclure\",$listeExclure ";
				}
			}
		}
		$reponse->closeCursor();
	}
	//echo $listeExclure;
	$andAxe2Exclus = "";
	if ($listeExclure != "")
	{
		$andAxe2Exclus = "AND Axe2_idAxe2 NOT IN ($listeExclure)";
	}

	//$congesAnnuel = congesAnnuel();
	//global $nbrConges;

	// Je vais chercher le nombre d'heure déclaré en tant que "congé"
	$sql 		=	"SELECT sum(totalHoraire) FROM Periodes WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' $andAxe2Exclus $andDate";
//	$sql 		=	"SELECT sum(totalHoraire) FROM Periodes WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and Axe2_idAxe2 = 47 $andDate";
	//echo "<br>--- $sql <br>";
	try
	{
		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	}		
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
	$total_annuel = 0;
	while ($donnees = $reponse->fetch())
	{
		$total_annuel	=	$donnees['sum(totalHoraire)'];
	}
	return $total_annuel;

}

// Voir si cette fonction est encore utile maintenant que j'ai ma table heure sup.
function heures_semaine_derniere($bdd, $dateToImport, $idUtilisateur)
{
	// Retourne les 7 derniers jours au lieu de la semaine, etc.
	$sql 		=	"SELECT totalHoraire FROM Periodes WHERE  WEEKOFYEAR('$dateToImport')-1 = WEEKOFYEAR(date) AND YEAR(date) = YEAR('$dateToImport') AND Utilisateurs_idUtilisateurs = $idUtilisateur";

	try
	{
		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	$total=0;
	while ($donnees = $reponse->fetch())
		{
			$heures			=	$donnees['totalHoraire'];
			$total				=	$total + $heures;
		}
	return $total;
}

function heuresContrat ($bdd, $idUtilisateur)
{
	$sql 		=	"SELECT nbrHeuresSemaine FROM Utilisateurs WHERE idUtilisateurs = $idUtilisateur";

	try
	{
		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	$total=0;
	while ($donnees = $reponse->fetch())
		{
			$heuresContrat	=	$donnees['nbrHeuresSemaine'];
		}
	return $heuresContrat;
}

function heuresSup($bdd, $dateToImport, $idUtilisateur)
{
	$sql 		=	"SELECT sum(nbrHeureSup) FROM HeureSup WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and date <= '$dateToImport'";

	try
	{
		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	while ($donnees = $reponse->fetch())
		{
			$heuresSup			=	$donnees['sum(nbrHeureSup)'];
		}
	return $heuresSup;
}

function heuresRachetees($bdd, $dateToImport, $idUtilisateur)
{
	$sql 		=	"SELECT sum(nbr) FROM RachatHeures WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and date <= '$dateToImport'";
	try
	{
		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	while ($donnees = $reponse->fetch())
		{
			$heuresRachetees			=	$donnees['sum(nbr)'];
		}
	return $heuresRachetees;
}

function congeRestant($bdd, $dateToImport, $idUtilisateur, $heuresContrat)
{

	$formatDate = new DateTime($dateToImport);
	$annee= $formatDate->format('Y');
	$anneeMoinsUn = $annee - 1;
	$anneePlusUn = $annee + 1;
	//echo "$annee, $anneeMoinsUn, $anneePlusUn";
	global $moisDepartAnneeConge;
	if ($moisDepartAnneeConge <= 9)
	{
		$moisDepartAnneeConge = "0" . $moisDepartAnneeConge;
	}

	if ($dateToImport >= "$annee-$moisDepartAnneeConge-01")
	{
	//	echo "<br>Nous sommes après le mois définie comme $moisDepartAnneeConge, donc à l'année A + année suivante<br>";
		$andDate	=	"and date >= '$annee-$moisDepartAnneeConge-01' and date < '$anneePlusUn-$moisDepartAnneeConge-01'";
		//echo "<br>condition respectée: $dateToImport >= $annee-$moisDepartAnneeConge-01";

	}
	if ($dateToImport < "$annee-$moisDepartAnneeConge-01")
	{
	//	echo "<br>Nous sommes avant le mois définie comme $moisDepartAnneeConge, donc à l'année A + année précédente<br>";
		$andDate	=	"and date < '$annee-$moisDepartAnneeConge-01' and date >= '$anneeMoinsUn-$moisDepartAnneeConge-01'";
		//echo "<br>condition respectée: $dateToImport < $annee-$moisDepartAnneeConge-01";
		//echo "<br>dateToImport: $dateToImport, AND = and date < '$annee-$moisDepartAnneeConge-01' and date >= '$anneeMoinsUn-$moisDepartAnneeConge-01'<br>";
	}

	$congesAnnuel = congesAnnuel();
	global $nbrConges;

	// Je vais chercher le nombre d'heure déclaré en tant que "congé"
	$sql 		=	"SELECT sum(totalHoraire) FROM Periodes WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and Axe2_idAxe2 like (select idAxe2 from Axe2 where nomAxe2 like 'Congé') $andDate";
//	$sql 		=	"SELECT sum(totalHoraire) FROM Periodes WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and Axe2_idAxe2 = 47 $andDate";
	//echo "<br>--- $sql <br>";
	try
	{
		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	while ($donnees = $reponse->fetch())
		{
			$congePrisHeures	=	$donnees['sum(totalHoraire)'];
			$congeRestant		=	($nbrConges - round($congePrisHeures / ($heuresContrat / 5)	,2));
		}
	return $congeRestant;
}

function congesAnnuel()
{
	global $bdd;
	// // Je vais chercher le nombre d'heure déclaré en tant que "congé"
	$sql 		=	"SELECT valeur FROM Configuration WHERE nom like 'nombre_jours_conges'";
	//echo "--- $sql <br>";
	try
	{
		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	while ($donnees = $reponse->fetch())
		{
			$congesAnnuel	=	$donnees['valeur'];
		}

	$reponse->closeCursor();
	return $congesAnnuel;
}



// function periodeGestionRTT()
// {

//    include ("importer_configuration.php");

// 	$sql		= "SELECT valeur FROM Configuration where nom like 'periode_gestion_RTT'";
// 	//echo "$sql";
// 	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
// 	while ($donnees = $reponse->fetch())
// 	{
// 		// Declaration des variables
// 		$periodeRTT		= $donnees['valeur'];
// 	}
// 	return $periodeRTT;
// 	$reponse->closeCursor();
// }

function rttRestant($bdd, $dateToImport, $idUtilisateur, $heuresContrat)
{
	global $nbrJoursRTT;
	global $typeGestionRTT;

	// Prise en compte du nombre de RTT par utilisateur.
	global $nbrRTT;

	$formatDate = new DateTime($dateToImport);
	$annee= $formatDate->format('Y');
	$anneeMoinsUn = $annee - 1;
	$anneePlusUn = $annee + 1;
	//echo "$annee, $anneeMoinsUn, $anneePlusUn";
	global $moisDepartAnneeRTT;
	
	// On rajoute le "0" devant le mois si il est <= 9
	if ($moisDepartAnneeRTT <= 9)
	{
		$moisDepartAnneeRTT = "0" . $moisDepartAnneeRTT;
	}

	if ($dateToImport >= "$annee-$moisDepartAnneeRTT-01")
	{
	//	echo "<br>Nous sommes après le mois définie comme $moisDepartAnneeRTT, donc à l'année A + année suivante<br>";
		$andDate	=	"and date >= '$annee-$moisDepartAnneeRTT-01' and date < '$anneePlusUn-$moisDepartAnneeRTT-01'";
		//echo "<br>condition respectée: $dateToImport >= $annee-$moisDepartAnneeRTT-01";
		$dateDepartRTT = "$annee-$moisDepartAnneeRTT-01";

	}
	if ($dateToImport < "$annee-$moisDepartAnneeRTT-01")
	{
	//	echo "<br>Nous sommes avant le mois définie comme $moisDepartAnneeRTT, donc à l'année A + année précédente<br>";
		$andDate	=	"and date < '$annee-$moisDepartAnneeRTT-01' and date >= '$anneeMoinsUn-$moisDepartAnneeRTT-01'";
		//echo "<br>condition respectée: $dateToImport < $annee-$moisDepartAnneeRTT-01";
		//echo "<br>dateToImport: $dateToImport, AND = and date < '$annee-$moisDepartAnneeRTT-01' and date >= '$anneeMoinsUn-$moisDepartAnneeRTT-01'<br>";
		$dateDepartRTT = "$anneeMoinsUn-$moisDepartAnneeRTT-01";

	}

	//$RTTAnnuel = RTTAnnuel();
	// echo "<br>dateDepartRTT = $dateDepartRTT";

	// Gestion des RTT par trimestres
	$moisToImport = $formatDate->format('m');

	// echo "<br>dateToImport: $dateToImport";
	// echo "<br>moisDepartAnneeRTT: $moisDepartAnneeRTT";

	//$dateToImportPHPTimestamp->modify('+3 month');

	//echo  date(‘d-m-Y’, strtotime(+3 month, $dateToImportPHPTimestamp));
	$trimestre1 =  date("Y-m-d", strtotime($dateDepartRTT." + 3 month"));
	$trimestre2 =  date("Y-m-d", strtotime($dateDepartRTT." + 6 month"));
	$trimestre3 =  date("Y-m-d", strtotime($dateDepartRTT." + 9 month"));
	$trimestre4 =  date("Y-m-d", strtotime($dateDepartRTT." + 12 month"));

	// echo "<br>trimestre1: $trimestre1";
	// echo "<br>trimestre2: $trimestre2";
	// echo "<br>trimestre3: $trimestre3";
	// echo "<br>trimestre4: $trimestre4";


	if ($typeGestionRTT == "Trimestre")
	{
		if ($dateToImport < $trimestre1)
		{
			//echo "<br>Trimestre 1<br>";
			$andTrimestre = "AND date >= '$dateDepartRTT' AND date < '$trimestre1'";
		}
		if ($dateToImport >= $trimestre1 && $dateToImport < $trimestre2)
		{
			//echo "<br>Trimestre 2<br>";
			$andTrimestre = "AND date >= '$trimestre1' AND date < '$trimestre2'";

		}
		if ($dateToImport >= $trimestre2 && $dateToImport < $trimestre3)
		{
			//echo "<br>Trimestre 3<br>";
			$andTrimestre = "AND date >= '$trimestre2' AND date < '$trimestre3'";
		}
		if ($dateToImport >= $trimestre3 && $dateToImport < $trimestre4)
		{
			//echo "<br>Trimestre 4<br>";
			$andTrimestre = "AND date >= '$trimestre3' AND date < '$trimestre4'";
		}
	}



	// Je vais chercher le nombre d'heure déclaré en tant que "congé"
	$sql 		=	"SELECT sum(totalHoraire) FROM Periodes WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and Axe2_idAxe2 like (select idAxe2 from Axe2 where nomAxe2 like 'RTT') $andDate $andTrimestre";
//	$sql 		=	"SELECT sum(totalHoraire) FROM Periodes WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and Axe2_idAxe2 = 47 $andDate";
	//echo "<br>--- $sql <br>";
	try
	{
		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	while ($donnees = $reponse->fetch())
		{
			$RTTPrisHeures	=	$donnees['sum(totalHoraire)'];
			$RTTRestant		=	($nbrRTT - round($RTTPrisHeures / ($heuresContrat / 5)	,2));

			if ($typeGestionRTT == "Trimestre")
			{
				$RTTRestant = ($nbrRTT / 4 - round($RTTPrisHeures / ($heuresContrat / 5)	,2));
			}

		}



	return $RTTRestant;

}

// function RTTAnnuel()
// {
// 	global $bdd;
// 	// // Je vais chercher le nombre d'heure déclaré en tant que "congé"
// 	$sql 		=	"SELECT valeur FROM Configuration WHERE nom like 'nombre_jours_RTT'";
// 	//echo "--- $sql <br>";
// 	try
// 	{
// 		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
// 	}
// 	catch(Exception $e)
// 	{
// 		// En cas d'erreur précédemment, on affiche un message et on arrête tout
// 		die('Erreur : '.$e->getMessage());
// 	}

// 	while ($donnees = $reponse->fetch())
// 		{
// 			$RTTAnnuel	=	$donnees['valeur'];
// 		}

// 	$reponse->closeCursor();
// 	return $RTTAnnuel;
// }


// function rttRestant($bdd, $dateToImport, $idUtilisateur, $heuresContrat)
// {
// 
// 	//$periodeRTT = "mensuel";
// 	$periodeRTT = periodeGestionRTT();
// 
// 	$formatDate = new DateTime($dateToImport);
// 	$mois= $formatDate->format('m');
// 	$annee= $formatDate->format('Y');
// 	//echo "<br>$mois, $annee";
// 
// 	if ($periodeRTT == "Trimestre")
// 	{
// 		if ($mois <= 03)
// 		{
// 		//	echo "<br>Premier trimestre<br>";
// 			$andDate	=	"and date >= '$annee-01-01' and date <= '$annee-03-31'";
// 		}
// 		if ($mois >= 4 && $mois <= 6)
// 		{
// 		//	echo "<br>Deuxieme trimestre<br>";
// 			$andDate	=	"and date >= '$annee-04-01' and date <= '$annee-06-30'";
// 		}
// 		if ($mois >= 7 && $mois <= 9)
// 		{
// 		//	echo "<br>Troisieme trimestre<br>";
// 			$andDate	=	"and date >= '$annee-07-01' and date <= '$annee-09-30'";
// 		}
// 		if ($mois >= 10)
// 		{
// 		//	echo "<br>Quatrieme trimestre<br>";
// 			$andDate	=	"and date >= '$annee-10-01' and date <= '$annee-12-31'";
// 		}
// 	}
// 
// 	if ($periodeRTT == "Année")
// 	{
// 		$andDate	=	"and date >= '$annee-01-01' and date <= '$annee-12-31'";
// 	}
// 
// 
// 	$RTTtrimestre = RTTtrimestre();
// 
// 	$sql 		=	"SELECT sum(totalHoraire) FROM Periodes WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and Axe2_idAxe2 like (select idAxe2 from Axe2 where nomAxe2 like 'RTT') $andDate";
// 	//echo "--- $sql <br>";
// 	try
// 	{
// 		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
// 	}
// 	catch(Exception $e)
// 	{
// 		// En cas d'erreur précédemment, on affiche un message et on arrête tout
// 		die('Erreur : '.$e->getMessage());
// 	}
// 
// 	while ($donnees = $reponse->fetch())
// 		{
// 			$rttPrisHeure		=	$donnees['sum(totalHoraire)'];
// 			$rttRestant		=	($RTTtrimestre - round($rttPrisHeure / ($heuresContrat / 5)	,2));
// 		}
// 	return $rttRestant;
// }

// function RTTtrimestre()
// {
// 	global $bdd;
// 	// // Je vais chercher le nombre d'heure déclaré en tant que "congé"
// 	$sql 		=	"SELECT valeur FROM Configuration WHERE nom like 'nombre_jours_RTT'";
// 	//echo "--- $sql <br>";
// 	try
// 	{
// 		$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
// 	}
// 	catch(Exception $e)
// 	{
// 		// En cas d'erreur précédemment, on affiche un message et on arrête tout
// 		die('Erreur : '.$e->getMessage());
// 	}
// 
// 	while ($donnees = $reponse->fetch())
// 		{
// 			$RTTtrimestre	=	$donnees['valeur'];
// 		}
// 
// 	$reponse->closeCursor();
// 	return $RTTtrimestre;
// }

$jour=calcul_heures("DAY", $bdd, $dateToImport, $idUtilisateur);
echo "$jour";

$semaine=calcul_heures("WEEKOFYEAR", $bdd, $dateToImport, $idUtilisateur);
echo ";$semaine";

$mois=calcul_heures("MONTH", $bdd, $dateToImport, $idUtilisateur);
echo ";$mois";

$annee=calcul_total_annuel($bdd, $dateToImport, $idUtilisateur);
echo ";$annee";

$heuresContrat=heuresContrat($bdd, $idUtilisateur);
echo ";$heuresContrat";

$totalSemaineDerniere=heures_semaine_derniere($bdd, $dateToImport, $idUtilisateur);
echo ";$totalSemaineDerniere";

$heuresSup=heuresSup($bdd, $dateToImport, $idUtilisateur);
echo ";$heuresSup";

$heuresRachetees=heuresRachetees($bdd, $dateToImport, $idUtilisateur);
echo ";$heuresRachetees";

$congeRestant=congeRestant($bdd, $dateToImport, $idUtilisateur, $heuresContrat);
echo ";$congeRestant";

$rttRestant = rttRestant($bdd, $dateToImport, $idUtilisateur, $heuresContrat);
echo ";$rttRestant";

?>
