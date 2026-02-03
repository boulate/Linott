<?php include("connexion_base.php"); 
session_start();
//echo "debut de la page <br><br>";
$nombre_periodes	=	$_SESSION['nombre_lignes_total'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];
$nomUtilisateur		=	$_SESSION['login']; 
//echo "nombre de periodes=$nombre_periodes </br><br>";
$dateToImport		=	$_GET['dateToImport'];
//echo "dateToImport=$dateToImport";

// Pour la consultation de fiche. Si un ID utilisateur différent est renseigné dans l'adresse, c'est lui le idUtilisateur en cours.
$idConsultUser 		= 	$_GET['idConsultUser'];
if ( $idConsultUser != "")
{
	$idUtilisateur = $idConsultUser;
}


$lundiMatin=0;
$lundiAprem=0;
$mardiMatin=0;
$mardiAprem=0;
$mercrediMatin=0;
$mercrediAprem=0;
$jeudiMatin=0;
$jeudiAprem=0;
$vendrediMatin=0;
$vendrediAprem=0;


//$select		= "SELECT * FROM Periodes where date <= CURDATE() and WEEKOFYEAR(date) = $semaineEnCours and Utilisateurs_idUtilisateurs = $idUtilisateur";
$select		= "SELECT * FROM Periodes where WEEKOFYEAR(date) = WEEKOFYEAR('$dateToImport') and YEAR(date) = YEAR('$dateToImport') and Utilisateurs_idUtilisateurs = $idUtilisateur";
//echo "$select<br>";
$reponse = $bdd->query($select) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

while ($donnees = $reponse->fetch())
{

	// Declaration des variables
	//$idPeriode=$donnees['idHoraires'];
	$date = $donnees['date'];
	//$horaireDebut = $donnees['horaireDebut'];
	//$horaireFin = $donnees['horaireFin'];
	$totalHoraire = $donnees['totalHoraire'];
	//$idUtilisateur = $donnees['Utilisateurs_idUtilisateurs'];
	//$loginUtilisateur = $donnees['Utilisateurs_login'];
	//$section = $donnees['Section_idSection'];
	//$axe1 = $donnees['Axe1_idAxe1'];
	//$axe2 = $donnees['Axe2_idAxe2'];
	//$axe3 = $donnees['Axe3_idAxe3'];
	$numeroLigne= $donnees['numeroLigne'];
	$jourDeLaSemaine = date('N', strtotime($date)); 

	//echo "$jourDeLaSemaine";

	//$periodeMatin++;
	//$periodeAprem++;
	if ($totalHoraire > 0)
	{	
		if ($numeroLigne >= 1 && $numeroLigne <=6)
		{
			if ($jourDeLaSemaine == 1)
			{
				$lundiMatin++;
			}
			if ($jourDeLaSemaine == 2)
			{
				$mardiMatin++;
			}
			if ($jourDeLaSemaine == 3)
			{
				$mercrediMatin++;
			}
			if ($jourDeLaSemaine == 4)
			{
				$jeudiMatin++;
			}
			if ($jourDeLaSemaine == 5)
			{
				$vendrediMatin++;
			}
		}
		if ($numeroLigne > 6 && $numeroLigne <=12)
		{
			if ($jourDeLaSemaine == 1)
			{ 
				$lundiAprem++;
			}
			if ($jourDeLaSemaine == 2)
			{
				$mardiAprem++;
			}
			if ($jourDeLaSemaine == 3)
			{
				$mercrediAprem++;
			}
			if ($jourDeLaSemaine == 4)
			{
				$jeudiAprem++;
			}
			if ($jourDeLaSemaine == 5)
			{
				$vendrediAprem++;
			}
		}
	}
}
echo "$lundiMatin,";
echo "$lundiAprem,";
echo "$mardiMatin,";
echo "$mardiAprem,";
echo "$mercrediMatin,";
echo "$mercrediAprem,";
echo "$jeudiMatin,";
echo "$jeudiAprem,";
echo "$vendrediMatin,";
echo "$vendrediAprem,";
echo date(w, strtotime($dateToImport));echo ",";
echo date(W, strtotime($dateToImport));

?>
