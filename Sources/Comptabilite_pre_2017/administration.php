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


	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="style.css" />
	<link rel="stylesheet" href="CSS/Delta/css/normalise.css"> 
	<link rel="icon" type="image/png" href="favicon.png" />


<!-- J'importe mon fichier permettant de vérifier les inputs -->
<script src="verifier_input_javascript.js"></script>

<script>
function getXMLHttpRequest() 
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

function retourFiche()
{
	location.href="compta.php";
}

function rafraichir_page()
{
	document.location.href='administration.php'
}

function validerRachat()
{
	idUtilisateur=document.getElementById('id_select_nom').value;

	importerUtilisateur(idUtilisateur);
}

function importerUtilisateur(idUtilisateur)
{
		//alert("debut test");
			var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhr.open("GET", "importer_utilisateur.php?utilisateur="+idUtilisateur, true);
			xhr.onreadystatechange = function() 
			{ 
				if(xhr.readyState == 4)
				{ 
					//alert(xhr.responseText);
					decouperImporterUtilisateur(xhr);
				}
				//alert(xhr.readyState); 
			} 

			xhr.send(null);	
}

function decouperImporterUtilisateur(xhr)
{
	// On transforme l'objet recu en chaine de caractères..
	xhrText=xhr.responseText;
	//alert(xhrText);

	// On decoupe cette chaine de caractères d'abord pour avoir les differentes dates.
	donnees=xhrText.split(",");
	//alert(donnees);



	//// Nous allons gérer les variables "colonne = xxx" en les transformant en "xxx".
	// On cree le tableau donneesHeures dans lequel on "pushera" ensuite nos donneesHeures.
	tableDonnees		=	new Array ();
		
	// On "push" ensuite la 2eme colonne de notre réponse (donc la valeur de la variable) dans notre tableau "donneesHeures".
	tableDonnees.push(donnees[0]);
	tableDonnees.push(donnees[1]);	
	tableDonnees.push(donnees[2]);	
	tableDonnees.push(donnees[3]);	
	tableDonnees.push(donnees[4]);
	tableDonnees.push(donnees[5]);
	tableDonnees.push(donnees[6]);


	idUtilisateur		=	tableDonnees[0];
	loginUtilisateur	=	tableDonnees[1];
	nom					=	tableDonnees[2];
	prenom				=	tableDonnees[3];
	heuresContrat		=	tableDonnees[4];
	heuresSupTotal		=	tableDonnees[5];
	heuresRachetees		=	tableDonnees[6];

	// Total d'heures sup avec prise en compte des heures déjà rachetées.
	heuresSup		=	heuresSupTotal - heuresRachetees;
	
	souhaitRachat		=	document.getElementById('nbr_heures_a_racheter').value;
	
	checkNum("rachatHeures", souhaitRachat);

	// A combien sera il une fois que les heures que l'on veut lui racheter seront validées?
	futurTotalHeuresSup	=	heuresSup - souhaitRachat;


	// On arrondi à deux chiffres après la virgule
	heuresSup			=	heuresSup.toFixed(2);
	futurTotalHeuresSup = 	futurTotalHeuresSup.toFixed(2);

	if (souhaitRachat == 0)
	{
		alert("Vous ne pouvez pas racheter 0 heures.");
	}
	if (souhaitRachat != 0)
	{

		textConfirm="Êtes vous certain de vouloir racheter\n\n  "+souhaitRachat+" heures à\n\n  "+prenom+" "+nom+" qui est actuellement à \n\n  "+heuresSup+" heures supplémentaires? \n\n  ("+heuresRachetees+" heures rachetées cette année) \n\n Cette personne passera à un total de "+futurTotalHeuresSup+" heures supplémentaires si vous validez cette opération.";

		//alert(textConfirm);

		var reponseConfirm= confirm(textConfirm);
		
		if (reponseConfirm)	
		{

		//location.href="rachat_heures.php?loginUtilisateur="+loginUtilisateur+"&idUtilisateur="+idUtilisateur+"&nbr="+souhaitRachat;

			var xhrRachat = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhrRachat.open("GET", "rachat_heures.php?loginUtilisateur="+loginUtilisateur+"&idUtilisateur="+idUtilisateur+"&nbr="+souhaitRachat, true);
			xhrRachat.onreadystatechange = function() 
			{ 
				if(xhrRachat.readyState == 4)
				{ 
					alert(xhrRachat.responseText);
					//alert("Prise en compte du rachat de "+souhaitRachat+" heures à "+prenom+" "+nom+" effectuée.");
				}
				//alert(xhr.readyState); 
			} 
			xhrRachat.send(null);
		}
	}
}

function modifier_utilisateur(idUtilisateur, nom, prenom, nbrHeures, nbrConges, nbrRTT, couleur, login)
{
	//alert(couleur);
	var admin = 0;
	var active = 0;

	if (document.getElementById('adminCheck'+idUtilisateur).checked == true)
	{
 		admin = 1;
 	}
	if (document.getElementById('activeUtilisateurCheck'+idUtilisateur).checked == true)
	{
		active = 1;
	}

	login=document.getElementById('login_'+idUtilisateur).value;
	nom=document.getElementById('nom_'+idUtilisateur).value;
	prenom=document.getElementById('prenom_'+idUtilisateur).value;
	nbrHeures=document.getElementById('nbr_heures_'+idUtilisateur).value;
	nbrConges=document.getElementById('nbrConges_'+idUtilisateur).value;
	nbrRTT=document.getElementById('nbrRTT_'+idUtilisateur).value;
	couleur=document.getElementById('couleur_'+idUtilisateur).value;

	couleur = couleur.replace("#", "");

	//checkInput(login);
// 	checkInput(nom);
// 	checkInput(prenom);
// 	checkInput(couleur);
// 	checkNum("heuresSemaine", nbrHeures);

	
	// Permet de remplacer une virgule rentree par l'utilisateur en point pour mysql
	if (nbrHeures.indexOf(',') != -1)
	{
		nbrHeures = nbrHeures.replace("," , ".");
		//alert(nbrHeures);
	}

			//alert(idUtilisateur+", "+nom+", "+prenom+", "+nbrHeures+", "+login);
			var xhrModifUtilisateur = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhrModifUtilisateur.open("GET", "modifier_utilisateur.php?idAModifier="+idUtilisateur+"&nom="+nom+"&prenom="+prenom+"&nbrHeures="+nbrHeures+"&nbrConges="+nbrConges+"&nbrRTT="+nbrRTT+"&couleur="+couleur+"&login="+login+"&admin="+admin+"&active="+active, true);
			//alert("modifier_utilisateur.php?idAModifier="+idUtilisateur+"&nom="+nom+"&prenom="+prenom+"&nbrHeures="+nbrHeures+"&login="+login+"&admin="+admin+"&active="+active);
			xhrModifUtilisateur.onreadystatechange = function() 
			{ 
				if(xhrModifUtilisateur.readyState == 4)
				{ 
					alert(xhrModifUtilisateur.responseText);
				}
				//alert(xhr.readyState); 
			} 

			xhrModifUtilisateur.send(null);
			//rafraichir_page("xhrModifUtilisateur");


}

function creer_utilisateur()
{
//alert("toto");
	var admin = 0;
	var active = 0;

	if (document.getElementById('adminCheck_nouvel_utilisateur').checked == true)
	{
 		admin = 1;
 	}
	if (document.getElementById('activeUtilisateurCheck_nouvel_utilisateur').checked == true)
	{
		active = 1;
	}

	login=document.getElementById('login_nouvel_utilisateur').value;
	password=document.getElementById('password_nouvel_utilisateur').value;
	nom=document.getElementById('nom_nouvel_utilisateur').value;
	prenom=document.getElementById('prenom_nouvel_utilisateur').value;
	nbrHeures=document.getElementById('nbr_heures_nouvel_utilisateur').value;
	nbrConges=document.getElementById('nbr_conges').value;
	nbrRTT=document.getElementById('nbr_RTT').value;
	couleur=document.getElementById('couleur_nouvel_utilisateur').value;

	couleur = couleur.replace("#", "");


	if (login == "")
	{
		alert('Vous devez renseigner un login');
		document.getElementById('login_nouvel_utilisateur').style.backgroundColor = "red";
		return null;
	}
	
	if (password == "")
	{
		alert('Vous devez renseigner un mot de passe');
		document.getElementById('password_nouvel_utilisateur').style.backgroundColor = "red";
		return null;
	}
	
	if (nom == "")
	{
		alert('Vous devez renseigner un nom');
		document.getElementById('nom_nouvel_utilisateur').style.backgroundColor = "red";
		return null;
	}
	
	if (prenom == "")
	{
		alert('Vous devez renseigner un prénom');
		document.getElementById('prenom_nouvel_utilisateur').style.backgroundColor = "red";
		return null;
	}

	if (nbrHeures == "")
	{
		alert("Vous devez renseigner un nombre d'heures par semaines.");
		document.getElementById('nbr_heures_nouvel_utilisateur').style.backgroundColor = "red";
		return null;
	}
	
	// Permet de remplacer une virgule rentree par l'utilisateur en point pour mysql
	if (nbrHeures.indexOf(',') != -1)
	{
		nbrHeures = nbrHeures.replace("," , ".");
		//alert(nbrHeures);
	}

// 	checkInput(login);
// 	checkInput(password);
// 	checkInput(nom);
// 	checkInput(prenom);
// 	checkNum("heuresSemaine", nbrHeures);
	if (couleur == "")
	{
		couleur = "#FFFFFF";
	}
	var xhrCreerUtilisateur = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhrCreerUtilisateur.open("GET", "creer_utilisateur.php?login="+login+"&password="+password+"&nom="+nom+"&prenom="+prenom+"&nbrHeures="+nbrHeures+"&nbrConges="+nbrConges+"&nbrRTT="+nbrRTT+"&couleur="+couleur+"&admin="+admin+"&active="+active, false);
	xhrCreerUtilisateur.onreadystatechange = function() 
	{ 
		if(xhrCreerUtilisateur.readyState == 4)
		{ 
			alert(xhrCreerUtilisateur.responseText);
		}
		//alert(xhr.readyState); 
	} 
	
	xhrCreerUtilisateur.send(null);
	
	if (xhrCreerUtilisateur.responseText.indexOf("a bien été créé") != -1)
	{	
		rafraichir_page("xhrCreerUtilisateur");
	}
}

