<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mysql;

class Message extends Mysql {
	
	/**
	 * Constants
	 */
	public const PARAM_MAX_READ_KEEP_DURATION = 'max_read_keep_duration';
	public const DURATION_WEEK = 'week';
	public const DURATION_MONTH = 'month';
	public const DURATION_QUARTER = 'quarter';
	public const DURATION_GONE = 'gone';
	public const IS_UNREAD = 'unread';
	public const IS_READ = 'read';
	
	/**
	 * public string unique_id(void)
	 */
	public function unique_id(): string {
		$unique = new Unique();
		do{
			$unique_id = $unique->message();
			$record = $this->field(['message_num'=>['COUNT(*)']])->table(['messages'])->where(['unique_id'=>$unique_id])->one();
			if($record && $record['message_num'] > 0) continue;
			break;
		}while(true);
		return $unique_id;
	}
	
	/**
	 * public ?Number|string get_param(string $param_name)
	 */
	public function get_param(string $param_name) {
		$record = $this->field(['*'])->table(['message_default_params'])->where(['id'=>1])->one();
		return $record[$param_name] ?? null;
	}
	
	/**
	 * public integer get_category_num(void)
	 */
	public function get_category_num(): int {
		$record = $this->field(['category_num'=>['COUNT(*)']])->table(['message_categories'])->one();
		return $record['category_num'] ?? 0;
	}
	
	/**
	 * public array get_categories(void)
	 */
	public function get_categories(): array {
		return $this->_category_view()->order(['id'=>'asc'])->select();
	}
	
