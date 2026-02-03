<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

<head>
	<title>Gestion des fiches de conformite</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body>
<table width=100% bgcolor=silver>
	<tr>
		<td width=20%>
		<p>
		<input type="radio" name="incidence_qualite" value="Aucune non conformite" id="1" /> <label for="1">Aucune non conformite</label>
		<br><input type="radio" name="incidence_qualite" value="Non conformite" id="2" /> <label for="2">Non conformite</label>
		<br><input type="radio" name="incidence_qualite" value="Non conformite majeure" id="3" /> <label for="3">Non conformite majeure</label>
		<br></p>
		</td>
		<td width=30%>
		Commentaire :<br />
		<textarea name="commentaire_indice_qualite" rows=3 cols=40></textarea>
		</td>


		<td width=20%>
		<p>
		Action sur produit/service:
		<br><input type="radio" name="action_sur_produit" value="oui" id="oui" /> <label for="oui">Oui</label>
		<br><input type="radio" name="action_sur_produit" value="non" id="non" /> <label for="non">Non</label>
		<br></p>
		</td>
		<td width=30%>
		Commentaire :<br />
		<textarea name="commentaire_action_sur_produit" rows=3 cols=40></textarea>
		</td>
	</tr>
</table>
<table width=100% border=1 bgcolor=silver>
	<tr>
		<td width=1%>
		Besoin d'actions :
		</td>
		<td width=10%>
		Type C/P/A
		</td>
		<td width=15%>
		Realisateur(s)
		</td>
		<td width=20%>
		Delai previsionnel
		</td>
		<!--
		<td width=25%>
		Realisation
		</td>
		-->
	</tr>
	<tr>
		<td>
			<textarea name="besoin_actions" rows=3 cols=50></textarea>
		</td>
		<td align=center>
			<select name="CPA" id="CPA">
				<option value=""></option>
				<option value="Correctif">Correctif</option>
				<option value="Preventif">Preventif</option>
				<option value="Amelioration">Amelioration</option>
			</select>
		</td>
		<td align=center>
			<input type="text" name="realisateur"/>
		</td>
		<td align=center>
			<input type="text" name="delai"/>
		</td>
		<!-- 
		<td align=center>
			<input type="text" name="realisation"/>
		</td>		
		-->
</table>
<table width=100% bgcolor=silver>
	<tr>
		<!--
		<td>
			Elements justificatifs :<br />
			<textarea name="elements_justificatifs" rows=3 cols=50></textarea>
		</td>
		-->
		
		<td>
			Verification de l'efficacite des actions menees :<br />
			<textarea name="efficacite_action" rows=3 cols=50></textarea>
		</td>

		<td>
			Cloture le:<br>
			<input type="text" name="cloture"/>
		</td>
		<td>	
			Visa du responsable :<br />
			<input type="text" name="visa_responsable"/>
		</td>
		<td>	
			Visa de la direction :<br />
			<input type="text" name="visa_direction"/>*<br />
			* Requis en cas de<br /> 
			non conformite majeure.
		</td>
	</tr>		

</table>




</body>
</html>

