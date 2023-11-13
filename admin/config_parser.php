<?php
if (!defined("_CONFIG_PARSER_")):
define("_CONFIG_PARSER_", 1);

class VOCPlugin_Config {
  var $cfgVars     = array();
  var $configPath = "";

  function parseFile($f_path) {

     clearstatcache();

     $handle = fopen($f_path, 'rb');
     if (!flock($handle, LOCK_EX))
        trigger_error ("Could not LOCK ".$f_path." file. Do you use Win 95/98/Me?", E_USER_ERROR);
     fseek($handle,0);

     $this->configPath = $f_path;

     while ($buffer = fgets($handle, 16384)) {
         $buffer = trim($buffer);

         if(substr($buffer, 0 ,1) == "\$") {
           //regular variable ?
              $buffer = substr($buffer, 1);
              for($i = 2; $i < strlen($buffer); $i++) {
                  if(substr($buffer, $i, 1) == "=") break;
              }
              $vName = trim(substr($buffer, 0, $i));
              $vVal  = $GLOBALS[$vName];

              $this->cfgVars[] = array("type" => "variable", "name" => $vName, "value" => $vVal);
         }
         else if(substr($buffer, 0 ,2) == "//") {
          //comment?
           $this->cfgVars[] = array("type" => "comment", "name" => "", "value" => substr($buffer, 2));
         }
         else if(substr($buffer, 0 ,1) == "d" or substr($buffer, 0 ,1) == "D") {
          //definition?
              for($i = 2; $i < strlen($buffer); $i++) {
                  if(substr($buffer, $i, 1) == ",") break;
              }

              $vName = trim(substr($buffer, 0, $i));
              $vName = trim(eregi_replace("define", "", $vName));
              $vName = trim(substr($vName, 1));
              $vName = trim(substr($vName, 1, strlen($vName)-2));

              $vVal  = constant($vName);
              $this->cfgVars[] = array("type" => "definition", "name" => $vName, "value" => $vVal);
         }
         else {
              $this->cfgVars[] = array("type" => "undefined", "name" => "", "value" => $buffer);
         }
     }


     flock($handle, LOCK_UN);
     fclose($handle);
  }

  function writeFile() {

     if($this->configPath == "") return;

     $handle = fopen($this->configPath, 'wb');
     if (!flock($handle, LOCK_EX))
        trigger_error ("Could not LOCK ".$f_path." file. Do you use Win 95/98/Me?", E_USER_ERROR);

     for($i = 0; $i < count($this->cfgVars); $i++) {
         if($this->cfgVars[$i]["type"] == "variable") {
            fwrite($handle, "\$".$this->cfgVars[$i]["name"]." = \"".addslashes($this->cfgVars[$i]["value"])."\";\n");
         }else if($this->cfgVars[$i]["type"] == "comment") {
            fwrite($handle, "//".$this->cfgVars[$i]["value"]."\n");
         } else if($this->cfgVars[$i]["type"] == "definition") {
            fwrite($handle, "define(\"".$this->cfgVars[$i]["name"]."\",\"".addslashes($this->cfgVars[$i]["value"])."\");\n");
         } else {
            fwrite($handle, addslashes($this->cfgVars[$i]["value"])."\n");
         }
     }
     fflush($handle);
     flock($handle, LOCK_UN);
     fclose($handle);
  }

}


endif;
?>