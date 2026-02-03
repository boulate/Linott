#!/bin/bash

# Petit script bash permettant une mise à jour automatique du logiciel Linott.

cd /var/www/Linott


if ping -c 1 infolibre-dijon.fr

	then 	adresse="infolibre-dijon.fr"
		echo "Votre DNS semble correctement configuré, nous allons utiliser l'adresse $adresse."

elif ping -c 1 88.191.149.39
	
	then 	adresse="88.191.149.39"
		echo "Il semble que vous ayez un problème de DNS, nous allons donc utiliser l'adresse $adresse."
	

fi

wget http://$adresse/Linott/maj.sh

chmod +x ./maj.sh

bash ./maj.sh

rm ./maj.sh

