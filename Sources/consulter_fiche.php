<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<?php session_start(); ?>
<head>
<?php // Permet de rediriger vers l'acceuil si utilisateur non enregistré.
	$prenom = $_SESSION['prenom'];
	if (!$prenom)
	{
		header('Location: index.php'); 
	} 
?>
<?php include("checkAdmin.php"); ?>

	<title>Consultation des fiches de déclaration d'heures</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- Mon thème -->
	<link rel="stylesheet" href="style.css" />

<!-- Integration de jquery calendar http://jqueryui.com/datepicker/ -->
<link rel="stylesheet" href="CSS/jquery-ui.css" />
<script src="jquery-1.8.3.js"></script>
<script src="jquery-ui.js"></script>

<link rel="stylesheet" href="CSS/Delta/css/normalise.css"> 
<link rel="stylesheet" href="CSS/Delta/theme/jquery-ui.css">
	<script src="CSS/Delta/js/modernizr-2.0.6.min.js"></script>
<link rel="icon" type="image/png" href="favicon.png" />

<script>
//window.onload=demarrage();

	$(function()	
	{

		$( "#datepicker" ).datepicker (
			{
			    	changeMonth: true,
				changeYear: true,
				onSelect:	function() 
						{  
						    	var vaDate = document.getElementById('AltFieldDateMysql').value;
							razFormulaire();
							importer_fiche_ajax(vaDate);
							importer_heures_ajax(vaDate);
							validation_semaine(vaDate);						  
						}, 

			}			);


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
		$(datepicker).datepicker('setDate', new Date('-1'));

		

	}
	);

function demarrage()
{
	var dateFormatMysql= $("#AltFieldDateMysql").val();
	razFormulaire();
	importer_fiche_ajax(dateFormatMysql);
	importer_heures_ajax(dateFormatMysql);
	validation_semaine(dateFormatMysql);
}


function hier()
{
	razFormulaire();

	// Inspiré
	var $picker = $("#datepicker");
	var date=new Date($picker.datepicker('getDate'));
	date.setDate(date.getDate()-1);
	$picker.datepicker('setDate', date);


	var dateFormatMysql= $("#AltFieldDateMysql").val();
	//alert (dateFormatMysql);
	importer_fiche_ajax(dateFormatMysql);
	importer_heures_ajax(dateFormatMysql);
	validation_semaine(dateFormatMysql);

	return false;
	// De moi
	//$(datepicker).datepicker('setDate', "-1d");
}

function demain()
{
	razFormulaire();

	// Next Day Link
	var $picker = $("#datepicker");
	var date=new Date($picker.datepicker('getDate'));
	date.setDate(date.getDate()+1);
	$picker.datepicker('setDate', date);


	var dateFormatMysql= $("#AltFieldDateMysql").val();
	//alert (dateFormatMysql);
	importer_fiche_ajax(dateFormatMysql);
	importer_heures_ajax(dateFormatMysql);
	validation_semaine(dateFormatMysql);

	return false;
}

function AllerJourNonValide() // Permet de se rendre au plus vieux jour non valide (ou non rempli)
{
	var dateFormatMysql= $("#AltFieldDateMysql").val();

	//alert(dateFormatMysql);
		var idConsultUser = document.getElementById('id_select_nom').value;
		var xhr_jour_non_valide = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhr_jour_non_valide.open("GET", "trouve_jour_non_valide.php?dateToImport="+dateFormatMysql+"&idConsultUser="+idConsultUser, true);
		xhr_jour_non_valide.onreadystatechange = function() 
		{ 
			if(xhr_jour_non_valide.readyState == 4)
			{ 
				//alert(xhr_jour_non_valide.responseText);
				
				xhrText=xhr_jour_non_valide.responseText;
				
				
				dateFormatMysql=xhrText;
					
				$(datepicker).datepicker('setDate', new Date(dateFormatMysql));

					
				razFormulaire();					
					
				importer_fiche_ajax(dateFormatMysql);
				importer_heures_ajax(dateFormatMysql);
				validation_semaine(dateFormatMysql);
			}
			//alert(xhr_jour_non_valide.readyState); 
		} 

		xhr_jour_non_valide.send(null);

}

