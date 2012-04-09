<?php

/* showad.php
 * 
 * this is a tool for displaying an ad. It is not coded really well yet
 * because I still don't have a clue what there will be to do in the end 
 * and I just experiment atm.
 */

// 
// get te request uri.
$requestURI = strtolower(trim($_SERVER['REQUEST_URI']));
$adInfo = getDescriptionOfAdForRequestURI($requestURI);

// 
// get a replacement.
if($adInfo['type'] == 'image') {
  // find a choosing algorithm.
  $replacement = '/media/mlp_banner_1.png';
}

// 
// redirect to the replacement.
header("Location: $replacement\n\n");
die();




function getDescriptionOfAdForRequestURI($uri) {
  $hostname = $_SERVER['HTTP_HOST'];

  $fp = fopen("repos.conf", "r");
  while($x = getRepoLine($fp)) {
    if(strtolower($x['domain']) == strtolower($hostname)) {
      return $x;
    }
  }

  return null;
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


?>
