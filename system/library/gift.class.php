<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mysql;

class Gift extends Mysql {
	
	/**
	 * Constants
	 */
	public const PARAM_MAX_GIFT_MODEL_NUM = 'max_gift_model_num';
	public const DURATION_WEEK = 'week';
	public const DURATION_MONTH = 'month';
	public const DURATION_QUARTER = 'quarter';
	public const DURATION_GONE = 'gone';
	public const PAY_WEIXIN = 'weixin';
	public const IS_ENABLED = 'enabled';
	public const IS_DISABLED = 'disabled';
	public const IS_UNPAID = 'unpaid';
	public const IS_PAID = 'paid';
	public const IS_RECHARGED = 'recharged';
	
	/**
	 * public string unique_id(void)
	 */
	public function unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->gift();
			$record = $this->field(['gift_num'=>['COUNT(*)']])->table(['gifts'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['gift_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public ?Number|string get_param(string $param_name)
	 */
	public function get_param(string $param_name) {
		$record = $this->field(['*'])->table(['gift_default_params'])->where(['id'=>1])->one();
		return $record[$param_name] ?? null;
	}
	
	/**
	 * public integer get_category_num(void)
	 */
	public function get_category_num(): int {
		$record = $this->field(['category_num'=>['COUNT(*)']])->table(['gift_categories'])->one();
		return $record['category_num'] ?? 0;
	}
	
	/**
	 * public array get_categories(void)
	 */
	public function get_categories(): array {
		return $this->_category_view()->order(['id'=>'asc'])->line('code', 'id');
	}
	
	/**
	 * public array get_category_page(integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_category_page(int $page_num = 1, int $page_size = 20): array {
		$records = $this->_category_view()->order(['id'=>'asc'])->pill($page_num, $page_size);
		return $this->get_category_extra_datas($records);
	}
	
	/**
	 * public array get_category_record(integer $category_id)
	 */
	public function get_category_record(int $category_id): array {
		$record = $this->_category_view()->where(['id'=>$category_id])->one();
		return $this->get_category_extra_datas($record, false);
	}
	
	/**
	 * public integer get_model_num(?string $status = null)
	 * @string $status = Gift::IS_ENABLED|Gift::IS_DISABLED
	 */
	public function get_model_num(?string $status = null): int {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $record = $this->field(['model_num'=>['COUNT(*)']])->table(['gift_models'])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['model_num'=>['COUNT(*)']])->table(['gift_models'])->where(['status'=>$status])->one();
		return $record['model_num'] ?? 0;
	}
	
	/**
	 * public array get_models(void)
	 */
	public function get_models(): array {
		return $this->_model_view()->where(['gm.status'=>self::IS_ENABLED])->order(['gm.id'=>'asc'])->select();
	}
	
	/**
	 * public array get_model_page(?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Gift::IS_ENABLED|Gift::IS_DISABLED
	 */
	public function get_model_page(?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $records = $this->_model_view()->order(['gm.id'=>'desc'])->pill($page_num, $page_size);
		elseif(in_array($status, $yes_status_enums, true)) $records = $this->_model_view()->where(['gm.status'=>$status])->order(['gm.id'=>'desc'])->pill($page_num, $page_size);
		return $this->get_model_extra_datas($records ?? []);
	}
	
	/**
	 * public array get_model_record(integer $model_id)
	 */
	public function get_model_record(int $model_id): array {
		$record = $this->_model_view()->where(['gm.id'=>$model_id])->one();
		return $this->get_model_extra_datas($record, false);
	}
	
	/**
	 * public integer delete_model(integer $model_id)
	 */
	public function delete_model(int $model_id): int {
		$this->clear_error();
		$v_record = $this->field(['gift_num'=>['COUNT(*)']])->table(['gifts'])->where(['model_id'=>$model_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['gift_num'] > 0) return -2; // Err : x, -)-
			return $this->table(['gift_models'])->where(['id'=>$model_id])->delete();
		}
		return -1;
	}
	
	/**
	 * public integer edit_model(integer $model_id, string $code, string $name)
	 */
	public function edit_model(int $model_id, string $code, string $name): int {
		$this->clear_error();
		$v_record = $this->field(['model_num'=>['COUNT(*)']])->table(['gift_models'])->where(['id'=>$model_id, 'code'=>$code], ['neq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['model_num'] > 0) return -2; // Err : x, -)-
			$model_datas = ['code'=>$code, 'name'=>$name];
			return $this->table(['gift_models'])->where(['id'=>$model_id])->edit(merge_time($model_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer add_model(integer $category_id, string $code, string $name, float $recharge_money, float $tag_price, float $discount_price)
	 */
	public function add_model(int $category_id, string $code, string $name, float $recharge_money, float $tag_price, float $discount_price): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$max_model_num = $this->get_param(self::PARAM_MAX_GIFT_MODEL_NUM);
		$model_num = $this->get_model_num();
		$v_record = $this->field(['model_num'=>['COUNT(*)']])->table(['gift_models'])->where(['code'=>$code])->one();
		if($this->get_error() == self::ERR_NONE){
			if($model_num >= $max_model_num) return -2; // Err : x, -)-
			elseif($v_record['model_num'] > 0) return -3; // Err : x, -)-
			$model_datas = ['category_id'=>$category_id, 'code'=>$code, 'name'=>$name];
			$model_datas += ['money_type'=>$system_money_type, 'recharge_money'=>$recharge_money, 'tag_price'=>$tag_price, 'discount_price'=>$discount_price];
			if($this->table(['gift_models'])->add(merge_time($model_datas)) > 0) return $this->get_last_id();
		}
		return -1;
	}
	
	/**
	 * public integer enable_model(integer $model_id)
	 */
	public function enable_model(int $model_id): int {
		return $this->table(['gift_models'])->where(['id'=>$model_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
	}
	
	/**
	 * public integer disable_model(integer $model_id)
	 */
	public function disable_model(int $model_id): bool {
		return $this->table(['gift_models'])->where(['id'=>$model_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
	}
	
	/**
	 */
	function ____________________________________________________________01() {
	}
	/**
	 */
	
	/**
	 * public integer num(?string $duration = null, ?string $status = null)
	 * @string $duration = Gift::DURATION_WEEK|Gift::DURATION_MONTH|Gift::DURATION_QUARTER|Gift::DURATION_GONE
	 * @string $status = Gift::IS_UNPAID|Gift::IS_PAID|Gift::IS_RECHARGED
	 */
	public function num(?string $duration = null, ?string $status = null): int {
		$yes_duration_enums = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		$yes_status_enums = [self::IS_UNPAID=>"`status`='unpaid'", self::IS_PAID=>"`pay_time` IS NOT NULL`", self::IS_RECHARGED=>"`recharge_time` IS NOT NULL"];
		$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`add_time`, '%Y-%m-%d'))";
		if(is_null($duration)){
			if(is_null($status)) $record = $this->field(['gift_num'=>['COUNT(*)']])->table(['gifts'])->one();
			elseif(in_array($status, array_keys($yes_status_enums), true)) $record = $this->field(['gift_num'=>['COUNT(*)']])->table(['gifts'])->where_cmd($yes_status_enums[$status])->one();
		}elseif(in_array($duration, $yes_duration_enums, true)){
			if(is_null($status)) $record = $this->field(['gift_num'=>['COUNT(*)']])->table(['gifts'])->where_cmd($duration_prefix . $yes_duration_enums[$duration])->one();
			elseif(in_array($status, array_keys($yes_status_enums), true)) $record = $this->field(['gift_num'=>['COUNT(*)']])->table(['gifts'])->where_cmd($duration_prefix . $yes_duration_enums[$duration] . " and " . $yes_status_enums[$status])->one();
		}
		return $record['gift_num'] ?? 0;
	}
	
	/**
	 * public array page(?string $duration = null, ?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $duration = Gift::DURATION_WEEK|Gift::DURATION_MONTH|Gift::DURATION_QUARTER|Gift::DURATION_GONE
	 * @string $status = Gift::IS_UNPAID|Gift::IS_PAID|Gift::IS_RECHARGED
	 */
	public function page(?string $duration = null, ?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_duration_enums = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		$yes_status_enums = [self::IS_UNPAID=>"`g`.`status`='unpaid'", self::IS_PAID=>"`g`.`pay_time` IS NOT NULL`", self::IS_RECHARGED=>"`g`.`recharge_time` IS NOT NULL"];
		$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`g`.`add_time`, '%Y-%m-%d'))";
		if(is_null($duration)){
			if(is_null($status)) $records = $this->_view()->order(['g.id'=>'desc'])->pill($page_num, $page_size);
			elseif(in_array($status, array_keys($yes_status_enums), true)) $records = $this->_view()->where_cmd($yes_status_enums[$status])->order(['g.id'=>'desc'])->pill($page_num, $page_size);
		}elseif(in_array($duration, $yes_duration_enums, true)){
			if(is_null($status)) $records = $this->_view()->where_cmd($duration_prefix . $yes_duration_enums[$duration])->order(['g.id'=>'desc'])->pill($page_num, $page_size);
			elseif(in_array($status, $yes_status_enums, true)) $records = $this->_view()->where_cmd($duration_prefix . $yes_duration_enums[$duration] . " and " . $yes_status_enums[$status])->order(['g.id'=>'desc'])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public array record(string $gift_unique_id)
	 */
	public function record(string $gift_unique_id): array {
		$record = $this->_view()->where(['g.unique_id'=>$gift_unique_id])->one();
		return $this->get_gift_extra_datas($record, false);
	}
	
	/**
	 * public array find(string $gift_unique_id)
	 */
	public function find(string $gift_unique_id): array {
		$record = $this->_view()->where(['g.unique_id'=>$gift_unique_id])->one();
		return $this->get_gift_extra_datas($record, false);
	}
	
	/**
	 * public integer remove(string $gift_unique_id)
	 */
	public function remove(string $gift_unique_id): int {
		$this->clear_error();
		$v_record = $this->field(['gift_num'=>['COUNT(*)'], 'status'])->table(['gifts'])->where(['unique_id'=>$gift_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['gift_num']) return 0; // Err : x, -)-
			elseif(self::IS_PAID == $v_record['status']) return -2; // Err : x, -)-
			elseif(self::IS_RECHARGED == $v_record['status']) return -3; // Err : x, -)-
			return $this->table(['gifts'])->where(['unique_id'=>$gift_unique_id])->delete();
		}
		return -1;
	}
	
	/**
	 * public integer create(integer $model_id, string $user_unique_id)
	 */
	public function create(int $model_id, string $user_unique_id): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$v_record1 = $this->field(['model_num'=>['COUNT(*)'], 'category_id', 'recharge_money', 'discount_price', 'status'])->table(['gift_models'])->where(['id'=>$model_id])->one();
		$v_record2 = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		$unique_id = $this->unique_id();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record1['model_num']) return -2; // Err : x, -)-
			elseif(self::IS_DISABLED == $v_record1['status']) return -3; // Err : x, -)-
			elseif(0 == $v_record2['user_num']) return -4; // Err : x, -)-
			$vm_record = $this->field(['gift_num'=>['COUNT(*)']])->table(['gifts'])->where(['user_id'=>$v_record2['id'], 'status'=>self::IS_UNPAID], ['eq', 'eq'])->one();
			if($vm_record['gift_num'] > 0) return -5; // Err : x, -)-
			elseif($this->get_error() == self::ERR_NONE){
				$gift_datas = ['unique_id'=>$unique_id, 'category_id'=>$v_record1['category_id'], 'model_id'=>$model_id, 'user_id'=>$v_record2['id'], 'code'=>$unique_id];
				$gift_datas += ['money_type'=>$system_money_type, 'recharge_money'=>$v_record1['recharge_money'], 'pay_money'=>$v_record1['discount_price']];
				if($this->table(['gifts'])->add(merge_time($gift_datas)) > 0) return $this->get_last_id();
			}
		}
		return -1;
	}
	
	/**
	 * public integer confirm_weixin_pay(string $result_code, string $transaction_id, string $out_trade_no, string $openid, string $fee_type, float $total_fee, integer $time_end)
	 */
	public function confirm_weixin_pay(string $result_code, string $transaction_id, string $out_trade_no, string $openid, string $fee_type, float $total_fee, int $time_end): int {
		$this->clear_error();
		$v_record = $this->field(['pay_num'=>['COUNT(*)']])->table(['gift_weixin_pay_records'])->where(['notify_transaction_id'=>$transaction_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['pay_num'] > 0) return -2; // Err : x, -)-
			$notify_datas = ['notify_result_code'=>$result_code, 'notify_transaction_id'=>$transaction_id, 'notify_out_trade_no'=>$out_trade_no, 'notify_openid'=>$openid];
			$notify_datas += ['notify_fee_type'=>$fee_type, 'notify_total_fee'=>$total_fee, 'notify_time_end'=>$time_end];
			if($this->table(['gift_weixin_pay_records'])->add(merge_time($notify_datas)) > 0){
				$notify_id = $this->get_last_id();
				$vm_record = $this->field(['gift_num'=>['COUNT(*)'], 'money_type', 'pay_money', 'pay_method', 'pay_time', 'status'])->table(['gifts'])->where(['unique_id'=>$out_trade_no])->one();
				var_dump($vm_record['pay_money']);
				if(0 == $vm_record['gift_num']) return -3; // Err : x, -)-
				elseif(strtolower($vm_record['money_type']) != strtolower($fee_type)) return -4; // Err : x, -)-
				elseif($vm_record['pay_money'] != $total_fee) return -5; // Err : x, -)-
				elseif(self::IS_PAID == $vm_record['status']) return -6; // Err : x, -)-
				elseif(self::IS_RECHARGED == $vm_record['status']) return -7; // Err : x, -)-
				$gift_datas = ['pay_method'=>self::PAY_WEIXIN, 'pay_time'=>$time_end, 'status'=>self::IS_PAID];
				if($this->table(['gifts'])->where(['unique_id'=>$out_trade_no])->edit(merge_time($gift_datas, false)) > 0) return $notify_id;
			}
		}
		return -1;
	}
	
	/**
	 * public integer recharge(string $gift_unique_id)
	 */
	public function recharge(string $gift_unique_id): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$v_record = $this->field(['gift_num'=>['COUNT(*)'], 'user_id', 'money_type', 'recharge_money', 'status'])->table(['gifts'])->where(['unique_id'=>$gift_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['gift_num']) return 0; // Err : x, -)-
			elseif(strtolower($v_record['money_type']) != strtolower($system_money_type)) return -2; // Err : x, -)-
			elseif(self::IS_UNPAID == $v_record['status']) return -3; // Err : x, -)-
			elseif(self::IS_RECHARGED == $v_record['status']) return -4; // Err : x, -)-
			elseif($this->begin()){
				$gift_datas = ['recharge_time'=>['UNIX_TIMESTAMP()'], 'status'=>self::IS_RECHARGED];
				$user_balance_datas = ['user_id'=>$v_record['user_id'], 'money_type'=>$system_money_type, 'change_money'=>$v_record['recharge_money'], 'description'=>'Gift-Card:' . $gift_unique_id, 'operate'=>'recharge', 'operate_manager'=>'-Empty-'];
				$user_datas = ['balance'=>['`balance`+' . $v_record['recharge_money']]];
				$end1 = $this->table(['gifts'])->where(['unique_id'=>$gift_unique_id])->edit(merge_time($gift_datas, false));
				$end2 = $this->table(['user_balance_records'])->add(merge_time($user_balance_datas));
				$end3 = $this->table(['users'])->where(['id'=>$v_record['user_id']])->edit(merge_time($user_datas, false));
				if($end1 > 0 && $end2 > 0 && $end3 > 0 && $this->end()) return $end1;
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer clear_unpaid_records(boolean $waiting = true)
	 */
	public function clear_unpaid_records(bool $waiting = true): int {
		if($waiting) return $this->table(['gifts'])->where(['status'=>self::IS_UNPAID, '(UNIX_TIMESTAMP()-`add_time`)'=>3600], ['eq', 'gr'])->delete();
		return $this->table(['gifts'])->where(['status'=>self::IS_UNPAID])->delete();
	}
	
	/**
	 */
	function ____________________________________________________________02() {
	}
	/**
	 */
	
	/**
	 * public array get_mining_years(void)
	 */
	public function get_mining_years(): array {
		$year_exp = "FROM_UNIXTIME(`add_time`, '%Y')";
		return $this->field(['year'=>[$year_exp]])->table(['gifts'])->group(['year'])->order(['year'=>'asc'])->line('year');
	}
	
	/**
	 * public array get_mining_month_nums(string $year, ?string $status = null)
	 * @string $status = Gift::IS_PAID|Gift::IS_RECHARGED
	 */
	public function get_mining_month_nums(string $year, ?string $status = null): array {
		$months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
		$yes_status_enums = [self::IS_PAID=>'pay_time', self::IS_RECHARGED=>'recharge_time'];
		$month_exp = "FROM_UNIXTIME(`add_time`, '%m')";
		$year_exp = "FROM_UNIXTIME(`add_time`, '%Y')";
		if(is_null($status)) $mining_datas = $this->field(['num'=>['COUNT(*)'], 'month'=>[$month_exp]])->table(['gifts'])->where([$year_exp=>$year])->group(['month'])->order(['month'=>'asc'])->line('num', 'month');
		elseif(in_array($status, array_keys(yes_status_enums), true)) $mining_datas = $this->field(['num'=>['COUNT(*)'], 'month'=>[$month_exp]])->table(['gifts'])->where([$year_exp=>$year, $yes_status_enums[$status]=>null], ['eq', 'nis'])->group(['month'])->order(['month'=>'asc'])->line('num', 'month');
		foreach($months as $month){
			$end_datas[$month] = $mining_datas[$month] ?? 0;
		}
		return $end_datas;
	}
	
	/**
	 * public array get_mining_month_moneys(string $year, ?string $status = null)
	 * @string $status = Gift::IS_PAID|Gift::IS_RECHARGED
	 */
	public function get_mining_month_moneys(string $year, ?string $status = null): array {
		$months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
		$yes_status_enums = [self::IS_PAID=>'pay_time', self::IS_RECHARGED=>'recharge_time'];
		$month_exp = "FROM_UNIXTIME(`add_time`, '%m')";
		$year_exp = "FROM_UNIXTIME(`add_time`, '%Y')";
		if(is_null($status)) $mining_datas = $this->field(['money'=>['SUM(`pay_money`)'], 'month'=>[$month_exp]])->table(['gifts'])->where([$year_exp=>$year])->group(['month'])->order(['month'=>'asc'])->line('money', 'month');
		elseif(in_array($status, array_keys($yes_status_enums), true)) $mining_datas = $this->field(['money'=>['SUM(`pay_money`)'], 'month'=>[$month_exp]])->table(['gifts'])->where([$year_exp=>$year, $yes_status_enums[$status]=>null], ['eq', 'nis'])->group(['month'])->order(['month'=>'asc'])->line('money', 'month');
		foreach($months as $month){
			$end_datas[$month] = $mining_datas[$month] ?? 0;
		}
		return $end_datas;
	}
	
	/**
	 * protected array get_category_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_category_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$gift_nums = $this->get_category_gift_nums();
		$paid_gift_nums = $this->get_category_gift_nums(self::IS_PAID);
		$recharged_gift_nums = $this->get_category_gift_nums(self::IS_RECHARGED);
		$func = function (&$record) use ($gift_nums, $paid_gift_nums, $recharged_gift_nums) {
			$record['gift_num'] = $gift_nums[$record['id']] ?? 0;
			$record['paid_gift_num'] = $paid_gift_nums[$record['id']] ?? 0;
			$record['recharged_gift_num'] = $recharged_gift_nums[$record['id']] ?? 0;
		};
		if($many){
			foreach($datas as &$data){
				$func($data);
			}
		}else
			$func($datas);
		return $datas;
	}
	
	/**
	 * protected array get_category_gift_nums(?string $status = null)
	 * @string $status = Gift::IS_PAID|Gift::IS_RECHARGED
	 */
	protected function get_category_gift_nums(?string $status = null): array {
		$yes_status_enums = [self::IS_PAID=>'pay_time', self::IS_RECHARGED=>'recharge_time'];
		if(is_null($status)) $end_datas = $this->field(['gift_num'=>['COUNT(*)'], 'category_id'])->table(['gifts'])->group(['category_id'])->order(['category_id'=>'asc'])->line('num', 'category_id');
		elseif(in_array($status, array_keys($yes_status_enums), true)) $end_datas = $this->field(['gift_num'=>['COUNT(*)'], 'category_id'])->table(['gifts'])->where([$yes_status_enums[$status]=>null], ['nis'])->group(['category_id'])->order(['category_id'=>'asc'])->line('gift_num', 'category_id');
		return $end_datas ?? [];
	}
	
	/**
	 * protected array get_model_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_model_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$gift_nums = $this->get_model_gift_nums();
		$paid_gift_nums = $this->get_model_gift_nums(self::IS_PAID);
		$recharged_gift_nums = $this->get_model_gift_nums(self::IS_RECHARGED);
		$func = function (&$record) use ($gift_nums, $paid_gift_nums, $recharged_gift_nums) {
			$record['gift_num'] = $gift_nums[$record['id']] ?? 0;
			$record['paid_gift_num'] = $paid_gift_nums[$record['id']] ?? 0;
			$record['recharged_gift_num'] = $recharged_gift_nums[$record['id']] ?? 0;
		};
		if($many){
			foreach($datas as &$data){
				$func($data);
			}
		}else
			$func($datas);
		return $datas;
	}
	
	/**
	 * protected array get_model_gift_nums(?string $status = null)
	 * @string $status = Gift::IS_PAID|Gift::IS_RECHARGED
	 */
	protected function get_model_gift_nums(?string $status = null): array {
		$yes_statuses = [self::IS_PAID=>"pay_time", self::IS_RECHARGED=>"recharge_time"];
		if(is_null($status)) $ends = $this->field(['gift_num'=>['COUNT(*)'], 'model_id'])->table(['gifts'])->group(['model_id'])->order(['model_id'=>'asc'])->line('gift_num', 'model_id');
		elseif(in_array($status, array_keys($yes_statuses), true)) $ends = $this->field(['gift_num'=>['COUNT(*)'], 'model_id'])->table(['gifts'])->where([$yes_statuses[$status]=>null], ['nis'])->group(['model_id'])->order(['model_id'=>'asc'])->line('gift_num', 'model_id');
		return $ends ?? [];
	}
	
	/**
	 * protected array get_gift_extra_datas(array $datas, boolan $many = true)
	 */
	protected function get_gift_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$func = function (&$record) {
			$unique_id = $record['unique_id'];
			$record['weixin_pay_datas'] = $this->get_gift_weixin_pay_datas($record['unique_id']);
		};
		if($many){
			foreach($datas as &$data){
				$func($data);
			}
		}else
			$func($datas);
		return $datas;
	}
	
	/**
	 * protected array get_gift_weixin_pay_datas(string $gift_unique_id)
	 */
	protected function get_gift_weixin_pay_datas(string $gift_unique_id): array {
		return $this->_weixin_pay_children_view()->where(['g.unique_id'=>$gift_unqiue_id])->one();
	}
	
	/**
	 * private Gift _category_view(void)
	 */
	private function _category_view(): Gift {
		$gc_cols = ['id', 'code', 'name', 'description', 'last_edit_time', 'add_time'];
		$extra_cols = ['gift_num'=>null, 'paid_gift_num'=>null, 'recharged_gift_num'=>null];
		$this->field(array_merge($gc_cols, $extra_cols))->table(['gift_categories']);
		return $this;
	}
	
	/**
	 * private Gift _model_view(void)
	 */
	private function _model_view(): Gift {
		$gm_cols = ['gm.id', 'gm.code', 'gm.name', 'gm.money_type', 'gm.recharge_money', 'gm.tag_price', 'gm.discount_price', 'gm.status', 'gm.last_edit_time', 'gm.add_time'];
		$gc_cols = ['category_id'=>['gc.id'], 'category_code'=>['gc.code']];
		$extra_cols = ['gift_num'=>null, 'paid_gift_num'=>null, 'recharged_gift_num'=>null];
		$this->field(array_merge($gm_cols, $gc_cols, $extra_cols))->table(['gm'=>'gift_models']);
		$this->join(['gc'=>'gift_categories', 'gm.category_id'=>'gc.id']);
		return $this;
	}
	
	/**
	 * private Gift _view(void)
	 */
	private function _view(): Gift {
		$g_cols = ['g.id', 'g.unique_id', 'g.code', 'g.require_money_type', 'g.require_recharge_money', 'g.require_pay_money', 'g.recharge_time', 'g.pay_method', 'g.pay_time', 'g.last_edit_time', 'g.add_time', 'g.status'];
		$gm_cols = ['model_id'=>['gm.id'], 'model_code'=>['gm.code']];
		$gc_cols = ['category_id'=>['gc.id'], 'category_code'=>['gc.code']];
		$u_cols = ['user_id'=>['u.id'], 'user_unique_id'=>['u.unique_id'], 'user_nickname'=>['u.nickname']];
		$extra_cols = ['weixin_pay_datas'=>null];
		$this->field(array_merge($g_cols, $gm_cols, $gc_cols, $u_cols, $extra_cols))->table(['g'=>'gifts']);
		$this->join(['gm'=>'gift_models', 'g.model_id'=>'gm.id']);
		$this->join(['gc'=>'gift_categories', 'gm.category_id'=>'gc.id']);
		$this->join(['u'=>'users', 'g.user_id'=>'u.id']);
		return $this;
	}
	
	/**
	 * private Gift _weixin_pay_children_view(void)
	 */
	private function _weixin_pay_children_view(): Gift {
		$gwpr_cols = ['gwpr.id', 'gwpr.notify_result_code', 'gwpr.notify_transaction_id', 'gwpr.notify_out_trade_no', 'gwpr.notify_openid', 'gwpr.notify_fee_type', 'gwpr.notify_total_fee', 'gwpr.notify_time_end', 'gwpr.last_edit_time', 'gwpr.add_time'];
		$this->field($gwpr_cols)->table(['gift_weixin_pay_records']);
		$this->join(['g'=>'gifts', 'gwpr.notify_out_trade_no'=>'g.unique_id']);
		return $this;
	}
	// -- Version : 0.99-Beta [2019-05-15] --
	// -- END --
}



