<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mysql;

class System extends Mysql {
	
	/**
	 * Constants
	 */
	public const PARAM_MONEY_TYPE = 'money_type';
	
	/**
	 * public ?(Number|string) get_param(string $param_name)
	 */
	public function get_param(string $param_name) {
		$record = $this->field(['*'])->table(['system_default_params'])->where(['id'=>1])->one();
		return $record[$param_name] ?? null;
	}
	// -- END --
}






