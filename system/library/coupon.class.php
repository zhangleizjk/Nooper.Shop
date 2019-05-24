<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mysql;

class Coupon extends Mysql {
	
	/**
	 * Constants
	 */
	public const DURATION_WEEK = 'week';
	public const DURATION_MONTH = 'month';
	public const DURATION_QUARTER = 'quarter';
	public const DURATION_GONE = 'gone';
	public const IS_ENABLED = 'enabled';
	public const IS_DISABLED = 'disabled';
	public const IS_EXPIRED = 'expired';
	public const IS_UNUSED = 'unused';
	public const IS_USED = 'used';
	
	/**
	 * public string unique_id(void)
	 */
	public function unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->coupon();
			$record = $this->field(['coupon_num'=>['COUNT(*)']])->table(['coupons'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['coupon_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public ?(Number|string) get_param(string $param_name)
	 */
	public function get_param(string $param_name) {
		$record = $this->field(['*'])->table(['coupon_default_params'])->where(['id'=>1])->one();
		return $record[$param_name] ?? null;
	}
	
	/**
	 * public integer get_category_num(void)
	 */
	public function get_category_num(): int {
		$record = $this->field(['category_num'=>['COUNT(*)']])->table(['coupon_categories'])->one();
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
	 * @stirng $status = Coupon::IS_ENABLED|Coupon::IS_DISABLED|Coupon::IS_EXPIRED
	 */
	public function get_model_num(?string $status = null): int {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED, self::IS_EXPIRED];
		if(is_null($status)) $record = $this->field(['model_num'=>['COUNT(*)']])->table(['coupon_models'])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['model_num'=>['COUNT(*)']])->table(['coupon_models'])->where(['status'=>$status])->one();
		return $record['model_num'] ?? 0;
	}
	
	/**
	 * public array get_models(void)
	 */
	public function get_models(): array {
		return $this->_model_view()->where(['cm.status'=>self::IS_ENABLED])->order(['cm.id'=>'asc'])->select();
	}
	
	/**
	 * public array get_model_page(?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @stirng $status = Coupon::IS_ENABLED|Coupon::IS_DISABLED|Coupon::IS_EXPIRED
	 */
	public function get_model_page(?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED, self::IS_EXPIRED];
		if(is_null($status)) $records = $this->_model_view()->order(['cm.id'=>'desc'])->pill($page_num, $page_size);
		elseif(in_array($status, $yes_status_enums, true)) $records = $this->_model_view()->where(['cm.status'=>$status])->order(['cm.id'=>'desc'])->pill($page_num, $page_size);
		return $this->get_model_extra_datas($records ?? []);
	}
	
	/**
	 * public array get_model_record(integer $model_id)
	 */
	public function get_model_record(int $model_id): array {
		$record = $this->_model_view()->where(['cm.id'=>$model_id])->one();
		return $this->get_model_extra_datas($record, false);
	}
	
	/**
	 * public integer delete_model(integer $model_id)
	 */
	public function delete_model(int $model_id): int {
		$this->clear_error();
		$v_record = $this->field(['coupon_num'=>['COUNT(*)']])->table(['coupons'])->where(['model_id'=>$model_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['coupon_num'] > 0) return -2; // Err : x, -)-
			return $this->table(['coupon_models'])->where(['id'=>$model_id])->delete();
		}
		return -1;
	}
	
	/**
	 * public integer edit_model(integer $model_id, string $code, string $name)
	 */
	public function edit_model(int $model_id, string $code, string $name): int {
		$this->clear_error();
		$v_record = $this->field(['model_num'=>['COUNT(*)']])->table(['coupon_models'])->where(['id'=>$model_id, 'code'=>$code], ['neq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['model_num'] > 0) return -2; // Err : x, -)-
			$model_datas = ['code'=>$code, 'name'=>$name];
			return $this->table(['coupon_models'])->where(['id'=>$model_id])->edit(merge_time($model_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer add_model(integer $category_id, string $code, string $name, float $min_charge_money, float $discount_money, integer $begin_time, integer $end_time)
	 */
	public function add_model(int $category_id, string $code, string $name, float $min_charge_money, float $discount_money, int $begin_time, int $end_time): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$v_record = $this->field(['model_num'=>['COUNT(*)'], 'now_time'=>['UNIX_TIMESTAMP()']])->table(['coupon_models'])->where(['code'=>$code])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['model_num'] > 0) return -2; // Err : x, -)-
			elseif($v_record['now_time'] > $begin_time) return -3; // Err : x, -)-
			elseif($begin_time >= $end_time) return -4; // Err : x, -)-
			$model_datas = ['category_id'=>$category_id, 'code'=>$code, 'name'=>$name];
			$model_datas += ['money_type'=>$system_money_type, 'min_charge_money'=>$min_charge_money, 'discount_money'=>$discount_money];
			$model_datas += ['begin_time'=>$begin_time, 'end_time'=>$end_time];
			if($this->table(['coupon_models'])->add(merge_time($model_datas)) > 0) return $this->get_last_id();
		}
		return -1;
	}
	
	/**
	 * public integer enable_model(integer $model_id)
	 */
	public function enable_model(int $model_id): int {
		$this->clear_error();
		$v_record = $this->field(['model_num'=>['COUNT(*)'], 'now_time'=>['UNIX_TIMESTAMP()'], 'end_time', 'status'])->table(['coupon_models'])->where(['id'=>$model_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['model_num']) return 0; // Err : x, -)-
			elseif(self::IS_EXPIRED == $v_record['status']) return -2; // Err : x, -)-
			elseif($v_record['now_time'] > $v_record['end_time']){
				$this->refresh_expired_records();
				return -3; // Err : x, -)-
			}
			return $this->table(['coupon_models'])->where(['id'=>$model_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
		}
		return -1;
	}
	
	/**
	 * public integer disable_model(integer $model_id)
	 */
	public function disable_model(int $model_id): int {
		$this->clear_error();
		$v_record = $this->field(['model_num'=>['COUNT(*)'], 'now_time'=>['UNIX_TIMESTAMP()'], 'end_time', 'status'])->table(['coupon_models'])->where(['id'=>$model_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['model_num']) return 0; // Err : x, -)-
			elseif(self::IS_EXPIRED == $v_record['status']) return -2; // Err : x, -)-
			elseif($v_record['now_time'] > $v_record['end_time']){
				$this->refresh_expired_records();
				return -3; // Err : x, -)-
			}
			return $this->table(['coupon_models'])->where(['id'=>$model_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
		}
		return -1;
	}
	
	/**
	 * public integer num(?string $duration = null, ?string $status = null)
	 * @string $duration = Coupon::DURATION_WEEK|Coupon::DURATION_MONTH|Coupon::DURATION_QUARTER|Coupon::DURATION_GONE
	 * @string $status = Coupon::IS_UNUSED|Coupon::IS_USED|Coupon::IS_EXPIRED
	 */
	public function num(?string $duration = null, ?string $status = null): int {
		$yes_duration_enums = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		$yes_status_enums = [self::IS_UNUSED, self::IS_USED, self::IS_EXPIRED];
		if(is_null($duration)){
			if(is_null($status)) $record = $this->field(['coupon_num'=>['COUNT(*)']])->table(['coupons'])->one();
			elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['coupon_num'=>['COUNT(*)']])->table(['coupons'])->where(['status'=>$status])->one();
		}elseif(in_array($duration, $yes_duration_enums, true)){
			$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`add_time`, '%Y-%m-%d'))";
			if(is_null($status)) $record = $this->field(['coupon_num'=>['COUNT(*)']])->table(['coupons'])->where_cmd($duration_prefix . $yes_duration_enums[$duration])->one();
			elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['coupon_num'=>['COUNT(*)']])->table(['coupons'])->where_cmd($duration_prefix . $yes_duration_enums[$duration] . " and `status`='" . $status . "'")->one();
		}
		return $record['coupon_num'] ?? 0;
	}
	
	/**
	 * public array page(?string $duration = null, ?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $duration = Coupon::DURATION_WEEK|Coupon::DURATION_MONTH|Coupon::DURATION_QUARTER|Coupon::DURATION_GONE
	 * @string $status = Coupon::IS_UNUSED|Coupon::IS_USED|Coupon::IS_EXPIRED
	 */
	public function page(?string $duration = null, ?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_duration_enums = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		$yes_status_enums = [self::IS_UNUSED, self::IS_USED, self::IS_EXPIRED];
		if(is_null($duration)){
			if(is_null($status)) $records = $this->_view()->order(['c.id'=>'desc'])->pill($page_num, $page_size);
			elseif(in_array($status, $yes_status_enums, true)) $records = $this->_view()->where(['c.status'=>$status])->order(['c.id'=>'desc'])->pill($page_num, $page_size);
		}elseif(in_array($duration, $yes_duration_enums, true)){
			$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`c`.`add_time`, '%Y-%m-%d'))";
			if(is_null($status)) $records = $this->_view()->where_cmd($duration_prefix . $yes_duration_enums[$duration])->order(['c.id'=>'desc'])->pill($page_num, $page_size);
			elseif(in_array($status, $yes_status_enums, true)) $records = $this->_view()->where_cmd($duration_prefix . $yes_duration_enums[$duration] . " and `c`.`status`='" . $status . "'")->order(['c.id'=>'desc'])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public array record(stirng $coupon_unique_id)
	 */
	public function record(string $coupon_unique_id): array {
		return $this->_view()->where(['c.unique_id'=>$coupon_unique_id])->one();
	}
	
	/**
	 * public integer remove(string $coupon_unique_id)
	 */
	public function remove(string $coupon_unique_id): int {
		$this->clear_error();
		$v_record = $this->field(['coupon_num'=>['COUNT(*)'], 'status'])->table(['coupons'])->where(['unique_id'=>$coupon_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['coupon_num']) return 0; // Err : x, -)-
			elseif(self::IS_USED == $v_record['status']) return -2; // Err : x, -)-
			return $this->table(['coupons'])->where(['unique_id'=>$coupon_unique_id])->delete();
		}
		return -1;
	}
	
	/**
	 * public integer create(integer $model_id, string $user_unique_id)
	 */
	public function create(int $model_id, string $user_unique_id): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$v_record1 = $this->field(['model_num'=>['COUNT(*)'], 'now_time'=>['UNIX_TIMESTAMP()'], 'category_id', 'min_charge_money', 'discount_money', 'begin_time', 'end_time', 'status'])->table(['coupon_models'])->where(['id'=>$model_id])->one();
		$v_record2 = $this->field(['user_num'=>['COUNT(*)'], 'id'])->table(['users'])->where(['unique_id'=>$user_unique_id])->one();
		$unique_id = $this->unique_id();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record1['model_num']) return -2; // Err : x, -)-
			elseif(self::IS_DISABLED == $v_record1['status']) return -3; // Err : x, -)-
			elseif(self::IS_EXPIRED == $v_record1['status']) return -4; // Err : x, -)-
			elseif($v_record1['now_time'] > $v_record1['end_time']){
				$this->refresh_expired_records();
				return -5; // Err : x, -)-
			}elseif(0 == $v_record2['user_num']) return -6; // Err : x, -)-
			$vm_record = $this->field(['coupon_num'=>['COUNT(*)']])->table(['coupons'])->where(['model_id'=>$model_id, 'user_id'=>$v_record2['id']], ['eq', 'eq'])->one();
			if($this->get_error() == self::ERR_NONE){
				if($vm_record['coupon_num'] > 0) return -7; // Err : x, -)-
				$coupon_datas = ['unique_id'=>$unique_id, 'category_id'=>$v_record1['category_id'], 'model_id'=>$model_id, 'user_id'=>$v_record2['id']];
				$coupon_datas += ['money_type'=>$system_money_type, 'min_charge_money'=>$v_record1['min_charge_money'], 'discount_money'=>$v_record1['discount_money']];
				$coupon_datas += ['begin_time'=>$v_record1['begin_time'], 'end_time'=>$v_record1['end_time']];
				if($this->table(['coupons'])->add(merge_time($coupon_datas)) > 0) return $this->get_last_id();
			}
		}
		return -1;
	}
	
