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
	
	<TITLE>Linott: Statistiques par axes</TITLE>

<script>
function rafraichirPage()
{
	location.href="statsAxes.php";
}
function filtrer()
{
	date="";
	axe1="";
	axe2="";
	axe3="";

	date=document.getElementById('filtre_date').value;
	axe1=document.getElementById('id_filtre_axe1').value;
	axe2=document.getElementById('id_filtre_axe2').value;
	axe3=document.getElementById('id_filtre_axe3').value;

	location.href="statsAxes.php?date="+date+"&axe1="+axe1+"&axe2="+axe2+"&axe3="+axe3;
}
function retourFiche()
{
	location.href="compta.php";
}
function affichage_popup(nom_de_la_page, nom_interne_de_la_fenetre)
{
	window.open (nom_de_la_page, nom_interne_de_la_fenetre, config='height=700, width=900, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no')
}

function afficherTotal()
{

	
	totalAxe1=document.getElementById('totalAxe1').value;
	totalAxe2=document.getElementById('totalAxe2').value;
	totalAxe3=document.getElementById('totalAxe3').value;

	document.getElementById('SommeTousUtilisateursAxe1').value = totalAxe1;
	document.getElementById('SommeTousUtilisateursAxe2').value = totalAxe2;
	document.getElementById('SommeTousUtilisateursAxe3').value = totalAxe3;
	
}

function exporter()
{
	pageEnCours = document.location.href;
	argumentsPage = pageEnCours.split("?");
	destination = argumentsPage[1];
	//document.location.href = "exporter_donnees.php?"+destination;
	document.location.href = "choix_type_export.php?"+destination;
}

// Permet d'update un champ depuis un popup (la popup doit l'appeler).
function updateChamp(id, valeur)
{
	document.getElementById(id).value= valeur;
	//filtrer();
}
function demarrage()
{
	change_couleur_bouton_menu_general();
	afficherTotal();
}

</script> 
</head>

<body onLoad="demarrage();">
<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id=tableGlobale><tr ><td></td><td id="tableGlobale">
<!--<table width=100%><tr><td width=3%></td><td>-->
<!-- Permet de se connecter à la base Mysql-->
<?php 		include("connexion_base.php");
			include("importer_configuration.php");
# Data:
# $pauseDejeunee	=	( derniereHeureJournee - premiereHeureJournee ) - total_journee
# 		OU	=	( premiereHeureAprem - derniereHeureMatin )
$mois			=	"";
$dateStat		=	$_GET['date'];
$axe1Stat		=	$_GET['axe1'];
$axe2Stat		=	$_GET['axe2'];
$axe3Stat 		=	$_GET['axe3'];
$loginSession	=	$_SESSION['login'];
$admin			=	$_SESSION['admin'];


?>
<?php include("menu_general.php") ?>

<table border="0" cellspacing="0" cellpadding="0" align=center width=100%>
	<tr>	
		<td width=21% align=left></td>	
		<td width=50% colspan=3 align=center></td> 
		<td></td>	
	</tr>
	<tr>
		<td></td>
		<td align=center><br />Renseignez un mois <br/><input type="text" id="filtre_date" name="filtre_date" onKeyPress="if (event.keyCode == 13) filtrer()" />
			<br/><FONT SIZE=2 COLOR="#888a85">exemples: le mois de fevrier 2013 = "02/13" <br>ou la période de janvier à avril 2013 = "01/13 - 04/13"</FONT></td>	
		<td></td>
	</tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" align=center width=100%>
	<tr>	
		<td width=21% align=left></td>	
		<td width=50% colspan=3 align=center></td> 
		<td></td>	
	</tr>
	<tr>
		<td><br>
		</td>
	</tr>

	<tr>	
		<td></td>
		<td align=center>Renseignez un axe1 <br/><input type="text" id="filtre_axe1" name="filtre_axe1" onClick="javascript:affichage_popup('fenetre_choix_axe1.php','popup_axe1_de_stats')" onKeyPress="if (event.keyCode == 13) filtrer()" />	<input type="hidden" id="id_filtre_axe1" />	<br/><FONT SIZE=2 COLOR="#888a85"></FONT></td>
		<td align=center>Renseignez un axe2 <br/><input type="text" id="filtre_axe2" name="filtre_axe2" onClick="javascript:affichage_popup('fenetre_choix_axe2.php','popup_axe2_de_stats')" onKeyPress="if (event.keyCode == 13) filtrer()" />	<input type="hidden" id="id_filtre_axe2" />	<br/><FONT SIZE=2 COLOR="#888a85"></FONT></td>
		<td align=center>Renseignez un axe3 <br/><input type="text" id="filtre_axe3" name="filtre_axe3" onClick="javascript:affichage_popup('fenetre_choix_axe3.php','popup_axe3_de_stats')" onKeyPress="if (event.keyCode == 13) filtrer()" />	<input type="hidden" id="id_filtre_axe3" />	<br/><FONT SIZE=2 COLOR="#888a85"></FONT></td>		
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td align=center><br/><input type=text id="bouton_valide" value="Valider"  onClick="filtrer()" /> <br/><FONT SIZE=2 COLOR="#888a85">Ou appuyez sur ENTREE</FONT></td>
		<td></td>
		
	</tr>
