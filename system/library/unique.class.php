<?php

// declare(strict_types = 1);
namespace NooperShop;

class Unique {
	
	/**
	 * public string file_name(void)
	 */
	public function file_name(): string {
		return $this->get_rand_str(40); // Note : COUNT=40, -)-
	}
	
	/**
	 * public string token(void)
	 */
	public function token(): string {
		return $this->get_unique_id([30]); // Note : COUNT=30, -)-
	}
	
	/**
	 * public string user(void)
	 */
	public function user(): string {
		return $this->get_unique_id([5, 8, 7, 5]); // Note : COUNT=28, -)-
	}
	

	
	/**
	 * public string product(void)
	 */
	public function product(): string {
		return $this->get_unique_id([5, 6, 3, 5]); // Note : COUNT=22, -)-
	}
	
	/**
	 * public boolean is_order(string $data)
	 */
	public function is_order(string $data): bool {
		$identifier = '[a-zA-Z0-9]';
		$pattern = '/^' . $identifier . '{8}-' . $identifier . '{5}-' . $identifier . '{8}-' . $identifier . '{8}$/';
		return preg_match($pattern, $data) ? true : false;
	}
	
	/**
	 * public string order(void)
	 */
	public function order(): string {
		return $this->get_unique_id([8, 5, 8, 8]); // Note : COUNT=32, -)-
	}
	
	/**
	 * public boolean is_gift(string $data)
	 */
	public function is_gift(string $data): bool {
		$identifier = '[a-zA-Z0-9]';
		$pattern = '/^' . $identifier . '{8}-' . $identifier . '{6}-' . $identifier . '{5}-' . $identifier . '{8}$/';
		return preg_match($pattern, $data) ? true : false;
	}
	
	/**
	 * public string gift(void)
	 */
	public function gift(): string {
		return $this->get_unique_id([8, 6, 5, 8]); // Note : COUNT=30, -)-
	}
	
	/**
	 * public string coupon(void)
	 */
	public function coupon(): string {
		return $this->get_unique_id([8, 9, 6, 8]); // Note : COUNT=34, -)-
	}
	
	/**
	 * public string message(void)
	 */
	public function message(): string {
		return $this->get_unique_id([6, 8, 6, 8, 8]); // Note : COUNT=40, -)-
	}
	
	/**
	 * public string express(void)
	 */
	public function express(): string {
		return $this->get_unique_id([5, 9, 4, 5]); // Note : COUNT=26, -)-
	}
	
	/**
	 * public string password(void)
	 */
	public function password(): string {
		return $this->get_rand_str(12); // Note : COUNT=12, -)-
	}
	
	/**
	 * protected string get_unique_id(array $lengths)
	 * @array $lengths = [integer $length,...]
	 */
	protected function get_unique_id(array $lengths): string {
		foreach($lengths as $length){
			if(is_integer($length)){
				$strs[] = $this->get_rand_str($length);
			}
		}
		return strtoupper(implode('-', $strs ?? []));
	}
	
	/**
	 * protected string get_rand_str(integer $length)
	 */
	protected function get_rand_str(int $length): string {
		$str = '';
		$chars = array_merge(range('0', '9'), range('A', 'Z'), range('a', 'z'));
		for($i = 0; $i < $length; $i++){
			$str .= $chars[mt_rand(0, count($chars) - 1)];
		}
		return $str;
	}
	// -- Version : 0.99-Beta [2019-05-21] --
	// -- END --
}