<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mysql;

class Order extends Mysql {
	
	/**
	 * Constants
	 */
	public const DURATION_WEEK = 'week';
	public const DURATION_MONTH = 'month';
	public const DURATION_QUARTER = 'quarter';
	public const DURATION_GONE = 'gone';
	public const PAY_WEIXIN = 'weixin';
	public const PAY_BALANCE = 'balance';
	public const IS_UNPAID = 'unpaid';
	public const IS_PAID = 'paid';
	public const IS_SHIPPED = 'shipped';
	public const IS_COMPLETED = 'completed';
	public const IS_REVIEWED = 'reviewed';
	public const IS_CANCELLED = 'cancelled';
	public const IS_CLOSED = 'closed';
	
	/**
	 * public string unique_id(void)
	 */
	public function unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->order();
			$record = $this->field(['order_num'=>['COUNT(*)']])->table(['orders'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['order_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public ?Number|string get_param(string $param_name)
	 */
	public function get_param(string $param_name) {
		$record = $this->field(['*'])->table(['order_default_params'])->where(['id'=>1])->one();
		return $record[$param_name] ?? null;
	}
	
	/**
	 * public integer num(?string $duration = null, ?string $status = null)
	 * @string $duration = Order::DURATION_WEEK|Order::DURATION_MONTH|Order::DURATION_QUARTER|Order::DURATION_GONE
	 * @string $status = Order::IS_UNPAID|Order::IS_PAID|Order::IS_SHIPPED|Order::IS_COMPLETED|Order::IS_REVIEWED|Order::IS_CANCELLED|Order::IS_CLOSED
	 */
	public function num(?string $duration = null, ?string $status = null): int {
		$yes_duration_enums = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		$yes_status_enums = [self::IS_UNPAID, self::IS_PAID, self::IS_SHIPPED, self::IS_COMPLETED, self::IS_CANCELLED, self::IS_CLOSED];
		if(is_null($duration)){
			if(is_null($status)) $record = $this->field(['order_num'=>['COUNT(*)']])->table(['orders'])->one();
			elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['order_num'=>['COUNT(*)']])->table(['orders'])->where(['status'=>$status])->one();
		}elseif(in_array($duration, array_keys($yes_duration_enums), true)){
			$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`add_time`, '%Y-%m-%d'))";
			if(is_null($status)) $record = $this->field(['order_num'=>['COUNT(*)']])->table(['orders'])->where_cmd($duration_prefix . $yes_duration_enums[$duration])->one();
			elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['order_num'=>['COUNT(*)']])->table(['orders'])->where_cmd($duration_prefix . $yes_duration_enums[$duration] . " and `status`='" . $status . "'")->one();
		}
		return $record['order_num'] ?? 0;
	}
	
	/**
	 * public array page(?string $duration = null, ?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $duration = Order::DURATION_WEEK|Order::DURATION_MONTH|Order::DURATION_QUARTER|Order::DURATION_GONE
	 * @string $status = Order::IS_UNPAID|Order::IS_PAID|Order::IS_SHIPPED|Order::IS_COMPLETED|Order::IS_REVIEWED|Order::IS_CANCELLED|Order::IS_CLOSED
	 */
	public function page(?string $duration = null, ?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_duration_enums = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		$yes_status_enums = [self::IS_UNPAID, self::IS_PAID, self::IS_SHIPPED, self::IS_COMPLETED, self::IS_CANCELLED, self::IS_CLOSED];
		if(is_null($duration)){
			if(is_null($status)) $records = $this->_view()->order(['o.id'=>'desc'])->pill($page_num, $page_size);
			elseif(in_array($status, $yes_status_enums, true)) $records = $this->_view()->where(['o.status'=>$status])->order(['o.id'=>'desc'])->pill($page_num, $page_size);
		}elseif(in_array($duration, array_keys($yes_duration_enums), true)){
			$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`o`.`add_time`, '%Y-%m-%d'))";
			if(is_null($status)) $records = $this->_view()->where_cmd($duration_prefix . $yes_duration_enums[$duration])->order(['o.id'=>'desc'])->pill($page_num, $page_size);
			elseif(in_array($status, $yes_status_enums, true)) $records = $this->_view()->where_cmd($duration_prefix . $yes_duration_enums[$duration] . " and `o`.`status`='" . $status . "'")->order(['o.id'=>'desc'])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public array record(string $order_unique_id)
	 */
	public function record(string $order_unique_id): array {
		$ends['basic'] = $this->_basic_children_view()->where(['id'=>$order_id])->one();
		if($ends['basic']){
			$ends['user'] = $this->_user_children_view()->where(['o.id'=>$order_id])->one();
			$ends['product'] = $this->_product_children_view()->where(['od.order_id'=>$order_id])->select();
			if('wechat' == $ends['basic']['pay_method']) $ends['pay'] = $this->_wechat_pay_children_view()->where(['owpr.order_id'=>$order_id])->one();
			elseif('balance' == $ends['basic']['pay_method']) $ends['pay'] = $this->_balance_pay_children_view()->where(['obpr.order_id'=>$order_id])->one();
			else $ends['pay'] = [];
			$ends['express'] = $this->_express_children_view()->where(['e.order_id'=>$order_id])->one();
		}
		return $ends;
	}
	
	/**
	 * public integer remove(integer $order_id)
	 */
	public function remove(int $order_id): int {
		$check_datas = $this->field(['order_num'=>['COUNT(*)'], 'status'])->table(['orders'])->where(['id'=>$order_id])->one();
		if($check_datas){
			if(0 == $check_datas['order_num']) return 0; // error for NO exist order, @@
			elseif($check_datas['status'] != self::is_unpaid) return -2; // error for already paid, @@
			if($this->begin()){
				$end1 = $this->table(['order_details'])->where(['order_id'=>$order_id])->delete();
				$end2 = $this->table(['orders'])->where(['id'=>$order_id])->delete();
				if($end1 > 0 && $end2 > 0 && $this->end()) return $end2;
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer create(integer $user_id, float $total_product_tag_money, float $total_product_discount_money, float $total_tax_money, float $total_express_carriage_money, float $total_coupon_discount_money, float $pay_money, string $address, string $receiver, string $phone, array $details, ?integer $coupon_id = null, ?string $postcode = null, ?string $note =null )
	 */
	public function create(int $user_id, float $total_product_tag_money, float $total_product_discount_money, float $total_tax_money, float $total_express_carriage_money, float $total_coupon_discount_money, float $pay_money, string $address, string $receiver, string $phone, array $details, ?int $coupon_id = null, ?string $postcode = null, ?string $note = null): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$unique_id = $this->unique_id();
		if($this->get_error() == self::ERR_NONE){
			$primary_datas = ['unique_id'=>$unique_id, 'user_id'=>$user_id, 'coupon_id'=>$coupon_id];
			$money_datas = ['money_type'=>$system_money_type, 'total_product_tag_money'=>$total_product_tag_money, 'total_product_discount_money'=>$total_product_discount_money, 'total_tax_money'=>$total_tax_money, 'total_express_carriage_money'=>$total_express_carriage_money, 'total_coupon_discount_money'=>$total_coupon_discount_money, 'pay_money'=>$pay_money];
			$express_datas = ['address'=>$address, 'receiver'=>$receiver, 'phone'=>$phone, 'postcode'=>$postcode];
			$extra_datas = ['note'=>$note];
			$datas = array_merge($primary_datas, $money_datas, $express_datas, $extra_datas);
			if($this->begin()){
				if($this->table(['orders'])->add(merge_time($datas)) > 0){
					$order_id = $this->get_last_id();
					$end_detail = true;
					foreach($details as $detail){
						if($this->validate_order_detail_datas($detail)){
							$detail_datas = array_merge($detail, ['order_id'=>$order_id, 'money_type'=>$system_money_type]);
							if($this->table(['order_details'])->add(merge_time($detail_datas)) > 0) continue;
						}
						$end_detail = false;
						break;
					}
					if($end_detail && $this->end()) return $order_id;
				}
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer cancel(string $order_unique_id, string $reason)
	 */
	public function cancel(string $order_unique_id, string $reason): int {
		$this->clear_error();
		$v_record = $this->field(['order_num'=>['COUNT(*)'], 'id', 'status'])->table(['orders'])->where(['unique_id'=>$order_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['order_num']) return 0;
			elseif($v_record['status'] != self::IS_UNPAID) return -2; // Err : x, -)-
			$datas = ['status'=>self::is_cancelled, 'cancel_reason'=>$reason, 'cancel_time'=>['UNIX_TIMESTAMP()'], 'last_edit_time'=>['UNIX_TIMESTAMP()']];
			return $this->table(['orders'])->where(['id'=>$check_datas['id']])->edit($datas);
		}
		return -1;
	}
	
	/**
	 * public integer closee(string $order_unique_id, string $reason)
	 */
	public function closee(string $order_unique_id, string $reason): int {
		$this->clear_error();
		$check_datas = $this->field(['order_num'=>['COUNT(*)'], 'id', 'status'])->table(['orders'])->where(['unique_id'=>$order_unique_id])->one();
		if($this->get_error() == self::err_none){
			if(0 == $check_datas['order_num']) return 0; // Err : NO exist the order, -_-
			elseif(self::is_closed == $check_datas['status']) return -2; // Err: x, -_-
			$datas = ['status'=>self::is_closed, 'close_reason'=>$reason, 'close_time'=>['UNIX_TIMESTAMP()'], 'last_edit_time'=>['UNIX_TIMESTAMP()']];
			return $this->table(['orders'])->where(['id'=>$check_datas['id']])->edit($datas);
		}
		return -1;
	}
	
	/**
	 * public integer pay(integer $order_id, string $method = Order::pay_method_wechat )
	 */
	public function pay(int $order_id, string $method = self::pay_method_wechat): int {
		$sys_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::param_money_type);
		if(!is_string($sys_money_type)) return -10; // error for CAN'T read system MONEY_TYPE, @@
		$check_datas = $this->field(['order_num'=>['COUNT(*)'], 'total_money_type', 'total_money', 'status'])->table(['orders'])->where(['id'=>$order_id])->one();
		if($check_datas){
			if(0 == $check_datas['order_num']) return 0; // error for NO exist order, @@
			elseif($check_datas['total_money_type'] != $sys_money_type) return -11; // error for NO equal MONEY_TYPE for system and order, @@
			elseif($check_datas['status'] != self::is_unpaid) return -2; // error for NO correct order status, @@
			if(self::pay_method_wechat == $check_datas['total_money_type']){
			}elseif(self::pay_method_balance == $check_datas['total_money_type']){
			}else
				return -3; // error for NO exist pay method, @@
		}
		return -1;
	}
	
	/**
	 * public pay_by_balance(integer $order_id)
	 */
	public function pay_by_balance(int $order_id, float $total_money): int {
		//
	}
	
	/**
	 * public integer ship(integer $order_id, integer $express_corporation_id, string $express_code, float $express_carriage_money, ?string $express_note = null)
	 */
	public function ship(int $order_id, int $express_corporation_id, string $express_code, float $express_carriage_money, ?string $express_note = null): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$v_record = $this->field(['order_num'=>['COUNT(*)'], 'status', 'address', 'receiver', 'phone', 'postcode'])->table(['orders'])->where(['id'=>$order_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['order_num']) return 0;
			elseif($v_record['status'] != self::IS_PAID) return -2; // Err: x, -)-
			elseif($this->begin()){
				$express = new Express($this->operator_type, $this->operator_id);
				$end_express = $express->create($express_corporation_id, $order_id, $express_code, $express_carriage_money, $v_record['address'], $v_record['receiver'], $v_record['phone'], $v_record['postcode'], $express_note);
				$order_datas = ['ship_time'=>['UNIX_TIMESTAMP()'], 'status'=>self::IS_SHIPPED];
				$end_order = $this->table(['orders'])->where(['id'=>$order_id])->edit(merge_time($order_datas, false));
				if($end_express > 0 && $end_order > 0 && $this->end()) return $end_order;
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public array get_order_user_datas(string $order_unique_id)
	 */
	public function get_order_user_datas(string $order_unique_id):array {
		return $this->_order_user_children_view()->where(['o.unique_id'=>$order_unique_id])->one();
	}
	
	/**
	 * public integer clear_unpaid_records(void)
	 */
	public function clear_unpaid_records(): int {
		return $this->table(['gifts'])->where(['status'=>self::is_unpaid])->delete();
	}
	
	/**
	 * public array get_mining_years(void)
	 */
	public function get_mining_years(): array {
		$datas = $this->field(['year'=>["FROM_UNIXTIME(`add_time`, '%Y')"]])->table(['orders'])->group(['year'=>'asc'])->select();
		foreach($datas as $data){
			$ends[] = $data['year'];
		}
		return $ends ?? [];
	}
	
	/**
	 * public array get_mining_month_nums(string $year, ?string $param = null)
	 * @string $param = Order::is_unpaid|Order::is_paid|Order::is_shipped|Order::is_completed|Order::is_cancelled|Order::is_closed
	 */
	public function get_mining_month_nums(string $year, ?string $param = null): array {
		$yes_month_enums = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
		$yes_status_enums = [self::is_unpaid, self::is_paid, self::is_shipped, self::is_completed, self::is_cancelled, self::is_closed];
		if(is_null($param)) $mining_datas = $this->field(['num'=>['COUNT(*)'], 'month'=>["FROM_UNIXTIME(`add_time`, '%m')"]])->table(['orders'])->where_cmd("FROM_UNIXTIME(`add_time`, '%Y')='" . $year . "'")->group(['month'=>'asc'])->select();
		elseif(in_array($param, $yes_status_enums, true)) $mining_datas = $this->field(['num'=>['COUNT(*)'], 'month'=>["FROM_UNIXTIME(`add_time`, '%m')"]])->table(['orders'])->where_cmd("FROM_UNIXTIME(`add_time`, '%Y')='" . $year . "' and `status`='" . $param . "'")->group(['month'=>'asc'])->select();
		else $mining_datas = [];
		foreach($mining_datas as $mining_data){
			$datas[$mining_data['month']] = $mining_data['num'];
		}
		foreach($yes_month_enums as $month){
			$ends[$month] = $datas[$month] ?? 0;
		}
		return $ends;
	}
	
	/**
	 * public array get_mining_month_moneys(string $year, string $param)
	 * @string $param = Order::is_completed
	 */
	public function get_mining_month_moneys(string $year, string $param): array {
		$yes_month_enums = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
		if(self::is_completed == $param) $mining_datas = $this->field(['pay_money'=>['SUM(`require_pay_money`)'], 'month'=>["FROM_UNIXTIME(`add_time`, '%m')"]])->table(['orders'])->where_cmd("FROM_UNIXTIME(`add_time`, '%Y')='$year' and `status`='" . $param . "'")->group(['month'=>'asc'])->select();
		else $mining_datas = [];
		foreach($mining_datas as $mining_data){
			$datas[$mining_data['month']] = $mining_data['pay_money'];
		}
		foreach($yes_month_enums as $month){
			$ends[$month] = $datas[$month] ?? 0;
		}
		return $ends;
	}
	
	/**
	 * protected boolean validate_order_detail_datas(Mixed $datas)
	 */
	protected function validate_order_detail_datas($datas): bool {
		$regulars = ['product_id'=>'integer', 'product_detail_id'=>'integer', 'product_unique_id'=>'string', 'product_code'=>'string', 'product_name'=>'string', 'product_property_enum_data_group'=>'string', 'tag_price'=>'float', 'discount_price'=>'float', 'quantity'=>'integer'];
		return is_my_array($datas, $regulars);
	}
	
	/**
	 * protected array get_order_weixin_pay_datas(integer $order_id)
	 */
	protected function get_order_weixin_pay_datas(int $order_id): array {
		return $this->_weixin_pay_children_view()->where(['order_id'=>$order_id])->one();
	}
	
	/**
	 * protected array get_order_balance_pay_datas(integer $order_id)
	 */
	protected function get_order_balance_pay_datas(int $order_id): array {
		return $this->_balance_pay_children_view()->where(['order_id'=>$order_id])->one();
	}
	
	/**
	 * protected array get_order_weixin_refund_ask_datas(string $order_unique_id)
	 */
	protected function get_order_weixin_refund_ask_datas(string $order_unique_id): array {
		return $this->_order_weixin_refund_ask_children_view()->order(['id'=>'desc'])->select();
	}
	
	/**
	 * protected array get_order_express_datas(integer $order_id)
	 */
	protected function get_order_express_datas(int $order_id): array {
		return $this->_order_express_children_view()->where(['e.order_id'=>$order_id])->order(['id'=>'desc'])->select();
	}
	
	/**
	 * private Order _view(void)
	 */
	private function _view(): Order {
		$o_cols = ['o.id', 'o.unique_id', 'o.total_money_type', 'o.total_tag_money', 'o.total_discount_money', 'o.total_carriage_money', 'o.total_money', 'o.last_edit_time', 'o.add_time', 'o.status'];
		$mc_cols = ['category_id'=>['mc.id'], 'category_code'=>['mc.code'], 'category_name'=>['mc.name']];
		$u_cols = ['user_id'=>['u.id'], 'user_unique_id'=>['u.unique_id'], 'user_nickname'=>['u.nickname']];
		$this->field(array_merge($o_cols, $mc_cols, $u_cols))->table(['m'=>'messages']);
		$this->join(['mc'=>'message_categories', 'm.category_id'=>'mc.id']);
		$this->join(['u'=>'users', 'm.user_id'=>'u.id']);
		return $this;
	}
	
	/**
	 * private Order _basic_children_view(void)
	 */
	private function _basic_children_view(): Order {
		$o_cols = ['id', 'unique_id', 'total_money_type', 'total_tag_money', 'total_discount_money', 'total_carriage_money', 'total_money', 'note', 'pay_method', 'pay_time', 'ship_time', 'complete_time', 'close_reason', 'close_time', 'last_edit_time', 'add_time', 'status'];
		$this->field($o_cols)->table(['orders']);
		return $this;
	}
	
	/**
	 * private Order _order_user_children_view(void)
	 */
	private function _order_user_children_view(): Order {
		$u_cols = ['u.id', 'u.unique_id', 'u.open_id', 'u.nickname', 'u.real_name', 'u.phone', 'u.last_edit_time', 'u.add_time'];
		$this->field($u_cols)->table(['o'=>'orders']);
		$this->join(['u'=>'users', 'o.user_id'=>'u.id']);
		return $this;
	}
	
	/**
	 * private Order _product_children_view(void)
	 */
	private function _product_children_view(): Order {
		$od_cols = ['od.id', 'od.product_id', 'od.product_unique_id', 'od.product_code', 'od.product_name', 'od.product_property_enum', 'od.money_type', 'od.tag_price', 'od.discount_price', 'od.quantity', 'od.add_time'];
		$pp_cols = ['pp.name'];
		$this->field(array_merge($od_cols, $pp_cols))->table(['od'=>'order_details']);
		$this->join(['pp'=>'product_pictures', 'od.product_id'=>'pp.product_id'], 'left');
		return $this;
	}
	
	/**
	 * private Order _order_coupon_children_view(void)
	 */
	private function _order_coupon_children_view(): Order {
		$c_cols = ['c.id', 'c.unique_id', 'c.money_type', 'c.min_charge_money', 'c.discount_money', 'c.begin_time', 'c.end_time', 'c.use_time', 'c.status', 'c.last_edit_time', 'c.add_time'];
		$cm_cols = ['model_id'=>['cm.id'], 'model_code'=>['cm.code']];
		$cc_cols = ['category_id'=>['cc.id'], 'category_code'=>['cc.code']];
		$this->field(array_merge($c_cols, $cm_cols, $cc_cols))->table(['c'=>'coupons']);
		$this->join(['cm'=>'coupon_models', 'c.model_id'=>'cm.id']);
		$this->join(['cc'=>'coupon_categories', 'c.category_id'=>'cc.id']);
		return $this;
	}
	
	/**
	 * private Order _weixin_pay_children_view(void)
	 */
	private function _weixin_pay_children_view(): Order {
		$owpr_cols = ['id', 'notify_transaction_id', 'notify_out_trade_no', 'notify_openid', 'notify_fee_type', 'notify_total_fee', 'notify_cash_fee_type', 'notify_cash_fee', 'notify_time_end', 'last_edit_time', 'add_time'];
		$this->field($owpr_cols)->table(['order_wechat_pay_records']);
		return $this;
	}
	
	/**
	 * private Order _order_balance_pay_children_view(void)
	 */
	private function _order_balance_pay_children_view(): Order {
		$obpr_cols = ['id', 'money_type', 'pay_money', 'last_edit_time', 'add_time'];
		$this->field($obpr_cols)->table(['order_balance_pay_records']);
		return $this;
	}
	
	/**
	 * private Order _order_weixin_refund_ask_children_view(void)
	 */
	private function _order_weixin_refund_ask_children_view(): Order {
		$owrar_cols = ['id', 'result_code', 'refund_id', 'out_refund_no', 'out_trade_no', 'refund_fee_type', 'total_fee', 'refund_fee', 'refund_desc', 'refund_manager', 'last_edit_time', 'add_time'];
		$this->field($owrar_cols)->table(['order_weixin_refund_ask_records']);
		return $this;
	}
	
	/**
	 * private Order _order_weixin_refund_children_view(void)
	 */
	private function _order_weixin_refund_children_view(): Order {
		$owrr_cols = [];
		$this->field($owrr_cols)->table(['order_weixin_refund_records']);
		return $this;
	}
	
	/**
	 * private Order _order_express_children_view(void)
	 */
	private function _order_express_children_view(): Order {
		$e_cols = ['e.id', 'e.unique_id', 'e.code', 'e.money_type', 'e.carriage_money', 'e.address', 'e.receiver', 'e.phone', 'e.postcode', 'e.note', 'e.last_edit_time', 'e.add_time'];
		$ec_cols = ['ec.id'=>['corporation_id'], 'ec.code'=>['corporation_code'], 'ec.name'=>['corporation_name'], 'ec.home_page'=>['corporation_home_page']];
		$this->field(array_merge($e_cols, $ec_cols))->table(['e'=>'expresses']);
		$this->join(['ec'=>'express_corporations', 'e.corporation_id'=>'ec.id']);
		return $this;
	}
	// -- END --
}











