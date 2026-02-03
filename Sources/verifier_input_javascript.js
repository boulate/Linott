function checkInput(aVerif)
{
	if(aVerif == "")
	{
		alert("Le champ ne peut pas être vide.");
		throw "stop execution";
	}
	if(aVerif.replace(/ /g,"") == "")
	{
		alert("Le champ ne peut pas contenir uniquement des espaces.");
		throw "stop execution";
	}
	if(aVerif.indexOf("\\")!=-1)
	{
		alert("le caractère \" \\ \" n'est pas autorisé. Vous pouvez utiliser des chiffres, des lettres, et les caractères spéciaux \"-\", \"_\", \".\" ou \"/\" .");
		throw "stop execution";
	}
	if(aVerif.indexOf("\'")!=-1)
	{
		alert("le caractère \" \' \" n'est pas autorisé. Vous pouvez utiliser des chiffres, des lettres, et les caractères spéciaux \"-\", \"_\", \".\" ou \"/\" .");
		throw "stop execution";
	}
	if(aVerif.indexOf("\"")!=-1)
	{
		alert("le caractère \" \" \" n'est pas autorisé. Vous pouvez utiliser des chiffres, des lettres, et les caractères spéciaux \"-\", \"_\", \".\" ou \"/\" .");
		throw "stop execution";
	}
	
	
	
// 	if(aVerif.indexOf("\,")!=-1)
// 	{
// 		alert("le caractère \"\\\" n'est pas autorisé. Vous pouvez utiliser des chiffres, des lettres, et les caractères spéciaux \"-\", \"_\", \".\" ou \"/\" .");
// 		throw "stop execution";
// 	}
}

function checkNum(type, aVerif)
{

	ExpressionReguliere	= null;
	ExpressionReguliere2	= null;
	ExpressionReguliere3	= null;
	ExpressionReguliere4	= null;
	
		// Test de la chaine rentrée: Si axe1: Seulement 2 chiffres, si Axe2, seulement 4 chiffres, etc.
		if (type == "Axe1")
		{
			ExpressionReguliere = "(^[0-9][0-9]$)";
		}
		if (type == "Axe2")
		{
			ExpressionReguliere = "(^[0-9][0-9][0-9][0-9]$)";
		}
		if (type == "Section")
		{
			ExpressionReguliere = "(^[0-9]$)";
		}
		if (type == "heuresSemaine")
		{
			ExpressionReguliere = "(^[0-9][0-9].[0-9][0-9]$)";
			ExpressionReguliere2 = "(^[0-9][0-9]$)";
		}
		if (type == "rachatHeures")
		{
			ExpressionReguliere = "(^-?[0-9][0-9]$)";
			ExpressionReguliere2 = "(^-?[0-9]$)";
			ExpressionReguliere3 = "(^-?[0-9][0-9].[0-9][0-9]$)";
			ExpressionReguliere4 = "(^-?[0-9].[0-9][0-9]$)";
		}
		
//		Teste si la chaine de caractère rentre dans le format de l’expression régulière
		if(	(aVerif.match(ExpressionReguliere)) || (aVerif.match(ExpressionReguliere2)) || (aVerif.match(ExpressionReguliere3)) || (aVerif.match(ExpressionReguliere4))	 )
		{
			return true;
		}
		else
		{
			if (type == "Axe1")
			{
				alert("Le format de l'axe 1 doit contenir 2 chiffres uniquement.");
				throw "stop execution";
			}
			if (type == "Axe2")
			{
				alert("Le format de l'axe 2 doit contenir 4 chiffres uniquement.");
				throw "stop execution";
			}
			if (type == "Section")
			{
				alert("Le format de section doit contenir 1 chiffre uniquement.");
				throw "stop execution";
			}
			if (type == "heuresSemaine")
			{
				alert("Le nombre d'heures par semaine doit être sous le format:\n\n \"hh.mm\" ou \"hh\" \n\n Exemple pour 39 heures par semaine: \"39.00\" ou \"39\"");
				throw "stop execution";
			}
			if (type == "rachatHeures")
			{
				alert("Le format de rachat d'heures doit contenir 2 chiffres uniquement.");
				throw "stop execution";
			}
			else
			{
				alert("Le format renseigné semble ne pas être correct.");
				throw "stop execution";
			}
		}
} 
