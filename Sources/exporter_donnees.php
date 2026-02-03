<?php session_start();

include("connexion_base.php");
include("importer_configuration.php"); 

$loginSession		=	$_SESSION['login'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];

require("checkAdmin.php");


	$axe1		= $_GET['axe1'];
	$axe2 		= $_GET['axe2'];
	$axe3 		= $_GET['axe3'];
	$dateStat 	= $_GET['date'];

	$andDate = "";
	$andAxe1 = "";
	$andAxe2 = "";
	$andAxe3 = "";

	$descriptifExportCSV = "";

	$type_requete=$_GET['type_requete'];

	if ( $axe1 != "" )
	{
		$andAxe1 = "and Axe1_idAxe1 = $axe1";
	}

	if ( $axe2 != "" )
	{
		$andAxe2 = "and Axe2_idAxe2 = $axe2";
	}

	if ( $axe3 != "" )
	{
		$andAxe3 = "and Axe3_idAxe3 = $axe3";
	}

// envoi des headers csv
header('Content-Type: application/csv-tab-delimited-table');
// nommage du fichier avec la date du jour
header('Content-disposition: filename=export_Linott_du_'.date('Y-m-d').'.csv');






	////////////////// Prise en compte de la configuration d'exclusion de certains axes2
	global $axe2_exclus_totaux;
	$listeExclure = "";
	if ($axe2_exclus_totaux != "")
	{
		// Permet d'exclure les axe2 sensés ne pas être comptés.
		$sql 		=	"SELECT idAxe2 FROM Axe2 WHERE codeAxe2 IN ($axe2_exclus_totaux)";
		//echo "$sql <br>";
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


//////////////////// Traitement des dates selectionnées ////////////////////
		
		// On supprime les espaces.
		$suprEspace = str_replace(CHR(32),"",$dateStat);
		$dateStat = $suprEspace;

		if ( $dateStat == "")
		{
			$andDate = "AND YEAR(Periodes.date) = YEAR(CURDATE()) ";
		}

		if ($dateStat != "")
		{
			// Si la chaine contient un tiret, on sait que c'est une période et pas un seul mois.
			$texte = htmlspecialchars($dateStat);
			$verif = preg_match("#-#", "'.$texte.'");
			// $verif Vaudra true (1) si le tiret est dedans et false si contraire //


			// Si le tiret est présent
			if ($verif == 1) 
			{
				//echo "je suis dans la condition tiret.<br>";
				// on explode le "xx/xx - xx/xx" en deux "xx/xx" bien différents.
				list($debut,$fin) = explode('-',$dateStat);


				// On transforme mm/yy en yyyy/mm/jj pour mysql
				// On rajoute le premier jour du mois à notre date
				$debut = "01/" . $debut;
				$fin = "01/" . $fin;

				// Puis on explode tout
				list($jourDebut,$moisDebut,$anneeDebut) = explode('/',$debut);
				list($jourFin,$moisFin,$anneeFin) = explode('/',$fin);
				// et ensuite on leur donne le format voulu.
				$dateDebut = "20" . $anneeDebut.'-'.$moisDebut.'-'.$jourDebut;
				$dateFin = "20" . $anneeFin.'-'.$moisFin.'-'.$jourFin;

				//echo "Date debut: $dateDebut<br>";
				//echo "Date fin: $dateFin<br>";

				$andDate = "AND  MONTH(Periodes.date) >= MONTH('$dateDebut') AND YEAR(Periodes.date) >= YEAR('$dateDebut') AND  MONTH(Periodes.date) <= MONTH('$dateFin') AND YEAR(Periodes.date) <= YEAR('$dateFin')";
			}

			if ($verif == 0) 
			{
				//echo "je suis dans la condition sans tiret.<br>";

				// On transforme mm/yy en yyyy/mm/jj pour mysql
				// On rajoute le premier jour du mois à notre date
				$dateStat = "01/" . $dateStat;

				// Puis on explode tout
				list($jour,$mois,$annee) = explode('/',$dateStat);
				// et ensuite on lui donne le format voulu.
				$dateStat = "20" . $annee.'-'.$mois.'-'.$jour;

				$andDate = "AND  MONTH('$dateStat') = MONTH(Periodes.date) AND YEAR('$dateStat') = YEAR(Periodes.date)";
			}
		}
	

	
if ( $type_requete == "1")
{
	export1();
}
if ( $type_requete == "2")
{
	export2();
}
if ( $type_requete == "3")
{
	export3();
}
if ( $type_requete == "4")
{
	export4();
}
if ( $type_requete == "5")
{
	export5();
}
if ( $type_requete == "6")
{
	export6();
}
if ( $type_requete == "7")
{
	export7();
}
if ( $type_requete == "8")
{
	export8();
}

function export1()
{
	//Première ligne avec le noms des colonnes
	echo '"Code";"Nom";"Heures"'."\n";

	function select ($Axe)
	{
		global $bdd;

		global $andDate;
		global $andAxe1;
		global $andAxe2;
		global $andAxe3;
		global $andAxe2Exclus;

		// Si c'est un axe3 on ne demande pas le code comptable mais l'id de ce axe3.
		// On ajoute "ID axe3 " devant la sortie si c'est un axe3 pour ne pas qu'on confonde les ID de axe3 avec les codes comptables.
		$code = "code";
		$afficheIdAxe3 = "";
		if ( $Axe == "Axe3")
		{
			$code = "id";
			$afficheIdAxe3 = "ID_axe3_";
		}

		$sql		= "SELECT $code$Axe as codeComptable, nom$Axe as nomAxe , sum(totalHoraire) as heures 
							FROM Periodes, $Axe 
							WHERE $Axe" . "_id$Axe = id$Axe 
								AND Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 )
								$andAxe1 
								$andAxe2 
								$andAxe3 
								$andDate
								$andAxe2Exclus
							GROUP BY `$Axe" . "_id$Axe` 
							ORDER BY codeComptable
						";
		//echo "$sql <br>";
		$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
		while ($donnees = $reponse->fetch())
		{
			// Declaration des variables
			$codeComptable		= $donnees['codeComptable'];
			$nomAxe				= $donnees['nomAxe'];
			$heures				= $donnees['heures'];

			$heuresVirgules = str_replace(".", ",", $heures);

		echo "\"$afficheIdAxe3$codeComptable\";\"$nomAxe\";\"$heuresVirgules\"\n";

		}
		$reponse->closeCursor();

	}

	select(Axe1);
	select(Axe2);
	select(Axe3);
}