function creer_groupe()
{
//alert("toto");

	nom=document.getElementById('choix_nom_nouveau_groupe').value;



	if (nom == "")
	{
		alert('Vous devez renseigner un nom de groupe');
		document.getElementById('choix_nom_nouveau_groupe').style.backgroundColor = "red";
		return null;
	}
		
	var xhrCreerGroupe = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhrCreerGroupe.open("GET", "creer_groupe.php?nom="+nom, false);
	xhrCreerGroupe.onreadystatechange = function() 
	{ 
		if(xhrCreerGroupe.readyState == 4)
		{ 
			alert(xhrCreerGroupe.responseText);
		}
		//alert(xhr.readyState); 
	} 
	
	xhrCreerGroupe.send(null);
	
	if (xhrCreerGroupe.responseText.indexOf("a bien été créé") != -1)
	{
		rafraichir_page("xhrCreerGroupe");
	}

}

function configurer_groupe(id, nom)
{
	window.open ("proprietes_groupe.php?idGroupe="+id, "proprietes_groupe_"+nom, config='height=800, width=900, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no');
}

function modifier_groupe(id)
{
	idGroupe 	= id;
	nomGroupe 	= document.getElementById('choix_nom_groupe_'+idGroupe).value;

	var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhr.open("GET", "modifier_groupe.php?idGroupe="+idGroupe+"&nomGroupe="+nomGroupe+"&modifierNom=1", false);
	xhr.onreadystatechange = function() 
	{ 
		if(xhr.readyState == 4)
		{ 
			alert(xhr.responseText);
		}
		//alert(xhr.readyState); 
	} 
	xhr.send(null);

	// Si le message ne retourne pas "erreur", on relance la page.
	if ( xhr.responseText.indexOf("Erreur: ") == -1 )
	{
		location.reload();
	}

}

function modifier_axe3(idAxe3)
{


//	alert(idAxe3);
	var active = 0;

	if (document.getElementById('activeAxe3Check'+idAxe3).checked == true)
	{
		active = 1;
	}

//	alert(active);

	nomAxe3=document.getElementById('nom_axe3_'+idAxe3).value;
	//checkInput(nomAxe3);
//	alert(nomAxe3);
	
			//alert(idAxe3+", "+nomAxe3);
			var xhrModifAxe3 = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhrModifAxe3.open("GET", "modifier_axe3.php?idAxe3="+idAxe3+"&nomAxe3="+nomAxe3+"&active="+active, true);
			xhrModifAxe3.onreadystatechange = function()
			{ 
				if(xhrModifAxe3.readyState == 4)
				{ 
					alert(xhrModifAxe3.responseText);
				}
				//alert(xhr.readyState); 
			} 

			xhrModifAxe3.send(null);


			//rafraichir_page("xhrModifAxe3");


}

function modifier_axe(typeAxe, idAxe)
{
	nomAxe		=	document.getElementById('nom_'+typeAxe+'_'+idAxe).value;
	codeAxe		=	document.getElementById('code_'+typeAxe+'_'+idAxe).value;
	idSection	=	"";
	
	// Si typeAxe contient "axe", alors je défini l'idsection liée à cet axe.
	if (typeAxe.indexOf("Axe") != -1)
	{
		idSection	=	document.getElementById('id_select_section_'+typeAxe+'_'+idAxe).value;
	}
	
	//checkInput(nomAxe);
	//checkNum(typeAxe, codeAxe);
	
			var xhrModifAxe = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhrModifAxe.open("GET", "modifier_axe.php?idAxe="+idAxe+"&nomAxe="+nomAxe+"&typeAxe="+typeAxe+"&codeAxe="+codeAxe+"&idSection="+idSection, true);
			xhrModifAxe.onreadystatechange = function()
			{ 
				if(xhrModifAxe.readyState == 4)
				{ 
					alert(xhrModifAxe.responseText);
				}
				//alert(xhr.readyState); 
			} 

			xhrModifAxe.send(null);
			//rafraichir_page("xhrModifAxe");
}

function creerAxe3()
{
	codeAxe3=document.getElementById('code_Axe3_nouveau').value;
	nomAxe3=document.getElementById('nom_axe3_nom_nouveau').value;
	//checkInput(nomAxe3);
	
			//alert(nomAxe3);
			var xhrCreerAxe3 = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
			xhrCreerAxe3.open("GET", "creer_axe3.php?nomAxe3="+nomAxe3+"&codeAxe3="+codeAxe3, false);
			xhrCreerAxe3.onreadystatechange = function()
			{ 
				if(xhrCreerAxe3.readyState == 4)
				{ 
					alert(xhrCreerAxe3.responseText);
				}
				//alert(xhr.readyState); 
			} 

			xhrCreerAxe3.send(null);

	if (xhrCreerAxe3.responseText.indexOf("a bien été créé") != -1)
	{
		rafraichir_page("xhrCreerAxe3");
	}
}

function creerAxe(typeAxe)
{
	nomAxe=document.getElementById('nom_'+typeAxe+'_nom_nouveau').value;
	codeAxe=document.getElementById('code_'+typeAxe+'_nouveau').value;
	
	// Si c'est un Axe on va chercher l'idSection, si c'est une Section on le renseigne comme vide.
	if (typeAxe.match("Axe"))
	{
		idSection=document.getElementById('id_select_section_nouveau_'+typeAxe).value;
	}
	if (typeAxe.match("Section"))
	{
		idSection="";
	}
	
	//checkNum(typeAxe, codeAxe);
	//checkInput(nomAxe);
	
	// Si aucune section n'a été choisi dans le menu déroulant des Axes, on l'exige.
	if (idSection == "null")
	{
		alert("Vous devez choisir une section pour ce nouvel axe.");
		throw "stop execution";
	}
			//alert(nomAxe3);
			var xhrCreerAxe = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente


			xhrCreerAxe.open("GET", "creer_axe.php?typeAxe="+typeAxe+"&nomAxe="+nomAxe+"&codeAxe="+codeAxe+"&idSection="+idSection, false);
			xhrCreerAxe.onreadystatechange = function()
			{ 
				if(xhrCreerAxe.readyState == 4)
				{ 
					alert(xhrCreerAxe.responseText);
				}
				//alert(xhr.readyState); 
			} 
			xhrCreerAxe.send(null);

	if (xhrCreerAxe.responseText.indexOf("a bien été créé") != -1)
	{			
		rafraichir_page("xhrCreerAxe");
	}
}


function afficher_gestion(onglet)
{

	if (	document.getElementById("bouton_afficher_gestion_"+onglet).value == "afficher" )
		{
			document.getElementById("tableOngletsVertCachee"+onglet).style.display = "";			
			document.getElementById("bouton_afficher_gestion_"+onglet).value = "masquer";
		}
		
	else if (	document.getElementById("bouton_afficher_gestion_"+onglet).value == "masquer" )
		{
			document.getElementById("tableOngletsVertCachee"+onglet).style.display = "none";
			document.getElementById("bouton_afficher_gestion_"+onglet).value = "afficher";
		}
}

