<?php

// declare(strict_types = 1);
namespace Nooper;

class Menu {
	
	/**
	 * Constants
	 */
	public const VIEW = 'view';
	public const CLICK_CUSTOM = 'click';
	public const CLICK_SCAN_PUSH = 'scancode_push';
	public const CLICK_SCAN_MESSAGE = 'scancode_waitmsg';
	public const CLICK_CAREMA = 'pic_sysphoto';
	public const CLICK_CAREMA_OR_PHOTO = 'pic_photo_or_album';
	public const CLICK_WEIXIN_PHOTO = 'pic_weixin';
	public const CLICK_LOCATION = 'location_select';
	public const MINIPROGRAM = 'miniprogram';
	public const MEDIA_ID = 'media_id';
	public const MEDIA_LIMITED_ID = 'view_limited';
	
	/**
	 * Properties
	 */
	protected $token;
	protected $bulider = ['button'=>[]];
	protected $operate_urls = ['create'=>'https://api.weixin.qq.com/cgi-bin/menu/create', 'delete'=>'https://api.weixin.qq.com/cgi-bin/menu/delete', 'get'=>'https://api.weixin.qq.com/cgi-bin/menu/get'];

	/*
	 * protected $operate_urls = [
	 * 'create'=>'https://api.weixin.qq.com/cgi-bin/menu/create',
	 * 'delete'=>'https://api.weixin.qq.com/cgi-bin/menu/delete',
	 * 'get'=>'https://api.weixin.qq.com/cgi-bin/menu/get'
	 * ];
	 */
	
	/**
	 * public void __construct(string $token = null)
	 */
	public function __construct(string $token = null) {
		$this->token = $token ?? (new Token())->read();
	}
	
	/**
	 * public ?array get(void)
	 */
	public function get(): ?array {
		$mmc = new Mimicry();
		$helper = new Translator();
		$qry_params = ['access_token'=>$this->token];
		try{
			$end_datas = $helper->parse_json($mmc->get($this->operate_urls['get'], $qry_params));
		}catch(\Exception $err){
			return null; // Err : Mimicry or Translator error, -)-
		}
		return $end_datas ?? [];
	}
	
	/**
	 * public integer function create(void)
	 */
	public function create(): int {
		$mmc = new Mimicry();
		$helper = new Translator();
		$json = $helper->create_json($this->bulider);
		if(is_string($json)){
			$qry_params = ['access_token'=>$this->token];
			try{
				$end_datas = $helper->parse_json($mmc->post($this->operate_urls['create'], $json, $qry_params));
				return $end_datas['errcode'] ?? -4; // Err : x, -)-
			}catch(\Exception $err){
				return -3; // Err : Mimicry or Translator error, -)-
			}
		}
		return -2; // Err : x, -)-
	}
	
	/**
	 * public integer delete(void)
	 */
	public function delete(): int {
		$mmc = new Mimicry();
		$helper = new Translator();
		$qry_params = ['access_token'=>$this->token];
		try{
			$end_datas = $helper->parse_json($mmc->get($this->urls['delete'], $qry_params));
			return $end_datas['errcode'] ?? -4; // Err : x, -)-
		}catch(\Exception $err){
			return -3; // Err : Mimicry or Translator error, -)-
		}
	}
	
	/**
	 * public array create_group(string $name)
	 */
	public function create_group(string $name): array {
		return ['name'=>$name, 'sub_button'=>[]];
	}
	
	/**
	 * public array create_view(string $name, string $url, ?array &$group = null)
	 */
	public function create_view(string $name, string $url, ?array &$group = null): array {
		$view = ['type'=>'view', 'name'=>$name, 'url'=>$url];
		if(isset($group['sub_button']) && is_array($group['sub_button'])){
			$group['sub_button'][] = $view;
		}
		return $view;
	}
	
	/**
	 * public array create_click(string $name, string $key, string $type = self::CLICK_CUSTOM, ?array &$group = null)
	 */
	public function create_click(string $name, string $key, string $type = self::CLICK_CUSTOM, ?array &$group = null): array {
		$yes_click_types = [self::CLICK_CUSTOM, self::CLICK_SCAN_PUSH, self::CLICK_SCAN_MESSAGE, self::CLICK_CAREMA, self::CLICK_CAREMA_OR_PHOTO, self::CLICK_WEIXIN_PHOTO, self::CLICK_LOCATION];
		if(in_array($type, $yes_click_types, true)){
			$click = ['type'=>$type, 'name'=>$name, 'key'=>$key];
			if(isset($group['sub_button']) && is_array($group['sub_button'])){
				$group['sub_button'][] = $click;
			}
			return $click;
		}
		return [];
	}
	
	/**
	 * public array create_miniprogram(string $name, string $url, string $app_id, string $page_path, ?array &$group = null)
	 */
	public function create_miniprogram(string $name, string $url, string $app_id, string $page_path, ?array &$group = null): array {
		$program = ['type'=>self::MINIPROGRAM, 'name'=>$name, 'url'=>$url, 'appid'=>$app_id, 'pagepath'=>$page_path];
		if(isset($group['sub_button']) && is_array($group['sub_button'])){
			$group['sub_button'][] = $program;
		}
		return $program;
	}
	
	/**
	 * public array create_media(string $name, string $id, string $type = self::MEDIA_ID, ?array &$group = null)
	 */
	public function create_media(string $name, string $id, string $type = self::MEDIA_ID, ?array &$group = null): array {
		$media = ['type'=>$type, 'name'=>$name, 'media_id'=>$id];
		if(isset($group['sub_button']) && is_array($group['sub_button'])){
			$group['sub_button'][] = $media;
		}
		return $media;
	}
	
	/**
	 * public ?string get_builder_json(void)
	 */
	public function get_builder_json(): ?string {
		return (new Translator())->create_json($this->bulider);
	}
	
	/**
	 * public array get_builder(void)
	 */
	public function get_builder(): array {
		return $this->builder;
	}
	
	/**
	 * public Menu add(array $menu)
	 */
	public function add(array $menu): Menu {
		$this->builder['button'][] = $menu;
		return $this;
	}
	// -- END --
}