function export2()
{
	//Première ligne avec le noms des colonnes
	echo '"Code_Axe1";"Code_Axe2";"Code_Axe1_Axe2";"Somme";"Utilisateur"'."\n";

	
	//$sql2		= "SELECT  codeAxe1, codeAxe2, sum(`totalHoraire`) as nbr_heures, `Utilisateurs_login` as Utilisateur FROM`Periodes`, Axe1, Axe2 where `Axe1_idAxe1` like idAxe1 and `Axe2_idAxe2` like idAxe2 $andDate and Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) $andAxe2Exclus group by codeAxe1, codeAxe2, `Utilisateurs_login`";

	global $bdd;

	global $andDate;
	global $andAxe1;
	global $andAxe2;
	global $andAxe3;
	global $andAxe2Exclus;



	$sql = "SELECT  codeAxe1, codeAxe2, sum(`totalHoraire`) as nbr_heures, `Utilisateurs_login` as Utilisateur 
			FROM`Periodes`, Axe1, Axe2 
			WHERE `Axe1_idAxe1` = idAxe1 
				AND `Axe2_idAxe2` = idAxe2 
				AND Utilisateurs_idUtilisateurs IN (SELECT idUtilisateurs FROM Utilisateurs WHERE active = 1 ) 
				$andDate 
				$andAxe1 
				$andAxe2 
				$andAxe3 
				$andAxe2Exclus 
			GROUP BY codeAxe1, codeAxe2, `Utilisateurs_login`";

	//echo "$sql <br>";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$codeAxe1			= $donnees['codeAxe1'];
		$codeAxe2			= $donnees['codeAxe2'];
		$heures				= $donnees['nbr_heures'];
		$agent				= $donnees['Utilisateur'];

		$heuresVirgules = str_replace(".", ",", $heures);
		$codeAxeGlobal = "$codeAxe1" . "$codeAxe2";

		echo "\"$codeAxe1\";\"$codeAxe2\";\"$codeAxeGlobal\";\"$heuresVirgules\";\"$agent\"\n";

	}
	$reponse->closeCursor();

}

