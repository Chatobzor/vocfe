<?php
include("check_session.php");
include("header.php");
include("../inc_common.php");

set_variable("clear_points");
if(intval($clear_points)) $clear_points = 1;
else $clear_points = 0;

set_variable("clear_points_all");
if(intval($clear_points_all)) $clear_points_all = 1;
else $clear_points_all = 0;

set_variable("clear_credits");
if(intval($clear_credits)) $clear_credits = 1;
else $clear_credits = 0;

set_variable("clear_photo_reiting");
if(intval($clear_photo_reiting)) $clear_photo_reiting = 1;
else $clear_photo_reiting = 0;


if($clear_points or
   $clear_points_all or
   $clear_credits or
   $clear_photo_reiting) {
       ?>
       <script language="JavaScript" type="text/javascript">
       <!--
	       location.href='<?php echo $chat_url; ?>admin/progress_frameset.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>&operation=clear&clear_points=<?=$clear_points?>&clear_points_all=<?=$clear_points_all?>&clear_credits=<?=$clear_credits?>&clear_photo_reiting=<?=$clear_photo_reiting?>';
       //->
       </script>
       <?php
        exit;
    }

		echo "<center><h2 style=\"color:#265D92;font-family:Verdana\">$adm_zerolize</h2></center>\n";
        echo "<form method=\"POST\" action=\"".$chat_url."admin/zerolize.php\">\n";
        echo "<input type=\"hidden\" name=\"session\" value=\"$session\">\n";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">\n";
		echo "<table border=\"0\" width=\"500\" align=center>";

        echo "<tr><td>$adm_zerolize_reiting </td><td><input type=\"checkbox\" name=\"clear_points\" value=\"1\"></td></tr>";
		echo "<tr><td>$adm_zerolize_reiting_all</td><td><input type=\"checkbox\" name=\"clear_points_all\" value=\"1\"></td></tr>";
        echo "<tr><td>$adm_zerolize_photo</td><td><input type=\"checkbox\" name=\"clear_photo_reiting\" value=\"1\"></td></tr>";
		echo "<tr><td>$adm_zerolize_credits</td><td><input type=\"checkbox\" name=\"clear_credits\" value=\"1\"></td></tr>";

		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
		echo "<tr><td colspan=\"2\" align=center>";
        echo "<input type=\"submit\" class=\"button_small\" value=\"$adm_zerolize\"></form>\n";
        echo "</td></tr>";
		echo "</table>";

?>