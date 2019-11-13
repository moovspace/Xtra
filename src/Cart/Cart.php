<?php
namespace Xtra\Cart;
use \Exception;

class Cart
{
	protected $CostProducts = 0;
	protected $CostDelivery = 0;
	protected $CostCheckout = 0;
	protected $Discount = 0;
	protected $Coupon = '';

	function __construct($id = 1, $delivery_min = 0, $delivery_cost = 0, $currency = 'PLN'){
		$this->CartId = $id;
		$this->CartDeliveryMin = $delivery_min;
		$this->CartDeliveryCost = $delivery_cost;
		// Create Cart
		$this->Load();
		$this->Cart['delivery_min'] = $delivery_min;
		$this->Cart['delivery_cost'] = $delivery_cost;
		$this->Cart['currency'] = $currency;
	}

	function Load(){
		$this->Cart = $_SESSION['cart'][$this->CartId];
	}

	function Save(){
		$_SESSION['cart'][$this->CartId] = $this->Cart;
	}

	function Clear(){
		unset($this->Cart);
		unset($_SESSION['cart'][$this->CartId]);
	}

	function Discount($value = 0){
		if($value > 0){
			$this->Discount = $this->Cart['discount'] = (float) $value;
		}
		$this->Save();
	}

	function Coupon($code = ''){
		if(!empty($code)){
			$this->Coupon = $this->Cart['cupon'] = (float) $code;
		}
		$this->Save();
	}

	function GetProducts(){
		return $this->Cart['products'];
	}

	function Plus($id){
		$pr = $this->Cart['products'][$id];
		if($pr instanceof CartProduct){
			// $pr->Count++;
			$pr->Plus();
			$this->Cart['products'][$id] = $pr;
		}
		$this->Save();
	}

	function Minus($id){
		$pr = $this->Cart['products'][$id];
		if($pr instanceof CartProduct && $pr->Count > 1){
			$pr->Count--;
			$this->Cart['products'][$id] = $pr;
		}
		$this->Save();
	}

	function Hash($product){
		$s1 = json_encode($product->Id());
		$s2 = json_encode($product->Attributes());
		$s3 = json_encode($product->Addons());
		return md5($s1.$s2.$s3);
	}

	function Add($product){
		if($product instanceof CartProduct){
			$id = $this->Hash($product);
			$this->Cart['products'][$id] = $product;
			$this->Save();
		}else{
			throw new Exception("Error product class", 1);
		}
	}

	function Remove($id){
		unset($this->Cart['products'][$id]);
		$this->Save();
	}

	function CostProducts(){
		$pr = $this->Cart['products'];
		$sum = 0;
		foreach ($pr as $p) {
			echo 'Id: ' . $p->Id() .' --> ' . (float) $p->Price() * (float) $p->Count() .' <br>';
			// echo $p->CostProduct().'<br>';
			// echo $p->CostAddons().'<br>';
			$sum += $p->Cost();
		}
		return $sum;
	}

	function CostCheckout(){
		$sum = $this->CostProducts();
		if($sum < $this->CartDeliveryMin){
			$sum += $this->CartDeliveryCost;
		}
		$cost = $sum - $this->Discount;
		if($cost < 0){ $cost = 0; }
		$cost =  number_format($cost,2);
		return $this->CostCheckout = $cost;
	}

	function Quantity(){
		return count($this->GetProducts());
	}

	function QuantityAll(){
		$cnt = 0;
		foreach ($this->GetProducts() as $v) {
			$cnt += $v->Count();
		}
		return $cnt;
	}

	function Show(){
		echo "<pre>";
		print_r($this->Cart);
		echo "</pre>";
	}
}


/*
// Start session after include autoload.php
session_start();

use Xtra\Cart\Cart;
use Xtra\Cart\CartProduct;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

try{
	// Addons products without addons
	$a1[] = new CartProduct(1, 'Sos', 1.5, 0, 1);
	$a2[] = new CartProduct(11, 'Ser', 1.5, 0, 1);

	// Attributes
	$attr = ['size' => 'M', 'color' => 'green'];

	// Regural Products with addons and atributes
	$p1 = new CartProduct(3, 'Pizza duża', 1.5, 0, 1, $a1);
	$p2 = new CartProduct(1, 'Pizza mała', 1.5, 0, 1, $a2, $attr);

	$delivery_min = 50.00;
	$delivery_cost = 5.00;

	$c = new Cart('username', $delivery_min, $delivery_cost);
	// $c->Discount(3.23);
	// $c->Clear();
	$c->Add($p1);
	$c->Add($p2);

	// $c->Plus('637831ac8966bdfc344185eec2a7940a');

	echo "Products: " . $c->Quantity() ." Cost + delivery: " . $c->CostCheckout();
	$c->Show();

}catch(Exception $e){
	echo $e->getMessage();
	// echo $e->getCode();
}
*/
?>
