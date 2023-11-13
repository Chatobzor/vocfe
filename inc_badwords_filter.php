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
			"'[sS]{1,5}[^[:alnum:]]{0,10}[hH]{1,5}[^[:alnum:]]{0,10}[eE]{1,5}[^[:alnum:]]{0,10}[iI]{1,5}[^[:alnum:]]{0,10}[sSЯ]{1,5}'",
			"'[aA]{1,5}[^[:alnum:]]{0,10}[rR]{1,5}[^[:alnum:]]{0,10}[sS]{1,5}[^[:alnum:]]{0,10}[cC]{1,5}[^[:alnum:]]{0,10}[hH]{1,5}'"
			);
	$good_words = "[...]";
	if (function_exists("preg_replace")) {
		$text = preg_replace($bad_words,$good_words, $text);
	}
	
	//russian 'mat'
	/*
		$bad_words = array( 
            "'[хХxXHh]{1,5}[^0-9A-Za-zа-яА-Я]*[уУyYuU]{1,5}[^0-9A-Za-zа-яА-Я]*[uUйЙиИяЯijIJeEеЕёЁ]{1,5}'", 
            "'\)\([^0-9A-Za-zа-яА-Я]*[уУyYuU]{1,5}[^0-9A-Za-zа-яА-Я]*[uUйЙиИяЯijIJeEеЕёЁ]{1,5}'", 
            "'[пПpPn]{1,5}[^0-9A-Za-zа-яА-Я]*[иИiI]{1,5}[^0-9A-Za-zа-яА-Я]*[зЗ3zZsS]{1,5}[^0-9A-Za-zа-яА-Я]*[дДdD]{1,5}'", 
            "'[ уУuUьЬъЪjJыЫyYаАaA]{1,5}[^0-9A-Za-zа-яА-Я]*[еЕeE]{1,5}[^0-9A-Za-zа-яА-Я]*[бБbB6]{1,5}[^0-9A-Za-zа-яА-Я]*[аАиИуУaAiIuU]{1,5}'", 
            "'[сСcCsS(]{1,5}[^0-9A-Za-zа-яА-Я]*[уУuUyY]{1,5}[^0-9A-Za-zа-яА-Я]*[4чЧ]?[^0-9A-Za-zа-яА-Я]*[кКkK]{1,5}[^0-9A-Za-zа-яА-Я]*[аАуУеЕиИaAuUyYeEiI]{1,5}'", 
            "'[fF]{1,5}[^0-9A-Za-zа-яА-Я]*[uUaA]{1,5}[^0-9A-Za-zа-яА-Я]*[cC]{1,5}[^0-9A-Za-zа-яА-Я]*[kK]{1,5}'", 
            "'[жЖzZ]{1,5}[^0-9A-Za-zа-яА-Я]*[hH]?[^0-9A-Za-zа-яА-Я]*[оОoO]{1,5}[^0-9A-Za-zа-яА-Я]*[пПpPp]{1,5}'", 
            "'[ ,?!\.][бБbB6]{1,5}[^0-9A-Za-zа-яА-Я]*[лЛlL]{1,5}[^0-9A-Za-zа-яА-Я]*[яЯjJyY]{1,5}'", 
            "'[пПpP]{1,5}[^0-9A-Za-zа-яА-Я]*[иИiI]{1,5}[^0-9A-Za-zа-яА-Я]*[дДdD]{1,5}[^0-9A-Za-zа-яА-Я]*[оОаАеЕoOaAeE]{1,5}'" 
            ); 
			
	$good_words = "[...]"; 
	$text = preg_replace($bad_words,$good_words, $text);
	*/
	return $text;
}
?>