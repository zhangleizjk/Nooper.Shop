<?php

// declare(strict_types = 1);
namespace Nooper;

use PDO;
use PDOException;

class Mysql {
	
	/**
	 * Constants
	 */
	protected const ERR_DATABASE_CONNECTION = -1001;
	protected const ERR_DATABASE_PREPARE = -1002;
	protected const ERR_DATABASE_SQL = -1003;
	protected const ERR_NONE = 0;
	
	/**
	 * Properties
	 */
	protected $id = 0;
	protected $sql;
	protected $error = self::ERR_NONE;
	protected $operator_type;
	protected $operator_id;
	protected $driver_options = [PDO::ATTR_CASE=>PDO::CASE_LOWER, PDO::ATTR_ERRMODE=>PDO::ERRMODE_SILENT, PDO::ATTR_ORACLE_NULLS=>PDO::NULL_NATURAL, PDO::ATTR_STRINGIFY_FETCHES=>false, PDO::ATTR_EMULATE_PREPARES=>false];
	protected $connect_params = [];
	protected $sql_cmds = ['distinct', 'field', 'table', 'join', 'where', 'group', 'having', 'order', 'limit'];
	protected $sql_datas = [];
	protected $database;
	protected $ds;
	
	/**
	 * public void __construct(string $operator_type, string $operator_id, ?array $connect_params = null)
	 */
	public function __construct(string $operator_type, string $operator_id, ?array $connect_params = null) {
		if(is_null($connect_params)) $this->connect_params = get_config('default_database_connect_params', []);
		elseif(is_database_connect_params($connect_params)) $this->connect_params = $connect_params;
		$this->operator_type = $operator_type;
		$this->operator_id = $operator_id;
	}
	
	/**
	 * public void __destruct(void)
	 */
	public function __destruct() {
		$this->close();
	}
	
	/**
	 * public ?string __get(string $cmd)
	 */
	public function __get(string $cmd): ?string {
		return $this->sql_datas[$cmd] ?? null;
	}
	
	/**
	 * public Mysql D(boolean $data)
	 */
	public function D(bool $data): Mysql {
		return $this->distinct($data);
	}
	
	/**
	 * public Mysql distinct(boolean $data)
	 */
	public function distinct(bool $data): Mysql {
		$this->sql('distinct', $data ? 'distinct' : 'all');
		return $this;
	}
	
	/**
	 * public Mysql distinct_cmd(string $data)
	 */
	public function distinct_cmd(string $data): Mysql {
		$this->sql('distinct', $data);
		return $this;
	}
	
	/**
	 * public Mysql F(array $datas)
	 * @array $datas = [(string $alias => ?(Scalar|array) $data)|(string $field),...]
	 * @array $data = [string $expr]
	 */
	public function F(array $datas): Mysql {
		return $this->field($datas);
	}
	
	/**
	 * public Mysql field(array $datas)
	 * @array $datas = [(string $alias => ?(Scalar|array) $data)|(string $field),...]
	 * @array $data = [string $expr]
	 */
	public function field(array $datas): Mysql {
		foreach($datas as $key => $data){
			if(is_integer($key) && is_database_named_str($data, true)) $ends[] = wrap_backquote($data);
			elseif(is_database_primary_named_str($key)){
				if(is_bool($data)) $data = $data ? '1' : '0';
				elseif(is_integer($data) or is_float($data)) $data = (string)$data;
				elseif(is_string($data)) $data = "'" . $data . "'";
				elseif(is_null($data)) $data = 'null';
				elseif(is_single_array($data)) $data = is_database_named_str($data[0]) ? wrap_backquote($data[0]) : $data[0];
				else continue;
				$ends[] = $data . ' as ' . wrap_backquote($key);
			}
		}
		if(isset($ends)) $this->sql('field', implode(',', $ends));
		return $this;
	}
	