function choixOnglet(choix)
{

	if (choix == "axe1")
	{
		document.getElementById("DivOngletGestionAxe1").style.display = "";
		document.getElementById("DivOngletGestionAxe2").style.display = "none";
		document.getElementById("DivOngletGestionAxe3").style.display = "none";
		document.getElementById("DivOngletGestionSection").style.display = "none";

		
		document.getElementById("ongletVertGestionAxe1").style.backgroundColor = "#B0DD8D";
		document.getElementById("ongletBleuGestionAxe2").style.backgroundColor = "#9BD6F9";
		document.getElementById("ongletBleuGestionAxe3").style.backgroundColor = "#9BD6F9";
		document.getElementById("ongletBleuGestionSection").style.backgroundColor = "#9BD6F9";
	}
	
	if (choix == "axe2")
	{
		document.getElementById("DivOngletGestionAxe1").style.display = "none";
		document.getElementById("DivOngletGestionAxe2").style.display = "";
		document.getElementById("DivOngletGestionAxe3").style.display = "none";
		document.getElementById("DivOngletGestionSection").style.display = "none";

		
		document.getElementById("ongletVertGestionAxe1").style.backgroundColor = "#9BD6F9";
		document.getElementById("ongletBleuGestionAxe2").style.backgroundColor = "#B0DD8D";
		document.getElementById("ongletBleuGestionAxe3").style.backgroundColor = "#9BD6F9";
		document.getElementById("ongletBleuGestionSection").style.backgroundColor = "#9BD6F9";
	}

	if (choix == "axe3")
	{
		document.getElementById("DivOngletGestionAxe1").style.display = "none";
		document.getElementById("DivOngletGestionAxe2").style.display = "none";
		document.getElementById("DivOngletGestionAxe3").style.display = "";
		document.getElementById("DivOngletGestionSection").style.display = "none";

		
		document.getElementById("ongletVertGestionAxe1").style.backgroundColor = "#9BD6F9";
		document.getElementById("ongletBleuGestionAxe2").style.backgroundColor = "#9BD6F9";
		document.getElementById("ongletBleuGestionAxe3").style.backgroundColor = "#B0DD8D";
		document.getElementById("ongletBleuGestionSection").style.backgroundColor = "#9BD6F9";
	}
	
	if (choix == "section")
	{
		document.getElementById("DivOngletGestionAxe1").style.display = "none";
		document.getElementById("DivOngletGestionAxe2").style.display = "none";
		document.getElementById("DivOngletGestionAxe3").style.display = "none";
		document.getElementById("DivOngletGestionSection").style.display = "";

		
		document.getElementById("ongletVertGestionAxe1").style.backgroundColor = "#9BD6F9";
		document.getElementById("ongletBleuGestionAxe2").style.backgroundColor = "#9BD6F9";
		document.getElementById("ongletBleuGestionAxe3").style.backgroundColor = "#9BD6F9";
		document.getElementById("ongletBleuGestionSection").style.backgroundColor = "#B0DD8D";
	}

	if (choix == "utilisateurs")
	{
		document.getElementById("DivOngletGestionUtilisateurs").style.display = "";		
		document.getElementById("DivOngletGestionGroupes").style.display = "none";

		document.getElementById("ongletVertGestionUtilisateurs").style.backgroundColor = "#B0DD8D";
		document.getElementById("ongletBleuGestionGroupes").style.backgroundColor = "#9BD6F9";
	}

	if (choix == "groupes")
	{
		document.getElementById("DivOngletGestionUtilisateurs").style.display = "none";		
		document.getElementById("DivOngletGestionGroupes").style.display = "";

		document.getElementById("ongletVertGestionUtilisateurs").style.backgroundColor = "#9BD6F9";
		document.getElementById("ongletBleuGestionGroupes").style.backgroundColor = "#B0DD8D";
	}

}

function reset_password(idUtilisateur, nomUtilisateur, prenomUtilisateur, loginUtilisateur, nomUtilisateur, prenomUtilisateur)
{

		textConfirm="Êtes vous certain de vouloir réinitialiser le mot de passe de: \n\n"+prenomUtilisateur+" "+nomUtilisateur+" ?";

		//alert(textConfirm);

		var reponseConfirm= confirm(textConfirm);

		if (reponseConfirm)	
		{
			var newPass1=prompt("Veuillez entrer le nouveau mot de passe pour \n\n"+prenomUtilisateur+" "+nomUtilisateur+" ?","");
			var newPass2=prompt("Veuillez confirmer ce nouveau mot de passe pour \n\n"+prenomUtilisateur+" "+nomUtilisateur+" ?","");
			
			//checkInput(newPass1);
			
			if (newPass1 == newPass2)
			{
				//alert(nomAxe3);
				var xhrModifierPassword = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente

				xhrModifierPassword.open("GET", "modifier_password.php?idUtilisateur="+idUtilisateur+"&newPassword="+newPass1+"&loginUtilisateur="+loginUtilisateur+"&nomUtilisateur="+nomUtilisateur+"&prenomUtilisateur="+prenomUtilisateur, true);
				//alert("modifier_password.php?idUtilisateur="+idUtilisateur+"&newPassword="+newPass1+"&loginUtilisateur="+loginUtilisateur);
				xhrModifierPassword.onreadystatechange = function()
				{ 
					if(xhrModifierPassword.readyState == 4)
					{ 
						alert(xhrModifierPassword.responseText);
					}
					//alert(xhr.readyState); 
				} 
				xhrModifierPassword.send(null);

			}
			if (newPass1 != newPass2)
			{
				alert("Les deux mots de passes rentrés ne correspondent pas.")
			}



		}	
}

function valider_configuration()
{
	moisDepartAnneeConge = document.getElementById('moisDepartAnneeConge').value;
	nbrJoursConges = document.getElementById('nbr_jours_conges').value;
	nbrJoursRTT = document.getElementById('nbr_jours_RTT').value;
	typeGestionRTT = document.getElementById('id_select_type_gestion_RTT').value;
	axe2_exclus_totaux = document.getElementById('axe2_exclus_totaux').value;
	moisDepartAnneeRTT = document.getElementById('moisDepartAnneeRTT').value;
	moisDepartDecompteHeures = document.getElementById('moisDepartDecompteHeures').value;
	zoneVacances = document.getElementById('select_zone').value;


	if ( document.getElementById('activerAxe3').checked == true)
	{
		activerAxe3 = 1;
	}
	else
	{
		activerAxe3 = 0;
	}


	if ( document.getElementById('afficher_heures_sup').checked == true)
	{
		afficherHeuresSup = 1;
	}
	else
	{
		afficherHeuresSup = 0;
	}

	if ( document.getElementById('afficher_codes_comptables_axes').checked == true)
	{
		afficherCodesComptablesSelectionAxes = 1;
	}
	else
	{
		afficherCodesComptablesSelectionAxes = 0;
	}

	if ( document.getElementById('afficher_codes_comptables_recap').checked == true)
	{
		afficherCodesComptablesRecap = 1;
	}
	else
	{
		afficherCodesComptablesRecap = 0;
	}

	if ( document.getElementById('afficher_conges').checked == true)
	{
		afficherConges = 1;
	}
	else
	{
		afficherConges = 0;
	}

	if ( document.getElementById('afficher_RTT').checked == true)
	{
		afficherRTT = 1;
	}
	else
	{
		afficherRTT = 0;
	}

	if ( document.getElementById('afficher_raccourcis_absences').checked == true)
	{
		afficherRaccourcisAbsences = 1;
	}
	else
	{
		afficherRaccourcisAbsences = 0;
	}

	if ( document.getElementById('afficher_jours_types').checked == true)
	{
		afficherJoursTypes = 1;
	}
	else
	{
		afficherJoursTypes = 0;
	}

	if ( document.getElementById('afficher_calcul_rapide_journee').checked == true)
	{
		afficherCalculRapideJournee = 1;
	}
	else
	{
		afficherCalculRapideJournee = 0;
	}	

	if ( document.getElementById('afficher_total_annuel').checked == true)
	{
		afficherTotalAnnuel = 1;
	}
	else
	{
		afficherTotalAnnuel = 0;
	}	


	if ( document.getElementById('renseigner_automatiquement_conge_valide').checked == true)
	{
		renseignerAutomatiquementCongeValide = 1;
	}
	else
	{
		renseignerAutomatiquementCongeValide = 0;
	}	


	if ( document.getElementById('autoriser_admin_suppr_event').checked == true)
	{
		autoriserAdminSupprEvent = 1;
	}
	else
	{
		autoriserAdminSupprEvent = 0;
	}	


	autoriser_admin_suppr_event

//renseigner_automatiquement_conge_valide

	var xhrValiderConfiguration = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
	xhrValiderConfiguration.open("GET", "valider_configuration.php?moisDepartAnneeConge="+moisDepartAnneeConge+"&nbrJoursConges="+nbrJoursConges+"&typeGestionRTT="+typeGestionRTT+"&nbrJoursRTT="+nbrJoursRTT+"&activerAxe3="+activerAxe3+"&afficherHeuresSup="+afficherHeuresSup+"&afficherCodesComptablesSelectionAxes="+afficherCodesComptablesSelectionAxes+"&afficherCodesComptablesRecap="+afficherCodesComptablesRecap+"&afficherConges="+afficherConges+"&afficherRTT="+afficherRTT+"&axe2_exclus_totaux="+axe2_exclus_totaux+"&afficherRaccourcisAbsences="+afficherRaccourcisAbsences+"&afficherJoursTypes="+afficherJoursTypes+"&moisDepartAnneeRTT="+moisDepartAnneeRTT+"&moisDepartDecompteHeures="+moisDepartDecompteHeures+"&zoneVacances="+zoneVacances+"&afficherCalculRapideJournee="+afficherCalculRapideJournee+"&afficherTotalAnnuel="+afficherTotalAnnuel+"&renseignerAutomatiquementCongeValide="+renseignerAutomatiquementCongeValide+"&autoriserAdminSupprEvent="+autoriserAdminSupprEvent, true);
	xhrValiderConfiguration.onreadystatechange = function()
	{ 
		if(xhrValiderConfiguration.readyState == 4)
		{ 
			alert(xhrValiderConfiguration.responseText);
		}
		//alert(xhr.readyState); 
	} 
	xhrValiderConfiguration.send(null);
}