</table>

<br/>
<table border=0>
	<tr>	
		<td align=left width=10%>
			<br />
			<br />
			<br />
			<input type=text id="bouton_raz" value="Réinit. filtres"  onClick="rafraichirPage()" />	
		</td>

		<td width=1%>
		</td>

		<td align=left width=50%><font color=blue> 
			<br />
			<br />
			<br />
		<?php // Si un mois est renseigné, on l'indique

		echo "Statistiques sur ";
		if ($dateStat != "")
		{
			echo " le mois <FONT color=red> $dateStat </FONT>";	
		}
		if ($dateStat == "")
		{
			echo " l'année civile en cours";
		}
		if ($axe1Stat != "")
		{
			echo " et l'axe1 <FONT color=red>n°$axe1Stat</FONT>";
		}
		if ($axe2Stat != "")
		{
			echo " et l'axe2 <FONT color=red>n°$axe2Stat</FONT>";
		}
		if ($axe3Stat != "")
		{
			echo " et l'axe3 <FONT color=red>n°$axe3Stat</FONT>";
		}
		if ($axe1Stat == "" && $axe2Stat == "" && $axe3Stat == "")
		{
			echo " et tous les axes";
		}
		echo ".   ";
		?>
		</font><br/>	
		</td>

		<td >


		<?php 
			if ($admin == 1)
			{
			echo 
			"	<table border=0 width=100%>
					<tr><th id='ongletBleu' >Exporter les données</th>
						<td width=25%></td>
					</tr>
				</table>
				<table border=0 width=100% id='tableOngletsBleu'>
				    <tr>
					    <td width=1%></td>
					    <td width=75%>Vous permet d'exporter les données comptables au format .csv (prend en compte les filtres en cours).</td>
					    <td width=1%></td>
					    <td align=center><br><input type=text id='bouton_exporter' value='Exporter'  onClick='javascript:exporter();' /></td>
				    	<td width=1%></td>
				    </tr>
				    <tr>
				    	<td></td>
				    </tr>
				</table>
				" ;
			}
		?>



		</td>
	</tr>	
</table>	
<?php

function select_periode($where)
{
	global $bdd;
	$sql = "SELECT * FROM Periodes where $where" ;
	try
	{
		$reponse = $bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}
		
		while ($donnees = $reponse->fetch())
		{
			$periodeID		=		$donnees['idHoraires'];
			$periodeDate		=		$donnees['date'];
			$periodeDebut		=		$donnees['horaireDebut'];
			$periodeFin		=		$donnees['horaireFin'];
			$periodeDuree		=		$donnees['totalHoraire'];
			$periodeIdUtilisateur	=		$donnees['Utilisateurs_idUtilisateurs'];
			$periodeLoginUtilisateur=		$donnees['Utilisateurs_login'];
			$periodeSection		=		$donnees['Section_idSection'];
			$periodeIdAxe1		=		$donnees['Axe1_idAxe1'];
			$periodeIdAxe2		=		$donnees['Axe2_idAxe2'];
			$periodeIdaxe3	=		$donnees['Axe3_idAxe3'];
			$periodeNumeroLigne	=		$donnees['totalHoraire'];
	
			echo "$periodeLoginUtilisateur";

		}
		$reponse_absence->closeCursor(); // Termine le traitement de la requête	
}

