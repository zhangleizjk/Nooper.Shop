<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mysql;

class Express extends Mysql {
	
	/**
	 * Constants
	 */
	public const PARAM_MAX_CARRIAGE_TEMPLATE_NUM = 'max_carriage_template_num';
	public const PARAM_MAX_CORPORATION_NUM = 'max_corporation_num';
	public const DURATION_WEEK = 'week';
	public const DURATION_MONTH = 'month';
	public const DURATION_QUARTER = 'quarter';
	public const DURATION_GONE = 'gone';
	public const IS_ENABLED = 'enabled';
	public const IS_DISABLED = 'disabled';
	
	/**
	 * public string unique_id(void)
	 */
	public function unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->express();
			$record = $this->field(['express_num'=>['COUNT(*)']])->table(['expresses'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['express_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public ?Number|string get_param(string $param_name)
	 */
	public function get_param(string $param_name) {
		$record = $this->field(['*'])->table(['express_default_params'])->where(['id'=>1])->one();
		return $record[$param_name] ?? null;
	}
	
	/**
	 * public integer get_address_region_num(?string $status = null)
	 * @string $status = Express::IS_ENABLED|Express::IS_DISABLED
	 */
	public function get_address_region_num(?string $status = null): int {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $record = $this->field(['region_num'=>['COUNT(*)']])->table(['express_address_regions'])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['region_num'=>['COUNT(*)']])->table(['express_address_regions'])->where(['status'=>$status])->one();
		return $record['region_num'] ?? 0;
	}
	
	/**
	 * public array get_address_regions(void)
	 */
	public function get_address_regions(): array {
		return $this->_address_region_view()->where(['status'=>self::IS_ENABLED])->order(['id'=>'asc'])->line('name', 'id');
	}
	
	/**
	 * public array get_address_region_page(?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Express::IS_ENABLED|Express::IS_DISABLED
	 */
	public function get_address_region_page(?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $records = $this->_address_region_view()->order(['id'=>'asc'])->pill($page_num, $page_size);
		elseif(in_array($status, $yes_status_enums, true)) $records = $this->_address_region_view()->where(['status'=>$status])->order(['id'=>'asc'])->pill($page_num, $page_size);
		return $records ?? [];
	}
	
	/**
	 * public array get_address_region_record(integer $region_id)
	 */
	public function get_address_region_record(int $region_id): array {
		return $this->_address_region_view()->where(['id'=>$region_id])->one();
	}
	
	/**
	 * public integer enable_address_region(integer $region_id)
	 */
	public function enable_address_region(int $region_id): int {
		if($this->begin()){
			$end1 = $this->table(['user_delivery_addresses'])->where(['express_address_region_id'=>$region_id])->edit(merge_time(['status'=>User::IS_ENABLED], false));
			$end2 = $this->table(['express_address_towns'])->where(['region_id'=>$region_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
			$end3 = $this->table(['express_address_counties'])->where(['region_id'=>$region_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
			$end4 = $this->table(['express_address_cities'])->where(['region_id'=>$region_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
			$end5 = $this->table(['express_address_provinces'])->where(['region_id'=>$region_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
			$end6 = $this->table(['express_address_regions'])->where(['id'=>$region_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
			if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $end4 >= 0 && $end5 >= 0 && $end6 >= 0 && $this->end()) return $end6;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer disable_address_region(integer $region_id)
	 */
	public function disable_address_region(int $region_id): int {
		if($this->begin()){
			$end1 = $this->table(['user_delivery_addresses'])->where(['express_address_region_id'=>$region_id])->edit(merge_time(['status'=>User::IS_DISABLED], false));
			$end2 = $this->table(['express_address_towns'])->where(['region_id'=>$region_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			$end3 = $this->table(['express_address_counties'])->where(['region_id'=>$region_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			$end4 = $this->table(['express_address_cities'])->where(['region_id'=>$region_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			$end5 = $this->table(['express_address_provinces'])->where(['region_id'=>$region_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			$end6 = $this->table(['express_address_regions'])->where(['id'=>$region_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $end4 >= 0 && $end5 >= 0 && $end6 >= 0 && $this->end()) return $end6;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer get_address_province_num(integer $region_id, ?string $status = null)
	 * @string $status = Express::IS_ENABLED|Express::IS_DISABLED
	 */
	public function get_address_province_num(int $region_id, ?string $status = null): int {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $record = $this->field(['province_num'=>['COUNT(*)']])->table(['express_address_provinces'])->where(['region_id'=>$region_id])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['province_num'=>['COUNT(*)']])->table(['express_address_provinces'])->where(['region_id'=>$region_id, 'status'=>$status], ['eq', 'eq'])->one();
		return $record['province_num'] ?? 0;
	}
	
	/**
	 * public array function get_address_provinces(integer $region_id)
	 */
	public function get_address_provinces(int $region_id): array {
		return $this->_address_province_view()->where(['region_id'=>$region_id, 'status'=>self::IS_ENABLED], ['eq', 'eq'])->order(['id'=>'asc'])->line('name', 'id');
	}
	
	/**
	 * public array get_address_province_page(integer $region_id, ?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Express::IS_ENABLED|Express::IS_DISABLED
	 */
	public function get_address_province_page(int $region_id, ?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $records = $this->_address_province_view()->where(['region_id'=>$region_id])->order(['id'=>'asc'])->pill($page_num, $page_size);
		elseif(in_array($status, $yes_status_enums, true)) $records = $this->_address_province_view()->where(['region_id'=>$region_id, 'status'=>$status], ['eq', 'eq'])->order(['id'=>'asc'])->pill($page_num, $page_size);
		return $records ?? [];
	}
	
	/**
	 * public array get_address_province_record(integer $province_id)
	 */
	public function get_address_province_record(int $province_id): array {
		return $this->_address_province_view()->where(['id'=>$province_id])->one();
	}
	
	/**
	 * public integer enable_address_province(integer $province_id)
	 */
	public function enable_address_province(int $province_id): int {
		$this->clear_error();
		$v_record = $this->field(['province_num'=>['COUNT(*)'], 'region_id'])->table(['express_address_provinces'])->where(['id'=>$province_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['province_num']) return 0; // Err : x, -)-
			$vm_record = $this->field(['status'])->table(['express_address_regions'])->where(['id'=>$v_record['region_id']])->one();
			if($this->get_error() == self::ERR_NONE){
				if(self::IS_DISABLED == $vm_record['status']) return -2; // Err : x, -)-
				elseif($this->begin()){
					$end1 = $this->table(['user_delivery_addresses'])->where(['express_address_province_id'=>$province_id])->edit(merge_time(['status'=>User::IS_ENABLED], false));
					$end2 = $this->table(['express_address_towns'])->where(['province_id'=>$province_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
					$end3 = $this->table(['express_address_counties'])->where(['province_id'=>$province_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
					$end4 = $this->table(['express_address_cities'])->where(['province_id'=>$province_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
					$end5 = $this->table(['express_address_provinces'])->where(['id'=>$province_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
					if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $end4 >= 0 && $end5 > 0 && $this->end()) return $end5;
					$this->rollback();
				}
			}
		}
		return -1;
	}
	
	/**
	 * public integer disable_address_province(integer $province_id)
	 */
	public function disable_address_province(int $province_id): int {
		if($this->begin()){
			$end1 = $this->table(['user_delivery_addresses'])->where(['express_address_province_id'=>$province_id])->edit(merge_time(['status'=>User::IS_DISABLED], false));
			$end2 = $this->table(['express_address_towns'])->where(['province_id'=>$province_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			$end3 = $this->table(['express_address_counties'])->where(['province_id'=>$province_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			$end4 = $this->table(['express_address_cities'])->where(['province_id'=>$province_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			$end5 = $this->table(['express_address_provinces'])->where(['id'=>$province_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $end4 >= 0 && $end5 >= 0 && $this->end()) return $end5;
		}
		return -1;
	}
	
	/**
	 * public integer get_address_city_num(integer $province_id, ?string $status = null)
	 * @string $status = Express::IS_ENABLED|Express::IS_DISABLED
	 */
	public function get_address_city_num(int $province_id, ?string $status = null): int {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $record = $this->field(['city_num'=>['COUNT(*)']])->table(['express_address_cities'])->where(['province_id'=>$province_id])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['city_num'=>['COUNT(*)']])->table(['express_address_cities'])->where(['province_id'=>$province_id, 'status'=>$status], ['eq', 'eq'])->one();
		return $record['city_num'] ?? 0;
	}
	
	/**
	 * public array function get_address_cities(integer $province_id)
	 */
	public function get_address_cities(int $province_id): array {
		return $this->_address_city_view()->where(['province_id'=>$province_id, 'status'=>self::IS_ENABLED], ['eq', 'eq'])->order(['id'=>'asc'])->line('name', 'id');
	}
	
	/**
	 * public array get_address_city_page(integer $province_id, ?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Express::IS_ENABLED|Express::IS_DISABLED
	 */
	public function get_address_city_page(int $province_id, ?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $records = $this->_address_city_view()->where(['province_id'=>$province_id])->order(['id'=>'asc'])->pill($page_num, $page_size);
		elseif(in_array($status, $yes_status_enums, true)) $records = $this->_address_city_view()->where(['province_id'=>$province_id, 'status'=>$status], ['eq', 'eq'])->order(['id'=>'asc'])->pill($page_num, $page_size);
		return $records ?? [];
	}
	
	/**
	 * public array get_address_city_record(integer $city_id)
	 */
	public function get_address_city_record(int $city_id): array {
		return $this->_address_city_view()->where(['id'=>$city_id])->one();
	}
	
	/**
	 * public integer enable_address_city(integer $city_id)
	 */
	public function enable_address_city(int $city_id): int {
		$this->clear_error();
		$v_record = $this->field(['city_num'=>['COUNT(*)'], 'province_id'])->table(['express_address_cities'])->where(['id'=>$city_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['city_num']) return 0; // Err : x, -)-
			$vm_record = $this->field(['status'])->table(['express_address_provinces'])->where(['id'=>$v_record['province_id']])->one();
			if($this->get_error() == self::ERR_NONE){
				if(self::IS_DISABLED == $vm_record['status']) return -2; // Err : x, -)-
				elseif($this->begin()){
					$end1 = $this->table(['user_delivery_addresses'])->where(['express_address_city_id'=>$city_id])->edit(merge_time(['status'=>User::IS_ENABLED]));
					$end2 = $this->table(['express_address_towns'])->where(['city_id'=>$city_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
					$end3 = $this->table(['express_address_counties'])->where(['city_id'=>$city_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
					$end4 = $this->table(['express_address_cities'])->where(['id'=>$city_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
					if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $end4 > 0 && $this->end()) return $end4;
					$this->rollback();
				}
			}
		}
		return -1;
	}
	
	/**
	 * public integer disable_address_city(integer $city_id)
	 */
	public function disable_address_city(int $city_id): int {
		$this->clear_error();
		if($this->begin()){
			$end1 = $this->table(['user_delivery_addresses'])->where(['express_address_city_id'=>$city_id])->edit(merge_time(['status'=>User::IS_DISABLED]));
			$end2 = $this->table(['express_address_towns'])->where(['city_id'=>$city_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			$end3 = $this->table(['express_address_counties'])->where(['city_id'=>$city_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			$end4 = $this->table(['express_address_cities'])->where(['id'=>$city_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $end4 >= 0 && $this->end()) return $end4;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer get_address_county_num(integer $city_id, ?string $status = null)
	 * @string $status = Express::IS_ENABLED|Express::IS_DISABLED
	 */
	public function get_address_county_num(int $city_id, ?string $status = null): int {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $record = $this->field(['county_num'=>['COUNT(*)']])->table(['express_address_counties'])->where(['city_id'=>$city_id])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['county_num'=>['COUNT(*)']])->table(['express_address_counties'])->where(['city_id'=>$city_id, 'status'=>$status], ['eq', 'eq'])->one();
		return $record['county_num'] ?? 0;
	}
	
	/**
	 * public array function get_address_counties(integer $city_id)
	 */
	public function get_address_counties(int $city_id): array {
		return $this->_address_county_view()->where(['city_id'=>$city_id, 'status'=>self::IS_ENABLED], ['eq', 'eq'])->order(['id'=>'asc'])->line('name', 'id');
	}
	
	/**
	 * public array get_address_county_page(integer $city_id, ?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Express::IS_ENABLED|Express::IS_DISABLED
	 */
	public function get_address_county_page(int $city_id, ?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $records = $this->_address_county_view()->where(['city_id'=>$city_id])->order(['id'=>'asc'])->pill($page_num, $page_size);
		elseif(in_array($status, $yes_status_enums, true)) $records = $this->_address_county_view()->where(['city_id'=>$city_id, 'status'=>$status], ['eq', 'eq'])->order(['id'=>'asc'])->pill($page_num, $page_size);
		return $records ?? [];
	}
	
	/**
	 * public array get_address_county_record(integer $county_id)
	 */
	public function get_address_county_record(int $county_id): array {
		return $this->_address_county_view()->where(['id'=>$county_id])->one();
	}
	
	/**
	 * public integer enable_address_county(integer $county_id)
	 */
	public function enable_address_county(int $county_id): int {
		$this->clear_error();
		$v_record = $this->field(['county_num'=>['COUNT(*)'], 'city_id'])->table(['express_address_counties'])->where(['id'=>$county_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['county_num']) return 0; // Err : x, -)-
			$vm_record = $this->field(['status'])->table(['express_address_cities'])->where(['id'=>$v_record['city_id']])->one();
			if($this->get_error() == self::ERR_NONE){
				if(self::IS_DISABLED == $vm_record['status']) return -2; // Err : x, -)-
				elseif($this->begin()){
					$end1 = $this->table(['user_delivery_addresses'])->where(['express_address_county_id'=>$county_id])->edit(merge_time(['status'=>User::IS_ENABLED], false));
					$end2 = $this->table(['express_address_towns'])->where(['county_id'=>$county_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
					$end3 = $this->table(['express_address_counties'])->where(['id'=>$county_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
					if($end1 >= 0 && $end2 >= 0 && $end3 > 0 && $this->end()) return $end3;
					$this->rollback();
				}
			}
		}
		return -1;
	}
	
	/**
	 * public integer disable_address_county(integer $county_id)
	 */
	public function disable_address_county(int $county_id): int {
		if($this->begin()){
			$end1 = $this->table(['user_delivery_addresses'])->where(['express_address_county_id'=>$county_id])->edit(merge_time(['status'=>User::IS_DISABLED], false));
			$end2 = $this->table(['express_address_towns'])->where(['county_id'=>$county_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			$end3 = $this->table(['express_address_counties'])->where(['id'=>$county_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $this->end()) return $end3;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer get_address_town_num(integer $county_id, ?string $status = null)
	 * @string $status = Express::IS_ENABLED|Express::IS_DISABLED
	 */
	public function get_address_town_num(int $county_id, ?string $status = null): int {
		$yes_status_enum = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $record = $this->field(['town_num'=>['COUNT(*)']])->table(['express_address_towns'])->where(['county_id'=>$county_id])->one();
		elseif(in_array($status, $yes_status_enum, true)) $record = $this->field(['town_num'=>['COUNT(*)']])->table(['express_address_towns'])->where(['county_id'=>$county_id, 'status'=>$status], ['eq', 'eq'])->one();
		return $record['town_num'] ?? 0;
	}
	
	/**
	 * public array function get_address_towns(integer $county_id)
	 */
	public function get_address_towns(int $county_id): array {
		return $this->_address_town_view()->where(['county_id'=>$county_id, 'status'=>self::IS_ENABLED], ['eq', 'eq'])->order(['id'=>'asc'])->line('name', 'id');
	}
	
	/**
	 * public array get_address_town_page(integer $county_id, ?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Express::IS_ENABLED|Express::IS_DISABLED
	 */
	public function get_address_town_page(int $county_id, ?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enum = [self::IS_ENABLED, self::IS_DISABLED];
		if(is_null($status)) $records = $this->_address_town_view()->where(['county_id'=>$county_id])->order(['id'=>'asc'])->pill($page_num, $page_size);
		elseif(in_array($status, $yes_status_enum, true)) $records = $this->_address_town_view()->where(['county_id'=>$county_id, 'status'=>$status], ['eq', 'eq'])->order(['id'=>'asc'])->pill($page_num, $page_size);
		return $records ?? [];
	}
	
	/**
	 * public array get_address_town_record(integer $town_id)
	 */
	public function get_address_town_record(int $town_id): array {
		return $this->_address_town_view()->where(['id'=>$town_id])->one();
	}
	
	/**
	 * public integer enable_address_town(integer $town_id)
	 */
	public function enable_address_town(int $town_id): int {
		$this->clear_error();
		$v_record = $this->field(['town_num'=>['COUNT(*)'], 'county_id'])->table(['express_address_towns'])->where(['id'=>$town_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['town_num']) return 0; // Err : x, -)-
			$vm_record = $this->field(['status'])->table(['express_address_counties'])->where(['id'=>$v_record['county_id']])->one();
			if($this->get_error() == self::ERR_NONE){
				if(self::IS_DISABLED == $vm_record['status']) return -2; // Err : x, -)-
				elseif($this->begin()){
					$end1 = $this->table(['user_delivery_addresses'])->where(['express_address_town_id'=>$town_id])->edit(merge_time(['status'=>User::IS_ENABLED], false));
					$end2 = $this->table(['express_address_towns'])->where(['id'=>$town_id])->edit(merge_time(['status'=>self::IS_ENABLED], false));
					if($end1 >= 0 && $end2 > 0 && $this->end()) return $end2;
					$this->rollback();
				}
			}
		}
		return -1;
	}
	
	/**
	 * public integer disable_address_town(integer $town_id)
	 */
	public function disable_address_town(int $town_id): int {
		if($this->begin()){
			$end1 = $this->table(['user_delivery_addresses'])->where(['express_address_town_id'=>$town_id])->edit(merge_time(['status'=>User::IS_DISABLED], false));
			$end2 = $this->table(['express_address_towns'])->where(['id'=>$town_id])->edit(merge_time(['status'=>self::IS_DISABLED], false));
			if($end1 >= 0 && $end2 >= 0 && $this->end()) return $end2;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer get_carriage_template_num(void)
	 */
	public function get_carriage_template_num(): int {
		$record = $this->field(['template_num'=>['COUNT(*)']])->table(['express_carriage_templates'])->one();
		return $record['template_num'] ?? 0;
	}
	
	/**
	 * public array get_carriage_templates(void)
	 */
	public function get_carriage_templates(): array {
		return $this->_carriage_template_view()->order(['id'=>'desc'])->line('code', 'id');
	}
	
	/**
	 * public array get_carriage_template_page(integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_carriage_template_page(int $page_num = 1, int $page_size = 20): array {
		$records = $this->_carriage_template_view()->order(['id'=>'desc'])->pill($page_num, $page_size);
		return $this->get_carriage_template_extra_datas($records);
	}
	
	/**
	 * public array get_carriage_template_record(integer $template_id)
	 */
	public function get_carriage_template_record(int $template_id): array {
		$record = $this->_carriage_template_view()->where(['id'=>$template_id])->one();
		return $this->get_carriage_template_extra_datas($record, false);
	}
	
	/**
	 * public integer delete_carriage_template(integer $template_id)
	 */
	public function delete_carriage_template(int $template_id): int {
		if($this->begin()){
			$product_datas = ['express_carriage_template_id'=>null, 'status'=>Product::IS_OFFLINE];
			$end1 = $this->table(['products'])->where(['express_carriage_template_id'=>$template_id, 'status'=>Product::IS_ONLINE], ['eq', 'eq'])->edit(merge_time($product_datas, false));
			$end2 = $this->table(['express_carriage_template_details'])->where(['template_id'=>$template_id])->delete();
			$end3 = $this->table(['express_carriage_templates'])->where(['id'=>$template_id])->delete();
			if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $this->end()) return $end3;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer edit_carriage_template(integer $template_id, string $code, string $name, float $basic_money, $float $progress_money)
	 */
	public function edit_carriage_template(int $template_id, string $code, string $name, float $basic_money, float $progress_money): int {
		$this->clear_error();
		$v_record = $this->field(['template_num'=>['COUNT(*)']])->table(['express_carriage_templates'])->where(['id'=>$template_id, 'code'=>$code], ['neq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['template_num'] > 0) return -2; // Err : x, -)-
			elseif($this->begin()){
				$template_detail_datas = ['basic_money'=>$basic_money, 'progress_money'=>$progress_money];
				$template_datas = ['code'=>$code, 'name'=>$name, 'basic_money'=>$basic_money, 'progress_money'=>$progress_money];
				$end1 = $this->table(['express_carriage_template_details'])->where(['template_id'=>$template_id])->edit(merge_time($template_detail_datas, false));
				$end2 = $this->table(['express_carriage_templates'])->where(['id'=>$template_id])->edit(merge_time($template_datas, false));
				if($end1 >= 0 && $end2 >= 0 && $this->end()) return $end2;
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer add_carriage_template(string $code, string $name, float $basic_money, float $progress_money)
	 */
	public function add_carriage_template(string $code, string $name, float $basic_money, float $progress_money): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$max_template_num = $this->get_param(self::PARAM_MAX_CARRIAGE_TEMPLATE_NUM);
		$template_num = $this->get_carriage_template_num();
		$v_record = $this->field(['template_num'=>['COUNT(*)']])->table(['express_carriage_templates'])->where(['code'=>$code])->one();
		if($this->get_error() == self::ERR_NONE){
			if($template_num >= $max_template_num) return -2; // Err : Param_Max_Carriage_Template_Num, -)-
			elseif($v_record['template_num'] > 0) return -3; // Err : x, -)-
			elseif($this->begin()){
				$template_datas = ['code'=>$code, 'name'=>$name, 'money_type'=>$system_money_type, 'basic_money'=>$basic_money, 'progress_money'=>$progress_money];
				if($this->table(['express_carriage_templates'])->add(merge_time($template_datas)) > 0){
					$template_id = $this->get_last_id();
					if($this->make_carriage_template_details($template_id, $basic_money, $progress_money) >= 0 && $this->end()) return $template_id;
				}
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer get_carriage_template_product_num(integer $template_id, ?string $status = null)
	 * @string $status = Product::IS_PREPARED|Product::IS_ONLINE|Product::IS_OFFLINE
	 */
	public function get_carriage_template_product_num(int $template_id, ?string $status = null): int {
		$yes_status_enums = [Product::IS_PREPARED, Product::IS_ONLINE, Product::IS_OFFLINE];
		if(is_null($status)) $record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['express_carriage_template_id'=>$template_id])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['express_carriage_template_id'=>$template_id, 'status'=>$status], ['eq', 'eq'])->one();
		return $record['product_num'] ?? 0;
	}
	
	/**
	 * public array get_carriage_template_product_page(integer $template_id, ?string $status = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Product::IS_PREPARED|Product::IS_ONLINE|Product::IS_OFFLINE
	 */
	public function get_carriage_template_product_page(int $template_id, ?string $status = null, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [Product::IS_PREPARED, Product::IS_ONLINE, Product::IS_OFFLINE];
		if(is_null($status)) $records = $this->_carriage_template_product_view()->where(['express_carriage_template_id'=>$template_id])->order(['place'=>'desc'])->pill($page_num, $page_size);
		elseif(in_array($status, $yes_status_enums, true)) $records = $this->_carriage_template_product_view()->where(['express_carriage_template_id'=>$template_id, 'status'=>$status], ['eq', 'eq'])->order(['place'=>'desc'])->pill($page_num, $page_size);
		return $records ?? [];
	}
	
	/**
	 * public integer get_carriage_template_detail_num(integer $template_id, integer $region_id)
	 */
	public function get_carriage_template_detail_num(int $template_id, int $region_id): int {
		$record = $this->field(['template_detail_num'=>['COUNT(*)']])->table(['express_carriage_template_details'])->where(['template_id'=>$template_id, 'region_id'=>$region_id], ['eq', 'eq'])->one();
		return $record['template_detail_num'] ?? 0;
	}
	
	/**
	 * public array get_carriage_template_detail_page(integer $template_id, integer $region_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_carriage_template_detail_page(int $template_id, int $region_id, int $page_num = 1, int $page_size = 20): array {
		return $this->_carriage_template_detail_view()->where(['ectd.template_id'=>$template_id, 'ectd.region_id'=>$region_id], ['eq', 'eq'])->order(['ectd.id'=>'asc'])->pill($page_num, $page_size);
	}
	
	/**
	 * public array get_carriage_template_detail_record(integer $template_detail_id)
	 */
	public function get_carriage_template_detail_record(int $template_detail_id): array {
		return $this->_carriage_template_detail_view()->where(['ectd.id'=>$template_detail_id])->one();
	}
	
	/**
	 * public integer edit_carriage_template_detail(integer $template_detail_id, float $basic_money, float progress_money)
	 */
	public function edit_carriage_template_detail(int $template_detail_id, float $basic_money, float $progress_money): int {
		$template_detail_datas = ['basic_money'=>$basic_money, 'progress_money'=>$progress_money];
		return $this->table(['express_carriage_template_details'])->where(['id'=>$template_detail_id])->edit(merge_time($template_detail_datas, false));
	}
	
	/**
	 * public integer get_corporation_num(void)
	 */
	public function get_corporation_num(): int {
		$record = $this->field(['corporation_num'=>['COUNT(*)']])->table(['express_corporations'])->one();
		return $record['corporation_num'] ?? 0;
	}
	
	/**
	 * public array get_corporations(void)
	 */
	public function get_corporations(): array {
		return $this->_corporation_view()->order(['is_default'=>'desc', 'id'=>'asc'])->select();
	}
	
	/**
	 * public array get_corporation_page(integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_corporation_page(int $page_num = 1, int $page_size = 20): array {
		$records = $this->_corporation_view()->order(['is_default'=>'desc', 'id'=>'asc'])->pill($page_num, $page_size);
		return $this->get_corporation_extra_datas($records);
	}
	
	/**
	 * public array get_corporation_record(integer $corporation_id)
	 */
	public function get_corporation_record(int $corporation_id): array {
		$record = $this->_corporation_view()->where(['id'=>$corporation_id])->one();
		return $this->get_corporation_extra_datas($record, false);
	}
	
	/**
	 * public integer delete_corporation(integer $corporation_id)
	 */
	public function delete_corporation(int $corporation_id): int {
		$this->clear_error();
		$v_record = $this->field(['express_num'=>['COUNT(*)']])->table(['expresses'])->where(['corporation_id'=>$corporation_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['express_num'] > 0) return -2; // Err : x, -)-
			return $this->table(['express_corporations'])->where(['id'=>$corporation_id])->delete();
		}
		return -1;
	}
	
	/**
	 * public integer edit_corporation(integer $corporation_id, string $code, string $name, ?string $home_page = null)
	 */
	public function edit_corporation(int $corporation_id, string $code, string $name, ?string $home_page = null): int {
		$this->clear_error();
		$v_record = $this->field(['corporation_num'=>['COUNT(*)']])->table(['express_corporations'])->where(['id'=>$corporation_id, 'code'=>$code], ['neq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['corporation_num'] > 0) return -2; // Err : x, -)-
			$corporation_datas = ['code'=>$code, 'name'=>$name, 'home_page'=>$home_page];
			return $this->table(['express_corporations'])->where(['id'=>$corporation_id])->edit(merge_time($corporation_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer add_corporation(string $code, string $name, ?string $home_page = null)
	 */
	public function add_corporation(string $code, string $name, ?string $home_page = null): int {
		$this->clear_error();
		$max_corporation_num = $this->get_param(self::PARAM_MAX_CORPORATION_NUM);
		$corporation_num = $this->get_corporation_num();
		$v_record = $this->field(['corporation_num'=>['COUNT(*)']])->table(['express_corporations'])->where(['code'=>$code])->one();
		if($this->get_error() == self::ERR_NONE){
			if($corporation_num >= $max_corporation_num) return -2; // Err : Param_Max_Corporation_Num, -)-
			elseif($v_record['corporation_num'] > 0) return -3; // Err : x, -)-
			$corporation_datas = ['code'=>$code, 'name'=>$name, 'home_page'=>$home_page];
			if($this->table(['express_corporations'])->add(merge_time($corporation_datas)) > 0) return $this->get_last_id();
		}
		return -1;
	}
	
	/**
	 * public integer set_default_corporation(integer $corporation_id)
	 */
	public function set_default_corporation(int $corporation_id): int {
		$this->clear_error();
		$v_record = $this->field(['corporation_num'=>['COUNT(*)']])->table(['express_corporations'])->where(['id'=>$corporation_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['corporation_num']) return 0; // Err : x, -)-
			elseif($this->begin()){
				$end1 = $this->table(['express_corporations'])->edit(merge_time(['is_default'=>false], false));
				$end2 = $this->table(['express_corporations'])->where(['id'=>$corporation_id])->edit(merge_time(['is_default'=>true], false));
				if($end1 >= 0 && $end2 > 0 && $this->end()) return $end2;
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer get_corporation_express_num(integer $corporation_id)
	 */
	public function get_corporation_express_num(int $corporation_id): int {
		$record = $this->field(['express_num'=>['COUNT(*)']])->table(['expresses'])->where(['corporation_id'=>$corporation_id])->one();
		return $record['express_num'] ?? 0;
	}
	
	/**
	 * public array get_corporation_express_page(integer $corporation_id, ?string $duration = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $duration = Express::DURATION_WEEK|Express::DURATION_MONTH|Express::DURATION_QUARTER|Express::DURATION_GONE
	 */
	public function get_corporation_express_page(int $corporation_id, ?string $duration = null, int $page_num = 1, int $page_size = 20): array {
		$yes_duration_enums = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		if(is_null($duration)) $records = $this->_corporation_express_view()->where(['corporation_id'=>$corporation_id])->order(['id'=>'desc'])->pill($page_num, $page_size);
		elseif(in_array($duration, array_keys($yes_duration_enums), true)){
			$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`add_time`, '%Y-%m-%d'))";
			$records = $this->_corporation_express_view()->where_cmd('`corporation_id`=' . $corporation_id . ' and ' . $duration_prefix . $yes_duration_enums[$duration])->order(['id'=>'desc'])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public integer num(?string $duration = null)
	 * @string $duration = Express::DURATION_WEEK|Express::DURATION_MONTH|Express::DURATION_QUARTER|Express::DURATION_GONE
	 */
	public function num(?string $duration = null): int {
		$yes_duration_enums = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		if(is_null($duration)) $record = $this->field(['express_num'=>['COUNT(*)']])->table(['expresses'])->one();
		elseif(in_array($duration, array_keys($yes_duration_enums), true)){
			$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`add_time`, '%Y-%m-%d'))";
			$record = $this->field(['express_num'=>['COUNT(*)']])->table(['expresses'])->where_cmd($duration_prefix . $yes_duration_enums[$duration])->one();
		}
		return $record['express_num'] ?? 0;
	}
	
	/**
	 * public array page(?string $duration = null, integer $page_num = 1, integer $page_size = 20)
	 * @string $duration = Express::DURATION_WEEK|Express::DURATION_MONTH|Express::DURATION_QUARTER|Express::DURATION_GONE
	 */
	public function page(?string $duration = null, int $page_num = 1, int $page_size = 20): array {
		$yes_duration_enums = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		if(is_null($duration)) $records = $this->_view()->order(['e.id'=>'desc'])->pill($page_num, $page_size);
		elseif(in_array($duration, array_keys($yes_duration_enums), true)){
			$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`e`.`add_time`, '%Y-%m-%d'))";
			$records = $this->_view()->where_cmd($duration_prefix . $yes_duration_enums[$duration])->order(['e.id'=>'desc'])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public array record(string $express_unique_id)
	 */
	public function record(string $express_unique_id): array {
		return $this->_view()->where(['e.unique_id'=>$express_unique_id])->one();
	}
	
	/**
	 * public array find(string $express_code)
	 */
	public function find(string $express_code): array {
		return $this->_view()->where(['e.code'=>$express_code])->select();
	}
	
	/**
	 * public integer remove(string $express_unique_id)
	 */
	public function remove(string $express_unique_id): int {
		return $this->table(['expresses'])->where(['unique_id'=>$express_unique_id])->delete();
	}
	
	/**
	 * public integer change(string $express_unique_id, integer $corporation_id, string $code, float $carriage_money, string $address, string $receiver, string $phone, ?string $postcode = null, ?string $note = null)
	 */
	public function change(string $express_unique_id, int $corporation_id, string $code, float $carriage_money, string $address, string $receiver, string $phone, ?string $postcode = null, ?string $note = null): int {
		$this->clear_error();
		$v_record = $this->field(['express_num'=>['COUNT(*)']])->table(['expresses'])->where(['unique_id'=>$express_unique_id, 'corporation_id'=>$corporation_id, 'code'=>$code], ['neq', 'eq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['express_num'] > 0) return -2; // Err : x, -)-
			$express_datas = ['corporation_id'=>$corporation_id, 'code'=>$code, 'carriage_money'=>$carriage_money];
			$express_datas += ['address'=>$address, 'receiver'=>$receiver, 'phone'=>$phone, 'postcode'=>$postcode, 'note'=>$note];
			return $this->table(['expresses'])->where(['unique_id'=>$express_unique_id])->edit(merge_time($express_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer create(integer $corporation_id, integer $order_id, string $code, float $carriage_money, string $address, string $receiver, string $phone, ?string $postcode = null, ?string $note = null)
	 */
	public function create(int $corporation_id, int $order_id, string $code, float $carriage_money, string $address, string $receiver, string $phone, ?string $postcode = null, ?string $note = null): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$v_record = $this->field(['express_num'=>['COUNT(*)']])->table(['expresses'])->where(['corporation_id'=>$corporation_id, 'code'=>$code], ['eq', 'eq'])->one();
		$unique_id = $this->unique_id();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['express_num'] > 0) return -2; // Err : x, -)-
			$express_datas = ['unique_id'=>$unique_id, 'corporation_id'=>$corporation_id, 'order_id'=>$order_id, 'code'=>$code];
			$express_datas += ['money_type'=>$system_money_type, 'carriage_money'=>$carriage_money];
			$express_datas += ['address'=>$address, 'receiver'=>$receiver, 'phone'=>$phone, 'postcode'=>$postcode, 'note'=>$note];
			print_r($express_datas);
			if($this->table(['expresses'])->add(merge_time($express_datas)) > 0) return $this->get_last_id();
		}
		return -1;
	}
	
	/**
	 * public array get_mining_years(void)
	 */
	public function get_mining_years(): array {
		$year_exp = "FROM_UNIXTIME(`add_time`, '%Y')";
		return $this->field(['year'=>[$year_exp]])->table(['expresses'])->group(['year'])->order(['year'=>'asc'])->line('year');
	}
	
	/**
	 * public array get_mining_month_nums(string $year)
	 */
	public function get_mining_month_nums(string $year): array {
		$months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
		$month_exp = "FROM_UNIXTIME(`add_time`, '%m')";
		$year_exp = "FROM_UNIXTIME(`add_time`, '%Y')";
		$mining_datas = $this->field(['num'=>['COUNT(*)'], 'month'=>[$month_exp]])->table(['expresses'])->where([$year_exp=>$year])->group(['month'])->order(['month'=>'asc'])->line('num', 'month');
		foreach($months as $month){
			$end_datas[$month] = $mining_datas[$month] ?? 0;
		}
		return $end_datas;
	}
	
	/**
	 * public array get_mining_month_moneys(string $year)
	 */
	public function get_mining_month_moneys(string $year): array {
		$months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
		$month_exp = "FROM_UNIXTIME(`add_time`, '%m')";
		$year_exp = "FROM_UNIXTIME(`add_time`, '%Y')";
		$mining_datas = $this->field(['money'=>['SUM(`carriage_money`)'], 'month'=>[$month_exp]])->table(['expresses'])->where([$year_exp=>$year])->group(['month'])->order(['month'=>'asc'])->line('money', 'month');
		foreach($months as $month){
			$end_datas[$month] = $mining_datas[$month] ?? 0.00;
		}
		return $end_datas;
	}
	
	/**
	 * protected array get_carriage_template_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_carriage_template_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$product_nums = $this->get_carriage_template_product_nums();
		$func = function (&$record) use ($product_nums) {
			$record['product_num'] = $product_nums[$record['id']] ?? 0;
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
	 * protected array get_carriage_template_product_nums(void)
	 */
	protected function get_carriage_template_product_nums(): array {
		return $this->field(['product_num'=>['COUNT(*)'], 'express_carriage_template_id'])->table(['products'])->group(['express_carriage_template_id'])->order(['express_carriage_template_id'=>'asc'])->line('product_num', 'express_carriage_template_id');
	}
	
	/**
	 * protected integer make_carriage_template_details(integer $template_id, float $basic_money, float $progress_money)
	 */
	protected function make_carriage_template_details(int $template_id, float $basic_money, float $progress_money): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$records = $this->_address_region_province_view()->order(['ear.id'=>'asc', 'eap.id'=>'asc'])->select();
		if($this->get_error() == self::ERR_NONE){
			$counter = 0;
			foreach($records as $record){
				$counter++;
				$template_detail_datas = ['template_id'=>$template_id, 'region_id'=>$record['region_id'], 'province_id'=>$record['province_id']];
				$template_detail_datas += ['money_type'=>$system_money_type, 'basic_money'=>$basic_money, 'progress_money'=>$progress_money];
				if($this->table(['express_carriage_template_details'])->add(merge_time($template_detail_datas)) > 0) continue;
				return -1;
			}
			return $counter;
		}
		return -1;
	}
	
	/**
	 * protected array get_corporation_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_corporation_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$express_nums = $this->get_corporation_express_nums();
		$express_moneys = $this->get_corporation_express_moneys();
		$func = function (&$record) use ($express_nums, $express_moneys) {
			$record['express_num'] = $express_nums[$record['id']] ?? 0;
			$record['express_money'] = $express_moneys[$record['id']] ?? 0;
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
	 * protected array get_corporation_express_nums(void)
	 */
	protected function get_corporation_express_nums(): array {
		return $this->field(['express_num'=>['COUNT(*)'], 'corporation_id'])->table(['expresses'])->group(['corporation_id'])->order(['corporation_id'=>'asc'])->line('express_num', 'corporation_id');
	}
	
	/**
	 * proteted array get_corporation_express_moneys(void)
	 */
	protected function get_corporation_express_moneys(): array {
		return $this->field(['express_money'=>['SUM(`carriage_money`)'], 'corporation_id'])->table(['expresses'])->group(['corporation_id'])->order(['corporation_id'=>'asc'])->line('express_money', 'corporation_id');
	}
	
	/**
	 * private Express _address_region_view(void)
	 */
	private function _address_region_view(): Express {
		$ear_cols = ['id', 'code', 'name', 'status', 'last_edit_time', 'add_time'];
		$this->field($ear_cols)->table(['express_address_regions']);
		return $this;
	}
	
	/**
	 * private Express _address_province_view(void)
	 */
	private function _address_province_view(): Express {
		$eap_cols = ['id', 'name', 'status', 'last_edit_time', 'add_time'];
		$this->field($eap_cols)->table(['express_address_provinces']);
		return $this;
	}
	
	/**
	 * private Express _address_city_view(void)
	 */
	private function _address_city_view(): Express {
		$eac_cols = ['id', 'name', 'status', 'last_edit_time', 'add_time'];
		$this->field($eac_cols)->table(['express_address_cities']);
		return $this;
	}
	
	/**
	 * private Express _address_county_view(void)
	 */
	private function _address_county_view(): Express {
		$eac_cols = ['id', 'name', 'status', 'last_edit_time', 'add_time'];
		$this->field($eac_cols)->table(['express_address_counties']);
		return $this;
	}
	
	/**
	 * private Expreses _address_town_view(void)
	 */
	private function _address_town_view(): Express {
		$eat_cols = ['id', 'name', 'status', 'last_edit_time', 'add_time'];
		$this->field($eat_cols)->table(['express_address_towns']);
		return $this;
	}
	
	/**
	 * private Express _address_region_province_view(void)
	 */
	private function _address_region_province_view(): Express {
		$this->field(['region_id'=>['ear.id'], 'province_id'=>['eap.id']])->table(['ear'=>'express_address_regions']);
		$this->join(['eap'=>'express_address_provinces', 'ear.id'=>'eap.region_id'], 'left');
		return $this;
	}
	
	/**
	 * private Express _carriage_template_view(void)
	 */
	private function _carriage_template_view(): Express {
		$ect_cols = ['id', 'code', 'name', 'money_type', 'basic_money', 'progress_money', 'last_edit_time', 'add_time'];
		$extra_cols = ['product_num'=>null];
		$this->field(array_merge($ect_cols, $extra_cols))->table(['express_carriage_templates']);
		return $this;
	}
	
	/**
	 * private Express _carriage_template_product_view(void)
	 */
	private function _carriage_template_product_view(): Express {
		$p_cols = ['p.id', 'p.unique_id', 'p.code', 'p.name', 'p.primary_video', 'p.primary_picture', 'p.money_type', 'p.min_tag_price', 'p.min_discount_price', 'p.place', 'p.click_num', 'p.sale_num', 'p.status', 'p.last_edit_time', 'p.add_time'];
		$pc_cols = ['category_id'=>['pc.id'], 'category_code'=>['pc.code']];
		$this->field(array_merge($p_cols, $pc_cols))->table(['psgd'=>'product_subgroup_details']);
		$this->join(['p'=>'products', 'psgd.product_id'=>'p.id']);
		$this->join(['pc'=>'product_categories', 'p.category_id'=>'pc.id']);
		return $this;
	}
	
	/**
	 * private Express _carriage_template_detail_view(void)
	 */
	private function _carriage_template_detail_view(): Express {
		$ectd_cols = ['ectd.id', 'ectd.money_type', 'ectd.basic_money', 'ectd.progress_money', 'ectd.last_edit_time', 'ectd.add_time'];
		$ect_cols = ['template_id'=>['ect.id'], 'template_code'=>['ect.code']];
		$ear_cols = ['region_id'=>['ear.id'], 'region_code'=>['ear.code'], 'region_name'=>['ear.name']];
		$eap_cols = ['province_id'=>['eap.id'], 'province_name'=>['eap.name']];
		$this->field(array_merge($ectd_cols, $ect_cols, $ear_cols, $eap_cols))->table(['ectd'=>'express_carriage_template_details']);
		$this->join(['ect'=>'express_carriage_templates', 'ectd.template_id'=>'ect.id']);
		$this->join(['ear'=>'express_address_regions', 'ectd.region_id'=>'ear.id']);
		$this->join(['eap'=>'express_address_provinces', 'ectd.province_id'=>'eap.id'], 'left');
		return $this;
	}
	
	/**
	 * private Express _coporation_view(void)
	 */
	private function _corporation_view(): Express {
		$ec_cols = ['id', 'code', 'name', 'home_page', 'is_default', 'last_edit_time', 'add_time'];
		$extra_cols = ['express_num'=>null, 'express_money'=>null];
		$this->field(array_merge($ec_cols, $extra_cols))->table(['express_corporations']);
		return $this;
	}
	
	/**
	 * private Express _corporation_express_view(void)
	 */
	private function _corporation_express_view(): Express {
		$e_cols = ['id', 'unique_id', 'code', 'money_type', 'carriage_money', 'address', 'receiver', 'phone', 'postcode', 'note', 'last_edit_time', 'add_time'];
		$this->field($e_cols)->table(['expresses']);
		return $this;
	}
	
	/**
	 * private Express _view(void)
	 */
	private function _view(): Express {
		$e_cols = ['e.id', 'e.unique_id', 'e.code', 'e.money_type', 'e.carriage_money', 'e.address', 'e.receiver', 'e.phone', 'e.postcode', 'e.note', 'e.last_edit_time', 'e.add_time'];
		$ec_cols = ['corporation_id'=>['ec.id'], 'corporation_code'=>['ec.code'], 'corporation_name'=>['ec.name'], 'corporation_home_page'=>['ec.home_page']];
		$o_cols = ['order_id'=>['o.id'], 'order_unique_id'=>['o.unique_id']];
		$this->field(array_merge($e_cols, $ec_cols, $o_cols))->table(['e'=>'expresses']);
		$this->join(['ec'=>'express_corporations', 'e.corporation_id'=>'ec.id']);
		$this->join(['o'=>'orders', 'e.order_id'=>'o.id']);
		return $this;
	}
	// -- Version : 0.99-Beta [2019-05-13] --
	// -- END --
}

