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
$typeOfAd = getTypeOfAdForRequestURI($requestURI);

// 
// get a replacement.
// TODO look it up better.
$replacement = '/media/mlp_banner_1.png';

header("Location: $replacement\n\n");
die();




function getTypeOfAdForRequestURI($uri) {
  // TODO: lookup in the database
  return "image";
}


?>