function rafraichir_page(deQui)
{
	if (deQui == "xhrSuppr")
	{
		alert("La periode a bien été supprimée.");
	}
	razFormulaire();
	var dateFormatMysql= $("#AltFieldDateMysql").val();
	importer_fiche_ajax(dateFormatMysql);
	importer_heures_ajax(dateFormatMysql);
	validation_semaine(dateFormatMysql);
	//calcul_total_journee();


}

function razFormulaire()
{
	couleur="white";
	// On vide déjà tout pour éviter que plusieurs jours ne se superposent.
	for (i=1; i<=12; i++)
	{
			if (i <=6) quand="Matin";
			else quand="Aprem";
			document.getElementById('de'+quand+'_periode'+i).value		= "";
			document.getElementById('de'+quand+'_periode'+i).style.backgroundColor = couleur;

			document.getElementById('a'+quand+'_periode'+i).value		= "";
			document.getElementById('a'+quand+'_periode'+i).style.backgroundColor = couleur;

			document.getElementById('choix_axe1_periode'+i).value		= "Choisir axe 1";
			document.getElementById('choix_axe1_periode'+i).style.backgroundColor = couleur;
			document.getElementById('id_choix_axe1_periode'+i).value	= "";

			document.getElementById('choix_axe2_periode'+i).value		= "Choisir axe 2";
			document.getElementById('choix_axe2_periode'+i).style.backgroundColor = couleur;
			document.getElementById('id_choix_axe2_periode'+i).value	= "";

			document.getElementById('choix_axe3_periode'+i).value		= "Choisir axe 3";
			document.getElementById('choix_axe3_periode'+i).style.backgroundColor = couleur;
			document.getElementById('id_choix_axe3_periode'+i).value	= "";

			document.getElementById('total'+quand+'_periode'+i).value	= "";

			document.getElementById('id_horaire_periode'+i).value	= "";

			document.getElementById('total_journee').value="0";

	}
}
function validation_semaine(dateToImport)
{
		dateToImport=dateToImport;
		//alert(dateToImport);
			//idUtilisateur=1;
			var idConsultUser = document.getElementById('id_select_nom').value;
			var xhr_valide_semaine = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhr_valide_semaine.open("GET", "validation_semaine.php?dateToImport="+dateToImport+"&idConsultUser="+idConsultUser, true);
			xhr_valide_semaine.onreadystatechange = function() 
			{ 
				if(xhr_valide_semaine.readyState == 4)
				{ 
					//alert(xhr_valide_semaine.responseText);
					decouperChaineValidationSemaine(xhr_valide_semaine);
				}
				//alert(xhr_valide_semaine.readyState); 
			} 

			xhr_valide_semaine.send(null);
}

