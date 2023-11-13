<?php
define ("GENDER_BOY", 1);
define ("GENDER_GIRL",2);
define("GENDER_THEY",3);

function form_message($last_id, $message, $ignored_users) {
        global $cu_array, $message_format, $message_fromme, $private_message, $private_message_fromme, $private_hidden,$w_whisper_to,$session,$user_name,$is_regist,$users,$w_unknown_user,$nick_highlight_before,$nick_highlight_after, $room_id,$str_w_n_before,$str_w_n_after, $chat_url;
        global $sw_usr_all_link, $sw_usr_girls_link, $sw_usr_boys_link, $sw_usr_adm_link, $sw_usr_they_link, $sw_usr_clan_link, $sw_usr_shaman_link;

        $IsPublic = true;
        $to_out = "";
        $mesg_array = explode("\t", $message, MESG_TOTALFIELDS);

        $mesg_cmd_body = $mesg_array[MESG_BODY];

        if(intval(trim($cu_array[USER_REDUCETRAFFIC])) > 0) $mesg_array[MESG_BODY] = eregi_replace("<img", "<!-",$mesg_array[MESG_BODY]);

        if ($mesg_array[MESG_ROOM] == $cu_array[USER_ROOM] or $mesg_array[MESG_ROOM] == -1) {
                if ($mesg_array[MESG_ID] > $last_id) {
                        if(!isset($ignored_users[strtolower($mesg_array[MESG_FROMWOTAGS])]) || $mesg_array[MESG_FROMWOTAGS] == "") {
                                if (strlen($mesg_array[MESG_TO])==0) {
                                        //not private

                                        if(strtolower($mesg_array[MESG_FROMWOTAGS]) == strtolower($cu_array[USER_NICKNAME])) {
                                                $mesg_array[MESG_BODY] = "<span class=\"hu\">".$mesg_array[MESG_BODY]."</span>";
                                        }

                                        if( !(strpos($mesg_array[MESG_BODY], strtolower($cu_array[USER_NICKNAME])) === FALSE)
                                                or ($cu_array[USER_GENDER] == GENDER_GIRL and !(strpos($mesg_array[MESG_BODY], $sw_usr_girls_link) === FALSE))
                        or (intval($cu_array[USER_CLANID]) == intval($mesg_array[MESG_CLANID]) and intval($mesg_array[MESG_CLANID]) > 0 and !(strpos($mesg_array[MESG_BODY], $sw_usr_clan_link."&gt;") === FALSE))
                              or ($cu_array[USER_GENDER] == GENDER_BOY and !(strpos($mesg_array[MESG_BODY], $sw_usr_boys_link) === FALSE))
                                                or ($cu_array[USER_GENDER] == GENDER_THEY and !(strpos($mesg_array[MESG_BODY], $sw_usr_they_link) === FALSE))
                                                or ($cu_array[USER_CLASS] > 0 and !(strpos($mesg_array[MESG_BODY], $sw_usr_adm_link) === FALSE))
                                        or !(strpos($mesg_array[MESG_BODY], $sw_usr_all_link) === FALSE)) {
                                                $mesg_array[MESG_BODY] = "<span class=\"hs\">".$mesg_array[MESG_BODY]."</span>";
                                        }

                                        if (strcmp($mesg_array[MESG_FROMWOTAGS], $cu_array[USER_NICKNAME])==0)
                                                $to_out = str_replace("[HOURS]",date("H",$mesg_array[MESG_TIME]),$message_fromme);
                                        else
                                                $to_out = str_replace("[HOURS]",date("H",$mesg_array[MESG_TIME]),$message_format);


                                                $IsPublic = true;
                                }
                                else {
                                        //private

                    //mring fix

                                        if (strcmp($mesg_array[MESG_FROMSESSION], $cu_array[USER_SESSION])==0 ||
                                                (strcmp($mesg_array[MESG_FROMWOTAGS], $cu_array[USER_NICKNAME])==0 && $mesg_array[MESG_FROMID] >0)
                                                ) {
                                                $to_out = str_replace("[HOURS]",date("H",$mesg_array[MESG_TIME]),$private_message_fromme);
                                        }else if (strcmp($mesg_array[MESG_TOSESSION], $cu_array[USER_SESSION])==0 ||
                                                                (strcmp($mesg_array[MESG_TO],$cu_array[USER_NICKNAME])==0 && $mesg_array[MESG_TOID] >0 )
                                                        ) {
                                                $to_out = str_replace("[HOURS]",date("H",$mesg_array[MESG_TIME]),$private_message);
                                        }else
                                                $to_out = str_replace("[HOURS]",date("H",$mesg_array[MESG_TIME]),$private_hidden);
                                        if(strlen($to_out) == 0) {
                                                if(($cu_array[USER_GENDER] == GENDER_BOY and $mesg_array[MESG_TO] == $sw_usr_boys_link) or
                            (intval($cu_array[USER_CLANID]) == intval($mesg_array[MESG_CLANID]) and intval($mesg_array[MESG_CLANID]) > 0 and $mesg_array[MESG_TO] == $sw_usr_clan_link) or
                            ($cu_array[USER_GENDER] == GENDER_GIRL and $mesg_array[MESG_TO] == $sw_usr_girls_link) or
                                                        ($cu_array[USER_GENDER] == GENDER_THEY and $mesg_array[MESG_TO] == $sw_usr_they_link) or
                                                        ($cu_array[USER_CLASS] > 0 and ($mesg_array[MESG_TO] == $sw_usr_adm_link or $mesg_array[MESG_TO] == $sw_usr_shaman_link)) or
                            ($cu_array[USER_CUSTOMCLASS] ==  CST_PRIEST and  $mesg_array[MESG_TO] == $sw_usr_shaman_link) or
                                                        $mesg_array[MESG_TO] == $sw_usr_all_link) {
                                                                $to_out = str_replace("[HOURS]",date("H",$mesg_array[MESG_TIME]),$private_message);
                                                }
                                        }

                                                if(strlen($to_out) > 0) $IsPublic = false;

                                }


                                $to_out = str_replace("[MIN]",date("i",$mesg_array[MESG_TIME]),$to_out);
                                $to_out = str_replace("[SEC]",date("s",$mesg_array[MESG_TIME]),$to_out);
                                $to_out = str_replace("[NICK]",$mesg_array[MESG_FROM],$to_out);
                                $to_out = str_replace("[NICK_WO_TAGS]",$mesg_array[MESG_FROMWOTAGS],$to_out);
                                $to_out = str_replace("[TO]",$mesg_array[MESG_TO],$to_out);
                                $to_out = str_replace("[PRIVATE]",$w_whisper_to,$to_out);
                                $to_out = str_replace("[AVATAR]",
                                                                                ((strlen($mesg_array[MESG_FROMAVATAR])<3) ? "" :
                                                                                        "<img src=\"".$chat_url."photos/".$mesg_array[MESG_FROMAVATAR]."\">"),
                                                                                $to_out);
                                $nick_in_str = 0;
                                if (function_exists('preg_replace')) {
                                        if (preg_match("/".$cu_array[USER_NICKNAME]."([".preg_quote("?&:, !")."])/", $mesg_array[MESG_BODY]))
                                                $nick_in_str = 1;
                                        $mesg_array[MESG_BODY] = preg_replace("/".$cu_array[USER_NICKNAME]."([".preg_quote("?&:, !")."])/", $nick_highlight_before.$user_name.$nick_highlight_after."\\1",$mesg_array[MESG_BODY]);
                                }
                                $to_out = str_replace("[MESSAGE]",$mesg_array[MESG_BODY],$to_out);
                                if ($nick_in_str) $to_out = $str_w_n_before . $to_out . $str_w_n_after;

                                if(strlen($to_out) > 0) {
                                       $to_out = addslashes($to_out);
                                       $to_out = eregi_replace("</script>","</'+'script'+'>", $to_out);

                                        if( $IsPublic == true) {$to_out = "parent.AddMsgToPublic('".$to_out."', '".addslashes($mesg_array[MESG_FROMWOTAGS])."');"; }
                                        else {  if(trim($mesg_array[MESG_FROMWOTAGS]) == "&CMD") {
                                                        $to_out = $mesg_cmd_body;
                                                }
                                                else  $to_out = "parent.AddMsgToPriv('".$to_out."', '".addslashes($mesg_array[MESG_FROMWOTAGS])."', '".addslashes($mesg_array[MESG_TO])."');";
                                          }
                                }


                        }
                }
        }

        return array($mesg_array[MESG_ID],$to_out);
}
?>