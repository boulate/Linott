<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<?php session_start(); ?>
<head>
<?php
	// Permet de rediriger vers l'acceuil si utilisateur non enregistré.
	$prenom = $_SESSION['prenom'];
	if (!$prenom)
	{
		header('Location: index.php'); 
	} 
	
	$idSession = $_SESSION['idUtilisateurs	'];
?>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="style.css" />
	<link rel="stylesheet" href="CSS/Delta/css/normalise.css"> 
	<link rel="icon" type="image/png" href="favicon.png" />


<!-- J'importe mon fichier permettant de vérifier les inputs -->
<script src="verifier_input_javascript.js"></script>

<script>

	function retourFiche()
	{
		location.href="statsAxes.php";
	}

	function exporter(type_requete)
	{
		//alert(type_requete);

		pageEnCours = document.location.href;
		argumentsPage = pageEnCours.split("?");
		destination = argumentsPage[1];
		document.location.href = "exporter_donnees.php?"+destination+"&type_requete="+type_requete;
	}

</script> 
</head>

<BODY>
<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table border=0 width=100% id=tableGlobale>
<tr><TD align=center></td><TD align=center id="tableGlobale">

<?php include("menu_general.php") ?>



    <!-- Table permettant les bords à 3% -->
    <table border=0 width=100%>
    <tr>
    <TD align=center width=3%></td>
    <TD align=center>
    

		<?php 
		include("connexion_base.php");
		include("importer_configuration.php"); 

		$loginSession		=	$_SESSION['login'];
		$idUtilisateur		=	$_SESSION['idUtilisateurs'];

		require("checkAdmin.php");

			$axe1 = $_GET['axe1'];
			$axe2 = $_GET['axe2'];
			$axe3 = $_GET['axe3'];
			$dateStat = $_GET['date'];

			$andDate = "";
			$andAxe1 = "";
			$andAxe2 = "";
			$andAxe3 = "";

			$descriptifExportCSV = "";


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