function export3()
{
	//Première ligne avec le noms des colonnes
	echo '"Code_Axe1";"Code_Axe2";"Code_Axe1_Axe2";"heures";"Pourcentage journée";"Utilisateur";"date"'."\n";

	global $bdd;

	global $andDate;
	global $andAxe1;
	global $andAxe2;
	global $andAxe3;
	global $andAxe2Exclus;



	$sql = "SELECT  codeAxe1, codeAxe2, sum(`totalHoraire`) as nbr_heures, `Utilisateurs_login` as Utilisateur , date
			FROM `Periodes`, Axe1, Axe2 
			WHERE `Axe1_idAxe1` = idAxe1 
				AND `Axe2_idAxe2` = idAxe2 $andDate
				AND Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) 
				$andAxe1 
				$andAxe2 
				$andAxe3
				$andAxe2Exclus 				
			GROUP BY codeAxe1, codeAxe2, `Utilisateurs_login`, date
			ORDER BY codeAxe1, codeAxe2, date asc, Utilisateur";
	//echo "$sql <br>";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$codeAxe1			= $donnees['codeAxe1'];
		$codeAxe2			= $donnees['codeAxe2'];
		$heures				= $donnees['nbr_heures'];
		$agent				= $donnees['Utilisateur'];
		$date 				= $donnees['date'];
		
		$totalJourneeEnCours = retourSql("select sum(totalHoraire) as somme from Periodes where 1=1 $andAxe2Exclus and date like \"$date\" and Utilisateurs_login like \"$agent\"");
		//echo " ***toto: $totalJourneeEnCours<br>";
		$pourcentageJournee = round($heures / $totalJourneeEnCours * 100, 2);
		$pourcentageJournee = str_replace(".", ",", $pourcentageJournee);
		
		// On converti la date du format Mysql vers format calc.
		$dateCalc = date("d/m/Y", strtotime($date));		
		
		$heuresVirgules = str_replace(".", ",", $heures);
		$codeAxeGlobal = "$codeAxe1" . "$codeAxe2";

		echo "\"$codeAxe1\";\"$codeAxe2\";\"$codeAxeGlobal\";\"$heuresVirgules\";\"$pourcentageJournee\";\"$agent\";\"$dateCalc\"\n";

	}
	$reponse->closeCursor();

}

