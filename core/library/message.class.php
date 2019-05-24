<?php
// declare(strict_types = 1);
namespace Nooper;

class Message {
	
	/**
	 * Properties
	 */
	
	/**
	 * public void function __construct(void)
	 */
	public function __construct() {
		// echo '- begin -';
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		// echo '- end -';
	}
	
	/**
	 * public void function send_empty_message(void)
	 */
	public function send_empty_message(): void {
		die('success');
	}
	
	/**
	 * public void function send_text_message(array $datas)
	 */
	public function send_text_message(array $datas): void {
		$message_keys = ['to_user_name', 'from_user_name', 'create_time', 'msg_type', 'content'];
		if(count($datas) != count($message_keys)) $this->send_empty_message();
		foreach($datas as $key => $data){
			if(!in_array($key, $message_keys, true)) $this->send_empty_message();
			elseif(!is_string($data)) $this->send_empty_message();
		}
		if($datas['msg_type'] != 'text') $this->send_empty_message();
		$datas = $this->underline2pascal($datas);
		$helper = new Translator();
		$xml = $helper->createXML($datas);
		$this->header('text/xml');
		die($xml);
	}
	
	/**
	 * public ?array function get_message(void)
	 */
	public function get_message(): array {
		$datas = $this->get_message_datas();
		if(is_null($datas)) return null;
		$type = $this->get_message_type($datas);
		if(is_null($type)) return null;
		return ['type'=>$type, 'datas'=>$datas];
	}
	
	/**
	 * protected ?array function get_message_datas(void)
	 */
	protected function get_message_datas(): array {
		$xml = file_get_contents('php://input');
		$helper = new Translator();
		$datas = $helper->parseXML($xml);
		return is_array($datas) ? $this->pascal2underline($datas) : null;
	}
	
	/**
	 * protected ?string function get_message_type(array $datas)
	 */
	protected function get_message_type(array $datas): string {
		if($this->is_user_text_message($datas)) return 'user.text';
		elseif($this->is_event_subscribe_message($datas)) return 'event.subscribe';
		elseif($this->is_event_unsubscribe_message($datas)) return 'event.unsubscribe';
		elseif($this->is_event_subscribe_qrscene_message($datas)) return 'event.subscribe.qrscene';
		elseif($this->is_event_scan_qrscene_message($datas)) return 'event.scan.qrscene';
		elseif($this->is_event_click_message($datas)) return 'event.click';
		elseif($this->is_event_view_message($datas)) return 'event.view';
		return null;
	}
	
	/**
	 * protected boolean function is_user_text_message(array $datas)
	 */
	protected function is_user_text_message(array $datas): bool {
		$message_keys = ['to_user_name', 'from_user_name', 'create_time', 'msg_type', 'content', 'msg_id'];
		if(count($datas) != count($message_keys)) return false;
		foreach($datas as $key => $data){
			if(!in_array($key, $message_keys, true)) return false;
			elseif(!is_string($data)) return false;
		}
		return 'text' == $datas['msg_type'] ? true : false;
	}
	
	/**
	 * protected boolean function is_event_subscribe_message(array $datas)
	 */
	protected function is_event_subscribe_message(array $datas): bool {
		$message_keys = ['to_user_name', 'from_user_name', 'create_time', 'msg_type', 'event'];
		if(count($datas) != count($message_keys)) return false;
		foreach($datas as $key => $data){
			if(!in_array($key, $message_keys, true)) return false;
			elseif(!is_string($data)) return false;
		}
		return 'event' == $datas['msg_type'] && 'subscribe' == $datas['event'] ? true : false;
	}
	
	/**
	 * protected boolean function is_event_subscribe_qrscene_message(array $datas)
	 */
	protected function is_event_subscribe_qrscene_message(array $datas): bool {
		$message_keys = ['to_user_name', 'from_user_name', 'create_time', 'msg_type', 'event', 'event_key', 'ticket'];
		if(count($datas) != count($message_keys)) return false;
		foreach($datas as $key => $data){
			if(!in_array($key, $message_keys, true)) return false;
			elseif(!is_string($data)) return false;
		}
		return 'event' == $datas['msg_type'] && 'subscribe' == $datas['event'] ? true : false;
	}
	
	/**
	 * protected boolean function is_event_scan_qrscene_message(array $datas)
	 */
	protected function is_event_scan_qrscene_message(array $datas): bool {
		$message_keys = ['to_user_name', 'from_user_name', 'create_time', 'msg_type', 'event', 'event_key', 'ticket'];
		if(count($datas) != count($message_keys)) return false;
		foreach($datas as $key => $data){
			if(!in_array($key, $message_keys, true)) return false;
			elseif(!is_string($data)) return false;
		}
		return 'event' == $datas['msg_type'] && 'scan' == $datas['event'] ? true : false;
	}
	
	/**
	 * protected boolean function is_event_unsubscribe_message(array $datas)
	 */
	protected function is_event_unsubscribe_message(array $datas): bool {
		$message_keys = ['to_user_name', 'from_user_name', 'create_time', 'msg_type', 'event'];
		if(count($datas) != count($message_keys)) return false;
		foreach($datas as $key => $data){
			if(!in_array($key, $message_keys, true)) return false;
			elseif(!is_string($data)) return false;
		}
		return 'event' == $datas['msg_type'] && 'unsubscribe' == $datas['event'] ? true : false;
	}
	
	/**
	 * protected boolean function is_event_click_message(array $datas)
	 */
	protected function is_event_click_message(array $datas): bool {
		$message_keys = ['to_user_name', 'from_user_name', 'create_time', 'msg_type', 'event', 'event_key'];
		if(count($datas) != count($message_keys)) return false;
		foreach($datas as $key => $data){
			if(!in_array($key, $message_keys, true)) return false;
			elseif(!is_string($data)) return false;
		}
		return 'event' == $datas['msg_type'] && 'click' == $datas['event'] ? true : false;
	}
	
	/**
	 * protected boolean function is_event_view_message(array $datas)
	 */
	protected function is_event_view_message(array $datas): bool {
		$message_keys = ['to_user_name', 'from_user_name', 'create_time', 'msg_type', 'event', 'event_key'];
		if(count($datas) != count($message_keys)) return false;
		foreach($datas as $key => $data){
			if(!in_array($key, $message_keys, true)) return false;
			elseif(!is_string($data)) return false;
		}
		return 'event' == $datas['msg_type'] && 'view' == $datas['event'] ? true : false;
	}
	
	/**
	 * protected array function pascal_to_underline(array $datas)
	 */
	protected function pascal_to_underline(array $datas): array {
		$keys = array_keys($datas);
		$values = array_values($datas);
		foreach($keys as &$key){
			if(is_string($key)) $key = pascal_to_underline_named($key);
		}
		return array_combine($keys, $values);
	}
	
	/**
	 * protected array function underline2pascal(array $datas)
	 */
	protected function underline2pascal(array $datas): array {
		$keys = array_keys($datas);
		$values = array_values($datas);
		foreach($keys as &$key){
			if(is_string($key)) $key = underline_to_pascal_named($key);
		}
		return array_combine($keys, $values);
	}
	
	/**
	 * protected void function header(string $mime_type)
	 */
	protected function header(string $mime_type): void {
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		header('Content-Type: ' . $mime_type);
	}
	//
}