////////////////////////////////// Debut du choix des types d'exports /////////////////////////////////////////////////////////////
			?>

	<table border=0 width=100%>
		<tr>
			<TD align=center>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertConsult">Type export 1</th><TD align=center></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><TD align=center><table border=0 width=98%>				
					      <tr><TD align=center></td></tr>
					      <tr>
					      	<TD align=center width=2%>
					      	</td>
					      	<th id="titreTableau">
							Somme des heures travaillées sur chaque axe1, axe2 et axe3.<br />
					      	</th>
					      	</tr>
					      	<tr>
					      	<TD align=center>
					      	</td>
					      	<TD align=left>
							Exemple de format: <br />
					      		<?php exemple_export_1(); ?>
					      		<br />
					      	</td>
					      	<td width="1%">
					      	</td>
						    <td width="5%" align=center ALIGN=CENTER>
						    	<input type="button" id="exporter" value="exporter"  onClick='javascript:exporter(1);' />
						    </td>
					      </tr>
					      <tr><TD align=center></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>
	</br>

	<br />
	<table border=0 width=100%>
		<tr>
			<TD align=center>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertConsult">Type export 2</th><TD align=center></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><TD align=center><table border=0 width=98%>				
					      <tr><TD align=center></td></tr>
					      <tr>
					      	<TD align=center width=2%>
					      	</td>
					      	<th id="titreTableau">
					      		Sommes d'heures travaillées pour chaque utilisateur sur chaque couple Axe1_Axe2.<br />
					      	</th>
					      	</tr>
					      	<tr>
					      	<TD align=center>
					      	</td>
					      	<TD align=left>
							Exemple de format: <br />
					      		<?php exemple_export_2(); ?>
					      		<br />
					      	</td>
					      	<td width="1%">
					      	</td>
						    <td width="5%" align=center ALIGN=CENTER>
						     	<input type="button" id="exporter" value="exporter"  onClick='javascript:exporter(2);' />
						    </td>
					      </tr>
					      <tr><TD align=center></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>
	</br>

	
	<br />
	<table border=0 width=100%>
		<tr>
			<TD align=center>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertConsult">Type export 3</th><TD align=center></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><TD align=center><table border=0 width=98%>				
					      <tr><TD align=center></td></tr>
					      <tr>
					      	<TD align=center width=2%>
					      	</td>
					      	<th id="titreTableau">
					      		Pourcentage (journalier) du temps passé par chaque utilisateur sur chaque couple Axe1_Axe2.<br />
					      	</th>
					      	</tr>
					      	<tr>
					      	<TD align=center>
					      	</td>
					      	<TD align=left>
							Exemple de format: <br />
					      		<?php exemple_export_3(); ?>
					      		<br />
					      	</td>
					      	<td width="1%">
					      	</td>
						    <td width="5%" align=center ALIGN=CENTER>
						    	<input type="button" id="exporter" value="exporter"  onClick='javascript:exporter(3);' />
						    </td>
					      </tr>
					      <tr><TD align=center></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>
	</br>
	
	
	<br />
	<table border=0 width=100%>
		<tr>
			<TD align=center>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertConsult">Type export 4</th><TD align=center></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><TD align=center><table border=0 width=98%>				
					      <tr><TD align=center></td></tr>
					      <tr>
					      	<TD align=center width=2%>
					      	</td>
					      	<th id="titreTableau">
					      		Pourcentage (sur la période selectionnée) d'heures travaillées dans chaque couple Axe1_Axe2<br> par rapport au total.<br />
					      	</th>
					      	</tr>
					      	<tr>
					      	<TD align=center>
					      	</td>
					      	<TD align=left>
							Exemple de format: <br />
					      		<?php exemple_export_4(); ?>
					      		<br />
					      	</td>
					      	<td width="1%">
					      	</td>
						    <td width="5%" align=center ALIGN=CENTER>
						    	<input type="button" id="exporter" value="exporter"  onClick='javascript:exporter(4);' />
						    </td>
					      </tr>
					      <tr><TD align=center></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>
	</br>
	
	<br />
	<table border=0 width=100%>
		<tr>
			<TD align=center>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertConsult">Type export 5</th><TD align=center></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><TD align=center><table border=0 width=98%>				
					      <tr><TD align=center></td></tr>
					      <tr>
					      	<TD align=center width=2%>
					      	</td>
					      	<th id="titreTableau">
					      		Pourcentage (période selectionnée) d'heures travaillées dans chaque couple Axe1_Axe2_Axe3<br> par rapport au total.<br />
					      	</th>
					      	</tr>
					      	<tr>
					      	<TD align=center>
					      	</td>
					      	<TD align=left>
							Exemple de format: <br />
					      		<?php exemple_export_5(); ?>
					      		<br />
					      	</td>
					      	<td width="1%">
					      	</td>
						    <td width="5%" align=center ALIGN=CENTER>
						    	<input type="button" id="exporter" value="exporter"  onClick='javascript:exporter(5);' />
						    </td>
					      </tr>
					      <tr><TD align=center></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>
	</br>

	<br />
	<table border=0 width=100%>
		<tr>
			<TD align=center>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertConsult">Type export 6</th><TD align=center></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><TD align=center><table border=0 width=98%>				
					      <tr><TD align=center></td></tr>
					      <tr>
					      	<TD align=center width=2%>
					      	</td>
					      	<th id="titreTableau">
					      		Pourcentage (journalier) du temps passé par chaque utilisateur sur chaque couple Axe1_Axe2.<br>(Axes 2 exclus compris)<br />
					      	</th>
					      	</tr>
					      	<tr>
					      	<TD align=center>
					      	</td>
					      	<TD align=left>
							Exemple de format: <br />
					      		<?php exemple_export_3(); ?>
					      		<br />
					      	</td>
					      	<td width="1%">
					      	</td>
						    <td width="5%" align=center ALIGN=CENTER>
						    	<input type="button" id="exporter" value="exporter"  onClick='javascript:exporter(6);' />
						    </td>
					      </tr>
					      <tr><TD align=center></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>
	</br>

	<br />
	<table border=0 width=100%>
		<tr>
			<TD align=center>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertConsult">Type export 7</th><TD align=center></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><TD align=center><table border=0 width=98%>				
					      <tr><TD align=center></td></tr>
					      <tr>
					      	<TD align=center width=2%>
					      	</td>
					      	<th id="titreTableau">
					      		Total journalier par employé.<br />
					      	</th>
					      	</tr>
					      	<tr>
					      	<TD align=center>
					      	</td>
					      	<TD align=left>
							Exemple de format: <br />
					      		<?php exemple_export_7(); ?>
					      		<br />
					      	</td>
					      	<td width="1%">
					      	</td>
						    <td width="5%" align=center ALIGN=CENTER>
						    	<input type="button" id="exporter" value="exporter"  onClick='javascript:exporter(7);' />
						    </td>
					      </tr>
					      <tr><TD align=center></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>
	</br>


	<br />
	<table border=0 width=100%>
		<tr>
			<TD align=center>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertConsult">Type export 8</th><TD align=center></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><TD align=center><table border=0 width=98%>				
					      <tr><TD align=center></td></tr>
					      <tr>
					      	<TD align=center width=2%>
					      	</td>
					      	<th id="titreTableau">
					      		Total des information renseignées par jours et par employés<br />
					      	</th>
					      	</tr>
					      	<tr>
					      	<TD align=center>
					      	</td>
					      	<TD align=left>
							Exemple de format: <br />
					      		<?php exemple_export_8(); ?>
					      		<br />
					      	</td>
					      	<td width="1%">
					      	</td>
						    <td width="5%" align=center ALIGN=CENTER>
						    	<input type="button" id="exporter" value="exporter"  onClick='javascript:exporter(8);' />
						    </td>
					      </tr>
					      <tr><TD align=center></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>
	</br>



    <!-- Fin de la table permettant les bords à 3% --> 
    </td>
    <TD align=center width=3%></td>
    </tr></table>	