function decouperChaineValidationSemaine(xhr, dateToImport)
{
	// On transforme l'objet recu en chaine de caractères..
	xhrText=xhr.responseText;
	//alert(xhrText);

	// On decoupe cette chaine de caractères d'abord pour avoir les differentes dates.
	semaine=xhrText.split(",");
	//alert(semaine);



	//// Nous allons gérer les variables "colonne = xxx" en les transformant en "xxx".
	// On cree le tableau donneesHeures dans lequel on "pushera" ensuite nos donneesHeures.
	donneesSemaine		=	new Array ();
		
	// On "push" ensuite la 2eme colonne de notre réponse (donc la valeur de la variable) dans notre tableau "donneesHeures".
	donneesSemaine.push(semaine[0]);
	donneesSemaine.push(semaine[1]);	
	donneesSemaine.push(semaine[2]);
	donneesSemaine.push(semaine[3]);
	donneesSemaine.push(semaine[4]);
	donneesSemaine.push(semaine[5]);
	donneesSemaine.push(semaine[6]);
	donneesSemaine.push(semaine[7]);
	donneesSemaine.push(semaine[8]);
	donneesSemaine.push(semaine[9]);
	donneesSemaine.push(semaine[10]);
	donneesSemaine.push(semaine[11]);

	lundiMatin		=	donneesSemaine[0];
	lundiAprem		=	donneesSemaine[1];
	mardiMatin		=	donneesSemaine[2];
	mardiAprem		=	donneesSemaine[3];
	mercrediMatin		=	donneesSemaine[4];
	mercrediAprem		=	donneesSemaine[5];
	jeudiMatin		=	donneesSemaine[6];
	jeudiAprem		=	donneesSemaine[7];
	vendrediMatin		=	donneesSemaine[8];
	vendrediAprem		=	donneesSemaine[9];

	jourCourant		=	donneesSemaine[10];

	semaineCourante		=	donneesSemaine[11];


	couleurValide="#B0DD8D";
	couleurNonValide="#d6d6d6";

	document.getElementById('numero_semaine').value = semaineCourante;

	document.getElementById('valid_semaine_lundi').style.backgroundColor = couleurNonValide;
	document.getElementById('valid_semaine_mardi').style.backgroundColor = couleurNonValide;
	document.getElementById('valid_semaine_mercredi').style.backgroundColor = couleurNonValide;
	document.getElementById('valid_semaine_jeudi').style.backgroundColor = couleurNonValide;
	document.getElementById('valid_semaine_vendredi').style.backgroundColor = couleurNonValide;


	if (lundiMatin > 0 && lundiAprem > 0)
	{
		document.getElementById('valid_semaine_lundi').style.backgroundColor = couleurValide;
	}
	if (mardiMatin > 0 && mardiAprem > 0)
	{
		document.getElementById('valid_semaine_mardi').style.backgroundColor = couleurValide;
	}
	if (mercrediMatin > 0 && mercrediAprem > 0)
	{
		document.getElementById('valid_semaine_mercredi').style.backgroundColor = couleurValide;
	}
	if (jeudiMatin > 0 && jeudiAprem > 0)
	{
		document.getElementById('valid_semaine_jeudi').style.backgroundColor = couleurValide;
	}
	if (vendrediMatin > 0 && vendrediAprem > 0)
	{
		document.getElementById('valid_semaine_vendredi').style.backgroundColor = couleurValide;
	}
		document.getElementById('valid_semaine_lundi').style.MozTransform = "scale(1)";
		document.getElementById('valid_semaine_mardi').style.MozTransform = "scale(1)";
		document.getElementById('valid_semaine_mercredi').style.MozTransform = "scale(1)";
		document.getElementById('valid_semaine_jeudi').style.MozTransform = "scale(1)";
		document.getElementById('valid_semaine_vendredi').style.MozTransform = "scale(1)"

		document.getElementById("valid_semaine_lundi").style.boxShadow="0px 0px 0px #999";
		document.getElementById("valid_semaine_mardi").style.boxShadow="0px 0px 0px #999";
		document.getElementById("valid_semaine_mercredi").style.boxShadow="0px 0px 0px #999";
		document.getElementById("valid_semaine_jeudi").style.boxShadow="0px 0px 0px #999";
		document.getElementById("valid_semaine_vendredi").style.boxShadow="0px 0px 0px #999";

		
	if (jourCourant == 1)
	{
		document.getElementById('valid_semaine_lundi').style.MozTransform = "scale(1.5)";
		document.getElementById("valid_semaine_lundi").style.boxShadow="2px 3px 3px #999";
		document.getElementById("valid_semaine_lundi").style.transition="300ms ease";
	}
	if (jourCourant == 2)
	{
		document.getElementById('valid_semaine_mardi').style.MozTransform = "scale(1.5)";
		document.getElementById("valid_semaine_mardi").style.boxShadow="2px 3px 3px #999";
		document.getElementById("valid_semaine_mardi").style.transition="300ms ease";
	}
	if (jourCourant == 3)
	{
		document.getElementById('valid_semaine_mercredi').style.MozTransform = "scale(1.5)";
		document.getElementById("valid_semaine_mercredi").style.boxShadow="2px 3px 3px #999";
		document.getElementById("valid_semaine_mercredi").style.transition="300ms ease";

	
	}
	if (jourCourant == 4)
	{
		document.getElementById('valid_semaine_jeudi').style.MozTransform = "scale(1.5)";
		document.getElementById("valid_semaine_jeudi").style.boxShadow="2px 3px 3px #999";
		document.getElementById("valid_semaine_jeudi").style.transition="300ms ease";

	
	}
	if (jourCourant == 5)
	{
		document.getElementById('valid_semaine_vendredi').style.MozTransform = "scale(1.5)";
		document.getElementById("valid_semaine_vendredi").style.boxShadow="2px 3px 3px #999";
		document.getElementById("valid_semaine_vendredi").style.transition="300ms ease";
	}

	//colorierValideSemaine(lundiMatin	,lundiAprem	,mardiMatin	,mardiAprem	,mercrediMatin	,mercrediAprem	,jeudiMatin	,jeudiAprem	,vendrediMatin	,vendrediAprem);
}

