<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

<?php session_start(); ?>

<HEAD>

<?php // Permet de rediriger vers l'acceuil si utilisateur non enregistré.
	$prenom = $_SESSION['prenom'];
	if (!$prenom)
	{
		header('Location: index.php'); 
	}
?>

<TITLE>Linott: Déclaration d'heures.</TITLE>
	
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
										razFormulaire();
										importer_fiche_ajax(vaDate);
										importer_heures_ajax(vaDate);
										validation_semaine(vaDate);						  
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

function demarrage() // Fonction lancée au démarrage ou au rafraichissement de la page.
{
	change_couleur_bouton_menu_general();
	var dateFormatMysql= $("#AltFieldDateMysql").val();
	razFormulaire();
	importer_fiche_ajax(dateFormatMysql);
	importer_heures_ajax(dateFormatMysql);
	validation_semaine(dateFormatMysql);

	disparition_bouton_creer_jour_type();
	disparition_bouton_supprimer_jour_type();
}

function hier() // Fonction lancée lors du clic sur "précédent" du calendrier.
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

function demain() // Fonction lancée lors du clic sur "suivant" du calendrier.
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

		var xhr_jour_non_valide = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhr_jour_non_valide.open("GET", "trouve_jour_non_valide.php?dateToImport="+dateFormatMysql, true);
		xhr_jour_non_valide.onreadystatechange = function() 
		{ 
			if(xhr_jour_non_valide.readyState == 4)
			{ 
				//alert(xhr_jour_non_valide.responseText);
				
				xhrText=xhr_jour_non_valide.responseText;
				
				
				dateFormatMysql=xhrText;
				if (dateFormatMysql == "")
				{
				    alert("Il semble que tous les jours précédents la date d'aujourd'hui soient correctement remplis.");
				    return null;
				}

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

function rafraichir_page(deQui) // Permet de mettre à jour la page quand on change de date. On supprime tout et on importe les données du nouveau jour courant.
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
}

function razFormulaire() // Permet de vider le formulaire.
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

			document.getElementById('total_journee_instant').value="0";

	}
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

function validation_semaine(dateToImport) // Permet le petit visuel rouge et vert de la semaine (rouge = jour non valide, vert = jour valide)
{
	dateToImport=dateToImport;
	//alert(dateToImport);
		//idUtilisateur=1;
		var xhr_valide_semaine = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhr_valide_semaine.open("GET", "validation_semaine.php?dateToImport="+dateToImport, true);
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

function decouperChaineValidationSemaine(xhr, dateToImport) // Met en forme les données recues de validation_semaine.php quand on lance la fonction javascript validation_semaine.
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

function importer_fiche_ajax(dateToImport) // Permet d'importer la fiche du jour courant.
{
	dateToImport=dateToImport;
			//idUtilisateur=1;
			var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhr.open("GET", "importer_fiche.php?dateToImport="+dateToImport, true);
			xhr.onreadystatechange = function() 
			{ 
				if(xhr.readyState == 4)
				{ 
					//alert(xhr.responseText);
					decouperChaineImporterFiche(xhr);
				}
				//alert(xhr.readyState); 
			} 

			xhr.send(null);	
	//data = "date="+escape(l1.options[index].value);
}
function decouperChaineImporterFiche(xhr) // Met en forme les données récupèrées de importer_fiche.php pour la fonction javascript importer_fiche_ajax
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

function importer_heures_ajax(dateToImport) // Permet d'importer une date et d'y récupèrer les valeurs correspondantes en heures sup', heures semaine, heures mois, heures jour courant, etc.
{
	dateToImport=dateToImport;
		//alert("debut test");
			var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhr.open("GET", "importer_dates.php?dateToImport="+dateToImport, true);
			xhr.onreadystatechange = function() 
			{ 
				if(xhr.readyState == 4)
				{ 
					//alert(xhr.responseText);
					decouperChaineImporterHeures(xhr);
				}
				//alert(xhr.readyState); 
			} 

			xhr.send(null);	
	//data = "date="+escape(l1.options[index].value);
		//alert("toto");
}
function decouperChaineImporterHeures(xhr) // Met en forme les heures importées pour la fonction javascript importer_heures_ajax
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

	//alert(jour);
	//alert(semaine);
	//alert(mois);
	//alert(annee);
	//alert(heuresContrat);
	//alert(heuresSemaineDerniere);

	renseignerSemaineMoisAnnee(jour, semaine, mois, annee, heuresContrat, heuresSemaineDerniere, heuresSup, heuresRachetees, congeRestant, rttRestant);
}

function renseignerSemaineMoisAnnee(jour, semaine, mois, annee, heuresContrat, heuresSemaineDerniere, heuresSup, heuresRachetees, congeRestant, rttRestant) // Renseigne les totaux (journée, semaine et mois)
{
	if (heuresRachetees != 0)
	{
		heuresSup = eval(parseFloat(heuresSup)-parseFloat(heuresRachetees)).toFixed(2); 
	}
	document.getElementById('total_journee').value			= jour;
	document.getElementById('total_semaine').value			= semaine;
	document.getElementById('total_mois').value 			= mois;
	document.getElementById('total_annuel').value 			= annee;
	document.getElementById('heures_contrat').value			= heuresContrat;
	document.getElementById('heures_sup').value			= heuresSup;
	var heuresTotaux 						= eval(parseFloat(heuresSup)+parseFloat(semaine)).toFixed(2);
	document.getElementById('heures_totaux').value			= heuresTotaux;
	document.getElementById('total_jours_conges_restants').value	= congeRestant;
	document.getElementById('total_jours_rtt_restants').value	= rttRestant;

}

