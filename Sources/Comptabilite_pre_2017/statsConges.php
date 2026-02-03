<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<?php session_start(); ?>
<head>
<?php // Permet de rediriger vers l'acceuil si utilisateur non enregistré.
	$prenom = $_SESSION['prenom'];
	if (!$prenom)
	{
		header('Location: index.php'); 
	} 
?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- Mon thème -->
	<link rel="stylesheet" href="style.css" />
	<link rel="stylesheet" href="CSS/Delta/css/normalise.css"> 
	<link rel="icon" type="image/png" href="favicon.png" />

	
	
<script>
function rafraichirPage()
{
	location.href="statsConges.php";
}
function filtrerDates()
{
	date=document.getElementById('filtre_date').value;
	location.href="statsConges.php?date="+date;
}
function retourFiche()
{
	location.href="compta.php";
}
function demarrage()
{
	change_couleur_bouton_menu_general();
}

</script> 
</head>

<body onLoad='demarrage();'>

<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id=tableGlobale><tr ><td></td><td id="tableGlobale">


<!-- <table width=100%><tr><td width=3%></td><td> -->
	<!-- Permet de se connecter à la base Mysql-->
	<?php 
	include("connexion_base.php");
	include("importer_configuration.php");

	# Data:
	# $pauseDejeunee	=	( derniereHeureJournee - premiereHeureJournee ) - total_journee
	# 		OU	=	( premiereHeureAprem - derniereHeureMatin )
	$mois			=	"";
	$dateStat		=	$_GET['date'];
	$loginSession	=	$_SESSION['login'];
	$admin			=	$_SESSION['admin'];

	$andDateStat	=	"";
	?>
	

	<?php include("menu_general.php") ?>

	<table border=0 width=100%>
		<tr>	
			<td width=20% ></td>
			<td align=center><br/ >Renseignez un mois <br/><input type="text" id="filtre_date" name="filtre_date" onKeyPress="if (event.keyCode == 13) filtrerDates()" /><br/><FONT SIZE=2 COLOR="#888a85">exemples: le mois de fevrier 2013 = "02/13" ou la période de janvier à avril 2013 = "01/13 - 04/13"</FONT><br><br></td>
			<td width=28% ></td>	
		</tr>
		<tr>
			<td></td>
			<td align=center><input type=text id="bouton_valide" value="Valider"  onClick="filtrer()" /><br/><FONT SIZE=2 COLOR="#888a85">Ou appuyez sur ENTREE</FONT></td>
			<td></td>
		</tr>

	</table>

	<table width=100%>
		<tr>
			<td align=left><font color=blue> 
				<?php // Si un mois est renseigné, on l'indique
				if ($dateStat != "")
				{
					echo "Statistiques en cours sur le mois: $dateStat";	
				}
				if ($dateStat == "")
				{
					echo "Statistiques sur l'année comptable en cours.";
				}
				?>
				</font>
				<input type=text id="bouton_raz" value="Réinit. filtres"  onClick="rafraichirPage()" />			
				<br><br>
			</td>	
		</tr>	
	</table>	
	<?php

	function stat_conges($loginUtilisateur, $date)
	{
		global $bdd;
		global $loginSession;
		global $admin;
		global $andDateStat;
		//echo "loginSession = $loginSession <br>";
		// Si ce n'est pas un admin, on affiche les heures de la personne concernée uniquement.
		$andSession = "AND Periodes.Utilisateurs_login like '$loginSession'";
		if ( $admin == 1 )
		{
			$andSession = "";
		}
		$andLogin = "AND login like '$loginSession'";
		if ( $admin == 1 )
		{
			$andLogin = "";
		}

		// Si une date est indiquée, on ne va traiter que le ou les mois concerné.
		global $dateStat;

		// On supprime les espaces.
		$suprEspace = str_replace(CHR(32),"",$dateStat);
		$dateStat = $suprEspace;
		//echo "Date stat: $dateStat";

		if ( $dateStat == "")
		{
			global $moisDepartAnneeConge;
			if ($moisDepartAnneeConge <= 9)
			{
				$moisDepartAnneeConge = "0" . $moisDepartAnneeConge;
				//echo "moisDepartAnneeConge = $moisDepartAnneeConge";
			}

			$dateJour = date("Y-m-d");
			$moisJour = date("m");
			$anneeJour = date("Y");

			$anneeJourMoins1 = $anneeJour - 1 ;
			$anneeJourPlus1 = $anneeJour + 1 ;

			if ( $moisJour >= $moisDepartAnneeConge )
			{
				$andDateStat = " AND Periodes.date >= '$anneeJour"."-$moisDepartAnneeConge"."-01' AND Periodes.date <  '$anneeJourPlus1"."-$moisDepartAnneeConge"."-01' ";
				//echo "$andDateStat";
			} 
			if ( $moisJour < $moisDepartAnneeConge )
			{
				$andDateStat = " AND Periodes.date <= '$anneeJour"."-$moisDepartAnneeConge"."-01' AND Periodes.date >  '$anneeJourMoins1"."-$moisDepartAnneeConge"."-01' ";
				//echo "$andDateStat";
			}

			//$andDateStat = "AND Periodes.date =>  ";
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

//				$andDateStat = "AND  MONTH(Periodes.date) >= MONTH('$dateDebut') AND YEAR(Periodes.date) >= YEAR('$dateDebut') AND  MONTH(Periodes.date) <= MONTH('$dateFin') AND YEAR(Periodes.date) <= YEAR('$dateFin')";
				$andDateStat = "AND  Periodes.date >= '$dateDebut' AND  Periodes.date <= LAST_DAY('$dateFin') ";
				//echo "$andDateStat";
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

				$andDateStat = "AND  MONTH('$dateStat') = MONTH(Periodes.date) AND YEAR('$dateStat') = YEAR(Periodes.date)";
			}

		}


		// Si loginUtilisateur est renseigné dans l'appel de la fonction, on ne prend que lui. Sinon on affiche tout le monde.
	#	if ($loginUtilisateur != "")
	#	{
	#		$andLogin = "AND login like '$loginUtilisateur'";
	#	}

		
		$sql_login = "SELECT * FROM Utilisateurs WHERE 1=1 and active=1 $andLogin order by Nom";
		//echo "$sql_login <br>";
		try
		{
			$reponse_login = $bdd->query($sql_login) or die('Erreur SQL !<br>' .$sql_login. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}

		while ($donnees = $reponse_login->fetch())
		{

			$sommeTotale = 0;

			$idUtilisateur		=	$donnees['idUtilisateurs'];		
			$nom			=	$donnees['nom'];
			$prenom			=	$donnees['prenom'];
			$nbrHeuresSemaine	=	$donnees['nbrHeuresSemaine'];

			$textHeuresRachetees = heuresRachetees($idUtilisateur);
			



					// On prend, dans Periodes, tout ce qui a un ID axe2 correspondant à l' ID axe2 de la table Axe2 contenant les codes axe2 = 5xxx (50xx = congés). 
					$sql_conges = 'SELECT Periodes.Utilisateurs_login, Axe2.nomAxe2 , Periodes.Axe2_idAxe2, sum(Periodes.totalHoraire) as sommeHeures FROM `Periodes`, Axe2 where Periodes.Utilisateurs_idUtilisateurs = "'.$idUtilisateur.'" '.$andDateStat.' '.$andSession.' AND Periodes.`Axe2_idAxe2` = Axe2.`idAxe2` AND `Axe2`.`codeAxe2` like "50%" group by Axe2.idAxe2';
					//echo "$sql_conges";
					try
					{
						$reponse_conges = $bdd->query($sql_conges) or die('Erreur SQL !<br>' .$sql_conges. '<br>'. mysql_error());
					}
					catch(Exception $e)
					{
						// En cas d'erreur précédemment, on affiche un message et on arrête tout
						die('Erreur : '.$e->getMessage());
					}
					
					
					
			echo "<table border=0 width=100% ><tr align=left >	<th width=10% colspan=2 id=ongletVertNom>$prenom $nom</th>		<td align=left>	$textHeuresRachetees	</td>	</tr>";		
			echo "</table>";
					echo "<table id=tableOngletsVert width=100%>";
					
					
					
					while ($donnees = $reponse_conges->fetch())
					{
						$periodeLoginUtilisateur	=		$donnees['Utilisateurs_login'];
						$nomAxe2					=		$donnees['nomAxe2'];
						$idAxe2						=		$donnees['Axe2_idAxe2'];	
						$sommeHeures				=		$donnees['sommeHeures'];
						$sommeJournee				=		round($sommeHeures / ($nbrHeuresSemaine / 5)	,2);
						$sommeDemiJournee			=		round($sommeHeures / ($nbrHeuresSemaine / 10)	,2);

						// Si il y a des congés, on va chercher les dates.
						if ($idAxe2 != "")
						{
							$dateCongees = dateCongees($periodeLoginUtilisateur, $idAxe2, $andDateStat);
						}

						echo "<tr>	<td width=1%></td><td width=6% align=left>$nomAxe2</td>	<td width=7% align=center style='background-color: rgba(0, 0, 0, 0.1)'>$sommeJournee jours</td>	<td>$dateCongees</td></tr>";
						
						// Permet d'afficher la somme totale de congés prises (tout confondu).
						$sommeTotale	=	$sommeTotale + $sommeJournee;
					}
					$reponse_conges->closeCursor(); // Termine le traitement de la requête

					// On affiche le total de la journée.
					echo "<tr>	<td width=1%></td><th width=6% align=left id='petitTitre'>Total</th>	<td width=7% align=center style='background-color: rgba(0, 0, 0, 0.25)' >$sommeTotale jours</td>	<td></td>	</tr>";

			echo "</table><br/><br/>";
		}
		$reponse_login->closeCursor(); // Termine le traitement de la requête


	}

	function dateCongees($periodeLoginUtilisateur, $idAxe2, $andDateStat)
	{
		global $bdd;
		$dateCongees="";

		//echo "SELECT distinct(date) FROM Periodes WHERE Axe2_idAxe2 = $idAxe2 $andDateStat AND Utilisateurs_login like '$periodeLoginUtilisateur' order by date <br>";
		$sql_date_congees = "SELECT distinct(date) FROM Periodes WHERE Axe2_idAxe2 = $idAxe2 $andDateStat AND Utilisateurs_login like '$periodeLoginUtilisateur' order by date";

		try
		{
			$reponse_date_congees = $bdd->query($sql_date_congees) or die('Erreur SQL !<br>' .$sql_date_congees. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}

		while ($donnees = $reponse_date_congees->fetch())
		{
			$dateBrute = $donnees['date'];
			$date = date('d/m/y', strtotime($dateBrute));
			$dateCongees =  $dateCongees . $date . " -- " ;

		}
		$reponse_date_congees->closeCursor(); // Termine le traitement de la requête
		return $dateCongees;
	}

	function heuresRachetees($idUtilisateur)
	{

		global $bdd;
		global $loginSession;
		global $andDateStat;

		$nbrHeuresRachetees 	= 0;
		$totalRachatHeures	= 0;
		$dateRachatHeures	= "";

		// On transforme dans $andDateStat le "Periodes." en ""
		$andDateStat = str_replace("Periodes.","",$andDateStat);

		// On prend, dans RachatHeures tout ce qui correspond à l'ID de la personne. 
		$sql_rachatHeures = 'SELECT * FROM RachatHeures WHERE Utilisateurs_idUtilisateurs = "'.$idUtilisateur.'" '.$andDateStat.' ';
		//echo "$sql_rachatHeures";
		try
		{
			$reponse_rachatHeures = $bdd->query($sql_rachatHeures) or die('Erreur SQL !<br>' .$sql_rachatHeures. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		while ($donnees = $reponse_rachatHeures->fetch())
		{
			$dateBrute			=		$donnees['date'];
			$nbrHeuresRachetees		=		$donnees['nbr'];
			$loginUtilisateur		=		$donnees['Utilisateurs_login'];
			$idUtilisateur			=		$donnees['Utilisateurs_idUtilisateurs'];

			$date = date('d/m/y', strtotime($dateBrute));
			$dateRachatHeures =  $dateRachatHeures . $date . ", " ;

			$totalRachatHeures	=	$totalRachatHeures + $nbrHeuresRachetees;
		}
		$reponse_rachatHeures->closeCursor(); // Termine le traitement de la requête

		if ($totalRachatHeures != 0)
		{
			return "<font color=grey size=2>Sur cette période, un total de $totalRachatHeures heures ont été rachetées en date du: $dateRachatHeures</font>";	
		}		



	}


	stat_conges();
	?>

<!-- </td><td width=3%></td></tr></table> -->
<!-- Fin de la table de mise en page globale -->
</td><td></td></tr></table>

</body>
