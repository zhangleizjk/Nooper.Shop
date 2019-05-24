<?php

// declare(strict_types = 1);
/**
 * $GLOBALS[_nooper_configs]
 */

/**
 * void set_config(string $key, Mixed $value)
 */
function set_config(string $key, $value): void {
	if(is_underline_named_str($key)) $GLOBALS['_nooper_configs'][$key] = $value;
}

/**
 * Mixed get_config(string $key, Mixed $default = null)
 */
function get_config(string $key, $default = null) {
	return $GLOBALS['_nooper_configs'][$key] ?? $default;
}

/**
 * array get_configs(?array $keys = null)
 * @array $keys = [string $key,...]
 */
function get_configs(array $keys = null): array {
	if(is_null($keys)) return $GLOBALS['_nooper_configs'] ?? [];
	foreach($keys as $key){
		if(is_string($key)) $ends[] = get_config($key);
	}
	return $ends ?? [];
}

/**
 * boolean is_single_array(Mixed $datas)
 */
function is_single_array($datas): bool {
	if(is_array($datas)){
		if(1 == count($datas)){
			if(isset($datas[0]) && is_string($datas[0])) return true;
		}
	}
	return false;
}

/**
 * boolean is_my_array(Mixed $datas, array $regulars)
 */
function is_my_array($datas, array $regulars): bool {
	$regular_type_enums = ['boolean'=>'is_bool', 'integer'=>'is_integer', 'float'=>'is_float', 'string'=>'is_string', 'null'=>'is_null', 'array'=>'is_array'];
	foreach($regulars as $regular_key => $regular_type){
		if(is_string($regular_key) && in_array($regular_type, array_keys($regular_type_enums), true)) continue;
		return false;
	}
	if(is_array($datas)){
		if(count($regulars) == count($datas)){
			foreach($datas as $key => $data){
				if(in_array($key, array_keys($regulars), true) && (call_user_func($regular_type_enums[$regulars[$key]], $data))) continue;
				return false;
			}
			return true;
		}
	}
	return false;
}

/**
 * boolean is_no_empty_str(Mixed $data)
 */
function is_no_empty_str($data): bool {
	if(is_string($data) && $data != '') return true;
	return false;
}

/**
 * boolean is_underline_named_str(Mixed $data)
 */
function is_underline_named_str($data): bool {
	if(is_string($data)){
		$pattern = '/^[a-z]+(_[a-z]+)*$/';
		return preg_match($pattern, $data) ? true : false;
	}
	return false;
}

/**
 * boolean is_database_named_regular(Mixed $data, boolean $wildcard = false)
 */
function is_database_named_str($data, bool $wildcard = false): bool {
	return is_database_primary_named_str($data, $wildcard) or is_database_plus_named_str($data, $wildcard);
}

/**
 * boolean is_database_primary_named_regular(Mixed $data, boolean $wildcard = false)
 */
function is_database_primary_named_str($data, bool $wildcard = false): bool {
	if(is_string($data)){
		if(is_underline_named_str($data)) return true;
		elseif($wildcard && '*' == $data) return true;
	}
	return false;
}

/**
 * boolean is_database_plus_named_regular(Mixed $data, boolean $wildcard = false)
 */
function is_database_plus_named_str($data, bool $wildcard = false): bool {
	if(is_string($data)){
		$pieces = explode('.', $data);
		if(count($pieces) == 2 && is_underline_named_str($pieces[0])){
			if(is_underline_named_str($pieces[1])) return true;
			elseif($wildcard && '*' == $pieces[1]) return true;
		}
	}
	return false;
}

/**
 * boolean is_log_file_named_str(Mixed $data)
 */
function is_log_file_named_str($data): bool {
	if(is_string($data)){
		$pattern = '/^[a-z]+(_[a-z]+)*\.log$/';
		return preg_match($pattern, $data) ? true : false;
	}
	return false;
}

/**
 * boolean is_database_connect_params(Mixed $datas)
 * @array $datas = ['protocol|host|port|dbname|charset|username|password' => string $data,...]
 */