function renseignerPage(idHoraires, date, horaireDebut, horaireFin, totalHoraire, idUtilisateur, loginUtilisateur, idSection, idAxe1, nomAxe1, idAxe2, nomAxe2, idAxe3, nomAxe3, numeroLigne) // renseigne la page si il y a des données en base correspondants au jours en cours.
{
	// On gère le matin et l'am. Si numeroLigne < 6 c'est le matin, sinon c'est l'am. Voir pour inclure la valeure réelle du nombre de ligne.
	if ( (numeroLigne <= 6) && (idHoraires != "") )
	{
	  quand = "Matin";
	  couleur = "#B0DD8D";
	}
	if ( (numeroLigne > 6) && (idHoraires != "") )
	{
	  quand = "Aprem";
	  couleur = "#9BD6F9";
	}
	//else quand = "Aprem";

	// On rempli les zones texte et on les colore
	//couleur="#D2D2D2"

	document.getElementById('de'+quand+'_periode'+numeroLigne).value	= horaireDebut;
	document.getElementById('de'+quand+'_periode'+numeroLigne).style.backgroundColor = couleur;
	document.getElementById('de'+quand+'_periode'+numeroLigne).style.border = couleur;

	document.getElementById('a'+quand+'_periode'+numeroLigne).value		= horaireFin;
	document.getElementById('a'+quand+'_periode'+numeroLigne).style.backgroundColor = couleur;
	document.getElementById('a'+quand+'_periode'+numeroLigne).style.border = couleur;

	document.getElementById('choix_axe1_periode'+numeroLigne).value		= nomAxe1;
	document.getElementById('choix_axe1_periode'+numeroLigne).style.backgroundColor = couleur;
	document.getElementById('choix_axe1_periode'+numeroLigne).style.border = couleur;
	document.getElementById('id_choix_axe1_periode'+numeroLigne).value	= idAxe1;

	document.getElementById('choix_axe2_periode'+numeroLigne).value		= nomAxe2;
	document.getElementById('choix_axe2_periode'+numeroLigne).style.backgroundColor = couleur;
	document.getElementById('choix_axe2_periode'+numeroLigne).style.border = couleur;
	document.getElementById('id_choix_axe2_periode'+numeroLigne).value	= idAxe2;

	document.getElementById('choix_axe3_periode'+numeroLigne).value	= nomAxe3;
	document.getElementById('choix_axe3_periode'+numeroLigne).style.backgroundColor = couleur;
	document.getElementById('choix_axe3_periode'+numeroLigne).style.border = couleur;
	document.getElementById('id_choix_axe3_periode'+numeroLigne).value	= idAxe3;

	document.getElementById('total'+quand+'_periode'+numeroLigne).value	= totalHoraire;

	document.getElementById('id_horaire_periode'+numeroLigne).value	= idHoraires;

	calcul_total_journee();
}

function lancerStats(bouton) // Lancement des actions de stats (récapitulatifs) selon le bouton cliqué
{
	if (bouton == "absences")
	{
		location.href="statsConges.php";
	}
	if (bouton == "axes")
	{
		location.href="statsAxes.php";
	}
}

function affichage_popup(axe, periode, nom_interne_de_la_fenetre) // Permet d'ouvrir les fenetres popup
{	

	//alert(document.getElementById('id_choix_axe1_periode'+periode).value);
	// On va chercher si il y a une valeur dans axe1 et axe2, et on les renseigne.
	// id_choix_axe1_periode$x
	// Ancienne version en php : fenetre_choix_axe1.php?periode=<?php echo "$periode"?>
	// Nouvelle version en javascript : 'axe2','<?php echo "$periode"?>','popup_axe2_periodes'
	axe1Selectionne=document.getElementById('id_choix_axe1_periode'+periode).value;
	axe2Selectionne=document.getElementById('id_choix_axe2_periode'+periode).value;
	if ( axe1Selectionne == "" )
	{ 
		axe1Selectionne="NULL";
	}
	if ( axe2Selectionne == "" )
	{ 
		axe2Selectionne="NULL";
	}

	nom_de_la_page="fenetre_choix_"+axe+".php?periode="+periode+"&axe1Selectionne="+axe1Selectionne+"&axe2Selectionne="+axe2Selectionne ;
	//alert(nom_de_la_page);

	// Permet d'ouvrir les fenetres popup
	window.open (nom_de_la_page, nom_interne_de_la_fenetre, config='height=800, width=900, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no');
}

function ouvrir_graph(nom_de_la_page, nom_interne_de_la_fenetre)
{
	var dateFormatMysql= $("#AltFieldDateMysql").val();
	
	// Permet d'ouvrir les fenetres popup graph
	window.open (nom_de_la_page+"?date="+dateFormatMysql, nom_interne_de_la_fenetre, config='height=900, width=1250, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no');
}


function calcul_periode(nomDe, noma, total) // Calcul temps reel de la somme d'heure sur la periode en cours de remplissage
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

function verif_periode() // Toute la vérification nécéssaire avant validation
{
	for (i=1 ; i <= Number(repetitions) ; i++)
	{
		if (i <= Number(repetitions)/2)
		{
			if ( (document.forms[0].elements['deMatin_periode'+i].value >= 24) || (document.forms[0].elements['deMatin_periode'+i].value < 0) || (isNaN(document.forms[0].elements['deMatin_periode'+i].value) == true) )
			{
				document.getElementById('deMatin_periode'+i).style.backgroundColor = "red";
				alert ("Matin "+i+", l'heure de début doit être un nombre supérieure ou égale à 0 et inférieure à 24 (0<=x<24) ");
				document.forms[0].elements['deMatin_periode'+i].value = "";
				return false;	
			}
			if ( (document.forms[0].elements['aMatin_periode'+i].value >= 24) || (document.forms[0].elements['aMatin_periode'+i].value < 0) || (isNaN(document.forms[0].elements['aMatin_periode'+i].value) == true)  )
			{
				document.getElementById('aMatin_periode'+i).style.backgroundColor = "red";
				alert ("Matin "+i+", l'heure de fin doit être un nombre supérieure ou égale à 0 et inférieure à 24 (0<=x<24) ");
				document.forms[0].elements['aMatin_periode'+i].value = "";
				return false;		
			}
		}
		if (i > Number(repetitions)/2)
		{
			if ( (document.forms[0].elements['deAprem_periode'+i].value >= 24) || (document.forms[0].elements['deAprem_periode'+i].value < 0)  || (isNaN(document.forms[0].elements['deAprem_periode'+i].value) == true) )
			{
				document.getElementById('deAprem_periode'+i).style.backgroundColor = "red";
				alert ("Aprem "+i+", l'heure de début doit être supérieure ou égale à 0 et inférieure à 24 (0<=x<24) ");
				document.forms[0].elements['deAprem_periode'+i].value = "";
				return false;	
			}
			if ( (document.forms[0].elements['aAprem_periode'+i].value >= 24) || (document.forms[0].elements['aAprem_periode'+i].value < 0) || (isNaN(document.forms[0].elements['aAprem_periode'+i].value) == true)  )
			{
				document.getElementById('aAprem_periode'+i).style.backgroundColor = "red";
				alert ("Aprem "+i+", l'heure de fin doit être supérieure ou égale à 0 et inférieure à 24 (0<=x<24) ");
				document.forms[0].elements['aAprem_periode'+i].value = "";
				return false;		
			}

		}
	}
}

