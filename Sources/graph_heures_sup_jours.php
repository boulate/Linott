<?php   

include("connexion_base.php"); 
session_start();
//$nombre_periodes		=	$_SESSION['nombre_lignes_total'];
$idUtilisateur			=	$_SESSION['idUtilisateurs'];
$idConsultUser 			= 	$_GET['idConsultUser'];
$date				=	$_GET['date'];

// Le graphique ne peut pas afficher un temps indéfini. Pour remédier à celà, nous n'allons afficher que les 6 derniers mois.
// On défini la timezone
date_default_timezone_set('Europe/Paris');
// On créé une variable contenant le jour obtenu en $_GET['date']. On lui précise le format d'entrée
$dateDebutGraph = date_create_from_format('Y-m-d', $date);
// On soustrait 6 mois à cette date de début de graph.
$dateDebutGraph->modify("-6months");

// Pour la consultation de fiche. Si un ID utilisateur différent est renseigné dans l'adresse, c'est lui le idUtilisateur en cours.
if ( $idConsultUser != "")
{
	$idUtilisateur = $idConsultUser;
}

// Changé car le graph n'arrive pas à afficher toutes les absences d'une année
$sql 		=	"SELECT date, nbrHeureSup, totalJournee FROM HeureSup WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' and date<='$date' ORDER BY date ASC";

// Le DATE_SUB me permet de selectionner uniquement les xx derniers jours.
//$sql 		=	"SELECT date, nbrHeureSup, totalJournee FROM HeureSup WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' AND date > DATE_SUB('$date', INTERVAL 190 DAY) ORDER BY date ASC ";
//$sql 		=	"SELECT date, nbrHeureSup, totalJournee FROM HeureSup WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' ORDER BY date ASC ";


try
{
	$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
}
catch(Exception $e)
{
	// En cas d'erreur précédemment, on affiche un message et on arrête tout
	die('Erreur : '.$e->getMessage());
}
$tableauRecup		= array();
$tableauRachat		= array();
$tableauHeuresSup 	= array();
$tableauDates		= array();
$tableauTotalJournee	= array();
$tableauAbsences	= array();

while ($donnees = $reponse->fetch())
{
	$dateHeuresSup			=	$donnees['date'];
	$heuresSup			=	$heuresSup + $donnees['nbrHeureSup'];
	$totalJournee			=	$donnees['totalJournee'];
	$heuresRachetees		=	0;
	$heuresRecuperees		=	0;
	$heuresAbsences			=	0;

		// On traite les rachats.
		$sqlRachat 		=	"SELECT date, nbr FROM RachatHeures WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur'  AND date = '$dateHeuresSup' ";
		try
		{
			$reponseRachat = $bdd->query($sqlRachat) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sqlRachat. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		while ($donneesRachat = $reponseRachat->fetch())
		{
			$dateRachatHeure		=	$donneesRachat['date'];
			if ($dateHeuresSup == $dateRachatHeure) 
			{
				$heuresRachetees	=	$donneesRachat['nbr'];
				$heuresSup 		=	$heuresSup - $heuresRachetees;
			}
		}

		// On traite les récups.
		$sqlRecup 		=	"SELECT date, sum(totalHoraire) FROM Periodes WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' AND Axe2_idAxe2 = 49 AND date = '$dateHeuresSup' ";
		try
		{
			$reponseRecup = $bdd->query($sqlRecup) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sqlRecup. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		while ($donneesRecup = $reponseRecup->fetch())
		{ 
			$dateRecup		=	$donneesRecup['date'];

			if ($dateHeuresSup == $dateRecup)
			// if (1 == 0) 
			{
				$heuresRecuperees	=	$donneesRecup['sum(totalHoraire)'];
				// Pas besoin de soustraire les heures recup puisqu'elles le sont déjà en base.
				// $heuresSup 		=	$heuresSup - $heuresRecuperees;
			}
		}

		// On rajoute les congés sur le graph.
		$sqlAbsences 		=	"SELECT date, sum(totalHoraire) FROM Periodes WHERE Utilisateurs_idUtilisateurs = '$idUtilisateur' AND `Axe1_idAxe1` = 16 AND `Axe2_idAxe2` != 55 AND date = '$dateHeuresSup' group by date ORDER BY date ASC ";
		try
		{
			$reponseAbsences = $bdd->query($sqlAbsences) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sqlAbsences. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}
		while ($donneesAbsences = $reponseAbsences->fetch())
		{
			$dateAbsences		=	$donneesAbsences['date'];
			if ($dateHeuresSup == $dateAbsences) 
			{
				$heuresAbsences	=	$donneesAbsences['sum(totalHoraire)'];
			}	
		}

	$dateParsee = date('d/m/y', strtotime($dateHeuresSup));

	
	
	// On transforme la date traitée en format mysql dans un format PHP.
	$dateTraitee = date_create_from_format('Y-m-d', $dateHeuresSup);
	// Si la date traitée est supérieur à la date de début de graph, on rentre les données dans le graphique
	if ( $dateTraitee > $dateDebutGraph)
	{
		array_push($tableauRecup, $heuresRecuperees);
		array_push($tableauRachat, $heuresRachetees);
		array_push($tableauAbsences, $heuresAbsences);
		array_push($tableauHeuresSup, $heuresSup);
		array_push($tableauDates,$dateParsee);
		array_push($tableauTotalJournee, $totalJournee);
	}
}





// print_r($tableauRecup);
// echo "<br><br>";
// print_r($tableauRachat);
// echo "<br><br>";
// print_r($tableauAbsences);
// echo "<br><br>";
// print_r($tableauHeuresSup);
// echo "<br><br>";
// print_r($tableauDates);
// echo "<br><br>";
// print_r($tableauTotalJournee);
// echo "<br><br>";

 /* CAT:Line chart */

 /* pChart library inclusions */
 include("pChart/class/pData.class.php");
 include("pChart/class/pDraw.class.php");
 include("pChart/class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  



 $MyData->addPoints($tableauTotalJournee,"Total journée");
 $MyData->addPoints($tableauHeuresSup,"Heures sup.");
 $MyData->addPoints($tableauRachat,"Rachat d'heures");
 $MyData->addPoints($tableauRecup,"Recup");
 $MyData->addPoints($tableauAbsences,"Absences");

 $MyData->setSerieWeight("Total journée",1);
 $MyData->setSerieWeight("Heures sup.",1);
 $MyData->setSerieTicks("Rachat d'heures",5);
 $MyData->setSerieTicks("Recup",5);
 $MyData->setSerieWeight("Absences",0);


 // Définition des couleurs:
$couleurNoir = array("R"=>0,"G"=>0,"B"=>0,"Alpha"=>90);
$couleurBleu = array("R"=>75,"G"=>75,"B"=>255,"Alpha"=>90);
$couleurRouge=array("R"=>220,"G"=>60,"B"=>70,"Alpha"=>70);
$couleurJaune=array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>100);
$couleurGris=array("R"=>150,"G"=>150,"B"=>150,"Alpha"=>0);
$MyData->setPalette("Heures sup.",$couleurRouge);
$MyData->setPalette("Rachat d'heures",$couleurNoir);
$MyData->setPalette("Recup",$couleurBleu);
$MyData->setPalette("Absences",$couleurGris);


 $MyData->setAxisName(0,"Heures");
 $MyData->addPoints($tableauDates,"Labels");
 $MyData->setSerieDescription("Labels","Months");
 $MyData->setAbscissa("Labels");


///////////////////////////////////////// IMAGE DE FOND ////////////////////////////////////////
 /* Create the pChart object */
 $myPicture = new pImage(1200,600,$MyData);

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,1200,600,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,1200,600,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,1200,30,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,1199,599,array("R"=>0,"G"=>0,"B"=>0));
 


 //////////////////////////////// PROPRIETES DU GRAPH ///////////////////////////////////////
 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"pChart/fonts/Forgotte.ttf","FontSize"=>8,"R"=>255,"G"=>255,"B"=>255));
 $myPicture->drawText(10,25,"Evolution de vos heures supplémentaires sur les 6 mois précédents la date selectionnée.",array("FontSize"=>14,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"pChart/fonts/pf_arma_five.ttf","FontSize"=>10,"R"=>0,"G"=>0,"B"=>0));

 /* Define the chart area */
 $myPicture->setGraphArea(50,40,1180,570);