	/**
	 * public integer use(string $coupon_unique_id, integer $order_id)
	 */
	public function use(string $coupon_unique_id, int $order_id): int {
		$this->clear_error();
		$v_record = $this->field(['coupon_num'=>['COUNT(*)'], 'now_time'=>['UNIX_TIMESTAMP()'], 'end_time', 'status'])->table(['coupons'])->where(['unique_id'=>$coupon_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['coupon_num']) return 0; // Err : x, -)-
			elseif(self::IS_USED == $v_record['status']) return -2; // Err : x, -)-
			elseif(self::IS_EXPIRED == $v_record['status']) return -3; // Err : x, -)-
			elseif($v_record['now_time'] > $v_record['end_time']){
				$this->refresh_expired_records();
				return -4; // Err : x, -)-
			}
			$coupon_datas = ['order_id'=>$order_id, 'use_time'=>['UNIX_TIMESTAMP()'], 'status'=>self::IS_USED];
			return $this->table(['coupons'])->where(['unique_id'=>$coupon_unique_id])->edit(merge_time($coupon_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer refresh_expired_records(void)
	 */
	public function refresh_expired_records(): int {
		if($this->begin()){
			$end1 = $this->table(['coupon_models'])->where(['end_time'=>['UNIX_TIMESTAMP()']], ['sm'])->edit(merge_time(['status'=>self::IS_EXPIRED], false));
			$end2 = $this->table(['coupons'])->where(['status'=>self::IS_UNUSED, 'end_time'=>['UNIX_TIMESTAMP()']], ['eq', 'sm'])->edit(merge_time(['status'=>self::IS_EXPIRED], false));
			if($end1 >= 0 && $end2 >= 0 && $this->end()) return $end2;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer clear_expired_records(boolean $waiting = true)
	 */
	public function clear_expired_records(bool $waiting = true): int {
		if($waiting) return $this->table(['coupons'])->where(['status'=>self::IS_EXPIRED, '(UNIX_TIMESTAMP()-`end_time`)'=>3600 * 24 * 30], ['eq', 'gr'])->delete();
		return $this->table(['coupons'])->where(['status'=>self::IS_EXPIRED])->delete();
	}
	
	/**
	 * public array get_mining_years(void)
	 */
	public function get_mining_years(): array {
		$year_exp = "FROM_UNIXTIME(`add_time`, '%Y')";
		return $this->field(['year'=>[$year_exp]])->table(['coupons'])->group(['year'])->order(['year'=>'asc'])->line('year');
	}
	
	/**
	 * public array get_mining_month_nums(string $year, ?string $status = null)
	 * @string $status = Coupon::IS_USED
	 */
	public function get_mining_month_nums(string $year, ?string $status = null): array {
		$months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
		$month_exp = "FROM_UNIXTIME(`add_time`, '%m')";
		$year_exp = "FROM_UNIXTIME(`add_time`, '%Y')";
		if(is_null($status)) $mining_datas = $this->field(['num'=>['COUNT(*)'], 'month'=>[$month_exp]])->table(['coupons'])->where([$year_exp=>$year])->group(['month'])->order(['month'=>'asc'])->line('num', 'month');
		elseif(self::IS_USED == $status) $mining_datas = $this->field(['num'=>['COUNT(*)'], 'month'=>[$month_exp]])->table(['coupons'])->where([$year_exp=>$year, 'status'=>self::IS_USED], ['eq', 'eq'])->group(['month'])->order(['month'=>'asc'])->line('num', 'month');
		foreach($months as $month){
			$end_datas[$month] = $mining_datas[$month] ?? 0;
		}
		return $end_datas ?? [];
	}
	
	/**
	 * public array get_mining_month_moneys(string $year, ?string $status = null)
	 * @string $status = Coupon::IS_USED
	 */
	public function get_mining_month_moneys(string $year, ?string $status = null): array {
		$months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
		$month_exp = "FROM_UNIXTIME(`add_time`, '%m')";
		$year_exp = "FROM_UNIXTIME(`add_time`, '%Y')";
		if(is_null($status)) $mining_datas = $this->field(['money'=>['SUM(`discount_money`)'], 'month'=>[$month_exp]])->table(['coupons'])->where([$year_exp=>$year])->group(['month'])->order(['month'=>'asc'])->line('money', 'month');
		elseif(self::IS_USED == $status) $mining_datas = $this->field(['money'=>['SUM(`discount_money`)'], 'month'=>[$month_exp]])->table(['coupons'])->where([$year_exp=>$year, 'status'=>self::IS_USED], ['eq', 'eq'])->group(['month'])->order(['month'=>'asc'])->line('money', 'month');
		foreach($months as $month){
			$end_datas[$month] = $mining_datas[$month] ?? 0;
		}
		return $end_datas ?? [];
	}
	
	/**
	 * protected array get_category_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_category_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$coupon_nums = $this->get_category_coupon_nums();
		$used_coupon_nums = $this->get_category_coupon_nums(self::IS_USED);
		$func = function (&$record) use ($coupon_nums, $used_coupon_nums) {
			$record['coupon_num'] = $coupon_nums[$record['id']] ?? 0;
			$record['used_coupon_num'] = $used_coupon_nums[$record['id']] ?? 0;
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
	 * protected array get_category_coupon_nums(?string $status = null)
	 * @string $status = Coupon::IS_USED
	 */
	protected function get_category_coupon_nums(?string $status = null): array {
		if(is_null($status)) $end_datas = $this->field(['coupon_num'=>['COUNT(*)'], 'cm.category_id'])->table(['cm'=>'coupon_models'])->join(['c'=>'coupons', 'cm.id'=>'c.model_id'])->group(['cm.category_id'])->order(['cm.category_id'=>'asc'])->line('coupon_num', 'category_id');
		elseif(self::IS_USED == $status) $end_datas = $this->field(['coupon_num'=>['COUNT(*)'], 'cm.category_id'])->table(['cm'=>'coupon_models'])->join(['c'=>'coupons', 'cm.id'=>'c.model_id'])->where(['c.status'=>$status])->group(['cm.category_id'])->order(['cm.category_id'=>'asc'])->line('coupon_num', 'category_id');
		return $end_datas ?? [];
	}
	
	/**
	 * protected array get_model_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_model_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$coupon_nums = $this->get_model_coupon_nums();
		$used_coupon_nums = $this->get_model_coupon_nums(self::IS_USED);
		$func = function (&$record) use ($coupon_nums, $used_coupon_nums) {
			$record['coupon_num'] = $coupon_nums[$record['id']] ?? 0;
			$record['used_coupon_num'] = $used_coupon_nums[$record['id']] ?? 0;
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
	 * protected array get_model_coupon_nums(?string $status = null)
	 * @string $status = Coupon::IS_USED
	 */
	protected function get_model_coupon_nums(?string $status = null): array {
		if(is_null($status)) $end_datas = $this->field(['coupon_num'=>['COUNT(*)'], 'model_id'])->table(['coupons'])->group(['model_id'])->order(['model_id'=>'asc'])->line('coupon_num', 'model_id');
		elseif(self::IS_USED == $status) $end_datas = $this->field(['coupon_num'=>['COUNT(*)'], 'model_id'])->table(['coupons'])->where(['status'=>$status])->group(['model_id'])->order(['model_id'=>'asc'])->line('coupon_num', 'model_id');
		return $end_datas ?? [];
	}
	
	/**
	 * private Coupon _category_view(void)
	 */
	private function _category_view(): Coupon {
		$cc_cols = ['id', 'code', 'name', 'description', 'last_edit_time', 'add_time'];
		$extra_cols = ['coupon_num'=>null, 'used_coupon_num'=>null];
		$this->field(array_merge($cc_cols, $extra_cols))->table(['coupon_categories']);
		return $this;
	}
	
	/**
	 * private Coupon _model_view(void)
	 */
	private function _model_view(): Coupon {
		$cm_cols = ['cm.id', 'cm.code', 'cm.name', 'cm.money_type', 'cm.min_charge_money', 'cm.discount_money', 'cm.begin_time', 'cm.end_time', 'cm.status', 'cm.last_edit_time', 'cm.add_time'];
		$cc_cols = ['category_id'=>['cc.id'], 'category_code'=>['cc.code']];
		$extra_cols = ['coupon_num'=>null, 'used_coupon_num'=>null];
		$this->field(array_merge($cm_cols, $cc_cols, $extra_cols))->table(['cm'=>'coupon_models']);
		$this->join(['cc'=>'coupon_categories', 'cm.category_id'=>'cc.id']);
		return $this;
	}
	
	/**
	 * private Coupon _view(void)
	 */
	private function _view(): Coupon {
		$c_cols = ['c.id', 'c.unique_id', 'c.money_type', 'c.min_charge_money', 'c.discount_money', 'c.begin_time', 'c.end_time', 'c.use_time', 'c.status', 'c.last_edit_time', 'c.add_time'];
		$cc_cols = ['category_id'=>['cc.id'], 'category_code'=>['cc.code']];
		$cm_cols = ['model_id'=>['cm.id'], 'model_code'=>['cm.code']];
		$u_cols = ['user_id'=>['u.id'], 'user_unique_id'=>['u.unique_id'], 'user_nickname'=>['u.nickname']];
		$o_cols = ['order_id'=>['o.id'], 'order_unique_id'=>['o.unique_id']];
		$this->field(array_merge($c_cols, $cc_cols, $cm_cols, $u_cols, $o_cols))->table(['c'=>'coupons']);
		$this->join(['cc'=>'coupon_categories', 'c.category_id'=>'cc.id']);
		$this->join(['cm'=>'coupon_models', 'c.model_id'=>'cm.id']);
		$this->join(['u'=>'users', 'c.user_id'=>'u.id']);
		$this->join(['o'=>'orders', 'c.order_id'=>'o.id'], 'left');
		return $this;
	}
	// -- Version : 0.99-Beta [2019-05-11] --
	// -- END --
}