function nombre_lignes_a_additionner(rep) // Récupère le nombre de répétion pour connaitre le nombre de lignes.

{
	// Récupère le nombre de répétion pour connaitre le nombre de lignes.
	repetitions = rep;
}

function calcul_total_journee() // Calcul le total de la journée selon les périodes remplies.
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
	document.getElementById('total_journee_instant').value=somme.toFixed(2);
	}
}

function conge_coche(deQui) // Traitement si le bouton absence est coché et traitement du menu déroulant correspondant.
{
	if (document.getElementById('absent').checked == true)
	{



		//alert(deQui.value);
		couleur			=	"green";
		heuresContrat		=	document.getElementById('heures_contrat').value;
		heuresDemiJournee 	= 	Math.round(parseFloat(heuresContrat*100))/1000;
		heureDebutMatin 	= 	8;
		heureFinMatin		=	heureDebutMatin+heuresDemiJournee;
		heureDebutAprem		=	14;
		heureFinAprem		=	heureDebutAprem+heuresDemiJournee;
		absenceAxe1		=	"Congés et autres absences";
		absenceIdAxe1		=	document.getElementById('id_axe1_code_99').value;
		absenceIdAxe3		=	1;

		if (deQui.value != 'absent')
		{
			absenceAxe2		=	deQui.options[deQui.selectedIndex].text;
			absenceIdAxe2		=	document.getElementById('type_absence').value;
		}
		if (deQui.value == 'absent')
		{
			absenceIdAxe2		=	document.getElementById('id_axe2_code_9900').value;
			absenceAxe2		=	"Absences";
		}
		absenceAxe3		=	" Projet non défini.";

		//alert(absenceAxe1);
		//alert(document.getElementById('id_axe1_code_99'));
		//alert(absenceIdAxe1);
		//alert(absenceAxe2);
		//alert(absenceIdAxe2);

		document.getElementById('deMatin_periode1').value			= heureDebutMatin;
		document.getElementById('aMatin_periode1').value			= heureFinMatin;
		document.getElementById('choix_axe1_periode1').value			= absenceAxe1;
		document.getElementById('id_choix_axe1_periode1').value			= absenceIdAxe1;
		document.getElementById('choix_axe2_periode1').value			= absenceAxe2;
		document.getElementById('id_choix_axe2_periode1').value			= absenceIdAxe2;
		document.getElementById('choix_axe3_periode1').value			= absenceAxe3;
		document.getElementById('id_choix_axe3_periode1').value			= absenceIdAxe3;

		calcul_periode('deMatin_periode1','aMatin_periode1','totalMatin_periode1');


		document.getElementById('deAprem_periode7').value			= heureDebutAprem;
		document.getElementById('aAprem_periode7').value			= heureFinAprem;
		document.getElementById('choix_axe1_periode7').value			= absenceAxe1;
		document.getElementById('id_choix_axe1_periode7').value			= absenceIdAxe1;
		document.getElementById('choix_axe2_periode7').value			= absenceAxe2;
		document.getElementById('id_choix_axe2_periode7').value			= absenceIdAxe2;
		document.getElementById('choix_axe3_periode7').value			= absenceAxe3;
		document.getElementById('id_choix_axe3_periode7').value			= absenceIdAxe3;

		calcul_periode('deAprem_periode7','aAprem_periode7','totalAprem_periode7');


		document.getElementById('deMatin_periode1').style.backgroundColor 	= couleur;
		document.getElementById('aMatin_periode1').style.backgroundColor 	= couleur;
		document.getElementById('choix_axe1_periode1').style.backgroundColor 	= couleur;
		//document.getElementById('choix_axe2_periode1').style.backgroundColor	= couleur;
		document.getElementById('choix_axe3_periode1').style.backgroundColor 	= couleur;

		document.getElementById('deAprem_periode7').style.backgroundColor 	= couleur;
		document.getElementById('aAprem_periode7').style.backgroundColor 	= couleur;
		document.getElementById('choix_axe1_periode7').style.backgroundColor 	= couleur;
		//document.getElementById('choix_axe2_periode7').style.backgroundColor 	= couleur;
		document.getElementById('choix_axe3_periode7').style.backgroundColor 	= couleur;

		
		calcul_total_journee();

		if (deQui.value == 'absent')
		{
		alert("Insertion automatique des données d'absence en fonction de votre contrat.\n\nATTENTION! Pensez à préciser votre type d'absence et validez la fiche si cela vous convient.\n\nDécochez cette case pour annuler cette action.");
		}

	}
	else
	{
		rafraichir_page();
	}
}

function test() // Fonction me permettant de faire des tests sans avoir à toujours en recreer une.
{
	var dateFormatMysql= $("#AltFieldDateMysql").val();
	alert (dateFormatMysql);
}