/////////////////////////////////////////// AXES ///////////////////////////////////////////////
/* Draw the scale labelskip permet de zapper des labels */
// CycleBackground = couleur qui alterne derrière le graph.
 $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,"LabelSkip"=>10,"SkippedTickAlpha"=>40);
 $myPicture->drawScale($scaleSettings);

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;

 /* Enable shadow computing */
$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));


///////////////////////////////////////// LEGENDE /////////////////////////////////////////////////
 /* Write the chart legend */
$myPicture->drawLegend(720,15,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>255,"FontG"=>255,"FontB"=>255));




//////////////////////////////// GRAPHIQUES / COURBES /////////////////////////////////////////////
// Draw les Absences
$MyData->setSerieDrawable("Heures sup.", FALSE);
$MyData->setSerieDrawable("Total journée", FALSE);
$MyData->setSerieDrawable("Rachat d'heures", FALSE);
$MyData->setSerieDrawable("Recup", FALSE);
$MyData->setSerieDrawable("Absences", TRUE);

//$myPicture->drawFilledSplineChart();
$settings = array("Interleave"=>0, "Rounded"=>TRUE); 
$myPicture->drawBarChart($settings);


 /* Draw the line chart */
// Draw heure journée
$MyData->setSerieDrawable("Heures sup.", FALSE);
$MyData->setSerieDrawable("Total journée", TRUE);
$MyData->setSerieDrawable("Rachat d'heures", FALSE);
$MyData->setSerieDrawable("Recup", FALSE);
$MyData->setSerieDrawable("Absences", FALSE);

$myPicture->drawAreaChart();
$myPicture->drawLineChart();


 // Draw heures sup'
$MyData->setSerieDrawable("Heures sup.", TRUE);
$MyData->setSerieDrawable("Total journée", FALSE);
$MyData->setSerieDrawable("Rachat d'heures", FALSE);
$MyData->setSerieDrawable("Recup", FALSE);
$MyData->setSerieDrawable("Absences", FALSE);

$myPicture->drawSplineChart();

// Draw recup
$MyData->setSerieDrawable("Heures sup.", FALSE);
$MyData->setSerieDrawable("Total journée", FALSE);
$MyData->setSerieDrawable("Rachat d'heures", FALSE);
$MyData->setSerieDrawable("Recup", TRUE);
$MyData->setSerieDrawable("Absences", FALSE);

$myPicture->drawStepChart(); 


// Draw rachat d'heures
$MyData->setSerieDrawable("Heures sup.", FALSE);
$MyData->setSerieDrawable("Total journée", FALSE);
$MyData->setSerieDrawable("Rachat d'heures", TRUE);
$MyData->setSerieDrawable("Recup", FALSE);
$MyData->setSerieDrawable("Absences", FALSE);

$myPicture->drawStepChart(); 




//$myPicture->drawPlotChart(array("DisplayValues"=>TRUE,"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));



 /* Render the picture (choose the best way) */
$myPicture->autoOutput("graphs/example.drawLineChart.plots.png");


?>