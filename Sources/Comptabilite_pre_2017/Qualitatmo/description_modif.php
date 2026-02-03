<table border=0 width=100% id='tableOngletsBleu'>

	<tr>

		<td width=50% align=left>
			<br />
				Date du dysfonctionnement / de la proposition d'amélioration :
			<br />
		</td>

		<td width=50%>
			<br />
			 <input type="text" name="date_anomalie" value="<?php echo $date_anomalie ?>" />
			<br />
		</td>
	</tr>

	<tr>
		<td align=center>
				Faits :<br />
				<textarea name="description_faits" rows=3 cols=35><?php echo $faits ?></textarea>
		</td>


		<td align=center>
				Causes :<br />
				<textarea name="description_causes" rows=3 cols=35><?php echo $causes ?></textarea>		
		</td>

		<td align=center>
				Consequences :<br />
				<textarea name="description_consequences" rows=3 cols=35><?php echo $consequences ?></textarea>		
		</td>

	</tr>
</table>


<table border=0 width=100% id='tableBleu'>
<tr>
	<td align=center>
				Action(s) curative(s) :<br />
				<textarea name="actions_court_terme" rows=3 cols=60><?php echo $actions_court_terme ?></textarea>
	</td>
	<td>
		----->
	</td>
	<td>
				Commentaire sur la ou les action(s) curative(s) :
				<textarea name="commentaire_actions_court_terme" rows=3 cols=60><?php echo $commentaire_actions_court_terme ?></textarea>
	</td>
</tr>
</table>
