<?php


function checkInput($aVerif, $type)
{

	$ExpressionReguliere	= null;
	$ExpressionReguliere2	= null;
	$ExpressionReguliere3	= null;
	$ExpressionReguliere4	= null;
	

	if( ($aVerif == "") && ($type != "axe2_exclus_totaux") && ($type != "nbrConges") && ($type != "nbrRTT") )
	{
		return "Les champs ne peuvent pas être vide.";
	}
	elseif( (str_replace(' ','',$aVerif) == "")   && ($type != "axe2_exclus_totaux") && ($type != "nbrConges") && ($type != "nbrRTT") )
	{
		return "Un champ ne peut pas contenir uniquement des espaces.";
	}
	elseif( strstr($aVerif, "\\"))
	{
		return "le caractère  \\  n'est pas autorisé. Vous pouvez utiliser des chiffres, des lettres, et les caractères spéciaux \"-\" ou \"_\".";
	}
	elseif( strstr($aVerif, "'"))
	{
		return "le caractère  '  n'est pas autorisé. Vous pouvez utiliser des chiffres, des lettres, et les caractères spéciaux \"-\" ou \"_\".";
	}
	elseif( strstr($aVerif, "\""))
	{
		return "le caractère  \"  n'est pas autorisé. Vous pouvez utiliser des chiffres, des lettres, et les caractères spéciaux \"-\" ou \"_\".";
	}
	elseif( (	strstr($aVerif, ",") ) && ($type != "axe2_exclus_totaux") )
	{
		return "le caractère  ,  n'est pas autorisé. Vous pouvez utiliser des chiffres, des lettres, et les caractères spéciaux \"-\" ou \"_\".";
	}
	
	
	// Test de la chaine rentrée: Si axe1: Seulement 2 chiffres, si Axe2, seulement 4 chiffres, etc.
	elseif ($type == "Axe1")
	{
		$ExpressionReguliere = "(^[0-9][0-9]$)";
	}
	elseif ($type == "Axe2")
	{
		$ExpressionReguliere = "(^[0-9][0-9][0-9][0-9]$)";
	}
	elseif ($type == "Axe3")
	{
		$ExpressionReguliere = "(^[0-9]{1,2}$)";
	}
	elseif ($type == "Mois")
	{
		$ExpressionReguliere = "(^[1-9]$)";
		$ExpressionReguliere2 = "(^[1][0-2]$)";
		$ExpressionReguliere3 = "(^[0][1-9]$)";
	}
	elseif ($type == "nbrJoursConges")
	{
		$ExpressionReguliere = "(^[0-9][0-9]$)";
	}
	elseif ($type == "nbrJoursRTT")
	{
		$ExpressionReguliere2 = "(^[0-9]$)";
		$ExpressionReguliere = "(^[0-9][0-9]$)";
	}
	elseif ($type == "Section")
	{
		$ExpressionReguliere = "(^[0-9]{1,2}$)";
	}
	elseif ($type == "rachatHeures")
	{
		$ExpressionReguliere = "(^-?[0-9][0-9]$)";
		$ExpressionReguliere2 = "(^-?[0-9]$)";
		$ExpressionReguliere3 = "(^-?[0-9][0-9].[0-9][0-9]$)";
		$ExpressionReguliere4 = "(^-?[0-9].[0-9][0-9]$)";
	}
	elseif ($type == "login")
	{
		$ExpressionReguliere = "(^[a-z]+\.{0,1}[a-z]*[0-9]{0,2}$)";
		if(substr($aVerif,-1) == '.')
		{
			return "Un login ne peut pas finir par un \" . \"";
		}
		if(substr($aVerif,0,1) == '.')
		{
			return "Un login ne peut pas commencer par un \" . \"";
		}
	}
	elseif ($type == "id")
	{
		$ExpressionReguliere = "(^[0-9]+$)";
	}
	elseif (($type == "nom") || ($type == "prenom")	)
	{
		$ExpressionReguliere = "(^[A-Za-z àáâãäåòóôõöøèéêëçìíîïùúûüÿñ-]+)";
		if(substr($aVerif,-1) == '-')
		{
			return "Un nom ou prénom ne peut pas finir par un \" - \"";
		}
		if(substr($aVerif,0,1) == '-')
		{
			return "Un nom ou prénom ne peut pas commencer par un \" - \"";
		}
	}
	elseif ($type == "heuresSemaine")
	{
		$ExpressionReguliere = "(^[0-9][0-9].[0-9][0-9]$)";
		$ExpressionReguliere2 = "(^[0-9][0-9]$)";
	}
	elseif ($type == "nbrConges")
	{
		$ExpressionReguliere = "(^[0-9][0-9].[0-9][0-9]$)";
		$ExpressionReguliere2 = "(^[0-9][0-9]$)";
		$ExpressionReguliere3 = "(^[0-9]$)";
		$ExpressionReguliere4 = "(^[0-9].[0-9][0-9]$)";
	}
	elseif ($type == "nbrRTT")
	{
		$ExpressionReguliere = "(^[0-9][0-9].[0-9][0-9]$)";
		$ExpressionReguliere2 = "(^[0-9][0-9]$)";
		$ExpressionReguliere3 = "(^[0-9]$)";
		$ExpressionReguliere4 = "(^[0-9].[0-9][0-9]$)";
	}
	elseif ($type == "couleur")
	{
		if ($aVerif == "")
		{
			$couleur = "White";
		}
		$ExpressionReguliere = "(^[0-9a-zA-Z]{3,6}$)";
	}
	elseif ($type == "caseACocher")
	{
		$ExpressionReguliere = "(^[0-1]?$)";
	}
	elseif ($type == "axe2_exclus_totaux")
	{
		$ExpressionReguliere = "(^[0-9,]{0,255}$)";
	}


	else
	{
		return "ok";
	}
	
//		Teste si la chaine de caractère rentre dans le format de l’expression régulière
	//if(	($aVerif.match($ExpressionReguliere)) || ($aVerif.match($ExpressionReguliere2)) || ($aVerif.match($ExpressionReguliere3)) || ($aVerif.match($ExpressionReguliere4))	 )
	if(	(preg_match($ExpressionReguliere, $aVerif) == 1) || (preg_match($ExpressionReguliere2, $aVerif) == 1) || (preg_match($ExpressionReguliere3, $aVerif) == 1) || (preg_match($ExpressionReguliere4, $aVerif) == 1)	 )
	{
		return "ok";
	}
	else
	{
		if ($type == "Axe1")
		{
			return "Le format de l'axe 1 doit contenir 2 chiffres uniquement.";
		}
		if ($type == "Axe2")
		{
			return "Le format de l'axe 2 doit contenir 4 chiffres uniquement.";
		}
		if ($type == "Axe3")
		{
			return "Le format de l'axe 3 doit contenir 1 ou 2 chiffres uniquement.";
		}
		if ($type == "Section")
		{
			return "Le format de section doit contenir 1 ou 2 chiffres uniquement.";
		}
		if ($type == "Mois")
		{
			return "Un mois doit avoir une valeur comprise entre 1 et 12.";
		}
		if ($type == "rachatHeures")
		{
			return "Le format de rachat d'heures doit contenir 2 chiffres uniquement.";
		}
		if ($type == "login")
		{
			return "	Le login doit contenir uniquement des lettres minuscules.
			
Il peut éventuellement être suivi d'un numéro (de 0 à 99), d'un point ou d'autres lettres minuscules
			
Exemples valides: michel, michel2, michel.12, michel.dupont, michel.dupont2, etc.";
		}
		if ($type == "id")
		{
			return "Un ID ne peut contenir que des chiffres.";
		}
		if (	($type == "nom") || ($type == "prenom")	)
		{
			return "Un nom ou prénom ne peut contenir que des caractères (accentués ou non), espaces et \" - \".";
		}
		if ($type == "heuresSemaine")
		{
			return "Le nombre d'heures par semaine doit être sous le format:
\"hh.mm\" ou \"hh\" 
Exemple pour 39 heures par semaine: \"39.00\" ou \"39\"";
		}
		if ($type == "nbrConges")
		{
			return "Le nombre de jours de congés annuels doit être sous le format:
\"hh.mm\" ou \"hh\" 
Exemple pour 30 jours de congés par ans: \"30.00\" ou \"30\"";
		}
		if ($type == "nbrRTT")
		{
			return "Le nombre de journées de RTT annuel doit être sous le format:
\"hh.mm\" ou \"hh\" 
Exemple pour 30 jours de RTT par ans: \"30.00\" ou \"30\"";
		}
		if ($type == "couleur")
		{
			return "Une couleur ne peut contenir qu'entre 3 et 6 chiffres ou lettres";
		}
		if ($type == "caseACocher")
		{
			return "Une case à cocher ne peut avoir que les valeurs 1 ou 0";
		}
		if ($type == "nbrJoursConges")
		{
			return "Le nombre de jours de congés doit contenir 2 chiffres uniquement.";
		}
		if ($type == "nbrJoursRTT")
		{
			return "Le nombre de jours de RTT doit contenir 1 à 2 chiffres uniquement.";
		}
		if ($type == "axe2_exclus_totaux")
		{
			return "Les codes comptables Axe2 à exclure des totaux doivent être séparés par des virgules (ex: 9901,9902,9907...)";
		}
		
		else
		{
			return "Le format renseigné semble ne pas être correct.";
		}
	}	
}


?>
