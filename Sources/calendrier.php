<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

<?php session_start(); ?>

<HEAD>

<?php // Permet de rediriger vers l'acceuil si utilisateur non enregistré.
	$prenom = $_SESSION['prenom'];
	$loginSession = $_SESSION['login'];
	$idSession		= $_SESSION['idUtilisateurs'];
	if (!$prenom)
	{
		header('Location: index.php'); 
	} 
?>

<TITLE>Linott: Planning</TITLE>
	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- Mon thème -->
	<link rel="stylesheet" href="style.css" />
	<link rel="stylesheet" href="calendrier.css" />

	<!-- Integration de jquery calendar http://jqueryui.com/datepicker/ -->
	<link rel="stylesheet" href="CSS/jquery-ui.css" />
	<script src="jquery-1.8.3.js"></script>
	<script src="jquery-ui.js"></script>

	<link rel="stylesheet" href="CSS/Delta/css/normalise.css"> 
	<link rel="stylesheet" href="CSS/Delta/theme/jquery-ui.css">
	<script src="CSS/Delta/js/modernizr-2.0.6.min.js"></script>
	<link rel="icon" type="image/png" href="favicon.png" />
	
<SCRIPT>
$(function()	// Fonction calendar Jquery. Permet d'afficher le petit calendrier, de récupèrer la date en cours, de remplir le champ de date, etc.
{
	$( "#datepicker" ).datepicker (
						{
						    changeMonth: true,
							changeYear: true,
							onSelect:	function() 
							{  
							    var vaDate = document.getElementById('AltFieldDateMysql').value;
							    //viderCalendar();
								importerTableEventUtilisateur(dateFormatMysql);
								importerDonneesCalendrier(vaDate);					  
							    visuel_mois();

							}, 

						}			
					);

	$.datepicker.regional['fr'] = 
	{
		closeText: 'Fermer',
		prevText: '<Préc',
		nextText: 'Suiv>',
		currentText: 'Courant',
		monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
		monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'],
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
		weekHeader: 'Sm',
		dateFormat: 'DD dd MM yy',
		altField: '#AltFieldDateMysql',
		altFormat: 'yy-mm-dd', // defaut pour mysql
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};

	$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );

	// Affiche automatiquement la date d'ajourd'hui dans le champ texte au chargement de la page.
	//$(datepicker).datepicker('setDate', new Date('-1'));
	$(datepicker).datepicker('setDate', new Date());

}	);

function demarrage()
{
	change_couleur_bouton_menu_general();
	var dateFormatMysql= $("#AltFieldDateMysql").val();

	importerTableEventUtilisateur(dateFormatMysql);
	importerDonneesCalendrier(dateFormatMysql);
	visuel_mois();	
	checkbox_astreinte();
	colorier_jours_vacances();
	aujourdhui();

	document.getElementById('option_evenement').style.display = "none";
}

function moisPrecedent() // Fonction lancée lors du clic sur "précédent" du calendrier.
{
	viderCalendar();

	// Inspiré
	var $picker = $("#datepicker");
	var date=new Date($picker.datepicker('getDate'));
	//alert(date);
	//date.setDate(date.getDate()-1);
	
	// Permet de passer au mois précédent (0 = dernier jour du mois d'avant)
	date.setDate(0);
	// Ca devrait marcher aussi
	//date.setMonth(date.getMonth(),0);
	$picker.datepicker('setDate', date);

	

	var dateFormatMysql= $("#AltFieldDateMysql").val();
	
	//alert(dateFormatMysql);
	importerTableEventUtilisateur(dateFormatMysql);	
	importerDonneesCalendrier(dateFormatMysql);

	visuel_mois();
	colorier_jours_vacances();
	aujourdhui();
	qui_est_la();

	return false;
}

function moisProchain() // Fonction lancée lors du clic sur "suivant" du calendrier.
{
	viderCalendar();
	// Next Day Link
	var $picker = $("#datepicker");
	var date=new Date($picker.datepicker('getDate'));
	// On fixe la date au dernier jour du mois précédent le mois en cours +2. Un simple "+1" pose prob si on est au 31/01 par exemple.
	date.setMonth(date.getMonth()+2,0);
	$picker.datepicker('setDate', date);


	var dateFormatMysql= $("#AltFieldDateMysql").val();
	//alert (dateFormatMysql);
	
	importerTableEventUtilisateur(dateFormatMysql);
	importerDonneesCalendrier(dateFormatMysql);

	visuel_mois();
	colorier_jours_vacances();
	aujourdhui();
	qui_est_la();

	// Je suis entrain de tester comment deselectionner les jours en cas de changement de mois;
	//repereJourClique("changeMois");

	//alert(dateFormatMysql);

	return false;
}

function rafraichir_page()
{
	location.reload(true);
}

