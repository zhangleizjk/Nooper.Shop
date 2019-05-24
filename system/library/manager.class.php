<?php

// declare(strict_types = 1);
namespace NooperShop;

use Nooper\Mysql;

class Manager extends Mysql {
	
	/**
	 * Constants
	 */
	public const PARAM_MAX_MANAGER_NUM = 'max_manager_num';
	
	/**
	 * public ?Number|string get_param(string $param_name)
	 */
	public function get_param(string $param_name) {
		$record = $this->field(['*'])->table(['manager_default_params'])->where(['id'=>1])->one();
		return $record[$param_name] ?? null;
	}
	
	/**
	 * public integer get_permission_num(void)
	 */
	public function get_permission_num(): int {
		$record = $this->field(['permission_num'=>['COUNT(*)']])->table(['manager_permissions'])->one();
		return $record['permission_num'] ?? 0;
	}
	
	/**
	 * public array get_permissions(void)
	 */
	public function get_permissions(): array {
		return $this->_permission_view()->order(['id'=>'asc'])->line('code', 'id');
	}
	
	/**
	 * public array get_permission_page(integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_permission_page(int $page_num = 1, int $page_size = 20): array {
		$records = $this->_permission_view()->order(['id'=>'asc'])->pill($page_num, $page_size);
		return $this->get_permission_extra_datas($records);
	}
	
	/**
	 * public array get_permission_record(integer $permission_id)
	 */
	public function get_permission_record(int $permission_id): array {
		$record = $this->_permission_view()->where(['id'=>$permission_id])->one();
		return $this->get_permission_extra_datas($record, false);
	}
	
	/**
	 * public integer get_permission_role_num(integer $permission_id)
	 */
	public function get_permission_role_num(int $permission_id): int {
		$record = $this->field(['role_num'=>['COUNT(*)']])->table(['manager_role_permissions'])->where(['permission_id'=>$permission_id])->one();
		return $record['role_num'] ?? 0;
	}
	
	/**
	 * public array get_permission_role_page(integer $permission_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_permission_role_page(int $permission_id, int $page_num = 1, int $page_size = 20): array {
		return $this->_permission_role_view()->where(['mrp.permission_id'=>$permission_id])->order(['mr.id'=>'asc'])->pill($page_num, $page_size);
	}
	
	/**
	 * public integer get_permission_manager_num(integer $permission_id)
	 */
	public function get_permission_manager_num(int $permission_id): int {
		$record = $this->field(['manager_num'=>['COUNT(*)']])->table(['m'=>'managers'])->join(['mrp'=>'manager_role_permissions', 'm.role_id'=>'mrp.role_id'])->where(['mrp.permission_id'=>$permission_id])->one();
		return $record['manager_num'] ?? 0;
	}
	
	/**
	 * public array get_permission_manager_page(integer $permission_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_permission_manager_page(int $permission_id, int $page_num = 1, int $page_size = 20): array {
		return $this->_permission_manager_view()->where(['mrp.permission_id'=>$permission_id])->order(['m.id'=>'asc'])->pill($page_num, $page_size);
	}
	
	/**
	 * public integer get_role_num(void)
	 */
	public function get_role_num(): int {
		$record = $this->field(['role_num'=>['COUNT(*)']])->table(['manager_roles'])->one();
		return $record['role_num'] ?? 0;
	}
	
	/**
	 * public array get_roles(void)
	 */
	public function get_roles(): array {
		return $this->_role_view()->order(['id'=>'asc'])->line('code', 'id');
	}
	
	/**
	 * public array get_role_page(integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_role_page(int $page_num = 1, int $page_size = 20): array {
		$records = $this->_role_view()->order(['id'=>'asc'])->pill($page_num, $page_size);
		return $this->get_role_extra_datas($records);
	}
	
	/**
	 * public array get_role_record(integer $role_id)
	 */
	public function get_role_record(int $role_id): array {
		$record = $this->_role_view()->where(['id'=>$role_id])->one();
		return $this->get_role_extra_datas($record, false);
	}
	
	/**
	 * public integer delete_role(integer $role_id)
	 */
	public function delete_role(int $role_id): int {
		if(1 == $role_id) return -2; // Err : CAN'T delete the system-admin role, -)-
		elseif($this->begin()){
			$end1 = $this->table(['manager_role_permissions'])->where(['role_id'=>$role_id])->delete();
			$end2 = $this->table(['manager_roles'])->where(['id'=>$role_id])->delete();
			if($end1 >= 0 && $end2 >= 0 && $this->end()) return $end2;
			$this->rollback();
		}
		return -1;
	}
	
