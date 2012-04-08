<?php

/* regenapache.php
 * 
 * tool for regenerating the apache configuration for offering 
 * different kinds of ad services 
 */

$SERVICE_CONFIGURATION_FILE = "/etc/apache2/sites-enabled/001-services";
$AD_CONFIGURATION_FILE = "/etc/apache2/sites-enabled/002-ads";
$APACHE_AD_BASE = "<WWW_AD_ROOT>";
$APACHE_SERVICE_BASE = "<WWW_VHOST_ROOT>";

// 
// 1. remove old configuration if it exists
if(file_exists($SERVICE_CONFIGURATION_FILE)) {
  unlink($SERVICE_CONFIGURATION_FILE);
}

// 
// 2. get a list of all services available
$services = findServices($APACHE_SERVICE_BASE);


// 
// 3. write an apache config file for those
$fp = fopen($SERVICE_CONFIGURATION_FILE, "w");
foreach($services as $service) {
  $TEMPLATE = getApacheConfigTemplate();

  // 
  // set variables in the template.
  $TEMPLATE = str_replace("<DIRECTORY>", $APACHE_SERVICE_BASE . /*  "/" . */ $service, $TEMPLATE);
}

// 
// 4. write a hosts file.

// 
// 5. reload the apache.
system("service apache2 reload");
exit;


function findServices($dir) {
  $result = array(); 

  $fp = opendir($dir);
  while($x = readdir($fp)) {
    print_r($x);
  }

  return $result;
}





/** 
 * get a template for apache configuration.
 */
function getApacheConfigTemplate() {
  return <<<TEMPLATE
<VirtualHost *:80>
        ServerAdmin webmaster@localhost

        DocumentRoot <DIRECTORY>
        <Directory />
                Options FollowSymLinks
                AllowOverride None
        </Directory>
        <Directory <DIRECTORY>>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride None
                Order allow,deny
                allow from all
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/service-error.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/service-access.log combined
TEMPLATE;
}


?>