	/**
	 * public Mysql field_cmd(string $data)
	 */
	public function field_cmd(string $data): Mysql {
		$this->sql('field', $data);
		return $this;
	}
	
	/**
	 * public Mysql T(array $datas)
	 * @array $datas = [(string $alias => string $memory)|(string $memory),...]
	 */
	public function T(array $datas): Mysql {
		return $this->table($datas);
	}
	
	/**
	 * public Mysql table(array $datas)
	 * @array $datas = [(string $alias => string $memory)|(string $memory),...]
	 */
	public function table(array $datas): Mysql {
		foreach($datas as $key => $data){
			if(is_database_primary_named_str($data)){
				if(is_integer($key)) $ends[] = wrap_backquote($data);
				elseif(is_database_primary_named_str($key)) $ends[] = wrap_backquote($data) . ' ' . wrap_backquote($key);
			}
		}
		if(isset($ends)) $this->sql('table', implode(',', $ends));
		return $this;
	}
	
	/**
	 * public Mysql table_cmd(string $data)
	 */
	public function table_cmd(string $data): Mysql {
		$this->sql('table', $data);
		return $this;
	}
	
	/**
	 * public Mysql J(array $datas, string $method = 'inner', string $compare = 'eq')
	 * @array $datas = [(string $alias => string $memory)|(string $memory), string $left_need => string $right_need]
	 * @string $method = 'inner|left|right'
	 * @string $compare = 'eq|neq|sm|gr'
	 */
	public function J(array $datas, string $method = 'inner', string $compare = 'eq'): Mysql {
		return $this->join($datas, $method, $compare);
	}
	
	/**
	 * public Mysql join(array $datas, string $method = 'inner', string $compare = 'eq')
	 * @array $datas = [(string $alias => string $memory)|(string $memory), string $left_need => string $right_need]
	 * @string $method = 'inner|left|right'
	 * @string $compare = 'eq|neq|sm|gr'
	 */
	public function join(array $datas, string $method = 'inner', string $compare = 'eq'): Mysql {
		if(count($datas) != 2) return $this;
		elseif(!in_array($method, ['inner', 'left', 'right'], true)) return $this;
		elseif(!in_array($compare, ['eq', 'neq', 'sm', 'gr'], true)) return $this;
		$method_maps = ['inner'=>'inner join', 'left'=>'left outer join', 'right'=>'right outer join'];
		$method = $method_maps[$method];
		$compare_maps = ['eq'=>'=', 'neq'=>'!=', 'sm'=>'<', 'gr'=>'>'];
		$compare = $compare_maps[$compare];
		list($keys, $values) = [array_keys($datas), array_values($datas)];
		if(is_database_primary_named_str($values[0])){
			if(is_database_primary_named_str($keys[0])) $memory = wrap_backquote($values[0]) . ' ' . wrap_backquote($keys[0]);
			elseif(is_integer($keys[0])) $memory = wrap_backquote($values[0]);
			else return $this;
		}else
			return $this;
		if(is_database_plus_named_str($keys[1])){
			if(is_database_plus_named_str($values[1])) $need = wrap_backquote($keys[1]) . $compare . wrap_backquote($values[1]);
			else return $this;
		}else
			return $this;
		$this->sql('join', implode(' ', [$this->join, $method, $memory, 'on', $need]));
		return $this;
	}
	
	/**
	 * public Mysql join_cmd(string $data)
	 */
	public function join_cmd(string $data): Mysql {
		$this->sql('join', $data);
		return $this;
	}
	
	/**
	 * public Mysql W(array $datas, array $compares = ['eq'], string $logic = 'and')
	 * @array $datas = [string $field => ?(Scalar|array) $data,...]
	 * @array $data = [string $expr]
	 * @array $compares = ['eq|neq|sm|gr|is|nis',...]
	 * @string $logic = 'and|or'
	 */
	public function W(array $datas, array $compares = ['eq'], string $logic = 'and'): Mysql {
		return $this->where($datas, $compares, $logic);
	}
	
