<?php

if (!defined("_TO_CANON_NICK_")):
define("_TO_CANON_NICK_", 1);
//if you add your own to_canon_nick function, add the name into array
$to_canon_funcs = array("none","simplest","normal","umlauts","rus-win1251");

function to_canon_nick($nick){
        global $current_to_canon;
        switch($current_to_canon){
                case "none":                 break;
                case "simplest":         $nick = strtolower($nick);
                                                        break;
                case "normal":                 $nick = strtolower($nick);
                                                        strtr($nick,"016i","olbl");
                                                        break;
                case "umlauts":                $nick = strtolower($nick);
                                                        $nick = strtr($nick,"016i","olbl");
                                                        $umlauts = array("Я","д","ц","ь");
                                                        $umlauts2 = array("ss","ae","oe","ue");
                                                        $nick = str_replace($umlauts, $umlauts2, $nick);
                                                        break;
                case "rus-win1251":            $nick = strtolower($nick);
                                               $nick = strtr($nick,"іі016абезикорсухьiнпмт","llolbabe3ukopcyxblhnmt");
                                               $nick = str_replace("ю","lo",$nick);
                                               $nick = str_replace("ы","bl",$nick);
                                                        break;
        }
        return $nick;
}
endif;
?>