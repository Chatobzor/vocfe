<?
$items=file($data_path."items.dat");
$last_id=$items[0];
array_shift($items);
for($i=0;$i<count($items);$i++){
	$cur_item=new item($items[$i]);
	$item_list[$cur_item->id]=$cur_item;
}
unset($tmp);
unset($cur_item);
?>