	/**
	 * public Mysql where(array $datas, array $compares = ['eq'], string $logic = 'and')
	 * @array $datas = [string $field => ?(Scalar|array) $data,...]
	 * @array $data = [string $expr]
	 * @array $compares = ['eq|neq|sm|gr|is|nis|lk',...]
	 * @string $logic = 'and|or'
	 */
	public function where(array $datas, array $compares = ['eq'], string $logic = 'and'): Mysql {
		if(!in_array($logic, ['and', 'or'], true)) return $this;
		elseif(count($datas) != count($compares)) return $this;
		$compare_maps = ['eq'=>'=', 'neq'=>'!=', 'sm'=>'<', 'gr'=>'>', 'nis'=>' IS NOT ', 'is'=>' IS ', 'lk'=>' LIKE '];
		foreach($compares as &$compare){
			if(!in_array($compare, ['eq', 'neq', 'sm', 'gr', 'is', 'nis', 'lk'], true)) return $this;
			$compare = $compare_maps[$compare];
		}
		$compares = array_combine(array_keys($datas), $compares);
		foreach($datas as $key => $data){
			if(is_string($key)){
				$prefix = (is_database_named_str($key) ? wrap_backquote($key) : $key) . $compares[$key];
				if(is_bool($data)) $ends[] = $prefix . ($data ? '1' : '0');
				elseif(is_integer($data) or is_float($data)) $ends[] = $prefix . (string)$data;
				elseif(is_string($data)) $ends[] = $prefix . "'" . $data . "'";
				elseif(is_null($data)) $ends[] = $prefix . 'NULL';
				elseif(is_single_array($data)) $ends[] = $prefix . $data[0];
			}
		}
		if(isset($ends)) $this->sql('where', 'where ' . implode(' ' . $logic . ' ', $ends));
		return $this;
	}
	
	/**
	 * public Mysql where_cmd(string $data)
	 */
	public function where_cmd(string $data): Mysql {
		$this->sql('where', 'where ' . $data);
		return $this;
	}
	
	/**
	 * public Mysql G(array $datas)
	 * @array $datas = [string $field,...]
	 */
	public function G(array $datas): Mysql {
		return $this->group($datas);
	}
	
	/**
	 * public Mysql group(array $datas)
	 * @array $datas = [string $field,...]
	 */
	public function group(array $datas): Mysql {
		foreach($datas as $data){
			if(is_database_named_str($data)) $ends[] = wrap_backquote($data);
		}
		if(isset($ends)) $this->sql('group', 'group by ' . implode(',', $ends));
		return $this;
	}
	
	/**
	 * public Mysql group_cmd(string $data)
	 */
	public function group_cmd(string $data): Mysql {
		$this->sql('group', 'group by ' . $data);
		return $this;
	}
	
	/**
	 * public Mysql H(array $datas, array $compares = ['eq'], string $logic = 'and')
	 * @array $datas = [string $field => ?(Scalar|array) $data,...]
	 * @array $data = [string $expr]
	 * @array $compares = ['eq|neq|sm|gr|is|nis',...]
	 * @string $logic = 'and|or'
	 */
	public function H(array $datas, array $compares = ['eq'], string $logic = 'and'): Mysql {
		return $this->having($datas, $compares, $logic);
	}
	
