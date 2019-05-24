<?php

// declare(strict_types = 1);
namespace Nooper;

class User {
	
	/**
	 * Properties
	 */
	protected $app_id;
	protected $app_secret;
	protected $access_token;
	protected $urls = ['create_user_tag'=>'https://api.weixin.qq.com/cgi-bin/tags/create', 'modify_user_tag'=>'https://api.weixin.qq.com/cgi-bin/tags/update', 'delete_user_tag'=>'https://api.weixin.qq.com/cgi-bin/tags/delete', 'get_user_tags'=>'https://api.weixin.qq.com/cgi-bin/tags/get', 'get_user_id'=>'https://api.weixin.qq.com/sns/oauth2/access_token', 'get_user_info'=>'https://api.weixin.qq.com/cgi-bin/user/info', 'get_users'=>'https://api.weixin.qq.com/cgi-bin/user/get', 'set_user_remark'=>'https://api.weixin.qq.com/cgi-bin/user/info/updateremark'];
	
	/**
	 * public void function __construct(void)
	 */
	public function __construct(string $app_id = null, string $app_secret = null, string $access_token = null) {
		$this->app_id = $app_id ?? get_config('app_id');
		$this->app_secret = $app_secret ?? get_config('app_secret');
		if(is_string($access_token)) $this->access_token = $access_token;
		else{
			$access_token = new Token();
			$this->access_token = $access_token->read();
		}
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		// echo '- end -';
	}
	
	/**
	 * public ?integer function create_user_tag(string $name)
	 */
	public function create_user_tag(string $name): int {
		$qry_params = ['access_token'=>$this->access_token];
		$datas = ['tag'=>['name'=>$name]];
		$ends = $this->send_post($this->urls['create_user_tag'], $datas, $qry_params);
		return $ends['tag']['id'] ?? null;
	}
	
	/**
	 * public boolean function modify_user_tag(integer $id, string $name)
	 */
	public function modify_user_tag(int $id, string $name): bool {
		$qry_params = ['access_token'=>$this->access_token];
		$datas = ['tag'=>['id'=>$id, 'name'=>$name]];
		$ends = $this->send_post($this->urls['modify_user_tag'], $datas, $qry_params);
		return 0 == $ends['errcode'] ? true : false;
	}
	
	/**
	 * public boolean function delete_user_tag(integer $id)
	 */
	public function delete_user_tag(int $id): bool {
		$qry_params = ['access_token'=>$this->access_token];
		$datas = ['tag'=>['id'=>$id]];
		$ends = $this->send_post($this->urls['delete_user_tag'], $datas, $qry_params);
		return 0 == $ends['errcode'] ? true : false;
	}
	
	/**
	 * public ?array function get_user_tags(void)
	 */
	public function get_user_tags(): array {
		$qry_params = ['access_token'=>$this->access_token];
		$end = $this->send_get($this->urls['get_user_tags'], $qry_params);
		return $end['tags'] ?? null;
	}
	
	/**
	 * public ?string function get_user_id(string $code)
	 */
	public function get_user_id(string $code): string {
		$qry_params = ['appid'=>$this->app_id, 'secret'=>'$this->app_secret', 'code'=>$code, 'grant_type'=>'authorization_code '];
		$ends = $this->send_get($this->urls['get_user_id'], $qry_params);
		return $ends['openid'] ?? null;
	}
	
	/**
	 * public ?array function get_user_info(string $open_id, ?string $language = null)
	 */
	public function get_user_info(string $open_id, string $language = null): array {
		$qry_params = ['access_token'=>$this->access_token, 'openid'=>$open_id];
		if(is_string($language)) $qry_params['lang'] = $language;
		$ends = $this->send_get($this->urls['get_user_info'], $qry_params);
		return isset($ends['openid']) ? $ends : null;
	}
	
	/**
	 * public ?array function get_users(?string $open_id = null)
	 */
	public function get_users(string $open_id = null): array {
		$qry_params = ['access_token'=>$this->access_token];
		if(!is_string($open_id)) $qry_params['openid'] = $open_id;
		$ends = $this->send_get($this->urls['get_users'], $qry_params);
		if(isset($ends['total'])){
			if(0 == $ends['count']) return [];
			$datas = $ends['data']['openid'];
			if($ends['next_openid'] != ''){
				$follow_datas = $this->get_users($ends['next_openid']);
				if(is_array($follow_datas)) $datas = array_merge($datas, $follow_datas);
			}
			return $datas;
		}
		return null;
	}
	
	/**
	 * public ?integer get_user_num(void)
	 */
	public function get_user_num(): int {
		$qry_params = ['access_token'=>$this->access_token];
		$ends = $this->send_get($this->urls['get_users'], $qry_params);
		return $ends['total'] ?? null;
	}
	
	/**
	 * public boolean function set_user_remark(string $open_id, string $remark)
	 */
	public function set_user_remark(string $open_id, string $remark): bool {
		$qry_params = ['access_token'=>$this->access_token];
		$datas = ['openid'=>$open_id, 'remark'=>$remark];
		$ends = $this->send_post($this->urls['set_user_remark'], $datas, $qry_params);
		return 0 == $ends['errcode'] ? true : false;
	}
	
	/**
	 * protected ?array function send_post(string $url, array $datas, array $qry_params = null)
	 */
	protected function send_post(string $url, array $datas, $qry_params = null): array {
		$mmc = new Mimicry();
		$helper = new Translator();
		$ends = $helper->parse_json($mmc->post($url, $helper->create_json($datas), $qry_params));
		return is_array($ends) ? $ends : null;
	}
	
	/**
	 * protected ?array function send_get(string $url, ?array $qry_params = null)
	 */
	protected function send_get(string $url, array $qry_params = null): array {
		$mmc = new Mimicry();
		$helper = new Translator();
		$ends = $helper->parse_json($mmc->get($url, $qry_params));
		return is_array($ends) ? $ends : null;
	}
	//
}

