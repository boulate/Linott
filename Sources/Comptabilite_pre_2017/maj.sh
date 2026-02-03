#!/bin/bash
if [ $(id -u) -ne 0 ]
then echo "Vous devez être root pour lancer ce programme."
	exit 0
fi

date=`date +%Y-%m-%d_%Hh%M`
logiciel="Linott"

if ping -c 1 infolibre-dijon.fr
	then 	adresse="infolibre-dijon.fr"
		echo "Votre DNS semble correctement configuré, nous allons utiliser l'adresse $adresse."
elif ping -c 1 88.191.149.39	
	then 	adresse="88.191.149.39"
		echo "Il semble que vous ayez un problème de DNS, nous allons donc utiliser l'adresse $adresse."
fi

if [ -d /var/www/Compta ]
	then 	mv /var/www/Compta /var/www/Linott
	else	"Je n'ai pas trouvé de dossier Compta. Je ne peux donc pas le changer en Linott."	
fi

if [ ! -d /var/www/Sauvegardes/ ]
	then mkdir /var/www/Sauvegardes/
	else echo "Le repertoire Sauvegardes existe déjà. Nous pouvons continuer."
fi	

cd /var/www/Linott

mkdir "/var/www/Sauvegardes/$logiciel"_"$date/"
mv /var/www/Linott/* "/var/www/Sauvegardes/$logiciel"_"$date/"

wget http://$adresse/Linott/Download/Linott-latest.tar.gz

tar xfzv Linott-latest.tar.gz
#cp -a "/var/www/Sauvegardes/$logiciel"_"$date/Logo_Linott.png" ./

chown -R www-data:www-data /var/www/Linott
chmod -R 770 /var/www/Linott

rm /var/www/Linott/Linott-latest.tar.gz


if uname -r | grep amd64
then
	cp /var/www/Linott/aide_guillaume.64 /usr/local/bin/
	chown root:root /usr/local/bin/aide_guillaume.64
	chmod 755 /usr/local/bin/aide_guillaume.64
else 
	cp /var/www/Linott/aide_guillaume.32 /usr/local/bin/
	chown root:root /usr/local/bin/aide_guillaume.32
	chmod 755 /usr/local/bin/aide_guillaume.32
fi