	/**
	 * public Mysql having(array $datas, array $compares = ['eq'], string $logic = 'and')
	 * @array $datas = [string $field => ?(Scalar|array) $data,...]
	 * @array $data = [string $expr]
	 * @array $compares = ['eq|neq|sm|gr|is|nis',...]
	 * @string $logic = 'and|or'
	 */
	public function having(array $datas, array $compares = ['eq'], string $logic = 'and'): Mysql {
		if(!in_array($logic, ['and', 'or'], true)) return $this;
		elseif(count($datas) != count($compares)) return $this;
		$compare_maps = ['eq'=>'=', 'neq'=>'!=', 'sm'=>'<', 'gr'=>'>', 'nis'=>' IS NOT ', 'is'=>' IS '];
		foreach($compares as &$compare){
			if(!in_array($compare, ['eq', 'neq', 'sm', 'gr', 'is', 'nis'], true)) return $this;
			$compare = $compare_maps[$compare];
		}
		$compares = array_combine(array_keys($datas), $compares);
		foreach($datas as $key => $data){
			if(is_string($key)){
				$prefix = (is_database_named_str($key) ? wrap_backquote($key) : $key) . $compares[$key];
				if(is_bool($data)) $ends[] = $prefix . ($data ? '1' : '0');
				elseif(is_integer($data) or is_float($data)) $ends[] = $prefix . (string)$data;
				elseif(is_string($data)) $ends[] = $prefix . "'" . $data . "'";
				elseif(is_null($data)) $ends[] = $prefix . 'NULL';
				elseif(is_single_array($data)) $ends[] = $prefix . $data[0];
			}
		}
		if(isset($ends)) $this->sql('having', 'having ' . implode(' ' . $logic . ' ', $ends));
		return $this;
	}
	
	/**
	 * public Mysql having_cmd(string $data)
	 */
	public function having_cmd(string $data): Mysql {
		$this->sql('having', 'having ' . $data);
		return $this;
	}
	
	/**
	 * public Mysql O(array $datas)
	 * @array $datas = [(string $field => 'asc|desc')|(string $field),...]
	 */
	public function O(array $datas): Mysql {
		return $this->order($datas);
	}
	
	/**
	 * public Mysql order(array $datas)
	 * @array $datas = [(string $field => 'asc|desc')|(string $field),...]
	 */
	public function order(array $datas): Mysql {
		foreach($datas as $key => $data){
			if(is_database_named_str($key) && in_array($data, ['asc', 'desc'], true)) $ends[] = wrap_backquote($key) . ' ' . $data;
			elseif(is_integer($key) && is_database_named_str($data)) $ends[] = wrap_backquote($data);
		}
		if(isset($ends)) $this->sql('order', 'order by ' . implode(',', $ends));
		return $this;
	}
	
	/**
	 * public Mysql order_cmd(string $data)
	 */
	public function order_cmd(string $data): Mysql {
		$this->sql('order', 'order by ' . $data);
		return $this;
	}
	
	/**
	 * public Mysql L(integer $num, integer $offset = 0)
	 */
	public function L(int $num, int $offset = 0): Mysql {
		return $this->limit($num, $offset);
	}
	
	/**
	 * public Mysql limit(integer $num, integer $offset = 0)
	 */
	public function limit(int $num, int $offset = 0): Mysql {
		$this->sql('limit', 'limit ' . ($offset != 0 ? $offset . ',' . (string)$num : (string)$num));
		return $this;
	}
	
	/**
	 * public Mysql limit_cmd(string $data)
	 */
	public function limit_cmd(string $data): Mysql {
		$this->sql('limit', 'limit ' . $data);
		return $this;
	}
	
	/**
	 * public Mysql C(void)
	 */
	public function C(): Mysql {
		return $this->clear();
	}
	
	/**
	 * public Mysql clear(void)
	 */
	public function clear(): Mysql {
		$this->sql_datas = [];
		return $this;
	}
	
	/**
	 * public array S(void)
	 */
	public function S(): array {
		return $this->select();
	}
	
	/**
	 * public array select(void)
	 */
	public function select(): array {
		$sql_children = ['select', $this->distinct, $this->field, 'from', $this->table, $this->join, $this->where, $this->group, $this->having, $this->order, $this->limit];
		$sql = implode(' ', array_filter($sql_children, 'is_no_empty_str'));
		return $this->clear()->query($sql) ?? [];
	}
	
