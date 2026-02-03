<?php include("Configuration/menu.php"); ?>

<script>
function change_couleur_bouton_menu_general()
{
	url_en_cours = document.location.href;
	//alert(url_en_cours);

	if	(url_en_cours.indexOf("compta.php") != -1 )
	{
		document.getElementById('bouton_retour').style.opacity = "0.3";
	}

	if (	(url_en_cours.indexOf("compta.php") != -1 )	|| (url_en_cours.indexOf("statsAxes.php") != -1 ) || (url_en_cours.indexOf("statsConges.php") != -1 ) || (url_en_cours.indexOf("choix_type_export.php") != -1 )	 )
	{
		document.getElementById('bouton_heures').style.backgroundColor = "#729fcf";
		document.getElementById('bouton_heures').style.color = "#D2D2D2";
	}

	if ( url_en_cours.indexOf("calendrier.php") != -1 )
	{
		document.getElementById('bouton_calendrier').style.backgroundColor = "#729fcf";
		document.getElementById('bouton_calendrier').style.color = "#D2D2D2";
	}

	if (	( url_en_cours.indexOf("fiches_actions.php") != -1 ) ||	( url_en_cours.indexOf("creer_fiche.php") != -1 ) || ( url_en_cours.indexOf("editer_fiche.php") != -1 )	 ||	( url_en_cours.indexOf("statistiques.php") != -1 ) ||	( url_en_cours.indexOf("editer_fiche.php") != -1 )	)
	{
		document.getElementById('bouton_fiches_actions').style.backgroundColor = "#729fcf";
		document.getElementById('bouton_fiches_actions').style.color = "#D2D2D2";

		if ( url_en_cours.indexOf("creer_fiche.php") != -1 )
		{
			document.getElementById('bouton_creer_fiche').style.backgroundColor = "#729fcf";
			document.getElementById('bouton_creer_fiche').style.color = "#D2D2D2";
		}
		if ( url_en_cours.indexOf("fiches_actions.php") != -1 )
		{
			document.getElementById('bouton_fiches_actions2').style.backgroundColor = "#729fcf";
			document.getElementById('bouton_fiches_actions2').style.color = "#D2D2D2";		
		}
		if ( url_en_cours.indexOf("statistiques.php") != -1 )
		{
			document.getElementById('bouton_statistiques').style.backgroundColor = "#729fcf";
			document.getElementById('bouton_statistiques').style.color = "#D2D2D2";
		}
	}

	if (	( url_en_cours.indexOf("preferences.php") != -1 )	|| ( url_en_cours.indexOf("fenetre_preference_axe1.php") != -1 )	|| ( url_en_cours.indexOf("fenetre_preference_axe2.php") != -1 )	|| ( url_en_cours.indexOf("fenetre_preference_axe3.php") != -1 )		)
	{
		document.getElementById('bouton_preferences').style.backgroundColor = "#729fcf";
		document.getElementById('bouton_preferences').style.color = "#D2D2D2";
	}

	if (	( url_en_cours.indexOf("administration.php") != -1) || (url_en_cours.indexOf("consulter_fiche.php") != -1 )		)
	{
		document.getElementById('bouton_administration').style.backgroundColor = "#729fcf";
		document.getElementById('bouton_administration').style.color = "#D2D2D2";
	}
}
</script>



<table border=0 width=100% id="tableMenuGeneral">
	<tr>
		<td width=18%>
			<?php
				$admin	=	$_SESSION['admin'];
				$prenom	=	$_SESSION['prenom'];
				$page_en_cours = $_SERVER['REQUEST_URI'];
			
				echo '<input type=button id="bouton_retour" value="Retour"  onClick="retourFiche()" />';
				
				if ( !strstr($page_en_cours, "Qualitatmo" ) )
				{
					echo ' <input type="button" id="bouton_deconnexion" value="Déconnexion" onClick="javascript:document.location.href=\'deconnexion.php\'" />';
				}
				else
				{
					echo ' <input type="button" id="bouton_deconnexion" value="Déconnexion" onClick="javascript:document.location.href=\'../deconnexion.php\'" />';
				}
			?>
		</td>		

		<th align=right>
			<!-- Déclaration d'heures -->
		</th>

		<td width=45% align=right>
			<?php 

				if ( strstr($page_en_cours, "Qualitatmo" ) )
				{
					echo ' <input type="button" value="Heures" id="bouton_heures" onClick="javascript:document.location.href=\'../compta.php\'" />';		
					echo ' <input type="button" value="Calendrier" id="bouton_calendrier" onClick="javascript:document.location.href=\'../calendrier.php\'" />';		
					echo ' <input type="button" value="Fiches actions" id="bouton_fiches_actions" onClick="javascript:document.location.href=\'fiches_actions.php\'" />';
					echo ' <input type="button" value="Préférences" id="bouton_preferences" onClick="javascript:document.location.href=\'../preferences.php\'" />';
					// Si le compte a les droits admin, on ajoute la zone administration.
					if ($admin == 1 )
					{
						echo ' <input type="button" value="Administration" id="bouton_administration" onClick="javascript:document.location.href=\'../administration.php\'" />';		
					}
				}
				else
				{
					echo ' <input type="button" value="Heures" id="bouton_heures" onClick="javascript:document.location.href=\'compta.php\'" />';		
					echo ' <input type="button" value="Calendrier" id="bouton_calendrier" onClick="javascript:document.location.href=\'calendrier.php\'" />';		
					if ($afficher_menu_fiches_actions == 1)
					{
						echo ' <input type="button" value="Fiches actions" id="bouton_fiches_actions" onClick="javascript:document.location.href=\'Qualitatmo/fiches_actions.php\'" />';		
					}
					else
					{
						echo ' <input type="hidden" value="Fiches actions" id="bouton_fiches_actions" />';								
					}
					echo ' <input type="button" value="Préférences" id="bouton_preferences" onClick="javascript:document.location.href=\'preferences.php\'" />';
					// Si le compte a les droits admin, on ajoute la zone administration.
					if ($admin == 1 )
					{
						echo ' <input type="button" value="Administration" id="bouton_administration" onClick="javascript:document.location.href=\'administration.php\'" />';		
					}
					else
					{
						echo ' <input type="hidden" value="Administration" id="bouton_administration" />';		
					}
				}

			?>
		</td>
	</tr>
</table>
<table border=0 width=100% id="tableLogo">
	<tr>
		<td width=18%>
		</td>
		
		<td align=center>
			<?php

				$logo="Logo_Linott.png";


				if ( strstr($page_en_cours, "Qualitatmo" ) )
				{
					echo "<a href=''><img src=../$logo /> </a>";
				}
				else
				{
					echo "<a href=''><img src=$logo /> </a>";
				}
			?>
		</td>

		<td width=26% align=center>
		</td>
	</tr>
</table>

