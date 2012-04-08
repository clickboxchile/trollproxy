#!/bin/bash
# install.doku.sh
# shell script for setting up the transparent proxy and documenting how it works.
# 
# configuration:

# target machine configuration:
TARGETHOST=10.1.13.27
BINDIR=/root/bin
WWWROOT=/var/www
WWWROOT_ESCAPED=`echo "<?php echo str_replace('/', '\\/', '${WWWROOT}'); ?>" | php`

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
#ssh root@${TARGETHOST} "rm -rf /root/.ssh && mkdir /root/.ssh && chmod 700 /root/.ssh"
#scp ${LOCALKEYFILE} root@${TARGETHOST}:/root/.ssh/authorized_keys

# 
# remove nano, install vim and other dependencies...
#ssh root@${TARGETHOST} apt-get install squid sudo libapache2-mod-php5 php5-sqlite openssh-server vim
#ssh root@${TARGETHOST} apt-get remove nano

# make directories to put our stuff on the remote machine.
#ssh root@${TARGETHOST} mkdir ${BINDIR}/
#ssh root@${TARGETHOST} mkdir ${WWWROOT}/ads
#ssh root@${TARGETHOST} mkdir ${WWWROOT}/vhosts

# 
# patch the utilities locally. set paths, configuration and stuff.
mkdir -p build
cp src/regenapache.php build
replacePaths "build/regenapache.php"


# 
# install the utility to regenerate the apache configuration
#scp build/regenapache.php root@${TARGETHOST}:/root/bin/



# cleanup 
#rm -r build



