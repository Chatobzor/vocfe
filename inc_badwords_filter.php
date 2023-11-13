<?php
function replace_badwords($text) {
	//i'm providing here some exmaples of bad words filtering.
	//you can select/modify the method you like.

	
	//simple check for sequences of symbols 
	// in php 5 cvs you can try str_ireplace
	/*
	$bad_words = array("fuck", "shit", "sheisse", "arschloch");
	$good_words = "[...]";
	$text = str_replace($bad_words, $good_words, $text);
	*/
	
	//check for 'F! Uccc~k' and Co
	$bad_words = array(
			"'[fF]{1,5}[^[:alnum:]]{0,10}[uUaA]{1,5}[^[:alnum:]]{0,10}[cC]{1,5}[^[:alnum:]]{0,10}[kK]{1,5}'",
			"'[sS]{1,5}[^[:alnum:]]{0,10}[hH]{1,5}[^[:alnum:]]{0,10}[iI]{1,5}[^[:alnum:]]{0,10}[tT]{1,5}'",
			"'[sS]{1,5}[^[:alnum:]]{0,10}[hH]{1,5}[^[:alnum:]]{0,10}[eE]{1,5}[^[:alnum:]]{0,10}[iI]{1,5}[^[:alnum:]]{0,10}[sSß]{1,5}'",
			"'[aA]{1,5}[^[:alnum:]]{0,10}[rR]{1,5}[^[:alnum:]]{0,10}[sS]{1,5}[^[:alnum:]]{0,10}[cC]{1,5}[^[:alnum:]]{0,10}[hH]{1,5}'"
			);
	$good_words = "[...]";
	if (function_exists("preg_replace")) {
		$text = preg_replace($bad_words,$good_words, $text);
	}
	
	//russian 'mat'
	/*
		$bad_words = array( 
            "'[õÕxXHh]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[óÓyYuU]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[uUéÉèÈÿßijIJeEåÅ¸¨]{1,5}'", 
            "'\)\([^0-9A-Za-zà-ÿÀ-ß]*[óÓyYuU]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[uUéÉèÈÿßijIJeEåÅ¸¨]{1,5}'", 
            "'[ïÏpPn]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[èÈiI]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[çÇ3zZsS]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[äÄdD]{1,5}'", 
            "'[ óÓuUüÜúÚjJûÛyYàÀaA]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[åÅeE]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[áÁbB6]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[àÀèÈóÓaAiIuU]{1,5}'", 
            "'[ñÑcCsS(]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[óÓuUyY]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[4÷×]?[^0-9A-Za-zà-ÿÀ-ß]*[êÊkK]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[àÀóÓåÅèÈaAuUyYeEiI]{1,5}'", 
            "'[fF]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[uUaA]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[cC]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[kK]{1,5}'", 
            "'[æÆzZ]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[hH]?[^0-9A-Za-zà-ÿÀ-ß]*[îÎoO]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[ïÏpPp]{1,5}'", 
            "'[ ,?!\.][áÁbB6]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[ëËlL]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[ÿßjJyY]{1,5}'", 
            "'[ïÏpP]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[èÈiI]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[äÄdD]{1,5}[^0-9A-Za-zà-ÿÀ-ß]*[îÎàÀåÅoOaAeE]{1,5}'" 
            ); 
			
	$good_words = "[...]"; 
	$text = preg_replace($bad_words,$good_words, $text);
	*/
	return $text;
}
?>