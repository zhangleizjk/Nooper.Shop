<?php

// declare(strict_types = 1);
namespace Nooper;

use ZipArchive;

class Logger {
	
	/**
	 * Properties
	 */
	protected $dir;
	protected $file_fullname;
	protected $file_name;
	
	/**
	 * public void __construct(?string $file_name = null)
	 */
	public function __construct(?string $file_name = null) {
		if(is_string($file_name) && is_log_file_named_regular($file_name)) $this->file_name = $file_name;
		elseif(is_null($file_name)) $this->file_name = get_config('default_log_file');
		if(is_string($this->file_name)){
			$this->dir = web_path . '/' . log_path;
			$this->file_fullname = $this->dir . '/' . $this->file_name;
		}
	}
	
	/**
	 * public boolean clear(void)
	 */
	public function clear(): bool {
		if($this->file_fullname){
			$file = @fopen($this->file_fullname, 'wb');
			return false === $file ? false : true;
		}
		return false;
	}
	
	/**
	 * public boolean backup(void)
	 */
	public function backup(): bool {
		$file_name = (string)get_timestamp() . '.log.zip';
		$file_fullname = $this->dir . '/' . $file_name;
		$package = new ZipArchive();
		if($package->open($file_fullname, ZipArchive::CREATE | ZipArchive::OVERWRITE)){
			if($package->addFile($this->file_fullname, $this->file_name) && $package->close() && $this->clear()) return true;
		}
		return false;
	}
	
	/**
	 * public boolean write(string $type, string $user, string $cmd, string $grade = 'error')
	 * @@$grade = 'error|general'
	 */
	public function write(string $type, string $user, string $cmd, string $grade = 'error'): bool {
		if($this->file_fullname){
			$file = @fopen($this->file_fullname, 'ab');
			if(false !== $file){
				$datas = [$grade, $type, $user, $this->wrapper($cmd), get_timestamp()];
				$end = @fputcsv($file, $datas, '^');
				return false === $end ? false : true;
			}
		}
		return false;
	}
	
	/**
	 * protected string wrapper(string $data)
	 */
	protected function wrapper(string $data): string {
		return '*** ' . $data . ' ***';
	}
	
	//
}