function supprimer_periode(periode) // Permet de supprimer une période (ligne d'heure) quand on clique sur le total d'heure de cette période.
{
	idPeriode=document.getElementById('id_horaire_periode'+periode).value

	if (idPeriode != "")
	{
		if (confirm("Voulez vous vraiment supprimer la période "+periode+" (id:"+idPeriode+")")) 
		{
			var xhrSuppr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhrSuppr.open("GET", "supprimer_periode.php?id_periode_a_suppr="+idPeriode, true);
			xhrSuppr.onreadystatechange = function() 
			{ 
				if(xhrSuppr.readyState == 4)
				{ 
					//alert(xhrSuppr.responseText);
					//alert('La periode '+periode+' a bien été supprimée.');
				}
				//alert(xhr.readyState); 
			} 

			xhrSuppr.send(null);
			rafraichir_page("xhrSuppr");


	////////////////// Mise à jour des heures sup' quand on supprime une fiche. //////////////////////////
			var dateFormatMysql= $("#AltFieldDateMysql").val();
			// Si je n'ai pas cette alerte, je me retrouve avec une valeur à 0 pour totalJournee.
			alert("Prise en compte de cette modification dans vos heures supplémentaires.");
			var totalJournee= document.getElementById('total_journee_instant').value;
			var xhrMajHeuresSup = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhrMajHeuresSup.open("GET", "gestion_heures_sup.php?from=xhrMajHeuresSup&date="+dateFormatMysql+"&total_journee="+totalJournee, true);
			xhrMajHeuresSup.onreadystatechange = function() 
			{ 
				if(xhrMajHeuresSup.readyState == 4)
				{ 
					//alert(xhrMajHeuresSup.responseText);
					//alert('La periode '+periode+' a bien été supprimée.');
				}
				//alert(xhr.readyState); 
			} 
			xhrMajHeuresSup.send(null);
		}
		else 
		{
			alert("Suppression annulée.")
		}
	}
	else 
	{
		// alert("Aucune période n'est renseignée sous cet ID en base. Vous cherchez peut être à supprimer une période que vous n'avez pas encore validé.");
		document.getElementById('choix_axe1_periode'+periode).value = "Choisir axe 1" ;
		document.getElementById('id_choix_axe1_periode'+periode).value = "" ;

		document.getElementById('choix_axe2_periode'+periode).value = "Choisir axe 2" ;
		document.getElementById('id_choix_axe2_periode'+periode).value = "" ;
	}
}

