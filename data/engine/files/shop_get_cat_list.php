<?
$category_list=array();
$cats=file($data_path."items_types.dat");
for($i=0;$i<count($cats);$i++){
	$cat_line=explode("\t",$cats[$i]);
	$category_list[$cat_line[0]]=trim($cat_line[1]);
}
unset($i);
unset($cat_line);
unset($cats);
?>