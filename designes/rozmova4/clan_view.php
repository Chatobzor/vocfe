<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_body_start.php");

function array_trim ( $array, $index ) {
   if (is_array ( $array ) ) {
     unset ( $array[$index] );
     array_unshift ( $array, array_shift ( $array ) );
     return $array;
     }
   else {
     return false;
     }
}

if($action == "") {
        include($ld_engine_path."clans_get_list.php");
    $html_to_out .= "<center><table width=\"90%\" cellpadding=4 cellspacing=0><tr><td width=\"90%\" class=head>";
        $html_to_out .= "<center><h2 style=\"color:navy;font-family:Verdana\">".ucfirst($w_roz_clans)."</h2></center>\n";

    for($i = 0; $i < count($clans_list); $i++) {

        if(is_file($file_path."clans-avatar/".floor($clans_list[$i]["id"]/2000)."/".$clans_list[$i]["id"].".gif")) {
                $html_to_out .= "<tr><td><img src=\"".$chat_url."clans-avatar/".floor($clans_list[$i]["id"]/2000)."/".$clans_list[$i]["id"].".gif\">&nbsp;";
                }
        else $html_to_out .= "<tr><td>";

       $is_regist_clan = $clans_list[$i]["id"];

        include($file_path."inc_user_class.php");
        $current_clan = new Clan();
        include($ld_engine_path."clan_get_object.php");

        $html_to_out .= "<a href=\"".$chat_url."clan_view.php?session=$session&action=view_clan&clan_id=".$clans_list[$i]["id"]."\">".$clans_list[$i]["name"]."</a> (".count($current_clan->members)." / ".$current_clan->credits.")</td></tr>";
    }

        $html_to_out .= "</table>";
}
if(trim($action) == "view_clan") {
        set_variable("clan_id");
    $clan_id = intval($clan_id);
    $is_regist_clan = $clan_id;

    include($file_path."inc_user_class.php");
    $current_clan = new Clan();
    include($ld_engine_path."clan_get_object.php");


    $html_to_out .= "<center><h2 style=\"color:navy;font-family:Verdana\">".ucfirst($current_clan->name)." (".count($current_clan->members).")</h2>";
    $html_to_out .= "[<a href=\"".$chat_url."clan_view.php?session=$session\">$w_roz_clans</a>]</center>";

    $html_to_out .= "<center><table width=\"90%\" cellpadding=4 cellspacing=0><tr><td width=\"90%\" class=head>";

    if(is_file($file_path."clans-logos/".floor($clan_id/2000)."/".$clan_id.".gif")) {
            $html_to_out .= "<tr><td align=CENTER><img src=\"".$chat_url."clans-logos/".floor($clan_id/2000)."/".$clan_id.".gif\"></td></tr>";
    }
    else if(is_file($file_path."clans-logos/".floor($clan_id/2000)."/".$clan_id.".jpg")) {
            $html_to_out .= "<tr><td td align=CENTER><img src=\"".$chat_url."clans-logos/".floor($clan_id/2000)."/".$clan_id.".jpg\"></td></tr>";
    }

    if(strlen($current_clan->email) > 0) $html_to_out .= "<tr><td align=CENTER>$w_roz_clan_email: <b>".str_replace("@", " [at] ", $current_clan->email)."</b></td></tr>";
    if(strlen($current_clan->url) > 0) $html_to_out .= "<tr><td align=CENTER>$w_roz_clan_url: <b>"."<a href=\"".$chat_url."go.php?url=".urlencode($current_clan->url)."\" target=\"_blank\">".$current_clan->url."</a></b></td></tr>";
    $html_to_out .= "<tr><td align=CENTER>$w_money: <b>".$current_clan->credits."</b></td></tr>";

    $showed_users = array();
    $showed_nicks = array();

    for($i = 0; $i < count($current_clan->members); $i++) {

      if(i >= count($current_clan->members)) break;

      $is_regist    = $current_clan->members[$i]["id"];
          if(is_file($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")) {
              include($ld_engine_path."users_get_object.php");
      }
      else {
                  $current_clan->members = array_trim($current_clan->members, $i);
      }

      if($current_user->clan_id != $is_regist_clan) {
                      $current_clan->members = array_trim($current_clan->members, $i);
      }
      else {
                $IsShowedFound = false;
                for($j = 0; $j < count($showed_users); $j++) {
                  if($is_regist == $showed_users[$j] or strcasecmp($showed_nicks, trim($current_user->nickname)) == 0) { $IsShowedFound = true; $current_clan->members = array_trim($current_clan->members, $i); }
          }
          if(!$IsShowedFound) {
                      $html_to_out .= "<tr><td>".$current_user->clan_status." <b><a href=\"".$current_design."profiler.php?session=$session&user_to_search=".$current_clan->members[$i]["nick"]."\">".$current_clan->members[$i]["nick"]."</a></b></td></tr>";
              }
          else {
              $showed_users[] = $is_regist;
              $showed_nicks[] = $current_user->nickname;
          }
      }
        }
          $html_to_out .= "</table>";
           include($ld_engine_path."clan_update_object.php");
}

echo($html_to_out);
include($file_path."designes/".$design."/common_body_end.php"); ?>