	/**
	 * public array pill(integer $page_num, integer $page_size)
	 */
	public function pill(int $page_num, int $page_size): array {
		return $this->limit($page_size, ($page_num - 1) * $page_size)->select();
	}
	
	/**
	 * public array one(void)
	 */
	public function one(): array {
		$this->limit(1);
		$sql_children = ['select', $this->distinct, $this->field, 'from', $this->table, $this->join, $this->where, $this->group, $this->having, $this->order, $this->limit];
		$sql = implode(' ', array_filter($sql_children, 'is_no_empty_str'));
		$records = $this->clear()->query($sql);
		return $records[0] ?? [];
	}
	
	/**
	 * public array line(string $data, ?string $key = null)
	 */
	public function line(string $data, ?string $key = null): array {
		$sql_children = ['select', $this->distinct, $this->field, 'from', $this->table, $this->join, $this->where, $this->group, $this->having, $this->order, $this->limit];
		$sql = implode(' ', array_filter($sql_children, 'is_no_empty_str'));
		$records = $this->clear()->query($sql);
		return $records ? array_column($records, $data, $key) : [];
	}
	
	/**
	 * public integer add(array $datas)
	 * @array $datas = [string $field => ?(Scalar|array) $data,...]
	 * @array $data = [string $expr]
	 */
	public function add(array $datas): int {
		$datas = $this->filter($datas);
		$keys_str = implode(',', array_keys($datas));
		$values_str = implode(',', array_values($datas));
		$sql_children = ['insert into', $this->table . '(' . $keys_str . ')', 'values(' . $values_str . ')'];
		$sql = implode(' ', array_filter($sql_children, 'is_no_empty_str'));
		return $this->clear()->cmd($sql, true) ?? -1;
	}
	
	/**
	 * public integer edit(array $datas)
	 * @array $datas = [string $field => ?(Scalar|array) $data,...]
	 * @array $data = [string $expr]
	 */
	public function edit(array $datas): int {
		$datas = $this->filter($datas);
		array_walk($datas, 'merge_key2data');
		$datas_str = implode(',', $datas);
		$sql_children = ['update', $this->table, 'set', $datas_str, $this->where, $this->order, $this->limit];
		$sql = implode(' ', array_filter($sql_children, 'is_no_empty_str'));
		return $this->clear()->cmd($sql) ?? -1;
	}
	
	/**
	 * public integer delete(void)
	 */
	public function delete(): int {
		$sql_children = ['delete from', $this->table, $this->where, $this->order, $this->limit];
		$sql = implode(' ', array_filter($sql_children, 'is_no_empty_str'));
		return $this->clear()->cmd($sql) ?? -1;
	}
	
	/**
	 * public ?integer cmd(string $sql, boolean $return_id = false)
	 */
	public function cmd(string $sql, bool $return_id = false): ?int {
		$this->sql = $sql;
		if($this->connect()){
			$logger = new Logger();
			$this->ds = $this->database->prepare($this->sql);
			if($this->ds){
				if($this->ds->execute()){
					$logger->write($this->operator_type, $this->operator_id, $sql, 'general');
					if($return_id) $this->id = (integer)$this->database->lastInsertId();
					return $this->ds->rowCount();
				}
				$this->error = self::ERR_DATABASE_SQL;
				$logger->write($this->operator_type, $this->operator_id, 'MySQL SQL Error: ' . $sql, 'error');
			}else{
				$this->error = self::ERR_DATABASE_PREPARE;
				$logger->write($this->operator_type, $this->operator_id, 'MySQL Prepare Error: ' . $sql, 'error');
			}
		}
		return null;
	}
	