<!-- Fin de la table de mise en page globale -->
</td><TD align=center></td></tr>
</table>
</body>
</html>

<?php 

///////////////////////////////// EXEMPLES DE TABLEAUX /////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////


function exemple_export_1()
{
echo '
	<TABLE WIDTH=100% border=1>
	<COL WIDTH=10%>
	<COL WIDTH=15%>
	<COL WIDTH=10%>
	<TR>
		<TH id="titreTableau" align=center HEIGHT=16 >
			Code
		</TH>
		<TH id="titreTableau" align=center >
			Nom
		</TH>
		<TH id="titreTableau" align=center >
			Heures
		</TH>
	</TR>
	<TR>
		<TD align=center HEIGHT=16  SDVAL="10" SDNUM="1036;">
			10
		</TD>
		<TD align=center >
			Management, organisation (maintenance outils)
		</TD>
		<TD align=center  SDVAL="686,15" SDNUM="1036;">
			686,15
		</TD>
	</TR>
	<TR>
		<TD align=center HEIGHT=16  SDVAL="21" SDNUM="1036;">
			21
		</TD>
		<TD align=center >
			Inventaires
		</TD>
		<TD align=center  SDVAL="1311,2" SDNUM="1036;">
			1311,2
		</TD>
	</TR>
	<TR>
		<TD align=center HEIGHT=16  SDVAL="22" SDNUM="1036;">
			22
		</TD>
		<TD align=center >
			Modélisation
		</TD>
		<TD align=center  SDVAL="946,75" SDNUM="1036;">
			946,75
		</TD>
	</TR>
	<TR>
		<TD align=center HEIGHT=16  SDVAL="1100" SDNUM="1036;">
			1100
		</TD>
		<TD align=center >
			Surveillances des polluants (par stations)
		</TD>
		<TD align=center  SDVAL="3870,6" SDNUM="1036;">
			3870,6
		</TD>
	</TR>
	<TR>
		<TD align=center HEIGHT=16  SDVAL="1190" SDNUM="1036;">
			1190
		</TD>
		<TD align=center >
			Surveillance MERA
		</TD>
		<TD align=center  SDVAL="214,4" SDNUM="1036;">
			214,4
		</TD>
	</TR>
	<TR>
		<TD align=center HEIGHT=16  SDVAL="1300" SDNUM="1036;">
			1300
		</TD>
		<TD align=center >
			Développement interne d outils
		</TD>
		<TD align=center  SDVAL="107,17" SDNUM="1036;">
			107,17
		</TD>
	</TR>
	<TR>
		<TD align=center HEIGHT=16 >
			ID_axe3_6
		</TD>
		<TD align=center >
			Projet non défini.
		</TD>
		<TD align=center  SDVAL="1558,1" SDNUM="1036;">
			1558,1
		</TD>
	</TR>
	<TR>
		<TD align=center HEIGHT=16 >
			ID_axe3_15
		</TD>
		<TD align=center >
			Gare Dijon - 90
		</TD>
		<TD align=center  SDVAL="56" SDNUM="1036;">
			56
		</TD>
	</TR>
	<TR>
		<TD align=center HEIGHT=16 >
			ID_axe3_29
		</TD>
		<TD align=center >
			Inventaire 2010
		</TD>
		<TD align=center  SDVAL="1257,78" SDNUM="1036;">
			1257,78
		</TD>
	</TR>
</TABLE>';

}