function verifier_fiche() // Permet de mettre en forme les retours de la fiche et de vérifier que tout est correctement renseigné.
{	
	// alert(document.getElementById('choix_axe3_periode1').type);
	ok=0;
	erreur=0;
	i=1;
	for (i ; i <= Number(repetitions) ; i++)
	{
		if (i <= Number(repetitions)/2)
		{
		//alert ('i = '+i+', repetitions = '+repetitions+'.');
			// Matin: Si il y a une heure de début, une heure de fin, un axe1 ou un axe2, on considere que la periode doit etre entierement renseignée.
			if ( (document.forms[0].elements['deMatin_periode'+i].value != "") 
			|| (document.forms[0].elements['aMatin_periode'+i].value != "") 
			|| (document.forms[0].elements['choix_axe1_periode'+i].value != "Choisir axe 1") 
			|| (document.forms[0].elements['choix_axe2_periode'+i].value != "Choisir axe 2") 
			|| (document.forms[0].elements['choix_axe3_periode'+i].value != "Choisir axe 3") 
			   )
			{

				if (document.forms[0].elements['deMatin_periode'+i].value == "")
				{
					document.getElementById('deMatin_periode'+i).style.backgroundColor = "red";
					document.getElementById('deMatin_periode'+i).focus();
					alert ("Vous devez renseigner une heure de début pour la periode matin "+i);
					erreur++;
					return false;
				}
				if (document.forms[0].elements['aMatin_periode'+i].value == "")
				{
					document.getElementById('aMatin_periode'+i).style.backgroundColor = "red";
					document.getElementById('aMatin_periode'+i).focus();
					alert ("Vous devez renseigner une heure de fin pour la periode matin "+i);
					erreur++;
					return false;
				}
				if (document.forms[0].elements['choix_axe1_periode'+i].value == "Choisir axe 1")
				{
					document.getElementById('choix_axe1_periode'+i).style.backgroundColor = "red";
					document.getElementById('choix_axe1_periode'+i).focus();
					alert ("Vous devez renseigner un axe 1 pour la periode matin "+i);						
					erreur++;
					return false;
				}
				if (document.forms[0].elements['choix_axe2_periode'+i].value == "Choisir axe 2")
				{
					document.getElementById('choix_axe2_periode'+i).style.backgroundColor = "red";
					document.getElementById('choix_axe2_periode'+i).focus();
					alert ("Vous devez renseigner un axe 2 pour la periode matin "+i);
					erreur++;
					return false;
				}
				if (document.forms[0].elements['choix_axe2_periode'+i].value == "Absences")
				{
					document.getElementById('choix_axe2_periode'+i).style.backgroundColor = "red";
					document.getElementById('choix_axe2_periode'+i).focus();
					alert ("Veuillez préciser le type d'absence pour la periode matin "+i);
					erreur++;
					return false;
				}
				if (document.forms[0].elements['choix_axe3_periode'+i].value == "Choisir axe 3" )
				{
					// Si l'option "activer axe3" n'est pas cochée, le type passe en hidden. Si c'est le cas, on ne fait pas de controle sur l'axe 3 et on lui applique un ID = 1
					if (document.getElementById('choix_axe3_periode'+i).type == "hidden")
					{
							document.getElementById('id_choix_axe3_periode'+i).value = 1;
							return true;
					}	

					if (document.getElementById('choix_axe3_periode'+i).type == "text")
					{
						document.getElementById('choix_axe3_periode'+i).style.backgroundColor = "red";
						document.getElementById('choix_axe3_periode'+i).focus();
						alert ("Vous devez renseigner un axe3 pour la periode matin "+i);
						erreur++;
						return false;
					}
				}
				if (Number(document.forms[0].elements['deMatin_periode'+i].value) >= Number(document.forms[0].elements['aMatin_periode'+i].value))
				{
					document.getElementById('deMatin_periode'+i).style.backgroundColor = "red";
					document.getElementById('aMatin_periode'+i).style.backgroundColor = "red";
					alert ("Matin "+i+", l'heure de début doit être différente et supérieure à l'heure de fin.");
					erreur++;
					return false;	
				}
				else
				{
					//alert ('Periode validee: n°'+i);
					document.getElementById('deMatin_periode'+i).style.backgroundColor = "green";
					document.getElementById('aMatin_periode'+i).style.backgroundColor = "green";
					document.getElementById('choix_axe1_periode'+i).style.backgroundColor = "green";
					document.getElementById('choix_axe2_periode'+i).style.backgroundColor = "green";
					document.getElementById('choix_axe3_periode'+i).style.backgroundColor = "green";
					ok++;
					// Pas de return false sinon il arrête le script et ne demande pas de remplir les éventuelles cases manquantes des lignes suivantes.
				}
			}
		}
		///////////////////On passe à l'aprem /////////////////////////
		if (i > Number(repetitions)/2)
		{
		//alert ('i = '+i+', repetitions = '+repetitions+'.');
		//alert(document.forms[0].elements['choix_axe1_periode'+i].value);
			// Aprem: Si il y a une heure de début, une heure de fin, un axe1 ou un axe2, on considere que la periode doit etre entierement renseignée.
			if ( (document.forms[0].elements['deAprem_periode'+i].value != "") 
			|| (document.forms[0].elements['aAprem_periode'+i].value != "") 
			|| (document.forms[0].elements['choix_axe1_periode'+i].value != "Choisir axe 1") 
			|| (document.forms[0].elements['choix_axe2_periode'+i].value != "Choisir axe 2") 
			|| (document.forms[0].elements['choix_axe3_periode'+i].value != "Choisir axe 3")
			    )
			{

				if (document.forms[0].elements['deAprem_periode'+i].value == "")
				{
					document.getElementById('deAprem_periode'+i).style.backgroundColor = "red";
					document.getElementById('deAprem_periode'+i).focus();
					alert ("Vous devez renseigner une heure de début pour la periode Aprem "+i);
					erreur++;
					return false;
				}
				if (document.forms[0].elements['aAprem_periode'+i].value == "")
				{
					document.getElementById('aAprem_periode'+i).style.backgroundColor = "red";
					document.getElementById('aAprem_periode'+i).focus();
					alert ("Vous devez renseigner une heure de fin pour la periode Aprem "+i);
					erreur++;
					return false;
				}
				if (document.forms[0].elements['choix_axe1_periode'+i].value == "Choisir axe 1")
				{
					document.getElementById('choix_axe1_periode'+i).style.backgroundColor = "red";
					document.getElementById('choix_axe1_periode'+i).focus();
					alert ("Vous devez renseigner un axe 1 pour la periode Aprem "+i);
					erreur++;
					return false;
				}
				if (document.forms[0].elements['choix_axe2_periode'+i].value == "Choisir axe 2")
				{
					document.getElementById('choix_axe2_periode'+i).style.backgroundColor = "red";
					document.getElementById('choix_axe2_periode'+i).focus();
					alert ("Vous devez renseigner un axe 2 pour la periode Aprem "+i);
					erreur++;
					return false;
				}
				if (document.forms[0].elements['choix_axe2_periode'+i].value == "Absences")
				{
					document.getElementById('choix_axe2_periode'+i).style.backgroundColor = "red";
					document.getElementById('choix_axe2_periode'+i).focus();
					alert ("Veuillez préciser le type d'absence pour la periode Aprem "+i);
					erreur++;
					return false;
				}
				if (document.forms[0].elements['choix_axe3_periode'+i].value == "Choisir axe 3")
				{
					// Si l'option "activer axe3" n'est pas cochée, le type passe en hidden. Si c'est le cas, on ne fait pas de controle sur l'axe 3 et on lui applique un ID = 1
					if (document.getElementById('choix_axe3_periode'+i).type == "hidden")
					{
							document.getElementById('id_choix_axe3_periode'+i).value = 1;
							return true;
					}	

					if (document.getElementById('choix_axe3_periode'+i).type == "text")
					{
						document.getElementById('choix_axe3_periode'+i).style.backgroundColor = "red";
						document.getElementById('choix_axe3_periode'+i).focus();
						alert ("Vous devez renseigner un axe3 2 pour la periode Aprem "+i);
						erreur++;
						return false;
					}
				}
				if (Number(document.forms[0].elements['deAprem_periode'+i].value) >= Number(document.forms[0].elements['aAprem_periode'+i].value))
				{
					document.getElementById('deAprem_periode'+i).style.backgroundColor = "red";
					document.getElementById('aAprem_periode'+i).style.backgroundColor = "red";
					alert ("Aprem "+i+", l'heure de début doit être différente et supérieure à l'heure de fin.");
					erreur++;
					return false;	
				}
				else
				{
					//alert ('Periode validee: n°'+i);
					document.getElementById('deAprem_periode'+i).style.backgroundColor = "green";
					document.getElementById('aAprem_periode'+i).style.backgroundColor = "green";
					document.getElementById('choix_axe1_periode'+i).style.backgroundColor = "green";
					document.getElementById('choix_axe2_periode'+i).style.backgroundColor = "green";
					document.getElementById('choix_axe3_periode'+i).style.backgroundColor = "green";
					ok++;
					// Pas de return false sinon il arrête le script et ne demande pas de remplir les éventuelles cases manquantes des lignes suivantes.
				}
			}


			if ((ok == 0) && (i == repetitions))
			{
				alert ("Impossible d'enregistrer la fiche: Vous n'avez rien renseigné.");
				return false;
			}
			// Cette fonction me fait sortir de la boucle à 7.
			if ( (document.getElementById('total_journee_instant').value < 24) && (document.getElementById('total_journee_instant').value > 0 ) && (i == repetitions) )
			{
				return true;
			}
		}
	}
	return false;
}