	/**
	 * public integer edit_role(integer $role_id, string $code, string $name, string $description)
	 */
	public function edit_role(int $role_id, string $code, string $name, string $description): int {
		$this->clear_error();
		if(1 == $role_id) return -2; // Err : CAN'T edit the system-admin role, -)-
		$v_record = $this->field(['role_num'=>['COUNT(*)']])->table(['manager_roles'])->where(['id'=>$role_id, 'code'=>$code], ['neq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['role_num'] > 0) return -2; // Err : x, -)-
			$role_datas = ['code'=>$code, 'name'=>$name, 'description'=>$description];
			return $this->table(['manager_roles'])->where(['id'=>$role_id])->edit(merge_time($role_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer add_role(string $code, string $name, string $description)
	 */
	public function add_role(string $code, string $name, string $description): int {
		$this->clear_error();
		$v_record = $this->field(['role_num'=>['COUNT(*)']])->table(['manager_roles'])->where(['code'=>$code])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['role_num'] > 0) return -2; // Err : x, -)-
			$role_datas = ['code'=>$code, 'name'=>$name, 'description'=>$description];
			if($this->table(['manager_roles'])->add(merge_time($role_datas)) > 0) return $this->get_last_id();
		}
		return -1;
	}
	
	/**
	 * public array get_role_permissions(integer $role_id)
	 */
	public function get_role_permissions(int $role_id): array {
		return $this->_role_permission_view()->where(['mrp.role_id'=>$role_id])->order(['mp.id'=>'asc'])->line('code', 'id');
	}
	
	/**
	 * public integer delete_role_permission(integer $role_id, integer $permission_id)
	 */
	public function delete_role_permission(int $role_id, int $permission_id): int {
		if(1 == $role_id) return -2; // Err : CAN'T edit system-admin role, -)-
		return $this->table(['manager_role_permissions'])->where(['role_id'=>$role_id, 'permission_id'=>$permission_id], ['eq', 'eq'])->delete();
	}
	
	/**
	 * public integer add_role_permission(integer $role_id, integer $permission_id)
	 */
	public function add_role_permission(int $role_id, int $permission_id): int {
		if(1 == $role_id) return -2; // Err : CAN'T edit system-admin role, -)-
		elseif(1 == $permission_id) return -3; // Err : CAN'T add system-permission, -)-
		$permission_datas = ['role_id'=>$role_id, 'permission_id'=>$permission_id];
		if($this->table(['manager_role_permissions'])->add(merge_time($permission_datas)) > 0) return $this->get_last_id();
		return -1;
	}
	
	/**
	 * public integer get_role_manager_num(integer $role_id)
	 */
	public function get_role_manager_num(int $role_id): int {
		$record = $this->field(['manager_num'=>['COUNT(*)']])->table(['managers'])->where(['role_id'=>$role_id])->one();
		return $record['manager_num'] ?? 0;
	}
	
	/**
	 * public array get_role_manager_page(integer $role_id, integer $page_num = 1, integer $page_size = 20)
	 */
	public function get_role_manager_page(int $role_id, int $page_num = 1, int $page_size = 20): array {
		return $this->_role_manager_view()->where(['mr.id'=>$role_id])->order(['m.id'=>'asc'])->pill($page_num, $page_size);
	}
	
	/**
	 * public integer num(void)
	 */
	public function num(): int {
		$record = $this->field(['manager_num'=>['COUNT(*)']])->table(['managers'])->one();
		return $record['manager_num'] ?? 0;
	}
	
	/**
	 * public array page(integer $page_num = 1, integer $page_size = 20)
	 */
	public function page(int $page_num = 1, int $page_size = 20): array {
		return $this->_view()->order(['m.id'=>'asc'])->pill($page_num, $page_size);
	}
	
	/**
	 * public array record(integer $manager_id)
	 */
	public function record(int $manager_id): array {
		$record = $this->_view()->where(['m.id'=>$manager_id])->one();
		return $this->get_manager_extra_datas($record, false);
	}
	
	/**
	 * public integer remove(integer $manager_id)
	 */
	public function remove(int $manager_id): int {
		if(1 == $manager_id) return -2; // Err : CAN'T delete the system-admin manager, -)-
		return $this->table(['managers'])->where(['id'=>$manager_id])->delete();
	}
	
	/**
	 * public integer change(integer $manager_id, integer $role_id, string $name, string $phone)
	 */
	public function change(int $manager_id, int $role_id, string $name, string $phone): int {
		$this->clear_error();
		if(1 == $manager_id) return -2; // Err : CAN'T edit the system-admin manager, -)-
		$v_record = $this->field(['manager_num'=>['COUNT(*)']])->table(['managers'])->where(['id'=>$manager_id, 'name'=>$name], ['neq', 'eq'])->one();
		if($this->get_error() == self::ERR_NONE){
			if($v_record['manager_num'] > 0) return -3; // Err : x, -)-
			$manager_datas = ['role_id'=>$role_id, 'name'=>$name, 'phone'=>$phone];
			return $this->table(['managers'])->where(['id'=>$manager_id])->edit(merge_time($manager_datas, false));
		}
		return -1;
	}
	
	/**
	 * public integer create(integer $role_id, string $name, string $phone, string $pwd)
	 */
	public function create(int $role_id, string $name, string $phone, string $pwd): int {
		$this->clear_error();
		if(1 == $role_id) return -2; // Err : CAN'T add the system-admin manager, -)-
		$max_manager_num = $this->get_param(self::PARAM_MAX_MANAGER_NUM);
		$manager_num = $this->num();
		$v_record = $this->field(['manager_num'=>['COUNT(*)']])->table(['managers'])->where(['name'=>$name])->one();
		if($this->get_error() == self::ERR_NONE){
			if($manager_num >= $max_manager_num) return -3; // Err : Param_Max_Manager_Num, -)-
			elseif($v_record['manager_num'] > 0) return -4; // Err : x, -)-
			$manager_datas = ['role_id'=>$role_id, 'name'=>$name, 'phone'=>$phone, 'pwd'=>["SHA2('" . $pwd . "', 512)"]];
			if($this->table(['managers'])->add(merge_time($manager_datas)) > 0) return $this->get_last_id();
		}
		return -1;
	}
	
	/**
	 * public integer password(integer $manager_id, string $pwd)
	 */
	public function password(int $manager_id, string $pwd): int {
		return $this->table(['managers'])->where(['id'=>$manager_id])->edit(merge_time(['pwd'=>["SHA2('" . $pwd . "', 512)"]], false));
	}
	
	/**
	 * public array login(string $name, string $pwd)
	 */
	public function login(string $name, string $pwd): array {
		$record = $this->field(['id'])->table(['managers'])->where(['name'=>$name, 'pwd'=>["SHA2('" . $pwd . "', 512)"]], ['eq', 'eq'])->one();
		return $record ? $this->record($record['id']) : [];
	}
	
	/**
	 * public string get_rand_pwd(void)
	 */
	public function get_rand_pwd(): string {
		return (new Unique())->password();
	}
	
	/**
	 * protected array get_permission_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_permission_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$role_nums = $this->get_permission_role_nums();
		$manager_nums = $this->get_permission_manager_nums();
		$func = function (&$record) use ($role_nums, $manager_nums) {
			$record['role_num'] = $role_nums[$record['id']] ?? 0;
			$record['manager_num'] = $manager_nums[$record['id']] ?? 0;
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
	 * protected array get_permission_role_nums(void)
	 */
	protected function get_permission_role_nums(): array {
		return $this->field(['role_num'=>['COUNT(*)'], 'permission_id'])->table(['manager_role_permissions'])->group(['permission_id'])->order(['permission_id'=>'asc'])->line('role_num', 'permission_id');
	}
	
	/**
	 * protected array get_permission_manager_nums(void)
	 */
	protected function get_permission_manager_nums(): array {
		return $this->field(['manager_num'=>['COUNT(*)'], 'permission_id'])->table(['mrp'=>'manager_role_permissions'])->join(['m'=>'managers', 'mrp.role_id'=>'m.role_id'])->group(['mrp.permission_id'])->order(['mrp.permission_id'=>'asc'])->line('manager_num', 'permission_id');
	}
	
	/**
	 * protected array get_role_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_role_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$manager_nums = $this->get_role_manager_nums();
		$func = function (&$record) use ($manager_nums) {
			$record['manager_num'] = $manager_nums[$record['id']] ?? 0;
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
	 * protected array get_role_manager_nums(void)
	 */
	protected function get_role_manager_nums(): array {
		return $this->field(['manager_num'=>['COUNT(*)'], 'role_id'])->table(['managers'])->group(['role_id'])->order(['role_id'=>'asc'])->line('manager_num', 'role_id');
	}
	
	/**
	 * protected array get_manager_extra_datas(array $datas, boolean $many = true)
	 */
	protected function get_manager_extra_datas(array $datas, bool $many = true): array {
		if(empty($datas)) return [];
		$func = function (&$record) {
			$record['permissions'] = $this->get_role_permissions($record['role_id']);
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
	 * private Manager _permission_view(void)
	 */
	private function _permission_view(): Manager {
		$mp_cols = ['id', 'code', 'name', 'description', 'last_edit_time', 'add_time'];
		$extra_cols = ['role_num'=>null, 'manager_num'=>null];
		$this->field($mp_cols, $extra_cols)->table(['manager_permissions']);
		return $this;
	}
	
	/**
	 * private Manager _permission_role_view(void)
	 */
	private function _permission_role_view(): Manager {
		$mr_cols = ['mr.id', 'mr.code', 'mr.name', 'mr.description', 'mr.last_edit_time', 'mr.add_time'];
		$this->field($mr_cols)->table(['mr'=>'manager_roles']);
		$this->join(['mrp'=>'manager_role_permissions', 'mr.id'=>'mrp.role_id']);
		return $this;
	}
	
	/**
	 * private Manager _permission_manager_view(void)
	 */
	private function _permission_manager_view(): Manager {
		$m_cols = ['m.id', 'm.name', 'm.phone', 'm.last_edit_time', 'm.add_time'];
		$mr_cols = ['role_id'=>['mr.id'], 'role_code'=>['mr.code']];
		$this->field(array_merge($m_cols, $mr_cols))->table(['m'=>'managers']);
		$this->join(['mr'=>'manager_roles', 'm.role_id'=>'mr.id']);
		$this->join(['mrp'=>'manager_role_permissions', 'mr.id'=>'mrp.role_id']);
		return $this;
	}
	
	/**
	 * private Manager _role_view(void)
	 */
	private function _role_view(): Manager {
		$mr_cols = ['id', 'code', 'name', 'description', 'last_edit_time', 'add_time'];
		$extra_cols = ['manager_num'=>null];
		$this->field(array_merge($mr_cols, $extra_cols))->table(['manager_roles']);
		return $this;
	}
	
	/**
	 * private Manager _role_permission_view(void)
	 */
	private function _role_permission_view(): Manager {
		$mp_cols = ['mp.id', 'mp.code', 'mp.name', 'mp.description', 'mp.last_edit_time', 'mp.add_time'];
		$this->field($mp_cols)->table(['mp'=>'manager_permissions']);
		$this->join(['mrp'=>'manager_role_permissions', 'mp.id'=>'mrp.permission_id']);
		return $this;
	}
	
	/**
	 * private Manager _role_manger_view(void)
	 */
	private function _role_manager_view(): Manager {
		$m_cols = ['m.id', 'm.name', 'm.phone', 'm.last_edit_time', 'm.add_time'];
		$mr_cols = ['role_id'=>['mr.id'], 'role_code'=>['mr.code']];
		$this->field(array_merge($m_cols, $mr_cols))->table(['m'=>'managers']);
		$this->join(['mr'=>'manager_roles', 'm.role_id'=>'mr.id']);
		return $this;
	}
	
	/**
	 * private Manager _view(void)
	 */
	private function _view(): Manager {
		$m_cols = ['m.id', 'm.name', 'm.phone', 'm.last_edit_time', 'm.add_time'];
		$mr_cols = ['role_id'=>['mr.id'], 'role_code'=>['mr.code']];
		$extra_cols = ['permissions'=>null];
		$this->field(array_merge($m_cols, $mr_cols, $extra_cols))->table(['m'=>'managers']);
		$this->join(['mr'=>'manager_roles', 'm.role_id'=>'mr.id']);
		return $this;
	}
	// -- Version : 0.99-Beta [2019-05-09] --
	// -- END --
}

