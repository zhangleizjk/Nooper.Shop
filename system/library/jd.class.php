<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mimicry;
use Nooper\Translator;

class JD {
	
	/**
	 * SET php.ini : max_execution_time = 120
	 */
	
	/**
	 * Properties
	 */
	protected $mimi;
	protected $translator;
	protected $schema = 'nooper_shop';
	protected $region_table = 'express_address_regions';
	protected $region_fields = ['id', 'code', 'name', 'status', 'last_edit_time', 'add_time'];
	protected $province_table = 'express_address_provinces';
	protected $province_fields = ['id', 'region_id', 'name', 'status', 'last_edit_time', 'add_time'];
	protected $city_table = 'express_address_cities';
	protected $city_fields = ['id', 'region_id', 'province_id', 'name', 'status', 'last_edit_time', 'add_time'];
	protected $county_table = 'express_address_counties';
	protected $county_fields = ['id', 'region_id', 'province_id', 'city_id', 'name', 'status', 'last_edit_time', 'add_time'];
	protected $town_table = 'express_address_towns';
	protected $town_fields = ['id', 'region_id', 'province_id', 'city_id', 'county_id', 'name', 'status', 'last_edit_time', 'add_time'];
	protected $platform = 'windows';
	protected $sql_file = './init_mysql_express_data.sql';
	protected $http_url = 'https://fts.jd.com/area/get';
	protected $http_param = 'fid';
	protected $region_datas = [['code'=>'CHN', 'name'=>'中国', 'jd_id'=>4555]];
	protected $province_datas = [['region_id'=>1, 'name'=>'北京', 'jd_id'=>1], ['region_id'=>1, 'name'=>'上海', 'jd_id'=>2], ['region_id'=>1, 'name'=>'天津', 'jd_id'=>3], ['region_id'=>1, 'name'=>'重庆', 'jd_id'=>4], ['region_id'=>1, 'name'=>'河北', 'jd_id'=>5], ['region_id'=>1, 'name'=>'山西', 'jd_id'=>6], ['region_id'=>1, 'name'=>'河南', 'jd_id'=>7], ['region_id'=>1, 'name'=>'辽宁', 'jd_id'=>8], ['region_id'=>1, 'name'=>'吉林', 'jd_id'=>9], ['region_id'=>1, 'name'=>'黑龙江', 'jd_id'=>10], ['region_id'=>1, 'name'=>'内蒙古', 'jd_id'=>11], ['region_id'=>1, 'name'=>'江苏', 'jd_id'=>12], ['region_id'=>1, 'name'=>'山东', 'jd_id'=>13], ['region_id'=>1, 'name'=>'安徽', 'jd_id'=>14], ['region_id'=>1, 'name'=>'浙江', 'jd_id'=>15], ['region_id'=>1, 'name'=>'福建', 'jd_id'=>16], ['region_id'=>1, 'name'=>'湖北', 'jd_id'=>17], ['region_id'=>1, 'name'=>'湖南', 'jd_id'=>18], ['region_id'=>1, 'name'=>'广东', 'jd_id'=>19], ['region_id'=>1, 'name'=>'广西', 'jd_id'=>20], ['region_id'=>1, 'name'=>'江西', 'jd_id'=>21], ['region_id'=>1, 'name'=>'四川', 'jd_id'=>22], ['region_id'=>1, 'name'=>'海南', 'jd_id'=>23], ['region_id'=>1, 'name'=>'贵州', 'jd_id'=>24], ['region_id'=>1, 'name'=>'云南', 'jd_id'=>25], ['region_id'=>1, 'name'=>'西藏', 'jd_id'=>26], ['region_id'=>1, 'name'=>'陕西', 'jd_id'=>27], ['region_id'=>1, 'name'=>'甘肃', 'jd_id'=>28], ['region_id'=>1, 'name'=>'青海', 'jd_id'=>29], ['region_id'=>1, 'name'=>'宁夏', 'jd_id'=>30], ['region_id'=>1, 'name'=>'新疆', 'jd_id'=>31], ['region_id'=>1, 'name'=>'港澳', 'jd_id'=>52993], ['region_id'=>1, 'name'=>'台湾', 'jd_id'=>32]];
	protected $city_datas = [];
	protected $county_datas = [];
	protected $town_datas = [];
	protected $pointer;
	
	/**
	 * public void __construct(void)
	 */
	public function __construct() {
		$this->translator = new Translator();
		$this->mimi = new Mimicry();
	}
	
	/**
	 * public void __destruct(void)
	 */
	public function __destruct() {
		if($this->pointer) fclose($this->pointer);
		$this->pointer = null;
	}
	
	/**
	 * public void quick(void)
	 */
	public function quick(): void {
		$this->create()->write_schema('Working in Nooper_Shop')->write_region('Part 9: Express')->write_province()->write_city()->write_county()->write_town();
	}
	
