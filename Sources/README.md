# README #

### Qu'est ce que Linott? ###
Linott est un logiciel de comptabilité analytique des heures. Il vous permet de suivre le temps exacte que vous a couté un projet (par employé, par thèmes, etc.)

### A savoir: ###
- Ce logiciel peut supporter plusieurs centaines de connexions simultanées (et bien plus, selon la puissance de votre serveur).
- Les performances générales du logiciel et sa réactivité sont directement liées à la puissance de votre serveur mais peuvent aussi être influencées par la puissance de la machine cliente.
- Ce logiciel est certifié sur les versions récentes des navigateurs "Mozilla Firefox" et "Iceweasel". Un portage vers "Chrome", "Chromium" et "Safari" est en cours. Aucun portage vers "Internet Explorer" n'est prévu pour le moment.
- Configuration minimum recommandée pour le serveur: Pentium 4 1.6Ghz, 128Mo RAM, 10Go de disque dur, carte réseau 10Mb/s 

### ATTENTION! ###
Pour simplifier la gestion des absences, de nombreuses automatisations ont été mises en place.
Pour que ces automatisations fonctionnent correctement, certaines contraintes de nommage doivent être respectées:

- Le code comptable de l'axe1 "Congés et autres absences" doit obligatoirement être "50".
- Le libélé du code comptable "5000" de l'axe 2 doit être "Absences".
- L'axe 2 gérant les congés doit se nommer "Congé".
- L'axe 2 gérant les RTT doit se nommer "RTT".
- L'axe 2 gérant les récupérations doit se nommer "Récupération".

### Versions ###
Vous pouvez consulter les changements de versions de ce logiciel en suivant ce lien: [changelog.php](http://infolibre-dijon.fr/Linott/changelog.php)

### Comment installer ###

#### Nouvelle installation: ####
`git clone https://boulate@bitbucket.org/boulate/linott.git`

Adapter les fichiers suivants à vos besoins:
	* Configuration/bdd.php
	* Configuration/menu.php


#### Mise à jour d'une installation existante: ####
`git pull origin master`