function apparition_bouton_creer_jour_type() // Fonction me permettant de faire des tests sans avoir à toujours en recreer une.
{
	document.getElementById('bouton_creer_jour_type').style.display = "";
}
function disparition_bouton_creer_jour_type()
{
	document.getElementById('bouton_creer_jour_type').style.display = "none";
}
function creer_jour_type()
{

	nom_jour_type = document.getElementById('champ_texte_creer_jour_type').value;
	if( nom_jour_type == "" )
	{
		alert("Veuillez donner un nom à votre jour type.");
	}
	else
	{

		de = new Array();
		a = new Array();
		nomAxe1 = new Array();
		idAxe1 = new Array();
		nomAxe2 = new Array();
		idAxe2 = new Array();
		nomAxe3 = new Array();
		idAxe3 = new Array();

		for ( i = 1 ; i <= 6; i++ )
		{
			de[i] 		= document.getElementById('deMatin_periode'+i).value;
			a[i]		= document.getElementById('aMatin_periode'+i).value;
			//nomAxe1[i] 	= document.getElementById('choix_axe1_periode'+i).value;
			idAxe1[i]	= document.getElementById('id_choix_axe1_periode'+i).value;
			//nomAxe2[i] 	= document.getElementById('choix_axe2_periode'+i).value;
			idAxe2[i]	= document.getElementById('id_choix_axe2_periode'+i).value;
			//nomAxe3[i] 	= document.getElementById('choix_axe3_periode'+i).value;

			// Si l'option "activer axe3" n'est pas cochée, on attribue la valeur 1 à idAxe3[i]
			if (document.getElementById('choix_axe3_periode'+i).type == "hidden")
			{
				idAxe3[i] = 1;
			}	

			if (document.getElementById('choix_axe3_periode'+i).type == "text")
			{
				idAxe3[i]	= document.getElementById('id_choix_axe3_periode'+i).value;
			}

		}
		for ( i = 7 ; i <= 12; i++ )
		{
			de[i]	 	= document.getElementById('deAprem_periode'+i).value;
			a[i]		= document.getElementById('aAprem_periode'+i).value;
			//nomAxe1[i] 	= document.getElementById('choix_axe1_periode'+i).value;
			idAxe1[i]	= document.getElementById('id_choix_axe1_periode'+i).value;
			//nomAxe2[i] 	= document.getElementById('choix_axe2_periode'+i).value;
			idAxe2[i]	= document.getElementById('id_choix_axe2_periode'+i).value;
			//nomAxe3[i] 	= document.getElementById('choix_axe3_periode'+i).value;
			
			// Si l'option "activer axe3" n'est pas cochée, on attribue la valeur 1 à idAxe3[i]
			if (document.getElementById('choix_axe3_periode'+i).type == "hidden")
			{
				idAxe3[i] = 1;
			}	

			if (document.getElementById('choix_axe3_periode'+i).type == "text")
			{
				idAxe3[i]	= document.getElementById('id_choix_axe3_periode'+i).value;
			}
		}
		
		periode = new Array();

		for ( i = 1 ; i <= 12 ; i ++)
		{
			periode[i] = (de[i]+"-"+a[i]+"-"+idAxe1[i]+"-"+idAxe2[i]+"-"+idAxe3[i]);
			// Si il n'y avait pas d'axe3, les periodes non remplies seront ----1 et donc enregistrées dans notre base. On traite ces périodes pour les effacer.
			if (periode[i] == "----1")
			{
				periode[i] = "----";
			}
		}

		var xhrCreerJourType = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhrCreerJourType.open("GET", "creer_jour_type.php?from=xhrCreerJourType&nom_jour_type="+nom_jour_type+"&periode1="+periode[1]+"&periode2="+periode[2]+"&periode3="+periode[3]+"&periode4="+periode[4]+"&periode5="+periode[5]+"&periode6="+periode[6]+"&periode7="+periode[7]+"&periode8="+periode[8]+"&periode9="+periode[9]+"&periode10="+periode[10]+"&periode11="+periode[11]+"&periode12="+periode[12], true);
		xhrCreerJourType.onreadystatechange = function() 
		{ 
			if(xhrCreerJourType.readyState == 4)
			{ 
				alert(xhrCreerJourType.responseText);
				//alert('La periode '+periode+' a bien été supprimée.');
			}
			//alert(xhr.readyState); 
		} 
		xhrCreerJourType.send(null);
	}

}

function select_jour_type(idJourType)
{

	//alert(idJourType.value);	
	var xhrImporterJourType = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	var textImporterJourType = "";
	xhrImporterJourType.open("GET", "importer_jour_type.php?idJourType="+idJourType.value, true);
	xhrImporterJourType.onreadystatechange = function() 
	{ 
		if(xhrImporterJourType.readyState == 4)
		{ 
			//alert(xhrImporterJourType.responseText);

			// On transforme l'objet recu en chaine de caractères..
			textImporterJourType=xhrImporterJourType.responseText;

			// On decoupe cette chaine de caractères d'abord pour avoir les differents types jours.
			types_jours=textImporterJourType.split("/EOV");
			//alert(types_jours);

			// length - 1 car il y a un / à la fin de la chaine de caractères.
			for (i=0 ; i < types_jours.length-1 ; i++)
			{
				periode = types_jours[i].split(';')[0];
				de 		= parseFloat(types_jours[i].split(';')[1]);
				a 		= parseFloat(types_jours[i].split(';')[2]);
				idAxe1 	= types_jours[i].split(';')[3];
				nomAxe1 = types_jours[i].split(';')[4];
				idAxe2 	= types_jours[i].split(';')[5];
				nomAxe2 = types_jours[i].split(';')[6];
				idAxe3 	= types_jours[i].split(';')[7];
				nomAxe3 = types_jours[i].split(';')[8];



				//alert(de+' --> '+a);
				if ( periode <= 6 )
				{
					document.getElementById('deMatin_periode'+periode).value = de ;
					document.getElementById('aMatin_periode'+periode).value = a ;
					document.getElementById('totalMatin_periode'+periode).value = Number(a)-Number(de).toFixed(2);
												
				}
				if ( periode > 6 )
				{
					document.getElementById('deAprem_periode'+periode).value = de ;
					document.getElementById('aAprem_periode'+periode).value = a ;	
					document.getElementById('totalAprem_periode'+periode).value = Number(a)-Number(de).toFixed(2);
						
				}
				document.getElementById('choix_axe1_periode'+periode).value 	= nomAxe1 ;
				document.getElementById('id_choix_axe1_periode'+periode).value 	= idAxe1 ;
				document.getElementById('choix_axe2_periode'+periode).value 	= nomAxe2 ;
				document.getElementById('id_choix_axe2_periode'+periode).value 	= idAxe2 ;
				document.getElementById('choix_axe3_periode'+periode).value 	= nomAxe3 ;
				document.getElementById('id_choix_axe3_periode'+periode).value 	= idAxe3 ;	
			}
			calcul_total_journee();

		}
		//alert(xhr.readyState); 
	} 
	xhrImporterJourType.send(null);
	//alert("fin");
}