function importer_vacances()
{
	
	if (confirm("Êtes vous certain de vouloir importer les nouvelles dates de vacances scolaires?\n\nCela peut prendre plusieurs minutes."))
	{
		document.getElementById('bouton_importer_vacances').value = "En cours ... ";
		document.getElementById('bouton_importer_vacances').style.backgroundColor = "red";
		var xhrImporterVacances = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente

		xhrImporterVacances.open("GET", "importer_vacances.php", false);
		xhrImporterVacances.onreadystatechange = function()
		{ 
			if(xhrImporterVacances.readyState == 4)
			{ 
				document.getElementById('bouton_importer_vacances').value = "importer";
				document.getElementById('bouton_importer_vacances').style.backgroundColor = "";			
			}
			//alert(xhr.readyState); 
		} 
		xhrImporterVacances.send(null);

		alert("Importation terminée.")
	}
}


function demarrage()
{
	change_couleur_bouton_menu_general();

	document.getElementById("tableOngletsVertCacheeAxe1").style.display = "none";
	document.getElementById("tableOngletsVertCacheeAxe2").style.display = "none";
	document.getElementById("tableOngletsVertCacheeAxe3").style.display = "none";
	document.getElementById("tableOngletsVertCacheeSection").style.display = "none";
	document.getElementById("tableOngletsVertCacheeUtilisateurs").style.display = "none";
	document.getElementById("tableOngletsVertCacheeGroupes").style.display = "none";
	
	document.getElementById("DivOngletGestionAxe2").style.display = "none";
	document.getElementById("DivOngletGestionAxe3").style.display = "none";
	document.getElementById("DivOngletGestionSection").style.display = "none";
	document.getElementById("DivOngletGestionGroupes").style.display = "none";

}
</script> 
</head>

<BODY onLoad="demarrage();">
<!-- Table donnant la mise en page globale de la page. Va jusqu'en bas -->
<table width=100% id=tableGlobale>
<tr ><td></td><td id="tableGlobale">


	<!-- Permet de se connecter à la base Mysql-->
	<?php include("connexion_base.php");

	$loginSession	=	$_SESSION['login'];

	function choixUtilisateur()
	{
		global $bdd;

		echo	'
				<select name="select_nom" id="id_select_nom">
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
			$nom				=	$donnees['nom'];
			$prenom				=	$donnees['prenom'];
			$nbrHeuresSemaine	=	$donnees['nbrHeuresSemaine'];

		echo	"<option value=$idUtilisateur>$prenom $nom</option>";

		}
		$reponse_utilisateurs->closeCursor(); // Termine le traitement de la requête

		echo '</select>';
	}

	function choixSection($section_axe, $type_axe, $idAxe)
	{
		global $bdd;
		

		if ($section_axe == "nouveau")
		{
			// On lui donne un ID du type: id_select_section_nouveau_Axe1
			$option_axe 	= 	"<option value='null'></option>";
			$modif_id		=	"_nouveau_$type_axe";
		}
		if ($section_axe != "nouveau")
		{
			// On lui donne un ID du style: id_select_section_Axe1_2
			$modif_id		=	"_$type_axe" . "_$idAxe";
		}
		
		echo	"<select name='select_section' id='id_select_section$modif_id'> $option_axe ";
		
		$sql_section	=	"SELECT * from Section ORDER BY codeSection";
		echo "$sql_section";
		try
		{
			$reponse_section = $bdd->query($sql_section) or die('Erreur SQL !<br>' .$sql_section. '<br>'. mysql_error());
		}
		catch(Exception $e)
		{
			// En cas d'erreur précédemment, on affiche un message et on arrête tout
			die('Erreur : '.$e->getMessage());
		}

		while ($donnees = $reponse_section->fetch())
		{
			$selected = "";

			$idSection		=	$donnees['idSection'];		
			$nomSection		=	$donnees['nomSection'];
			$codeSection		=	$donnees['codeSection'];

			if ($section_axe == $idSection)
			{
				$selected = "selected";
			}
		echo "<option value=$idSection $selected>$codeSection - $nomSection</option>";
		}
		$reponse_section->closeCursor(); // Termine le traitement de la requête

		echo '</select>';
	}
	
	
	
	?>
<?php include("menu_general.php") ?>

	<br/>
	<br/>
    <!-- Table permettant les bords à 3% -->
    <table border=0 width=100%>
    <tr>
    <td width=3%></td>
    <td>
	<table border=0 width=100%>
		<tr>
			<td>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertRachat">Rachat d'heures</th><td></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><td><table border=0 width="98%">
						  <tr><td></td></tr>
						  <tr>
							  <td align=center> Utilisateur: <?php choixUtilisateur(); ?> </td>
							  <td>
							      <table border=0 width=100%>
								  <tr>
								      <td align=center>Nombre d'heures à racheter: <input type="text" id="nbr_heures_a_racheter"></td>
								      <td align=right><input type=text id="bouton_valider_rachat" value="valider"  onClick="validerRachat()" /></td>
								  </tr>
							      </table>
							  </td>
						  </tr>
						  <tr><td></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>
	<br/>

	<table border=0 width=100%>
		<tr>
			<td>
				<table border="0" width="100%">
					<tr>
						<th id="ongletVertConsult">Consulter une fiche</th><td></td>
					</tr>
				</table>
				<table border="0" width="100%" id="tableOngletsVert">
					<tr><td><table border="0" width="98%">				
					      <tr><td></td></tr>
					      <tr>
						      <td align=right><input type=text id="bouton_consulter_fiche" value="valider"  onClick="javascript:document.location.href='consulter_fiche.php'" /></td>
					      </tr>
					      <tr><td></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>
	</br>

	<table border="0" width="100%">
	    <tr>
	      <td>
		      <table border="0" width="100%">
			  <tr>
				<th id="ongletVertGestionUtilisateurs" onclick=choixOnglet("utilisateurs")>Gestion utilisateurs</th>
				<th id="ongletBleuGestionGroupes" onclick=choixOnglet("groupes")>Gestion groupes</th>
				<td></td>

			  </tr>
		      </table>


