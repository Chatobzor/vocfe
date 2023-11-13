<?
include("check_session.php");
include("../inc_common.php");
include($engine_path."class_items.php");
include($engine_path."shop_get_cat_list.php");
//set_variable('title');
set_variable("do");
set_variable("id");
set_variable("where");
if(!is_numeric($id) && !empty($id)){
        header("Location: ".$PHP_SELF."?session=$session&lang=$lang");
}
//echo $file_path;
//$chat_url."items/"
include ($engine_path."get_item_list.php");
$reversed_items=$item_list;
$actions_list=getActionsList();
function work_with_pict($file,$id=1){
        global $file_path;
        $ext=explode(".",$file['name']);
        $ext=$ext[sizeof($ext)-1];
        unlink($file_path."/items/".$id.".".$ext);
        copy($file['tmp_name'],$file_path."/items/".$id.".".$ext);
        unlink($file['tmp_name']);
        return $id.".".$ext;
}
function testVars($clas){
        $clas->title=trim($clas->title);
        if(empty($clas->title)) return "title";
        if(!is_numeric($clas->price)) return "price";
        if(!is_numeric($clas->quantity) && $clas->unlimited!="on") return "quantity";
}
function getActionsList(){
        global $engine_path;
        $ret_array=array();
        if (is_dir($engine_path."item_actions")) {
           if ($dh = opendir($engine_path."item_actions")) {
               while (($file = readdir($dh)) !== false) {
               if($file != "." && $file != "..") {
                                   $action_title='';
                   if(is_dir($engine_path."item_actions/".$file)) {
                     if(is_file($engine_path."item_actions/".$file."/config.php")) {
                          include($engine_path."item_actions/".$file."/config.php");
                                                  $ret_array[$file]=trim($action_title);
                     }
                   }
                        }
                }
                closedir($dh);
                }
        }
        return $ret_array;
}
function spec_trim($s){
        $s=str_replace("\t","",$s);
        $s=str_replace("\r","",$s);
        $s=str_replace("\n","",$s);
        return $s;
}
function write_data_to_file($array,$id=0){
        global $data_path;
        $fp = fopen($data_path."items.dat","w+");
   if (!$fp) trigger_error("Could not open $data_path/items.dat for writing. Please, check permissions", E_USER_ERROR);
        if (!flock($fp, LOCK_EX))
                trigger_error("Could not write to $data_path/items.dat! Do you use Win 95/98/Me?", E_USER_WARNING);
        fputs($fp,spec_trim($id));
        @reset($array);
        $begin="\n";
        while(list($id,$clas)=@each($array)){
                $clas->saled=spec_trim($clas->saled);
                $clas->vip=spec_trim($clas->vip);
                $clas->category=spec_trim($clas->category);
                $clas->action=spec_trim(trim($clas->action));
                $clas->title=substr($clas->title, 0, 50);
                $str=$begin.$id."\t".$clas->title."\t".$clas->image."\t".$clas->price."\t".$clas->quantity."\t".$clas->saled."\t".$clas->vip."\t".$clas->category."\t".$clas->action;
                fputs($fp,$str);
        }
        fclose($fp);
}