function supprimer_jour_type()
{
	id_jour_type = document.getElementById('jour_type').value;
	nom_jour_type = document.getElementById("jour_type").options[document.getElementById('jour_type').selectedIndex].text; 

	if (confirm("Voulez vous vraiment supprimer le jour type nommé : \""+nom_jour_type+"\" ?")) 
	{
		var xhrSupprJourType = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
		xhrSupprJourType.open("GET", "supprimer_jour_type.php?id_jour_type="+id_jour_type, true);
		xhrSupprJourType.onreadystatechange = function() 
		{ 
			if(xhrSupprJourType.readyState == 4)
			{ 
				//alert(xhrSupprJourType.responseText);
				//alert('La periode '+periode+' a bien été supprimée.');
			}
			//alert(xhr.readyState); 
		} 
		xhrSupprJourType.send(null);
		//rafraichir_page("xhrSupprJourType");
	}
	else 
	{
		alert("Suppression annulée.")
	}
}

function apparition_bouton_supprimer_jour_type() // Fonction me permettant de faire des tests sans avoir à toujours en recreer une.
{
	document.getElementById('bouton_supprimer_jour_type').style.display = "";
}

function disparition_bouton_supprimer_jour_type()
{
	document.getElementById('bouton_supprimer_jour_type').style.display = "none";
}
//////////////////////////////////////////////////////////
// 	FIN DES JAVASCRIPT 				//
//////////////////////////////////////////////////////////

</SCRIPT>

</HEAD>
<BODY onLoad="demarrage();">

<?php 
include("connexion_base.php"); 
include("importer_configuration.php");
include("importer_id_absences.php");
?>

<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id=tableGlobale><tr ><td></td><td id="tableGlobale">

<?php include("menu_general.php") ?>

<table border=0 width=100%>
	<tr>
		<td align=left  width=18%>
			<table border=0 width=100%>
				<tr align=left >
					<td colspan=2>

						<!-- Test suivant -->
						<input type="button" id="hier"  name="hier" value="Précédent" onClick="javascript:hier()">
						<input type="button" id="demain"  name="demain" value="Suivant" onClick="javascript:demain()">
							
					</td>
				</tr>
				<tr align=left >
					<td>
						 Date:
					</td>
					<td align=right>
	<form name="valider_fiche" method="post" onsubmit="return verifier_fiche();" action="valider_fiche.php" target="_blank">
						<!-- Calendrier -->
						<input type="text" id="datepicker" name="date" />
						<!-- Champ caché permettant de mettre la date en "altfield" pour l avoir au format MYSQL (voir fonction altfiel et altformat dans la declaration du calendrier) -->
						<input type="HIDDEN" id="AltFieldDateMysql" name="AltFieldDateMysql" />
					</td>
				</tr>
			</table>

		</td>
		<td align=center>
			<table border=0 width=100% align=center>
				<tr align=center >
					<td colspan=6>
							<!-- Gestion du select type d'absence -->
						<?php 
						if ( $afficherRaccourcisAbsences == "checked" )
						{
							echo '<br> <label for="absent"><input type="checkbox" name="absent" value="absent" id="absent" onClick="javascript:conge_coche(this)"> Absent toute la journée pour cause de : </label>';
							// Je mets dans un champ hidden la valeur de l'ID axe1 correspondant au code comptable 99 = "congés et autres absences"
							echo '<input type="hidden" id="id_axe1_code_99" value="' . $idAxe1Code99 . '">';
							echo '<input type="hidden" id="id_axe2_code_9900" value="' . $idAxe2Code9900 . '">';

							$reponse_absence = $bdd->query('SELECT * FROM Axe2 WHERE codeAxe2 like "99%%" ORDER BY nomAxe2 ');

							echo "<select name=type_absence id=type_absence onClick=javascript:conge_coche(this)>";
										
							while ($donnees = $reponse_absence->fetch())
							{
								$type_absence=$donnees['nomAxe2'];
								$id_absence=$donnees['idAxe2'];
							
							echo 	"<option value=$id_absence> $type_absence </option>";
							
							}
							$reponse_absence->closeCursor(); // Termine le traitement de la requête
							echo "</select><br><br>";
						}
						else
						{
							echo '<br> <label for="absent"><input type="hidden" name="absent" value="absent" id="absent" onClick="javascript:conge_coche(this)"></label>';
						}
						?>
					</td>
				</tr>
				<tr>
					<td width=20%>
					</td>
					<td align=right width=20%>
						<?php
							// Gestion du "jour type"
							if ( $afficherJoursTypes == "checked" )
							{
								echo "Choisir un jour type : ";
						?>
					</td>
					<td width=25% align=center>	
						<?php
								$idUtilisateur		=	$_SESSION['idUtilisateurs'];
								$reponse_jours_types = $bdd->query("SELECT * FROM JoursTypes WHERE idUtilisateur = $idUtilisateur ORDER BY nom");

								echo '<select name=jour_type id=jour_type  onchange="if (this.selectedIndex) select_jour_type(this);" onFocus="javascript:apparition_bouton_supprimer_jour_type()" onBlur="javascript:disparition_bouton_supprimer_jour_type()"> >';
								echo "<option value='NULL'></option>";		
								while ($donnees = $reponse_jours_types->fetch())
								{
									$jour_type=$donnees['nom'];
									$id_jour_type=$donnees['id'];
								
								echo 	"<option value=$id_jour_type> $jour_type </option>";
								
								}
								$reponse_jours_types->closeCursor(); // Termine le traitement de la requête
								echo "</select>";
						?>
					</td>
					<td width=15% align=center>
						<?php
								echo ' <input type="text" id="champ_texte_creer_jour_type" onFocus="javascript:apparition_bouton_creer_jour_type()" onBlur="javascript:disparition_bouton_creer_jour_type()"> ';
						?>
					</td>
					<td width=25%>
						<?php						
								echo ' <input type="button" value="Créer" id="bouton_creer_jour_type" onMouseDown="javascript:creer_jour_type()"> ';
								echo ' <input type="button" value="Supprimer" id="bouton_supprimer_jour_type" onMouseDown="javascript:supprimer_jour_type()"> ';

							}
							else
							{
								echo '<br> <label for="absent"><input type="hidden" name="absent" value="absent" id="absent" onClick="javascript:conge_coche(this)"></label>';
							}
						?>
					</td>
				</tr>
			</table>
		</td>

		<td align=right width=26%>
			<table border=0>
				<tr>
					<td align=center>
						<font size=3>Récapitulatif: </font><br>
					</td>
				</tr>
				<tr>
					<td>
						<input type="button" id="bouton_tats_absences" value="Absences" onClick="javascript:lancerStats('absences');" readonly />
						<input type="button" id="bouton_stats_axes" value="Axes" onClick="javascript:lancerStats('axes');" readonly />
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<!-- Inserer le tableau de selection des heures -->
<?php include("table_heures.php") ?>
<br>

