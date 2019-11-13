<?php
namespace Xtra\Cart;
use \Exception;
use Xtra\Cart\CartProduct;

class Cart
{
	protected $CostProducts = 0;
	protected $CostDelivery = 0;
	protected $CostCheckout = 0;
	protected $Discount = 0;
	protected $Coupon = '';
	protected $Products = null;
	protected $Cart = array();

	function __construct($id = 1, $delivery_min = 0, $delivery_cost = 0, $currency = 'PLN'){
		$this->CartId = $id;
		$this->CartDeliveryMin = $delivery_min;
		$this->CartDeliveryCost = $delivery_cost;

		$_SESSION['cart'][$id]['delivery_min'] = $delivery_min;
		$_SESSION['cart'][$id]['delivery_cost'] = $delivery_cost;
		$_SESSION['cart'][$id]['currency'] = $currency;
	}

	function Clear(){
		unset($_SESSION['cart'][$this->CartId]);
	}

	function Discount($value = 0){
		if($value > 0){
			$this->Discount = $_SESSION['cart'][$this->CartId]['discount'] = (float) $value;
		}
	}

	function Coupon($code = ''){
		if(!empty($code)){
			$this->Coupon = $_SESSION['cart'][$this->CartId]['cupon'] = (float) $code;
		}
	}

	function GetProducts(){
		return $this->Products = $_SESSION['cart'][$this->CartId]['products'];
	}

	function Plus($id){
		$_SESSION['cart'][$this->CartId]['products'][$id]->Count++;
		$this->GetProducts();
	}

	function Minus($id){
		$_SESSION['cart'][$this->CartId]['products'][$id]->Count--;
		if($_SESSION['cart'][$this->CartId]['products'][$id]->Count < 1){
			$_SESSION['cart'][$this->CartId]['products'][$id]->Count = 1;
		}
		$this->GetProducts();
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
			$_SESSION['cart'][$this->CartId]['products'][$id] = $product;
			$this->GetProducts();
		}else{
			throw new Exception("Error product class", 1);
		}
	}

	function Remove($id){
		unset($_SESSION['cart'][$this->CartId]['products'][$id]);
		$this->GetProducts();
	}

	function CostProducts(){
		$pr = $_SESSION['cart'][$this->CartId]['products'];
		$sum = 0;
		foreach ($pr as $p) {
			$sum += $p->Cost();
		}
		return $this->CostProducts = $sum;
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
		$pr = $_SESSION['cart'][$this->CartId]['products'];
		$cnt = 0;
		foreach ($pr as $v) {
			$cnt += $v->Count;
		}
		return $cnt;
	}

	function Show(){
		echo "<pre>";
		print_r($_SESSION['cart'][$this->CartId]);
		echo "</pre>";
	}
}
?>

<?php
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

	// Regural Products with addons and atributes
	$p1 = new CartProduct(3, 'Pizza duża', 1.5, 0, 1, $a1);
	$p2 = new CartProduct(2, 'Pizza mała', 1.5, 1.3, 1, $a2);

	$delivery_min = 50;
	$delivery_cost = 5;

	$c = new Cart('username', $delivery_min, $delivery_cost);
	// $c->Discount(3.23);
	$c->Clear();
	$c->Add($p1);
	$c->Add($p2);
	echo "Products: " . $c->Quantity() ." Cost + delivery: " . $c->CostCheckout();
	// $c->Show();

}catch(Exception $e){
	echo $e->getMessage();
	// echo $e->getCode();
}
*/
?>