function export4()
{
	//Première ligne avec le noms des colonnes
	echo '"Code_Axe1";"Code_Axe2";"Code_Axe1_Axe2";"heures";"Pourcentage du total"'."\n";

	global $bdd;

	global $andDate;
	global $andAxe1;
	global $andAxe2;
	global $andAxe3;
	global $andAxe2Exclus;



	$sql = "SELECT  codeAxe1, codeAxe2, sum(`totalHoraire`) as nbr_heures
			FROM `Periodes`, Axe1, Axe2 
			WHERE `Axe1_idAxe1` = idAxe1 
				AND `Axe2_idAxe2` = idAxe2
 				AND Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) 
				$andDate 
				$andAxe1 
				$andAxe2 
				$andAxe3
				$andAxe2Exclus 			
			GROUP BY codeAxe1, codeAxe2
			ORDER BY codeAxe1, codeAxe2";

	//echo "$sql <br>";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$codeAxe1			= $donnees['codeAxe1'];
		$codeAxe2			= $donnees['codeAxe2'];
		$heures				= $donnees['nbr_heures'];
		$date 				= $donnees['date'];
		
		$totalJourneeEnCours = retourSql("select sum(totalHoraire) as somme from Periodes where 1=1 $andDate $andAxe2Exclus AND Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) " , "somme");
		//echo " ***toto: $totalJourneeEnCours<br>";
		$pourcentageJournee = round($heures / $totalJourneeEnCours * 100, 4);
		$pourcentageJournee = str_replace(".", ",", $pourcentageJournee);
		
		
		$heuresVirgules = str_replace(".", ",", $heures);
		$codeAxeGlobal = "$codeAxe1" . "$codeAxe2";

		echo "\"$codeAxe1\";\"$codeAxe2\";\"$codeAxeGlobal\";\"$heuresVirgules\";\"$pourcentageJournee\"\n";

	}
	$reponse->closeCursor();
}


function export5()
{
	//Première ligne avec le noms des colonnes
	echo '"Code_Axe1";"Code_Axe2";"Code_Axe3";"Code_Axe1_Axe2";"heures";"Pourcentage du total"'."\n";

	global $bdd;

	global $andDate;
	global $andAxe1;
	global $andAxe2;
	global $andAxe3;
	global $andAxe2Exclus;



	$sql = "SELECT  codeAxe1, codeAxe2, codeAxe3, sum(`totalHoraire`) as nbr_heures
			FROM `Periodes`, Axe1, Axe2, Axe3
			WHERE `Axe1_idAxe1` = idAxe1 
				AND `Axe2_idAxe2` = idAxe2 
				AND `Axe3_idAxe3` = idAxe3
				AND Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) 			 
				$andDate 
				$andAxe1 
				$andAxe2 
				$andAxe3
				$andAxe2Exclus 			
			GROUP BY codeAxe1, codeAxe2, codeAxe3
			ORDER BY codeAxe1, codeAxe2, codeAxe3";

	//echo "$sql <br>";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$codeAxe1			= $donnees['codeAxe1'];
		$codeAxe2			= $donnees['codeAxe2'];
		$codeAxe3			= $donnees['codeAxe3'];
		$heures				= $donnees['nbr_heures'];
		$date 				= $donnees['date'];
		
		$totalJourneeEnCours = retourSql("select sum(totalHoraire) as somme from Periodes where 1=1 $andDate $andAxe2Exclus AND Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) " , "somme");
		//echo " ***toto: $totalJourneeEnCours<br>";
		$pourcentageJournee = round($heures / $totalJourneeEnCours * 100, 4);
		$pourcentageJournee = str_replace(".", ",", $pourcentageJournee);
		
		
		$heuresVirgules = str_replace(".", ",", $heures);
		$codeAxeGlobal = "$codeAxe1" . "$codeAxe2" . "$codeAxe3";

		echo "\"$codeAxe1\";\"$codeAxe2\";\"$codeAxe3\";\"$codeAxeGlobal\";\"$heuresVirgules\";\"$pourcentageJournee\"\n";

	}
	$reponse->closeCursor();
}

