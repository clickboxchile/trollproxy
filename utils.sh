#!/bin/bash
# utils.sh
# utilities for the install script.


# -- ----------------------------------------------- -- #
# -- -- replace an expression in a single file    -- -- #
# -- ----------------------------------------------- -- #
function replacePath {
	# filename=$1
    # replace=$2
    # with=$3

	echo "<?php echo str_replace('$2', '$3', file_get_contents('$1')); ?>" | php > $1.tmp 2>/dev/null
	cp $1.tmp $1
	rm $1.tmp
}

# -- ----------------------------------------------- -- #
# -- -- replace path expressions by global config -- -- #
# -- ----------------------------------------------- -- #
function replacePaths {
	# filename=$1

	replacePath $1 "<WWW_AD_ROOT>" "${WWWROOT}/ads"
	replacePath $1 "<WWW_VHOST_ROOT>" "${WWWROOT}/vhosts"
}


