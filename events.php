<?php
//Events pseudo-processor
//for Valentine Edition Pro II

if (!defined("_COMMON_")) {echo "stop";exit;}

if (!defined("_EVENTS_")):
define("_EVENTS_", 1);

define("EVENT_ADD_USER", 1);
define("EVENT_REM_USER", 2);
define("EVENT_DO_PUBLIC_MESSAGE", 3);
define("EVENT_DO_PRIVATE_MESSAGE", 4);
define("EVENT_LOGIN", 5);
define("EVENT_LOGOUT", 6);
define("EVENT_TIMEOUT", 7);
define("EVENT_EDT_USER", 8);
define("EVENT_POST_MESSAGE", 9);
define("EVENT_RENDER_USERLIST", 10);
define("EVENT_PREREG_USER", 11);
define("EVENT_POST_COMMENT", 12);
define("EVENT_POST_OFFLINEPM", 13);
define("EVENT_HTML_MESSAGE", 14);
define("EVENTS_ENGINE_VERSION", 1);

if (!isset($eventHandlers)) $eventHandlers = array();

function checkEventLegality($VEvent) {
  if($VEvent != EVENT_ADD_USER           and
     $VEvent != EVENT_REM_USER           and
     $VEvent != EVENT_DO_PUBLIC_MESSAGE  and
     $VEvent != EVENT_DO_PRIVATE_MESSAGE and
     $VEvent != EVENT_LOGIN              and
     $VEvent != EVENT_LOGOUT             and
     $VEvent != EVENT_TIMEOUT            and
     $VEvent != EVENT_POST_MESSAGE       and
     $VEvent != EVENT_PREREG_USER        and
     $VEvent != EVENT_POST_COMMENT       and
     $VEvent != EVENT_POST_OFFLINEPM     and
     $VEvent != EVENT_HTML_MESSAGE       and
     $VEvent != EVENT_RENDER_USERLIST) return false;

     return true;
}

function installEventHandler($VEvent, $VHandler) {
global $eventHandlers;

$i = 0;

   if(!checkEventLegality($VEvent)) return;

   for($i = 0; $i < count($eventHandlers); $i++) {
       if($eventHandlers[$i]["event"]   == $VEvent and
          $eventHandlers[$i]["handler"] == $VHandler) return;
   }

   $eventHandlers[] = array("event"   => $VEvent,
                            "handler" => $VHandler);
}

function riseEvent($VEvent, $HParam, &$LParam) {
global $eventHandlers;

$i = 0;

   if(!checkEventLegality($VEvent)) return;

   for($i = 0; $i < count($eventHandlers); $i++) {
       if($eventHandlers[$i]["event"]   == $VEvent) {
               if(function_exists($eventHandlers[$i]["handler"])) $eventHandlers[$i]["handler"]($HParam, $LParam); //call_user_func($eventHandlers[$i]["handler"], $HParam, &$LParam);
       }
   }
}

function initPlugins() {
global $file_path;


if (is_dir($file_path."plugins")) {
   if ($dh = opendir($file_path."plugins")) {
       while (($file = readdir($dh)) !== false) {
           if($file != "." && $file != "..") {
                   if(is_dir($file_path."plugins/".$file)) {
                   //Plugin dir found
                   //trying to load config
                     if(is_file($file_path."plugins/".$file."/config.php")) {
                          include($file_path."plugins/".$file."/config.php");
                          }
                     if(is_file($file_path."plugins/".$file."/plugin.php") and $VOCPlugin_Enabled == "YES") {
                          include($file_path."plugins/".$file."/plugin.php");
                          if(function_exists($VOCPlugin_Name."_InitPlugin")) call_user_func($VOCPlugin_Name."_InitPlugin");
                     }
                   }
           }
       }
       closedir($dh);
   }
}

//end of initPlugins
}

initPlugins();

endif;
?>