<!-- GESTION DE L'ONGLET Utilisateurs -------------------------------------------------------- -->

		<div id="DivOngletGestionUtilisateurs">
     		  
     		  <table border="0" width="100%" id="tableOngletsVert" >
     		  	<tr><td><table border="0" width="98%">
					  <tr><td><br /></td></tr>
					  <tr><td></td></tr>

				    <tr>
						<th colspan="3" id="titreTableau" align=left>Créer utilisateur</th>
						<td align="center" colspan="9"> </td>
					</tr>

					  <tr>
						  <td align="center" id="titreTableau" width="5%">
							   
						  </td>

						  <td align="center" id="titreTableau" width="8%">
							  Login
						  </td>

						  <td align="center" id="titreTableau" width="14%">
							  Mot de passe
						  </td>

						  <td align="center" id="titreTableau" width="8%">
							  Nom
						  </td>

						  <td align="center" id="titreTableau" width="8%">
							  Prénom
						  </td>

						  <td align="center" id="titreTableau" width="8%">
							  h / sem
						  </td>
						  
						  <td align="center" id="titreTableau" width="8%">
							  Congés
						  </td>

						  <td align="center" id="titreTableau" width="8%">
							  RTT
						  </td>

						  <td align="center" id="titreTableau" width="8%">
							  Couleur
						  </td>

						  <td align="center" id="titreTableau" width="6%">
							  Admin
						  </td>

						  <td align="center" id="titreTableau" width="6%">
							  Activé
						  </td>

						  <td align="center" id="titreTableau" width="10%">
							 <!-- Validation -->
						  </td>
					  </tr>

					<tr>
							<td>
								
							</td>
				      		
				      		<td align=center>
				      			<label title="Login renseigné par l'utilisateur pour se connecter (il doit être unique).">				      			
									<input type='text' value="" id="login_nouvel_utilisateur">
								</label>
				      		</td>

				      		<td align=center>
							<input type='text' value="" id="password_nouvel_utilisateur">
				      		</td>

				      		<td align=center>
							<input type='text' value="" id="nom_nouvel_utilisateur">
				      		</td>
				      		
				      		<td align=center>
							<input type='text' value="" id="prenom_nouvel_utilisateur">
				      		</td>
				      		
				      		<td align=center>
				      			<label title="nombre d'heures que l'utilisateur doit effectuer chaque semaine.">
				      				<input type='text' value="" id="nbr_heures_nouvel_utilisateur">
								</label>				      		
				      		</td>

				      		<td align=center>
				      			<label title="nombre de congés annuels de l'utilisateur.">
									<input type='text' value="" id="nbr_conges">
								</label>
				      		</td>

				      		<td align=center>
				      			<label title="nombre de RTT annuels de l'utilisateur.">				      			
									<input type='text' value="" id="nbr_RTT">
								</label>							
				      		</td>				      						      		

				      		<td align=center>
							<input type='color' value="" id="couleur_nouvel_utilisateur">
				      		</td>
				      		
				      		<td align=center>
							<input type='checkbox' name='admin' 	value="" id='adminCheck_nouvel_utilisateur'><label for='nouvel_utilisateur'></label>
				      		</td>
				      		
				      		<td align=center>
							<input type='checkbox' name='active'	value="" id='activeUtilisateurCheck_nouvel_utilisateur'><label for='nouvel_utilisateur'></label>
				      		</td>
				      		
				      		<td align="right">
							<input type='text' id='bouton_créer_utilisateur' value='créer' onClick='javascript:creer_utilisateur()' >
				      		</td>
				      		
				      </tr></td>
 					  <tr><td><br></td></tr>
					      <tr>
							<th id="titreTableau" colspan="3" align="left">
								Modifier utilisateurs : 
							</th>
						      <td align=center colspan="8"><hr /></td>
						      <td align="right"><input type=text id="bouton_afficher_gestion_Utilisateurs" value="afficher"  onClick="javascript:afficher_gestion('Utilisateurs')" /></td>
					      </tr>
				  	  </table></td></tr>

				      <tr><td><table id="tableOngletsVertCacheeUtilisateurs" width="100%" border="0">


  				      <tr><td></td></tr>

					  <tr>
						  <th id="titreTableau" width="5%">
							  ID
						  </th>

						  <th id="titreTableau" width="8%">
							  Login
						  </th>

						  <th id="titreTableau" width="14%">
							  Mot de passe
						  </th>

						  <th id="titreTableau" width="8%">
							  Nom
						  </th>

						  <th id="titreTableau" width="8%">
							  Prénom
						  </th>

						  <th id="titreTableau" width="8%">
							  h / sem
						  </th>

						  <th id="titreTableau" width="8%">
							  Congés
						  </th>

						  <th id="titreTableau" width="8%">
							  RTT
						  </th>						  
						  <th id="titreTableau" width="8%">
							  Couleur
						  </th>

						  <th id="titreTableau" width="6%">
							  Admin
						  </th>

						  <th id="titreTableau" width="6%">
							  Activé
						  </th>

						  <th id="titreTableau" width="10%">
							  Validation
						  </th>
						  <th width="2%">

						  </th>
					  </tr>

					  <tr>
						  <?php

							  session_start();

							  $sql = "SELECT * FROM Utilisateurs ORDER BY Nom" ;
							  try
							  {
								  $reponse_login = $bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
							  }
							  catch(Exception $e)
							  {
								  // En cas d'erreur précédemment, on affiche un message et on arrête tout
								  die( 'Erreur au moment du login : '.$e->getMessage() );
							  }


							  while ($donnees = $reponse_login->fetch())
							  {
								  $idUtilisateur			=	$donnees['idUtilisateurs'];
								  $nom					=	$donnees['nom'];
								  $prenom				=	$donnees['prenom'];
								  $nbr_heures				=	$donnees['nbrHeuresSemaine'];
								  $nbrConges 			=	$donnees['nbrConges'];
								  $nbrRTT 				=	$donnees['nbrRTT'];
								  $login				=	$donnees['login'];
								  $admin				=	$donnees['admin'];
								  $active				=	$donnees['active'];
								  $couleur				=	"#" . $donnees['couleur'];


									  if ($admin == 1) 
									  {
										  $checkedAdmin = 'checked="checked"';				
									  }
									  else
									  {
										  $checkedAdmin = '';
									  }


									  if ($active == 1) 
									  {
										  $checkedActive = 'checked="checked"';				
									  }
									  else
									  {
										  $checkedActive = '';
									  }

									  echo "<tr>
											  <td align=center>$idUtilisateur</td>
											  <td align=center><input type=text value='$login' id=login_$idUtilisateur></td>
  											  <td align=center><input type='text' id='bouton_modifier_password' value='réinitialiser' onClick='javascript:reset_password( \"$idUtilisateur\", \"$nom\", \"$prenom\", \"$login\", \"$nom\", \"$prenom\" )' /></td>
											  <td align=center><input type=text value='$nom' id=nom_$idUtilisateur></td>
											  <td align=center><input type=text value='$prenom' id=prenom_$idUtilisateur></td>
											  <td align=center><input type=text value='$nbr_heures' id=nbr_heures_$idUtilisateur></td>
											  <td align=center><input type=text value='$nbrConges' id=nbrConges_$idUtilisateur></td>
											  <td align=center><input type=text value='$nbrRTT' id=nbrRTT_$idUtilisateur></td>
											  <td align=center><input type=color value=$couleur id=couleur_$idUtilisateur></td>
											  <td align=center><input type='checkbox' name='admin' 	value=$idUtilisateur id='adminCheck$idUtilisateur' $checkedAdmin><label for='$idUtilisateur'></label><br></td>
											  <td align=center><input type='checkbox' name='active'	value=$idUtilisateur id='activeUtilisateurCheck$idUtilisateur' $checkedActive><label for='$idUtilisateur'></label><br></td>
											  <td align=center><input type='text' id='bouton_modifier_utilisateur' value='modifier' onClick='javascript:modifier_utilisateur( \"$idUtilisateur\" , \"$nom\", \"$prenom\", \"$nbr_heures\", \"$nbrConges\", \"$nbrRTT\", \"$couleur\", \"$login\" )' /></td>
											  <td></td>
										</tr>";

							  }
						  ?>
					  </tr>

				      <tr><td><br></td></tr>


				 	</td></tr></table>

		      </table>
		    </div>


<!-- GESTION DE L'ONGLET Groupes -------------------------------------------------------- -->

		<div id="DivOngletGestionGroupes">
     		  
     		  <table border="0" width="100%" id="tableOngletsVert" >
     		  	<tr><td><table border="0" width="98%">
					  <tr><td><br /></td></tr>
					  <tr><td></td></tr>

				    <tr>
						<th colspan="2" id="titreTableau" align=left>Créer groupe :</th>

			      		<td align=center width="50%">
							Nom du groupe : <input type='text' value="" id="choix_nom_nouveau_groupe">
			      		</td>

			      		<td align="right" width="20%">
							<input type='text' id='bouton_créer_groupe' value='créer' onClick='javascript:creer_groupe()' >
			      		</td>
				      		
				      </tr></td>
 					  <tr><td><br></td></tr>
					      <tr>
							<th id="titreTableau" colspan="2" align="left">
								Modifier groupes : 
							</th>
						      <td align=center colspan="1"><hr /></td>
						      <td align="right"><input type=text id="bouton_afficher_gestion_Groupes" value="afficher"  onClick="javascript:afficher_gestion('Groupes')" /></td>
					      </tr>
				  	  </table></td></tr>

				      <tr><td><table id="tableOngletsVertCacheeGroupes" width="100%" border="0">


  				      <tr><td></td></tr>

					  <tr>
						  <th id="titreTableau" width="30%">
							  Nom du groupe
						  </th>

						  <th id="titreTableau" width="50%">
							  Choix utilisateurs
						  </th>

						  <th id="titreTableau" width="20%">
							  Validation
						  </th>
					  </tr>

					  <tr>
						  <?php

							  session_start();

							  $sql = "SELECT * FROM Groupes ORDER BY Nom" ;
							  try
							  {
								  $reponse_login = $bdd->query($sql) or die('Erreur SQL !<br>' .$sql. '<br>'. mysql_error());
							  }
							  catch(Exception $e)
							  {
								  // En cas d'erreur précédemment, on affiche un message et on arrête tout
								  die( 'Erreur au moment du login : '.$e->getMessage() );
							  }


							  while ($donnees = $reponse_login->fetch())
							  {
								  $idGroupe				=	$donnees['id'];
								  $nom					=	$donnees['nom'];
								  $utilisateurs			=	$donnees['idUtilisateurs'];


								  echo "<tr>
											<td align=center><input type='text' value=$nom id='choix_nom_groupe_$idGroupe'></td>
											<td align=center><input type='text' id='bouton_configurer_groupe' value='choisir' onClick='javascript:configurer_groupe(\"$idGroupe\", \"$nom\")' /></td>
											<td align=center><input type='text' id='bouton_modifier_groupe' value='modifier' onClick='javascript:modifier_groupe(\"$idGroupe\")' /></td>
									</tr>";

							  }
						  ?>
					  </tr>

				      <tr><td><br></td></tr>


				 	</td></tr></table>

		      </table>
		    </div>



	    </tr>
	</table>
	
	<br/>


	<table border=0 width=100%>
		<tr>
			<td>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertGestionAxe1" onclick=choixOnglet("axe1")>Gestion axes 1</th>
						<th id="ongletBleuGestionAxe2" onclick=choixOnglet("axe2")>Gestion axes 2</th>
						<th id="ongletBleuGestionAxe3" onclick=choixOnglet("axe3")>Gestion axes 3</th>
						<th id="ongletBleuGestionSection" onclick=choixOnglet("section")>Gestion section</th>
						<td></td>

					</tr>
				</table>
				