function export6()
{
	//Première ligne avec le noms des colonnes
	echo '"Code_Axe1";"Code_Axe2";"Code_Axe1_Axe2";"heures";"Pourcentage journée";"Utilisateur";"date"'."\n";

	global $bdd;

	global $andDate;
	global $andAxe1;
	global $andAxe2;
	global $andAxe3;


	$sql = "SELECT  codeAxe1, codeAxe2, sum(`totalHoraire`) as nbr_heures, `Utilisateurs_login` as Utilisateur , date
			FROM`Periodes`, Axe1, Axe2 
			WHERE `Axe1_idAxe1` = idAxe1 
				AND `Axe2_idAxe2` = idAxe2 
				AND Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) 
				$andDate 
				$andAxe1 
				$andAxe2 
				$andAxe3 
			GROUP BY codeAxe1, codeAxe2, `Utilisateurs_login`, date
			ORDER BY codeAxe1, codeAxe2, date asc, Utilisateur";
	//echo "$sql <br>";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$codeAxe1			= $donnees['codeAxe1'];
		$codeAxe2			= $donnees['codeAxe2'];
		$heures				= $donnees['nbr_heures'];
		$agent				= $donnees['Utilisateur'];
		$date 				= $donnees['date'];
		
		$totalJourneeEnCours = retourSql("select sum(totalHoraire) as somme from Periodes where 1=1 and date like \"$date\" and Utilisateurs_login like \"$agent\"");
		//echo " ***toto: $totalJourneeEnCours<br>";
		$pourcentageJournee = round($heures / $totalJourneeEnCours * 100, 2);
		$pourcentageJournee = str_replace(".", ",", $pourcentageJournee);
		
		// On converti la date du format Mysql vers format calc.
		$dateCalc = date("d/m/Y", strtotime($date));		
		
		$heuresVirgules = str_replace(".", ",", $heures);
		$codeAxeGlobal = "$codeAxe1" . "$codeAxe2";

		echo "\"$codeAxe1\";\"$codeAxe2\";\"$codeAxeGlobal\";\"$heuresVirgules\";\"$pourcentageJournee\";\"$agent\";\"$dateCalc\"\n";

	}
	$reponse->closeCursor();

}

function export7()
{
	//Première ligne avec le noms des colonnes
	echo '"Date";"Login utilisateur";"Nombre heures"'."\n";

	
	//$sql2		= "SELECT  codeAxe1, codeAxe2, sum(`totalHoraire`) as nbr_heures, `Utilisateurs_login` as Utilisateur FROM`Periodes`, Axe1, Axe2 where `Axe1_idAxe1` like idAxe1 and `Axe2_idAxe2` like idAxe2 $andDate and Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) $andAxe2Exclus group by codeAxe1, codeAxe2, `Utilisateurs_login`";

	global $bdd;

	global $andDate;
	global $andAxe1;
	global $andAxe2;
	global $andAxe3;
	global $andAxe2Exclus;



	//$sql = "SELECT  codeAxe1, codeAxe2, sum(`totalHoraire`) as nbr_heures, `Utilisateurs_login` as Utilisateur FROM`Periodes`, Axe1, Axe2 where `Axe1_idAxe1` like idAxe1 and `Axe2_idAxe2` like idAxe2 $andDate $andAxe1 $andAxe2 $andAxe3 and Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) $andAxe2Exclus group by codeAxe1, codeAxe2, `Utilisateurs_login`";
	
	$sql = "SELECT date, `Utilisateurs_login`, sum(`totalHoraire`) as 'heures' FROM `Periodes`
			WHERE 1 = 1 
				$andDate 
				$andAxe1 
				$andAxe2 
				$andAxe3
			GROUP BY `Utilisateurs_idUtilisateurs`, `date`
			ORDER BY `date`";
	//echo "$sql <br>";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$date			= $donnees['date'];
		$login_utilisateur			= $donnees['Utilisateurs_login'];
		$heures				= $donnees['heures'];

		$heuresVirgules = str_replace(".", ",", $heures);

		echo "\"$date\";\"$login_utilisateur\";\"$heuresVirgules\"\n";

	}
	$reponse->closeCursor();

}