function exemple_export_2()
{
echo '
	<TABLE WIDTH=100% border=1>
		<COL WIDTH=10%>
		<COL WIDTH=10%>
		<COL WIDTH=10%>
		<COL WIDTH=10%>
		<COL WIDTH=10%>
		<TR>
			<TH id="titreTableau" align=center HEIGHT=6 >
				Code_Axe1
			</TH>
			<TH id="titreTableau" align=center >
				Code_Axe2
			</TH>
			<TH id="titreTableau" align=center >
				Code_Axe1_Axe2
			</TH>
			<TH id="titreTableau" align=center >
				Heures
			</TH>
			<TH id="titreTableau" align=center >
				Utilisateur
			</TH>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="10" SDNUM="1036;">
				10
			</TD>
			<TD align=center align=center  SDVAL="1100" SDNUM="1036;">
				1100
			</TD>
			<TD align=center align=center  SDVAL="101100" SDNUM="1036;">
				101100
			</TD>
			<TD align=center align=center  SDVAL="47,09" SDNUM="1036;">
				47,09
			</TD>
			<TD align=center align=center >
				lorenzo
			</TD>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="10" SDNUM="1036;">
				10
			</TD>
			<TD align=center align=center  SDVAL="1100" SDNUM="1036;">
				1100
			</TD>
			<TD align=center align=center  SDVAL="101100" SDNUM="1036;">
				101100
			</TD>
			<TD align=center align=center  SDVAL="44,28" SDNUM="1036;">
				44,28
			</TD>
			<TD align=center align=center >
				ruan
			</TD>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="10" SDNUM="1036;">
				10
			</TD>
			<TD align=center align=center  SDVAL="1170" SDNUM="1036;">
				1170
			</TD>
			<TD align=center align=center  SDVAL="101170" SDNUM="1036;">
				101170
			</TD>
			<TD align=center align=center  SDVAL="2" SDNUM="1036;">
				2
			</TD>
			<TD align=center align=center >
				camillia
			</TD>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="10" SDNUM="1036;">
				10
			</TD>
			<TD align=center align=center  SDVAL="1170" SDNUM="1036;">
				1170
			</TD>
			<TD align=center align=center  SDVAL="101170" SDNUM="1036;">
				101170
			</TD>
			<TD align=center align=center  SDVAL="1" SDNUM="1036;">
				1
			</TD>
			<TD align=center align=center >
				compta
			</TD>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="11" SDNUM="1036;">
				11
			</TD>
			<TD align=center align=center  SDVAL="1100" SDNUM="1036;">
				1100
			</TD>
			<TD align=center align=center  SDVAL="111100" SDNUM="1036;">
				111100
			</TD>
			<TD align=center align=center  SDVAL="23,15" SDNUM="1036;">
				23,15
			</TD>
			<TD align=center align=center >
				camillia
			</TD>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="11" SDNUM="1036;">
				11
			</TD>
			<TD align=center align=center  SDVAL="1100" SDNUM="1036;">
				1100
			</TD>
			<TD align=center align=center  SDVAL="111100" SDNUM="1036;">
				111100
			</TD>
			<TD align=center align=center  SDVAL="190,33" SDNUM="1036;">
				190,33
			</TD>
			<TD align=center align=center >
				charlaine
			</TD>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="11" SDNUM="1036;">
				11
			</TD>
			<TD align=center align=center  SDVAL="1150" SDNUM="1036;">
				1150
			</TD>
			<TD align=center align=center  SDVAL="111150" SDNUM="1036;">
				111150
			</TD>
			<TD align=center align=center  SDVAL="1,34" SDNUM="1036;">
				1,34
			</TD>
			<TD align=center align=center >
				ruan
			</TD>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="11" SDNUM="1036;">
				11
			</TD>
			<TD align=center align=center  SDVAL="1170" SDNUM="1036;">
				1170
			</TD>
			<TD align=center align=center  SDVAL="111170" SDNUM="1036;">
				111170
			</TD>
			<TD align=center align=center  SDVAL="28,5" SDNUM="1036;">
				28,5
			</TD>
			<TD align=center align=center >
				camillia
			</TD>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="11" SDNUM="1036;">
				11
			</TD>
			<TD align=center align=center  SDVAL="2500" SDNUM="1036;">
				2500
			</TD>
			<TD align=center align=center  SDVAL="112500" SDNUM="1036;">
				112500
			</TD>
			<TD align=center align=center  SDVAL="7,5" SDNUM="1036;">
				7,5
			</TD>
			<TD align=center align=center >
				charlaine
			</TD>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="11" SDNUM="1036;">
				11
			</TD>
			<TD align=center align=center  SDVAL="5500" SDNUM="1036;">
				5500
			</TD>
			<TD align=center align=center  SDVAL="115500" SDNUM="1036;">
				115500
			</TD>
			<TD align=center align=center  SDVAL="7" SDNUM="1036;">
				7
			</TD>
			<TD align=center align=center >
				juyen
			</TD>
		</TR>
		<TR>
			<TD align=center align=center HEIGHT=6  SDVAL="11" SDNUM="1036;">
				11
			</TD>
			<TD align=center align=center  SDVAL="5500" SDNUM="1036;">
				5500
			</TD>
			<TD align=center align=center  SDVAL="115500" SDNUM="1036;">
				115500
			</TD>
			<TD align=center align=center  SDVAL="10,25" SDNUM="1036;">
				10,25
			</TD>
			<TD align=center align=center >
				ruan
			</TD>
		</TR>
	</TABLE>
	';
}

