#!/bin/bash
echo "=== Starting the 10Layer Installation Script ==="
echo

#echo "--- NOTE: SSH key required for access to Git repository ---"
#read -e -p "Have you sent your key to 10Layer and do you have access to the Git repository? (Y/n)" -n 1 CONTINUE

#if [[ "$CONTINUE" -eq "n" ]]; then
#	echo "Please generate an SSH key and send the public key to jason@10layer.com"
#	echo "You should find your SSH key in ~/.ssh/id_rsa.pub or ~/.ssh/id_dsa.pub"
#	echo "If you don't have one, you can generate one now by typing 'ssh-keygen'"
#	exit 1
#fi

CURRDIR="$( cd "$( dirname "$0" )" && pwd )"
cd $CURRDIR
WEBDIR=$(cd ..; pwd)
cd $CURRDIR

DEFAULT=`/bin/hostname -f`
read -e -p "Confirm hostname ($DEFAULT):" HOSTNAME
if [[ -z "$HOSTNAME" ]]; then
	HOSTNAME=$DEFAULT
fi

echo "Installing 10Layer for $HOSTNAME"

DEFAULT="n"
read -e -p "Do you want to install MySql? (y/N):" DOMYSQL
if [[ -z "$DOMYSQL" ]]; then
	DOMYSQL=$DEFAULT
fi

if [ "$DOMYSQL" == "y" ]; then
	read -e -s -p "Select a root password for MySql:" MYSQLROOTPWD
	echo
	read -e -s -p "Confirm password:" MYSQLROOTPWD_CONF
	echo
	echo
	while [[ "$MYSQLROOTPWD" != "$MYSQLROOTPWD_CONF" ]]
	do
		echo "Passwords don't match, try again"
		read -e -s -p "Select a root password for MySql:" MYSQLROOTPWD
		echo
		read -e -s -p "Confirm password:" MYSQLROOTPWD_CONF
		echo
		echo
	done
fi

echo "=== Starting Dependency Installation ==="

apt-get update
apt-get upgrade -y

echo "=== Installing MySql ==="

if [ "$DOMYSQL" == "y" ]; then
	export DEBIAN_FRONTEND=noninteractive
	apt-get -y install mysql-server pwgen
	echo "Give mysql server time to start up before we try to set a password..."
	sleep 10
	mysql -uroot -e <<EOSQL "UPDATE mysql.user SET Password=PASSWORD('"$MYSQLROOTPWD"') WHERE User='root'; FLUSH PRIVILEGES;"
EOSQL
	echo "Done setting mysql password."
fi

echo "=== Installing Apache2, PHP5 and some useful stuff ==="
apt-get -y install php5 apache2 php5-mysql php5-imagick php5-dev memcached php5-memcached php5-curl imagemagick pdftk

echo "=== Setting up Apache2 for virtual hosts ==="

cp $CURRDIR/etc/apache2/mods-available/vhost_alias.conf /etc/apache2/mods-available/vhost_alias.conf
a2enmod vhost_alias
a2enmod rewrite
mkdir /var/www/virtual
chown www-data:www-data /var/www/virtual
apache2ctl restart

echo "=== Installing magic development stuff ==="
apt-get -y install python-setuptools build-essential python-dev mercurial-common git-core curl python-stompy
easy_install pdfminer

echo "=== Installing Orbited server ==="

easy_install twisted

wget https://bitbucket.org/desmaj/orbited/downloads/orbited-0.7.11beta3.tar.gz
tar xvfz orbited-0.7.11beta3.tar.gz
cd orbited-0.7.11beta3/
# hg clone https://bitbucket.org/desmaj/orbited
# cd orbited/daemon
easy_install .
cd $CURRDIR

useradd -d /dev/null -s /etc/orbited orbited

easy_install simplejson
easy_install stompservice

cat $CURRDIR/etc/orbited.cfg | sed -e 's/local.10layer.com/'$HOSTNAME'/g' > /etc/orbited.cfg
cp $CURRDIR/init.d/orbited /etc/init.d/orbited

chmod 755 /etc/init.d/orbited
update-rc.d orbited defaults
/etc/init.d/orbited start

echo "=== Installing MongoDB ==="

#apt-key adv --keyserver keyserver.ubuntu.com --recv 7F0CEB10
#echo "deb http://downloads-distro.mongodb.org/repo/debian-sysvinit dist 10gen" >> /etc/apt/sources.list
#apt-get update
#apt-get -y install mongodb-10gen
apt-get -y install mongodb

git clone http://github.com/mongodb/mongo-php-driver/
cd mongo-php-driver/
phpize
./configure
make
make install
cp $CURRDIR/etc/php5/conf.d/mongo.ini /etc/php5/conf.d/mongo.ini
apache2ctl restart

if [ "$DOMYSQL" == "y" ]; then
	echo "=== Setting up Database ==="
	RANDOMPASS=$(pwgen -n1 -s)
	mysql -uroot -p$MYSQLROOTPWD -e <<EOSQL "CREATE DATABASE IF NOT EXISTS 10layer; CREATE USER 10layer@localhost IDENTIFIED BY '"$RANDOMPASS"'; GRANT  ALL PRIVILEGES ON  10layer.* TO  10layer@localhost; FLUSH PRIVILEGES;"
EOSQL
	mysql -u10layer -p$RANDOMPASS 10layer < $WEBDIR/database/10layer.sql
fi

#cd $WEBDIR/application/config
#sed 's/10layer_password/'$RANDOMPASS'/' database.php > databasenew.php
#cp databasenew.php database.php
#rm databasenew.php

#sed 's/local.10layer.com/'$HOSTNAME'/' config.php > confignew.php
#cp confignew.php config.php
#rm confignew.php

#sed 's/local.10layer.com/'$HOSTNAME'/' 10layer.php > confignew.php
#cp confignew.php 10layer.php
#rm confignew.php


echo "=== Almost done! Copying directories ==="
#mkdir "/var/www/virtual/$HOSTNAME"
#cp -a $WEBDIR/* "/var/www/virtual/$HOSTNAME/."
ln -s $WEBDIR /var/www/virtual/$HOSTNAME

echo "=== Restarting Apache ==="
apache2ctl restart

echo "=== Congratulations! All done! ==="