<!-- GESTION DE L'ONGLET AXE 1 -------------------------------------------------------- -->
				
				<div id="DivOngletGestionAxe1">
				<table border="0" width="100%" id="tableOngletsVert">
					<tr><td><table border="0" width="98%">				
					      <tr><td></td></tr>
       					      <tr><td><br></td></tr>

					      <tr>
							<th id="titreTableau" width=20%>
								Créer un axe 1: 
							</th>
						      
						      <td align=center>Code: <input type=text id="code_Axe1_nouveau" value="" /></td>
						      <td align=center>Nom: <input type=text id="nom_Axe1_nom_nouveau" value="" /></td>
						      <td align=center>Section: <?php choixSection("nouveau", "Axe1");?></td>
      						  <td align=right><input type=text id="bouton_creer_axe1" value="valider"  onClick="javascript:creerAxe('Axe1')" /></td>

					      </tr>
     					      <tr><td><br></td></tr>
					      <tr>
							<th id="titreTableau" width=20%>
								Modifier les axes 1: 
							</th>
						      <td align=center colspan=3><hr /></td>
						      <td align=right><input type=text id="bouton_afficher_gestion_Axe1" value="afficher"  onClick="javascript:afficher_gestion('Axe1')" /></td>
					      </tr>
					      <tr><td></td></tr>
					</table></td></tr>
					<tr><td><table border=0 width=100% id="tableOngletsVertCacheeAxe1">				
					      <tr><td></td></tr>
					      
						<tr>
							<th id="titreTableau" width=10%>
							  ID
							</th>
							<th id="titreTableau" width=20%>
							  Code comptable
							</th>
							<th id="titreTableau">
							  Nom de l'axe 1
							</th>
							<th id="titreTableau" width=20%>
								Section
							</th>
							<th id="titreTableau" width=20%>
							  Validation
							</th>
						</tr>
						
						<tr><td>
							<?php
							// On fait une boucle "while" pour afficher chaque axe1.
							$reponse_axe1 = $bdd->query('SELECT idAxe1, codeAxe1, nomAxe1, Section_idSection, nomSection FROM Axe1, Section WHERE Axe1.Section_idSection = Section.idSection ORDER BY codeAxe1');
							
							$i=1;
							while ($donnees_axe1 = $reponse_axe1->fetch())
								{
									$code_axe1	=	$donnees_axe1['codeAxe1'];
									$nom_axe1	=	$donnees_axe1['nomAxe1'];
									$id_axe1	=	$donnees_axe1['idAxe1'];
									$id_section	=	$donnees_axe1['Section_idSection'];
									$section_axe1	=	$donnees_axe1['nomSection'];
				
										echo "	<tr>
											<td align=center>$id_axe1</td>
											<td align=center><input type=text value=\"$code_axe1\" id=code_Axe1_$id_axe1></td>
											<td align=center><input type=text value=\"$nom_axe1\" id=nom_Axe1_$id_axe1></td>
											<td align=center>";choixSection($id_section, "Axe1", $id_axe1); echo "</td>
											<td align=center><input type='text' id='bouton_modifier_axe1' value='modifier' onClick='javascript:modifier_axe(\"Axe1\", $id_axe1)' /></td>
										</tr>";

								}
							$reponse_axe1->closeCursor();
							?>
						</td></tr>
							

					
						

					      
					      <tr><td><br></td></tr>
					      
					      <tr>
						      <td colspan=4></td><td align=center><input type=text id="bouton_afficher_gestion_Axe1" value="masquer"  onClick="javascript:afficher_gestion('Axe1')" /></td>
					      </tr>
					      
					<tr><td></td></tr>      
					</table></td></tr>
				</table>
				
				
				
				
				</div>
				
				
<!-- GESTION DE L'ONGLET AXE 2 -------------------------------------------------------- -->

				<div id="DivOngletGestionAxe2">
				<table border=0 width=100% id="tableOngletsVert">
					<tr><td><table border=0 width=98%>				
					      <tr><td></td></tr>
       					      <tr><td><br></td></tr>

					      <tr>
							<th id="titreTableau" width=20%>
								Créer un axe 2: 
							</th>
						      
						      <td align=center>Code: <input type=text id="code_Axe2_nouveau" value="" /></td>
						      <td align=center>Nom: <input type=text id="nom_Axe2_nom_nouveau" value="" /></td>
      						      <td align=center>Section: <?php choixSection("nouveau", "Axe2");?></td>
      						      <td align=right><input type=text id="bouton_creer_axe2" value="valider"  onClick="javascript:creerAxe('Axe2')" /></td>

					      </tr>
     					      <tr><td><br></td></tr>
					      <tr>
							<th id="titreTableau" width=20%>
								Modifier les axes 2: 
							</th>
						      <td align=center colspan=3><hr /></td>
						      <td align=right><input type=text id="bouton_afficher_gestion_Axe2" value="afficher"  onClick="javascript:afficher_gestion('Axe2')" /></td>
					      </tr>
					      <tr><td></td></tr>
					</table></td></tr>
					<tr><td><table border=0 width=100% id="tableOngletsVertCacheeAxe2">				
					      <tr><td></td></tr>
					      
						<tr>
							<th id="titreTableau" width=10%>
							  ID
							</th>
							<th id="titreTableau" width=20%>
							  Code comptable
							</th>
							<th id="titreTableau">
							  Nom de l'axe 2
							</th>
							<th id="titreTableau" width=20%>
								Section
							</th>
							<th id="titreTableau" width=20%>
							  Validation
							</th>
						</tr>
						
						<tr><td>
							<?php
							// On fait une boucle "while" pour afficher chaque axe2.
							$reponse_axe2 = $bdd->query('SELECT idAxe2, codeAxe2, nomAxe2, Section_idSection, nomSection FROM Axe2, Section WHERE Axe2.Section_idSection = Section.idSection ORDER BY codeAxe2');
							
							$i=1;
							while ($donnees_axe2 = $reponse_axe2->fetch())
								{
									$code_axe2	=	$donnees_axe2['codeAxe2'];
									$nom_axe2	=	$donnees_axe2['nomAxe2'];
									$id_axe2	=	$donnees_axe2['idAxe2'];
									$id_section	=	$donnees_axe2['Section_idSection'];
									$section_axe2	=	$donnees_axe2['nomSection'];
				
									echo "	<tr>
											<td align=center>$id_axe2</td>
											<td align=center><input type=text value=\"$code_axe2\" id=code_Axe2_$id_axe2></td>
											<td align=center><input type=text value=\"$nom_axe2\" id=nom_Axe2_$id_axe2></td>
											<td align=center>";choixSection($id_section, "Axe2", $id_axe2); echo "</td>
											<td align=center><input type='text' id='bouton_modifier_axe2' value='modifier' onClick='javascript:modifier_axe(\"Axe2\", $id_axe2)' /></td>
										</tr>";

								}
							$reponse_axe2->closeCursor();
							?>
						</td></tr>
							

					
						

					      
					      <tr><td><br></td></tr>
					      
					      <tr>
						      <td colspan=4></td><td align=center><input type=text id="bouton_afficher_gestion_Axe2" value="masquer"  onClick="javascript:afficher_gestion('Axe2')" /></td>
					      </tr>
					      
					<tr><td></td></tr>      
					</table></td></tr>
				</table>
				</div>
				
