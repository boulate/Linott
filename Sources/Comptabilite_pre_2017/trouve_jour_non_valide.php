<?php include("connexion_base.php"); 
session_start();
//echo "debut de la page <br><br>";
$idUtilisateur		=	$_SESSION['idUtilisateurs'];
$nomUtilisateur		=	$_SESSION['login']; 
$dateToImport		=	$_GET['dateToImport'];

// Pour la consultation de fiche. Si un ID utilisateur différent est renseigné dans l'adresse, c'est lui le idUtilisateur en cours.
$idConsultUser 		= 	$_GET['idConsultUser'];
if ( $idConsultUser != "")
{
	$idUtilisateur = $idConsultUser;
}


//echo "date to import =$dateToImport";

// Requete SQL qui marche:
// SELECT cdate FROM Calendar
// where cdate not in 
// (select date from Periodes where Utilisateurs_idUtilisateurs = 1 group by date having sum(totalHoraire) >= 4)
// and cdate < '2013-08-26' and cdate >= '2013-01-01' and DAYOFWEEK(cdate)  <> 1 and DAYOFWEEK(cdate)  <> 7

$anneeEnCours=date(Y, strtotime($dateToImport));

$select = 'SELECT cdate FROM Calendar where cdate not in (select date from Periodes where Utilisateurs_idUtilisateurs = '.$idUtilisateur.' group by date having sum(totalHoraire) >= 4) and cdate < CURDATE() and cdate >= \''.$anneeEnCours.'-01-01\' and DAYOFWEEK(cdate)  <> 1 and DAYOFWEEK(cdate)  <> 7 order by cdate DESC';
//echo "$select";
$reponse = $bdd->query($select) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

  while ($donnees = $reponse->fetch())
  {
	  $date = $donnees['cdate'];	
  }

  echo "$date";
?>
