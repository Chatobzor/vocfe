<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_browser_detect.php");
?>
<script language="JavaScript">
<!--
function rel()
{
        document.location.href='<?php echo $chat_url."alerter.php?session=$session";?>';
}
window.setTimeout("location.reload()",600000);
<?php
if ($browser == "msie" && $user_chat_type!="reload")
{
?>
function open_win(win_file, win_title)
{
        with(window.parent.voc_mess_frameset.message_box.document)
        {
                open('text/html','replace');
                close();
        }
        window.parent.voc_mess_frameset.message_box.document.location.href = ''+win_file;
        window.parent.voc_mess_frameset.show_box();
}
<?php }else{ ?>
function open_win(win_file, win_title)
{
        window.open(win_file, win_title, 'resizable=yes,width=500,height=350,toolbar=no,scrollbars=yes,location=no,menubar=no,status=no');
}
<?php }?>
//-->
</script>
<noscript>
<?php
echo "<meta http-equiv=\"refresh\" content=\"600;URL=alerter.php?session=$session\">\n";
?>
</noscript>

<?php include($file_path."designes/".$design."/common_body_start.php");?>

<?php
        if(intval($new_board_messages) > 0) {
?>
<script language="JavaScript">
<!--
//open_win('board_list.php?session=<?php echo $session;?>');
alert('<?php echo $w_roz_new_message; ?>' + ' "'+ '<?php echo $w_roz_offline_pm; ?>'+ '"');
//-->
</script>
<?php  }
        if($current_user->user_class > 0 or
           $current_user->custom_class > 0 or
           $current_user->allow_pass_check) {

           $CanBeDone = true;
            if($current_user->last_pass_check < my_time() - PASS_CHANGE_TIME) {

?>
<script language="JavaScript">
<!--
//open_win('board_list.php?session=<?php echo $session;?>');
alert('<?php echo $w_pass_secutity_alert ?>');
//-->
</script>
<?php } }

include($file_path."designes/".$design."/common_body_end.php");?>