function is_database_connect_params($datas): bool {
	$yes_key_enums = ['protocol', 'host', 'port', 'dbname', 'charset', 'username', 'password'];
	if(!is_array($datas)) return false;
	elseif(count($datas) != count($yes_key_enums)) return false;
	foreach($datas as $key => $data){
		if(!in_array($key, $yes_key_enums, true)) return false;
		elseif(!is_string($data)) return false;
	}
	return true;
}

/**
 * string camel2underline_named_str(string $data)
 */
function camel2underline_named_str(string $data): string {
	$pattern = '/([A-Z])/';
	$replace = '_$1';
	return strtolower(preg_replace($pattern, $replace, $data));
}

/**
 * string pascal2underline_named_str(string $data)
 */
function pascal2underline_named_str(string $data): string {
	$data = camel2underline_named_str($data);
	return substr($data, 1);
}

/**
 * string underline2pascal_named_str(string $data)
 */
function underline2pascal_named_str(string $data): string {
	$pattern = '/_([a-z])/';
	return preg_replace($pattern, function ($matches) {
		return strtoupper($matches[1]);
	}, '_' . $data);
}

/**
 * void merge_key2data(string &$data, string $key)
 */
function merge_key2data(string &$data, string $key): void {
	$data = $key . '=' . $data;
}

/**
 * array merge_time(array $datas, boolean $create = true)
 */
function merge_time(array $datas, bool $create = true): array {
	return array_merge($datas, ['last_edit_time'=>['UNIX_TIMESTAMP()']], $create ? ['add_time'=>['UNIX_TIMESTAMP()']] : []);
}

/**
 * string wrap_backquote(string $data)
 */
function wrap_backquote(string $data): string {
	$pieces = explode('.', $data);
	foreach($pieces as &$piece){
		$piece = '`' . $piece . '`';
	}
	return implode('.', $pieces);
}

/**
 * void header_display(string $mime_type)
 */
function header_display(string $mime_type): void {
	$params = ['Cache-Control: no-cache', 'Pragma: no-cache', 'Content-Type: ' . $mime_type];
	foreach($params as $param){
		header($param);
	}
}

/**
 * void header_download(string $mime_type, string $file_name)
 */
function header_download(string $mime_type, string $file_name): void {
	$params = ['Accept-Ranges:bytes', 'Cache-Control: no-cache', 'Pragma: no-cache', 'Content-Description: File Transfer', 'Content-Type: ' . $mime_type, 'Content-Disposition: attachment; filename=' . $file_name, 'Content-Transfer-Encoding: binary'];
	foreach($params as $param){
		header($param);
	}
}

/**
 * string get_rand_str(integer $num = 30)
 */
function get_rand_str(int $num = 30): string {
	$str = '';
	$chars = array_merge(range('0', '9'), range('a', 'z'));
	for($i = 0; $i < $num; $i++){
		$str .= $chars[mt_rand(0, count($chars) - 1)];
	}
	return strtoupper($str);
}

/**
 * ?string get_digital_sign(array $datas, string $api_key)
 */
function get_digital_sign(array $datas, string $api_key): ?string {
	foreach($datas as $key => $data){
		if(!is_string($key) or !is_string($data)) return null;
		elseif('sign' == $key or '' == $data) unset($datas[$key]);
	}
	if($datas){
		ksort($datas);
		array_walk($datas, 'merge_key2data');
		$datas[] = ('key=' . $api_key);
		return strtoupper(md5(implode('&', $datas)));
	}
	return null;
}

/**
 * integer get_timestamp(?string $datetime = null)
 */
function get_timestamp(?string $datetime = null): int {
	$dtz = new DateTimeZone('Asia/Shanghai');
	$dt = is_null($datetime) ? new DateTime('now', $dtz) : DateTime::createFromFormat('YmdHis', $datetime, $dtz);
	return $dt->getTimestamp();
}
//  -- END --