function exemple_export_3()
{
echo 
'<TABLE border=1 COLS=7 WIDTH=100% CELLPADDING=2 CELLSPACING=0>
	<COL WIDTH=5%>
	<COL WIDTH=5%>
	<COL WIDTH=5%>
	<COL WIDTH=5%>
	<COL WIDTH=15%>
	<COL WIDTH=10%>
	<COL WIDTH=5%>
	<TR>
		<TH id="titreTableau" align=center >
			Code_Axe1
		</TH>
		<TH id="titreTableau" align=center >
			Code_Axe2
		</TH>
		<TH id="titreTableau" align=center >
			Code_Axe1_Axe2
		</TH>
		<TH id="titreTableau" align=center >
			heures
		</TH>
		<TH id="titreTableau" align=center >
			Pourcentage journée
		</TH>
		<TH id="titreTableau" align=center >
			Utilisateur
		</TH>
		<TH id="titreTableau" align=center >
			date
		</TH>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			10
		</TD>
		<TD align=center SDVAL="1100" SDNUM="1036;">
			1100
		</TD>
		<TD align=center SDVAL="101100" SDNUM="1036;">
			101100
		</TD>
		<TD align=center SDVAL="7,75" SDNUM="1036;">
			7,75
		</TD>
		<TD align=center SDVAL="100" SDNUM="1036;">
			100
		</TD>
		<TD align=center>
			juyen
		</TD>
		<TD align=center>
			10/01/2013
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			10
		</TD>
		<TD align=center SDVAL="1100" SDNUM="1036;">
			1100
		</TD>
		<TD align=center SDVAL="101100" SDNUM="1036;">
			101100
		</TD>
		<TD align=center SDVAL="0,58" SDNUM="1036;">
			0,58
		</TD>
		<TD align=center SDVAL="7,91" SDNUM="1036;">
			7,91
		</TD>
		<TD align=center>
			jeannine
		</TD>
		<TD align=center>
			28/01/2013
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="24" SDNUM="1036;">
			24
		</TD>
		<TD align=center SDVAL="3000" SDNUM="1036;">
			3000
		</TD>
		<TD align=center SDVAL="243000" SDNUM="1036;">
			243000
		</TD>
		<TD align=center SDVAL="8" SDNUM="1036;">
			8
		</TD>
		<TD align=center SDVAL="100" SDNUM="1036;">
			100
		</TD>
		<TD align=center>
			jerome
		</TD>
		<TD align=center>
			19/06/2013
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="24" SDNUM="1036;">
			24
		</TD>
		<TD align=center SDVAL="4300" SDNUM="1036;">
			4300
		</TD>
		<TD align=center SDVAL="244300" SDNUM="1036;">
			244300
		</TD>
		<TD align=center SDVAL="8,25" SDNUM="1036;">
			8,25
		</TD>
		<TD align=center SDVAL="78,57" SDNUM="1036;">
			78,57
		</TD>
		<TD align=center>
			jeannine
		</TD>
		<TD align=center>
			03/10/2013
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="30" SDNUM="1036;">
			30
		</TD>
		<TD align=center SDVAL="1100" SDNUM="1036;">
			1100
		</TD>
		<TD align=center SDVAL="301100" SDNUM="1036;">
			301100
		</TD>
		<TD align=center SDVAL="0,5" SDNUM="1036;">
			0,5
		</TD>
		<TD align=center SDVAL="7,14" SDNUM="1036;">
			7,14
		</TD>
		<TD align=center>
			juyen
		</TD>
		<TD align=center>
			23/05/2013
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="30" SDNUM="1036;">
			30
		</TD>
		<TD align=center SDVAL="1400" SDNUM="1036;">
			1400
		</TD>
		<TD align=center SDVAL="301400" SDNUM="1036;">
			301400
		</TD>
		<TD align=center SDVAL="1" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="12,9" SDNUM="1036;">
			12,9
		</TD>
		<TD align=center>
			karim
		</TD>
		<TD align=center>
			16/07/2013
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="30" SDNUM="1036;">
			30
		</TD>
		<TD align=center SDVAL="1400" SDNUM="1036;">
			1400
		</TD>
		<TD align=center SDVAL="301400" SDNUM="1036;">
			301400
		</TD>
		<TD align=center SDVAL="1,55" SDNUM="1036;">
			1,55
		</TD>
		<TD align=center SDVAL="40,58" SDNUM="1036;">
			40,58
		</TD>
		<TD align=center>
			jeannine
		</TD>
		<TD align=center>
			29/07/2013
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="50" SDNUM="1036;">
			50
		</TD>
		<TD align=center SDVAL="5004" SDNUM="1036;">
			5004
		</TD>
		<TD align=center SDVAL="505004" SDNUM="1036;">
			505004
		</TD>
		<TD align=center SDVAL="1" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="11,44" SDNUM="1036;">
			11,44
		</TD>
		<TD align=center>
			jeannine
		</TD>
		<TD align=center>
			30/01/2013
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="50" SDNUM="1036;">
			50
		</TD>
		<TD align=center SDVAL="5004" SDNUM="1036;">
			5004
		</TD>
		<TD align=center SDVAL="505004" SDNUM="1036;">
			505004
		</TD>
		<TD align=center SDVAL="7,8" SDNUM="1036;">
			7,8
		</TD>
		<TD align=center SDVAL="100" SDNUM="1036;">
			100
		</TD>
		<TD align=center>
			ruan
		</TD>
		<TD align=center>
			14/02/2013
		</TD>
	</TR>
</TABLE>';
}

