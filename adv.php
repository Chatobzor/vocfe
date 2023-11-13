<?php
if (isset($_SERVER['SERVER_ADDR']))
{
        echo "you cannot launch this script through web-interface";
        exit;
}

   include("inc_common.php");

   function sendMsgToChat($sNick, $roomID, $Msg)
   {
        global $flood_protection;
        global $messages_to_show, $ld_engine_path, $engine_path, $data_path, $messages_file, $IsPublic, $registered_colors;

        $flood_protection = 0;
        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                        MESG_ROOM=>$roomID,
                                        MESG_FROM=>$sNick,
                                        MESG_FROMWOTAGS=>$sNick,
                                        MESG_FROMSESSION=>"",
                                        MESG_FROMID=>0,
                                        MESG_TO=>"",
                                        MESG_TOSESSION=>"",
                                        MESG_TOID=>0,
                                        MESG_BODY=>$Msg);
        include($engine_path."messages_put.php");
   }

      // Assign the $phpAds_raw['html'] variable to your template
    $txt = "<b>Увага! Навчатись безкоштовно цілком можливо! СМС-Іспити! <a href=\"http://www.rozmova.if.ua/site/news/1007\" target=_blank>http://www.rozmova.if.ua/site/news/1007</a>";
    $txt = "<span class=ha>".$txt."</span></b>";
    sendMsgToChat("Дворецький", 1, $txt);
?>
