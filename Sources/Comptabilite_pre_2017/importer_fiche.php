<?php include("connexion_base.php"); 
session_start();
//echo "debut de la page <br><br>";
$nombre_periodes	=	$_SESSION['nombre_lignes_total'];
$idUtilisateur		=	$_SESSION['idUtilisateurs'];
$nomUtilisateur		=	$_SESSION['login']; 
//echo "nombre de periodes=$nombre_periodes </br><br>";
$dateToImport		=	$_GET['dateToImport'];
echo "dateToImport=$dateToImport";

// Pour la consultation de fiche. Si un ID utilisateur différent est renseigné dans l'adresse, c'est lui le idUtilisateur en cours.
$idConsultUser 		= 	$_GET['idConsultUser'];
if ( $idConsultUser != "")
{
	$idUtilisateur = $idConsultUser;
}

$select		= "SELECT * FROM Periodes where date = '$dateToImport' and Utilisateurs_idUtilisateurs = $idUtilisateur";
$reponse = $bdd->query($select) or die('<br>Erreur SQL au moment de la selection sur from fiche!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());

//$i=1;
while ($donnees = $reponse->fetch())
		{
		// Declaration des variables
		$idPeriode=$donnees['idHoraires'];
		$date = $donnees['date'];
		$horaireDebut = $donnees['horaireDebut'];
		$horaireFin = $donnees['horaireFin'];
		$totalHoraire = $donnees['totalHoraire'];
		$idUtilisateur = $donnees['Utilisateurs_idUtilisateurs'];
		$loginUtilisateur = $donnees['Utilisateurs_login'];
		$section = $donnees['Section_idSection'];
		$axe1 = $donnees['Axe1_idAxe1'];
		$axe2 = $donnees['Axe2_idAxe2'];
		$axe3 = $donnees['Axe3_idAxe3'];
		$numeroLigne= $donnees['numeroLigne'];


			// On gère les idAxe1/2 et idAxe3 qu'on transforme en nom.
			$reponse_nom_axe1		= $bdd->query("SELECT nomAxe1 FROM Axe1 where idAxe1 = $axe1") or die('<br>Erreur SQL au moment de la selection sur from axe1!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
			while ($loop = $reponse_nom_axe1->fetch())
				{
					$nom_axe1 = $loop['nomAxe1'];
				}


			$reponse_nom_axe2		= $bdd->query("SELECT nomAxe2 FROM Axe2 where idAxe2 = $axe2") or die('<br>Erreur SQL au moment de la selection sur from axe2!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
			while ($loop = $reponse_nom_axe2->fetch())
				{
					$nom_axe2 = $loop['nomAxe2'];
				}


			$reponse_nom_axe3		= $bdd->query("SELECT * FROM Axe3 where idAxe3 = $axe3") or die('<br>Erreur SQL au moment de la selection sur from axe3!<br>Votre selection semble ne correspondre a aucun resultat.' .$sql. '<br>'. mysql_error());
			while ($loop = $reponse_nom_axe3->fetch())
				{
					$nom_axe3 = $loop['nomAxe3'];
				}


		echo "--idPeriode=$idPeriode;";
		echo "Date=$date;";
		echo "horaireDebut=$horaireDebut;";
		echo "horaireFin=$horaireFin;";
		echo "totalHoraire=$totalHoraire;";
		echo "idUtilisateur=$idUtilisateur;";
		echo "loginUtilisateur=$loginUtilisateur;";
		echo "Section=$section;";
		echo "axe1=$axe1;";
		echo "nom_axe1=$nom_axe1;";
		echo "axe2=$axe2;";
		echo "nom_axe2=$nom_axe2;";
		echo "axe3=$axe3;";
		echo "nom_axe3=$nom_axe3;";
		echo "numeroLigne=$numeroLigne";

}



?>