function exemple_export_4()
{
echo '
	<TABLE border=1 COLS=5 WIDTH=100% CELLPADDING=2 CELLSPACING=0>
	<COL WIDTH=10%>
	<COL WIDTH=10%>
	<COL WIDTH=10%>
	<COL WIDTH=10%>
	<COL WIDTH=10%>
	<TR>
		<TH id="titreTableau" align=center >

			Code_Axe1
		</TH>
		<TH id="titreTableau" align=center >

			Code_Axe2
		</TH>
		<TH id="titreTableau" align=center >

			Code_Axe1_Axe2
		</TH>
		<TH id="titreTableau" align=center >

			heures
		</TH>
		<TH id="titreTableau" align=center >

			Pourcentage du total
		</TH>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			10
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1200
		</TD>
		<TD align=center SDVAL="101200" SDNUM="1036;">
			101200
		</TD>
		<TD align=center SDVAL="0,5" SDNUM="1036;">
			0,5
		</TD>
		<TD align=center SDVAL="0,0032" SDNUM="1036;">
			0,0012
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="11" SDNUM="1036;">
			11
		</TD>
		<TD align=center SDVAL="2050" SDNUM="1036;">
			2050
		</TD>
		<TD align=center SDVAL="112050" SDNUM="1036;">
			112050
		</TD>
		<TD align=center SDVAL="23" SDNUM="1036;">
			23
		</TD>
		<TD align=center SDVAL="0,1489" SDNUM="1036;">
			0,1565
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="20" SDNUM="1036;">
			20
		</TD>
		<TD align=center SDVAL="4000" SDNUM="1036;">
			4000
		</TD>
		<TD align=center SDVAL="204000" SDNUM="1036;">
			204000
		</TD>
		<TD align=center SDVAL="7,25" SDNUM="1036;">
			7,25
		</TD>
		<TD align=center SDVAL="0,0469" SDNUM="1036;">
			0,0545
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="23" SDNUM="1036;">
			23
		</TD>
		<TD align=center SDVAL="4025" SDNUM="1036;">
			4025
		</TD>
		<TD align=center SDVAL="234025" SDNUM="1036;">
			234025
		</TD>
		<TD align=center SDVAL="5,25" SDNUM="1036;">
			5,25
		</TD>
		<TD align=center SDVAL="0,034" SDNUM="1036;">
			0,0385
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="24" SDNUM="1036;">
			24
		</TD>
		<TD align=center SDVAL="1180" SDNUM="1036;">
			1180
		</TD>
		<TD align=center SDVAL="241180" SDNUM="1036;">
			241180
		</TD>
		<TD align=center SDVAL="3,25" SDNUM="1036;">
			3,25
		</TD>
		<TD align=center SDVAL="0,021" SDNUM="1036;">
			0,0221
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="24" SDNUM="1036;">
			24
		</TD>
		<TD align=center SDVAL="2050" SDNUM="1036;">
			2050
		</TD>
		<TD align=center SDVAL="242050" SDNUM="1036;">
			242050
		</TD>
		<TD align=center SDVAL="11,5" SDNUM="1036;">
			11,5
		</TD>
		<TD align=center SDVAL="0,0745" SDNUM="1036;">
			0,0769
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="24" SDNUM="1036;">
			24
		</TD>
		<TD align=center SDVAL="4300" SDNUM="1036;">
			4300
		</TD>
		<TD align=center SDVAL="244300" SDNUM="1036;">
			244300
		</TD>
		<TD align=center SDVAL="15,75" SDNUM="1036;">
			15,75
		</TD>
		<TD align=center SDVAL="0,102" SDNUM="1036;">
			0,172
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="30" SDNUM="1036;">
			30
		</TD>
		<TD align=center SDVAL="1500" SDNUM="1036;">
			1500
		</TD>
		<TD align=center SDVAL="301500" SDNUM="1036;">
			301500
		</TD>
		<TD align=center SDVAL="4,25" SDNUM="1036;">
			4,25
		</TD>
		<TD align=center SDVAL="0,0275" SDNUM="1036;">
			0,0329
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="40" SDNUM="1036;">
			40
		</TD>
		<TD align=center SDVAL="1190" SDNUM="1036;">
			1190
		</TD>
		<TD align=center SDVAL="401190" SDNUM="1036;">
			401190
		</TD>
		<TD align=center SDVAL="1" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="0,0065" SDNUM="1036;">
			0,0136
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="41" SDNUM="1036;">
			41
		</TD>
		<TD align=center SDVAL="1402" SDNUM="1036;">
			1402
		</TD>
		<TD align=center SDVAL="411402" SDNUM="1036;">
			411402
		</TD>
		<TD align=center SDVAL="0,92" SDNUM="1036;">
			0,92
		</TD>
		<TD align=center SDVAL="0,006" SDNUM="1036;">
			0,0124
		</TD>
	</TR>
</TABLE>';

}