	/**
	 * public array get_category_page(integer $page_num = 1)
	 */
	public function get_category_page(int $page_num = 1): array {
		$offset_num = ($page_num - 1) * $this->page_record_num;
		$records = $this->_category_view()->order(['id'=>'asc'])->limit($this->page_record_num, $offset_num)->select();
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
	 * public integer get_template_num(void)
	 */
	public function get_template_num(): int {
		$record = $this->field(['template_num'=>['COUNT(*)']])->table(['message_templates'])->one();
		return $record['template_num'] ?? 0;
	}
	
	/**
	 * public array get_template_page(integer $page_num = 1)
	 */
	public function get_template_page(int $page_num = 1): array {
		$offset_num = ($page_num - 1) * $this->page_record_num;
		return $this->_template_view()->order(['id'=>'asc'])->limit($this->page_record_num, $offset_num)->select();
	}
	
	/**
	 * public array get_template_record(integer $template_id, ?array $replace_datas = null)
	 */
	public function get_template_record(int $template_id, ?array $replace_datas = null): array {
		$record = $this->_template_view()->where(['mt.id'=>$template_id])->one();
		return is_null($replace_datas) ? $record : $this->replace_template_description($record, $replace_datas);
	}
	
	/**
	 * public integer num(?string $duration = null, ?string $status = null)
	 * @string $duration = Gift::DURATION_WEEK|Gift::DURATION_MONTH|Gift::DURATION_QUARTER|Gift::DURATION_GONE
	 * @string $status = Message::IS_UNREAD|Message::IS_READ
	 */
	public function num(?string $duration = null, ?string $status = null): int {
		$yes_durations = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		$yes_statuses = [self::IS_UNREAD, self::IS_READ];
		if(is_null($duration)){
			if(is_null($status)) $record = $this->field(['message_num'=>['COUNT(*)']])->table(['messages'])->one();
			elseif(in_array($status, $yes_statuses, true)) $record = $this->field(['message_num'=>['COUNT(*)']])->table(['messages'])->where(['status'=>$status])->one();
		}elseif(in_array($duration, array_keys($yes_durations), true)){
			$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`add_time`, '%Y-%m-%d'))";
			if(is_null($status)) $record = $this->field(['message_num'=>['COUNT(*)']])->table(['messages'])->where_cmd($duration_prefix . $yes_durations[$duration])->one();
			elseif(in_array($status, $yes_statuses, true)) $record = $this->field(['message_num'=>['COUNT(*)']])->table(['messages'])->where_cmd($duration_prefix . $yes_durations[$duration] . " and `status`='" . $status . "'")->one();
		}
		return $record['message_num'] ?? 0;
	}
	
	/**
	 * public array page(?string $duration = null, ?string $status = null, integer $page_num = 1)
	 * @string $duration = Gift::DURATION_WEEK|Gift::DURATION_MONTH|Gift::DURATION_QUARTER|Gift::DURATION_GONE
	 * @string $status = Message::IS_UNREAD|Message::IS_READ
	 */
	public function page(?string $duration = null, ?string $status = null, int $page_num = 1): array {
		$yes_durations = [self::DURATION_WEEK=>"<7", self::DURATION_MONTH=>"<30", self::DURATION_QUARTER=>"<90", self::DURATION_GONE=>">=90"];
		$yes_statuses = [self::IS_UNREAD, self::IS_READ];
		$offset_num = ($page_num - 1) * $this->page_record_num;
		if(is_null($duration)){
			if(is_null($status)) $records = $this->_view()->order(['m.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
			elseif(in_array($status, $yes_statuses, true)) $records = $this->_view()->where(['m.status'=>$status])->order(['m.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
		}elseif(in_array($duration, array_keys($yes_durations), true)){
			$duration_prefix = "DATEDIFF(CURRENT_DATE(), FROM_UNIXTIME(`m`.`add_time`, '%Y-%m-%d'))";
			if(is_null($status)) $records = $this->_view()->where_cmd($duration_prefix . $yes_durations[$duration])->order(['m.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
			elseif(in_array($status, $yes_statuses, true)) $records = $this->_view()->where_cmd($duration_prefix . $yes_durations[$duration] . " and `m`.`status`='" . $status . "'")->order(['m.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
		}
		return $records ?? [];
	}
	
	/**
	 * public array record(string $message_unique_id)
	 */
	public function record(string $message_unique_id): array {
		return $this->_view()->where(['m.unique_id'=>$message_unique_id])->one();
	}
	
	/**
	 * public integer remove(string $message_unique_id)
	 */
	public function remove(string $message_unique_id): int {
		return $this->table(['messages'])->where(['unique_id'=>$message_unique_id])->delete();
	}
	
	/**
	 * public integer change(string $message_unique_id, string $title, string $description)
	 */
	public function change(string $message_unique_id, string $title, string $description): int {
		$this->clear_error();
		$v_record = $this->field(['message_num'=>['COUNT(*)'], 'status'])->table(['messages'])->where(['unique_id'=>$message_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['message_num']) return 0;
			elseif(self::IS_READ == $v_record['status']) return -2; // Err : x, -)-
			$datas = ['title'=>$title, 'description'=>$description];
			return $this->table(['messages'])->where(['unique_id'=>$message_unique_id])->edit(merge_time($datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer create(integer $category_id, integer $user_id, string $title, string $description)
	 */
	public function create(int $category_id, int $user_id, string $title, string $description): int {
		$this->clear_error();
		$v_record1 = $this->field(['category_num'=>['COUNT(*)']])->table(['message_categories'])->where(['id'=>$category_id])->one();
		$v_record2 = $this->field(['user_num'=>['COUNT(*)']])->table(['users'])->where(['id'=>$user_id])->one();
		$unique_id = $this->unique_id();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record1['category_num']) return -2; // Err : NO exist the category, -)-
			elseif(0 == $v_record2['user_num']) return -3; // Err : NO exist the user, -)-
			$datas = ['unique_id'=>$unique_id, 'category_id'=>$category_id, 'user_id'=>$user_id, 'title'=>$title, 'description'=>$description];
			if($this->table(['messages'])->add(merge_time($datas)) > 0) return $this->get_last_id();
		}
		return -1;
	}
	
	/**
	 * public array find(string $message_unique_id, integer $page_num = 1)
	 */
	public function find(string $message_unique_id, int $page_num = 1): array {
		$offset_num = ($page_num - 1) * $this->page_record_num;
		return $this->_view()->where(['m.unique_id'=>$message_unique_id])->order(['m.id'=>'desc'])->limit($this->page_record_num, $offset_num)->select();
	}
	
	/**
	 * public integer read(string $message_unique_id)
	 */
	public function read(string $message_unique_id): int {
		$this->clear_error();
		$v_record = $this->field(['message_num'=>['COUNT(*)'], 'status'])->table(['messages'])->where(['unique_id'=>$message_unique_id])->one();
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['message_num']) return 0;
			elseif(self::IS_READ == $v_record['status']) return -2; // Err : x, -)-
			$datas = ['read_time'=>['UNIX_TIMESTAMP()'], 'status'=>self::IS_READ];
			return $this->table(['messages'])->where(['unique_id'=>$message_unique_id])->edit(merge_time($datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer broadcast(integer $category_id, string $title, string $description)
	 */
	public function broadcast(int $category_id, string $title, string $description): int {
		$this->clear_error();
		$v_record = $this->field(['category_num'=>['COUNT(*)']])->table(['message_categories'])->where(['id'=>$category_id])->one();
		$user_ids = $this->field(['id'])->table(['users'])->order(['id'=>'asc'])->line('id');
		if($this->get_error() == self::ERR_NONE){
			if(0 == $v_record['category_num']) return -2; // Err : NO exist the category, -)-
			elseif($this->begin()){
				$end = true;
				$counter = 0;
				foreach($user_ids as $user_id){
					$unique_id = $this->unique_id();
					if($this->get_error() == self::ERR_NONE){
						$datas = ['unique_id'=>$unique_id, 'category_id'=>$category_id, 'user_id'=>$user_id, 'title'=>$title, 'description'=>$description];
						if($this->table(['messages'])->add(merge_time($datas)) > 0){
							$counter++;
							continue;
						}
					}
					$end = false;
					break;
				}
				if($end && $this->end()) return $counter;
				$this->rollback();
			}
		}
		return -1;
	}
	
	/**
	 * public integer clear_read_message(boolean $force = false)
	 */
	public function clear_read_message(bool $force = false): int {
		$this->clear_error();
		$max_read_keep_duration = $this->get_param(self::PARAM_MAX_READ_KEEP_DURATION);
		if($this->get_error() == self::ERR_NONE){
			if($force) return $this->table(['messages'])->where(['status'=>self::IS_READ])->delete();
			else return $this->table(['messages'])->where(['status'=>self::IS_READ, '(UNIX_TIMESTAMP()-`read_time`)'=>$max_read_keep_duration], ['eq', 'gr'])->delete();
		}
		return -1;
	}
	
	/**
	 * public array get_mining_years(void)
	 */
	public function get_mining_years(): array {
		return $this->field(['year'=>["FROM_UNIXTIME(`add_time`, '%Y')"]])->table(['messages'])->group(['year'])->order(['year'=>'asc'])->line('year');
	}
	
	/**
	 * public array get_mining_month_nums(string $year, ?string $status = null)
	 * @string $status = Message::IS_READ
	 */
	public function get_mining_month_nums(string $year, ?string $status = null): array {
		$yes_months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
		$month_exp = "FROM_UNIXTIME(`add_time`, '%m')";
		$year_exp = "FROM_UNIXTIME(`add_time`, '%Y')";
		if(is_null($status)) $mining_datas = $this->field(['num'=>['COUNT(*)'], 'month'=>[$month_exp]])->table(['messages'])->where([$year_exp=>$year])->group(['month'])->order(['month'=>'asc'])->line('num', 'month');
		elseif(self::IS_READ == $status) $mining_datas = $this->field(['num'=>['COUNT(*)'], 'month'=>[$month_exp]])->table(['messages'])->where([$year_exp=>$year, 'status'=>$status], ['eq', 'eq'])->group(['month'])->order(['month'=>'asc'])->line('num', 'month');
		foreach($yes_months as $month){
			$ends[$month] = $mining_datas[$month] ?? 0;
		}
		return $ends;
	}
	
	/**
	 * protected array get_category_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_category_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$message_nums = $this->get_category_message_nums();
		$read_message_nums = $this->get_category_message_nums(self::IS_READ);
		$func = function (&$record) use ($message_nums, $read_message_nums) {
			$record['message_num'] = $message_nums[$record['id']] ?? 0;
			$record['read_message_num'] = $read_message_nums[$record['id']] ?? 0;
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
	 * protected array get_category_message_nums(?string $status = null)
	 * @string $status = Message::IS_READ
	 */
	protected function get_category_message_nums(?string $status = null): array {
		if(is_null($status)) $ends = $this->field(['message_num'=>['COUNT(*)'], 'category_id'])->table(['messages'])->group(['category_id'])->order(['category_id'=>'asc'])->line('message_num', 'category_id');
		elseif(self::IS_READ) $ends = $this->field(['message_num'=>['COUNT(*)'], 'category_id'])->table(['messages'])->where(['status'=>self::IS_READ])->group(['category_id'])->order(['category_id'=>'asc'])->line('message_num', 'category_id');
		return $ends ?? [];
	}
	
	/**
	 * protected array replace_template_description(array $record, array $replace_datas)
	 */
	protected function replace_template_description(array $record, array $replace_datas): array {
		$pattern = '/\[##([a-z_]+)##\]/';
		$record['description'] = preg_replace_callback($pattern, function ($matchs) use ($replace_datas) {
			return $replace_datas[$matchs[1]] ?? $matchs[0];
		}, $record['description']);
		return $record;
	}
	
	/**
	 * private Message _category_view(void)
	 */
	private function _category_view(): Message {
		$mc_cols = ['id', 'code', 'name', 'description', 'last_edit_time', 'add_time'];
		$extra_cols = ['message_num'=>null, 'read_message_num'=>null];
		$this->field(array_merge($mc_cols, $extra_cols))->table(['message_categories']);
		return $this;
	}
	
	/**
	 * private Message _template_view(void)
	 */
	private function _template_view(): Message {
		$mt_cols = ['mt.id', 'mt.code', 'mt.name', 'mt.title', 'mt.description', 'mt.last_edit_time', 'mt.add_time'];
		$mc_cols = ['category_id'=>['mc.id'], 'category_code'=>['mc.code']];
		$this->field(array_merge($mt_cols, $mc_cols))->table(['mt'=>'message_templates']);
		$this->join(['mc'=>'message_categories', 'mt.category_id'=>'mc.id']);
		return $this;
	}
	
	/**
	 * private Message _view(void)
	 */
	private function _view(): Message {
		$m_cols = ['m.id', 'm.unique_id', 'm.title', 'm.description', 'm.read_time', 'm.status', 'm.last_edit_time', 'm.add_time'];
		$mc_cols = ['category_id'=>['mc.id'], 'category_code'=>['mc.code']];
		$u_cols = ['user_id'=>['u.id'], 'user_unique_id'=>['u.unique_id'], 'user_nickname'=>['u.nickname']];
		$this->field(array_merge($m_cols, $mc_cols, $u_cols))->table(['m'=>'messages']);
		$this->join(['mc'=>'message_categories', 'm.category_id'=>'mc.id']);
		$this->join(['u'=>'users', 'm.user_id'=>'u.id']);
		return $this;
	}
	// -- END --
}

