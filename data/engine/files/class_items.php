<?
class item {
	var $id;
	var $title;
	var $image;
	var $price;
	var $quantity;
	var $saled;
	var $vip;
	var $unlimited;
	var $category;
	var $action;
	
	function item($str){
		if(!empty($str)){
		$res=explode("\t",$str);
		$this->id=$res[0];
		$this->title=$res[1];
		$this->image=$res[2];
		$this->price=$res[3];
		$this->quantity=$res[4];
		$this->saled=$res[5];
		$this->vip=$res[6];
		$this->category=$res[7];
		$this->action=trim($res[8]);
		}
	}
}
?>