	/**
	 * public JD create(string $platform = 'windows')
	 * @string $platform = 'windows|linux|unix'
	 */
	public function create(string $platform = 'windows'): JD {
		$yes_platform_enum = ['windows', 'linux', 'unix'];
		if(in_array($platform, $yes_platform_enum, true)) $this->platform = $platform;
		$this->pointer = fopen($this->sql_file, 'wb');
		return $this;
	}
	
	/**
	 * public JD write_schema(?string $note = null, boolean $empty = true)
	 */
	public function write_schema(?string $note = null, bool $empty = true): JD {
		if(empty($this->pointer)) return $this;
		elseif(is_string($note)) $this->write_comment($note);
		$schema_str = "use " . $this->wrapper($this->schema);
		fwrite($this->pointer, $schema_str);
		if($empty) $this->write_empty();
		return $this;
	}
	
	/**
	 * public JD write_region(?string $note = null, boolean $empty = false)
	 */
	public function write_region(?string $note = null, bool $empty = false): JD {
		if(empty($this->pointer)) return $this;
		elseif(is_string($note)) $this->write_comment($note);
		$table_str = $this->get_table_str($this->region_table, $this->region_fields);
		fwrite($this->pointer, $table_str);
		$region_id = 0;
		$region_num = count($this->region_datas);
		foreach($this->region_datas as $region_data){
			list('code'=>$code, 'name'=>$name) = $region_data;
			$data_str = "\t\t(" . ++$region_id . ", '" . $code . "', '" . $name . "', 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
			$data_str .= $this->line(($region_id == $region_num ? ";" : ","));
			fwrite($this->pointer, $data_str);
		}
		if($empty) $this->write_empty();
		return $this;
	}
	
	/**
	 * public JD write_province(?string $note = null, boolean $empty = false)
	 */
	public function write_province(?string $note = null, bool $empty = false): JD {
		if(empty($this->pointer)) return $this;
		elseif(is_string($note)) $this->write_comment($note);
		$table_str = $this->get_table_str($this->province_table, $this->province_fields);
		fwrite($this->pointer, $table_str);
		$province_id = 0;
		$province_num = count($this->province_datas);
		foreach($this->province_datas as $province_data){
			list('region_id'=>$region_id, 'name'=>$name) = $province_data;
			$data_str = "\t\t(" . ++$province_id . ", " . $region_id . ", '" . $name . "', 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
			$data_str .= $this->line(($province_id == $province_num ? ";" : ","));
			fwrite($this->pointer, $data_str);
		}
		if($empty) $this->write_empty();
		return $this;
	}
	
	/**
	 * public JD write_city(?string $note = null, boolean $empty = false)
	 */
	public function write_city(?string $note = null, bool $empty = false): JD {
		if(empty($this->pointer)) return $this;
		elseif(empty($this->city_datas)) $this->get_city_datas();
		if(is_string($note)) $this->write_comment($note);
		$table_str = $this->get_table_str($this->city_table, $this->city_fields);
		fwrite($this->pointer, $table_str);
		$city_id = 0;
		$city_num = count($this->city_datas);
		foreach($this->city_datas as $city_data){
			list('region_id'=>$region_id, 'province_id'=>$province_id, 'name'=>$name) = $city_data;
			$data_str = "\t\t(" . ++$city_id . ", " . $region_id . ", " . $province_id . ", '" . $name . "', 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
			$data_str .= $this->line(($city_id == $city_num ? ";" : ","));
			fwrite($this->pointer, $data_str);
		}
		if($empty) $this->write_empty();
		return $this;
	}
	
	/**
	 * public JD write_county(?string $note = null, boolean $empty = false)
	 */
	public function write_county(?string $note = null, bool $empty = false): JD {
		if(empty($this->pointer)) return $this;
		elseif(empty($this->county_datas)) $this->get_county_datas();
		if(is_string($note)) $this->write_comment($note);
		$table_str = $this->get_table_str($this->county_table, $this->county_fields);
		fwrite($this->pointer, $table_str);
		$county_id = 0;
		$county_num = count($this->county_datas);
		foreach($this->county_datas as $county_data){
			list('region_id'=>$region_id, 'province_id'=>$province_id, 'city_id'=>$city_id, 'name'=>$name) = $county_data;
			$data_str = "\t\t(" . ++$county_id . ", " . $region_id . ", " . $province_id . ", " . $city_id . ", '" . $name . "', 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
			$data_str .= $this->line(($county_id == $county_num ? ";" : ","));
			fwrite($this->pointer, $data_str);
		}
		if($empty) $this->write_empty();
		return $this;
	}
	
