<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mysql;

class User extends Mysql {
	
	/**
	 * Constants
	 */
	public const PARAM_MAX_USER_NUM = 'max_user_num';
	public const PARAM_MAX_USER_CART_RECORD_NUM = 'max_user_cart_record_num';
	public const PARAM_MAX_USER_COLLECTION_RECORD_NUM = 'max_user_collection_record_num';
	public const PARAM_MAX_USER_FOOTMARK_RECORD_NUM = 'max_user_footmark_record_num';
	public const PARAM_MAX_USER_DELIVERY_ADDRESS_RECORD_NUM = 'max_user_delivery_address_record_num';
	public const SEX_SECRECY = 'secrecy';
	public const SEX_MALE = 'male';
	public const SEX_FEMALE = 'female';
	public const IS_UNREGISTERED = 'unregistered';
	public const IS_REGISTERED = 'registered';
	public const IS_LOCKED = 'locked';
	public const IS_ONLINE = 'online';
	public const IS_OFFLINE = 'offline';
	public const IS_DELETED = 'deleted';
	public const IS_NO_MODEL = 'no-model';
	public const IS_NO_ENOUGH = 'no-enough';
	public const IS_ENABLED = 'enabled';
	public const IS_DISABLED = 'disabled';
	public const IS_PAY = 'pay';
	public const IS_REFUND = 'refund';
	public const IS_RECHARGE = 'recharge';
	public const IS_OTHER = 'other';
	