<!-- Table d'en bas: Tous les totaux, le boutons valider et la validation en couleur de la semaine -->
<table border=0 width=100% >
	<tr align=left>
		<td width=3%>
		</td>
		
		<td width=10% align=left>
			Total de la journée:
		</td>

		<td width=20% align=left>
			<!--Il est renseigne par la fonction javascript calcul_total_journee() -->
			<input type="numeric" step="any" id="total_journee" name="total_journee" onClick=javascript:ouvrir_graph("graph_heures_sup_jours.php","graph_heures_sup_jours") readonly /> heures.
		</td>

		<td width=22% align=center>
			

		</td>

		<td width=37% align=right>
			<?php




				if ( $afficherCalculRapideJournee == "checked" )
				{
				echo "
						<table border='0' width='100%'>	
							<tr>
								<td align='right' width='67%'> <font color='grey'>Total pré validation = </font> </td>								
								<td width='1%'> </td>	
								<td> <font color='grey'> <input type='text' id='total_journee_instant' name='total_journee_instant' readonly /> heures.</font> </td>
							</tr>	
						</table>
					";
				}
				else
				{
				echo "
						<table border='0' width='100%'>	
							<tr>
								<td> </td>	
								<td></td>	
								<td><input type='hidden' id='total_journee_instant' name='total_journee_instant' readonly /></td>
							</tr>	
						</table>
					";
				}
			?>
		</td>

	<!--		Truc de malade: Avec la td en dessous, impossible d'aligner à droite.-->
	<!--		<td width=35% align=right>-->
	<!--			<!-- Input submit permettant de valider le formulaire -->
	<!--			<input type="submit" name="valider" id="valider" value="valider" />-->
	<!--		</td>-->

	</tr>

	<tr>
		<td>
		</td>
		
		<td align=left>__________
		</td>

		<td>
		</td>


		<td align=center>
			<?php 
				if ( $afficherConges == "checked" )
				{
					echo 'Congé(s) restant(s):';
				}
				else
				{
					echo '';
				}
			?>
		</td>

		<td align=right colspan=2>
			<!-- Input submit permettant de valider le formulaire -->
			<input type="submit" name="valider" id="valider" value="Valider" />
		</td>
	</tr>

	<tr align=left>
		<td>
		</td>
		
		<td align=left>
		Total de la semaine:
		</td>
		<td align=left>
			<input type="numeric" step="any" id="total_semaine" name="total_semaine" onClick=javascript:ouvrir_graph('graph_heures_sup_semaine.php','graph_heures_sup_semaine.php') readonly/> heures sur <input type="numeric" step="any" id="heures_contrat" name="heures_contrat" readonly />h.
		</td>


		<td align=center>
			<?php 
				if ( $afficherConges == "checked" )
				{
					echo '<input type="numeric" step="any" id="total_jours_conges_restants" name="total_jours_conges_restants" readonly/> jour(s).';
				}
				else
				{
					echo '<input type="hidden" id="total_jours_conges_restants" name="total_jours_conges_restants" readonly/>';
				}
			?>
		</td>

		<td>
		</td>
	</tr>

	<tr>
		<td>
		</td>
		
		<td align=right>
			<?php 
				if ( $afficherHeuresSup == "checked" )
				{
					echo "+";
				}
			?>
		</td>
		<td>
			
			<?php 
				if ( $afficherHeuresSup == "checked" )
				{
					echo '<input type="numeric" step="any" id="heures_sup" name="heures_sup" readonly/> heure(s) supplémentaire(s).';
				}
				else
				{
					echo '<input type=hidden id="heures_sup" name="heures_sup" readonly/>';
				}
			?>

		</td>

		<td>
		</td>

		<td>
		</td>
	</tr>

	<tr>
		<td>
		</td>
		
		<td align=right>
			<?php 
				if ( $afficherHeuresSup == "checked" )
				{
					echo "=";
				}
			?>
		</td>
		<td>
			

			<?php 
				if ( $afficherHeuresSup == "checked" )
				{
					echo '<input type="numeric" step="any" id="heures_totaux" name="heures_totaux" readonly/> heure(s) totale(s). ';
				}
				else
				{
					echo '<input type="hidden" id="heures_totaux" name="heures_totaux" readonly/>';
				}
			?>
		</td>

		<td align=center>
			<?php 
				if ( $afficherRTT == "checked" )
				{
					echo 'RTT restant(s):';
				}
				else
				{
					echo '';
				}
			?>
		</td>

		<td>
		</td>
	</tr>

	<tr>
		<td>
		</td>
		
		<td align=left>__________
		</td>

		<td>
		</td>

		<td align=center>
			<?php 
				if ( $afficherRTT == "checked" )
				{
					echo '<input type="numeric" step="any" id="total_jours_rtt_restants" name="total_jours_rtt_restants" readonly/> jour(s).';
				}
				else
				{
					echo '<input type="hidden" id="total_jours_rtt_restants" name="total_jours_rtt_restants" readonly/>';
				}
			?>
		</td>

		<td>
		</td>
	</tr>
	
	<tr align=left>
		<td>
		</td>
		
		<td align=left>
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
			<table border=0 CELLPADDING=5 >	
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
		<td width=3%>
		</td>
	</tr>
</table>

<!-- Table contenant l'horloge de conversion base 60 / base 100 -->
<table border=0 width=100% >
	<tr>
		<td align="center" >
			<img src="horloge_centiemes.png"  /> 
		</td>
	<tr>
</table>

<!-- Fin de la table de mise en page globale -->
</td><td></td></tr></table>
</form>

</BODY>
</HTML>
