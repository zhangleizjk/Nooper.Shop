<?php
// declare(strict_types = 1);

/**
 * define path constants
 */
defined('web_path') or define('web_path', 'e:/Nooper.Shop');
defined('core_path') or define('core_path', 'core');
defined('core_function_path') or define('core_function_path', 'function');
defined('core_library_path') or define('core_library_path', 'library');
defined('core_config_path') or define('core_config_path', 'config');
defined('system_path') or define('system_path', 'system');
defined('system_function_path') or define('system_function_path', 'function');
defined('system_library_path') or define('system_library_path', 'library');
defined('system_config_path') or define('system_config_path', 'config');
defined('loader_path') or define('loader_path', 'init');
defined('callback_path') or define('callback_path', 'callback');
defined('binary_path') or define('binary_path', 'bin');
defined('payer_path') or define('payer_path', 'payer');
defined('root_path') or define('root_path', 'root');
defined('app_path') or define('app_path', 'program');
defined('log_path') or define('log_path', 'log');

/**
 * boolean is_function_file_named_regular(string $data)
 */
function is_function_file_named_regular(string $data): bool {
	$pattern = '/^[a-z]+(_[a-z]+)*\.func\.php$/';
	return preg_match($pattern, $data) ? true : false;
}

/**
 * boolean is_class_file_named_regular(string $data)
 */
function is_class_file_named_regular(string $data): bool {
	$pattern = '/^[a-z]+(_[a-z]+)*\.class\.php$/';
	return preg_match($pattern, $data) ? true : false;
}

/**
 * boolean is_config_file_named_regular(string $data)
 */
function is_config_file_named_regular(string $data): bool {
	$pattern = '/^[a-z]+(_[a-z]+)*\.conf\.php$/';
	return preg_match($pattern, $data) ? true : false;
}

/**
 * void load_function_files(string $path)
 */
function load_function_files(string $path): void {
	if(is_dir($path)){
		foreach(scandir($path) as $file_name){
			$full_name = implode('/', [$path, $file_name]);
			if(is_function_file_named_regular($file_name) && is_file($full_name)) require_once $full_name;
		}
	}
}

/**
 * void load_library_files(string $path)
 */
function load_library_files(string $path): void {
	if(is_dir($path)){
		foreach(scandir($path) as $file_name){
			$full_name = implode('/', array($path, $file_name));
			if(is_class_file_named_regular($file_name) && is_file($full_name)) require_once $full_name;
		}
	}
}

/**
 * void load_config_files(string $path)
 */
function load_config_files(string $path): void {
	if(is_dir($path)){
		foreach(scandir($path) as $file_name){
			$full_name = implode('/', array($path, $file_name));
			if(is_config_file_named_regular($file_name) && is_file($full_name)){
				$configs = require_once $full_name;
				if(is_array($configs)){
					foreach($configs as $key => $config){
						set_config($key, $config);
					}
				}
			}
		}
	}
}

/**
 * void load_core_files(void)
 */
function load_core_files(): void {
	load_function_files(implode('/', [web_path, core_path, core_function_path]));
	load_library_files(implode('/', [web_path, core_path, core_library_path]));
	load_config_files(implode('/', [web_path, core_path, core_config_path]));
}

/**
 * void load_system_files(void)
 */
function load_system_files(): void {
	load_function_files(implode('/', [web_path, system_path, system_function_path]));
	load_library_files(implode('/', [web_path, system_path, system_library_path]));
	load_config_files(implode('/', [web_path, system_path, system_config_path]));
}

/**
 * void fire(void)
 */
function fire(): void {
	load_core_files();
	load_system_files();
}

/**
 * init
 */
fire();