	/**
	 * public JD write_town(?string $note = null, boolean $empty = false)
	 */
	public function write_town(?string $note = null, bool $empty = false): JD {
		if(empty($this->pointer)) return $this;
		elseif(empty($this->town_datas)) $this->get_town_datas();
		if(is_string($note)) $this->write_comment($note);
		$table_str = $this->get_table_str($this->town_table, $this->town_fields);
		fwrite($this->pointer, $table_str);
		$town_id = 0;
		$town_num = count($this->town_datas);
		foreach($this->town_datas as $town_data){
			list('region_id'=>$region_id, 'province_id'=>$province_id, 'city_id'=>$city_id, 'county_id'=>$county_id, 'name'=>$name) = $town_data;
			$data_str = "\t\t(" . ++$town_id . ", " . $region_id . ", " . $province_id . ", " . $city_id . ", " . $county_id . ", '" . $name . "', 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
			$data_str .= $this->line(($town_id == $town_num ? ";" : ","));
			fwrite($this->pointer, $data_str);
		}
		if($empty) $this->write_empty();
		return $this;
	}
	
	/**
	 * protected void write_comment(string $note)
	 */
	protected function write_comment(string $note): void {
		$note_str = $this->line("/**");
		$note_str .= $this->line(" * " . $note);
		$note_str .= $this->line(" */");
		fwrite($this->pointer, $note_str);
	}
	
	/**
	 * protected void write_empty(void)
	 */
	protected function write_empty(): void {
		$empty_str = $this->line("");
		$empty_str .= $this->line("");
		fwrite($this->pointer, $empty_str);
	}
	
	/**
	 * protected array get_region_datas(void)
	 */
	protected function get_region_datas(): array {
		return $this->region_datas;
	}
	
	/**
	 * protected array get_province_datas(void)
	 */
	protected function get_province_datas(): array {
		return $this->province_datas;
	}
	
	/**
	 * protected void get_city_datas(void)
	 */
	protected function get_city_datas(): array {
		$province_id = 1;
		foreach($this->province_datas as $province_data){
			list('region_id'=>$region_id, 'jd_id'=>$jd_id) = $province_data;
			foreach($this->get_datas($jd_id) as $id => $name){
				$city_datas[] = ['region_id'=>$region_id, 'province_id'=>$province_id, 'name'=>$name, 'jd_id'=>$id];
			}
			$province_id++;
		}
		$this->city_datas = $city_datas ?? [];
		return $this->city_datas;
	}
	
	/**
	 * protected void get_county_datas(void)
	 */
	protected function get_county_datas(): array {
		if(empty($this->city_datas)) $this->get_city_datas();
		$city_id = 1;
		foreach($this->city_datas as $city_data){
			list('region_id'=>$region_id, 'province_id'=>$province_id, 'jd_id'=>$jd_id) = $city_data;
			foreach($this->get_datas($jd_id) as $id => $name){
				$county_datas[] = ['region_id'=>$region_id, 'province_id'=>$province_id, 'city_id'=>$city_id, 'name'=>$name, 'jd_id'=>$id];
			}
			$city_id++;
		}
		$this->county_datas = $county_datas ?? [];
		return $this->county_datas;
	}
	
	/**
	 * protected void get_town_datas(void)
	 */
	protected function get_town_datas(): array {
		if(empty($this->county_datas)) $this->get_county_datas();
		$county_id = 1;
		foreach($this->county_datas as $county_data){
			list('region_id'=>$region_id, 'province_id'=>$province_id, 'city_id'=>$city_id, 'jd_id'=>$jd_id) = $county_data;
			foreach($this->get_datas($jd_id) as $id => $name){
				$town_datas[] = ['region_id'=>$region_id, 'province_id'=>$province_id, 'city_id'=>$city_id, 'county_id'=>$county_id, 'name'=>$name, 'jd_id'=>$id];
			}
			$county_id++;
		}
		$this->town_datas = $town_datas ?? [];
		return $this->town_datas;
	}
	
	/**
	 * protected array get_datas(integer $jd_id)
	 */
	protected function get_datas(int $jd_id): array {
		$jd_datas = $this->translator->parse_json($this->fix($this->mimi->get($this->http_url, [$this->http_param=>(string)$jd_id])));
		if($jd_datas){
			$jd_datas = array_column($jd_datas, 'name', 'id');
			ksort($jd_datas, SORT_STRING);
		}
		return $jd_datas ?? [];
	}
	
	/**
	 * protected string get_table_str(string $table, array $fields)
	 */
	protected function get_table_str(string $table, array $fields): string {
		foreach($fields as &$field){
			$field = $this->wrapper($field);
		}
		$table_str = $this->line("truncate table " . $this->wrapper($table) . ";");
		$table_str .= $this->line("insert into " . $this->wrapper($table) . "(" . implode(', ', $fields) . ") values");
		return $table_str;
	}
	
	/**
	 * protected string wrapper(string $data)
	 */
	protected function wrapper(string $data): string {
		return '`' . $data . '`';
	}
	
	/**
	 * protected string line(string $data)
	 */
	protected function line(string $data): string {
		$yes_platform_enum = ['windows'=>"\r\n", 'linux'=>"\n", 'unix'=>"\n"];
		return $data . $yes_platform_enum[$this->platform];
	}
	
	/**
	 * protected string fix(string $json)
	 */
	protected function fix(string $json): string {
		$pattern = '/\\\\\s/';
		return preg_replace($pattern, '', $json);
	}
	// -- END --
}



