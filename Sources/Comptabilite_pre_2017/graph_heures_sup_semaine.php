<?php   

include("connexion_base.php"); 
session_start();
//$nombre_periodes		=	$_SESSION['nombre_lignes_total'];
$idUtilisateur			=	$_SESSION['idUtilisateurs'];
$idConsultUser 			= 	$_GET['idConsultUser'];
$date				=	$_GET['date'];

// Pour la consultation de fiche. Si un ID utilisateur différent est renseigné dans l'adresse, c'est lui le idUtilisateur en cours.
if ( $idConsultUser != "")
{
	$idUtilisateur = $idConsultUser;
}

$sql 		=	"SELECT date, sum(nbrHeureSup), sum(totalJournee) FROM HeureSup WHERE Utilisateurs_idUtilisateurs =  '$idUtilisateur' AND YEAR(date) = YEAR('$date') AND WEEKOFYEAR(date) > 1 AND WEEKOFYEAR(date) < 52 AND WEEKOFYEAR(date) < WEEKOFYEAR(CURDATE()) group by WEEKOFYEAR(date) ORDER BY date ASC";

try
{
	$reponse = $bdd->query($sql) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
}
catch(Exception $e)
{
	// En cas d'erreur précédemment, on affiche un message et on arrête tout
	die('Erreur : '.$e->getMessage());
}
$tableauHeuresSup 	= array();
$tableauDates		= array();
$tableauTotalSemaine	= array();
while ($donnees = $reponse->fetch())
{
	$dateHeuresSup		=	$donnees['date'];
	$heuresSupSemaine		=	$heuresSupSemaine + $donnees['sum(nbrHeureSup)'];
	$totalSemaine			=	$donnees['sum(totalJournee)'];

	$dateParsee = date('W', strtotime($dateHeuresSup));

	array_push($tableauHeuresSup, $heuresSupSemaine);
	array_push($tableauDates,$dateParsee);
	array_push($tableauTotalSemaine, $totalSemaine);
}


// print_r($tableauHeuresSup);
// echo "<br>";
// print_r($tableauDates);
// echo "<br>";
// print_r($tableauTotalSemaine);
$moyenneHeuresSemaine = round(array_sum($tableauTotalSemaine)/count($tableauTotalSemaine),1);
//echo $moyenneHeuresSemaine;


 /* CAT:Line chart */

 /* pChart library inclusions */
 include("pChart/class/pData.class.php");
 include("pChart/class/pDraw.class.php");
 include("pChart/class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  

 $MyData->addPoints($tableauTotalSemaine,"Total semaine");
 // $MyData->addPoints($tableauHeuresSup,"Heures sup.");

 $MyData->setSerieWeight("Total semaine",1);
 // $MyData->setSerieWeight("Heures sup.",1);

 // Définition des couleurs:
// $couleurNoir = array("R"=>0,"G"=>0,"B"=>0,"Alpha"=>70);
$couleurRouge = array("R"=>180,"G"=>0,"B"=>0,"Alpha"=>80);
$couleurJaune=array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>100);
$couleurJauneFonce=array("R"=>120,"G"=>150,"B"=>0,"Alpha"=>50);



 $MyData->setAxisName(0,"Heures");
 $MyData->addPoints($tableauDates,"Labels");
 $MyData->setSerieDescription("Labels","Months");
 $MyData->setAbscissa("Labels");




////////////////////////////////////// IMAGE DE FOND ///////////////////////////////
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
 


 ////////////////////////////////// PROPRIETE DU GRAPH /////////////////////////////
 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"pChart/fonts/Forgotte.ttf","FontSize"=>8,"R"=>255,"G"=>255,"B"=>255));
 $myPicture->drawText(10,25,"Evolution du nombre d'heures travaillées par semaine (ne prend pas en compte la semaine en cours)",array("FontSize"=>14,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"pChart/fonts/pf_arma_five.ttf","FontSize"=>10,"R"=>0,"G"=>0,"B"=>0));

 /* Define the chart area */
 $myPicture->setGraphArea(50,40,1180,570);


///////////////////////////////////////// AXES ////////////////////////////////////////////
/* Draw the scale. */
// labelskip permet de zapper des labels, floating=calcul des ymin et ymax du graph, sinon SCALE_MODE_START0 

// Axes = Floating: On calcul le meilleur ymax et ymin
 //$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 
// Axe Y commence à 0 et on calcul le max.
//$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Mode"=>SCALE_MODE_ADDALL_START0, "GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 
// Axe Y à la main
$AxisBoundaries = array(0=>array("Min"=>20,"Max"=>55));
$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"Mode"=>SCALE_MODE_MANUAL, "ManualScale"=>$AxisBoundaries, "GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);

// On dessine l'axe Y.
$myPicture->drawScale($scaleSettings);

/* Turn on Antialiasing */
$myPicture->Antialias = TRUE;

/* Enable shadow computing */
//$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));




////////////////////////////////////// LEGENDE //////////////////////////////////////////
/* Write the chart legend */
$myPicture->drawLegend(1075,15,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>255,"FontG"=>255,"FontB"=>255));





//////////////////////////////////// GRAPHIQUE ///////////////////////////////////////
 /* Draw the line chart */
// Draw heure journée
$MyData->setSerieDrawable("Heures sup.", FALSE);
$MyData->setSerieDrawable("Total Semaine", TRUE);


$MyData->setPalette("Total semaine",$couleurJauneFonce);
$myPicture->drawFilledSplineChart(); 
//$myPicture->drawAreaChart();

$MyData->setPalette("Total semaine",$couleurJaune);
$myPicture->drawSplineChart();

/* Write the chart boundaries */
$BoundsSettings = array("MaxDisplayR"=>210,"MaxDisplayG"=>0,"MaxDisplayB"=>0, "MinDisplayR"=>0,"MinDisplayG"=>0,"MinDisplayB"=>255);
$myPicture->writeBounds(BOUND_BOTH,$BoundsSettings);

// Dessine le trait de moyenne grace à moyenneHeuresSemaine
//$myPicture->drawThreshold($moyenneHeuresSemaine,array("Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));
$myPicture->drawThreshold($moyenneHeuresSemaine,array("WriteCaption"=>TRUE,"Caption"=>"MOY=$moyenneHeuresSemaine","CaptionR"=>30,"CaptionG"=>30,"CaptionB"=>30,"R"=>50,"G"=>50,"B"=>50));
//$myPicture->drawPlotChart(array("DisplayValues"=>TRUE,"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));



 /* Render the picture (choose the best way) */
$myPicture->autoOutput("graphs/example.drawLineChart.plots.png");


?>