
<head>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">

	</SCRIPT>
</head>




<body>

	<?php
		// Nombre de lignes totales de la page (matin et après midi compris). Nombre de ligne prend ensuite cette valeur pour être decrémentée dans les boucles. Periode sera comparé à nombre_lignes_total pour sortir de la boucle au bon moment.
		$nombre_lignes_total=12;
		// On le déclare ensuite dans une session pour pouvoir le récuperer dans la page d'apres.
		$_SESSION['nombre_lignes_total'] = $nombre_lignes_total; 
		// Nombre_lignes est égal à nombre_lignes_total mais c'est lui qui va être décrémenté dans la boucle.
		$nombre_lignes=$nombre_lignes_total;

		// Periode permet d incrementer les pages qui seront ouvertes via le clic sur la zone text. adresse.php?periode=1. Quand période dépasse la moitié de nombre_lignes_total la boucle se termine.
		$periode=1; 
	?>
		<!-- Permet de rentrer la variable php $nombre_lignes_total dans javascript en variable repetitions -->
		<script> javascript:nombre_lignes_a_additionner(<?php echo "$nombre_lignes_total" ?>); </script>

<table border=0 width=100% >
  <tr>
    <td width=3%></td>
    <td>
		
	<table border=0><tr height=5px><td></td></tr>
	</table>
	
	<table border=0 width=100%>
		<tr>
			<th width=15% bgcolor="#B0DD8D" id=matinAprem>
				Matin:
			</th>

			<td>
			</td>

		</tr>
	</table>
	<table border=0 width=100% bgcolor="#B0DD8D" id=tableHeures>
		    <tr><td></td></tr> <!-- Petite ligne pour ajouter une epaisseur en haut -->

		<?php
		// Tant que période est inférieur à nombre_lignes_total/2 on reste dans la boucle (on sort ensuite pour passer à l après midi).
		while($periode<=$nombre_lignes_total/2)
			{
			?>
				<tr id=deA>
				<td align=center id=deA>
						<!-- Heure de debut sur cette periode -->
						De <input type="numeric" step="any" id="deMatin_periode<?php echo "$periode"?>" onKeyUp="javascript:calcul_periode('deMatin_periode<?php echo "$periode"?>','aMatin_periode<?php echo "$periode"?>','totalMatin_periode<?php echo "$periode"?>')" onBlur="javascript:calcul_periode('deMatin_periode<?php echo "$periode"?>','aMatin_periode<?php echo "$periode"?>','totalMatin_periode<?php echo "$periode"?>')" name="deMatin_periode<?php echo "$periode"?>"/> 
						<!-- Heure de fin sur cette periode -->
						à <input type="numeric" step="any" id="aMatin_periode<?php echo "$periode"?>" onKeyUp="javascript:calcul_periode('deMatin_periode<?php echo "$periode"?>','aMatin_periode<?php echo "$periode"?>','totalMatin_periode<?php echo "$periode"?>')" onBlur="javascript:calcul_periode('deMatin_periode<?php echo "$periode"?>','aMatin_periode<?php echo "$periode"?>','totalMatin_periode<?php echo "$periode"?>')" name="aMatin_periode<?php echo "$periode"?>"/> : 

					

						<!-- Choix axe 1 sur cette periode -->
						<input type="text" id="choix_axe1_periode<?php echo "$periode"?>" name="choix_axe1_periode<?php echo "$periode"?>"  value="Choisir axe 1" onKeyUp=javascript:affichage_popup('fenetre_choix_axe1.php?periode=<?php echo "$periode"?>','popup_axe1_periodes'); onClick=javascript:affichage_popup('fenetre_choix_axe1.php?periode=<?php echo "$periode"?>','popup_axe1_periodes') readonly> 
						<input type="hidden" id="id_choix_axe1_periode<?php echo "$periode"?>" name="id_choix_axe1_periode<?php echo "$periode"?>"> 
					
						<!-- Choix axe 2 sur cette periode -->
						<input type="text" id="choix_axe2_periode<?php echo "$periode"?>" name="choix_axe2_periode<?php echo "$periode"?>"  value="Choisir axe 2" onKeyUp=javascript:affichage_popup('fenetre_choix_axe2.php?periode=<?php echo "$periode"?>','popup_axe2_periodes'); onClick=javascript:affichage_popup('fenetre_choix_axe2.php?periode=<?php echo "$periode"?>','popup_axe2_periodes') readonly> 
						<input type="hidden" id="id_choix_axe2_periode<?php echo "$periode"?>" name="id_choix_axe2_periode<?php echo "$periode"?>"> 

						<!-- Choix Axe3 sur cette periode -->
						<?php 
							if ( $activerAxe3 == "checked" )
							{
								$typeAxe3="text";
							}
							else
							{
								$typeAxe3="hidden";
							}
						?>

						<input type="<?php echo $typeAxe3 ?>" id="choix_axe3_periode<?php echo "$periode"?>" name="choix_axe3_periode<?php echo "$periode"?>"  value="Choisir axe 3" onKeyUp=javascript:affichage_popup('fenetre_choix_axe3.php?periode=<?php echo "$periode"?>','popup_axe3_periodes'); onClick=javascript:affichage_popup('fenetre_choix_axe3.php?periode=<?php echo "$periode"?>','popup_axe3_periodes') readonly> 					
						<input type="hidden" id="id_choix_axe3_periode<?php echo "$periode"?>" name="id_choix_axe3_periode<?php echo "$periode"?>"> 

						= <input type="numeric" step="any" id="totalMatin_periode<?php echo "$periode"?>" name="totalMatin_periode<?php echo "$periode"?>" onClick="javascript:supprimer_periode('<?php echo "$periode"?>')" readonly/> heures.

						<input type="hidden" id="id_horaire_periode<?php echo "$periode"?>" name="id_horaire_periode<?php echo "$periode"?>" >

				</td>
				</tr>

		<?php		
			$nombre_lignes--;
			$periode++;
		}
		?>
	    <tr><td></td></tr> <!-- Petite ligne pour ajouter une epaisseur en bas -->
	</table>

	<table border=0><tr height=20px><td></td></tr>
	</table>
	
	<!----------------------------------------->
	<!-- On attaque la table de l apres midi -->
	<table border=0 width=100%>
		<tr>
			<th width=15% bgcolor="#9BD6F9" id=matinAprem>
			  Après-midi:
			</th>
			<td>
			</td>
		</tr>
	</table>
	<table width=100% bgcolor="#9BD6F9" id=tableHeures>
		<tr><td></td></tr> <!-- Petite ligne pour ajouter une epaisseur en haut -->

		<?php
		// Tant que période est inférieur à nombre_lignes_total on reste dans la boucle.
		while($periode<=$nombre_lignes_total)
			{
			?>
				<tr>
				<td align=center>
						<!-- Heure de debut sur cette periode -->
						De <input type="numeric" step="any" id="deAprem_periode<?php echo "$periode"?>" onKeyUp="javascript:calcul_periode('deAprem_periode<?php echo "$periode"?>','aAprem_periode<?php echo "$periode"?>','totalAprem_periode<?php echo "$periode"?>')" onBlur="javascript:calcul_periode('deAprem_periode<?php echo "$periode"?>','aAprem_periode<?php echo "$periode"?>','totalAprem_periode<?php echo "$periode"?>')" name="deAprem_periode<?php echo "$periode"?>"/> 
						<!-- Heure de fin sur cette periode -->
						à <input type="numeric" step="any" id="aAprem_periode<?php echo "$periode"?>" onKeyUp="javascript:calcul_periode('deAprem_periode<?php echo "$periode"?>','aAprem_periode<?php echo "$periode"?>','totalAprem_periode<?php echo "$periode"?>')" onBlur="javascript:calcul_periode('deAprem_periode<?php echo "$periode"?>','aAprem_periode<?php echo "$periode"?>','totalAprem_periode<?php echo "$periode"?>')" name="aAprem_periode<?php echo "$periode"?>"/> : 

					

						<!-- Choix axe 1 sur cette periode -->
						<input type="text" id="choix_axe1_periode<?php echo "$periode"?>" name="choix_axe1_periode<?php echo "$periode"?>"  value="Choisir axe 1" onKeyUp=javascript:affichage_popup('fenetre_choix_axe1.php?periode=<?php echo "$periode"?>','popup_axe1_periodes'); onClick=javascript:affichage_popup('fenetre_choix_axe1.php?periode=<?php echo "$periode"?>','popup_axe1_periodes') readonly> 
						<input type="hidden" id="id_choix_axe1_periode<?php echo "$periode"?>" name="id_choix_axe1_periode<?php echo "$periode"?>"> 

						<!-- Choix axe 2 sur cette periode -->
						<input type="text" id="choix_axe2_periode<?php echo "$periode"?>" name="choix_axe2_periode<?php echo "$periode"?>"  value="Choisir axe 2" onKeyUp=javascript:affichage_popup('fenetre_choix_axe2.php?periode=<?php echo "$periode"?>','popup_axe2_periodes'); onClick=javascript:affichage_popup('fenetre_choix_axe2.php?periode=<?php echo "$periode"?>','popup_axe2_periodes') readonly> 
						<input type="hidden" id="id_choix_axe2_periode<?php echo "$periode"?>" name="id_choix_axe2_periode<?php echo "$periode"?>"> 

						<!-- Choix Axe3 sur cette periode -->
												<?php 
							if ( $activerAxe3 == "checked" )
							{
								$typeAxe3="text";
							}
							else
							{
								$typeAxe3="hidden";
							}
						?>

						<input type="<?php echo $typeAxe3 ?>" id="choix_axe3_periode<?php echo "$periode"?>" name="choix_axe3_periode<?php echo "$periode"?>"  value="Choisir axe 3" onKeyUp=javascript:affichage_popup('fenetre_choix_axe3.php?periode=<?php echo "$periode"?>','popup_axe3_periodes'); onClick=javascript:affichage_popup('fenetre_choix_axe3.php?periode=<?php echo "$periode"?>','popup_axe3_periodes') readonly> 					
						<input type="hidden" id="id_choix_axe3_periode<?php echo "$periode"?>" name="id_choix_axe3_periode<?php echo "$periode"?>"> 

						= <input type="numeric" step="any" id="totalAprem_periode<?php echo "$periode"?>" name="totalAprem_periode<?php echo "$periode"?>" onClick="javascript:supprimer_periode('<?php echo "$periode"?>')" readonly/> heures.

						<input type="hidden" id="id_horaire_periode<?php echo "$periode"?>" name="id_horaire_periode<?php echo "$periode"?>">
				</td>
				</tr>

		<?php		
			$nombre_lignes--;
			$periode++;
		}
		?>
		<tr><td></td></tr> <!-- Petite ligne pour ajouter une epaisseur en bas -->
	</table>
   </td> <td width=3%>

</td></tr></table>

	
	
</body>