function exemple_export_5()
{
echo '
	<TABLE border=1 COLS=5 WIDTH=100% CELLPADDING=2 CELLSPACING=0>
	<COL WIDTH=10%>
	<COL WIDTH=10%>
	<COL WIDTH=10%>
	<COL WIDTH=10%>
	<COL WIDTH=10%>
	<COL WIDTH=10%>
	<TR>
		<TH id="titreTableau" align=center >
			Code_Axe1
		</TH>
		<TH id="titreTableau" align=center >

			Code_Axe2
		</TH>
		<TH id="titreTableau" align=center >

			Code_Axe3
		</TH>
		<TH id="titreTableau" align=center >

			Code_Axe1_Axe2
		</TH>
		<TH id="titreTableau" align=center >

			heures
		</TH>
		<TH id="titreTableau" align=center >

			Pourcentage du total
		</TH>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			10
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1200
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="101200" SDNUM="1036;">
			1012001
		</TD>
		<TD align=center SDVAL="0,5" SDNUM="1036;">
			0,5
		</TD>
		<TD align=center SDVAL="0,0032" SDNUM="1036;">
			0,0012
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="11" SDNUM="1036;">
			11
		</TD>
		<TD align=center SDVAL="2050" SDNUM="1036;">
			2050
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="112050" SDNUM="1036;">
			1120501
		</TD>
		<TD align=center SDVAL="23" SDNUM="1036;">
			23
		</TD>
		<TD align=center SDVAL="0,1489" SDNUM="1036;">
			0,1565
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="20" SDNUM="1036;">
			20
		</TD>
		<TD align=center SDVAL="4000" SDNUM="1036;">
			4000
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="204000" SDNUM="1036;">
			2040001
		</TD>
		<TD align=center SDVAL="7,25" SDNUM="1036;">
			7,25
		</TD>
		<TD align=center SDVAL="0,0469" SDNUM="1036;">
			0,0545
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="23" SDNUM="1036;">
			23
		</TD>
		<TD align=center SDVAL="4025" SDNUM="1036;">
			4025
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			0
		</TD>
		<TD align=center SDVAL="234025" SDNUM="1036;">
			2340250
		</TD>
		<TD align=center SDVAL="5,25" SDNUM="1036;">
			5,25
		</TD>
		<TD align=center SDVAL="0,034" SDNUM="1036;">
			0,0385
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="24" SDNUM="1036;">
			24
		</TD>
		<TD align=center SDVAL="1180" SDNUM="1036;">
			1180
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			0
		</TD>
		<TD align=center SDVAL="241180" SDNUM="1036;">
			2411800
		</TD>
		<TD align=center SDVAL="3,25" SDNUM="1036;">
			3,25
		</TD>
		<TD align=center SDVAL="0,021" SDNUM="1036;">
			0,0221
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="24" SDNUM="1036;">
			24
		</TD>
		<TD align=center SDVAL="2050" SDNUM="1036;">
			2050
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="242050" SDNUM="1036;">
			2420501
		</TD>
		<TD align=center SDVAL="11,5" SDNUM="1036;">
			11,5
		</TD>
		<TD align=center SDVAL="0,0745" SDNUM="1036;">
			0,0769
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="24" SDNUM="1036;">
			24
		</TD>
		<TD align=center SDVAL="4300" SDNUM="1036;">
			4300
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="244300" SDNUM="1036;">
			2443001
		</TD>
		<TD align=center SDVAL="15,75" SDNUM="1036;">
			15,75
		</TD>
		<TD align=center SDVAL="0,102" SDNUM="1036;">
			0,172
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="30" SDNUM="1036;">
			30
		</TD>
		<TD align=center SDVAL="1500" SDNUM="1036;">
			1500
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="301500" SDNUM="1036;">
			3015001
		</TD>
		<TD align=center SDVAL="4,25" SDNUM="1036;">
			4,25
		</TD>
		<TD align=center SDVAL="0,0275" SDNUM="1036;">
			0,0329
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="40" SDNUM="1036;">
			40
		</TD>
		<TD align=center SDVAL="1190" SDNUM="1036;">
			1190
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			0
		</TD>
		<TD align=center SDVAL="401190" SDNUM="1036;">
			4011900
		</TD>
		<TD align=center SDVAL="1" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="0,0065" SDNUM="1036;">
			0,0136
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="41" SDNUM="1036;">
			41
		</TD>
		<TD align=center SDVAL="1402" SDNUM="1036;">
			1402
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			0
		</TD>
		<TD align=center SDVAL="411402" SDNUM="1036;">
			4114020
		</TD>
		<TD align=center SDVAL="0,92" SDNUM="1036;">
			0,92
		</TD>
		<TD align=center SDVAL="0,006" SDNUM="1036;">
			0,0124
		</TD>
	</TR>
</TABLE>';

}


