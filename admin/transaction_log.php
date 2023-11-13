<?

include("check_session.php");
include("../inc_common.php");
include($engine_path."class_items.php");
include($engine_path."get_item_list.php");
include($engine_path."transactions.php");
include_once($file_path."inc_user_class.php");

$current_user = new User;

include("header.php");
$transations = file($data_path."transactions.dat");
?>
<table align="center">
    <tr>
        <td align="center"><strong>Date</strong></td>
        <td><strong>Sender</strong></td>
        <td><strong>Receiver</strong></td>
        <td><strong>Item</strong></td>
        <td><strong>Quantity</strong></td>
        <td><strong>Price</strong></td>
        <td><strong>Operation</strong></td>
        <td><strong>Result</strong></td>
    </tr>
    <?
    $tr = new transaction();
    @reset($transations);
    foreach ($transations as $transaction) {
        $line = explode("\t", $transaction);
        ?>
        <tr bgcolor="#F0F0F0">
            <td align="center"><?= date("d.m.Y H:i:s", $line[0]); ?></td>
            <td><?
                $is_regist = intval($line[1]);
                if (is_file($data_path."users/".floor($is_regist / 2000)."/".$is_regist.".user")) {
                    include($ld_engine_path."users_get_object.php");
                    echo $current_user->nickname;
                } else {
                    echo "!deleted!";
                }
                ?></td>
            <td><?
                $is_regist = intval($line[2]);
                if (is_file($data_path."users/".floor($is_regist / 2000)."/".$is_regist.".user")) {
                    include($ld_engine_path."users_get_object.php");
                    echo $current_user->nickname;
                } else {
                    echo "!deleted!";
                }
                ?></td>
            <td><?= $item_list[$line[3]]->title; ?></td>
            <td><?= $line[4] ?></td>
            <td><?= $line[5] ?></td>
            <td align="center"><?= $line[6] ?></td>
            <td><?
                echo $tr->str_error(trim($line[7])); ?></td>
        </tr>
        <?
    }
    ?>
</table>
