<?php include("connexion_base.php"); 
session_start();
//echo "debut de la page <br><br>";
$nombre_periodes	=	$_SESSION['nombre_lignes_total'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];
$nomUtilisateur		=	$_SESSION['login']; 
$dateToImport		=	$_GET['dateToImport'];
//echo "dateToImport=$dateToImport";


$select		= "SELECT * FROM Calendar where cdate = '$dateToImport'";
//echo "$select";
$reponse = $bdd->query($select) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

//$i=1;
while ($donnees = $reponse->fetch())
		{
		// Declaration des variables
		$annee=$donnees['cyear'];
		$mois = $donnees['cmonth'];
		$jour = $donnees['cday'];
		$date = $donnees['cdate'];

		
		
		
		
echo "date=";
echo "$date;";

			
echo "jourMois=";
$jourMois		= date("d",strtotime("$date")); // jour du mois sur deux chiffres
echo "$jourMois;";

echo "mois=";
$mois			= date("m",strtotime("$date")); // mois sur deux chiffres
echo "$mois;";

echo "annee=";
$annee			= date("Y",strtotime("$date")); // année sur quatre chiffres
echo "$annee;";

echo "jourSemaine=";
$jourSemaine		= date("N",strtotime("$date")); // numero du jour de la semaine
echo "$jourSemaine;";

echo "jourDebutMois=";
$jourDebutMois		= date("N",mktime(0,0,0,$mois,1,$annee)); // numéro du jour débutant le mois
echo "$jourDebutMois;";

echo "jourFinMois=";
$jourFinMois=date("N",mktime(0,0,0,$mois+1,0,$annee)); // numéro du jour finissant le mois
echo "$jourFinMois;";

echo "nbrJoursMois=";
$nbrJoursMois=date("d",mktime(0,0,0,$mois+1,0,$annee)); // nombre de jours dans le mois
echo "$nbrJoursMois;";


// Definition du nom du jour de la semaine.
if ($jourSemaine == 1) $nomJourSemaine = "Lundi";
if ($jourSemaine == 2) $nomJourSemaine = "Mardi";
if ($jourSemaine == 3) $nomJourSemaine = "Mercredi";
if ($jourSemaine == 4) $nomJourSemaine = "Jeudi";
if ($jourSemaine == 5) $nomJourSemaine = "Vendredi";
if ($jourSemaine == 6) $nomJourSemaine = "Samedi";
if ($jourSemaine == 7) $nomJourSemaine = "Dimanche";

echo "nomJourSemaine=$nomJourSemaine";




		
}



?>
