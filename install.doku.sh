#!/bin/bash
# install.doku.sh
# shell script for setting up the transparent proxy and documenting how it works.
# 
# configuration:

# target machine configuration:
TARGETHOST=10.1.13.30
BINDIR=/root/bin
DOMAIN=shack
WWWROOT=/var/www
WWWROOT_ESCAPED=`echo "<?php echo str_replace('/', '\\/', '${WWWROOT}'); ?>" | php 2> /dev/null`
MYNET=10.1.0.0/255.255.0.0

# ssh configuration.
LOCALKEYFILE=/home/armin/.ssh/id_rsa.pub

source ./utils.sh

# utilities
SED=`which sed`
CURDIR=`pwd`


# install an ssh key there.
echo "Installing ssh public key on the target machine..."
echo "  - Some ssh foo like host key verification or enter password might come up."
echo "  - You need root-access on the target machine."
echo "  - If the public key is deployed there already, there will probably be no questions for a password."
echo "  - The private ssh configuration of the root on the other side will be wiped."
ssh root@${TARGETHOST} "rm -rf /root/.ssh && mkdir /root/.ssh && chmod 700 /root/.ssh"
scp ${LOCALKEYFILE} root@${TARGETHOST}:/root/.ssh/authorized_keys

# 
# remove nano, install vim and other dependencies...
ssh root@${TARGETHOST} apt-get install squid sudo libapache2-mod-php5 php5-sqlite openssh-server vim less
ssh root@${TARGETHOST} apt-get remove nano

# make directories to put our stuff on the remote machine.
ssh root@${TARGETHOST} mkdir ${BINDIR}/
ssh root@${TARGETHOST} mkdir ${WWWROOT}/ads
ssh root@${TARGETHOST} mkdir ${WWWROOT}/vhosts

# 
# patch the utilities locally. set paths, configuration and stuff.
mkdir -p build
cp src/regenapache.php build
replacePaths "build/regenapache.php"

cp src/squidconfig build
replacePaths "build/squidconfig"

cp src/showad.php build
replacePaths "build/showad.php"


# 
# install the utility to regenerate the apache configuration
scp build/regenapache.php root@${TARGETHOST}:/root/bin/
ssh root@${TARGETHOST} php /root/bin/regenapache.php
scp build/squidconfig root@${TARGETHOST}:/etc/squid/squid.conf
ssh root@${TARGETHOST} service squid restart
scp repo/repos.conf root@${TARGETHOST}:/var/www/ads
scp build/showad.php root@${TARGETHOST}:/var/www/ads/index.php
scp src/showad.htaccess root@${TARGETHOST}:/var/www/ads/.htaccess
scp -r repo/ads root@${TARGETHOST}:/var/www/ads/media

# 
# add a cronjob to perform regular jobs.
scp src/cronjobs root@${TARGETHOST}:/etc/cron.d/trollProxy

# 
# need mod rewrite and apache restart.
ssh root@${TARGETHOST} a2enmod rewrite
ssh root@${TARGETHOST} service apache2 restart

# cleanup 
rm -r build