<!-- GESTION DE L'ONGLET AXE 3 -------------------------------------------------------- -->
				
				<div id=DivOngletGestionAxe3 >
				<table border=0 width=100% id="tableOngletsVert">
					<tr><td><table border=0 width=98%>				
					      <tr><td></td></tr>
       					      <tr><td><br></td></tr>

					      <tr>
							<th id="titreTableau" width=20%>
								Créer un axe 3: 
							</th>
						      
      						    <td align=center>Code: <input type=text id="code_Axe3_nouveau" value="" /></td>
						      	<td align=center>Nom: <input type=text id="nom_axe3_nom_nouveau" value="" /></td>
      						    <td align=right colspan=2><input type=text id="bouton_creer_axe3" value="valider"  onClick="javascript:creerAxe3()" /></td>

					      </tr>
     					      <tr><td><br></td></tr>
					      <tr>
							<th id="titreTableau" width=20%>
								Modifier les axes 3: 
							</th>
						      <td align=center colspan=3><hr />  </td>
						      <td align=right><input type=text id="bouton_afficher_gestion_Axe3" value="afficher"  onClick="javascript:afficher_gestion('Axe3')" /></td>
					      </tr>
					      <tr><td></td></tr>
					</table></td></tr>
					<tr><td><table border=0 width=100% id="tableOngletsVertCacheeAxe3">				
					      <tr><td></td></tr>
					      
						<tr>
							<th id="titreTableau" width=10%>
							
							</th>
							<th id="titreTableau" width=20%>
							  ID
							</th>
							<th id="titreTableau" width=20%>
							  Code comptable
							</th>
							<th id="titreTableau">
							  Nom de l'axe3
							</th>
							<th id="titreTableau" width=5%>
							  Activé
							</th>
							<th id="titreTableau" width=20%>
							  Validation
							</th>
						</tr>
						
						<tr><td>
							<?php
							// On fait une boucle "while" pour afficher chaque axe3.
							$reponse_axe3 = $bdd->query('SELECT * FROM Axe3 ORDER BY codeAxe3, nomAxe3');
							
							$i=1;
							while ($donnees_axe3 = $reponse_axe3->fetch())
								{
									$code_axe3	=	$donnees_axe3['codeAxe3'];
									$nom_axe3	=	$donnees_axe3['nomAxe3'];
									$id_axe3	=	$donnees_axe3['idAxe3'];
									$active		=	$donnees_axe3['active'];
																		
									  if ($active == 1) 
									  {
										  $checkedActive = 'checked="checked"';				
									  }
									  if ($active == 0)
									  {
										  $checkedActive = '';
									  }
									
								
									echo "	<tr>
											<td></td>
											<td align=center>$id_axe3</td>
											<td align=center><input type=text value=\"$code_axe3\" id=code_Axe3_$id_axe3></td>
											<td align=center><input type=text value=\"$nom_axe3\" id=nom_axe3_$id_axe3></td>
											<td align=center><input type='checkbox' name='active' 	value=$id_axe3 id='activeAxe3Check$id_axe3' $checkedActive><label for='$id_axe3'></label><br></td>
											<td align=center><input type='text' id='bouton_modifier_axe3' value='modifier' onClick='javascript:modifier_axe3($id_axe3)' /></td>
										</tr>";

								}
							$reponse_axe3->closeCursor();
							?>
						</td></tr>
							
							
					
						

					      
					      <tr><td><br></td></tr>
					      
					      <tr>
						      <td colspan=5></td><td align=center><input type=text id="bouton_afficher_gestion_Axe3" value="masquer"  onClick="javascript:afficher_gestion('Axe3')" /></td>
					      </tr>
					      
					<tr><td></td></tr>      
					</table></td></tr>
				</table>
				
				
				</div>

<!-- GESTION DE L'ONGLET SECTION -------------------------------------------------------- -->
				<div id="DivOngletGestionSection">
				<table border=0 width=100% id="tableOngletsVert">
					<tr><td><table border=0 width=98%>				
					      <tr><td></td></tr>
       					      <tr><td><br></td></tr>

					      <tr>
							<th id="titreTableau" width=20%>
								Créer une section: 
							</th>
						      
						      <td align=center>Code: <input type=text id="code_Section_nouveau" value="" /></td>
						      <td align=center>Nom: <input type=text id="nom_Section_nom_nouveau" value="" /></td>
      						      <td align=right><input type=text id="bouton_creer_section" value="valider"  onClick="javascript:creerAxe('Section')" /></td>

					      </tr>
     					      <tr><td><br></td></tr>
					      <tr>
							<th id="titreTableau" width=20%>
								Modifier les sections: 
							</th>
						      <td align=center colspan=2><hr /></td>
						      <td align=right><input type=text id="bouton_afficher_gestion_Section" value="afficher"  onClick="javascript:afficher_gestion('Section')" /></td>
					      </tr>
					      <tr><td></td></tr>
					</table></td></tr>
					<tr><td><table border=0 width=100% id="tableOngletsVertCacheeSection">				
					      <tr><td></td></tr>
					      
						<tr>
							<th id="titreTableau" width=10%>
							  ID
							</th>
							<th id="titreTableau" width=20%>
							  Code comptable
							</th>
							<th id="titreTableau">
							  Nom de la section
							</th>
							<th id="titreTableau" width=20%>
							  Validation
							</th>
						</tr>
						
						<tr><td>
							<?php
							// On fait une boucle "while" pour afficher chaque section.
							$reponse_section = $bdd->query('SELECT * from Section ORDER BY codesection');
							
							$i=1;
							while ($donnees_section = $reponse_section->fetch())
								{
									$code_section	=	$donnees_section['codeSection'];
									$nom_section	=	$donnees_section['nomSection'];
									$id_section	=	$donnees_section['idSection'];
				
									echo "	<tr>
											<td align=center>$id_section</td>
											<td align=center><input type=text value=\"$code_section\" id=code_Section_$id_section></td>
											<td align=center><input type=text value=\"$nom_section\" id=nom_Section_$id_section></td>
											<td align=center><input type='text' id='bouton_modifier_section' value='modifier' onClick='javascript:modifier_axe(\"Section\", $id_section)' /></td>
										</tr>";

								}
							$reponse_section->closeCursor();
							?>
						</td></tr>
							

					
						

					      
					      <tr><td><br></td></tr>
					      
					      <tr>
						      <td colspan=3></td><td align=center><input type=text id="bouton_afficher_gestion_Section" value="masquer"  onClick="javascript:afficher_gestion('Section')" /></td>
					      </tr>
					      
					<tr><td></td></tr>      
					</table></td></tr>
				</table>
				</div>				
				
			</td>
		</tr>
	</table>

	<br />

	<table border=0 width=100%>
		<tr>
			<td>
				<table border=0 width=100%>
					<tr>
						<th id="ongletVertConfiguration">Configuration</th><td></td>
					</tr>
				</table>
				<table border=0 width=100% id="tableOngletsVert">
					<tr><td><table border=0 width=98% cellspacing=0px cellpadding=5px>				
					      <tr><td></td></tr>
					    
					      <?php require("importer_configuration.php"); ?> 
					      
<!------------------------------------------- -->
					      <tr>
					      	<td width=16%></td>
						<th id="titreTableau">
								Configuration générale.
						</th>
					      	<td align=center>
					      	</td>
					      </tr>
					      
					      <tr>
					      	<td></td>					      	
					      	<td align=left>
					      		Activer l'axe 3.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='activerAxe3' 	value="" id='activerAxe3' <?php echo $activerAxe3 ?> onClick='javascript:alert("Si vous désactivez l utilisation de l axe 3, le axe3 renseigné par défaut sera le axe3 ayant comme id: 1.")'>
					      	</td>
					      	<td></td>					      	
					      </tr>					      
					      
					      <tr>
					      	<td></td>					      	
					      	<td align=left>
					      		Codes comptables "Axe2" à exclure des totaux horaires (pour exclure les absences par ex.).<br />
					      		Exemple de liste: 9901,9902,9907
					      	</td>
					      	<td align=center>
					      		<input type="text" name="axe2_exclus_totaux" id="axe2_exclus_totaux" value="<?php echo $axe2_exclus_totaux ?>">
					      	</td>
					      	<td></td>					      	
					      </tr>					      
					      
					      
					      
					      
<!------------------------------------------- -->
					      <tr><td></td></tr>
					      <tr>
					      	<td></td>
						<th id="titreTableau">
								Configuration des absences.
						</th>
					      	<td align=center>
					      	</td>
					      </tr>
					      
					      <tr>
					      	<td></td>
					      	<td align=left width=60%>
					      		Mois de départ de l'année comptable concernant les congés (exemple: 06 pour juin).
					      	</td>
					      	<td align=center>
					      		<input type="text" id="moisDepartAnneeConge" value="<?php echo $moisDepartAnneeConge ?>">
					      	</td>
					      	<td></td>					  
					      </tr>					      
					      
					      <tr>
					      	<td></td>
					      	<td align=left width=60%>
					      		Nombre de jours de congés annuel.
					      	</td>
					      	<td align=center>
					      		<input type="text" id="nbr_jours_conges" value="<?php echo $nbrJoursConges ?>">
					      	</td>
					      	<td></td>					  
					      </tr>					      
					      					      
					      <tr>
					      	<td></td>					   
					      	<td align=left>
					      		Période de gestion des RTT.
					      	</td>
						      	<?php
						      		$selectedAnnee = "";
						      		$selectedTrimestre = "";
						      		$selectedRien = "";

						      		if ($typeGestionRTT == "Année")
						      		{
						      			$selectedAnnee = 'selected="selected"';
						      		}
						      		if ($typeGestionRTT == "Trimestre")
						      		{
						      			$selectedTrimestre = 'selected="selected"';
						      		}
						      		else
						      		{
						      			$selectedRien = 'selected="selected"';
						      		}

						      	?>
					      	<td align=center>
								<select name="select_type_gestion_RTT" id="id_select_type_gestion_RTT">
									<option <?php echo $selectedRien ?> value="rien"></option>
									<option <?php echo $selectedAnnee ?> value="Année">Année</option>
									<option <?php echo $selectedTrimestre ?> value="Trimestre">Trimestre</option>
								</select>
					      	<td></td>					      	
					      </tr>					      
					      
					      <tr>
					      	<td></td>
					      	<td align=left width=60%>
					      		Mois de départ de l'année comptable concernant les RTT (exemple: 06 pour juin).
					      	</td>
					      	<td align=center>
					      		<input type="text" id="moisDepartAnneeRTT" value="<?php echo $moisDepartAnneeRTT ?>">
					      	</td>
					      	<td></td>					  
					      </tr>
					      
					      <tr>
					      	<td></td>					   
					      	<td align=left>
					      		Mois de départ du décompte d'heures annuel.
					      	</td>
					      	<td align=center>
					      		<input type="text" id="moisDepartDecompteHeures" value="<?php echo $moisDepartDecompteHeures ?>">
					      	</td>
					      	<td></td>					      	
					      </tr>	

					      <tr>
					      	<td></td>					   
					      	<td align=left>
					      		Nombre de jours de RTT annuel.
					      	</td>
					      	<td align=center>
					      		<input type="text" id="nbr_jours_RTT" value="<?php echo $nbrJoursRTT ?>">
					      	</td>
					      	<td></td>					      	
					      </tr>
					      				      
					      
					      
					      
					      
