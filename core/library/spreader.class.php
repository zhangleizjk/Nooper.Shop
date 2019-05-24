<?php
// declare(strict_types = 1);
namespace Nooper;

use Exception;

class Spreader {
	
	/**
	 * Properties
	 */
	protected $token;
	protected $operate_urls = ['create_qrcode'=>'https://api.weixin.qq.com/cgi-bin/qrcode/create', 'display_qrcode_image'=>'https://mp.weixin.qq.com/cgi-bin/showqrcode'];
	
	/**
	 * public void function __construct(?string $access_token = null)
	 */
	public function __construct(string $access_token = null) {
		if(is_string($access_token)) $this->token = $access_token;
		else (new Token())->read();
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		// echo '- end -';
	}
	
	/**
	 * public ?array function create_qrcode(integer $seconds, integer $scene_id)
	 */
	public function create_qrcode(int $seconds, int $scene_id): array {
		$qry_params = ['access_token'=>$this->token];
		$datas = ['action_name'=>'QR_SCENE', 'action_info'=>['scene'=>['scene_id'=>$scene_id]], 'expire_seconds'=>$seconds];
		return $this->send($this->operate_urls['create_qrcode'], $datas, $qry_params);
	}
	
	/**
	 * public ?array function create_limited_qrcode(integer $scene_id)
	 */
	public function create_limited_qrcode(int $scene_id): array {
		$qry_params = ['access_token'=>$this->token];
		$datas = ['action_name'=>'QR_LIMIT_SCENE', 'action_info'=>['scene'=>['scene_id'=>$scene_id]]];
		return $this->send($this->operate_urls['create_qrcode'], $datas, $qry_params);
	}
	
	/**
	 * public ?array function create_limited_str_qrcode(string $scene_str)
	 */
	public function create_limited_str_qrcode(string $scene_str): array {
		$qry_params = ['access_token'=>$this->token];
		$datas = ['action_name'=>'QR_LIMIT_STR_SCENE', 'action_info'=>['scene'=>['scene_str'=>$scene_str]]];
		return $this->send($this->operate_urls['create_qrcode'], $datas, $qry_params);
	}
	
	/**
	 * public string function display_qrcode_image(string $ticket)
	 */
	public function display_qrcode_image(string $ticket): string {
		return $this->operate_urls['display_qrcode_image'] . '?ticket=' . rawurldecode($ticket);
	}
	
	/**
	 * public void function download_qrcode_image(string $ticket, string $file_name)
	 */
	public function download_qrcode_image(string $ticket, string $file_name): void {
		header_download('image/jpeg', $file_name);
		$mmc = new Mimicry();
		$qry_params = ['ticket'=>rawurlencode($ticket)];
		echo $mmc->get($this->operate_urls['display_qrcode_image'], $qry_params);
	}
	
	/**
	 * protected ?array function send(string $url, array $datas, array $qry_params)
	 */
	protected function send(string $url, array $datas, array $qry_params): array {
		$mmc = new Mimicry();
		$helper = new Translator();
		$json_data = $helper->create_json($datas);
		if(is_string($json_data)){
			try{
				return $helper->parse_json($mmc->post($url, $json_data, $qry_params));
			}catch(\Exception $err){
				return null;
			}
		}
		return null;
	}
	//
}