function stat_axes($loginUtilisateur, $date)
{
	global $bdd;
	global $loginSession;
	global $admin;
	global $afficherCodesComptablesRecap;
	//echo "loginSession = $loginSession <br>";
	// Si ce n'est pas un admin, on affiche que les heures pour chaque personne.
	$andSession = "AND Periodes.Utilisateurs_login like '$loginSession'";
	if ( $admin == 1)
	{
		$andSession = "";
	}
	$andLogin = "AND login like '$loginSession'";
	if ( $admin == 1 )
	{
		$andLogin = "";
	}

	// Si une date est indiquée, on ne va traiter que le mois concerné.
	global $dateStat;

	// On supprime les espaces.
	$suprEspace = str_replace(CHR(32),"",$dateStat);
	$dateStat = $suprEspace;
	//echo "Date stat: $dateStat";
		
	if ( $dateStat == "")
	{
		$andDateStat = "AND YEAR(Periodes.date) = YEAR(CURDATE()) ";
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

			//$andDateStat = "AND  MONTH(Periodes.date) >= MONTH('$dateDebut') AND YEAR(Periodes.date) >= YEAR('$dateDebut') AND  MONTH(Periodes.date) <= MONTH('$dateFin') AND YEAR(Periodes.date) <= YEAR('$dateFin')";
			$andDateStat = "AND  Periodes.date >= '$dateDebut' AND  Periodes.date <= LAST_DAY('$dateFin') ";

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


	// Si un axe1 est indiqué, on le prend en compte dans le select
	global $axe1Stat;
	if ($axe1Stat != "")
	{
		$andAxe1Stat = "AND Axe1_idAxe1 = $axe1Stat";
	}
	// Si un axe2 est indiqué, on le prend en compte dans le select
	global $axe2Stat;
	if ($axe2Stat != "")
	{
		$andAxe2Stat = "AND Axe2_idAxe2 = $axe2Stat";
	}
	// Si un axe3 est indiqué, on le prend en compte dans le select
	global $axe3Stat;
	if ($axe3Stat != "")
	{
		$andAxe3Stat = "AND Axe3_idAxe3 = $axe3Stat";
	}


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
		if ( $admin == 1)
		{
			echo "<table border=0 width=100%><tr><td width=3%></td><td>		<br />Attention: Votre configuration dans la section \"administration\" exclue de ces statistiques les \"Axes2\" dont les codes comptables sont: $axe2_exclus_totaux.<br /><br />		</td></tr></table>";
		}
	}







	// Si loginUtilisateur est renseigné dans l'appel de la fonction, on ne prend que lui. Sinon on affiche tout le monde.
#	if ($loginUtilisateur != "")
#	{
#		$andLogin = "AND login like '$loginUtilisateur'";
#	}

	
	$sql_login = "SELECT * FROM Utilisateurs WHERE 1=1 and active=1 $andLogin ORDER BY Nom";
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

		$idUtilisateur		=	$donnees['idUtilisateurs'];		
		$nom				=	$donnees['nom'];
		$prenom				=	$donnees['prenom'];
		$nbrHeuresSemaine	=	$donnees['nbrHeuresSemaine'];

	// Une table par utilisateur.
	echo "<table border=0 width=100% ><tr><td>";
	echo "<tr>";
	echo "<td colspan=3>";
	
		// Une petite table pour le haut de l'onglet
		echo "<table border=0 width=30% ><tr align=left >	<th id=ongletVertNom>$prenom $nom</th></tr></table>";
	
		// Une table contenant tous les axes et axe3
		echo "<table border=0 width=100% id=tableOngletsVert><tr>";
		//////////////////////////// AXE 1 /////////////////////////////////////
		
		// Une table contenant toutes les heures et les noms des axes et axe3
		echo "<td width=37%>";	
			echo "<table border=0 width=100% >";		
				// On prend, dans Periodes, tout ce qui a un ID axe1 correspondant à l' ID axe1 de la table Axe1 contenant les codes axe1 = 5xxx (50xx = congés). 
				$sql_axe1 = 'SELECT Periodes.Utilisateurs_login, Axe1.nomAxe1 , Periodes.Axe1_idAxe1, sum(Periodes.totalHoraire) as sommeHeures, Axe1.codeAxe1 FROM `Periodes`, Axe1 where Periodes.Utilisateurs_idUtilisateurs = "'.$idUtilisateur.'" '.$andDateStat.' '.$andAxe1Stat.' '.$andAxe2Stat.' '.$andAxe3Stat.'  '.$andSession.' AND Periodes.`Axe1_idAxe1` = Axe1.`idAxe1` '.$andAxe2Exclus.' group by Axe1.idAxe1 order by sommeHeures desc';
				//echo "$sql_axe1";
				try
				{
					$reponse_axe1 = $bdd->query($sql_axe1) or die('Erreur SQL !<br>' .$sql_axe1. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				$totalHeuresUtilisateurAxe1=0;
				while ($donnees = $reponse_axe1->fetch())
				{
					$periodeLoginUtilisateur=		$donnees['Utilisateurs_login'];
					$nomAxe1				=		$donnees['nomAxe1'];
					$idAxe1					=		$donnees['Axe1_idAxe1'];	
					$sommeHeures		=		$donnees['sommeHeures'];
					$sommeJournee		=		round($sommeHeures / ($nbrHeuresSemaine / 5)	,2);
					$sommeDemiJournee	=		round($sommeHeures / ($nbrHeuresSemaine / 10)	,2);

					$code_axe1="";
					if ( $afficherCodesComptablesRecap == "checked" )
					{
						$code_axe1=$donnees['codeAxe1'];
						$code_axe1="$code_axe1 - ";
					}

					// Si il y a des congés, on va chercher les dates.
					if ($idAxe1 != "")
					{
						$dateCongees = dateCongees($periodeLoginUtilisateur, $idAxe1, $andDateStat);
					}

			// Table pour l'axe 1
			echo "<tr> <td style='background-color: rgba(0, 0, 0, 0.1)' width=20% align=center>$sommeHeures</td>	<td width=80%>$code_axe1 $nomAxe1</td>		</tr>";

				$totalHeuresUtilisateurAxe1 = 	$totalHeuresUtilisateurAxe1 + $sommeHeures;
				$totalGlobalAxe1	=	$sommeHeures + $totalGlobalAxe1;
				}
			echo "</table>";
				$reponse_axe1->closeCursor(); // Termine le traitement de la requête
		echo "</td>";
		///////////////////////// FIN AXE 1 ///////////////////////////////////



		//////////////////////////// AXE 2 /////////////////////////////////////
		
		// Une table contenant toutes les heures et les noms des axes et axe3
		echo "<td width=37%>";	
			echo "<table border=0 width=100%>";
				// On prend, dans Periodes, tout ce qui a un ID axe2 correspondant à l' ID axe2 de la table Axe2 contenant les codes axe2 = 5xxx (50xx = congés). 
				$sql_axe2 = 'SELECT Periodes.Utilisateurs_login, Axe2.nomAxe2 , Periodes.Axe2_idAxe2, sum(Periodes.totalHoraire) as sommeHeures , Axe2.codeAxe2 FROM `Periodes`, Axe2 where Periodes.Utilisateurs_idUtilisateurs = "'.$idUtilisateur.'" '.$andDateStat.' '.$andAxe1Stat.' '.$andAxe2Stat.' '.$andAxe3Stat.' '.$andSession.' AND Periodes.`Axe2_idAxe2` = Axe2.`idAxe2`  '.$andAxe2Exclus.' group by Axe2.idAxe2 order by sommeHeures desc';
				//echo "$sql_axe2";
				try
				{
					$reponse_axe2 = $bdd->query($sql_axe2) or die('Erreur SQL !<br>' .$sql_axe2. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				$totalHeuresUtilisateurAxe2=0;
				while ($donnees = $reponse_axe2->fetch())
				{
					$periodeLoginUtilisateur=		$donnees['Utilisateurs_login'];
					$nomAxe2		=		$donnees['nomAxe2'];
					$idAxe2			=		$donnees['Axe2_idAxe2'];	
					$sommeHeures		=		$donnees['sommeHeures'];
					$sommeJournee		=		round($sommeHeures / ($nbrHeuresSemaine / 5)	,2);
					$sommeDemiJournee	=		round($sommeHeures / ($nbrHeuresSemaine / 10)	,2);

					// Si il y a des congés, on va chercher les dates.
					if ($idAxe2 != "")
					{
						$dateCongees = dateCongees($periodeLoginUtilisateur, $idAxe2, $andDateStat);
					}

					$code_axe1="";
					if ( $afficherCodesComptablesRecap == "checked" )
					{
						$code_axe2=$donnees['codeAxe2'];
						$code_axe2="$code_axe2 - ";
					}

			echo "<tr>	<td style='background-color: rgba(0, 0, 0, 0.1)' width=20% align=center>$sommeHeures</td>	<td width=80%>$code_axe2 $nomAxe2</td>		</tr>";
				$totalHeuresUtilisateurAxe2	= 	$totalHeuresUtilisateurAxe2 + $sommeHeures;
				$totalGlobalAxe2	=	$sommeHeures + $totalGlobalAxe2;
				}
			echo "</table>";
				$reponse_axe2->closeCursor(); // Termine le traitement de la requête
		echo "</td>";
		///////////////////////// FIN AXE 2 ///////////////////////////////////




		//////////////////////////// PROJET /////////////////////////////////////	
		echo "<td>";	
			echo "<table border=0 width=100%>";
				// On prend, dans Periodes, tout ce qui a un ID axe2 correspondant à l' ID axe2 de la table Axe2 contenant les codes axe2 = 5xxx (50xx = congés). 
				$sql_axe3 = 'SELECT Periodes.Utilisateurs_login, Axe3.nomAxe3 , Periodes.Axe3_idAxe3, sum(Periodes.totalHoraire) as sommeHeures FROM `Periodes`, Axe3 where Periodes.Utilisateurs_idUtilisateurs = "'.$idUtilisateur.'" '.$andDateStat.' '.$andAxe1Stat.' '.$andAxe2Stat.' '.$andAxe3Stat.' '.$andSession.' AND Periodes.`Axe3_idAxe3` = Axe3.`idAxe3`  '.$andAxe2Exclus.' group by Axe3.idAxe3 order by sommeHeures desc';
				//echo "$sql_axe3";
				try
				{
					$reponse_axe3 = $bdd->query($sql_axe3) or die('Erreur SQL !<br>' .$sql_axe3. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				$totalHeuresUtilisateurAxe3=0;
				while ($donnees = $reponse_axe3->fetch())
				{
					$periodeLoginUtilisateur	=		$donnees['Utilisateurs_login'];
					$nomAxe3			=		$donnees['nomAxe3'];
					$idAxe3			=		$donnees['Axe3_idAxe3'];	
					$sommeHeures		=		$donnees['sommeHeures'];
					$sommeJournee		=		round($sommeHeures / ($nbrHeuresSemaine / 5)	,2);
					$sommeDemiJournee		=		round($sommeHeures / ($nbrHeuresSemaine / 10)	,2);

					// Si il y a des congés, on va chercher les dates.
					if ($idAxe3 != "")
					{
						$dateCongees = dateCongees($periodeLoginUtilisateur, $idAxe3, $andDateStat);
					}

			echo "<tr>	<td style='background-color: rgba(0, 0, 0, 0.10)' width=20% align=center>$sommeHeures</td>	<td width=80%>$nomAxe3</td>	</tr>";
				$totalHeuresUtilisateurAxe3	= 	$totalHeuresUtilisateurAxe3 + $sommeHeures;
				$totalGlobalAxe3		=	$sommeHeures + $totalGlobalAxe3;
				}
			echo "</table>";
				$reponse_axe3->closeCursor(); // Termine le traitement de la requête
		
		
		echo "</td>";
		///////////////////////// FIN PROJET ///////////////////////////////////

		
		// Total d'heures pour chaque utilisateur
		if ($totalHeuresUtilisateurAxe1 > 0)
		{
		echo "</tr>";
			echo "<th style='background-color: rgba(0, 0, 0, 0.25)' id='petitTitre'>Total Axe 1: $totalHeuresUtilisateurAxe1</th>";
			echo "<th style='background-color: rgba(0, 0, 0, 0.25)' id='petitTitre'>Total Axe 2: $totalHeuresUtilisateurAxe2</th>";
			echo "<th style='background-color: rgba(0, 0, 0, 0.25)' id='petitTitre'>Total Axe3: $totalHeuresUtilisateurAxe3</th>";
		}

		echo "</tr></table>";
	echo "<br>";
	echo "</td></tr></table>";	
	}
	$reponse_login->closeCursor(); // Termine le traitement de la requête

	// La table bleu représentant le total tous utilisateurs confondus
	if ( $admin == 1)
	{
		///////////////////////////////////////////////////////////////
		// Si ADMIN, On affiche le TOTAL de chaque axe pour TOUS les utilisateurs

		echo "<table border=0 width=100% ><tr><td>";
	echo "<tr>";
	echo "<td colspan=3>";
	
		// Une petite table pour le haut de l'onglet
		echo "<table border=0 width=30% ><tr align=left >	<th id=ongletBleuNom>Somme tous utilisateurs</th></tr></table>";
	
		// Une table contenant tous les axes et axe3
		echo "<table border=0 width=100% id=tableOngletsBleu><tr>";
		//////////////////////////// AXE 1 /////////////////////////////////////
		
		// Une table contenant toutes les heures et les noms des axes et axe3
		echo "<td width=37%>";	
			echo "<table border=0 width=100% >";		
				// On prend, dans Periodes, tout ce qui a un ID axe1 correspondant à l' ID axe1 de la table Axe1 contenant les codes axe1 = 5xxx (50xx = congés). 
				$sql_axe1 = '	SELECT Axe1.nomAxe1 , Periodes.Axe1_idAxe1, sum(Periodes.totalHoraire) as sommeHeures FROM `Periodes`, Axe1 where Periodes.`Axe1_idAxe1` = Axe1.`idAxe1` and Periodes.`Utilisateurs_idUtilisateurs` IN (select idUtilisateurs from Utilisateurs WHERE active = 1) '.$andDateStat.' '.$andAxe1Stat.' '.$andAxe2Stat.' '.$andAxe3Stat.'  '.$andAxe2Exclus.' group by Axe1.idAxe1 order by sommeHeures desc';

			// echo "$sql_axe1";
				try
				{
					$reponse_axe1 = $bdd->query($sql_axe1) or die('Erreur SQL !<br>' .$sql_axe1. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				$totalHeuresUtilisateurAxe1=0;
				while ($donnees = $reponse_axe1->fetch())
				{
					//$periodeLoginUtilisateur=		$donnees['Utilisateurs_login'];
					$nomAxe1		=		$donnees['nomAxe1'];
					$idAxe1			=		$donnees['Axe1_idAxe1'];	
					$sommeHeures		=		$donnees['sommeHeures'];
					$sommeJournee		=		round($sommeHeures / ($nbrHeuresSemaine / 5)	,2);
					$sommeDemiJournee	=		round($sommeHeures / ($nbrHeuresSemaine / 10)	,2);

					// Si il y a des congés, on va chercher les dates.
					if ($idAxe1 != "")
					{
						$dateCongees = dateCongees($periodeLoginUtilisateur, $idAxe1, $andDateStat);
					}

			// Table pour l'axe 1
			echo "<tr> <td style='background-color: rgba(0, 0, 0, 0.1)' width=20% align=center>$sommeHeures</td>	<td width=80%>$nomAxe1</td>		</tr>";

				$totalHeuresUtilisateurAxe1 = 	$totalHeuresUtilisateurAxe1 + $sommeHeures;
				//$totalGlobalAxe1	=	$sommeHeures + $totalGlobalAxe1;
				}
			echo "</table>";
				$reponse_axe1->closeCursor(); // Termine le traitement de la requête
		echo "</td>";
		///////////////////////// FIN AXE 1 ///////////////////////////////////



		// //////////////////////////// AXE 2 /////////////////////////////////////
		
		// // Une table contenant toutes les heures et les noms des axes et axe3
		 echo "<td width=37%>";	
		 	echo "<table border=0 width=100%>";
		 		// On prend, dans Periodes, tout ce qui a un ID axe2 correspondant à l' ID axe2 de la table Axe2 contenant les codes axe2 = 5xxx (50xx = congés). 
				$sql_axe2 = '	SELECT Axe2.nomAxe2 , Periodes.Axe2_idAxe2, sum(Periodes.totalHoraire) as sommeHeures FROM `Periodes`, Axe2 where Periodes.`Axe2_idAxe2` = Axe2.`idAxe2` and Periodes.`Utilisateurs_idUtilisateurs` IN (select idUtilisateurs from Utilisateurs WHERE active = 1) '.$andDateStat.' '.$andAxe1Stat.' '.$andAxe2Stat.' '.$andAxe3Stat.'  '.$andAxe2Exclus.' group by Axe2.idAxe2 order by sommeHeures desc';
		 		//echo "$sql_axe2";
		 		try
		 		{
		 			$reponse_axe2 = $bdd->query($sql_axe2) or die('Erreur SQL !<br>' .$sql_axe2. '<br>'. mysql_error());
		 		}
		 		catch(Exception $e)
		 		{
		 			// En cas d'erreur précédemment, on affiche un message et on arrête tout
		 			die('Erreur : '.$e->getMessage());
		 		}
		 		$totalHeuresUtilisateurAxe2=0;
		 		while ($donnees = $reponse_axe2->fetch())
		 		{
		 			$periodeLoginUtilisateur=		$donnees['Utilisateurs_login'];
		 			$nomAxe2		=		$donnees['nomAxe2'];
		 			$idAxe2			=		$donnees['Axe2_idAxe2'];	
		 			$sommeHeures		=		$donnees['sommeHeures'];
		 			$sommeJournee		=		round($sommeHeures / ($nbrHeuresSemaine / 5)	,2);
		 			$sommeDemiJournee	=		round($sommeHeures / ($nbrHeuresSemaine / 10)	,2);

		 			 // Si il y a des congés, on va chercher les dates.
		 			 if ($idAxe2 != "")
		 			 {
		 			 	$dateCongees = dateCongees($periodeLoginUtilisateur, $idAxe2, $andDateStat);
		 			 }

		 	echo "<tr>	<td style='background-color: rgba(0, 0, 0, 0.1)' width=20% align=center>$sommeHeures</td>	<td width=80%>$nomAxe2</td>		</tr>";
		 		$totalHeuresUtilisateurAxe2	= 	$totalHeuresUtilisateurAxe2 + $sommeHeures;
		 		$totalGlobalAxe2	=	$sommeHeures + $totalGlobalAxe2;
		 		}
		 	echo "</table>";
		 		$reponse_axe2->closeCursor(); // Termine le traitement de la requête
		 echo "</td>";
		// ///////////////////////// FIN AXE 2 ///////////////////////////////////




		// //////////////////////////// PROJET /////////////////////////////////////	
		echo "<td>";	
			echo "<table border=0 width=100%>";
				// On prend, dans Periodes, tout ce qui a un ID axe2 correspondant à l' ID axe2 de la table Axe2 contenant les codes axe2 = 5xxx (50xx = congés). 
				$sql_axe3 = '	SELECT Axe3.nomAxe3 , Periodes.Axe3_idAxe3, sum(Periodes.totalHoraire) as sommeHeures FROM `Periodes`, Axe3 where Periodes.`Axe3_idAxe3` = Axe3.`idAxe3` and Periodes.`Utilisateurs_idUtilisateurs` IN (select idUtilisateurs from Utilisateurs WHERE active = 1) '.$andDateStat.' '.$andAxe1Stat.' '.$andAxe2Stat.' '.$andAxe3Stat.'  '.$andAxe2Exclus.' group by Axe3.idAxe3 order by sommeHeures desc';
				//echo "$sql_axe3";
				try
				{
					$reponse_axe3 = $bdd->query($sql_axe3) or die('Erreur SQL !<br>' .$sql_axe3. '<br>'. mysql_error());
				}
				catch(Exception $e)
				{
					// En cas d'erreur précédemment, on affiche un message et on arrête tout
					die('Erreur : '.$e->getMessage());
				}
				$totalHeuresUtilisateurAxe3=0;
				while ($donnees = $reponse_axe3->fetch())
				{
					$periodeLoginUtilisateur	=		$donnees['Utilisateurs_login'];
					$nomAxe3					=		$donnees['nomAxe3'];
					$Axe3_idAxe3			=		$donnees['Axe3_idAxe3'];	
					$sommeHeures				=		$donnees['sommeHeures'];
					$sommeJournee				=		round($sommeHeures / ($nbrHeuresSemaine / 5)	,2);
					$sommeDemiJournee			=		round($sommeHeures / ($nbrHeuresSemaine / 10)	,2);

					// // Si il y a des congés, on va chercher les dates.
					// if ($idAxe3 != "")
					// {
					// 	$dateCongees = dateCongees($periodeLoginUtilisateur, $idAxe3, $andDateStat);
					// }

			echo "<tr>	<td style='background-color: rgba(0, 0, 0, 0.10)' width=20% align=center>$sommeHeures</td>	<td width=80%>$nomAxe3</td>	</tr>";
				$totalHeuresUtilisateurAxe3	= 	$totalHeuresUtilisateurAxe3 + $sommeHeures;
				$totalGlobalAxe3		=	$sommeHeures + $totalGlobalAxe3;
				}
			echo "</table>";
				$reponse_axe3->closeCursor(); // Termine le traitement de la requête
		
		
		echo "</td>";
		///////////////////////// FIN PROJET ///////////////////////////////////

		
		// Total d'heures pour chaque utilisateur
		if ($totalHeuresUtilisateurAxe1 > 0)
		{
		echo "</tr>";
			echo "<th style='background-color: rgba(0, 0, 0, 0.25)' id='petitTitre'>Total Axe 1: $totalHeuresUtilisateurAxe1</th>";
			echo "<th style='background-color: rgba(0, 0, 0, 0.25)' id='petitTitre'>Total Axe 2: $totalHeuresUtilisateurAxe2</th>";
			echo "<th style='background-color: rgba(0, 0, 0, 0.25)' id='petitTitre'>Total Axe3: $totalHeuresUtilisateurAxe3</th>";
		}

		echo "</tr></table>";
	echo "<br>";
	echo "</td></tr></table>";	
	// }

		// FIN TOTAL AXES POUR TOUS UTILISATEURS
		///////////////////////////////////////////////////////////////
	}




echo "
<input type=hidden id=totalAxe1 value=$totalGlobalAxe1>
<input type=hidden id=totalAxe2 value=$totalGlobalAxe2>
<input type=hidden id=totalAxe3 value=$totalGlobalAxe3>
";


}

function dateCongees($periodeLoginUtilisateur, $idAxe2, $andDateStat)
{
	global $bdd;
	$dateCongees="";

	//echo "SELECT distinct(date) FROM Periodes WHERE Axe2_idAxe2 = $idAxe2 $andDateStat AND Utilisateurs_login like '$periodeLoginUtilisateur' order by date <br>";
	$sql_date_congees = "SELECT distinct(date) FROM Periodes WHERE Axe2_idAxe2 = $idAxe2 $andDateStat AND Utilisateurs_login like '$periodeLoginUtilisateur' order by date";
	
	//modif pour test total
	//$sql_date_congees = "SELECT distinct(date) FROM Periodes WHERE Axe2_idAxe2 = $idAxe2 $andDateStat order by date";
	

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


stat_axes();
?>
<!-- Fin de la table de mise en page globale -->
</td><td></td></tr></table>
<!-- </td><td width=3%></td></tr></table> -->
</body>
</html>
