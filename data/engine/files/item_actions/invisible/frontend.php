<form action="act_submit.php" method="post" name="actions">
    <input type="Hidden" name="action_name" value="invisible">
    <input type="Hidden" name="param[set]" value="1">
    <input type="Hidden" name="session" value="<?= $session ?>">
    <table>
        <tr>
            <td><?= $w_shop_invisibility ?>:</td>
            <Td><input type="Submit" class="input_button" value="<?= $w_shop_invisibility_use ?>"></TD>
        </tr>
    </table>
</form>