function aller_jour_semaine(jour)
{
	razFormulaire();

	// Recupère la date en cours de consultation
	var $picker = $("#datepicker");
	var date=new Date($picker.datepicker('getDate'));

	// Met dans la variable "n" le numero de jour de la semaine (0=lundi, 6=dimanche)
	var n = date.getUTCDay(); 

	// Calcul le nombre de jour à décaler pour atteindre le jour souhaité.
	var bouger=Number(jour)-Number(n);
	//alert('Je veux afficher: '+jour+' et je suis: '+n+', Je dois faire: '+bouger);

	// Set la nouvelle date calculée en bougeant du nombre de jour calculé plus haut.
	var date=new Date($picker.datepicker('getDate'));
	date.setDate(date.getDate()+bouger);
	$picker.datepicker('setDate', date);


	var dateFormatMysql= $("#AltFieldDateMysql").val();
	//alert (dateFormatMysql);

	importer_fiche_ajax(dateFormatMysql);
	importer_heures_ajax(dateFormatMysql);
	validation_semaine(dateFormatMysql);

	return false;
}
//////////////////////////////////////////////////////////
function getXMLHttpRequest() {
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


function importer_fiche_ajax(dateToImport)
{
	dateToImport=dateToImport;
	//	alert("debut test");
			//idUtilisateur=1;
			var idConsultUser = document.getElementById('id_select_nom').value;
			//alert(idConsultUser);
			var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhr.open("GET", "importer_fiche.php?dateToImport="+dateToImport+"&idConsultUser="+idConsultUser, true);
			xhr.onreadystatechange = function() 
			{ 
				if(xhr.readyState == 4)
				{ 
					//alert(xhr.responseText);
					decouperChaine(xhr);
				}
				//alert(xhr.readyState); 
			} 

			xhr.send(null);	
	//data = "date="+escape(l1.options[index].value);
		//alert("toto");
}

function decouperChaine(xhr)
{
	// On transforme l'objet recu en chaine de caractères..
	xhrText=xhr.responseText;

	// On decoupe cette chaine de caractères d'abord pour avoir les differentes periodes.
	periodes=xhrText.split("--");

	//alert(periodes);
	for (i in periodes)
	{
		// la boucle for commence à 0. Ce if permet de l'exclure.
		if (i != 0)	
		{	
			// On decoupe ensuite chaque champs
			colonne=periodes[i].split(";");


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

			idHoraires		=	donnees[0];
			date			=	donnees[1];
			horaireDebut		=	donnees[2];
			horaireFin		=	donnees[3];
			totalHoraire		=	donnees[4];
			idUtilisateur		=	donnees[5];
			loginUtilisateur	=	donnees[6];
			idSection		=	donnees[7];
			idAxe1			=	donnees[8];
			nomAxe1			=	donnees[9];
			idAxe2			=	donnees[10];
			nomAxe2			=	donnees[11];
			idAxe3		=	donnees[12];
			nomAxe3		=	donnees[13];
			numeroLigne		=	donnees[14];


			renseignerPage(idHoraires, date, horaireDebut, horaireFin, totalHoraire, idUtilisateur, loginUtilisateur, idSection, idAxe1, nomAxe1, idAxe2, nomAxe2, idAxe3, nomAxe3, numeroLigne);
		}
	}
}

function importer_heures_ajax(dateToImport)
{
	dateToImport=dateToImport;
		//alert("debut test");
			var idConsultUser = document.getElementById('id_select_nom').value;
			var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhr.open("GET", "importer_dates.php?dateToImport="+dateToImport+"&idConsultUser="+idConsultUser, true);
			xhr.onreadystatechange = function() 
			{ 
				if(xhr.readyState == 4)
				{ 
					//alert(xhr.responseText);
					decouperChaineImportDates(xhr);
				}
				//alert(xhr.readyState); 
			} 

			xhr.send(null);	
	//data = "date="+escape(l1.options[index].value);
		//alert("toto");
}

function decouperChaineImportDates(xhr)
{
	// On transforme l'objet recu en chaine de caractères..
	xhrText=xhr.responseText;
	//alert(xhrText);

	// On decoupe cette chaine de caractères d'abord pour avoir les differentes dates.
	heures=xhrText.split(";");
	//alert(heures);



	//// Nous allons gérer les variables "colonne = xxx" en les transformant en "xxx".
	// On cree le tableau donneesHeures dans lequel on "pushera" ensuite nos donneesHeures.
	donneesHeures		=	new Array ();
		
	// On "push" ensuite la 2eme colonne de notre réponse (donc la valeur de la variable) dans notre tableau "donneesHeures".
	donneesHeures.push(heures[0]);
	donneesHeures.push(heures[1]);	
	donneesHeures.push(heures[2]);
	donneesHeures.push(heures[3]);
	donneesHeures.push(heures[4]);
	donneesHeures.push(heures[5]);
	donneesHeures.push(heures[6]);
	donneesHeures.push(heures[7]);
	donneesHeures.push(heures[8]);
	donneesHeures.push(heures[9]);


	jour					=	donneesHeures[0];
	semaine					=	donneesHeures[1];
	mois					=	donneesHeures[2];
	annee					=	donneesHeures[3];
	heuresContrat				=	donneesHeures[4];
	heuresSemaineDerniere			=	donneesHeures[5];
	heuresSup				=	donneesHeures[6];
	heuresRachetees				=	donneesHeures[7];
	congeRestant 				=	donneesHeures[8];
	rttRestant 				=	donneesHeures[9];

	//alert(semaine);
	//alert(mois);
	//alert(annee);
	//alert(heuresContrat);
	//alert(heuresSemaineDerniere);

	renseignerSemaineMoisAnnee(semaine, mois, annee, heuresContrat, heuresSemaineDerniere, heuresSup, heuresRachetees, congeRestant, rttRestant);

}
function renseignerSemaineMoisAnnee(semaine, mois, annee, heuresContrat, heuresSemaineDerniere, heuresSup, heuresRachetees, congeRestant, rttRestant)
{
	if (heuresRachetees != 0)
	{
		heuresSup = eval(parseFloat(heuresSup)-parseFloat(heuresRachetees)).toFixed(2); 
	}

	document.getElementById('total_semaine').value	= semaine;
	document.getElementById('total_mois').value	= mois;
	document.getElementById('total_annuel').value = annee;
	document.getElementById('heures_contrat').value	= heuresContrat;
	document.getElementById('heures_sup').value	= heuresSup;
	var heuresTotaux=eval(parseFloat(heuresSup)+parseFloat(semaine)).toFixed(2);
	document.getElementById('heures_totaux').value	= heuresTotaux;
	document.getElementById('total_jours_conges_restants').value	= congeRestant;
	document.getElementById('total_jours_rtt_restants').value		= rttRestant;

}

function renseignerPage(idHoraires, date, horaireDebut, horaireFin, totalHoraire, idUtilisateur, loginUtilisateur, idSection, idAxe1, nomAxe1, idAxe2, nomAxe2, idAxe3, nomAxe3, numeroLigne)
{
	// On gère le matin et l'am. Si numeroLigne < 6 c'est le matin, sinon c'est l'am. Voir pour inclure la valeure réelle du nombre de ligne.
	if ( (numeroLigne <= 6) && (idHoraires != "") ) quand = "Matin";
	if ( (numeroLigne > 6) && (idHoraires != "") ) quand = "Aprem";	
	//else quand = "Aprem";

	// On rempli les zones texte et on les colore
	couleur="#D2D2D2"

	document.getElementById('de'+quand+'_periode'+numeroLigne).value	= horaireDebut;
	document.getElementById('de'+quand+'_periode'+numeroLigne).style.backgroundColor = couleur;

	document.getElementById('a'+quand+'_periode'+numeroLigne).value		= horaireFin;
	document.getElementById('a'+quand+'_periode'+numeroLigne).style.backgroundColor = couleur;

	document.getElementById('choix_axe1_periode'+numeroLigne).value		= nomAxe1;
	document.getElementById('choix_axe1_periode'+numeroLigne).style.backgroundColor = couleur;
	document.getElementById('id_choix_axe1_periode'+numeroLigne).value	= idAxe1;

	document.getElementById('choix_axe2_periode'+numeroLigne).value		= nomAxe2;
	document.getElementById('choix_axe2_periode'+numeroLigne).style.backgroundColor = couleur;
	document.getElementById('id_choix_axe2_periode'+numeroLigne).value	= idAxe2;

	document.getElementById('choix_axe3_periode'+numeroLigne).value	= nomAxe3;
	document.getElementById('choix_axe3_periode'+numeroLigne).style.backgroundColor = couleur;
	document.getElementById('id_choix_axe3_periode'+numeroLigne).value	= idAxe3;

	document.getElementById('total'+quand+'_periode'+numeroLigne).value	= totalHoraire;

	document.getElementById('id_horaire_periode'+numeroLigne).value	= idHoraires;

	calcul_total_journee();
}


function calcul_periode(nomDe, noma, total)
{
	//On recupere la valeur rentree dans les champs texts deMatinx et aMatinx. On les place dans les variables "debut" et "fin".
	debut=document.getElementById(nomDe).innerHTML =document.getElementById(nomDe).value
	fin=document.getElementById(noma).innerHTML =document.getElementById(noma).value

	// On calcul la difference entre fin et debut.
	total_periode=Number(fin)-Number(debut) ;
	// On le renseigne dans le champ "total periode".
	document.getElementById(total).value	=	total_periode.toFixed(2);
	verif_periode();
	calcul_total_journee();
}

function affichage_popup(nom_de_la_page, nom_interne_de_la_fenetre) // Permet d'ouvrir les fenetres popup
{
	// Permet d'ouvrir les fenetres popup
	var idConsultUser = document.getElementById('id_select_nom').value;
	//alert(idConsultUser);
	//xhr.open("GET", "importer_fiche.php?dateToImport="+dateToImport+"&idConsultUser="+idConsultUser, true);

	window.open (nom_de_la_page+"?idConsultUser="+idConsultUser, nom_interne_de_la_fenetre, config='height=900, width=1250, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no');
}

function ouvrir_graph(nom_de_la_page, nom_interne_de_la_fenetre)
{
	var dateFormatMysql= $("#AltFieldDateMysql").val();
	
	var idConsultUser = document.getElementById('id_select_nom').value;

	
	// Permet d'ouvrir les fenetres popup graph
	window.open (nom_de_la_page+"?date="+dateFormatMysql+"&idConsultUser="+idConsultUser, nom_interne_de_la_fenetre, config='height=900, width=1250, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no');
}


function nombre_lignes_a_additionner(rep)
{
	repetitions = rep;
}

function calcul_total_journee()
{
	// On met à jour le total de la journée.
	somme=0;
	for (j = 1 ; j <= Number(repetitions) ; j++)
	{
		// Ces deux "if" nous permettent de prendre en compte le fait que la moitié de repetition est dans matin et l'autre dans apres midi.
		// La premiere moitié renvoi les valeurs vers totalMatin_periodex et l'autre vers totalAprem_periodex.
		if (j <= Number(repetitions)/2)
		{
			somme=Number(somme) + Number(document.getElementById('totalMatin_periode'+j).value);
		}
		if (j > Number(repetitions)/2)
		{
			somme=Number(somme) + Number(document.getElementById('totalAprem_periode'+j).value);
		}
	document.getElementById('total_journee').value=somme.toFixed(2);
	}
}

function retourFiche()
{
	location.href="administration.php";
}

function demarrage()
{
	change_couleur_bouton_menu_general();
}
/////////////////////////////////////////////////////////
// 	FIN DES JAVASCRIPT 			       //
/////////////////////////////////////////////////////////
</script>
</head>

<body onLoad="demarrage();">
<?php include("connexion_base.php"); ?>

<?php
	$prenom = $_SESSION['prenom'];
	if (!$prenom)
	{
		echo "VOUS N'ETES PAS CONNECTE ";
		echo "...";
	} 

// Permet de choisir la personne dont on veut consulter la fiche
function choixUtilisateur()
{
	global $bdd;

	echo	'
			<select name="select_nom" id="id_select_nom" onChange="javascript:rafraichir_page();">
				<option value="null"></option>
		';
	
	$sql_utilisateurs	=	"SELECT * from Utilisateurs where active=1 ORDER BY Nom";
	echo "$sql_utilisateurs";
	try
	{
		$reponse_utilisateurs = $bdd->query($sql_utilisateurs) or die('Erreur SQL !<br>' .$sql_utilisateurs. '<br>'. mysql_error());
	}
	catch(Exception $e)
	{
		// En cas d'erreur précédemment, on affiche un message et on arrête tout
		die('Erreur : '.$e->getMessage());
	}

	while ($donnees = $reponse_utilisateurs->fetch())
	{

		$idUtilisateur		=	$donnees['idUtilisateurs'];		
		$nom			=	$donnees['nom'];
		$prenom			=	$donnees['prenom'];
		$nbrHeuresSemaine	=	$donnees['nbrHeuresSemaine'];

	echo	"<option value=$idUtilisateur>$prenom $nom</option>";

	}
	$reponse_utilisateurs->closeCursor(); // Termine le traitement de la requête

	echo '</select>';
}

?>

<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id=tableGlobale><tr ><td></td><td id="tableGlobale">



<?php include("menu_general.php") ?>

<table border=0 width=100% cellpadding="0px" cellspacing="0px">
	<tr>
		<td align=left witdh=18%>
			<table>
				<tr>
					<td align=center colspan=2>
						<br>
						<!-- Test suivant -->
						<input type="button" id="hier"  name="hier" value="Précédent" onClick="javascript:hier()">
						<input type="button" id="demain"  name="demain" value="Suivant" onClick="javascript:demain()"><br>	
					</td>
				</tr>
				<tr>
					<td>
						Date:
					</td>
					<td align=center>
<!-- <form name="valider_fiche" method="post" onsubmit="return verifier_fiche();" action="valider_fiche.php" target="_blank"> -->
						<!-- Calendrier -->
						<input type="text" id="datepicker" name="date" />
						<!-- Champ caché permettant de mettre la date en "altfield" pour l avoir au format MYSQL (voir fonction altfiel et altformat dans la declaration du calendrier) -->
						<input type="HIDDEN" id="AltFieldDateMysql" name="AltFieldDateMysql" />
					</td>
				</tr>
			</table>

		</td>
		<td align=center >
			<br>
			Vous souhaitez consulter la fiche de:<br>
			<?php choixUtilisateur(); ?>
			<br>
			<font color=red size=3>	<br>ATTENTION! 
							<br>Vous êtes entrain de consulter la fiche d'heure d'une autre personne.
							<br>Vous n'êtes pas sur votre propre fiche.
			</font>
		</td>

		<td align=center width=29%>
			<br>
		</td>
	</tr>
</table>

<!-- Inserer le tableau de selection des heures -->
<?php 

include("connexion_base.php"); 
include("importer_configuration.php");
include("table_heures.php");

?>
<br>

<table width=90% border=0>
	<tr align=left>
		<td width=11% align=center>
			Total de la journée:
		</td>

		<td width=17% align=left>
			<!--Il est renseigne par la fonction javascript calcul_total_journee() -->
		 	<input type="numeric" step="any" id="total_journee" name="total_journee" onClick=javascript:ouvrir_graph('graph_heures_sup_jours.php','graph_heures_sup_jours') readonly /> heures.
		</td>

		<td width=25% align=center>
			Il vous reste :

		</td>

		<td width=37% align=right>
			<!-- Input submit permettant de valider le formulaire -->
			<!-- <input type="submit" name="valider" id="valider" value="Valider" /> -->
		</td>

<!--		Truc de malade: Avec la td en dessous, impossible d'aligner à droite.-->
<!--		<td width=35% align=right>-->
<!--			<!-- Input submit permettant de valider le formulaire -->
<!--			<input type="submit" name="valider" id="valider" value="valider" />-->
<!--		</td>-->

	</tr>

	<tr>
		<td align=center>__________
		</td>

		<td>
		</td>

		<td align=center>
			<table border=0 width=100%><tr>
				<td width=45%>
				</td>
				<td>
					<input type="numeric" step="any" id="total_jours_conges_restants" name="total_jours_conges_restants" readonly/> jour(s) de congé.
				</td>
			</tr></table>
		</td>		

		<td>
		</td>
	</tr>

	<tr align=left>
		<td align=center>
		Total de la semaine:
		</td>
		<td align=left>
			<input type="numeric" step="any" id="total_semaine" name="total_semaine" onClick=javascript:ouvrir_graph('graph_heures_sup_semaine.php','graph_heures_sup_semaine.php') readonly/> heures sur <input type="numeric" step="any" id="heures_contrat" name="heures_contrat" readonly />h.
		</td>

		<td align=center>
			<table border=0 width=100%><tr>
				<td width=45%>
				</td>
				<td>
			<input type="numeric" step="any" id="total_jours_rtt_restants" name="total_jours_rtt_restants" readonly/> jour(s) de RTT.
				</td>
			</tr>
			</table>
		</td>


		<td>
		</td>
	</tr>

	<tr>
		<td align=right>
			+
		</td>
		<td>
			<input type="numeric" step="any" id="heures_sup" name="heures_sup" readonly/> heure(s) supplémentaire(s). 
		</td>

		<td>
		</td>
	</tr>

	<tr>
		<td align=right>
			=
		</td>
		<td>
			<input type="numeric" step="any" id="heures_totaux" name="heures_totaux" readonly/> heure(s) totale(s). 
		</td>

		<td>
		</td>
	</tr>

	<tr>
		<td align=center>__________
		</td>

		<td>
		</td>

		<td>
		</td>
	</tr>
	
	<tr align=left>
		<td align=center>
			Total du mois:

			<?php 
				if ( $afficherTotalAnnuel == "checked" )
				{
					echo '
					<br>__________
					<br>
					<br>Total annuel:';
				}
				else
				{
					
				}
			?>
		</td>

		<td align=left>
			<input type="numeric" step="any" id="total_mois" name="total_mois" readonly/> heures.

			<?php 
				if ( $afficherTotalAnnuel == "checked" )
				{
					echo '
					<br>
					<br>
					<br><input type="numeric" step="any" size="200" id="total_annuel" name="total_annuel" readonly/> heures.';
				}
				else
				{
					echo '
					<br>
					<br>
					<br><input type="hidden" id="total_annuel" name="total_annuel" readonly/>';
				}
			?>
		</td>
		
		<td>
		</td>

		<td align=right>
			<table CELLPADDING=5 border=0>	
						<tr>
							<td>Semaine n°<input type="text" id="numero_semaine"	value="" 	readonly /></td>
							<!-- onfocus="this.blur()" permet de cacher le curseur clignotant. -->
							<td><input type="text" id="valid_semaine_lundi" 		value="Lun" 	readonly onClick=javascript:aller_jour_semaine(0) onfocus="this.blur()" /></td>
							<td><input type="text" id="valid_semaine_mardi" 		value="Mar" 	readonly onClick=javascript:aller_jour_semaine(1) onfocus="this.blur()" /></td>
							<td><input type="text" id="valid_semaine_mercredi" 	value="Mer" 	readonly onClick=javascript:aller_jour_semaine(2) onfocus="this.blur()" /></td>
							<td><input type="text" id="valid_semaine_jeudi" 		value="Jeu" 	readonly onClick=javascript:aller_jour_semaine(3) onfocus="this.blur()" /></td>
							<td><input type="text" id="valid_semaine_vendredi" 	value="Ven" 	readonly onClick=javascript:aller_jour_semaine(4) onfocus="this.blur()" /></td>
						</tr>
						<tr align=right>
							<td colspan=6> <input type="button" id="jour_non_valide"  name="jour_non_valide" value="   <   Se rendre au plus vieux jour non valide." onClick="javascript:AllerJourNonValide()"> </td>
						</tr>
			</table>
		</td>
	</tr>
</table>
<table width=100% border=0>
	<tr>
		<td align="center" >
			<img src="horloge_centiemes.png"  /> 
		</td>
	<tr>
</table>

<!-- Fin de la table de mise en page globale -->
</td><td></td></tr></table>
</form>
</body>
</html>

