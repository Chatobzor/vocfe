<form action="act_submit.php" method="post" name="actions">
        <input type="Hidden" name="action_name" value="status">
        <input type="Hidden" name="param[set]" value="1">
        <input type="Hidden" name="session" value="<?=$session?>">
<table>
        <tr>
                <td><?=$w_roz_chat_status?>:</td>
                <td><input type="text" name="new_status" value="<?=$current_user->chat_status?>" class=flat size=25 maxlength="25"></td>
                <Td><input type="Submit" class="input_button" value="OK"></TD>
        </tr>
</table>
</form>
