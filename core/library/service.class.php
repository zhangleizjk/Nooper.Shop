<?php
// declare(strict_types = 1);
namespace Nooper;

class Service {
	
	/**
	 * Properties
	 */
	protected $access_token;
	protected $urls = ['create_account'=>'https://api.weixin.qq.com/customservice/kfaccount/add', 'modify_account'=>'https://api.weixin.qq.com/customservice/kfaccount/update', 'delete_account'=>'https://api.weixin.qq.com/customservice/kfaccount/del', 'send_message'=>'https://api.weixin.qq.com/cgi-bin/message/custom/send'];
	
	/**
	 * public void function __construct(?string $token = null)
	 */
	public function __construct(string $token = null) {
		if(is_null($token)){
			$token = new Token();
			$this->access_token = $token->read();
		}else
			$this->access_token = $token;
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		// echo '- end -';
	}
	
	/**
	 * public boolean function add_account(array $datas)
	 */
	public function add_account(array $datas): bool {
		$keys = ['kf_account', 'nickname', 'password'];
		$url = $this->urls['create_account'] . '?access_token=' . $this->access_token;
		foreach($datas as $key => $data){
			if(!is_string($key) or !is_string($data)) return false;
			elseif(!in_array($key, $keys, true)) return false;
		}
		$helper = new Translator();
		$json = $helper->createJSON($datas);
		$mm = new Mimicry();
		$json = $mm->post($url, $json);
		$ends = $helper->parseJSON($json);
		return isset($ends['errcode']) && '0' == $ends['errocde'] ? true : false; 
	}
	
	/**
	 * public boolean function modify_account(array $datas)
	 * @ array $datas = [string $key => string $value]
	 */
	public function modify_account(array $datas): bool {
		$keys = ['kf_account', 'nickname', 'password'];
		$url = $this->urls['modify_account'] . '?access_token=' . $this->access_token;
		foreach($datas as $key => $data){
			if(!is_string($key) or !is_string($data)) return false;
			elseif(!in_array($key, $keys, true)) return false;
		}
		$helper = new Translator();
		$json = $helper->createJSON($datas);
		$mm = new Mimicry();
		$json = $mm->post($url, $json);
		$ends = $helper->parseJSON($json);
		return isset($ends['errcode']) && '0' == $ends['errocde'] ? true : false;
	}
	
	/**
	 * public boolean function delete_account(array $datas)
	 * @ array $datas = [string $key => string $value]
	 */
	public function delete_account(array $datas): bool {
		$keys = ['kf_account', 'nickname', 'password'];
		$url = $this->urls['delete_account'] . '?access_token=' . $this->access_token;
		foreach($datas as $key => $data){
			if(!is_string($key) or !is_string($data)) return false;
			elseif(!in_array($key, $keys, true)) return false;
		}
		$helper = new Translator();
		$json = $helper->createJSON($datas);
		$mm = new Mimicry();
		$json = $mm->post($url, $json);
		$ends = $helper->parseJSON($json);
		return isset($ends['errcode']) && '0' == $ends['errocde'] ? true : false;
	}
	
	/**
	 * public boolean function upload_account_portrait(string $account)
	 */
	public function upload_account_portrait(string $account): bool {
		$url = $this->urls['upload_account_portrait'] . '?access_token=' . $this->access_token.'&kf_account='.$account;
		//?
	}
	
	/**
	 * public ?array function get_account_list(void)
	 */
	public function get_account_list(): array {
		$url = $this->urls['get_account_list'] . '?access_token=' . $this->access_token;
		$helper = new Translator();
		$mm = new Mimicry();
		$json = $mm->get($url);
		$ends = $helper->parseJSON($json);
		return isset($ends['kf_list']) ? $ends : null;
	}
	
	/**
	 * public boolean send_text_message(string $user, string $message, ?string $account = null)
	 */
	public function send_text_message(string $user, string $message, string $account = null): bool {
		$url = $this->urls['send_message'] . '?access_token=' . $this->access_token;
		$datas = ['touser'=>$user, 'msgtype'=>'text', 'text'=>['content'=>$message]];
		if(is_string($account)) $datas['customservice'] = ['kf_account'=>$account];
		$helper = new Translator();
		$json = $helper->createJSON($datas);
		$mm = new Mimicry();
		$json = $mm->post($url, $json);
		$ends = $helper->parseJSON($json);
		return isset($ends['errcode']) && '0' == $ends['errocde'] ? true : false;
	}
	//
}