function exemple_export_7()
{
echo '
	<TABLE border=1 COLS=5 WIDTH=100% CELLPADDING=2 CELLSPACING=0>
	<COL WIDTH=30%>
	<COL WIDTH=30%>
	<COL WIDTH=30%>
	<TR>
		<TH id="titreTableau" align=center >
			Date
		</TH>
		<TH id="titreTableau" align=center >
			Utilisateur
		</TH>
		<TH id="titreTableau" align=center >
			Nombre heures
		</TH>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-06
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Varot.D
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			8.10
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-06
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Martin.H
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			5.70
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-06
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Thomas.L
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			5.60
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-07
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Varot.D
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			10.80
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-07
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Martin.H
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			7.80
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-07
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Thomas.L
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			7.80
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-08
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Varot.D
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			7.80
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-08
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Martin.H
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			8.60
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-08
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Thomas.L
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			6.90
		</TD>
	</TR>

</TABLE>';

}


function exemple_export_8()
{
echo '
	<TABLE border=1 COLS=5 WIDTH=100% CELLPADDING=2 CELLSPACING=0>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<COL WIDTH=8%>
	<TR>
		<TH id="titreTableau" align=center >
			Date
		</TH>
		<TH id="titreTableau" align=center >
			Utilisateur
		</TH>
		<TH id="titreTableau" align=center >
			Heure départ
		</TH>
		<TH id="titreTableau" align=center >
			Heure fin
		</TH>
		<TH id="titreTableau" align=center >
			Code axe 1
		</TH>
		<TH id="titreTableau" align=center >
			Nom axe 1
		</TH>
		<TH id="titreTableau" align=center >
			Code axe 2
		</TH>
		<TH id="titreTableau" align=center >
			Nom axe 2
		</TH>
		<TH id="titreTableau" align=center >
			Code axe 3
		</TH>
		<TH id="titreTableau" align=center >
			Nom axe 3
		</TH>
		<TH id="titreTableau" align=center >
			Total heures
		</TH>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-06
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Varot.D
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			8
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			11.5
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			6
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Qualibrage
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			26
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Appareil CO2
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Projet
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			3.5
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-06
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Varot.D
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			11.5
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			12.00
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			6
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Qualibrage
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			26
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Appareil CO2
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Projet
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			0.5
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-06
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Balin.r
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			8.00
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			11.5
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			6
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Qualibrage
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			26
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Appareil CO2
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			1
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Projet
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			3.5
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-06
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Lidon.Y
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			8.00
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			11.5
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			8
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Réunion
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			31
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Air intérieur
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			4
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Commercial
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			3.5
		</TD>
	</TR>
	<TR>
		<TD align=center SDVAL="10" SDNUM="1036;">
			2014-08-07
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Varot.D
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			9.00
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			12
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			12
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Formation
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			12
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Informatique
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			2
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			Formation
		</TD>
		<TD align=center SDVAL="1200" SDNUM="1036;">
			3
		</TD>
	</TR>

</TABLE>';

}


?>
