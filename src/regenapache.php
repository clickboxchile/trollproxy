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
  fwrite($fp, TEMPLATE . "\n");
}
fclose($fp);

// 
// 4. write a hosts file.
$fp = fopen("/etc/hosts", "w");
fwrite($fp, getHostFileBeginning());

foreach($services as $service) {
  fwrite($fp, "127.0.0.1 " . $service);
}

// 
// 5. now additionally add the ad-imposers
$adImposerServerAliases = array();
$fp2 = fopen("<WWW_AD_ROOT>/repos.conf", "r");
while($x = getRepoLine($fp2)) {
  fwrite($fp, "127.0.0.1 " . $x['domain']);
  $adImposerServerAliases []= $x['domain'];
}

fclose($fp2);
fclose($fp);

// 
// write another apache configuration and add the server 
// aliases for the ads.
$fp = fopen("/etc/apache2/sites-enabled/002-adprovider", "w");
fwrite($fp, "<VirtualHost *:80>\n");
fwrite($fp, "  ServerName proxy.<DOMAIN>\n");
fwrite($fp, "  ServerAdmin trollzentrale@<DOMAIN>\n");
fwrite($fp, "  ServerAlias " . implode(" ", $adImposerServerAliases) . "\n");
fwrite($fp, "  \n");
fwrite($fp, "  DocumentRoot <WWW_AD_ROOT>\n");
fwrite($fp, "  <Directory />\n");
fwrite($fp, "    Options FollowSymLinks\n");
fwrite($fp, "    AllowOverride None\n");
fwrite($fp, "  </Directory>\n");
fwrite($fp, "  <Directory <WWW_AD_ROOT>>\n");
fwrite($fp, "    Options Indexes FollowSymLinks MultiViews\n");
fwrite($fp, "    AllowOverride None\n");
fwrite($fp, "    Order allow,deny\n");
fwrite($fp, "    allow from all\n");
fwrite($fp, "  </Directory>\n");
fwrite($fp, "  \n");
fwrite($fp, "  ErrorLog ${APACHE_LOG_DIR}/service-error.log\n");
fwrite($fp, "  \n");
fwrite($fp, "  LogLevel warn\n");
fwrite($fp, "  \n");
fwrite($fp, "  CustomLog ${APACHE_LOG_DIR}/service-access.log combined\n");
fwrite($fp, "</VirtualHost>");
fclose($fp);

// 
// 7. reload the apache.
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

function getRepoLine($fp) {
  // read a line.
  $line = fgets($fp);
  
if($line == null) {
    return null;
  }

  // remove withespaces.
  $line = trim($line);

  // split the line.
  list($urlpart, $type) = explode("=", $line);
  list($domainpart, $uripart) = explode("/", $urlpart, 2);

  return array('url' => $urlpart, 'type' => $type, 'domain' => $domainpart, 'uri' => '/' . $uripart);
}



function getHostFileBeginning() {
return <<<HOSTFILE
127.0.0.1       localhost transparentProxy.<DOMAIN> transparentProxy

# The following lines are desirable for IPv6 capable hosts
::1     ip6-localhost ip6-loopback
fe00::0 ip6-localnet
ff00::0 ip6-mcastprefix
ff02::1 ip6-allnodes
ff02::2 ip6-allrouters


HOSTFILE;
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
</VirtualHost>
TEMPLATE;
}


?>
