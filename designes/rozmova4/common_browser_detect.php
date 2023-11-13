<?php
$browser = getenv("HTTP_USER_AGENT"); 
$brow = eregi ("(ozilla.[34])", $browser);   
$brow2 = eregi ("(MSIE.[56])", $browser);   
$brow1 = eregi ("(opera.[5])", $browser);  
$brow3 = eregi ("(ozilla.[5])", $browser); 
if ( $brow == '1' & $brow2 !='1' & $brow1 !='1')
	$browser = 'netscape';
if ( $brow1 == '1') 
	$browser = "opera" ;
if ( $brow3 == '1' & $brow1 !='1' & $brow2 !='1' & $brow !='1')
	$browser ="mozilla browser";
if ( $brow2 == '1' & $brow =='1')
	$browser = "msiie";
?>