	/**
	 * public string unique_id(void)
	 */
	public function unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->user();
			$record = $this->field(['user_num'=>['COUNT(*)']])->table(['users'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['user_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public string unique_cart_id(void)
	 */
	public function cart_unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->token();
			$record = $this->field(['cart_num'=>['COUNT(*)']])->table(['user_carts'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['cart_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public string unique_collection_id(void)
	 */
	public function collection_unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->token();
			$record = $this->field(['collection_num'=>['COUNT(*)']])->table(['user_collections'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['collection_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public string unique_footmark_id(void)
	 */
	public function footmark_unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->token();
			$record = $this->field(['footmark_num'=>['COUNT(*)']])->table(['user_footmarks'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['footmark_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public string unique_delivery_address_id(void)
	 */
	public function delivery_address_unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->token();
			$record = $this->field(['delivery_address_num'=>['COUNT(*)']])->table(['user_delivery_addresses'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['delivery_address_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public string unique_review_id(void)
	 */
	public function review_unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->token();
			$record = $this->field(['review_num'=>['COUNT(*)']])->table(['user_reviews'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['review_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public ?Number|string get_param(string $param_name)
	 */
	public function get_param(string $param_name) {
		$record = $this->field(['*'])->table(['user_default_params'])->where(['id'=>1])->one();
		return $record[$param_name] ?? null;
	}
	
	//
	FUNCTION ___________________________________________________00() {
	}
	//
	
	/**
	 * public integer num(?string $status = null)
	 * @string $status = User::IS_UNREGISTERED|User::IS_REGISTERED|User::IS_LOCKED
	 */
	public function num(?string $status = null): int {
		$yes_status_enums = [self::IS_UNREGISTERED, self::IS_REGISTERED, self::IS_LOCKED];
		if(is_null($status)) $record = $this->field(['user_num'=>['COUNT(*)']])->table(['users'])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['user_num'=>['COUNT(*)']])->table(['users'])->where(['status'=>$status])->one();
		return $record['user_num'] ?? 0;
	}
	
	/**
	 * public array page(?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = User::IS_UNREGISTERED|User::IS_REGISTERED|User::IS_LOCKED
	 */
	public function page(?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_UNREGISTERED, self::IS_REGISTERED, self::IS_LOCKED];
		if(is_null($status)) $records = $this->_view()->order(['id'=>'desc'])->pill($page_num, $page_size);
		elseif(in_array($status, $yes_status_enums, true)) $records = $this->_view()->where(['status'=>$status])->order(['id'=>'desc'])->pill($page_num, $page_size);
		return $records ?? [];
	}
	
	/**
	 * public array record(string $user_unique_id)
	 */
	public function record(string $user_unique_id): array {
		return $this->_view()->where(['unique_id'=>$user_unique_id])->one();
	}
	
	/**
	 * public array find(string $key, integer $page_num = 1, integer $page_size = 20)
	 */
	public function find(string $key, int $page_num = 1, int $page_size = 20): array {
		return $this->_view()->where(['nickname'=>'%' . $key . '%'], ['lk'])->order(['id'=>'desc'])->pill($page_num, $page_size);
	}
	
	/**
	 * public boolean change(integer $customer_id, array $datas)
	 * @$datas = [string $real_name, string $phone]
	 */
	public function change(int $customer_id, array $datas): bool {
		$end = $this->table(['customer'])->where(['id'=>$customer_id])->edit($datas);
		return $end > 0 ? true : false;
	}
	
	/**
	 * public integer create(string $open_id, string $nickname, string $sex, ?string $head_img_url = null)
	 */
	public function create(string $open_id, string $nickname, string $sex, ?string $head_img_url = null): int {
		$check_datas = $this->field(['user_num'=>['COUNT(*)'], 'nickname', 'sex', 'head_img_url'])->table(['users'])->where(['open_id'=>$open_id])->one();
		if($check_datas){
			if($check_datas['user_num'] > 0){
				$datas = ['nickname'=>$nickname, 'sex'=>$sex, 'head_img_url'=>$head_img_url, 'last_edit_time'=>['UNIX_TIMESTAMP()']];
				if($this->table(['users'])->where(['open_id'=>$open_id])->edit($datas) > 0) return 0;
			}else{
				$basic_datas = ['unqiue_id'=>$this->unique_id(), 'open_id'=>$open_id, 'nickname'=>$nickname, 'sex'=>$sex, 'head_img_url'=>$head_img_url];
				$time_datas = ['last_edit_time'=>['UNIX_TIMESTAMP()'], 'add_time'=>['UNIX_TIMESTAMP()']];
				$datas = array_merge($basic_datas, $time_datas);
				if($this->table(['users'])->add($datas) > 0) return $this->get_last_id();
			}
		}
		return -1;
	}
	
	/**
	 * public boolean lock(integer $customer_id)
	 */
	public function lock(int $customer_id): bool {
		$end = $this->table(['customers'])->where(['id'=>$customer_id])->modify(['status'=>'locked']);
		return $end > 0 ? true : false;
	}
	
	/**
	 * public boolean unlock(integer $customer_id)
	 */
	public function unlock(int $customer_id): bool {
		$end = $this->table(['customers'])->where(['id'=>$customer_id])->modify(['status'=>'normal']);
		return $end > 0 ? true : false;
	}
	
	/**
	 * public boolean register(integer $customer_id, string $phone, string $pwd)
	 */
	public function register(int $customer_id, string $phone, string $pwd): bool {
		$datas = ['phone'=>$phone, 'pwd'=>$pwd];
		$end = $this->tables(['customers'])->where(['id'=>$customer_id])->modify($datas);
		return $end > 0 ? true : false;
	}
	
	/**
	 * public array login(string $phone, string $pwd)
	 */
	public function login(string $email, string $pwd): array {
		$pwd = ["PASSWORD('" . $pwd . "')"];
		$ends = $this->field(['id', 'unique_id', 'open_id', 'phone'])->table(['customers'])->where(['phone'=>$phone, 'pwd'=>$pwd])->select();
		return $ends[0] ?? [];
	}
	
	/**
	 * public boolean password(integer $customer_id, string $pwd)
	 */
	public function password(int $customer_id, string $pwd): bool {
		$pwd = ["PASSWORD('" . $pwd . "')"];
		$end = $this->table(['customers'])->where(['id'=>$customer_id])->modify(['pwd'=>$pwd]);
		return $end > 0 ? true : false;
	}
	
	//
	FUNCTION ___________________________________________________01() {
	}
	//
	
	/**
	 * public integer get_order_num(integer $user_id, ?string $status = null)
	 * @string $status = Order::is_unpaid|Order::is_paid|Order::is_shipped|Order::is_completed|Order::is_cancelled
	 */
	public function get_order_num(int $user_id, ?string $status = null): int {
		$yes_enums = [Order::is_unpaid, Order::is_paid, Order::is_shipped, Order::is_completed, Order::is_cancelled];
		if(is_null($status)) $ends = $this->field(['order_num'=>['COUNT(*)']])->table(['orders'])->where(['user_id'=>$user_id])->one();
		elseif(in_array($status, $yes_enums, true)) $ends = $this->field(['order_num'=>['COUNT(*)']])->table(['orders'])->where(['user_id'=>$user_id, 'status'=>$status])->one();
		return $ends['order_num'] ?? 0;
	}
	
	/**
	 * public array get_order_page(integer $user_id, ?string $status = null, integer $page_num = 1)
	 * @string $status = Order::is_unpaid|Order::is_paid|Order::is_shipped|Order::is_completed|Order::is_cancelled
	 */
	public function get_order_page(int $user_id, ?string $status = null, int $page_num = 1): array {
		$yes_enums = [Order::is_unpaid, Order::is_paid, Order::is_shipped, Order::is_completed, Order::is_cancelled];
		$offset_num = ($page_num - 1) * $this->page_record_num;
		if(is_null($status)) $ends = $this->_order_view()->where(['o.user_id'=>$user_id])->order(['o.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
		elseif(in_array($status, $yes_enums, true)) $ends = $this->_order_view()->where(['o.user_id'=>$user_id, 'o.status'=>$status])->order(['o.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
		return $ends ?? [];
	}
	
	/**
	 * public integer get_gift_num(integer $user_id, ?string $status = null)
	 * @string $status = Gift::is_unpaid|Gift::is_paid|Gift::is_charged
	 */
	public function get_gift_num(int $user_id, ?string $status = null): int {
		$yes_enums = [Gift::is_unpaid, Gift::is_paid, Gift::is_charged];
		if(is_null($status)) $ends = $this->field(['gift_num'=>['COUNT(*)']])->table(['gifts'])->where(['user_id'=>$user_id])->one();
		elseif(in_array($status, $yes_enums, true)) $ends = $this->field(['gift_num'=>['COUNT(*)']])->table(['gifts'])->where(['user_id'=>$user_id, 'status'=>$status])->one();
		return $ends['gift_num'] ?? 0;
	}
	
	/**
	 * public array get_gift_page(integer $user_id, ?string $status = null, integer $page_num = 1)
	 * @string $status = Gift::is_unpaid|Gift::is_paid|Gift::is_charged
	 */
	public function get_gift_page(int $user_id, ?string $status = null, int $page_num = 1): array {
		$yes_enums = [Gift::is_unpaid, Gift::is_paid, Gift::is_charged];
		$offset_num = ($page_num - 1) * $this->page_record_num;
		if(is_null($status)) $ends = $this->_gift_view()->where(['g.user_id'=>$user_id])->order(['g.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
		elseif(in_array($status, $yes_enums, true)) $ends = $this->_gift_view()->where(['g.user_id'=>$user_id, 'g.status'=>$status])->order(['g.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
		return $ends ?? [];
	}
	
	/**
	 * public integer get_coupon_num(integer $user_id, ?string $status = null)
	 * @string $status = Coupon::is_normal|Coupon::is_used|Coupon::is_expired
	 */
	public function get_coupon_num(int $user_id, ?string $status = null): int {
		$yes_enums = [Coupon::is_normal, Coupon::is_used, Coupon::is_expired];
		if(is_null($status)) $ends = $this->field(['coupon_num'=>['COUNT(*)']])->table(['coupons'])->where(['user_id'=>$user_id])->one();
		elseif(in_array($status, $yes_enums, true)) $ends = $this->field(['coupon_num'=>['COUNT(*)']])->table(['coupons'])->where(['user_id'=>$user_id, 'status'=>$status])->one();
		return $ends['coupon_num'] ?? 0;
	}
	
	/**
	 * public array get_coupon_page(integer $user_id, ?string $status = null, integer $page_num = 1)
	 * @string $status = Coupon::is_normal|Coupon::is_used|Coupon::is_expired
	 */
	public function get_coupon_page(int $user_id, ?string $status = null, int $page_num = 1): array {
		$yes_enums = [Coupon::is_normal, Coupon::is_used, Coupon::is_expired];
		$offset_num = ($page_num - 1) * $this->page_record_num;
		if(is_null($status)) $ends = $this->_coupon_view()->where(['c.user_id'=>$user_id])->order(['c.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
		elseif(in_array($status, $yes_enums, true)) $ends = $this->_coupon_view()->where(['c.user_id'=>$user_id, 'c.status'=>$status])->order(['c.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
		return $ends ?? [];
	}
	
	/**
	 * public integer get_message_num(integer $user_id, ?integer $category_id = null, ?string $status = null)
	 * @string $status = Message::is_unread|Message::is_read
	 */
	public function get_message_num(int $user_id, ?int $category_id = null, ?string $status = null): int {
		$yes_enums = [Message::is_unread, Message::is_read];
		if(is_null($category_id)){
			if(is_null($status)) $ends = $this->field(['msg_num'=>['COUNT(*)']])->table(['messages'])->where(['user_id'=>$user_id])->one();
			elseif(in_array($status, $yes_enums, true)) $ends = $this->field(['msg_num'=>['COUNT(*)']])->table(['messages'])->where(['user_id'=>$user_id, 'status'=>$status])->one();
		}elseif(is_int($category_id)){
			if(is_null($status)) $ends = $this->field(['msg_num'=>['COUNT(*)']])->table(['messages'])->where(['user_id'=>$user_id, 'category_id'=>$category_id])->one();
			elseif(in_array($status, $yes_enums, true)) $ends = $this->field(['msg_num'=>['COUNT(*)']])->table(['messages'])->where(['user_id'=>$user_id, 'category_id'=>$category_id, 'status'=>$status])->one();
		}
		return $ends['msg_num'] ?? 0;
	}
	
	/**
	 * public array get_message_page(integer $user_id, ?integer $category_id = null, ?string $status = null, integer $page_num = 1)
	 * @string $status = Message::is_unread|Message::is_read
	 */
	public function get_message_page(int $user_id, ?int $category_id = null, ?string $status = null, int $page_num = 1): array {
		$yes_enums = [Message::is_unread, Message::is_read];
		$offset_num = ($page_num - 1) * $this->page_record_num;
		if(is_null($category_id)){
			if(is_null($status)) $ends = $this->_message_view()->where(['m.user_id'=>$user_id])->order(['m.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
			elseif(in_array($status, $yes_enums, true)) $ends = $this->_message_view()->where(['m.user_id'=>$user_id, 'm.status'=>$status])->order(['m.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
		}elseif(is_int($category_id)){
			if(is_null($status)) $ends = $this->_message_view()->where(['m.user_id'=>$user_id, 'm.category_id'=>$category_id])->order(['m.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
			elseif(in_array($status, $yes_enums, true)) $ends = $this->_message_view()->where(['m.user_id'=>$user_id, 'm.category_id'=>$category_id, 'm.status'=>$status])->order(['m.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
		}
		return $ends ?? [];
	}
	
	//
	FUNCTION ___________________________________________________05() {
	}
	//
	
	/**
	 * public integer get_cart_num(string $user_unique_id)
	 */
	public function get_cart_num(string $user_unique_id): int {
		$this->clear_error();
		$v_record = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['user_num']) return 0; // Err : x, -)-
			$record = $this->field(['cart_num'=>['COUNT(*)']])->table(['user_carts'])->where(['user_id'=>$v_record['id']])->one();
		}
		return $record['cart_num'] ?? 0;
	}
	
	/**
	 * public array get_cart_page(string $user_unique_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_cart_page(string $user_unique_id, int $page_num = 1, int $page_size = 20): array {
		$this->clear_error();
		$v_record = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['user_num']) return []; // Err : x, -)-
			$records = $this->_user_cart_view()->where(['user_id'=>$v_record['id']])->order(['id'=>'desc'])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public array get_cart_record(string $cart_unique_id)
	 */
	public function get_cart_record(string $cart_unique_id): array {
		return $this->_user_cart_view()->where(['uc.unique_id'=>$cart_unique_id])->one();
	}
	
	/**
	 * public integer delete_cart(string $cart_unique_id)
	 */
	public function delete_cart(string $cart_unique_id): int {
		return $this->table(['user_carts'])->where(['unique_id'=>$cart_unique_id])->delete();
	}
	
	/**
	 * public integer edit_cart(string $cart_unique_id, integer $quantity)
	 */
	public function edit_cart(string $cart_unique_id, int $quantity): int {
		$this->clear_error();
		$v_record = $this->field(['cart_num'=>['COUNT(*)'], 'product_model_id'])->table(['user_carts'])->where(['unique_id'=>$cart_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['cart_num']) return -0; // Err : x, -)-
			elseif(is_null($v_record['product_model_id'])) return -2; // Err : x, -)-
			$vm_record = $this->field(['product_model_num'=>['COUNT(*)'], 'stock_num'])->table(['product_models'])->where(['id'=>$v_record['product_model_id']])->one();
			if($this->get_error() == self::ERR_NONE){
				if(0 == $vm_record['product_model_num']) return -3; // Err : x, -)-
				elseif($quantity > $vm_record['stock_num']) return -4; // Err : x, -)-
				return $this->table(['user_carts'])->where(['unique_id'=>$cart_unique_id])->edit(merge_time(['quantity'=>$quantity], false));
			}
		}
		return -1;
	}
	
	/**
	 * public integer add_cart(string $user_unique_id, integer $product_id, integer $product_model_id, integer $quantity)
	 */
	public function add_cart(string $user_unique_id, int $product_id, int $product_model_id, int $quantity): int {
		$this->clear_error();
		$v_record1 = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		$v_record2 = $this->field(['product_num'=>['COUNT(*)'], 'unique_id', 'code', 'name', 'status'])->table(['products'])->where(['id'=>$product_id])->one();
		$v_record3 = $this->field(['product_model_num'=>['COUNT(*)'], 'description', 'stock_num'])->table(['product_models'])->where(['id'=>$product_model_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record1['user_num']) return -2; // Err : x, -)-
			elseif(0 == $v_record2['product_num']) return -3; // Err : x, -)-
			elseif(Product::IS_PREPARED == $v_record2['status']) return -4; // Err : x, -)-
			elseif(Product::IS_OFFLINE == $v_record2['status']) return -5; // Err : x, -)-
			elseif(0 == $v_record3['product_model_num']) return -6; // Err : x, -)-
			$vm_record = $this->field(['cart_num'=>['COUNT(*)'], 'id', 'quantity'])->table(['user_carts'])->where(['user_id'=>$v_record1['id'], 'product_id'=>$product_id, 'product_model_id'=>$product_model_id], ['eq', 'eq', 'eq'])->one();
			$unique_id = $this->cart_unique_id();
			if($this->get_error() == self::ERR_NONE){
				if($vm_record['cart_num'] > 0){
					$total_quantity = $vm_record['quantity'] + $quantity;
					if($total_quantity > $v_record3['stock_num']) return -7;
					return $this->table(['user_carts'])->where(['id'=>$vm_record['id']])->edit(merge_time(['quantity'=>$total_quantity], false));
				}else{
					if($quantity > $v_record3['stock_num']) return -8; // Err : x, -)-
					$cart_datas = ['unique_id'=>$unique_id, 'user_id'=>$v_record1['id'], 'product_id'=>$product_id, 'product_model_id'=>$product_model_id];
					$cart_datas += ['product_unique_id'=>$v_record2['unique_id'], 'product_code'=>$v_record2['code'], 'product_name'=>$v_record2['name']];
					$cart_datas += ['product_model_description'=>$v_record3['description'], 'quantity'=>$quantity];
					return $this->table(['user_carts'])->add(merge_time($cart_datas));
				}
			}
		}
		return -1;
	}
	
	//
	FUNCTION ___________________________________________________08() {
	}
	//
	
	/**
	 * public integer get_collection_num(string $user_unique_id)
	 */
	public function get_collection_num(string $user_unique_id): int {
		$this->clear_error();
		$v_record = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['user_num']) return 0; // Err : x, -)-
			$record = $this->field(['collection_num'=>['COUNT(*)']])->table(['user_collections'])->where(['user_id'=>$v_record['id']])->one();
		}
		return $record['collection_num'] ?? 0;
	}
	
	/**
	 * public array get_collection_page(string $user_unique_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_collection_page(string $user_unique_id, int $page_num = 1, int $page_size = 20): array {
		$this->clear_error();
		$v_record = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['user_num']) return []; // Err : x, -)-
			$records = $this->_user_collection_view()->where(['user_id'=>$v_record['id']])->order(['id'=>'desc'])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public integer delete_collection(string $collection_unique_id)
	 */
	public function delete_collection(string $collection_unique_id): int {
		return $this->table(['user_collections'])->where(['unique_id'=>$collection_unique_id])->delete();
	}
	
	/**
	 * public integer add_collection(string $user_unique_id, integer $product_id)
	 */
	public function add_collection(string $user_unique_id, int $product_id): int {
		$this->clear_error();
		$max_collection_num = $this->get_param(self::PARAM_MAX_USER_COLLECTION_RECORD_NUM);
		$collection_num = $this->get_collection_num($user_unique_id);
		$v_record1 = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		$v_record2 = $this->field(['product_num'=>['COUNT(*)'], 'unique_id', 'code', 'name', 'status'])->table(['products'])->where(['id'=>$product_id])->one();
		$unique_id = $this->collection_unique_id();
		if($this->get_error() == self::ERR_NONE){
			if($collection_num >= $max_collection_num) return -2; // Err : x, -)-
			elseif(0 == $v_record1['user_num']) return -3; // Err : x, -)-
			elseif(0 == $v_record2['product_num']) return -4; // Err : x, -)-
			elseif(Product::IS_PREPARED == $v_record2['status']) return -5; // Err : x, -)-
			elseif(Product::IS_OFFLINE == $v_record2['status']) return -6; // Err : x, -)-
			$collection_datas = ['unique_id'=>$unique_id, 'user_id'=>$v_record1['id'], 'product_id'=>$product_id];
			$collection_datas += ['product_unique_id'=>$v_record2['unique_id'], 'product_code'=>$v_record2['code'], 'product_name'=>$v_record2['name']];
			return $this->table(['user_collections'])->add(merge_time($collection_datas));
		}
		return -1;
	}
	
	//
	FUNCTION ___________________________________________________12() {
	}
	//
	
	/**
	 * public integer get_footmark_num(string $user_unique_id)
	 */
	public function get_footmark_num(string $user_unique_id): int {
		$this->clear_error();
		$v_record = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['user_num']) return 0; // Err : x, -)-
			$record = $this->field(['footmark_num'=>['COUNT(*)']])->table(['user_footmarks'])->where(['user_id'=>$v_record['id']])->one();
		}
		return $record['footmark_num'] ?? 0;
	}
	
	/**
	 * public array get_footmark_page(string $user_unique_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_footmark_page(string $user_unique_id, int $page_num = 1, int $page_size = 20): array {
		$this->clear_error();
		$v_record = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['user_num']) return []; // Err : x, -)-
			$records = $this->_user_footmark_view()->where(['user_id'=>$v_record['id']])->order(['id'=>'desc'])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public integer delete_footmark(string $footmark_unique_id)
	 */
	public function delete_footmark(string $footmark_unique_id): int {
		return $this->table(['user_footmarks'])->where(['unique_id'=>$footmark_unique_id])->delete();
	}
	
	/**
	 * public integer add_footmark(string $user_unique_id, integer $product_id)
	 */
	public function add_footmark(string $user_unique_id, int $product_id): int {
		$this->clear_error();
		$max_footmark_num = $this->get_param(self::PARAM_MAX_USER_FOOTMARK_RECORD_NUM);
		$footmark_num = $this->get_footmark_num($user_unique_id);
		$v_record1 = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		$v_record2 = $this->field(['product_num'=>['COUNT(*)'], 'unique_id', 'code', 'name', 'status'])->table(['products'])->where(['id'=>$product_id])->one();
		$unique_id = $this->footmark_unique_id();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record1['user_num']) return -2; // Err : x, -)-
			elseif(0 == $v_record2['product_num']) return -3; // Err : x, -)-
			elseif(Product::IS_PREPARED == $v_record2['status']) return -4; // Err : x, -)-
			elseif(Product::IS_OFFLINE == $v_record2['status']) return -5; // Err : x, -)-
			$footmark_datas = ['unique_id'=>$unique_id, 'user_id'=>$v_record1['id'], 'product_id'=>$product_id];
			$footmark_datas += ['product_unique_id'=>$v_record2['unique_id'], 'product_code'=>$v_record2['code'], 'product_name'=>$v_record2['name']];
			$vm_record = $this->field(['footmark_num'=>['COUNT(*)'], 'id'])->table(['user_footmarks'])->where(['user_id'=>$v_record1['id'], 'product_id'=>$product_id], ['eq', 'eq'])->one();
			if($this->get_error() == self::ERR_NONE){
				if($vm_record['footmark_num'] > 0){
					if($this->begin()){
						$end1 = $this->table(['user_footmarks'])->where(['id'=>$vm_record['id']])->delete();
						$end2 = $this->table(['user_footmarks'])->add(merge_time($footmark_datas));
						if($end1 > 0 && $end2 > 0 && $this->end()) return $end2;
						$this->rollback();
					}
					return -1;
				}else{
					if($footmark_num >= $max_footmark_num){
						//
						
						//
					}else{
						return $this->table(['user_footmarks'])->add(merge_time($footmark_datas));
					}
				}
			}
		}
		return -1;
	}
	
	//
	FUNCTION ___________________________________________________13() {
	}
	//
	
	/**
	 * public integer get_delivery_address_num(string $user_unique_id)
	 */
	public function get_delivery_address_num(string $user_unique_id): int {
		$this->clear_error();
		$v_record = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['user_num']) return 0; // Err : x, -)-
			$record = $this->field(['delivery_address_num'=>['COUNT(*)']])->table(['user_delivery_addresses'])->where(['user_id'=>$v_record['id']])->one();
		}
		return $record['delivery_address_num'] ?? 0;
	}
	
	/**
	 * public array get_delivery_address_page(string $user_unique_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_delivery_address_page(string $user_unique_id, int $page_num = 1, int $page_size = 20): array {
		$this->clear_error();
		$v_record = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['user_num']) return []; // Err : x, -)-
			$records = $this->_user_delivery_address_view()->where(['user_id'=>$v_record['id']])->order(['id'=>'desc'])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public array get_delivery_address_record(string $delivery_address_unique_id)
	 */
	public function get_delivery_address_record(string $delivery_address_unique_id): array {
		return $this->_user_delivery_address_view()->where(['unique_id'=>$delivery_address_unique_id])->one();
	}
	
	/**
	 * public integer delete_delivery_address(string $delivery_address_unique_id)
	 */
	public function delete_delivery_address(string $delivery_address_unqiue_id): int {
		return $this->table(['user_delivery_addresses'])->where(['unique_id'=>$delivery_address_unqiue_id])->delete();
	}
	
	/**
	 * public integer edit_delivery_address(string $delivery_address_unique_id, integer $express_address_region_id, integer $express_address_province_id, integer $express_address_city_id, integer $express_address_county_id, integer $express_address_town_id, string $primary_address, string $detail_address, string $receiver, string $phone, ?string $postcode = null)
	 */
	public function edit_delivery_address(string $delivery_address_unique_id, int $express_address_region_id, int $express_address_province_id, int $express_address_city_id, int $express_address_county_id, int $express_address_town_id, string $primary_address, string $detail_address, string $receiver, string $phone, ?string $postcode = null): int {
		//
		$this->clear_error();
		$v_record1 = $this->field(['region_num'=>['COUNT(*)'], 'status'])->table(['express_address_regions'])->where(['id'=>$express_address_region_id])->one();
		$v_record2 = $this->field(['province_num'=>['COUNT(*)'], 'status'])->table(['express_address_provinces'])->where(['id'=>$express_address_province_id])->one();
		$v_record3 = $this->field(['city_num'=>['COUNT(*)'], 'status'])->table(['express_address_cities'])->where(['id'=>$express_address_city_id])->one();
		$v_record4 = $this->field(['county_num'=>['COUNT(*)'], 'status'])->table(['express_address_counties'])->where(['id'=>$express_address_county_id])->one();
		$v_record5 = $this->field(['town_num'=>['COUNT(*)'], 'status'])->table(['express_address_towns'])->where(['id'=>$express_address_town_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record1['region_num']) return -2; // Err : x, -)-
			elseif(Express::IS_DISABLED == $v_record1['status']) return -3; // Err : x, -)-
			elseif(0 == $v_record2['province_num']) return -4; // Err : x, -)-
			elseif(Express::IS_DISABLED == $v_record2['status']) return -5; // Err : x, -)-
			elseif(0 == $v_record3['city_num']) return -6; // Err : x, -)-
			elseif(Express::IS_DISABLED == $v_record3['status']) return -7; // Err : x, -)-
			elseif(0 == $v_record4['county_num']) return -8; // Err : x, -)-
			elseif(Express::IS_DISABLED == $v_record4['status']) return -9; // Err : x, -)-
			elseif(0 == $v_record5['town_num']) return -10; // Err : x, -)-
			elseif(Express::IS_DISABLED == $v_record5['status']) return -11; // Err : x, -)-
			$delivery_address_datas = ['express_address_region_id'=>$express_address_region_id, 'express_address_province_id'=>$express_address_province_id, 'express_address_city_id'=>$express_address_city_id, 'express_address_county_id'=>$express_address_county_id, 'express_address_town_id'=>$express_address_town_id];
			$delivery_address_datas += ['primary_address'=>$primary_address, 'detail_address'=>$detail_address, 'receiver'=>$receiver, 'phone'=>$phone, 'postcode'=>$postcode];
			return $this->table(['user_delivery_addresses'])->where(['unique_id'=>$delivery_address_unique_id])->edit(merge_time($delivery_address_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer add_delivery_address(string $user_unique_id, integer $express_address_region_id, integer $express_address_province_id, integer $express_address_city_id, integer $express_address_county_id, integer $express_address_town_id, string $primary_address, string $detail_address, string $receiver, string $phone, ?string $postcode = null)
	 */
	public function add_delivery_address(string $user_unique_id, int $express_address_region_id, int $express_address_province_id, int $express_address_city_id, int $express_address_county_id, int $express_address_town_id, string $primary_address, string $detail_address, string $receiver, string $phone, ?string $postcode = null): int {
		$this->clear_error();
		$max_delivery_address_num = $this->get_param(self::PARAM_MAX_USER_DELIVERY_ADDRESS_RECORD_NUM);
		$delivery_address_num = $this->get_delivery_address_num($user_unique_id);
		$v_record1 = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		$v_record2 = $this->field(['region_num'=>['COUNT(*)'], 'status'])->table(['express_address_regions'])->where(['id'=>$express_address_region_id])->one();
		$v_record3 = $this->field(['province_num'=>['COUNT(*)'], 'status'])->table(['express_address_provinces'])->where(['id'=>$express_address_province_id])->one();
		$v_record4 = $this->field(['city_num'=>['COUNT(*)'], 'status'])->table(['express_address_cities'])->where(['id'=>$express_address_city_id])->one();
		$v_record5 = $this->field(['county_num'=>['COUNT(*)'], 'status'])->table(['express_address_counties'])->where(['id'=>$express_address_county_id])->one();
		$v_record6 = $this->field(['town_num'=>['COUNT(*)'], 'status'])->table(['express_address_towns'])->where(['id'=>$express_address_town_id])->one();
		$unique_id = $this->delivery_address_unique_id();
		if($this->get_error() == self::ERR_NONE){
			if($delivery_address_num >= $max_delivery_address_num) return -2; // Err : x, -)-
			elseif(0 == $v_record1['user_num']) return -3; // Err : x, -)-
			elseif(0 == $v_record2['region_num']) return -4; // Err : x, -)-
			elseif(Express::IS_DISABLED == $v_record2['status']) return -5; // Err : x, -)-
			elseif(0 == $v_record3['province_num']) return -6; // Err : x, -)-
			elseif(Express::IS_DISABLED == $v_record3['status']) return -7; // Err : x, -)-
			elseif(0 == $v_record4['city_num']) return -8; // Err : x, -)-
			elseif(Express::IS_DISABLED == $v_record4['status']) return -9; // Err : x, -)-
			elseif(0 == $v_record5['county_num']) return -10; // Err : x, -)-
			elseif(Express::IS_DISABLED == $v_record5['status']) return -11; // Err : x, -)-
			elseif(0 == $v_record6['town_num']) return -12; // Err : x, -)-
			elseif(Express::IS_DISABLED == $v_record6['status']) return -13; // Err : x, -)-
			$delivery_address_datas = ['unique_id'=>$unique_id, 'user_id'=>$v_record1['id']];
			$delivery_address_datas += ['express_address_region_id'=>$express_address_region_id, 'express_address_province_id'=>$express_address_province_id, 'express_address_city_id'=>$express_address_city_id, 'express_address_county_id'=>$express_address_county_id, 'express_address_town_id'=>$express_address_town_id];
			$delivery_address_datas += ['primary_address'=>$primary_address, 'detail_address'=>$detail_address, 'receiver'=>$receiver, 'phone'=>$phone, 'postcode'=>$postcode];
			return $this->table(['user_delivery_addresses'])->add(merge_time($delivery_address_datas));
		}
		return -1;
	}
	
	/**
	 * public integer set_default_delivery_address(string $delivery_address_unique_id)
	 */
	public function set_default_delivery_address(string $delivery_address_unique_id): int {
		$this->clear_error();
		$v_record = $this->field(['delivery_address_num'=>['COUNT(*)'], 'user_id', 'status'])->table(['user_delivery_addresses'])->where(['unique_id'=>$delivery_address_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['delivery_address_num']) return -2; // Err : x, -)-
			elseif(self::IS_DISABLED == $v_record['status']) return -3; // Err : x, -)-
			if($this->begin()){
				$end1 = $this->table(['user_delivery_addresses'])->where(['user_id'=>$v_record['user_id']])->edit(merge_time(['is_default'=>false], false));
				$end2 = $this->table(['user_delivery_addresses'])->where(['unique_id'=>$delivery_address_unique_id])->edit(merge_time(['is_default'=>true], false));
				if($end1 > 0 && $end2 > 0 && $this->end()) return $end2;
				$this->rollback();
			}
		}
		return -1;
	}
	
	//
	FUNCTION ___________________________________________________14() {
	}
	//
	
	/**
	 * public integer get_review_num(integer $user_id)
	 */
	public function get_review_num(int $user_id): int {
		$ends = $this->field(['review_num'=>['COUNT(*)']])->table(['user_reviews'])->where(['user_id'=>$user_id])->one();
		return $ends['review_num'] ?? 0;
	}
	
	/**
	 * public array get_review_page(integer $user_id, integer $page_num = 1)
	 */
	public function get_review_page(int $user_id, int $page_num = 1): array {
		$offset_num = ($page_num - 1) * $this->page_record_num;
		return $this->_review_view()->where(['ur.user_id'=>$user_id])->order(['ur.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
	}
	
	//
	FUNCTION ___________________________________________________02() {
	}
	//
	
	/**
	 * private User _view(void)
	 */
	private function _view(): User {
		$u_cols = ['id', 'unique_id', 'open_id', 'nickname', 'sex', 'head_img_url', 'real_name', 'phone', 'balance', 'growth', 'point', 'status', 'last_edit_time', 'add_time'];
		$this->field($u_cols)->table(['users']);
		return $this;
	}
	
	/**
	 * private User _user_order_view(void)
	 */
	private function _user_order_view(): User {
		$o_cols = ['o.id', 'o.unique_id', 'o.total_tag_money', 'o.total_discount_money', 'o.total_express_carriage_money', 'o.total_money', 'o.add_time', 'o.status'];
		$this->field(array_merge($o_cols))->table(['o'=>'orders']);
		$this->field(['gc.id', 'model_id'=>'gcm.id', 'model_code'=>'gcm.code', 'gc.unique_id', 'gc.transaction_id', 'gc.code', 'gcm.recharge_money', 'gcm.sale_price', 'gc.pay_time', 'gc.recharge_time', 'gc.add_time', 'gc.status']);
		$this->table(['gc'=>'gift_cards'])->join(['gcm'=>'gift_card_models', 'gc.model_id'=>'gcm.id']);
		return $this;
	}
	
	/**
	 * private User _user_gift_view(void)
	 */
	private function _user_gift_view(): User {
		$this->field(['gc.id', 'model_id'=>'gcm.id', 'model_code'=>'gcm.code', 'gc.unique_id', 'gc.code', 'gcm.recharge_money', 'gcm.sale_price', 'gc.add_time', 'gc.status']);
		$this->table(['gc'=>'gift_cards'])->join(['gcm'=>'gift_card_models', 'gc.model_id'=>'gcm.id']);
		return $this;
	}
	
	/**
	 * private User _user_coupon_view(void)
	 */
	private function _user_coupon_view(): User {
		return $this;
	}
	
	/**
	 * private User _user_message_view(void)
	 */
	private function _user_message_view(): User {
		return $this;
	}
	
	/**
	 * private User _user_cart_view(void)
	 */
	private function _user_cart_view(): User {
		$uc_cols = ['uc.id', 'uc.unique_id', 'uc.product_id', 'uc.product_model_id', 'uc.product_unique_id', 'uc.product_code', 'uc.product_name', 'uc.product_model_description', 'uc.quantity', 'uc.status', 'uc.last_edit_time', 'uc.add_time'];
		$pm_cols = ['pm.money_type', 'pm.tag_price', 'pm.discount_price'];
		$this->field(array_merge($uc_cols, $pm_cols))->table(['uc'=>'user_carts']);
		$this->join(['pm'=>'product_models', 'uc.product_model_id'=>'pm.id'], 'left');
		return $this;
	}
	
	/**
	 * private User _user_collection_view(void)
	 */
	private function _user_collection_view(): User {
		$uc_cols = ['uc.id', 'uc.product_id', 'uc.product_unique_id', 'uc.product_code', 'uc.product_name', 'uc.status', 'uc.last_edit_time', 'uc.add_time'];
		$p_cols = ['product_min_tag_price'=>['p.min_tag_price'], 'product_min_discount_price'=>['p.min_discount_price']];
		$this->field(array_merge($uc_cols, $p_cols))->table(['uc'=>'user_collections']);
		$this->join(['p'=>'products', 'uc.product_id'=>'p.id'], 'left');
		return $this;
	}
	
	/**
	 * private User _user_footmark_view(void)
	 */
	private function _user_footmark_view(): User {
		$uf_cols = ['uf.id', 'uf.product_id', 'uf.product_unique_id', 'uf.product_code', 'uf.product_name', 'uf.status', 'uf.last_edit_time', 'uf.add_time'];
		$p_cols = ['product_min_tag_price'=>['p.min_tag_price'], 'product_min_discount_price'=>['p.min_discount_price']];
		$this->field(array_merge($uf_cols, $p_cols))->table(['uf'=>'user_footmarks']);
		$this->join(['p'=>'products', 'uf.product_id'=>'p.id'], 'left');
		return $this;
	}
	
	/**
	 * private User _user_delivery_address_view(void)
	 */
	private function _user_delivery_address_view(): User {
		$uda_cols = ['id', 'unique_id', 'express_address_region_id', 'express_address_province_id', 'express_address_city_id', 'express_address_county_id', 'express_address_town_id', 'primary_address', 'detail_address', 'receiver', 'phone', 'postcode', 'is_default', 'status', 'last_edit_time', 'add_time'];
		$this->field($uda_cols)->table(['user_delivery_addresses']);
		return $this;
	}
	
	/**
	 * private User _user_review_view(void)
	 */
	private function _user_review_view(): User {
		return $this;
	}
	// -- Version : 0.99-Beta [2019-05-31] --
	// -- END --
}











