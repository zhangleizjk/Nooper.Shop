<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mysql;

class Product extends Mysql {
	
	/**
	 * Constants
	 */
	public const PARAM_MAX_PRODUCT_CATEGORY_NUM = 'max_product_category_num';
	public const PARAM_MAX_PRODUCT_CATEGORY_PROPERTY_NUM = 'max_product_category_property_num';
	public const PARAM_MAX_PRODUCT_GROUP_NUM = 'max_product_group_num';
	public const PARAM_MAX_PRODUCT_SUBGROUP_NUM = 'max_product_subgroup_num';
	public const PARAM_MAX_PRODUCT_NUM = 'max_product_num';
	public const PARAM_MAX_PRODUCT_PROPERTY_ENUM_NUM = 'max_product_property_enum_num';
	public const PARAM_MAX_PRODUCT_VIDEO_NUM = 'max_product_video_num';
	public const PARAM_MAX_PRODUCT_PICTURE_NUM = 'max_product_picture_num';
	public const PARAM_MAX_PRODUCT_DESCRIPTION_PICTURE_NUM = 'max_product_description_picture_num';
	public const PARAM_MAX_PRODUCT_STOCK_NUM = 'max_product_stock_num';
	public const SORT_DEFAULT = 'default';
	public const SORT_SALE_NUM = 'sale_num';
	public const SORT_PRICE_UP = 'price_up';
	public const SORT_PRICE_DOWN = 'price_down';
	public const SORT_ADD_TIME = 'add_time';
	public const IS_PREPARED = 'prepared';
	public const IS_ONLINE = 'online';
	public const IS_OFFLINE = 'offline';
	
	//
	FUNCTION ___________________________________________________00() {
	}
	//
	