function getXMLHttpRequest() // Fonction AJAX utilisée par toutes les fonctions allant chercher des données sur une autre page PHP (validation semaine, importer fiche, importer date, importer heures, etc.)
{
    var xhr = null;
     
    if (window.XMLHttpRequest || window.ActiveXObject) {
        if (window.ActiveXObject) {
            try {
                xhr = new ActiveXObject("Msxml2.XMLHTTP");
            } catch(e) {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
        } else {
            xhr = new XMLHttpRequest();
        }
    } else {
        alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
        return null;
    }
     
    return xhr;
}

function importerDonneesCalendrier(dateToImport)
{


	//	alert("debut test");
			//idUtilisateur=1;
			var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhr.open("GET", "importerDonneesCalendrier.php?dateToImport="+dateToImport, false);
			xhr.onreadystatechange = function() 
			{ 
				if(xhr.readyState == 4)
				{ 
					//alert(xhr.responseText);
					decouperChaineDonneesCalendrier(xhr);
				}
				//alert(xhr.readyState); 
			} 

			xhr.send(null);	
	//data = "date="+escape(l1.options[index].value);
		//alert("toto");
}

function decouperChaineDonneesCalendrier(xhr) // Met en forme les données récupèrées de importer_fiche.php pour la fonction javascript importer_fiche_ajax
{
	// On transforme l'objet recu en chaine de caractères..
	xhrText=xhr.responseText;

	// On decoupe chaque champs
	colonne=xhrText.split(";");
			//// Nous allons gérer les variables "colonne = xxx" en les transformant en "xxx".
			// On cree le tableau donnees dans lequel on "pushera" ensuite nos donnees.
			donnees		=	new Array ();
			// Ensuite, chaque fois que l'on trouve une ligne au tableau colonne, on la coupe en deux et on prend la 2nd partie (donc la valeur de la variable).
			for (j in colonne)
			{
				// "Reponse" est un tableau de 2 colonnes contenant le nom de la variable et sa valeur.
				reponse 	= 	colonne[j].split("=");

				// On "push" ensuite la 2eme colonne de notre réponse (donc la valeur de la variable) dans notre tableau "donnees".
				donnees.push(reponse[1]);
			}

			date		=	donnees[0];
			jourMois	=	donnees[1];
			mois		=	donnees[2];
			annee		=	donnees[3];
			jourSemaine	=	donnees[4];
			jourDebutMois	=	donnees[5];
			jourFinMois	=	donnees[6];
			nbrJoursMois	=	donnees[7];

			renseignerCalendrier(date, jourMois, mois, annee, jourSemaine, jourDebutMois, jourFinMois, nbrJoursMois);
}

function renseignerCalendrier(date, jourMois, mois, annee, jourSemaine, jourDebutMois, jourFinMois, nbrJoursMois)
{
	//alert("date: "+date+", jourMois: "+jourMois+", mois: "+mois+", annee: "+annee+", jourSemaine: "+jourSemaine+", jourDebutMois: "+jourDebutMois+", jourFinMois: "+jourFinMois+", nbrJoursMois: "+nbrJoursMois);
	
	jourDebutMois= parseInt(jourDebutMois);
	nbrJoursMois=parseInt(nbrJoursMois);
	
	// Définition des noms des jours de la semaine.
	if (jourSemaine==1) nomJourSemaine="Lundi";
	if (jourSemaine==2) nomJourSemaine="Mardi";
	if (jourSemaine==3) nomJourSemaine="Mercredi";
	if (jourSemaine==4) nomJourSemaine="Jeudi";
	if (jourSemaine==5) nomJourSemaine="Vendredi";
	if (jourSemaine==6) nomJourSemaine="Samedi";	
	if (jourSemaine==7) nomJourSemaine="Dimanche";		

	// Définition du nom du mois.
	if (mois	==	1) nomMois="Janvier";
	if (mois	==	2) nomMois="Février";
	if (mois	==	3) nomMois="Mars";
	if (mois	==	4) nomMois="Avril";
	if (mois	==	5) nomMois="Mai";
	if (mois	==	6) nomMois="Juin";
	if (mois	==	7) nomMois="Juillet";
	if (mois	==	8) nomMois="Août";
	if (mois	==	9) nomMois="Septembre";
	if (mois	==	10) nomMois="Octobre";
	if (mois	==	11) nomMois="Novembre";
	if (mois	==	12) nomMois="Décembre";
	
	// Je défini la limite pour savoir jusqu'où va aller mon calendar
	var limite	=	Number(jourDebutMois+nbrJoursMois);
	
	// Je rempli les dates de mon calendar
	var j = 1;
	for (i = jourDebutMois;	i < limite	; i++)
	{
		document.getElementById('text_'+i).value = j;
		j++;
	}
	
	
	// Je vais rendre les jours non remplis moins visibles.
	// On passe toutes les cases en transparentes avant d'éclairer celles qui nous interessent.
	for (i = 1;	i < jourDebutMois	; i++)
	{
		document.getElementById('table_calendrier_'+i).style.visibility = "hidden";
	}
	for (i = limite;	i < 43	; i++)
	{
		document.getElementById('table_calendrier_'+i).style.visibility = "hidden";
	}
	
	casesSemaine 	= [1,2,3,4,5,8,9,10,11,12,15,16,17,18,19,22,23,24,25,26,29,30,31,32,33,36,37,38,39,40];
	casesWE			= [6,7,13,14,20,21,27,28,34,35,41,42];
	for (i = jourDebutMois;	i < limite	; i++)
	{
		document.getElementById('table_calendrier_'+i).style.visibility = "visible";
		document.getElementById('table_calendrier_'+i).style.opacity = 1;

		if (casesSemaine.indexOf(i) > -1)
		{
			document.getElementById('table_calendrier_'+i).className = "semaine";
		}
		if (casesWE.indexOf(i) > -1)
		{
			document.getElementById('table_calendrier_'+i).className = "we";
		}
	}

	renseignerConges();
	afficher_que_utilisateur();
	aujourdhui();
}

function repereJourClique(numCase)
{
	//if (numCase != "changeMois")
	//{
		// A rajouter: Si le temps de clic est supérieur à XXX ms , alors on ne selectionne pas.
		jourClique = numCase - jourDebutMois + 1;
		dateHumain = jourClique+"/"+mois+"/"+annee;
		dateMysql = annee+"-"+mois+"-"+jourClique;
		
		objetCase_numCase = new objetCase(numCase, dateHumain, dateMysql);
	//}
		//alert(objetCase_numCase.dateHumain);
			
		// On encadre juste celui selectionné. Pour ca on désencadre tous les autres avant.
		for (i = 1;	i < 43	; i++)
		{
			document.getElementById('table_calendrier_'+i).style.border="2px solid #E6E6E6";
		}
		
		// Si l'array selectionCase n'existe pas, on le créé. Puis on push l'objet dedans.
		if(typeof(selectionCase)=='undefined')
		{
			//alert("Je créé un petit nouveau");
			selectionCase = new Array ();
			selectionCase.push(objetCase_numCase);
		}
		else
		{
			// longueurSelection retourne la longueur du tableau contenant les objets de dates selectionnées
			var longueurSelection = selectionCase.length;
			//alert("Mon tableau a une longueur de: "+longueurSelection);
		

			// Si la case selectionnée est déjà dans le tableau de selection on la supprime. Sinon on l'ajoute.
			var suppression = 0;
			for ( i = 0 ; i < longueurSelection ; i++)
			{
				//alert("je rentre dans le for et je suis au passage i = "+i);
				//alert("je vais définir pointeur, mon i vaut "+i+", et mon dateMysql vaut "+dateMysql);
				pointeur = selectionCase[i].dateMysql;
				//alert("je m apprete à rentrer dans le if de la boucle, mon i vaut "+i+", mon pointeur vaut "+pointeur+" et mon dateMysql vaut "+dateMysql);
				if (pointeur == dateMysql)
				{
					//alert("je rentre dans le if de la boucle, mon i vaut "+i);
					//document.getElementById('table_calendrier_'+numCase).style.border="2px solid #E6E6E6";
					selectionCase.splice(i,1);
					suppression = 1;
					//alert("je viens de supprimer: "+selectionCase[i].numCase);
					//alert("je suis à la fin du if de la boucle, mon i vaut "+i);
					longueurSelection=longueurSelection - 1;
					//alert("i = "+i+" , loungueurSelection passe à "+longueurSelection);
				}
			}
			//alert("Je sors du for, mon i vaut "+i);
			if ( ( suppression == 0 ) && (numCase != "changeMois")  )
			{
			//	alert("je push mon nouvel objet.");
				selectionCase.push(objetCase_numCase);
			}		
		}


	// On colorie les cases selectionnées.
	var textDates = "";
	var longueurSelection = selectionCase.length;
	for ( i = 0; i < longueurSelection; i++)
	{
		// Je récupère le mois de la selection qu'on est entrain de parcourir
		moisSelection = selectionCase[i].dateMysql.split("-")[1];
		// On rajoute un "0" si le mois est < 10 et que le mois ne contient pas de 0 (car sans que je sache pourquoi , certains mois sont en "0x" et d'autres en "x")
		if ( (moisSelection < 10) && (moisSelection.indexOf("0") == -1) )
		{
			moisSelection = "0"+moisSelection;
		}

		// Je prends la date en cours d'affichage
		var dateFormatMysql= $("#AltFieldDateMysql").val();
		// Et j'en extrait le mois.
		moisDateMysql = dateFormatMysql.split("-")[1];
		//alert(moisDateMysql+", "+moisSelection);
		// Si le mois en cours d'affichage est égal au moins en cours de parcours, alors on affiche la selection en gris

		textDates = textDates+selectionCase[i].dateHumain+", ";
		//alert("objet.numCase: "+selectionCase[i].numCase+" , objet.dateMysql: "+selectionCase[i].dateMysql+" , objet.dateHumain: "+selectionCase[i].dateHumain+", textDates: "+textDates);
		caseCochee = selectionCase[i].numCase;
		
		if ( (moisDateMysql == moisSelection) )
		{
					document.getElementById('table_calendrier_'+caseCochee).style.border="2px solid #777";
		}

	}

	// Et le nombre de jours que cela représente.
	document.getElementById('nbrJours').value = longueurSelection;
	
}

function objetCase(numCase, dateHumain, dateMysql)
{
	this.numCase=numCase;
	this.dateHumain=dateHumain;
	this.dateMysql=dateMysql;
}


function checkEvenements()
{
		typeEvenement = "";

		idTypeAbsence=document.getElementById('type_absence').value;
		nomEvenement=document.getElementById('choix_nomEvenement').value;
		idUtilisateurAstreinte=document.getElementById('astreinte').value;

		// Si absence est coché
		if (document.getElementById('checkAbsence').checked == true)
		{
			if ( idTypeAbsence == "absence_non_choisie" )
			{
				alert("Veuillez choisir un type d'absence.\n\nVous avez coché \"Absence\" sans choisir le type d'absence dans le menu déroulant.");
				throw { name: 'Erreur', message: 'Erreur de manipulation de l utilisateur' };
			}
			if ( idTypeAbsence != "absence_non_choisie" )
			{
				typeEvenement = "absence";
			}

		}
		// Si un choix est fait dans le menu déroulant des absences
		if ( idTypeAbsence != "absence_non_choisie" )
		{
			if (document.getElementById('checkAbsence').checked == false)
			{
				alert("Vous avez selectionné un type d'absence sans cocher l'option \"Absence\".\n\nVeuillez cocher la case \"Absence\" pour valider votre choix.");
				throw { name: 'Erreur', message: 'Erreur de manipulation de l utilisateur' };
			}
		}
		// Si evenement est coché.
		if (document.getElementById('checkEvenement').checked == true)
		{
			if (nomEvenement == "")
			{
				alert("Veuillez indiquer un type d'évènement.\n\nVous avez coché \"Évènement\" sans indiquer le type d'évènement.");
				throw { name: 'Erreur', message: 'Erreur de manipulation de l utilisateur' };
			}
			if (nomEvenement != "")
			{
				typeEvenement = "event";
			}
		}
		// Si du text est rentré dans la description evenement
		if ( nomEvenement != "" )
		{
			if (document.getElementById('checkEvenement').checked == false)
			{
				alert("Vous avez renseigné un type d'évènement sans cocher l'option \"Évènement\".\n\nVeuillez cocher la base \"Évènement\" pour valider votre choix.");
				throw { name: 'Erreur', message: 'Erreur de manipulation de l utilisateur' };
			}
			if ( ( typeof(listeUtilisateursConcernes)=='undefined' ) || ( typeof(listeGroupesConcernes)=='undefined' ) )
			{
				alert("Vous avez créé un événement sans choisir qui était concerné.");
				throw { name: 'Erreur', message: 'Erreur de manipulation de l utilisateur' };
			}
		}

		// Si astreinte est cochée
		if (document.getElementById('checkAstreinte').checked == true)
		{
			if ( idUtilisateurAstreinte == "astreinte_non_choisie" )
			{
				alert("Veuillez choisir une personne d'astreinte.\n\nVous avez coché \"Astreinte\" sans choisir de nom dans le menu déroulant.");
				throw { name: 'Erreur', message: 'Erreur de manipulation de l utilisateur' };				
			}
			if ( idUtilisateurAstreinte != "astreinte_non_choisie" )
			{
				typeEvenement = "astreinte";
			}
		}

		indisponible = 0;
		// Si "Cet événement rend indisponible" est coché
		if (document.getElementById('indisponible').checked == true)
		{
			indisponible = 1;
		}



}


function validerCalendrier()
{
	
	// On va récupèrer le type de bouton radio selectionné (congé, RTT, autre, evenements, etc.)
	//checkBoutonRadio();
	checkEvenements();

	if(typeof(selectionCase)=='undefined')
	{
		alert("Vous devez selectionner au moins un jour.");
		throw "stop execution";
	}

	nombreJours = selectionCase.length;

	if (nombreJours == 0)
	{
		alert("Vous devez selectionner au moins un jour.");
		throw "stop execution";
	}
	//alert(nombreJours);
	
	if (typeEvenement == "")
	{
		alert("Vous devez selectionner un type d'événement");
		throw "stop execution";
	}

	for ( i = 0 ; i < nombreJours ; i++ )
	{
		alerter = "0";

		//alert("je suis dans la boucle");
		date			=	selectionCase[i].dateMysql;
		type			=	typeEvenement;
		valide			=	"N";
		bloquant 		=	"0";


		periodeEvenement = document.getElementById('periode').value;



		if ( type == "event")
		{	
			description 	=	nomEvenement;
			if ( document.getElementById('bloquant').checked == true)
			{
				bloquant =	"1";
			}
		}
		if ( type != "event")
		{
			listeUtilisateursConcernes	=	"";
			listeGroupesConcernes 		= 	"";
		}

		if ( type == "absence")
		{
			description		=	document.getElementById('type_absence').value;
		}
		if ( type == "astreinte")
		{
			description = document.getElementById('astreinte').value;
			periodeEvenement = "JO";
		}

		//alert("je suis devant le xhr");
		var xhrValidCalendrier = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		//alert("je vais lancer l'open, date="+date+", type="+type+", valide="+valide);
//		xhrValidCalendrier.open("GET", "modifier_calendrier_conge.php?date="+date+"&periodeEvenement="+periodeEvenement+"&typeEvenement="+type+"&valide="+valide+"&description="+description+"&bloquant="+bloquant, true);
		xhrValidCalendrier.open("GET", "modifier_calendrier_conge.php?date="+date+"&periodeEvenement="+periodeEvenement+"&typeEvenement="+type+"&valide="+valide+"&description="+description+"&bloquant="+bloquant+"&listeGroupesConcernes="+listeGroupesConcernes+"&listeUtilisateursConcernes="+listeUtilisateursConcernes+"&indisponible="+indisponible, false);
		//alert("modifier_calendrier_conge.php?date="+date+"&periodeEvenement="+periodeEvenement+"&typeEvenement="+type+"&valide="+valide+"&description="+description+"&bloquant="+bloquant+"&listeGroupesConcernes="+listeGroupesConcernes+"&listeUtilisateursConcernes="+listeUtilisateursConcernes);
		xhrValidCalendrier.onreadystatechange = function() 
		{ 
			if(xhrValidCalendrier.readyState == 4)
			{ 
				retour = xhrValidCalendrier.responseText;
				if ( retour.indexOf("Vous ne pouvez donc pas faire de demande d'absence sur cette période.") != -1 )
				{	
					alert(xhrValidCalendrier.responseText);
				}

				for (j = 1;	j < 43	; j++)
				{
					document.getElementById('table_calendrier_'+j).style.border="2px solid #E6E6E6";
				}
				//alert(xhrValidCalendrier.responseText);
			}
			//alert(xhr.readyState); 
		}
		xhrValidCalendrier.send(null);

		// Je comprends pas, si j'enlève cet alerte, j'ai le rafraichir page et rien d'autre. Pour ne pas avoir de pb je peux aussi commenter le rafraichir page.
		//alert("titi i="+i+",nombreJours="+nombreJours+", date="+date);
	}

	actualiserCalendrier();
//	rafraichir_page();

}

function actualiserCalendrier()
{
	var dateFormatMysql= $("#AltFieldDateMysql").val();	
	importerTableEventUtilisateur(dateFormatMysql);
	importerDonneesCalendrier(dateFormatMysql);
	
	// Remise à zero du tableau de selection
	selectionCase = new Array ();
	document.getElementById('nbrJours').value = selectionCase.length;


	document.getElementById('checkAbsence').checked = false;
	document.getElementById('checkAstreinte').checked = false;
	document.getElementById('checkEvenement').checked = false;
	document.getElementById('indisponible').checked = false;
	document.getElementById('bloquant').checked = false;

	document.getElementById('choix_nomEvenement').value = "";


	afficher_options_evenement();


	document.getElementById('type_absence').value = "absence_non_choisie";
	document.getElementById('astreinte').value = 'astreinte_non_choisie';
	document.getElementById('periode').value = 'JO';

	document.getElementById('utilisateurs_concernes_tous').checked = false;
	document.getElementById('utilisateurs_concernes_certains').checked = false;


	delete window.listeGroupesConcernes;
	delete window.listeUtilisateursConcernes;


}

function importerTableEventUtilisateur(date)
{

	mois=date.split("-")[1];
	annee=date.split("-")[0];
	idUtilisateur=document.getElementById('idSession').value;

	// TEST : Si l'utilisateur est concerné par l'événement, c'est son nom qui s'affiche et pas celui du créateur.
	loginSession = document.getElementById('loginSession').value;
	loginSessionMaj = loginSession.charAt(0).toUpperCase()+loginSession.substring(1).toLowerCase();

		var xhrListeEvtUtilisateur = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhrListeEvtUtilisateur.open("GET", "lister_uniquement_evenements_utilisateur.php?idUtilisateur="+idUtilisateur+"&annee="+annee+"&mois="+mois, false);
		//alert("lister_uniquement_evenements_utilisateur.php?idUtilisateur="+idUtilisateur+"&annee="+annee+"&mois="+mois);
		xhrListeEvtUtilisateur.onreadystatechange = function() 
		{ 
			if(xhrListeEvtUtilisateur.readyState == 4)
			{ 
				listeEvenementUtilisateurs = xhrListeEvtUtilisateur.responseText;
				//alert(listeEvenementUtilisateurs);
				if (listeEvenementUtilisateurs == "")
				{
					//alert("vide");
					listeEvenementUtilisateurs = "NULL";
					//alert("Nouvel XHR= "+listeEvenementUtilisateurs);
				}
			}
			//alert(xhr.readyState); 
		} 
		xhrListeEvtUtilisateur.send(null);

		tableEventUtilisateur = listeEvenementUtilisateurs.split(",");
}

function renseignerConges()
{
	var xhrRenseignerCalendrier = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	
	xhrRenseignerCalendrier.open("GET", "renseigner_conge_calendrier.php?mois="+mois+"&annee="+annee, false);
	xhrRenseignerCalendrier.onreadystatechange = function() 
	{ 
		if(xhrRenseignerCalendrier.readyState == 4)
		{ 
			//alert(xhrRenseignerCalendrier.responseText);
			decouperChaineRenseignerConges(xhrRenseignerCalendrier);
		}
		//alert(xhr.readyState); 
	} 
	xhrRenseignerCalendrier.send(null);
}

function decouperChaineRenseignerConges(xhr) // Met en forme les données récupèrées de importer_fiche.php pour la fonction javascript importer_fiche_ajax
{
	// On transforme l'objet recu en chaine de caractères..
	xhrText=xhr.responseText;
	//alert(xhrText);
	// On decoupe chaque champs. On a donc une ligne pour chaque congé, elle meme remplie de xx=xx-xx=xx
	ligneConge=xhrText.split("//");
	//alert(ligneConge);

		// Vu que le dernier champ renvoyé par la page renseigner_conge_calendrier fini par un "/", il considère qu'il y a kkchose derriere (vide). Le "-1" 
		// dans le length ci dessous permet de zapper cette ligne.
		for ( i = 0 ; i < ligneConge.length-1 ; i++ )
		{
		
			tableauLignesConges	=	new Array ();
			//alert(i);
			//alert(ligneConge);
			
			reponse	=	ligneConge[i].split(";");
			//alert(reponse);
			tableauLignesConges.push(reponse[0]);
			tableauLignesConges.push(reponse[1]);
			tableauLignesConges.push(reponse[2]);
			tableauLignesConges.push(reponse[3]);
			tableauLignesConges.push(reponse[4]);
			tableauLignesConges.push(reponse[5]);
			tableauLignesConges.push(reponse[6]);
			tableauLignesConges.push(reponse[7]);
			tableauLignesConges.push(reponse[8]);
			tableauLignesConges.push(reponse[9]);


			//alert(tableauLignesConges);			
		

			idConge				=	tableauLignesConges[0];
			idUtilisateur		=	tableauLignesConges[1];
			loginUtilisateur 	=	tableauLignesConges[2];
			couleur				=	tableauLignesConges[3];
			date				=	tableauLignesConges[4];
			periode 			=	tableauLignesConges[5];
			type				=	tableauLignesConges[6];
			valide				=	tableauLignesConges[7];
			description			=	tableauLignesConges[8];
			bloquant			=	tableauLignesConges[9];


 			//alert(idConge+idUtilisateur+loginUtilisateur+couleur+date+type+valide);
 			renseignerCongeCalendrier(idConge, idUtilisateur, loginUtilisateur, couleur, date, periode, type, valide, description, bloquant);
		
		}
}

function  renseignerCongeCalendrier(idConge, idUtilisateur, loginUtilisateur, couleur, date, periode, type, valide, description, bloquant)
{

	dateSplit 	=	date.split("-");
	jour		=	dateSplit[2];
	mois		=	dateSplit[1];
	annee		=	dateSplit[0];
	//alert(jour+"/"+mois+"/"+annee);
	
	
	numCase = parseInt(jourDebutMois)+parseInt(jour)-1;
	
	//alert("numCase="+numCase);
	
	// On met une maj au début du nom.
	loginUtilisateurMaj = loginUtilisateur.charAt(0).toUpperCase()+loginUtilisateur.substring(1).toLowerCase();
	texte=loginUtilisateurMaj+" _ "+description;


	if ( (type == "event") && (loginUtilisateur != loginSession) )
	{
		if (tableEventUtilisateur.indexOf(idConge) != -1)
		{
			texte = loginSessionMaj+" _ "+description;
		}
	}

	couleurText = "";
	tdAlign = "";
	finCouleurText= "</font>";

	if (type == "event")
	{
		couleur="#666";
		couleurText = "<font color=#EEE>";
		finCouleurText= "</font>";
	}

	if ( type == "astreinte" )
	{
		couleur="transparent";
		couleurText = "<font color=#444>";
		finCouleurText= "</font>";
		texte=description;
		tdAlign = "align=right";
	}



	// On aligne selon la période matin / midi / soir.
	if ( periode == "JO" )
	{
		alignement="center"
		largeur="98"
	}
	if ( periode == "MA" )
	{
		alignement="left"
		largeur="58"
	}
	if ( periode == "AM" )
	{
		alignement="right"
		largeur="55"
	}

	bandeau = 	"<TABLE width=98% align=center border=0 id=congeId"+idConge+">"
					+"<tr>"
						+"<td width=100%>"
							+"<TABLE width="+largeur+"% align="+alignement+" border=0 id=text_evenement_"+idConge+" bgcolor="+couleur+" onClick=click_nom_event("+idConge+");>"
								+"<tr>"
									+"<td "+tdAlign+">"+couleurText+texte+finCouleurText
									+"</td>"
								+"</tr>"
							+"</TABLE>"
						+"</td>"
						+"<td id=td_valide_evenement_"+idConge+">"
							+"<TABLE id=valide_evenement_"+idConge+" onClick=click_valide_event("+idConge+") align=right>"
								+"<tr>"
									+"<td>"
									+"</td>"
								+"</tr>"
							+"</TABLE>"
						+"</td>"
					+"</tr>"
				+"</TABLE>";
	


	//alert(bandeau);

	// Si l'id existe déjà sur la page, c'est que c'est une modification d'évenement. On ne traite donc pas le innerHTML pareil.
	if ( document.getElementById('congeId'+idConge) )
	{
		document.getElementById('congeId'+idConge).innerHTML = bandeau;
	}
	// Si c'est une astreinte, on la traite differemment.
	else if ( type == "astreinte")
	{
		document.getElementById('astreinte_'+numCase).innerHTML += bandeau;
		document.getElementById('congeId'+idConge).style.opacity = 0.8;
		document.getElementById('text_evenement_'+idConge).style.align = "right";		
	}
	else
	{
		document.getElementById('table_calendrier_'+numCase).innerHTML 	+= bandeau;
	}
		if (( type == "event" ) || ( type == "astreinte" ))
		{
			document.getElementById('td_valide_evenement_'+idConge).style.display = "none";		
		}
		
		if ( valide == "N" )
		{
	  	  	document.getElementById('valide_evenement_'+idConge).innerHTML = '<tr><td><IMG SRC="Images/attention_48px.png" ALT="?" width=14px></td></tr>';
	  	  
		}
		if ( valide == "V" )
		{
	    	document.getElementById('valide_evenement_'+idConge).innerHTML = '<tr><td><IMG SRC="Images/valider_48px.png" ALT="V" width=14px></td></tr>';

		}
		if ( valide == "R" )
		{
	  	  	document.getElementById('valide_evenement_'+idConge).innerHTML = '<tr><td><IMG SRC="Images/refuse_48px.png" ALT="R" width=14px></td></tr>';
		}

		if ( valide == "A" )
		{
			document.getElementById('valide_evenement_'+idConge).innerHTML = '<tr><td><IMG SRC="Images/attente_48px.png" ALT="R" width=14px></td></tr>';
		}

	//alert("pause");

	// On enleve les "0" devant les chiffres des jours < 10.	
	jourSansZero = jour
	if ( jourSansZero < 10 )
	{
			jourSansZero = jourSansZero.replace("0", "");
	}
	// Sans ca le numéro du jour disparait sur le calendrier.
	document.getElementById('text_'+numCase).value = jourSansZero;	
}

function click_nom_event(idEvent)
{
	//alert("Click sur le nom de l'évenement "+idEvent);

	window.open ("proprietes_evenement_calendrier.php?idEvent="+idEvent, "proprietes_evenement_calendrier_"+idEvent, config='height=800, width=900, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no');
}

function click_valide_event(idEvent)
{
	if ( document.getElementById('ligne_test').value == 1 )
 	{
		get_infos_evenement(idEvent, "validation");
	}
}

function get_infos_evenement(idEvent, typeModif)
{
	var xhrGetInfosEvenement = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	
	xhrGetInfosEvenement.open("GET", "importer_infos_evenement.php?idEvenement="+idEvent, false);
	xhrGetInfosEvenement.onreadystatechange = function() 
	{ 
		if(xhrGetInfosEvenement.readyState == 4)
		{ 
			//alert(xhrGetInfosEvenement.responseText);
			decouperChaineGetInfosEvenement(xhrGetInfosEvenement, typeModif);
		}
		//alert(xhr.readyState); 
	} 
	xhrGetInfosEvenement.send(null);
}
function decouperChaineGetInfosEvenement(xhr, typeModif)
{

		// On transforme l'objet recu en chaine de caractères..
	xhrText=xhr.responseText;
	//alert(xhrText);


	reponse	=	xhrText.split(";");

	idEvenementBDD 				= reponse[0];
	idUtilisateurEvenement 		= reponse[1];
	loginUtilisateurEvenement 	= reponse[2];
	dateEvenement 				= reponse[3];
	periodeEvenement			= reponse[4];
	typeEvenement 				= reponse[5];
	validation 					= reponse[6];
	description					= reponse[7];
	bloquant 					= reponse[8];


 	//alert(idEvenementBDD+idUtilisateurEvenement+loginUtilisateurEvenement+dateEvenement+typeEvenement+validation+description+bloquant);
 



 	if (typeModif == "validation")
 	{
	 	if ( validation == "N" )
	 	{
	 		validation_conge(idEvenementBDD, idUtilisateurEvenement, loginUtilisateurEvenement, dateEvenement, periodeEvenement, typeEvenement, "V", description, bloquant);
	 		document.getElementById('valide_evenement_'+idEvenementBDD).innerHTML = '<IMG SRC="Images/valider_48px.png" ALT="V" width=14px>';
	 	}
	 	if ( validation == "V" )
	 	{
	 		validation_conge(idEvenementBDD, idUtilisateurEvenement, loginUtilisateurEvenement, dateEvenement, periodeEvenement, typeEvenement, "R", description, bloquant);
	 		document.getElementById('valide_evenement_'+idEvenementBDD).innerHTML = '<IMG SRC="Images/refuse_48px.png" ALT="R" width=14px>';
	 	}
	 	if ( validation == "R" )
	 	{
	 		validation_conge(idEvenementBDD, idUtilisateurEvenement, loginUtilisateurEvenement, dateEvenement, periodeEvenement, typeEvenement, "A", description, bloquant);
	 		document.getElementById('valide_evenement_'+idEvenementBDD).innerHTML = '<IMG SRC="Images/attente_48px.png" ALT="V" width=14px>';
	 	}
	  	if ( validation == "A" )
	 	{
	 		validation_conge(idEvenementBDD, idUtilisateurEvenement, loginUtilisateurEvenement, dateEvenement, periodeEvenement, typeEvenement, "V", description, bloquant);
	 		document.getElementById('valide_evenement_'+idEvenementBDD).innerHTML = '<IMG SRC="Images/valider_48px.png" ALT="V" width=14px>';
	 	}	
 	}


}
function modifier_evenement(idEvenementBDD, idUtilisateurEvenement, loginUtilisateurEvenement, dateEvenement, periodeEvenement, typeEvenement, validation, description, bloquant)
{

			//alert(idUtilisateur+", "+nom+", "+prenom+", "+nbrHeures+", "+login);
			var xhrModifEvenement = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhrModifEvenement.open("GET", "modifier_infos_evenement.php?idEvenement="+idEvenementBDD+"&idUtilisateurEvenement="+idUtilisateurEvenement+"&loginUtilisateurEvenement="+loginUtilisateurEvenement+"&dateEvenement="+dateEvenement+"&periodeEvenement="+periodeEvenement+"&typeEvenement="+typeEvenement+"&validation="+validation+"&description="+description+"&bloquant="+bloquant, false);
			//alert("modifier_utilisateur.php?idAModifier="+idUtilisateur+"&nom="+nom+"&prenom="+prenom+"&nbrHeures="+nbrHeures+"&login="+login+"&admin="+admin+"&active="+active);
			xhrModifEvenement.onreadystatechange = function() 
			{ 
				if(xhrModifEvenement.readyState == 4)
				{ 
					//alert(xhrModifEvenement.responseText);
				}
				//alert(xhr.readyState); 
			} 

			xhrModifEvenement.send(null);
			//rafraichir_page("xhrModifEvenement");
}
function validation_conge(idEvenementBDD, idUtilisateurEvenement, loginUtilisateurEvenement, dateEvenement, periodeEvenement, typeEvenement, validation, description, bloquant)
{

			//alert(idUtilisateur+", "+nom+", "+prenom+", "+nbrHeures+", "+login);
			var xhrModifEvenement = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhrModifEvenement.open("GET", "validation_conge.php?idEvenement="+idEvenementBDD+"&idUtilisateurEvenement="+idUtilisateurEvenement+"&loginUtilisateurEvenement="+loginUtilisateurEvenement+"&dateEvenement="+dateEvenement+"&periodeEvenement="+periodeEvenement+"&typeEvenement="+typeEvenement+"&validation="+validation+"&description="+description+"&bloquant="+bloquant, false);
			//alert("modifier_utilisateur.php?idAModifier="+idUtilisateur+"&nom="+nom+"&prenom="+prenom+"&nbrHeures="+nbrHeures+"&login="+login+"&admin="+admin+"&active="+active);
			xhrModifEvenement.onreadystatechange = function() 
			{ 
				if(xhrModifEvenement.readyState == 4)
				{ 
					//alert(xhrModifEvenement.responseText);
				}
				//alert(xhr.readyState); 
			} 

			xhrModifEvenement.send(null);
			//rafraichir_page("xhrModifEvenement");


			if (	document.getElementById('renseigner_automatiquement_conge_valide').value == "checked" )
			{
				//alert("je suis dedans");
				// Si on valide le congé, on le renseigne en base pour qu'il soit automatiquement dans la fiche d'heures.
				if ( validation == "V")
				{
					gestion_conge_fiche_heures("renseigner", idEvenementBDD);
				}
				if ( validation == "R")
				{
					gestion_conge_fiche_heures("supprimer", idEvenementBDD);
				}
			}
}

function gestion_conge_fiche_heures(queFaire, idConge)
{
			var xhrRenseigneAutomatiqueConge = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			//xhrRenseigneAutomatiqueConge.open("GET", "renseigner_automatiquement_conge.php?idEvenement="+idEvenementBDD+"&queFaire="+queFaire, false);
			xhrRenseigneAutomatiqueConge.open("GET", "renseigner_automatiquement_conge.php?idEvenement="+idConge+"&queFaire="+queFaire, false);
			//alert("modifier_utilisateur.php?idAModifier="+idUtilisateur+"&nom="+nom+"&prenom="+prenom+"&nbrHeures="+nbrHeures+"&login="+login+"&admin="+admin+"&active="+active);
			xhrRenseigneAutomatiqueConge.onreadystatechange = function() 
			{ 
				if(xhrRenseigneAutomatiqueConge.readyState == 4)
				{ 
					if (xhrRenseigneAutomatiqueConge.responseText.indexOf("ATTENTION") != -1 )
					{
						alert(xhrRenseigneAutomatiqueConge.responseText);
					}
				}
				//alert(xhr.readyState); 
			} 

			xhrRenseigneAutomatiqueConge.send(null);
}

function viderCalendar()
{
	for (i = 1 ;	i < 43	; i++)
	{
		document.getElementById('text_'+i).value = "";
		document.getElementById('text_'+i).style.color = "";
	}

	// Me retourne une liste des tables présentes dans le document.
	listeTable = document.getElementsByTagName('table');
	tailleTableOrigine = listeTable.length;

	//alert("taille de la table: "+tailleTableOrigine);

	for (j = 0 ; j < tailleTableOrigine ; j++)
	{
//		alert("j="+j+" , id="+listeTable[j].id+" , parentNode="+listeTable[j].parentNode.id);
//		alert("j avant = "+j+", tailleTableOrigine avant = "+tailleTableOrigine);




			if (	(listeTable[j].id.indexOf("congeId") != -1) ) 
			{
//				alert("c'est un congé ID");
//				alert("enlever id="+listeTable[j].id+" dans table de valeur j="+j+" , parentNode="+listeTable[j].parentNode.id);
			
				//listeTable[j].parentNode.removeChild(listeTable[j]);									
				document.getElementById(listeTable[j].id).parentNode.removeChild(listeTable[j]);
				// Ce j-- m'a pris la tête pendant des semaines et est INDISPENSABLE!!!
				// En effacant un tableau, la taille de notre table change.
				// Si je ne fais pas ce j-- alors que je viens de supprimer une ligne, je vais en sauter une sur deux.
				// Exemple: Si je supprime la table 5, la ligne 5 disparait. La table 6 passe donc en ligne 5.
				// Donc si je passe direct à la lecture de la ligne 6, j'aurai la table 7 car la pile aura fait descendre ma table 6.
				j--;
				// Le fait de descendre aussi "tailleTableOrigine" me permet de garantir l'execution de la boucle jusqu'au bout.
				// Si tailleTableOrigine était à 13 et qu'on supprime 3 valeurs, arrivé à 10, javascript va planter car il chercher quelque chose qui n'existe pas.
				//tailleTableOrigine--;
				// A cause de la table "valid" créé en plus pour chaque horaire, je dois faire un tableOrigine-- 3x:
				// - 1x pour la table que je supprime contenant le text de congé + la validation
				// - 1x pour la table contenant le texte
				// - 1x pour la table de validation
				tailleTableOrigine--;
				tailleTableOrigine--;
				tailleTableOrigine--;
			}
			if ( listeTable[j].id.indexOf("presence_case_") != -1)  
			{
				//alert("tptp");
				document.getElementById(listeTable[j].id).parentNode.removeChild(listeTable[j]);

				j--;

				tailleTableOrigine--;
			}
			if ( listeTable[j].id.indexOf("text_presence_") != -1)
			{
				//alert("tptp");
				document.getElementById(listeTable[j].id).parentNode.removeChild(listeTable[j]);

				j--;

				tailleTableOrigine--;
				tailleTableOrigine--;
			}

//			alert("j après = "+j+", tailleTableOrigine après = "+tailleTableOrigine);
	}
//	alert("toto");

//	alert("je suis sorti de la boucle");

}

function retourFiche()
{
	location.href="compta.php";
}

function afficher_astreintes()
{

	etatCheckAfficherAstreinte = document.getElementById('afficher_astreintes').checked;

	if (etatCheckAfficherAstreinte == true)
	{
		valeur = 1;
		document.getElementById('pref_afficher_astreintes').value = 1;
	}
	else if (etatCheckAfficherAstreinte == false)
	{
		valeur = 0;
		document.getElementById('pref_afficher_astreintes').value = 0;
	}





	//alert(idUtilisateur+", "+nom+", "+prenom+", "+nbrHeures+", "+login);
	var xhrModifierPref = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhrModifierPref.open("GET", "modifier_preferences_utilisateurs.php?type=afficher_astreintes&valeur="+valeur, false);
	xhrModifierPref.onreadystatechange = function() 
	{ 
		if(xhrModifierPref.readyState == 4)
		{ 
			//alert(xhrModifierPref.responseText);
		}
		//alert(xhr.readyState); 
	} 
	xhrModifierPref.send(null);

	// Me retourne une liste des tables présentes dans le document.
	listeTable = document.getElementsByTagName('td');
	tailleTableOrigine = listeTable.length;

	for (j = 0 ; j < tailleTableOrigine ; j++)
	{
		if (listeTable[j].id.indexOf("astreinte_") != -1)
		{	
			if (etatCheckAfficherAstreinte == false)
			{	
				document.getElementById(listeTable[j].id).style.visibility = "hidden";
			}
			if (etatCheckAfficherAstreinte == true)
			{	
				document.getElementById(listeTable[j].id).style.visibility = "visible";
			}			
		}
	}
}

function checkbox_astreinte()
{
	var xhrImporterPref = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhrImporterPref.open("GET", "importer_preferences_utilisateurs.php?type=afficher_astreintes", false);
	xhrImporterPref.onreadystatechange = function() 
	{ 
		if(xhrImporterPref.readyState == 4)
		{ 
			etatCheckAstreinteBase = xhrImporterPref.responseText;
			//alert(etatCheckAstreinteBase);
		}
		//alert(xhr.readyState); 
	} 
	xhrImporterPref.send(null);
	
	if (etatCheckAstreinteBase == "1")
	{
		document.getElementById('afficher_astreintes').checked = true;
	}
	else if (etatCheckAstreinteBase == "0")
	{
		document.getElementById('afficher_astreintes').checked = false;
	}

	// Me retourne une liste des tables présentes dans le document.
	listeTable = document.getElementsByTagName('td');
	tailleTableOrigine = listeTable.length;

	for (j = 0 ; j < tailleTableOrigine ; j++)
	{
		if (listeTable[j].id.indexOf("astreinte_") != -1)
		{	
			if (etatCheckAstreinteBase == "0")
			{	
				document.getElementById(listeTable[j].id).style.visibility = "hidden";
			}
			if (etatCheckAstreinteBase == "1")
			{	
				document.getElementById(listeTable[j].id).style.visibility = "visible";
			}			
		}
	}
}

function colorier_jours_vacances()
{
	zone = document.getElementById('configuration_zone_vacances').value;

	var $picker = $("#datepicker");
	var date=new Date($picker.datepicker('getDate'));
	// On fixe la date au dernier jour du mois précédent le mois en cours +2. Un simple "+1" pose prob si on est au 31/01 par exemple.
	mois = date.getMonth()+1;
	annee = date.getFullYear();


	//alert(mois+", "+annee);

	var xhrListerJoursVacances = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhrListerJoursVacances.open("GET", "lister_jours_vacances.php?zone="+zone+"&mois="+mois+"&annee="+annee, false);
	xhrListerJoursVacances.onreadystatechange = function() 
	{ 
		if(xhrListerJoursVacances.readyState == 4)
		{ 
			//joursVacances = xhrListerJoursVacances.responseText;
			//alert(xhrListerJoursVacances.responseText);



			dateVacances=xhrListerJoursVacances.responseText.split(";");
			//alert(dateVacances);

			for (k in dateVacances)
			{
				//alert(dateVacances[k]);
				dateSplit 	=	dateVacances[k].split("-");
				jour		=	dateSplit[2];
				// mois		=	dateSplit[1];
				// annee	=	dateSplit[0];

				//alert(jour);
				//alert(jour+"/"+mois+"/"+annee);
				numCase = parseInt(jourDebutMois)+parseInt(jour)-1;
				//alert(numCase);

				//document.getElementById('text_'+numCase).style.backgroundColor = "rgba(154, 164, 220, 0.4)";
				document.getElementById('text_'+numCase).style.color = "rgba(30, 30, 220, 1)";
				//document.getElementById('text_'+numCase).style.fontWeight = "bold";

			}
			

		}
		//alert(xhr.readyState); 
	} 
	xhrListerJoursVacances.send(null);

	gestion_jours_feries(annee, mois);
}

function gestion_jours_feries(annee, mois)
{	
	joursFeries = getJoursFeries(annee);

	for (l in joursFeries)
	{
		//alert(joursFeries[l]);
		jourFerie		=	joursFeries[l].getDate();
		moisFerie		=	joursFeries[l].getMonth()+1;



		if (moisFerie == mois)
		{
			numCase = parseInt(jourDebutMois)+parseInt(jourFerie)-1;
			document.getElementById('text_'+numCase).style.color = "rgba(250, 50, 50, 1)";
			//document.getElementById('text_'+numCase).style.fontWeight = "bold";
			//alert(jourFerie+"/"+moisFerie);
		}
	}
}

function getJoursFeries (an)
{
	var JourAn = new Date(an, "00", "01")
	var FeteTravail = new Date(an, "04", "01")
	var Victoire1945 = new Date(an, "04", "08")
	var FeteNationale = new Date(an,"06", "14")
	var Assomption = new Date(an, "07", "15")
	var Toussaint = new Date(an, "10", "01")
	var Armistice = new Date(an, "10", "11")
	var Noel = new Date(an, "11", "25")
	var SaintEtienne = new Date(an, "11", "26")
	
	var G = an%19
	var C = Math.floor(an/100)
	var H = (C - Math.floor(C/4) - Math.floor((8*C+13)/25) + 19*G + 15)%30
	var I = H - Math.floor(H/28)*(1 - Math.floor(H/28)*Math.floor(29/(H + 1))*Math.floor((21 - G)/11))
	var J = (an*1 + Math.floor(an/4) + I + 2 - C + Math.floor(C/4))%7
	var L = I - J
	var MoisPaques = 3 + Math.floor((L + 40)/44)
	var JourPaques = L + 28 - 31*Math.floor(MoisPaques/4)
	var Paques = new Date(an, MoisPaques-1, JourPaques)
	var VendrediSaint = new Date(an, MoisPaques-1, JourPaques-2)
	var LundiPaques = new Date(an, MoisPaques-1, JourPaques+1)
	var Ascension = new Date(an, MoisPaques-1, JourPaques+39)
	var Pentecote = new Date(an, MoisPaques-1, JourPaques+49)
	var LundiPentecote = new Date(an, MoisPaques-1, JourPaques+50)
	
//	return new Array(JourAn, VendrediSaint, Paques, LundiPaques, FeteTravail, Victoire1945, Ascension, Pentecote, LundiPentecote, FeteNationale, Assomption, Toussaint, Armistice, Noel, SaintEtienne)
	return new Array(JourAn, LundiPaques, FeteTravail, Victoire1945, Ascension, LundiPentecote, FeteNationale, Assomption, Toussaint, Armistice, Noel);

}

function choix_utilisateurs_evenement(choix, type)
{
	if (choix.value == "tout_le_monde")
	{
		listeGroupesConcernes = "ALL";
		listeUtilisateursConcernes = "ALL";
	}
	else if (choix.value == "certains_utilisateurs")
	{
		if(typeof(listeGroupesConcernes)=='undefined')
		{
			listeGroupesConcernes = "";
		}
		if(typeof(listeUtilisateursConcernes)=='undefined')
		{
			listeUtilisateursConcernes = "";
		}
		window.open ("choix_utilisateurs.php?listeGroupesConcernes="+listeGroupesConcernes+"&listeUtilisateursConcernes="+listeUtilisateursConcernes, "choix_utilisateurs", config='height=800, width=900, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no');
	}
	else
	{
		//alert(choix+", "+type);

		if (type == "groupe")
		{
			listeGroupesConcernes = choix;
		}
		if (type == "utilisateur")
		{
			listeUtilisateursConcernes = choix;
		}
	}
}

function afficher_options_evenement()
{
	if ( document.getElementById('checkEvenement').checked == true )
	{
		document.getElementById('option_evenement').style.display = "";
	}
	else
	{
		document.getElementById('option_evenement').style.display = "none";
	}
}

function afficher_que_utilisateur()
{
	if (document.getElementById('afficher_que_utilisateur').checked == false )
	{
		listeTable = document.getElementsByTagName('table');
		tailleTableOrigine = listeTable.length;
		//alert("taille de la table: "+tailleTableOrigine);

		for (j = 0 ; j < tailleTableOrigine ; j++)
		{
			// Je cache toutes les tables d'évènements
			if (	(listeTable[j].id.indexOf("text_evenement_") != -1 ) )
			{			
				document.getElementById(listeTable[j].id).style.visibility = "";
			}

			if (	(listeTable[j].id.indexOf("valide_evenement_") != -1 ) )
			{			
				document.getElementById(listeTable[j].id).style.visibility = "";
			}
		}	
				
	}



	if (document.getElementById('afficher_que_utilisateur').checked == true )
	{
		var xhrListeEvtUtilisateur = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhrListeEvtUtilisateur.open("GET", "lister_uniquement_evenements_utilisateur.php?idUtilisateur="+idUtilisateur+"&annee="+annee+"&mois="+mois, false);
		//alert("lister_uniquement_evenements_utilisateur.php?idUtilisateur="+idUtilisateur+"&annee="+annee+"&mois="+mois);
		xhrListeEvtUtilisateur.onreadystatechange = function() 
		{ 
			if(xhrListeEvtUtilisateur.readyState == 4)
			{ 
				listeEvenementUtilisateurs = xhrListeEvtUtilisateur.responseText;
				//alert(listeEvenementUtilisateurs);
				if (listeEvenementUtilisateurs == "")
				{
					//alert("vide");
					listeEvenementUtilisateurs = "NULL";
					//alert("Nouvel XHR= "+listeEvenementUtilisateurs);
				}
			}
			//alert(xhr.readyState); 
		} 
		xhrListeEvtUtilisateur.send(null);

		var tableEvent = listeEvenementUtilisateurs.split(",");
		// On cree le tableau donnees dans lequel on "pushera" ensuite nos donnees.
		//alert(tableEvent.length);


		listeTable = document.getElementsByTagName('table');
		tailleTableOrigine = listeTable.length;
		//alert("taille de la table: "+tailleTableOrigine);
		//alert("test de Guillaume: "+xhrListeEvtUtilisateur.responseText+" , taille de table: "+tailleTableOrigine);

		for (j = 0 ; j < tailleTableOrigine ; j++)
		{
			if (listeTable[j].id.indexOf("text_evenement_NULL") == -1)
			{
				//alert(listeTable[j].id);
				// Je cache toutes les tables d'évènements
				if (	(listeTable[j].id.indexOf("text_evenement_") != -1 ) )
				{
							//alert("test Guillaume: "+listeTable[j].id);			
					document.getElementById(listeTable[j].id).style.visibility = "hidden";
				}

				if (	(listeTable[j].id.indexOf("valide_evenement_") != -1 ) )
				{			
					document.getElementById(listeTable[j].id).style.visibility = "hidden";
				}

				//Pour chaque ID d'event concernant l'utilisateur, je passe l'affichage en visible.
				for (i = 0 ; i < tableEvent.length ; i++)
				{
					if (	(listeTable[j].id.indexOf("text_evenement_"+tableEvent[i]) != -1) )
					{
						document.getElementById("text_evenement_"+tableEvent[i]).style.visibility = "";
					}
					if (	(listeTable[j].id.indexOf("valide_evenement_"+tableEvent[i]) != -1) )
					{
						document.getElementById("valide_evenement_"+tableEvent[i]).style.visibility = "";
					}
				}
			}
		}
	}
}
function infos_utilisateurs(demande)
{
		var xhrInfosUtilisateurs = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhrInfosUtilisateurs.open("GET", "infos_utilisateurs.php?demande="+demande, false);
		//alert("infos_utilisateurs.php?demande="+demande);
		xhrInfosUtilisateurs.onreadystatechange = function() 
		{ 
			if(xhrInfosUtilisateurs.readyState == 4)
			{ 
				infos_utilisateurs = xhrInfosUtilisateurs.responseText;
				//alert(infos_utilisateurs);

			}
			//alert(xhr.readyState); 
		} 
		xhrInfosUtilisateurs.send(null);

		return infos_utilisateurs;
}

function qui_est_la()
{	
	if (document.getElementById('qui_est_la').checked == false )
	{

		//viderCalendar();

		var dateFormatMysql= $("#AltFieldDateMysql").val();	
		importerTableEventUtilisateur(dateFormatMysql);
		importerDonneesCalendrier(dateFormatMysql);


		listeTable = document.getElementsByTagName('table');
		tailleTableOrigine = listeTable.length;
		//alert("taille de la table: "+tailleTableOrigine);

		for (j = 0 ; j < tailleTableOrigine ; j++)
		{
			// Je cache toutes les tables de présence
			if (	(listeTable[j].id.indexOf("text_presence_") != -1 ) )
			{			
				document.getElementById(listeTable[j].id).style.display = "none";
			}
			// Je cache toutes les tables de présence
			if (	(listeTable[j].id.indexOf("presence_case_") != -1 ) )
			{			
				document.getElementById(listeTable[j].id).style.display = "none";
			}

			// J'affiche toutes les tables d'évènements
			if (	(listeTable[j].id.indexOf("congeId") != -1 ) )
			{			
				document.getElementById(listeTable[j].id).style.display = "";
			}
		}			
	}

	if (document.getElementById('qui_est_la').checked == true )
	{

		viderCalendar();
		colorier_jours_vacances();
		aujourdhui();


		//////// Plus utile avec le viderCalendar
		// listeTable = document.getElementsByTagName('table');
		// tailleTableOrigine = listeTable.length;
		// //alert("taille de la table: "+tailleTableOrigine);
		// //alert("test de Guillaume: "+xhrListeEvtUtilisateur.responseText+" , taille de table: "+tailleTableOrigine);
		// for (j = 0 ; j < tailleTableOrigine ; j++)
		// {

		// 	if (	(listeTable[j].id.indexOf("congeId") != -1 ) )
		// 	{
		// 		document.getElementById(listeTable[j].id).style.display = "none";
		// 	}

		// 	// Je cache toutes les tables de présence
		// 	if (	(listeTable[j].id.indexOf("text_presence_") != -1 ) )
		// 	{			
		// 		document.getElementById(listeTable[j].id).style.display = "";
		// 	}
		// 	// Je cache toutes les tables de présence
		// 	if (	(listeTable[j].id.indexOf("presence_case_") != -1 ) )
		// 	{			
		// 		document.getElementById(listeTable[j].id).style.display = "";
		// 	}

		// }

		date = document.getElementById('AltFieldDateMysql').value;
		dateSplit 	=	date.split("-");
		mois		=	dateSplit[1];
		annee 		=	dateSplit[0];





		// Si j'utilise la fonction "info_utilisateurs" qui contient ces lignes ajax, il ne la voit pas lors des suivants / précédents. La console me dit "infos_utilisateurs() n'est pas une fonction"
		var xhrInfosUtilisateurs = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhrInfosUtilisateurs.open("GET", "infos_utilisateurs.php?demande=infos_users_actifs", false);
		//alert("infos_utilisateurs.php?demande="+demande);
		xhrInfosUtilisateurs.onreadystatechange = function() 
		{ 
			if(xhrInfosUtilisateurs.readyState == 4)
			{ 
				infos_utilisateurs = xhrInfosUtilisateurs.responseText;
				//alert(infos_utilisateurs);

			}
			//alert(xhr.readyState); 
		} 
		xhrInfosUtilisateurs.send(null);

		var listeInfosUsersActifs						= infos_utilisateurs;

		//var listeInfosUsersActifs						= infos_utilisateurs("infos_users_actifs");
		var tableInfosUsersActifs 						= listeInfosUsersActifs.split(",");
		//alert(tableInfosUsersActifs.length);







		var xhrQuiEstLa = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		//alert("lister_uniquement_evenements_utilisateur.php?annee="+annee+"&mois="+mois);
		xhrQuiEstLa.open("GET", "lister_qui_est_la.php?annee="+annee+"&mois="+mois, false);
		xhrQuiEstLa.onreadystatechange = function() 
		{ 
			if(xhrQuiEstLa.readyState == 4)
			{ 
				listeQuiEstLa = xhrQuiEstLa.responseText;
				//alert(listeQuiEstLa);
				if (listeQuiEstLa == "")
				{
					//alert("vide");
					listeQuiEstLa = "NULL";
					//alert("Nouvel XHR= "+listeQuiEstLa);
				}
			}
			//alert(xhr.readyState); 
		} 
		xhrQuiEstLa.send(null);

		var tableQuiEstLa = listeQuiEstLa.split("///");
		// On cree le tableau donnees dans lequel on "pushera" ensuite nos donnees.
		//alert(tableQuiEstLa.length);



			jourDebutMois= parseInt(jourDebutMois);
			nbrJoursMois=parseInt(nbrJoursMois);
			// Je défini la limite pour savoir jusqu'où va aller mon calendar
			var limite	=	Number(jourDebutMois+nbrJoursMois);
	
			//alert(jourDebutMois+", "+limite);
		



			for (j = jourDebutMois;	j < limite	; j++)
			{
				//document.getElementById('table_calendrier_'+i).style.visibility = "visible";
				//document.getElementById('table_calendrier_'+i).style.opacity = 1;
				numCase = j;

				// Les samedi et dimanche, on ne fait rien.
				var casesWE = new Array(6, 7, 13, 14, 20, 21, 27, 28, 34, 35);
				if (casesWE.indexOf(j) == -1)
				{

					var tableauUserOccupeDate = [];// On défini le jour traité selon la case en cours de traitement
					var tableauUserOccupeMatin = [];
					var tableauUserOccupeAprem = [];
					dateJoursTraitement = parseInt(numCase)-parseInt(jourDebutMois)+1;
						
						for (i = 0 ; i < tableQuiEstLa.length ; i++)
						{
							var tableInfo = tableQuiEstLa[i].split(";");

							date 					= tableInfo[0];
							periode 				= tableInfo[1];
							idUtilisateur 			= tableInfo[2];
							loginUtilisateur 		= tableInfo[3];
							nom 					= tableInfo[4];
							prenom 					= tableInfo[5];
							couleur 				= tableInfo[6];

							dateSplit 	=	date.split("-");
							jour		=	dateSplit[2];

							if ( (jour == dateJoursTraitement)  ) 
							{
								// Si l'id n'est pas encore dans la table, on l'ajoute
								if (tableauUserOccupeDate.indexOf(idUtilisateur) == -1)
								{
									tableauUserOccupeDate.push(idUtilisateur);
								}
							}
							if ( (jour == dateJoursTraitement) && (periode == "JO") ) 
							{	
								// Si l'id n'est pas encore dans la table matin, on l'ajoute
								if (tableauUserOccupeMatin.indexOf(idUtilisateur) == -1)
								{
									tableauUserOccupeMatin.push(idUtilisateur);
								}
								// Si l'id n'est pas encore dans la table aprem, on l'ajoute
								if (tableauUserOccupeAprem.indexOf(idUtilisateur) == -1)
								{
									tableauUserOccupeAprem.push(idUtilisateur);
								}
							}
							if ( (jour == dateJoursTraitement) && (periode == "MA") )
							{
								// Si l'id n'est pas encore dans la table matin, on l'ajoute
								if (tableauUserOccupeMatin.indexOf(idUtilisateur) == -1)
								{
									tableauUserOccupeMatin.push(idUtilisateur);
								}

							}
							if ( (jour == dateJoursTraitement) && (periode == "AM") )
							{
								// Si l'id n'est pas encore dans la table aprem, on l'ajoute
								if (tableauUserOccupeAprem.indexOf(idUtilisateur) == -1)
								{
									tableauUserOccupeAprem.push(idUtilisateur);
								}
							}


							nombreUtilisateursActifs = tableInfosUsersActifs.length;
							
							nombrePresentsCeJours = nombreUtilisateursActifs - tableauUserOccupeDate.length;
							nombrePresentsCeMatin = nombreUtilisateursActifs - tableauUserOccupeMatin.length;
							nombrePresentsCetAprem = nombreUtilisateursActifs - tableauUserOccupeAprem.length;

						}

						// Si tout le monde est là, on change la couleur du texte de présence.
						couleurTextPresenceJour = "white";
						couleurTextPresenceMatin = "white";
						couleurTextPresenceAprem = "white";
						if (nombrePresentsCeJours == nombreUtilisateursActifs)
						{
							couleurTextPresenceJour = "E34042";
						}
						if (nombrePresentsCeMatin == nombreUtilisateursActifs)
						{
							couleurTextPresenceMatin = "E34042";
						}
						if (nombrePresentsCetAprem == nombreUtilisateursActifs)
						{
							couleurTextPresenceAprem = "E34042";
						}


					// On met le bandeau noir de taux de présence:
					bandeauTauxPresence = 	"<TABLE width=100% align=center border=0 id=text_presence_"+numCase+">"
															+"<tr>"
																+"<td align=center>"
																+""
																+"<font color="+couleurTextPresenceJour+" size='1'>Présence: </font><font color="+couleurTextPresenceJour+" size='3'><b>"+nombrePresentsCeJours+"</b> sur "+nombreUtilisateursActifs+"</font>"
																+"<br />"
																+"</td>"
															+"</tr>"
															+"<tr>"
																+"<td>"
																	+"<TABLE width='100%' id='text_presence_matin_am'>"
																		+"<tr>"
																			+"<td align='center'>"
																				+"<font color="+couleurTextPresenceMatin+" size=1>Matin "+nombrePresentsCeMatin+" / "+nombreUtilisateursActifs+"</font>"
																			+"</td>"
																			+"<td align='center'>"
																				+"<font color="+couleurTextPresenceAprem+" size=1>Après m. "+nombrePresentsCetAprem+" / "+nombreUtilisateursActifs+"</font>"
																			+"</td>"
																		+"<tr>"
																	+"</table>"
																+"</td>"
															+"</tr>"
														+"</TABLE>";

					document.getElementById('table_calendrier_'+numCase).innerHTML 	+= bandeauTauxPresence;

					opaciteBandeauPresent = Math.pow(parseInt(nombrePresentsCeJours)/parseInt(nombreUtilisateursActifs), 2);
					//alert(opaciteBandeauPresent);
					//document.getElementById('tauxPresent_'+numCase).style.opacity = opaciteBandeauPresent;
					document.getElementById('text_presence_'+numCase).style.backgroundColor = "rgba(68, 68, 68, "+opaciteBandeauPresent+")";

					// J'affiche tous les utilisateurs actifs:
					for ( i = 0 ; i < tableInfosUsersActifs.length ; i++)
					{

						var tableInfo = tableInfosUsersActifs[i].split(";");

						//alert(tableInfo);

						idUser 					= tableInfo[0];
						nomUser 				= tableInfo[1];
						prenomUser	 			= tableInfo[2];
						loginUser				= tableInfo[3];
						couleurUser				= tableInfo[4];


						bandeau = 				"<TABLE width=100% align=center border=0 id=presence_case_"+numCase+"_id_"+idUser+">" //+" bgcolor="+couleurUser	
													+"<tr>"
														+"<td align='left' width='12%'>"
							
														+"</td>"
														+"<td align='left' id=presence_matin_case_"+numCase+"_id_"+idUser+">"
															+nomUser
														+"</td>"
														+"<td align='right' id=presence_aprem_case_"+numCase+"_id_"+idUser+">"
															+prenomUser
														+"</td>"
														+"<td align='right' width='12%'>"
										
														+"</td>"
													+"</tr>"
												+"</TABLE>";

						document.getElementById('table_calendrier_'+numCase).innerHTML 	+= bandeau;		
						document.getElementById('presence_case_'+numCase+'_id_'+idUser).style.opacity = 1;

						// Si c'est le dernier de la table, on ajoute derrière une table vide pour que ce soit plus propre visuellement
						if ( i == tableInfosUsersActifs.length-1 )
						{
							document.getElementById('table_calendrier_'+numCase).innerHTML 	+=  				
													"<TABLE width=100% align=center border=0 id=presence_case_"+numCase+"_id_case_vide>" //+" bgcolor="+couleurUser	
													+"<tr>"
														+"<td><br />" 
														+"</td>"
													+"</tr>"
												+"</TABLE>";								
						}

					}
				}
			}





			// La table quiestla semble trop longue de 1
			for (i = 0 ; i < tableQuiEstLa.length-1 ; i++)
			{
				var tableInfo = tableQuiEstLa[i].split(";");

				date 					= tableInfo[0];
				periode 				= tableInfo[1];
				idUtilisateur 			= tableInfo[2];
				loginUtilisateur 		= tableInfo[3];
				nom 					= tableInfo[4];
				prenom 					= tableInfo[5];
				couleur 				= tableInfo[6];



			 			//renseignerCongeCalendrier(idConge, idUtilisateur, , couleur, date, periode, type, valide, description, bloquant)


				dateSplit 	=	date.split("-");
				jour		=	dateSplit[2];
				mois		=	dateSplit[1];
				annee		=	dateSplit[0];
				//alert(jour+"/"+mois+"/"+annee);
				
				
				numCase = parseInt(jourDebutMois)+parseInt(jour)-1;


				if (periode == "JO")
				{
					document.getElementById('presence_matin_case_'+numCase+'_id_'+idUtilisateur).style.opacity = 0.2;
					document.getElementById('presence_aprem_case_'+numCase+'_id_'+idUtilisateur).style.opacity = 0.2;
				}
				if (periode == "MA")
				{
					document.getElementById('presence_matin_case_'+numCase+'_id_'+idUtilisateur).style.opacity = 0.2;
				}
				if (periode == "AM")
				{
					document.getElementById('presence_aprem_case_'+numCase+'_id_'+idUtilisateur).style.opacity = 0.2;
				}
			}
		
			jour = 1;
		for (numCase = jourDebutMois;	numCase < limite	; numCase++)
		{
			// Sans ca le numéro du jour disparait sur le calendrier.
			document.getElementById('text_'+numCase).value = jour;
			
			jour++ ;
		}

	}

}

function visuel_mois()
{
	nomMois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

	document.getElementById('td_visuel_mois').innerHTML = nomMois[parseInt(mois-1)];
}

function aujourdhui()
{
	var aujourdhui = new Date();
	var anneeAujourdhui   = aujourdhui.getFullYear();
	var moisAujourdhui    = aujourdhui.getMonth() + 1;
	var jourAujourdhui    = aujourdhui.getDate();

	caseAujourdhui = parseInt(jourDebutMois)+parseInt(jourAujourdhui)-1;

	if(moisAujourdhui == mois && anneeAujourdhui == annee)
	{
		//alert("jour="+jourAujourdhui+" , mois="+moisAujourdhui+" , annee="+anneeAujourdhui+" , case="+caseAujourdhui);
		document.getElementById('table_calendrier_'+caseAujourdhui).className="aujourdhui";
	}
}

</script>

</head>

<BODY onLoad="demarrage();">
<?php
include("connexion_base.php"); 

?>
<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id="tableGlobale"><tr><td></td><td id="tableGlobale">

<?php include("menu_general.php") ?>

<input type="hidden" id="loginSession" value="<?php echo $loginSession ?>" />
<input type="hidden" id="idSession" value="<?php echo $idSession ?>" />


<table id="tableHaut" width="100%" border="0">
	<tr>
		<td width=25%>
			<table border=0 id="tableParentSuivantPrecedentDatePicker">
				<tr>
					<td valign="bottom"><br>
						<table id="suivantPrecedentDatepicker" border=0 width=100%>
							<tr align=center >
								<td>
									<!-- Test suivant -->
									<input type="button" id="moisPrecedent"  name="moisPrecedent" value="Précédent" onClick="javascript:moisPrecedent()">
									<input type="button" id="moisProchain"  name="moisProchain" value="Suivant" onClick="javascript:moisProchain()">	
								</td>
							</tr>
							<tr align=center>
								<td align=center>
									<!-- Calendrier -->
									<input type="text" id="datepicker" name="date" />
									<!-- Champ caché permettant de mettre la date en "altfield" pour l avoir au format MYSQL (voir fonction altfiel et altformat dans la declaration du calendrier) -->
									<input type="hidden" id="AltFieldDateMysql" name="AltFieldDateMysql" />
								</td>
							</tr>
						</table>
					</td>
				<tr>
				<tr height=30px>
				</tr>
			</table>
		</td>
		
		<td>
			<table width='100%' border="0">
				<tr>
					<td>
						<br />
					</td>
				</tr>				
				<tr>
					<td border="0" id='td_visuel_mois'>
					</td>
				</tr>
				<tr>
					<td>
					</td>
				</tr>
			</table>
		</td>

		<?php 	include("importer_preferences_utilisateurs.php") ;
				include("importer_configuration.php");
		?>

		<td td width=28% align=right border="0">
			<table border="0" id="preferences_calendrier">
				<tr>
					<td>
						<label for="qui_est_la"><input type=checkbox id="qui_est_la" value="qui_est_la" onChange="qui_est_la();"> Qui est là?</label>						
						<br />


						<label for="afficher_astreintes"><input type=checkbox id="afficher_astreintes" value="afficher_astreintes" onChange="afficher_astreintes();"> Afficher les astreintes </label>
						<?php 	include("importer_preferences_utilisateurs.php") ;
								include("importer_configuration.php");
						?>
						<input type="hidden" id="pref_afficher_astreintes" value='<?php echo "$preferences_afficher_astreintes" ?>'>
						<input type="hidden" id="configuration_zone_vacances" value='<?php echo "$zoneVacancesConfig" ?>'>
						<input type="hidden" id="renseigner_automatiquement_conge_valide" value='<?php echo "$renseignerAutomatiquementCongeValide" ?>'>
						<br />


						<label for="afficher_que_utilisateur"><input type=checkbox id="afficher_que_utilisateur" value="afficher_que_utilisateur" onChange="afficher_que_utilisateur();"> Masquer les infos qui ne me concernent pas</label>
						

					</td>
				</tr>
			</table>
		</td>
		<td width=5%>
		</td>
	</tr>
</table>



<table id="table3pcent" width=100%><tr><td width=3%></td><td>
	<table width=100%  border=0 id="table_calendrier_global">
		<?php
			echo "<tr id=joursSemaine><td>Lundi</td><td>Mardi</td><td>Mercredi</td><td>Jeudi</td><td>Vendredi</td><td>Samedi</td><td>Dimanche</td></tr>";
			$numCase=1;
			for ( $i = 1 ; $i <= 6 ; $i++)
			{
			echo "<tr>\n";
				for ($j = 1 ; $j <= 7 ; $j++)
				{
				// Permet de lancer l action avec un simple clic
				//	echo "\t\t<td id=table_calendrier_$numCase onClick=repereJourClique($numCase); onmouseout=unscale($numCase);> <input type=text id=text_$numCase disabled>	</td>\n";
				
				// Permet de lancer l action de selection uniquement en cas de double clic
					echo "\t\t 	<td id=table_calendrier_$numCase onDblclick=repereJourClique($numCase); onmouseout=unscale($numCase);> 
									<table id=table_text_$numCase>
										<tr>
											<td width=20%>
												<input type=text id=text_$numCase disabled>
											</td>
											<td id=astreinte_$numCase>
												
											</td>
										</tr>
									</table>
								</td>\n";	
				$numCase++;
				}
			echo "\t</tr>\n\t";
			}

		?>
	</table>
</td><td width=3%></td></tr></table>
<br>
<br>
<table border="0" id="choixTypeConge" width="95%">
	<tr>
		<td width="17%">

		</td>
	
		<td>	
			Que voulez vous appliquer au(x) <input type="text" id="nbrJours" style="width:20px" value="0"> jour(s) selectionnés?<br /><br />				
			<?php 
				echo '<br> <label for="checkAbsence"><input type="checkbox" name="absent" value="absent" id="checkAbsence" onClick="javascript:conge_coche(this)"> Une absence: </label>';
				// Je mets dans un champ hidden la valeur de l'ID axe1 correspondant au code comptable 99 = "congés et autres absences"
				echo '<input type="hidden" id="id_axe1_code_99" value="' . $idAxe1Code99 . '">';
				echo '<input type="hidden" id="id_axe2_code_9900" value="' . $idAxe2Code9900 . '">';

				$reponse_absence = $bdd->query('SELECT * FROM Axe2 WHERE codeAxe2 like "99%%" ORDER BY nomAxe2 ');

				echo "<select name=type_absence id=type_absence onClick=javascript:conge_coche(this)>";
				echo "<option value=absence_non_choisie>Choisir le type d'absence</option>";		
				while ($donnees = $reponse_absence->fetch())
				{
					$type_absence=$donnees['nomAxe2'];
					$id_absence=$donnees['idAxe2'];
				
				echo 	"<option value='$type_absence'> $type_absence </option>";
				
				}
				$reponse_absence->closeCursor(); // Termine le traitement de la requête
				echo "</select>"; 
			?>

			<?php

				echo "	<br />
						<br />
						<br />
						<label title=\"Définir une astreinte pour la personne selectionnée dans la liste\"><input type=checkbox id=checkAstreinte> Définir une personne d'astreinte: </label>
					";


				$reponse_utilisateurs = $bdd->query('SELECT * FROM Utilisateurs WHERE active = "1" ORDER BY prenom ');

				echo "<select name=astreinte id=astreinte onClick=javascript:conge_coche(this)>";
				echo "<option value=astreinte_non_choisie>Choisir un utilisateur</option>";		
				while ($donnees = $reponse_utilisateurs->fetch())
				{
					$nom=$donnees['nom'];
					$prenom=$donnees['prenom'];
					$id=$donnees['idUtilisateurs'];
				
				echo 	"<option value='$id'> $prenom $nom </option>";
				
				}
				$reponse_utilisateurs->closeCursor(); // Termine le traitement de la requête
				echo "</select>";

			?>
						<br />
			<br />
			<br />
			<label for="checkEvenement"><input type="checkbox" name="choice" id="checkEvenement" value="evenement" onClick="afficher_options_evenement()"> Créer un évènement: </label>
			<input type="text" id="choix_nomEvenement">

				<table id="option_evenement" width="100%" border="0">
					<tr>
						<td>
						</td>

						<td align="center">
							<br />- Cet évènement concerne: 
						</td>

						<td>
							<FORM>
								<br /><input type="radio" id="utilisateurs_concernes_tous" name="utilisateurs_concernes" value="tout_le_monde" onClick="choix_utilisateurs_evenement(this)"><label for="utilisateurs_concernes_tous"> Tous les utilisateurs</label>
								<br /><input type="radio" id="utilisateurs_concernes_certains" name="utilisateurs_concernes" value="certains_utilisateurs" onClick="choix_utilisateurs_evenement(this)"><label for="utilisateurs_concernes_certains"> Certains utilisateurs</label>
							</FORM>
						</td>
					</tr>
					<tr>
						<td width="10%">
						</td>

						<td width="42%">
							<br /><label title="Cocher cette case vous fera apparaitre comme absent de votre lieu de travail."><input type=checkbox id="indisponible"> Cet événement rend indisponible</label>
						</td>
						<td>
							<br />							
							<?php
								$admin	=	$_SESSION['admin'];
								if ($admin == 1 )
								{
									//echo "<table><tr><td>";
									echo "	<input type=hidden id=ligne_test value=1>	
											<label title=\"Bloquer les demandes d'absence sur la (les) journée(s) selectionnée(s)\"><input type=checkbox id=bloquant> Verrouiller la période.</label>";
									//echo "</td></tr></table>";
								}
								if ( $admin != 1 )
								{
									echo "<input type=hidden id=bloquant>";
								}
							?>
							<br><br>
						</td>
					</tr>
				</table>


		</td>

		<td align=left width="20%">
			Période: <select name="periode" id="periode"> 
				<option value="JO">Journée entière</option>
				<option value="MA">Matin</option>
				<option value="AM">Après midi</option>
			</select>
		</td>
		
		<td align=right width="10%">
				<input type="button" name="valider" id="valider" value="Valider" onclick="validerCalendrier();" />	
		</td>
	</tr>
</table>

<!-- Fin de la table de mise en page globale -->
</td><td></td></tr></table>

</body>
</html>
