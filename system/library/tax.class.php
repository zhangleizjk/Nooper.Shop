<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mysql;

class Tax extends Mysql {
	
	/**
	 * public integer num(integer $region_id)
	 */
	public function num(int $region_id): int {
		$record = $this->field(['tax_num'=>['COUNT(*)']])->table(['taxes'])->where(['express_address_region_id'=>$region_id])->one();
		return $record['tax_num'] ?? 0;
	}
	
	/**
	 * public array page(integer $region_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function page(int $region_id, int $page_num = 1, int $page_size = 20): array {
		return $this->_view()->where(['t.express_address_region_id'=>$region_id])->order(['pc.place'=>'asc'])->pill($page_num, $page_size);
	}
	
	/**
	 * public array record(integer $tax_id)
	 */
	public function record(int $tax_id): array {
		return $this->_view()->where(['t.id'=>$tax_id])->one();
	}
	
	/**
	 * public integer change(integer $tax_id, float $rate)
	 */
	public function change(int $tax_id, float $rate): int {
		return $this->table(['taxes'])->where(['id'=>$tax_id])->edit(merge_time(['rate'=>$rate], false));
	}
	
	/**
	 * private Tax _view(void)
	 */
	private function _view(): Tax {
		$t_cols = ['t.id', 't.rate', 't.last_edit_time', 't.add_time'];
		$ear_cols = ['express_address_region_id'=>['ear.id'], 'express_address_region_code'=>['ear.code']];
		$pc_cols = ['product_category_id'=>['pc.id'], 'product_category_code'=>['pc.code']];
		$this->field(array_merge($t_cols, $ear_cols, $pc_cols))->table(['t'=>'taxes']);
		$this->join(['ear'=>'express_address_regions', 't.express_address_region_id'=>'ear.id']);
		$this->join(['pc'=>'product_categories', 't.product_category_id'=>'pc.id']);
		return $this;
	}
	// -- Version : 0.99-Beta [2019-05-14] --
	// -- END --
}