function export8()
{
	//Première ligne avec le noms des colonnes
	echo '"Date";"Login utilisateur";"Numéro période";"Heure de départ";"Heure de fin";"Code Axe 1";"Nom Axe 1";"Code Axe 2";"Nom Axe 2";"Code Axe 3";"Nom Axe 3";"Durée de la période"'."\n";

	
	//$sql2		= "SELECT  codeAxe1, codeAxe2, sum(`totalHoraire`) as nbr_heures, `Utilisateurs_login` as Utilisateur FROM`Periodes`, Axe1, Axe2 where `Axe1_idAxe1` like idAxe1 and `Axe2_idAxe2` like idAxe2 $andDate and Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) $andAxe2Exclus group by codeAxe1, codeAxe2, `Utilisateurs_login`";

	global $bdd;

	global $andDate;
	global $andAxe1;
	global $andAxe2;
	global $andAxe3;
	global $andAxe2Exclus;



	//$sql = "SELECT  codeAxe1, codeAxe2, sum(`totalHoraire`) as nbr_heures, `Utilisateurs_login` as Utilisateur FROM`Periodes`, Axe1, Axe2 where `Axe1_idAxe1` like idAxe1 and `Axe2_idAxe2` like idAxe2 $andDate $andAxe1 $andAxe2 $andAxe3 and Utilisateurs_idUtilisateurs in (select idUtilisateurs from Utilisateurs where active = 1 ) $andAxe2Exclus group by codeAxe1, codeAxe2, `Utilisateurs_login`";
	$tableJourSemaine = array("lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche"); 	

	$sql = "SELECT WEEKDAY(date) as jourSemaine, DATE_FORMAT(date, '%d/%m/%Y') as date, `Utilisateurs_login`, numeroLigne, horaireDebut, HoraireFin, Axe1_idAxe1, nomAxe1, Axe2_idAxe2, nomAxe2, Axe3_idAxe3, nomAxe3, totalHoraire FROM `Periodes`
			INNER JOIN Axe1 ON Periodes.Axe1_idAxe1 = Axe1.idAxe1
			INNER JOIN Axe2 ON Periodes.Axe2_idAxe2 = Axe2.idAxe2
			INNER JOIN Axe3 ON Periodes.Axe3_idAxe3 = Axe3.idAxe3
			WHERE 1 = 1 $andDate $andAxe1 $andAxe2 $andAxe3
			ORDER BY Utilisateurs_login, `date`, numeroLigne";
	//echo "$sql <br>";
	$reponse 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donnees = $reponse->fetch())
	{
		// Declaration des variables
		$jourSemaine			= $tableJourSemaine[$donnees['jourSemaine']];
		$date					= $donnees['date'];
		$login_utilisateur		= $donnees['Utilisateurs_login'];
		$numeroLigne			= $donnees['numeroLigne'];
		$horaireDebut			= $donnees['horaireDebut'];
		$HoraireFin				= $donnees['HoraireFin'];
		$idAxe1					= $donnees['Axe1_idAxe1'];
		$idAxe2					= $donnees['Axe2_idAxe2'];
		$idAxe3					= $donnees['Axe3_idAxe3'];
		$nomAxe1				= $donnees['nomAxe1'];
		$nomAxe2				= $donnees['nomAxe2'];
		$nomAxe3				= $donnees['nomAxe3'];
		$totalHoraire			= $donnees['totalHoraire'];

		$horaireDebut 	= str_replace(".", ",", $horaireDebut);
		$HoraireFin 	= str_replace(".", ",", $HoraireFin);
		$totalHoraire 	= str_replace(".", ",", $totalHoraire);

		echo "\"$jourSemaine $date\";\"$login_utilisateur\";\"$numeroLigne\";\"$horaireDebut\";\"$HoraireFin\";\"$idAxe1\";\"$nomAxe1\";\"$idAxe2\";\"$nomAxe2\";\"$idAxe3\";\"$nomAxe3\";\"$totalHoraire\"\n";

	}
	$reponse->closeCursor();

}




function retourSql($sql , $valeurAttendue)
{
	global $bdd;
	//echo " $sql <br>";
	$reponseRetour 	= $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
	while ($donneesRetour = $reponseRetour->fetch())
	{
		// Declaration des variables
		$retour = $donneesRetour["somme"];
	}
	$reponseRetour->closeCursor();

	return $retour;
}


?>