	/**
	 * public ?array query(string $sql)
	 */
	public function query(string $sql): ?array {
		$this->sql = $sql;
		if($this->connect()){
			$logger = new Logger();
			$this->ds = $this->database->prepare($this->sql);
			if($this->ds){
				if($this->ds->execute()) return $this->ds->fetchAll(PDO::FETCH_ASSOC);
				$this->error = self::ERR_DATABASE_SQL;
				$logger->write($this->operator_type, $this->operator_id, 'MySQL SQL Error: ' . $sql, 'error');
			}else{
				$this->error = self::ERR_DATABASE_PREPARE;
				$logger->write($this->operator_type, $this->operator_id, 'MySQL Prepare Error: ' . $sql, 'error');
			}
		}
		return null;
	}
	
	/**
	 * public boolean begin(void)
	 */
	public function begin(): bool {
		if($this->connect() && !$this->database->inTransaction()) return $this->database->beginTransaction();
		return false;
	}
	
	/**
	 * public boolean end(void)
	 */
	public function end(): bool {
		if($this->connect() && $this->database->inTransaction()) return $this->database->commit();
		return false;
	}
	
	/**
	 * public boolean rollback(void)
	 */
	public function rollback(): bool {
		if($this->connect() && $this->database->inTransaction()) return $this->database->rollBack();
		return false;
	}
	
	/**
	 * public void clear_error(void)
	 */
	public function clear_error(): void {
		$this->error = self::ERR_NONE;
	}
	/**
	 * public integer get_error(void)
	 */
	public function get_error(): int {
		return $this->error;
	}
	
	/**
	 * public ?string get_last_sql(void)
	 */
	public function get_last_sql(): ?string {
		return $this->sql;
	}
	
	/**
	 * public integer get_last_id(void)
	 */
	public function get_last_id(): int {
		return $this->id;
	}
	
	/**
	 * protected array connector(void)
	 */
	protected function connector(): array {
		/* extract($this->connect_params); */
		list('protocol'=>$protocol, 'host'=>$host, 'port'=>$port, 'dbname'=>$dbname, 'charset'=>$charset) = $this->connect_params;
		list('username'=>$username, 'password'=>$password) = $this->connect_params;
		$dsn = implode(';', [$protocol . ':host=' . $host, 'port=' . $port, 'dbname=' . $dbname, 'charset=' . $charset]);
		return [$dsn, $username, $password];
	}
	
	/**
	 * protected boolean connect(void)
	 */
	protected function connect(): bool {
		if($this->database) return true;
		elseif(empty($this->connect_params)) return false;
		list($dsn, $username, $password) = $this->connector();
		try{
			$this->database = new PDO($dsn, $username, $password, $this->driver_options);
		}catch(PDOException $err){
			$this->error = self::ERR_DATABASE_CONNECTION;
			$logger = new Logger();
			$logger->write($this->operator_type, $this->operator_id, 'MySQL Connection Error', 'error');
			return false;
		}
		return true;
	}
	
	/**
	 * protected boolean sql(string $cmd, string $data)
	 */
	protected function sql(string $cmd, string $data): bool {
		if(in_array($cmd, $this->sql_cmds, true)){
			$this->sql_datas[$cmd] = $data;
			return true;
		}
		return false;
	}
	
	/**
	 * protected array filter(array $datas)
	 * @array $datas = [string $field => ?(Scalar|array) $data,...]
	 * @array $data = [string $expr]
	 */
	protected function filter(array $datas): array {
		foreach($datas as $field => $data){
			if(is_database_named_str($field)){
				if(is_bool($data)) $data = $data ? '1' : '0';
				elseif(is_integer($data) or is_float($data)) $data = (string)$data;
				elseif(is_string($data)) $data = "'" . $data . "'";
				elseif(is_null($data)) $data = 'NULL';
				elseif(is_single_array($data)) $data = $data[0];
				else continue;
				$ends[wrap_backquote($field)] = $data;
			}
		}
		return $ends ?? [];
	}
	
	/**
	 * protected void close(void)
	 */
	protected function close(): void {
		$this->free();
		$this->database = null;
	}
	
	/**
	 * protected void free(void)
	 */
	protected function free(): void {
		$this->ds = null;
	}
	// -- END --
}