	/**
	 * public string unique_id(void)
	 */
	public function unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->product();
			$record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['product_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public string unique_video_file_name(void)
	 */
	public function unique_video_file_name(): string {
		$unique = new Unique();
		do{
			$file_name = $unique->file_name();
			$record = $this->field(['video_num'=>['COUNT(*)']])->table(['product_videos'])->where(['file_name'=>$file_name])->one();
			if($record && $record['video_num'] > 0) continue;
			break;
		}while(true);
		return $file_name;
	}
	
	/**
	 * public string unique_picture_file_name(void)
	 */
	public function unique_picture_file_name(): string {
		$unique = new Unique();
		do{
			$file_name = $unique->file_name();
			$record = $this->field(['picture_num'=>['COUNT(*)']])->table(['product_pictures'])->where(['file_name'=>$file_name])->one();
			if($record && $record['picture_num'] > 0) continue;
			break;
		}while(true);
		return $file_name;
	}
	
	/**
	 * public string unique_description_picture_file_name(void)
	 */
	public function unique_description_picture_file_name(): string {
		$unique = new Unique();
		do{
			$file_name = $unique->file_name();
			$record = $this->field(['picture_num'=>['COUNT(*)']])->table(['product_description_pictures'])->where(['file_name'=>$file_name])->one();
			if($record && $record['picture_num'] > 0) continue;
			break;
		}while(true);
		return $file_name;
	}
	
	/**
	 * public ?Number|string get_param(string $param_name)
	 */
	public function get_param(string $param_name) {
		$record = $this->field(['*'])->table(['product_default_params'])->where(['id'=>1])->one();
		return $record[$param_name] ?? null;
	}
	
	//
	FUNCTION ___________________________________________________01() {
	}
	//
	
	/**
	 * public integer get_category_num(void)
	 */
	public function get_category_num(): int {
		$record = $this->field(['category_num'=>['COUNT(*)']])->table(['product_categories'])->one();
		return $record['category_num'] ?? 0;
	}
	
	/**
	 * public array get_categories(void)
	 */
	public function get_categories(): array {
		return $this->_category_view()->table(['product_categories'])->order(['place'=>'asc'])->select();
	}
	
	/**
	 * public array get_category_page(integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_category_page(int $page_num = 1, int $page_size = 20): array {
		$records = $this->_category_view()->order(['place'=>'asc'])->pill($page_num, $page_size);
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
	 * public integer delete_category(integer $category_id)
	 */
	public function delete_category(int $category_id): int {
		$this->clear_error();
		$v_record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['category_id'=>$category_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['product_num'] > 0) return -2; // Err : x, -)-
			elseif($this->begin()){
				$end1 = $this->table(['taxes'])->where(['product_category_id'=>$category_id])->delete();
				$end2 = $this->table(['product_category_properties'])->where(['category_id'=>$category_id])->delete();
				$end3 = $this->table(['product_categories'])->where(['id'=>$category_id])->delete();
				if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $this->end()) return $end3;
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer edit_category(integer $category_id, string $code, string $name)
	 */
	public function edit_category(int $category_id, string $code, string $name): int {
		$this->clear_error();
		$v_record = $this->field(['category_num'=>['COUNT(*)']])->table(['product_categories'])->where(['id'=>$category_id, 'code'=>$code], ['neq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['category_num'] > 0) return -2; // Err : x, -)-
			$category_datas = ['code'=>$code, 'name'=>$name];
			return $this->table(['product_categories'])->where(['id'=>$category_id])->edit(merge_time($category_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer add_category(string $code, string $name)
	 */
	public function add_category(string $code, string $name): int {
		$this->clear_error();
		$region_flags = $this->field(['id'])->table(['express_address_regions'])->order(['id'=>'asc'])->line('id');
		$v_record = $this->field(['category_num'=>['COUNT(*)']])->table(['product_categories'])->where(['code'=>$code])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['category_num'] > 0) return -2; // Err : x, -)-
			elseif($this->begin()){
				$category_datas = ['code'=>$code, 'name'=>$name];
				if($this->table(['product_categories'])->add(merge_time($category_datas)) > 0){
					$category_id = $this->get_last_id();
					$end1 = $this->table(['product_categories'])->where(['id'=>$category_id])->edit(merge_time(['place'=>(100000 + $category_id) * 10], false));
					$end2 = true;
					foreach($region_flags as $region_id){
						$tax_datas = ['express_address_region_id'=>$region_id, 'product_category_id'=>$category_id, 'rate'=>0.000];
						if($this->table(['taxes'])->add(merge_time($tax_datas)) > 0) continue;
						$end2 = false;
						break;
					}
					if($end1 > 0 && $end2 && $this->end()) return $category_id;
				}
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer move_categroy(integer $category_id, boolean $down = true)
	 */
	public function move_category(int $category_id, bool $down = true): int {
		$this->clear_error();
		$v_record = $this->field(['category_num'=>['COUNT(*)'], 'place'])->table(['product_categories'])->where(['id'=>$category_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['category_num']) return 0; // Err : x, -)-
			elseif($down) $vm_record = $this->field(['id', 'place'])->table(['product_categories'])->where(['place'=>$v_record['place']], ['gr'])->order(['place'=>'asc'])->limit(1)->one();
			else $vm_record = $this->field(['id', 'place'])->table(['product_categories'])->where(['place'=>$v_record['place']], ['sm'])->order(['place'=>'desc'])->limit(1)->one();
			if($this->get_error() == self::ERR_NONE){
				if(empty($vm_record)) return -2; // Err : x, -)-
				elseif($this->begin()){
					$end1 = $this->table(['product_categories'])->where(['id'=>$vm_record['id']])->edit(merge_time(['place'=>$v_record['place']], false));
					$end2 = $this->table(['product_categories'])->where(['id'=>$category_id])->edit(merge_time(['place'=>$vm_record['place']], false));
					if($end1 > 0 && $end2 > 0 && $this->end()) return $end2;
					$this->rollback();
				}
			}
		}
		return -1;
	}
	
	/**
	 * public integer get_category_property_num(integer $category_id)
	 */
	public function get_category_property_num(int $category_id): int {
		$record = $this->field(['property_num'=>['COUNT(*)']])->table(['product_category_properties'])->where(['category_id'=>$category_id])->one();
		return $record['property_num'] ?? 0;
	}
	
	/**
	 * public array get_category_properties(integer $category_id)
	 */
	public function get_category_properties(int $category_id): array {
		return $this->_category_property_view()->table(['product_category_properties'])->where(['category_id'=>$category_id])->order(['id'=>'asc'])->select();
	}
	
	/**
	 * public integer delete_category_property(integer $property_id)
	 */
	public function delete_category_property(int $property_id): int {
		$this->clear_error();
		$v_record = $this->field(['property_num'=>['COUNT(*)'], 'category_id'])->table(['product_category_properties'])->where(['id'=>$property_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['property_num']) return 0; // Err : x, -)-
			$vm_record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['category_id'=>$v_record['category_id']])->one();
			if($this->get_error() == self::ERR_NONE){ // if($vm_record['product_num'] > 0) return -2; // Err : x, -)-
return $this->table(['product_category_properties'])->where(['id'=>$property_id])->delete();}
		}
		return -1;
	}
	
	/**
	 * public integer edit_category_property(integer $property_id, string $name)
	 */
	public function edit_category_property(int $property_id, string $name): int {
		$this->clear_error();
		$v_record = $this->field(['property_num'=>['COUNT(*)'], 'category_id'])->table(['product_category_properties'])->where(['id'=>$property_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['property_num']) return 0; // Err : x, -)-
			$vm_record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['category_id'=>$v_record['category_id']])->one();
			if($this->get_error() == self::ERR_NONE){
				if($vm_record['product_num'] > 0) return -2; // Err : x, -)-
				return $this->table(['product_category_properties'])->where(['id'=>$property_id])->edit(merge_time(['name'=>$name], false));
			}
		}
		return -1;
	}
	
	/**
	 * public integer add_category_property(integer $category_id, string $name)
	 */
	public function add_category_property(int $category_id, string $name): int {
		$this->clear_error();
		$max_property_num = $this->get_param(self::PARAM_MAX_PRODUCT_CATEGORY_PROPERTY_NUM);
		$property_num = $this->get_category_property_num($category_id);
		$v_record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['category_id'=>$category_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if($property_num >= $max_property_num) return -2; // Err : Param_Max_Product_Category_Property_Num, -)-
			elseif($v_record['product_num'] > 0) return -3; // Err : x, -)-
			$property_datas = ['category_id'=>$category_id, 'name'=>$name];
			if($this->table(['product_category_properties'])->add(merge_time($property_datas)) > 0) return $this->get_last_id();
		}
		return -1;
	}
	
	/**
	 * public integer get_category_product_num(integer $category_id, ?string $status = null)
	 * @string $status = Product::IS_PREPARED|Product::IS_ONLINE|Product::IS_OFFLINE
	 */
	public function get_category_product_num(int $category_id, ?string $status = null): int {
		$yes_status_enums = [self::IS_PREPARED, self::IS_ONLINE, self::IS_OFFLINE];
		if(is_null($status)) $record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['category_id'=>$category_id])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['category_id'=>$category_id, 'status'=>$status], ['eq', 'eq'])->one();
		return $record['product_num'] ?? 0;
	}
	
	/**
	 * public array get_category_product_page(integer $category_id, ?string $status = null, string $sort= Product::SORT_DEFAULT, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Product::IS_PREPARED|Product::IS_ONLINE|Product::IS_OFFLINE
	 * @string $sort = Product:SORT_DEFAULT|Product::SORT_SALE_NUM|Product::SORT_PRICE_UP|Product::SORT_PRICE_DOWN|Product::SORT_ADD_TIME
	 */
	public function get_category_product_page(int $category_id, ?string $status = null, string $sort = Product::SORT_DEFAULT, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_PREPARED, self::IS_ONLINE, self::IS_OFFLINE];
		$yes_sort_enums = [self::SORT_DEFAULT=>['p.place'=>'desc'], self::SORT_SALE_NUM=>['p.sale_num'=>'desc'], self::SORT_PRICE_UP=>['p.min_tag_price'=>'asc'], self::SORT_PRICE_DOWN=>['p.min_tag_price'=>'desc'], self::SORT_ADD_TIME=>['p.add_time'=>'desc']];
		if(in_array($sort, array_keys($yes_sort_enums), true)){
			if(is_null($status)) $records = $this->_category_product_view()->where(['pc.id'=>$category_id])->order($yes_sort_enums[$sort])->pill($page_num, $page_size);
			elseif(in_array($status, $yes_status_enums, true)) $records = $this->_category_product_view()->where(['pc.id'=>$category_id, 'status'=>$status], ['eq', 'eq'])->order($yes_sort_enums[$sort])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	//
	FUNCTION ___________________________________________________02() {
	}
	//
	
	/**
	 * public integer get_group_num(void)
	 */
	public function get_group_num(): int {
		$record = $this->field(['group_num'=>['COUNT(*)']])->table(['product_groups'])->one();
		return $record['group_num'] ?? 0;
	}
	
	/**
	 * public array get_groups(void)
	 */
	public function get_groups(): array {
		return $this->_group_view()->table(['product_groups'])->order(['place'=>'asc'])->select();
	}
	
	/**
	 * public array get_group_page(integer $page_num = 1, interger $page_size = 20)
	 */
	public function get_group_page(int $page_num = 1, int $page_size = 20): array {
		$records = $this->_group_view()->order(['place'=>'asc'])->pill($page_num, $page_size);
		return $this->get_group_extra_datas($records);
	}
	
	/**
	 * public array get_group_record(integer $group_id)
	 */
	public function get_group_record(int $group_id): array {
		$record = $this->_group_view()->where(['id'=>$group_id])->one();
		return $this->get_group_extra_datas($record, false);
	}
	
	/**
	 * public integer delete_group(integer $group_id)
	 */
	public function delete_group(int $group_id): int {
		if($this->begin()){
			$end1 = $this->table(['product_subgroup_details'])->where(['group_id'=>$group_id])->delete();
			$end2 = $this->table(['product_subgroups'])->where(['group_id'=>$group_id])->delete();
			$end3 = $this->table(['product_group_details'])->where(['group_id'=>$group_id])->delete();
			$end4 = $this->table(['product_groups'])->where(['id'=>$group_id])->delete();
			if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $end4 >= 0 && $this->end()) return $end4;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer edit_group(integer $group_id, string $code, string $name)
	 */
	public function edit_group(int $group_id, string $code, string $name): int {
		$this->clear_error();
		$v_record = $this->field(['group_num'=>['COUNT(*)']])->table(['product_groups'])->where(['id'=>$group_id, 'code'=>$code], ['neq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['group_num'] > 0) return -2; // Err : x, -)-
			$group_datas = ['code'=>$code, 'name'=>$name];
			return $this->table(['product_groups'])->where(['id'=>$group_id])->edit(merge_time($group_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer add_group(string $code, string $name)
	 */
	public function add_group(string $code, string $name): int {
		$this->clear_error();
		$v_record = $this->field(['group_num'=>['COUNT(*)']])->table(['product_groups'])->where(['code'=>$code])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['group_num'] > 0) return -2; // Err : x, -)-
			elseif($this->begin()){
				$group_datas = ['code'=>$code, 'name'=>$name];
				if($this->table(['product_groups'])->add(merge_time($group_datas)) > 0){
					$group_id = $this->get_last_id();
					$end = $this->table(['product_groups'])->where(['id'=>$group_id])->edit(merge_time(['place'=>(200000 + $group_id) * 10], false));
					if($end > 0 && $this->end()) return $group_id;
				}
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer move_group(integer $group_id, boolean $down = true)
	 */
	public function move_group(int $group_id, bool $down = true): int {
		$this->clear_error();
		$v_record = $this->field(['group_num'=>['COUNT(*)'], 'place'])->table(['product_groups'])->where(['id'=>$group_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['group_num']) return 0; // Err : x, -)-
			if($down) $vm_record = $this->field(['id', 'place'])->table(['product_groups'])->where(['place'=>$v_record['place']], ['gr'])->order(['place'=>'asc'])->limit(1)->one();
			else $vm_record = $this->field(['id', 'place'])->table(['product_groups'])->where(['place'=>$v_record['place']], ['sm'])->order(['place'=>'desc'])->limit(1)->one();
			if($this->get_error() == self::ERR_NONE){
				if(empty($vm_record)) return -2; // Err : x, -)-
				elseif($this->begin()){
					$end1 = $this->table(['product_groups'])->where(['id'=>$vm_record['id']])->edit(merge_time(['place'=>$v_record['place']], false));
					$end2 = $this->table(['product_groups'])->where(['id'=>$group_id])->edit(merge_time(['place'=>$vm_record['place']], false));
					if($end1 > 0 && $end2 > 0 && $this->end()) return $end2;
					$this->rollback();
				}
			}
		}
		return -1;
	}
	
	/**
	 * public integer get_group_product_num(integer $group_id, ?string $status = null)
	 * @string $status = Product::IS_PREPARED|Product::IS_ONLINE|Product::IS_OFFLINE
	 */
	public function get_group_product_num(int $group_id, ?string $status = null): int {
		$yes_status_enums = [self::IS_PREPARED, self::IS_ONLINE, self::IS_OFFLINE];
		if(is_null($status)) $record = $this->field(['product_num'=>['COUNT(*)']])->table(['product_group_details'])->where(['group_id'=>$group_id])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['product_num'=>['COUNT(*)']])->table(['pgd'=>'product_group_details'])->join(['p'=>'products', 'pgd.product_id'=>'p.id'])->where(['pgd.group_id'=>$group_id, 'p.status'=>$status], ['eq', 'eq'])->one();
		return $record['product_num'] ?? 0;
	}
	
	/**
	 * public array get_group_product_page(integer $group_id, ?string $status = null, string $sort= Product::SORT_DEFAULT, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Product::IS_PREPARED|Product::IS_ONLINE|Product::IS_OFFLINE
	 * @string $sort = Product:SORT_DEFAULT|Product::SORT_SALE_NUM|Product::SORT_PRICE_UP|Product::SORT_PRICE_DOWN|Product::SORT_ADD_TIME
	 */
	public function get_group_product_page(int $group_id, ?string $status = null, string $sort = Product::SORT_DEFAULT, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_PREPARED, self::IS_ONLINE, self::IS_OFFLINE];
		$yes_sort_enums = [self::SORT_DEFAULT=>['p.place'=>'desc'], self::SORT_SALE_NUM=>['p.sale_num'=>'desc'], self::SORT_PRICE_UP=>['p.min_tag_price'=>'asc'], self::SORT_PRICE_DOWN=>['p.min_tag_price'=>'desc'], self::SORT_ADD_TIME=>['p.add_time'=>'desc']];
		if(in_array($sort, array_keys($yes_sort_enums), true)){
			if(is_null($status)) $records = $this->_group_product_view()->where(['pgd.group_id'=>$group_id])->order($yes_sort_enums[$sort])->pill($page_num, $page_size);
			elseif(in_array($status, $yes_status_enums, true)) $records = $this->_group_product_view()->where(['pgd.group_id'=>$group_id, 'p.status'=>$status], ['eq', 'eq'])->order($yes_sort_enums[$sort])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public integer delete_group_product(integer $group_id, integer $product_id)
	 */
	public function delete_group_product(int $group_id, int $product_id): int {
		$this->clear_error();
		if($this->begin()){
			$end1 = $this->table(['product_subgroup_details'])->where(['group_id'=>$group_id, 'product_id'=>$product_id], ['eq', 'eq'])->delete();
			$end2 = $this->table(['product_group_details'])->where(['group_id'=>$group_id, 'product_id'=>$product_id], ['eq', 'eq'])->delete();
			if($end1 >= 0 && $end2 >= 0 && $this->end()) return $end2;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer add_group_product(integer $group_id, integer $product_id)
	 */
	public function add_group_product(int $group_id, int $product_id): int {
		$this->clear_error();
		$v_record = $this->field(['detail_num'=>['COUNT(*)']])->table(['product_group_details'])->where(['group_id'=>$group_id, 'product_id'=>$product_id], ['eq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['detail_num'] > 0) return -2; // Err : x, -)-
			$detail_datas = ['group_id'=>$group_id, 'product_id'=>$product_id];
			if($this->table(['product_group_details'])->add(merge_time($detail_datas)) > 0) return $this->get_last_id();
		}
		return -1;
	}
	
	//
	FUNCTION ___________________________________________________03() {
	}
	//
	
	/**
	 * public integer get_subgroup_num(integer $group_id)
	 */
	public function get_subgroup_num(int $group_id): int {
		$record = $this->field(['subgroup_num'=>['COUNT(*)']])->table(['product_subgroups'])->where(['group_id'=>$group_id])->one();
		return $record['subgroup_num'] ?? 0;
	}
	
	/**
	 * public array get_subgroups(integer $group_id)
	 */
	public function get_subgroups(int $group_id): array {
		return $this->_subgroup_view()->where(['psg.group_id'=>$group_id])->order(['psg.place'=>'asc'])->select();
	}
	
	/**
	 * public array get_subgroup_page(integer $group_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_subgroup_page(int $group_id, int $page_num = 1, int $page_size = 20): array {
		$records = $this->_subgroup_view()->where(['psg.group_id'=>$group_id])->order(['psg.place'=>'asc'])->pill($page_num, $page_size);
		return $this->get_subgroup_extra_datas($records);
	}
	
	/**
	 * public array get_subgroup_record(integer $subgroup_id)
	 */
	public function get_subgroup_record(int $subgroup_id): array {
		$record = $this->_subgroup_view()->where(['psg.id'=>$subgroup_id])->one();
		return $this->get_subgroup_extra_datas($record, false);
	}
	
	/**
	 * public integer delete_subgroup(integer $subgroup_id)
	 */
	public function delete_subgroup(int $subgroup_id): int {
		$this->clear_error();
		if($this->begin()){
			$end1 = $this->table(['product_subgroup_details'])->where(['subgroup_id'=>$subgroup_id])->delete();
			$end2 = $this->table(['product_subgroups'])->where(['id'=>$subgroup_id])->delete();
			if($end1 >= 0 && $end2 >= 0 && $this->end()) return $end2;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer edit_subgroup(integer $subgroup_id, string $code, string $name)
	 */
	public function edit_subgroup(int $subgroup_id, string $code, string $name): int {
		$this->clear_error();
		$v_record = $this->field(['subgroup_num'=>['COUNT(*)'], 'group_id'])->table(['product_subgroups'])->where(['id'=>$subgroup_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['subgroup_num']) return 0; // Err : x, -)-
			$vm_record = $this->field(['subgroup_num'=>['COUNT(*)']])->table(['product_subgroups'])->where(['id'=>$subgroup_id, 'group_id'=>$v_record['group_id'], 'code'=>$code], ['neq', 'eq', 'eq'])->one();
			if($this->get_error() == self::ERR_NONE){
				if($vm_record['subgroup_num'] > 0) return -2; // Err : x, -)-
				$subgroup_datas = ['code'=>$code, 'name'=>$name];
				return $this->table(['product_subgroups'])->where(['id'=>$subgroup_id])->edit(merge_time($subgroup_datas, false));
			}
		}
		return -1;
	}
	
	/**
	 * public integer add_subgroup(integer $group_id, string $code, string $name)
	 */
	public function add_subgroup(int $group_id, string $code, string $name): int {
		$this->clear_error();
		$v_record = $this->field(['subgroup_num'=>['COUNT(*)']])->table(['product_subgroups'])->where(['group_id'=>$group_id, 'code'=>$code], ['eq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['subgroup_num'] > 0) return -2; // Err : x, -)-
			elseif($this->begin()){
				$subgroup_datas = ['group_id'=>$group_id, 'code'=>$code, 'name'=>$name];
				if($this->table(['product_subgroups'])->add(merge_time($subgroup_datas)) > 0){
					$subgroup_id = $this->get_last_id();
					$end = $this->table(['product_subgroups'])->where(['id'=>$subgroup_id])->edit(merge_time(['place'=>(300000 + $subgroup_id) * 10], false));
					if($end > 0 && $this->end()) return $subgroup_id;
				}
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer get_subgroup_product_num(integer $subgroup_id, ?string $status = null)
	 * @string $status = Product::IS_PREPARED|Product::IS_ONLINE|Product::IS_OFFLINE
	 */
	public function get_subgroup_product_num(int $subgroup_id, ?string $status = null): int {
		$yes_status_enums = [self::IS_PREPARED, self::IS_ONLINE, self::IS_OFFLINE];
		if(is_null($status)) $record = $this->field(['product_num'=>['COUNT(*)']])->table(['product_subgroup_details'])->where(['subgroup_id'=>$subgroup_id])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['product_num'=>['COUNT(*)']])->table(['psgd'=>'product_subgroup_details'])->join(['p'=>'products', 'psgd.product_id'=>'p.id'])->where(['psgd.subgroup_id'=>$subgroup_id, 'p.status'=>$status], ['eq', 'eq'])->one();
		return $record['product_num'] ?? 0;
	}
	
	/**
	 * public array get_subgroup_product_page(integer $subgroup_id, ?string $status = null, string $sort= Product::SORT_DEFAULT, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Product::IS_PREPARED|Product::IS_ONLINE|Product::IS_OFFLINE
	 * @string $sort = Product:SORT_DEFAULT|Product::SORT_SALE_NUM|Product::SORT_PRICE_UP|Product::SORT_PRICE_DOWN|Product::SORT_ADD_TIME
	 */
	public function get_subgroup_product_page(int $subgroup_id, ?string $status = null, string $sort = Product::SORT_DEFAULT, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_PREPARED, self::IS_ONLINE, self::IS_OFFLINE];
		$yes_sort_enums = [self::SORT_DEFAULT=>['p.place'=>'desc'], self::SORT_SALE_NUM=>['p.sale_num'=>'desc'], self::SORT_PRICE_UP=>['p.min_tag_price'=>'asc'], self::SORT_PRICE_DOWN=>['p.min_tag_price'=>'desc'], self::SORT_ADD_TIME=>['p.add_time'=>'desc']];
		if(in_array($sort, array_keys($yes_sort_enums), true)){
			if(is_null($status)) $records = $this->_subgroup_product_view()->where(['psgd.subgroup_id'=>$subgroup_id])->order($yes_sort_enums[$sort])->pill($page_num, $page_size);
			elseif(in_array($status, $yes_status_enums, true)) $records = $this->_subgroup_product_view()->where(['psgd.subgroup_id'=>$subgroup_id, 'p.status'=>$status], ['eq', 'eq'])->order($yes_sort_enums[$sort])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public integer delete_subgroup_product(integer $subgroup_id, integer $product_id)
	 */
	public function delete_subgroup_product(int $subgroup_id, int $product_id): int {
		return $this->table(['product_subgroup_details'])->where(['subgroup_id'=>$subgroup_id, 'product_id'=>$product_id], ['eq', 'eq'])->delete();
	}
	
	/**
	 * public integer add_subgroup_product(integer $subgroup_id, integer $product_id)
	 */
	public function add_subgroup_product(int $subgroup_id, int $product_id): int {
		$this->clear_error();
		$v_record1 = $this->field(['subgroup_num'=>['COUNT(*)'], 'group_id'])->table(['product_subgroups'])->where(['id'=>$subgroup_id])->one();
		$v_record2 = $this->field(['detail_num'=>['COUNT(*)']])->table(['product_subgroup_details'])->where(['subgroup_id'=>$subgroup_id, 'product_id'=>$product_id], ['eq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record1['subgroup_num']) return -1;
			elseif($v_record2['detail_num'] > 0) return -2; // Err : x, -)-
			$group_detail_datas = ['group_id'=>$v_record1['group_id'], 'product_id'=>$product_id];
			$subgroup_detail_datas = ['group_id'=>$v_record1['group_id'], 'subgroup_id'=>$subgroup_id, 'product_id'=>$product_id];
			$vm_record = $this->field(['detail_num'=>['COUNT(*)']])->table(['product_group_details'])->where(['group_id'=>$v_record1['group_id'], 'product_id'=>$product_id], ['eq', 'eq'])->one();
			if($this->get_error() == self::ERR_NONE){
				if($vm_record['detail_num'] > 0 && $this->table(['product_subgroup_details'])->add(merge_time($subgroup_detail_datas)) > 0) return $this->get_last_id();
				elseif($this->begin()){
					$end1 = $this->table(['product_group_details'])->add(merge_time($group_detail_datas));
					$end2 = $this->table(['product_subgroup_details'])->add(merge_time($subgroup_detail_datas));
					if($end1 > 0 && $end2 > 0 && $this->end()) return $this->get_last_id();
					$this->rollback();
				}
			}
		}
		return -1;
	}
	
	//
	FUNCTION ___________________________________________________04() {
	}
	//
	
	/**
	 * public integer num(?string $status = null)
	 * @string $status = Product::IS_PREPARED|Product::IS_ONLINE|Product::IS_OFFLINE
	 */
	public function num(?string $status = null): int {
		$yes_status_enums = [self::IS_PREPARED, self::IS_ONLINE, self::IS_OFFLINE];
		if(is_null($status)) $record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->one();
		elseif(in_array($status, $yes_status_enums, true)) $record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['status'=>$status])->one();
		return $record['product_num'] ?? 0;
	}
	
	/**
	 * public array page(?string $status = null, string $sort= Product::SORT_DEFAULT, integer $page_num = 1, integer $page_size = 20)
	 * @string $status = Product::IS_PREPARED|Product::IS_ONLINE|Product::IS_OFFLINE
	 * @string $sort = Product:SORT_DEFAULT|Product::SORT_SALE_NUM|Product::SORT_PRICE_UP|Product::SORT_PRICE_DOWN|Product::SORT_ADD_TIME
	 */
	public function page(?string $status = null, string $sort = Product::SORT_DEFAULT, int $page_num = 1, int $page_size = 20): array {
		$yes_status_enums = [self::IS_PREPARED, self::IS_ONLINE, self::IS_OFFLINE];
		$yes_sort_enums = [self::SORT_DEFAULT=>['p.place'=>'desc'], self::SORT_SALE_NUM=>['p.sale_num'=>'desc'], self::SORT_PRICE_UP=>['p.min_tag_price'=>'asc'], self::SORT_PRICE_DOWN=>['p.min_tag_price'=>'desc'], self::SORT_ADD_TIME=>['p.add_time'=>'desc']];
		if(in_array($sort, array_keys($yes_sort_enums), true)){
			if(is_null($status)) $records = $this->_view()->order($yes_sort_enums[$sort])->pill($page_num, $page_size);
			elseif(in_array($status, $yes_status_enums, true)) $records = $this->_view()->where(['status'=>$status])->order($yes_sort_enums[$sort])->pill($page_num, $page_size);
		}
		return $records ?? [];
	}
	
	/**
	 * public array record(integer $product_id)
	 */
	public function record(int $product_id): array {
		return $this->_view()->where(['p.id'=>$product_id])->one();
	}
	
	/**
	 * public integer remove(integer $product_id)
	 */
	public function remove(int $product_id): int {
		$this->clear_error();
		$v_record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['id'=>$product_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['product_num']) return 0; // Err : x, -)-
			elseif($this->begin()){
				$end1 = $this->table(['product_group_details'])->where(['product_id'=>$product_id])->delete();
				$end2 = $this->table(['product_subgroup_details'])->where(['product_id'=>$product_id])->delete();
				$end3 = $this->table(['product_property_enums'])->where(['product_id'=>$product_id])->delete();
				$end4 = $this->table(['product_models'])->where(['product_id'=>$product_id])->delete();
				$end5 = $this->table(['product_videos'])->where(['product_id'=>$product_id])->delete();
				$end6 = $this->table(['product_pictures'])->where(['product_id'=>$product_id])->delete();
				$end7 = $this->table(['product_description_pictures'])->where(['product_id'=>$product_id])->delete();
				$end8 = $this->table(['products'])->where(['id'=>$product_id])->delete();
				if($end1 >= 0 && $end2 >= 0 && $end3 >= 0 && $end4 >= 0 && $end5 >= 0 && $end6 >= 0 && $end7 >= 0 && $end8 > 0 && $this->end()) return $end8;
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer change(integer $product_id, string $code, string $name, float $min_tag_price, float $min_discount_price)
	 */
	public function change(int $product_id, string $code, string $name, float $min_tag_price, float $min_discount_price): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$v_record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['id'=>$product_id, 'code'=>$code], ['neq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['product_num'] > 0) return -2; // Err : x, -)-
			$product_datas = ['code'=>$code, 'name'=>$name, 'min_tag_price'=>$min_tag_price, 'min_discount_price'=>$min_discount_price];
			return $this->table(['products'])->where(['id'=>$product_id])->edit(merge_time($product_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer create(integer $category_id, string $code, string $name, float $min_tag_price, float $min_discount_price)
	 */
	public function create(int $category_id, string $code, string $name, float $min_tag_price, float $min_discount_price): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$v_record = $this->field(['product_num'=>['COUNT(*)']])->table(['products'])->where(['code'=>$code])->one();
		$unique_id = $this->unique_id();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['product_num'] > 0) return -2; // Err : x, -)-
			elseif($this->begin()){
				$product_datas = ['unique_id'=>$unique_id, 'category_id'=>$category_id, 'code'=>$code, 'name'=>$name];
				$product_datas += ['money_type'=>$system_money_type, 'min_tag_price'=>$min_tag_price, 'min_discount_price'=>$min_discount_price];
				if($this->table(['products'])->add(merge_time($product_datas)) > 0){
					$product_id = $this->get_last_id();
					$end = $this->table(['products'])->where(['id'=>$product_id])->edit(merge_time(['place'=>(400000 + $product_id) * 10], false));
					if($end > 0 && $this->end()) return $product_id;
				}
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public get_stock_num(integer $product_id)
	 */
	public function get_stock_num(int $product_id): int {
		$record = $this->field(['stock_num'=>['SUM(`stock_num`)']])->table(['product_models'])->where(['product_id'=>$product_id])->one();
		return $record['stock_num'] ?? 0;
	}
	
	/**
	 * public integer set_express_carriage_template(integer $product_id, integer $template_id)
	 */
	public function set_express_carriage_template(int $product_id, int $template_id): int {
		return $this->table(['products'])->where(['id'=>$product_id])->edit(merge_time(['express_carriage_template_id'=>$template_id], false));
	}
	
	/**
	 * public integer set_primary_video(integer $product_id, string $file_name)
	 */
	public function set_primary_video(int $product_id, string $file_name): int {
		return $this->table(['products'])->where(['id'=>$product_id])->edit(merge_time(['primary_video'=>$file_name], false));
	}
	
	/**
	 * public integer set_primary_picture(integer $product_id, string $file_name)
	 */
	public function set_primary_picture(int $product_id, string $file_name): int {
		return $this->table(['products'])->where(['id'=>$product_id])->edit(merge_time(['primary_picture'=>$file_name], false));
	}
	
	/**
	 * public integer offline(integer $product_id)
	 */
	public function offline(int $product_id): int {
		return $this->table(['products'])->where(['id'=>$product_id])->edit(merge_time(['status'=>self::IS_OFFLINE], false));
	}
	
	/**
	 * public integer online(integer $product_id)
	 */
	public function online(int $product_id): int {
		// check ? 
		
		return $this->table(['products'])->where(['id'=>$product_id])->edit(merge_time(['status'=>self::IS_ONLINE], false));
	}
	
	/**
	 * public array find(string $key)
	 */
	public function find(string $key): array {
		//
	}
	
	//
	FUNCTION ___________________________________________________05() {
	}
	//
	
	/**
	 * public array get_product_property_enums(integer $product_id, boolean $key_mode = true)
	 */
	public function get_product_property_enums(int $product_id, bool $key_mode = true): array {
		$records = $this->_product_property_enum_view()->where(['ppe.product_id'=>$product_id])->order(['pcp.id'=>'asc'])->select();
		if($key_mode){
			foreach($records as $record){
				$end_datas[$record['name']][$record['id']] = $record['data'];
			}
			return $end_datas ?? [];
		}else{
			foreach($records as $record){
				$end_datas[$record['name']][] = $record['name'] . '**' . $record['data'];
			}
			return array_values($end_datas ?? []);
		}
	}
	
	/**
	 * public integer delete_product_property_enum(integer $enum_id)
	 */
	public function delete_product_property_enum(int $enum_id): int {
		return $this->table(['product_property_enums'])->where(['id'=>$enum_id])->delete();
	}
	
	/**
	 * public integer edit_product_property_enum(integer $enum_id, string $data)
	 */
	public function edit_product_property_enum(int $enum_id, string $data): int {
		return $this->table(['product_property_enums'])->where(['id'=>$enum_id])->edit(merge_time(['data'=>$data], false));
	}
	
	/**
	 * public integer add_product_property_enum(integer $product_id, integer $property_id, string $data)
	 */
	public function add_product_property_enum(int $product_id, int $property_id, string $data): int {
		$this->clear_error();
		$v_record = $this->field(['product_num'=>['COUNT(*)'], 'category_id'])->table(['products'])->where(['id'=>$product_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['product_num']) return 0; // Err : x, -)-
			$property_flags = $this->field(['id'])->table(['product_category_properties'])->where(['category_id'=>$v_record['category_id']])->line('id');
			if($this->get_error() == self::ERR_NONE){
				if(!in_array($property_id, $property_flags, true)) return -2; // Err : x, -)-
				$enum_datas = ['product_id'=>$product_id, 'property_id'=>$property_id, 'data'=>$data];
				if($this->table(['product_property_enums'])->add(merge_time($enum_datas)) > 0) return $this->get_last_id();
			}
		}
		return -1;
	}
	
	//
	FUNCTION ___________________________________________________06() {
	}
	//
	
	/**
	 * public integer get_product_model_num(integer $product_id)
	 */
	public function get_product_model_num(int $product_id): int {
		$record = $this->field(['model_num'=>['COUNT(*)']])->table(['product_models'])->where(['product_id'=>$product_id])->one();
		return $record['model_num'] ?? 0;
	}
	
	/**
	 * public array get_product_models(integer $product_id)
	 */
	public function get_product_models(int $product_id): array {
		$records = $this->_product_model_view()->where(['product_id'=>$product_id])->order(['id'=>'asc'])->select();
		return $this->transform_product_model_description_datas($records);
	}
	
	/**
	 * public array get_product_model_page(integer $product_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_product_model_page(int $product_id, int $page_num = 1, int $page_size = 20): array {
		$records = $this->_product_model_view()->where(['product_id'=>$product_id])->order(['id'=>'asc'])->pill($page_num, $page_size);
		return $this->transform_product_model_description_datas($records);
	}
	
	/**
	 * public array get_product_model_record(integer $model_id)
	 */
	public function get_product_model_record(int $model_id): array {
		$record = $this->_product_model_view()->where(['id'=>$model_id])->one();
		return $this->transform_product_model_description_datas($record, false);
	}
	
	/**
	 * public integer edit_product_model(integer $model_id, float $tag_price, float $discount_price, integer $stock_num)
	 */
	public function edit_product_model(int $model_id, float $tag_price, float $discount_price, int $stock_num): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		if($this->get_error() == self::ERR_NONE){
			$model_datas = ['money_type'=>$system_money_type, 'tag_price'=>$tag_price, 'discount_price'=>$discount_price, 'stock_num'=>$stock_num];
			return $this->table(['product_models'])->where(['id'=>$model_id])->edit(merge_time($model_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer make_product_models(integer $product_id)
	 */
	public function make_product_models(int $product_id): int {
		$this->clear_error();
		$system_money_type = (new System($this->operator_type, $this->operator_id))->get_param(System::PARAM_MONEY_TYPE);
		$v_record = $this->field(['product_num'=>['COUNT(*)'], 'category_id', 'money_type', 'min_tag_price', 'min_discount_price'])->table(['products'])->where(['id'=>$product_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['product_num']) return 0; // Err : x, -)-
			elseif($this->begin()){
				$description_datas = $this->make_product_model_description_datas($this->get_product_property_enums($product_id, false));
				$end1 = $this->table(['product_models'])->where(['product_id'=>$product_id])->delete();
				$end2 = true;
				$counter = 0;
				foreach($description_datas as $description){
					$model_datas = ['product_id'=>$product_id, 'description'=>$description, 'money_type'=>$system_money_type];
					$model_datas += ['tag_price'=>$v_record['min_tag_price'], 'discount_price'=>$v_record['min_discount_price'], 'stock_num'=>0];
					if($this->table(['product_models'])->add(merge_time($model_datas)) > 0){
						$counter++;
						continue;
					}
					$end2 = false;
					break;
				}
				if($end1 >= 0 && $end2 && $this->end()) return $counter;
				$this->rollback();
			}
		}
		return -1;
	}
	
	//
	FUNCTION ___________________________________________________07() {
	}
	//
	
	/**
	 * public integer get_product_video_num(integer $product_id)
	 */
	public function get_product_video_num(int $product_id): int {
		$record = $this->field(['video_num'=>['COUNT(*)']])->table(['product_videos'])->where(['product_id'=>$product_id])->one();
		return $record['video_num'] ?? 0;
	}
	
	/**
	 * public array get_product_videos(integer $product_id)
	 */
	public function get_product_videos(int $product_id): array {
		return $this->_product_video_view()->where(['product_id'=>$product_id])->order(['place'=>'asc'])->select();
	}
	
	/**
	 * public integer delete_product_video(integer $video_id)
	 */
	public function delete_product_video(int $video_id): int {
		return $this->table(['product_videos'])->where(['id'=>$video_id])->delete();
	}
	
	/**
	 * public integer add_product_video(integer $product_id, string $file_name)
	 */
	public function add_product_video(int $product_id, string $file_name): int {
		$this->clear_error();
		$max_video_num = $this->get_param(self::PARAM_MAX_PRODUCT_VIDEO_NUM);
		$video_num = $this->get_product_video_num($product_id);
		$v_record = $this->field(['video_num'=>['COUNT(*)']])->table(['product_videos'])->where(['file_name'=>$file_name])->one();
		if($this->get_error() == self::ERR_NONE){
			if($video_num >= $max_video_num) return -2; // Err: Param_Max_Product_Video_Num
			elseif($v_record['video_num'] > 0) return -3; // Err : x, -)-
			elseif($this->begin()){
				$video_datas = ['product_id'=>$product_id, 'file_name'=>$file_name];
				if($this->table(['product_videos'])->add(merge_time($video_datas)) > 0){
					$video_id = $this->get_last_id();
					$end = $this->table(['product_videos'])->where(['id'=>$video_id])->edit(merge_time(['place'=>(500000 + $video_id) * 10], false));
					if($end > 0 && $this->end()) return $video_id;
				}
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer move_product_video(integer $video_id, boolean $down = true)
	 */
	public function move_product_video(int $video_id, bool $down = true): int {
		$this->clear_error();
		$v_record = $this->field(['video_num'=>['COUNT(*)'], 'product_id', 'place'])->table(['product_videos'])->where(['id'=>$video_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['video_num']) return 0; // Err : x, -)-
			if($down) $vm_record = $this->field(['id', 'place'])->table(['product_videos'])->where(['product_id'=>$v_record['product_id'], 'place'=>$v_record['place']], ['eq', 'gr'])->order(['place'=>'asc'])->limit(1)->one();
			else $vm_record = $this->field(['id', 'place'])->table(['product_videos'])->where(['product_id'=>$v_record['product_id'], 'place'=>$v_record['place']], ['eq', 'sm'])->order(['place'=>'desc'])->limit(1)->one();
			if($this->get_error() == self::ERR_NONE){
				if(empty($vm_record)) return -2; // Err : x, -)-
				elseif($this->begin()){
					$end1 = $this->table(['product_videos'])->where(['id'=>$vm_record['id']])->edit(merge_time(['place'=>$v_record['place']], false));
					$end2 = $this->table(['product_videos'])->where(['id'=>$video_id])->edit(merge_time(['place'=>$vm_record['place']], false));
					if($end1 > 0 && $end2 > 0 && $this->end()) return $end2;
					$this->rollback();
				}
			}
		}
		return -1;
	}
	
	/**
	 * public integer clear_product_videos(integer $product_id)
	 */
	public function clear_product_videos(int $product_id): int {
		return $this->table(['product_videos'])->where(['product_id'=>$product_id])->delete();
	}
	
	/**
	 * public integer get_product_picture_num(integer $product_id)
	 */
	public function get_product_picture_num(int $product_id): int {
		$record = $this->field(['picture_num'=>['COUNT(*)']])->table(['product_pictures'])->where(['product_id'=>$product_id])->one();
		return $record['picture_num'] ?? 0;
	}
	
	/**
	 * public array get_product_pictures(integer $product_id)
	 */
	public function get_product_pictures(int $product_id): array {
		return $this->_product_picture_view()->where(['product_id'=>$product_id])->order(['place'=>'asc'])->select();
	}
	
	/**
	 * public integer delete_product_picture(integer $picture_id)
	 */
	public function delete_product_picture(int $picture_id): int {
		return $this->table(['product_pictures'])->where(['id'=>$picture_id])->delete();
	}
	
	/**
	 * public integer add_product_picture(integer $product_id, string $file_name)
	 */
	public function add_product_picture(int $product_id, string $file_name): int {
		$this->clear_error();
		$max_picture_num = $this->get_param(self::PARAM_MAX_PRODUCT_PICTURE_NUM);
		$picture_num = $this->get_product_picture_num($product_id);
		$v_record = $this->field(['picture_num'=>['COUNT(*)']])->table(['product_pictures'])->where(['file_name'=>$file_name])->one();
		if($this->get_error() == self::ERR_NONE){
			if($picture_num >= $max_picture_num) return -2; // Err: Param_Max_Product_Picture_Num
			elseif($v_record['picture_num'] > 0) return -3; // Err : x, -)-
			elseif($this->begin()){
				$picture_datas = ['product_id'=>$product_id, 'file_name'=>$file_name];
				if($this->table(['product_pictures'])->add(merge_time($picture_datas)) > 0){
					$picture_id = $this->get_last_id();
					$end = $this->table(['product_pictures'])->where(['id'=>$picture_id])->edit(merge_time(['place'=>(600000 + $picture_id) * 10], false));
					if($end > 0 && $this->end()) return $picture_id;
				}
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer move_product_picture(integer $picture_id, boolean $down = true)
	 */
	public function move_product_picture(int $picture_id, bool $down = true): int {
		$this->clear_error();
		$v_record = $this->field(['picture_num'=>['COUNT(*)'], 'product_id', 'place'])->table(['product_pictures'])->where(['id'=>$picture_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['picture_num']) return 0; // Err : x, -)-
			if($down) $vm_record = $this->field(['id', 'place'])->table(['product_pictures'])->where(['product_id'=>$v_record['product_id'], 'place'=>$v_record['place']], ['eq', 'gr'])->order(['place'=>'asc'])->limit(1)->one();
			else $vm_record = $this->field(['id', 'place'])->table(['product_pictures'])->where(['product_id'=>$v_record['product_id'], 'place'=>$v_record['place']], ['eq', 'sm'])->order(['place'=>'desc'])->limit(1)->one();
			if($this->get_error() == self::ERR_NONE){
				if(empty($vm_record)) return -2; // Err : x, -)-
				elseif($this->begin()){
					$end1 = $this->table(['product_pictures'])->where(['id'=>$vm_record['id']])->edit(merge_time(['place'=>$v_record['place']], false));
					$end2 = $this->table(['product_pictures'])->where(['id'=>$picture_id])->edit(merge_time(['place'=>$vm_record['place']], false));
					if($end1 > 0 && $end2 > 0 && $this->end()) return $end2;
					$this->rollback();
				}
			}
		}
		return -1;
	}
	
	/**
	 * public integer clear_product_pictures(integer $product_id)
	 */
	public function clear_product_pictures(int $product_id): int {
		return $this->table(['product_pictures'])->where(['product_id'=>$product_id])->delete();
	}
	
	/**
	 * public integer get_product_description_picture_num(integer $product_id)
	 */
	public function get_product_description_picture_num(int $product_id): int {
		$record = $this->field(['picture_num'=>['COUNT(*)']])->table(['product_description_pictures'])->where(['product_id'=>$product_id])->one();
		return $record['picture_num'] ?? 0;
	}
	
	/**
	 * public array get_product_description_pictures(integer $product_id)
	 */
	public function get_product_description_pictures(int $product_id): array {
		return $this->_product_description_picture_view()->where(['product_id'=>$product_id])->order(['place'=>'asc'])->select();
	}
	
	/**
	 * public integer delete_product_description_picture(integer $picture_id)
	 */
	public function delete_product_description_picture(int $picture_id): int {
		return $this->table(['product_description_pictures'])->where(['id'=>$picture_id])->delete();
	}
	
	/**
	 * public integer add_product_description_picture(integer $product_id, string $file_name)
	 */
	public function add_product_description_picture(int $product_id, string $file_name): int {
		$this->clear_error();
		$max_picture_num = $this->get_param(self::PARAM_MAX_PRODUCT_DESCRIPTION_PICTURE_NUM);
		$picture_num = $this->get_product_description_picture_num($product_id);
		$v_record = $this->field(['picture_num'=>['COUNT(*)']])->table(['product_description_pictures'])->where(['file_name'=>$file_name])->one();
		if($this->get_error() == self::ERR_NONE){
			if($picture_num >= $max_picture_num) return -2; // Err: Param_Max_Product_Description_Picture_Num
			elseif($v_record['picture_num'] > 0) return -3; // Err : x, -)-
			elseif($this->begin()){
				$picture_datas = ['product_id'=>$product_id, 'file_name'=>$file_name];
				if($this->table(['product_description_pictures'])->add(merge_time($picture_datas)) > 0){
					$picture_id = $this->get_last_id();
					$end = $this->table(['product_description_pictures'])->where(['id'=>$picture_id])->edit(merge_time(['place'=>(700000 + $picture_id) * 10], false));
					if($end > 0 && $this->end()) return $picture_id;
				}
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer move_product_description_picture(integer $picture_id, boolean $down = true)
	 */
	public function move_product_description_picture(int $picture_id, bool $down = true): int {
		$this->clear_error();
		$v_record = $this->field(['picture_num'=>['COUNT(*)'], 'product_id', 'place'])->table(['product_description_pictures'])->where(['id'=>$picture_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['picture_num']) return 0; // Err : x, -)-
			if($down) $vm_record = $this->field(['id', 'place'])->table(['product_description_pictures'])->where(['product_id'=>$v_record['product_id'], 'place'=>$v_record['place']], ['eq', 'gr'])->order(['place'=>'asc'])->limit(1)->one();
			else $vm_record = $this->field(['id', 'place'])->table(['product_description_pictures'])->where(['product_id'=>$v_record['product_id'], 'place'=>$v_record['place']], ['eq', 'sm'])->order(['place'=>'desc'])->limit(1)->one();
			if($this->get_error() == self::ERR_NONE){
				if(empty($vm_record)) return -2; // Err : x, -)-
				elseif($this->begin()){
					$end1 = $this->table(['product_description_pictures'])->where(['id'=>$vm_record['id']])->edit(merge_time(['place'=>$v_record['place']], false));
					$end2 = $this->table(['product_description_pictures'])->where(['id'=>$picture_id])->edit(merge_time(['place'=>$vm_record['place']], false));
					if($end1 > 0 && $end2 > 0 && $this->end()) return $end2;
					$this->rollback();
				}
			}
		}
		return -1;
	}
	
	/**
	 * public integer clear_product_description_pictures(integer $product_id)
	 */
	public function clear_product_description_pictures(int $product_id): int {
		return $this->table(['product_description_pictures'])->where(['product_id'=>$product_id])->delete();
	}
	
	//
	FUNCTION ___________________________________________________08() {
	}
	//
	
	/**
	 * public integer get_product_review_num(integer $product_id)
	 */
	public function get_product_review_num(int $product_id): int {
		$record = $this->field(['review_num'=>['COUNT(*)']])->table(['user_reviews'])->where(['product_id'=>$product_id])->one();
		return $record['review_num'] ?? 0;
	}
	
	/**
	 * public array get_product_review_page(integer $product_id, integer $page_num = 1, interger $page_size = 20)
	 */
	public function get_product_review_page(int $product_id, int $page_num = 1, int $page_size = 20): array {
		return $this->_product_review_view()->order(['id'=>'desc'])->pill($page_num, $page_size);
	}
	
	/**
	 * public integer delete_product_review(integer $review_id)
	 */
	public function delete_product_review(int $review_id): int {
		return $this->table(['user_reviews'])->where(['id'=>$review_id])->delete();
	}
	
	//
	FUNCTION ___________________________________________________09() {
	}
	//
	
	/**
	 * protected array get_category_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_category_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$property_nums = $this->get_category_property_nums();
		$product_nums = $this->get_category_product_nums();
		$func = function (&$record) use ($property_nums, $product_nums) {
			$record['property_num'] = $property_nums[$record['id']] ?? 0;
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
	 * protected array get_category_property_nums(void)
	 */
	protected function get_category_property_nums(): array {
		return $this->field(['property_num'=>['COUNT(*)'], 'category_id'])->table(['product_category_properties'])->group(['category_id'])->order(['category_id'=>'asc'])->line('property_num', 'category_id');
	}
	
	/**
	 * protected array get_category_product_nums(void)
	 */
	protected function get_category_product_nums(): array {
		return $this->field(['product_num'=>['COUNT(*)'], 'category_id'])->table(['products'])->group(['category_id'])->order(['category_id'=>'asc'])->line('product_num', 'category_id');
	}
	
	/**
	 * protected array get_group_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_group_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$subgroup_nums = $this->get_group_subgroup_nums();
		$product_nums = $this->get_group_product_nums();
		$func = function (&$record) use ($subgroup_nums, $product_nums) {
			$record['subgroup_num'] = $subgroup_nums[$record['id']] ?? 0;
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
	 * protected array get_group_subgroup_nums(void)
	 */
	protected function get_group_subgroup_nums(): array {
		return $this->field(['subgroup_num'=>['COUNT(*)'], 'group_id'])->table(['product_subgroups'])->group(['group_id'])->order(['group_id'=>'asc'])->line('subgroup_num', 'group_id');
	}
	
	/**
	 * protected array get_group_product_nums(void)
	 */
	protected function get_group_product_nums(): array {
		return $this->field(['product_num'=>['COUNT(*)'], 'group_id'])->table(['product_group_details'])->group(['group_id'])->order(['group_id'=>'asc'])->line('product_num', 'group_id');
	}
	
	/**
	 * protected array get_subgroup_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_subgroup_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$product_nums = $this->get_subgroup_product_nums();
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
	 * protected array get_subgroup_product_nums(void)
	 */
	protected function get_subgroup_product_nums(): array {
		return $this->field(['product_num'=>['COUNT(*)'], 'subgroup_id'])->table(['product_subgroup_details'])->group(['subgroup_id'])->order(['subgroup_id'=>'asc'])->line('product_num', 'subgroup_id');
	}
	
	/**
	 * protected array get_product_record_extra_datas(array $record)
	 */
	protected function get_product_record_extra_datas(array $record): array {
		if(empty($record)) return [];
		$record['video_num'] = $this->get_product_video_num($record['id']);
		$record['video_records'] = $this->get_product_videos($record['id']);
		$record['picture_num'] = $this->get_product_picture_num($record['id']);
		$record['picture_records'] = $this->get_product_pictures($record['id']);
		$record['description_picture_num'] = $this->get_product_description_picture_num($record['id']);
		$record['description_picture_records'] = $this->get_product_description_pictures($record['id']);
		$record['review_num'] = $this->get_product_review_num($record['id']);
		return $record;
	}
	
	/**
	 * protected array make_product_model_description_datas(?array $datas = null, integer $key = 0)
	 */
	protected function make_product_model_description_datas(?array $datas = null, int $key = 0): array {
		if(empty($datas[$key])) return [];
		elseif(empty($datas[$key + 1])) return $datas[$key];
		$now_datas = $datas[$key++];
		$down_datas = $this->make_product_model_description_datas($datas, $key);
		foreach($now_datas as $now_data){
			foreach($down_datas as $down_data){
				$end_datas[] = $now_data . ',,' . $down_data;
			}
		}
		return $end_datas;
	}
	
	/**
	 * protected array transform_product_model_description_datas(array $datas, boolean $many = true)
	 */
	protected function transform_product_model_description_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$func = function (&$record) {
			list($names, $datas) = [[], []];
			$property_datas = explode(',,', $record['description']);
			foreach($property_datas as $property){
				list($names[], $datas[]) = explode('**', $property);
			}
			$record['description'] = array_combine($names, $datas);
		};
		if($many){
			foreach($datas as &$data){
				$func($data);
			}
		}else
			$func($datas);
		return $datas;
	}
	
	//
	FUNCTION ___________________________________________________10() {
	}
	//
	
	/**
	 * private Product _category_view(void)
	 */
	private function _category_view(): Product {
		$pc_cols = ['id', 'code', 'name', 'place', 'last_edit_time', 'add_time'];
		$extra_cols = ['property_num'=>null, 'product_num'=>null];
		$this->field(array_merge($pc_cols, $extra_cols))->table(['product_categories']);
		return $this;
	}
	
	/**
	 * private Product _category_property_view(void)
	 */
	private function _category_property_view(): Product {
		$pcp_cols = ['id', 'name', 'last_edit_time', 'add_time'];
		$this->field($pcp_cols)->table(['product_category_properties']);
		return $this;
	}
	
	/**
	 * private Product _category_product_view(void)
	 */
	private function _category_product_view(): Product {
		$p_cols = ['p.id', 'p.unique_id', 'p.code', 'p.name', 'p.primary_video', 'p.primary_picture', 'p.money_type', 'p.min_tag_price', 'p.min_discount_price', 'p.place', 'p.click_num', 'p.sale_num', 'p.status', 'p.last_edit_time', 'p.add_time'];
		$pc_cols = ['category_id'=>['pc.id'], 'category_code'=>['pc.code']];
		$this->field(array_merge($p_cols, $pc_cols))->table(['p'=>'products']);
		$this->join(['pc'=>'product_categories', 'p.category_id'=>'pc.id']);
		return $this;
	}
	
	/**
	 * private Product _group_view(void)
	 */
	private function _group_view(): Product {
		$pg_cols = ['id', 'code', 'name', 'place', 'last_edit_time', 'add_time'];
		$extra_cols = ['subgroup_num'=>null, 'product_num'=>null];
		$this->field(array_merge($pg_cols, $extra_cols))->table(['product_groups']);
		return $this;
	}
	
	/**
	 * private Product _group_product_view(void)
	 */
	private function _group_product_view(): Product {
		$p_cols = ['p.id', 'p.unique_id', 'p.code', 'p.name', 'p.primary_video', 'p.primary_picture', 'p.money_type', 'p.min_tag_price', 'p.min_discount_price', 'p.place', 'p.click_num', 'p.sale_num', 'p.status', 'p.last_edit_time', 'p.add_time'];
		$pc_cols = ['category_id'=>['pc.id'], 'category_code'=>['pc.code']];
		$this->field(array_merge($p_cols, $pc_cols))->table(['pgd'=>'product_group_details']);
		$this->join(['p'=>'products', 'pgd.product_id'=>'p.id']);
		$this->join(['pc'=>'product_categories', 'p.category_id'=>'pc.id']);
		return $this;
	}
	
	/**
	 * private Product _subgroup_view(void)
	 */
	private function _subgroup_view(): Product {
		$psg_cols = ['psg.id', 'psg.code', 'psg.name', 'psg.place', 'psg.last_edit_time', 'psg.add_time'];
		$pg_cols = ['group_id'=>['pg.id'], 'group_code'=>['pg.code']];
		$extra_cols = ['product_num'=>null];
		$this->field(array_merge($psg_cols, $pg_cols, $extra_cols))->table(['psg'=>'product_subgroups']);
		$this->join(['pg'=>'product_groups', 'psg.group_id'=>'pg.id']);
		return $this;
	}
	
	/**
	 * private Product _subgroup_product_view(void)
	 */
	private function _subgroup_product_view(): Product {
		$p_cols = ['p.id', 'p.unique_id', 'p.code', 'p.name', 'p.primary_video', 'p.primary_picture', 'p.money_type', 'p.min_tag_price', 'p.min_discount_price', 'p.place', 'p.click_num', 'p.sale_num', 'p.status', 'p.last_edit_time', 'p.add_time'];
		$pc_cols = ['category_id'=>['pc.id'], 'category_code'=>['pc.code']];
		$this->field(array_merge($p_cols, $pc_cols))->table(['psgd'=>'product_subgroup_details']);
		$this->join(['p'=>'products', 'psgd.product_id'=>'p.id']);
		$this->join(['pc'=>'product_categories', 'p.category_id'=>'pc.id']);
		return $this;
	}
	
	/**
	 * private Product _view(void)
	 */
	private function _view(): Product {
		$p_cols = ['p.id', 'p.unique_id', 'p.code', 'p.name', 'p.primary_video', 'p.primary_picture', 'p.money_type', 'p.min_tag_price', 'p.min_discount_price', 'p.place', 'p.click_num', 'p.sale_num', 'p.status', 'p.last_edit_time', 'p.add_time'];
		$pc_cols = ['category_id'=>['pc.id'], 'category_code'=>['pc.code']];
		$this->field(array_merge($p_cols, $pc_cols))->table(['p'=>'products']);
		$this->join(['pc'=>'product_categories', 'p.category_id'=>'pc.id']);
		return $this;
	}
	
	/**
	 * private Product _product_property_enum_view(void)
	 */
	private function _product_property_enum_view(): Product {
		$ppe_cols = ['ppe.id', 'ppe.data'];
		$pcp_cols = ['pcp.name'];
		$this->field(array_merge($ppe_cols, $pcp_cols))->table(['ppe'=>'product_property_enums']);
		$this->join(['pcp'=>'product_category_properties', 'ppe.property_id'=>'pcp.id']);
		return $this;
	}
	
	/**
	 * private Product _product_model_view(void)
	 */
	private function _product_model_view(): Product {
		$pm_cols = ['id', 'description', 'money_type', 'tag_price', 'discount_price', 'stock_num', 'last_edit_time', 'add_time'];
		$this->field($pm_cols)->table(['product_models']);
		return $this;
	}
	
	/**
	 * private Product _product_video_view(void)
	 */
	private function _product_video_view(): Product {
		$pv_cols = ['id', 'file_name', 'place', 'last_edit_time', 'add_time'];
		$this->field($pv_cols)->table(['product_videos']);
		return $this;
	}
	
	/**
	 * private Product _product_picture_view(void)
	 */
	private function _product_picture_view(): Product {
		$pp_cols = ['id', 'file_name', 'place', 'last_edit_time', 'add_time'];
		$this->field($pp_cols)->table(['product_pictures']);
		return $this;
	}
	
	/**
	 * private Product _product_description_picture_view(void)
	 */
	private function _product_description_picture_view(): Product {
		$pdp_cols = ['id', 'file_name', 'place', 'last_edit_time', 'add_time'];
		$this->field($pdp_cols)->table(['product_description_pictures']);
		return $this;
	}
	
	/**
	 * private Product _product_review_view(void)
	 */
	private function _product_review_view(): Product {
		$ur_cols = ['ur.id', 'ur.product_id', 'ur.product_detail_id', 'ur.product_unique_id', 'ur.product_code', 'ur.product_name', 'ur.product_property_enum_data_group', 'ur.grade', 'ur.description', 'ur.last_edit_time', 'ur.add_time'];
		$u_cols = ['user_id'=>'u.id', 'user_unique_id'=>'u.unique_id', 'user_nickname'=>'u.nickname'];
		$o_cols = ['order_id'=>'o.id', 'order_unique_id'=>'o.unique_id'];
		$this->field(array_merge($ur_cols, $u_cols, $o_cols))->table(['ur'=>'user_reviews']);
		$this->join(['u'=>'users', 'ur.user_id'=>'u.id']);
		$this->join(['o'=>'orders', 'ur.order_id'=>'o.id']);
		return $this;
	}
	// -- END : 2019-05-07 --
}











