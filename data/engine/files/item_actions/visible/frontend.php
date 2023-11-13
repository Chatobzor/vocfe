<?
/*
DareDEVIL (07:20 PM) : 
 include($engine_path."messages_put.php");

                $fields_to_update[0][0] = USER_INVISIBLE;
                $fields_to_update[0][1] = 1;
                include($engine_path."user_din_data_update.php");
DareDEVIL (07:20 PM) : 
тьфу
DareDEVIL (07:20 PM) : 
$fields_to_update[0][0] = USER_INVISIBLE;
                $fields_to_update[0][1] = intval($update_invis);
                include($engine_path."user_din_data_update.php");
*/
?>
<form action="act_submit.php" method="post" name="actions">
	<input type="Hidden" name="action_name" value="invisible">
	<input type="Hidden" name="param[set]" value="1">
	<input type="Hidden" name="session" value="<?=$session?>">
<table>
	<tr>
		<td>Dидимість:</td>
		<Td><input type="Submit" value="Встановити"></TD>
	</tr>
</table>
</form>