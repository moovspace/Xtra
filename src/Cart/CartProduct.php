<?php
namespace Xtra\Cart;
use \Exception;

class CartProduct
{
	public $Count = 1;
	protected $Id = '';
	protected $Name = '';
	protected $Price = 0;
	protected $PriceSale = 0;
	// Only in regular product
	protected $Attributes = null; // Description, color, size ...
	protected $Addons = null; // CartProducts array

	function __construct($id, $name, $price, $price_sale = 0, $count = 1, $addons = [], $attr = [])
	{
		if(empty($id)){ throw new Exception("Error product id", 1); }
		if(empty($name)){ throw new Exception("Error product name", 2); }
		if($price < 0){ throw new Exception("Error product price", 3); }
		if($count < 1){ throw new Exception("Error product count", 4); }
		if($price_sale < 0){ throw new Exception("Error product sale price", 5); }
		if(!is_array($addons) || !is_array($attr)){ throw new Exception("Error addons or attr must be an array", 6); }
		if($price_sale >= $price){ throw new Exception("Error product sale price must be lower than price", 7); }
		$this->Id = $id;
		$this->Name = $name;
		$this->Price = (float) $price;
		$this->PriceSale = (float) $price_sale;
		$this->Count = (int) $count;
		$this->Attributes = $attr;
		// Addons
		foreach ($addons as $k => $a) {
			$this->AddonAdd($a);
		}
	}
	function Id(){
		return $this->Id;
	}
	function Name(){
		return $this->Name;
	}
	function Price(){
		return $this->Price;
	}
	function PriceSale(){
		return $this->PriceSale;
	}
	function Addons(){
		return $this->Addons;
	}
	function Count(){
		return $this->Count;
	}
	function Attributes(){
		return $this->Attributes;
	}
	function Plus(){
		$this->Count++;
	}
	function Minus(){
		$this->Count--;
		if($this->Count < 1){
			$this->Count = 1;
		}
	}
	function AddonAdd($addon){
		if($addon instanceof CartProduct){
			unset($addon->Addons); // Remove addons from product
			$this->Addons[] = $addon;
		}else{
			throw new Exception("Error addon. Add CartProduct() object without addons", 1);
		}
	}
	function AddonRemove($id){
		unset($this->Addons[$id]);
	}
	function AddonPlus($id){
		$a = $this->Addons[$id];
		if($a != null){
			$a->Count++;
			if($a->Count < 1){ $this->Count = 1; }
			$this->Addons[$id] = $a;
		}
	}
	function AddonMinus($id){
		$a = $this->Addons[$id];
		if($a != null){
			$a->Count--;
			if($a->Count < 1){ $this->Count = 1; }
			$this->Addons[$id] = $a;
		}
	}
	function Cost(){
		return $this->CostProduct() + $this->CostAddons();
	}
	function CostProduct(){
		$price = 0;
		if($this->PriceSale > 0 && $this->PriceSale < $this->Price){
			$price = $this->PriceSale * $this->Count;
		}else{
			$price = $this->Price * $this->Count;
		}
		return $price;
	}
	function CostAddons(){
		$price = 0;
		// Addons it is product
		foreach ($this->Addons as $k => $a) {
			if($a->PriceSale > 0 && $a->PriceSale < $a->Price){
				$price += $a->PriceSale * $a->Count;
			}else{
				$price += $a->Price * $a->Count;
			}
		}
		return $price;
	}
}
?>
