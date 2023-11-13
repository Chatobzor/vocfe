<?
include("check_session.php");
include("../inc_common.php");
include("header.php");
include($engine_path."shop_get_cat_list.php");
function spec_trim($s){
        $s=str_replace("\t","",$s);
        $s=str_replace("\r","",$s);
        $s=str_replace("\n","",$s);
        return $s;
}
end($category_list);
$last_id=key($category_list);
function write_to_file($array){
        global $data_path;
        @reset($array);
        $fp=fopen($data_path."items_types.dat","w+");
        if (!$fp) trigger_error("Could not open $data_path/items_types.dat for writing. Please, check permissions", E_USER_ERROR);
        if (!flock($fp, LOCK_EX))
                trigger_error("Could not write to $data_path/items_types.dat! Do you use Win 95/98/Me?", E_USER_WARNING);
        $begin="";
        while(list($id,$title)=@each($array)){
                $id=spec_trim($id);
                $title=spec_trim($title);
                fputs($fp,$begin.$id."\t".$title);
                $begin="\n";
        }
        fclose($fp);
}
set_variable('do');
set_variable('id');
set_variable('title');

switch ($do){
case 'add':        if(is_numeric($id) && !empty($id) && !empty($title)){
                                $category_list[$id]=strip_tags(spec_trim(trim($title)));
                                write_to_file($category_list);
                                header("Location: shop_cats.php?session=$session&lang=$lang");
                        }
                        break;
case 'remove': if(is_numeric($id) && !empty($id)){
                                        unset($category_list[$id]);
                                        write_to_file($category_list);
                                        header("Location: shop_cats.php?session=$session&lang=$lang");
                                }
                                break;
case 'edit':?>
        <div align=center><font size=5 face="Verdana, Tahoma" color="#265D92" align=CENTER><b><?=$adm_shop_manager_cats;?></b></font></div>
                <table align="center" cellpadding="3">
                <tr>
                <form method="post">
                <input type="Hidden" name="lang" value="<?=$lang;?>">
                <input type="Hidden" name="session" value="<?=$session;?>">
                <input type="Hidden" name="id" value="<?=$id?>">
                <input type="Hidden" name="do" value="add">
                <td><input type="Text" name="title" value="<?=$category_list[$id];?>"></td>
                <td align="center"><input type="Submit" class="button_small" value="<?=$adm_shop['Op_Edit']?>"></td>
                </form>
                </tr>
                </table>
                </body>
                </html>
        <?exit;
          break;
}
?>
<div align=center><font size=5 face="Verdana, Tahoma" color="#265D92" align=CENTER><b><?=$adm_shop_manager_cats;?></b></font></div>
<table align="center" cellpadding="3">
        <tr>
                <td><strong><?=$adm_shop['Title'];?></strong></td>
                <td align="center"><strong><?=$adm_shop['Operations']?></strong></td>
        </tr>

        <?
                @reset($category_list);
                while(list($id,$title)=@each($category_list)){
        ?>
        <Tr>
                <td><?=$title?></td>
                <td align="center"><strong><a href="?lang=<?=$lang?>&session=<?=$session?>&do=edit&id=<?=$id?>"><?=$adm_shop['Op_Edit']?></a>&nbsp;<a href="?lang=<?=$lang?>&session=<?=$session?>&do=remove&id=<?=$id?>"><?=$adm_shop['Op_Del']?></a></strong></td>
        </TR>
        <?
                }
        ?>
        <tr>
                <form method="post">
                <input type="Hidden" name="session" value="<?=$session;?>">
                <input type="Hidden" name="id" value="<?=$last_id+1?>">
                <input type="Hidden" name="lang" value="<?=$lang;?>">
                <input type="Hidden" name="do" value="add">
                <td><input type="Text" name="title"></td>
                <td align="center"><input type="Submit" class="button_small" value="<?=$adm_shop['Op_Add']?>"></td>
                </form>
        </tr>
</table>
</body>
</html>