function showForm($mode="add",$id=0,$formdata='',$error='',$wop=false){
global $adm_shop, $chat_url,$category_list,$actions_list;
?>
<form method="post" name="itemForm" encType="multipart/form-data">
<input type="Hidden" name="do" value="<?=$mode;?>">
<input type="Hidden" name="id" value="<?=$id;?>">
<input type="Hidden" name="where" value="form">
<table cellspacing="0" cellpadding="4" border="0" align="center">
<tr>
        <td colspan=2 class=head align=center bgcolor=#265D92><?=$adm_shop['Op_Add'];?> <?=$adm_shop['Item'];?></td>
</tr>
<tr>
        <td><?=$adm_shop['Title'];?>:</TD>
        <td><input type="Text" name="form_title" value="<?=$formdata->title;?>" <?if($error=="title") echo "style=\"background-color: #FFCED5;\"";?>></TD>
</tr>
<tr>
        <td><?=$adm_shop['Price'];?>:</TD>
        <td><input type="Text" name="form_price" value="<?=$formdata->price;?>" <?if($error=="price") echo "style=\"background-color: #FFCED5;\"";?>></TD>
</tr>
<tr>
        <td><?=$adm_shop['Unlimited'];?>:</TD>
        <td><input type="checkbox" name="form_unlimited" onclick="document.itemForm.form_quantity.disabled=this.checked" <?if($formdata->quantity==-1) echo "checked";?>></TD>
</tr>

<tr>
        <td><?=$adm_shop['Quantity'];?>:</TD>
        <td><input type="Text" name="form_quantity" <?if($formdata->quantity==-1) echo "disabled"; else echo "value=\"$formdata->quantity\"";?> <?if($error=="quantity") echo "style=\"background-color: #FFCED5;\"";?>></TD>
</tr>
<tr>
        <td><?=$adm_shop['Action'];?>:</TD>
        <td><select name="action">
                <option value="0" <? if(empty($formdata->action) || $formdata->action==0) echo "selected";?>><?=$adm_shop['No_action']?></option>
        <?
                        @reset($actions_list);
                        while(list($id,$title)=@each($actions_list)){
                                if($formdata->action==$id)
                                        $addon='selected';
                                else
                                        $addon='';
                                ?>
                                        <option value="<?=$id?>" <?=$addon;?>><?=$title?></option>
                                <?
                        }
        ?>
                </select>
        </TD>
</tr>
<tr>
        <td><?=$adm_shop['Category'];?>:</TD>
        <td><select name="category">
                <option value="0" <? if(empty($formdata->category) || $formdata->category==0) echo "selected";?>><?=$adm_shop['All']?></option>
        <?
                        @reset($category_list);
                        while(list($id,$title)=@each($category_list)){
                                if($formdata->category==$id)
                                        $addon='selected';
                                else
                                        $addon='';
                                ?>
                                        <option value="<?=$id?>" <?=$addon;?>><?=$title?></option>
                                <?
                        }
        ?>
                </select>
        </TD>
</tr>

<tr>
        <td><?=$adm_shop['VIP'];?>:</TD>
        <td><input type="CHeckbox" name="vip" <?if($formdata->vip==1) echo "checked";?>></TD>
</tr>

<?
if($mode=="edit" && !$wop){
?>
<tr>
        <td>Current image</td>
        <td><img src="<?=$chat_url."items/".$formdata->image?>"></td>
</tr>
<?
}
?>
<tr>
        <td><?=$adm_shop['Picture'];?>:</TD>
        <td><input type="file" name="form_picture" <?if($error=="picture") echo "style=\"background-color: #FFCED5;\"";?>></TD>
</tr>
<tr>
        <td colspan="2" align="center"><input class=button_small type="Submit" value="<?=$adm_shop['Op_Add'];?>"></td>
</tr>
</table>
</form>
<?
}
//End functions definitions
include("header.php");
?>
<div align=center><font size=5 face="Verdana, Tahoma" color="#265D92" align=CENTER><b><?=$adm_shop_manager_itmes;?></b></font></div>
<?
switch ($do){
        case "add" : if($where=="form"){
                                        set_variable('form_title');
                                        set_variable('form_quantity');
                                        set_variable('form_unlimited');
                                        set_variable('form_price');
                                        set_variable('vip');
                                        set_variable('action');
                                        $reversed_items[$id]=new item;
                                        $reversed_items[$id]->id=$id;
                                        $reversed_items[$id]->title=strip_tags(spec_trim(trim($form_title)));
                                        $reversed_items[$id]->quantity=spec_trim(trim($form_quantity));
                                        $reversed_items[$id]->unlimited=spec_trim(trim($form_unlimited));
                                        $reversed_items[$id]->category=spec_trim(trim($category));
                                        $reversed_items[$id]->action='';
                                        $reversed_items[$id]->action=spec_trim(trim($action));
                                        if($reversed_items[$id]->unlimited=="on")
                                                $reversed_items[$id]->quantity=-1;
                                        if($vip=="on")
                                                $reversed_items[$id]->vip=1;
                                        else
                                                $reversed_items[$id]->vip=0;
                                        $reversed_items[$id]->price=spec_trim(trim($form_price));
                                        $error=testVars($reversed_items[$id]);
                                        if(!$error && ($_FILES['form_picture']['error']!=0)){
                                                $error="picture";
                                        }else{
                                                 list($roz_width, $roz_height, $type, $attr)=getimagesize($_FILES['form_picture']['tmp_name']);
                                                 if(($type != 1 and $type != 2)  or $roz_height > 150 or $roz_width > 150) $error="picture";
                                        }
                                        if(!$error){
                                                $reversed_items[$id]->image=work_with_pict($_FILES['form_picture'],$id);
                                                if($reversed_items[$id]->unlimited=="on")
                                                $reversed_items[$id]->quantity=-1;
                                                $reversed_items[$id]->id=$id;
                                                $reversed_items[$id]->saled=0;
                                                write_data_to_file($reversed_items,$id);
                                                header("Location: ".$PHP_SELF."?session=$session&lang=$lang");
                                        }else{
                                                showForm("add",$id,$reversed_items[$id],$error,true);
                                        }
                                } break;
        case "edit" :if($where!="form"){
                                        if(empty($id)){
                                                header("Location: ".$PHP_SELF."?session=$session&lang=$lang");
                                        }else{
                                                showForm("edit",$id,$reversed_items[$id]);
                                        }
                                }else{
                                set_variable('form_title');
                                set_variable('form_quantity');
                                set_variable('form_unlimited');
                                set_variable('form_price');
                                set_variable('vip');
                                set_variable('category');
                                set_variable('action');
                                if($vip=="on")
                                                $reversed_items[$id]->vip=1;
                                        else
                                                $reversed_items[$id]->vip=0;
                                $reversed_items[$id]->title=strip_tags(spec_trim(trim($form_title)));
                                $reversed_items[$id]->quantity=spec_trim(trim($form_quantity));
                                $reversed_items[$id]->unlimited=spec_trim(trim($form_unlimited));
                                $reversed_items[$id]->category=spec_trim(trim($category));
                                $reversed_items[$id]->action='';
                                $reversed_items[$id]->action=spec_trim(trim($action));
                                if($reversed_items[$id]->unlimited=="on")
                                                $reversed_items[$id]->quantity=-1;
                                $reversed_items[$id]->price=spec_trim(trim($form_price));
                                $error=testVars($reversed_items[$id]);
                                if(!$error && !empty($_FILES['form_picture']['name'])){
                                         list($roz_width, $roz_height, $type, $attr)=getimagesize($_FILES['form_picture']['tmp_name']);
                                        if(($type != 1 and $type != 2)  or $roz_height > 150 or $roz_width > 150) $error="picture";
                                }

                                if(!$error){
                                        if(!empty($_FILES['form_picture']['name']))
                                                $reversed_items[$id]->image=work_with_pict($_FILES['form_picture'],$id);
                                        if($reversed_items[$id]->unlimited=="on")
                                                $reversed_items[$id]->quantity=-1;
                                        write_data_to_file($reversed_items,$last_id);
                                        header("Location: ".$PHP_SELF."?session=$session&lang=$lang");
                                }else
                                        showForm("edit",$id,$reversed_items[$id],$error);
                                } break;
        case "remove":
                                        if(empty($id)){
                                                header("Location: ".$PHP_SELF."?session=$session&lang=$lang");
                                        }else{
                                                unlink(unlink($file_path."/items/".$reversed_items[$id]->image));
                                                unset($reversed_items[$id]);
                                                write_data_to_file($reversed_items,$last_id);
                                                header("Location: ".$PHP_SELF."?session=$session&lang=$lang");
                                        }
                                        break;
        default:
?>
<table cellspacing="2" cellpadding="0" border="0" align="center">
<?
for($i=0;$i<count($items);$i++){
$cur_item=new item($items[$i]);
?>
<tr>
        <td colspan="2"><hr color="#265D92" noshade></td></td>
</tr>
<tr>
        <td width="100" height="100" valign="top" align="center" nowrap><img src="<?=$chat_url."items/".$cur_item->image;?>"></td>
        <td><strong><?=$adm_shop['Title'];?>: <?=$cur_item->title;?></strong><br>
        <strong><?=$adm_shop['Price'];?>: <?=$cur_item->price;?></strong><br>
        <strong><?=$adm_shop['Quantity'];?>: <?if($cur_item->quantity==-1) echo $adm_shop['Unlimited']; else echo $cur_item->quantity?></strong><br>
        <strong><?=$adm_shop['Saled'];?>: <?=$cur_item->saled;?></strong><br>
        <strong><?=$adm_shop['Category']?>: <?if(!empty($cur_item->category)) if(!empty($category_list[$cur_item->category]) && $cur_item->category!=0 ) echo $category_list[$cur_item->category]; else echo $adm_shop['All']; else echo $adm_shop['All'];?></strong><br>
        <strong><?=$adm_shop['Action']?>:       <?if(empty($cur_item->action) || $cur_item->action=="0") echo $adm_shop['No_action']; else echo $actions_list[$cur_item->action];?> </strong><br>
        <strong><?=$adm_shop['Operations'];?>: <a href="?session=<?=$session?>&lang=<?=$lang?>&do=edit&id=<?=$cur_item->id?>"><?=$adm_shop['Op_Edit'];?></a> <a href="?session=<?=$session?>&lang=<?=$lang?>&do=remove&id=<?=$cur_item->id?>" onClick="return confirm('Are you sure?');"><?=$adm_shop['Op_Del'];?></a> </strong><br>
        <?if($cur_item->vip==1){?><strong><font color="#ff0000"><?=$adm_shop['VIP']?></font></strong><br><?}?>
        </td>
</tr>
<?
}
?>
</table>
<?showForm("add",$last_id+1);?>
<?      break;}?>
</body>
</html>