<!------------------------------------------- -->
					      <tr><td></td></tr>
					      <tr>
					      	<td></td>
						<th id="titreTableau">
								Configuration d'affichage.
						</th>
					      	<td align=center>
					      	</td>
					      </tr>

					      <tr>
					      	<td></td>					      	
					      	<td align=left>
					      		Activer l'affichage des jours de RTT restants.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='afficher_RTT' 	value="" id='afficher_RTT' <?php echo $afficherRTT ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>
					      
					      <tr>
					      	<td></td>					      	
					      	<td align=left>
					      		Activer l'affichage des jours de congés restants.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='afficher_conges' 	value="" id='afficher_conges' <?php echo $afficherConges ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>
					      
					      <tr>
					      	<td></td>					      	
					      	<td align=left>
					      		Activer l'affichage des heures supplémentaires.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='afficher_heures_sup' 	value="" id='afficher_heures_sup' <?php echo $afficherHeuresSup ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>

					      <tr>
					      	<td></td>					      	
					      	<td align=left>
					      		Activer l'affichage des codes comptables dans les fenêtres de sélection des axes.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='afficher_codes_comptables_axes' 	value="" id='afficher_codes_comptables_axes' <?php echo $afficherCodesComptablesSelectionAxes ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>

					   	<tr>
					      	<td></td>					      	
					      	<td align=left>
					      		Activer l'affichage des codes comptables dans le récapitulatif.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='afficher_codes_comptables_recap' 	value="" id='afficher_codes_comptables_recap' <?php echo $afficherCodesComptablesRecap ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>

					      <tr>
					      	<td></td>					      	
					      	<td align=left>
							Activer l'affichage du menu permettant le renseignement rapide des absences.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='afficher_raccourcis_absences' 	value="" id='afficher_raccourcis_absences' <?php echo $afficherRaccourcisAbsences ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>

					      <tr>
					      	<td></td>					      	
					      	<td align=left>
							Activer l'affichage du menu permettant le renseignement et la suppression de jours types.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='afficher_jours_types' 	value="" id='afficher_jours_types' <?php echo $afficherJoursTypes ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>

					      <tr>
					      	<td></td>					      	
					      	<td align=left>
							Activer l'affichage du total journalier "pré-validation" (ne prend pas en compte l'exclusion d'axes).
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='afficher_calcul_rapide_journee' 	value="" id='afficher_calcul_rapide_journee' <?php echo $afficherCalculRapideJournee ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>

					      <tr>
					      	<td></td>					      	
					      	<td align=left>
							Activer l'affichage du total annuel sur la fiche d'heures.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='afficher_total_annuel' 	value="" id='afficher_total_annuel' <?php echo $afficherTotalAnnuel ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>
<!------------------------------------------- -->
					      <tr><td><br /></td></tr>
					      <tr>
					      	<td></td>
						<th id="titreTableau">
								Configuration du calendrier.
						</th>
					      	<td align=center>
					      	</td>
					      </tr>


					      <tr>
					      	<td></td>	
					      		<?php
						      		$sql_plus_vieille_date_vacances	=	"SELECT MAX(dateFin) as vieille from zonesVacances";
									//echo "$sql_plus_vieille_date_vacances";
									try
									{
										$reponse_plus_vieille_date_vacances = $bdd->query($sql_plus_vieille_date_vacances) or die('Erreur SQL !<br>' .$sql_plus_vieille_date_vacances. '<br>'. mysql_error());
									}
									catch(Exception $e)
									{
										// En cas d'erreur précédemment, on affiche un message et on arrête tout
										die('Erreur : '.$e->getMessage());
									}

									while ($donnees = $reponse_plus_vieille_date_vacances->fetch())
									{
										$plusvieilleDateVacances		=	$donnees['vieille'];

										$plusvieilleDateVacances 		=	date("d/m/Y", strtotime($plusvieilleDateVacances));	
									}
									$reponse_plus_vieille_date_vacances->closeCursor(); // Termine le traitement de la requête

								?>			      	
					      	<td align=left>
								Importer les dernières dates de vacances (la version actuelle contient les dates juqu'au: <?php echo "$plusvieilleDateVacances" ?>)	
					      	</td>
					      	<td align=center>
								<input type=text id="bouton_importer_vacances" value="importer"  onClick="javascript:importer_vacances()" />	
					      	</td>
					      	<td></td>					      	
					      </tr>


					      <tr>
					      	<td></td>					      	
					      	<td align=left>
					      		Zone  de vacances scolaires.
					      	</td>
					      	<td align=center><?php
								echo	"<select name='select_zone' id='select_zone'>";
		
								$sql_zone	=	"SELECT distinct(zone) from zonesVacances ORDER BY zone";
								//echo "$sql_zone";
								try
								{
									$reponse_zone = $bdd->query($sql_zone) or die('Erreur SQL !<br>' .$sql_zone. '<br>'. mysql_error());
								}
								catch(Exception $e)
								{
									// En cas d'erreur précédemment, on affiche un message et on arrête tout
									die('Erreur : '.$e->getMessage());
								}

								while ($donnees = $reponse_zone->fetch())
								{
									$selected = "";

									$zone		=	$donnees['zone'];		

									if ($zoneVacancesConfig == $zone)
									{
										$selected = "selected";
									}
								echo "<option value=$zone $selected>$zone</option>";
								}
								$reponse_zone->closeCursor(); // Termine le traitement de la requête

								echo '</select>';
							?>
					      	</td>
					      	<td></td>					      	
					      </tr>

					      <tr>
					      	<td></td>					      	
					      	<td align=left>
							Activer le remplissage automatique de la fiche d'heure quand une demande d'absence est validée.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='renseigner_automatiquement_conge_valide' 	value="" id='renseigner_automatiquement_conge_valide' <?php echo $renseignerAutomatiquementCongeValide ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>

					      <tr>
					      	<td></td>					      	
					      	<td align=left>
							Autoriser les administrateurs à supprimer des événements dans le calendrier.
					      	</td>
					      	<td align=center>
								<input type='checkbox' name='autoriser_admin_suppr_event' 	value="" id='autoriser_admin_suppr_event' <?php echo $autoriserAdminSupprEvent ?> >
					      	</td>
					      	<td></td>					      	
					      </tr>
<!-- 
					      <tr>
					      	<td align=center>
					      		Mois de remise à zero du compteur de congés (exemple: 06).
					      	</td>
					      	<td align=left>
					      		<input type="text" id="mois_raz_conge">
					      	</td>
					      </tr>					      

					      <tr>
					      	<td align=center>
					      		Coefficient de majoration du samedi.
					      	</td>
					      	<td align=left>
					      		<input type="text" id="coef_samedi">
					      	</td>
					      </tr>

					      <tr>
					      	<td align=center>
					      		Coefficient de majoration du dimanche.
					      	</td>
					      	<td align=left>
					      		<input type="text" id="coef_dimanche">
					      	</td>
					      </tr>

					      <tr>
					      	<td align=center>
					      		Coefficient de majoration des jours fériés.
					      	</td>
					      	<td align=left>
					      		<input type="text" id="coef_jours_feries">
					      	</td>
					      </tr> -->
					      					      					      
					      <tr>
						      <td></td>
					      	<td></td>
					      	<td></td>

						      <td align=right><input type=text id="bouton_valider_configuration" value="valider"  onClick="javascript:valider_configuration()" /></td>
					      </tr>
					      <tr><td></td></tr>
					</table></td></tr>
				</table>
			</td>
		</tr>
	</table>

	<br>



    <!-- Fin de la table permettant les bords à 3% --> 
    </td>
    <td width=3%></td>
    </tr></table>
	
<!-- Fin de la table de mise en page globale -->
</td><td></td></tr>
</table>
</body>
