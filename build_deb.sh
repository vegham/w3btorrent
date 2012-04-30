#!/bin/sh

# this script is part of w3btorrent
# the use of this script is free for all as the modified program clearly states that its been adopted from w3btorrent 

#
## REQUIREMENTS
#
if [ $USER != "root" ]; then
	echo "Must build as root"
	exit
fi
if [ ! `which dh_make` ]; then
	echo "Missing the program dh_make - sudo apt-get install dh-make"
	exit
fi

if [ ! -x `which dh_make` ]; then
	echo "Can't run the program dh_make"
	exit
fi

if [ ! `which php` ]; then
	echo "Missing the program php-cli - sudo apt-get install php5-cli"
	exit
fi

if [ ! -x `which php` ]; then
	echo "Can't run the program php"
	exit
fi


# variables
version=`php -r 'include("CONFIG.php");echo \$CONFIG["version"];'`
path=$PWD
user=www-data

# setup paths
cd ..
rm -rf w3btorrent-$version
mkdir w3btorrent-$version
cd w3btorrent-$version



dh_make -s -n -i -p w3btorrent
cd debian
rm *.ex *.EX
mkdir w3btorrent
mkdir w3btorrent/var
mkdir w3btorrent/var/cache
mkdir w3btorrent/var/cache/w3btorrent
mkdir w3btorrent/var/www
mkdir w3btorrent/var/www/w3btorrent
mkdir w3btorrent/etc
mkdir w3btorrent/etc/w3btorrent/
echo '<?xml version="1.0"?>
<w3btorrent version="$version" author="build_deb.sh">
<mysql></mysql>
<path></path>
<rtorrent></rtorrent>
<users></users>
</w3btorrent>' > w3btorrent/etc/w3btorrent/config.xml

cp -a $path/inc w3btorrent/var/www/w3btorrent/
cp -a $path/pix w3btorrent/var/www/w3btorrent/
cp -a $path/script w3btorrent/var/www/w3btorrent/
cp -a $path/css w3btorrent/var/www/w3btorrent/
cp $path/CONFIG.php w3btorrent/var/www/w3btorrent/
cp $path/cron.php w3btorrent/var/www/w3btorrent/
cp $path/header.php w3btorrent/var/www/w3btorrent/
cp $path/index.php w3btorrent/var/www/w3btorrent/
cp $path/index.php w3btorrent/var/www/w3btorrent/

# change the ddir in config
php -r '$c = file("w3btorrent/var/www/w3btorrent/CONFIG.php");
$c[count($c)-2] = "\$CONFIG[\"dDir\"] = \"/var/cache/w3btorrent\";//added by build_deb.sh\n\$CONFIG[\"cfg\"] = \"/etc/w3btorrent/config.xml\";//added by build_deb.sh\n";
file_put_contents("w3btorrent/var/www/w3btorrent/CONFIG.php",join($c));'

mkdir w3btorrent/DEBIAN
echo "Package: w3btorrent
Priority: optional
Section: universe/web
Maintainer: `uname -n`
Homepage: http://w3btorrent.sourceforge.net/
Architecture: all
Version: $version
Depends: rtorrent, php5, php5-xmlrpc
Suggests: apache2, screen
Description: lightweight web based torrent download manager" > control
cp control w3btorrent/DEBIAN/

# permissions
dh_fixperms
chown $user:$user w3btorrent/etc/w3btorrent/config.xml
chown $user:$user w3btorrent/var/cache/w3btorrent
chmod 600 w3btorrent/etc/w3btorrent/config.xml
chmod 700 w3btorrent/var/cache/w3btorrent

cd ..
dh_builddeb
cd ..
rm -rf w3btorrent-$version # remove this line if you want the files before compressed with deb