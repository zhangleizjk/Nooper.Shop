<?php

// declare(strict_types = 1);
namespace Nooper;

use Exception;

class Mimicry {
	
	/**
	 * Properties
	 */
	protected $curl;
	protected $cert;
	protected $key;
	protected $errors = [
		0=>'curle_ok',
		1=>'curle_unsupported_protocol',
		2=>'curle_failed_init',
		3=>'curle_url_malformat',
		4=>'curle_url_malformat_user',
		5=>'curle_couldnt_resolve_proxy',
		6=>'curle_couldnt_resolve_host',
		7=>'curle_couldnt_connect',
		8=>'curle_ftp_weird_server_reply',
		9=>'curle_remote_access_denied',
		10=>'curle_ftp_accept_failed',
		11=>'curle_ftp_weird_pass_reply',
		12=>'curle_ftp_accept_timeout',
		13=>'curle_ftp_weird_pasv_reply',
		14=>'curle_ftp_weird_227_format',
		15=>'curle_ftp_cant_get_host',
		16=>'curle_http2',
		17=>'curle_ftp_couldnt_set_type',
		18=>'curle_partial_file',
		19=>'curle_ftp_couldnt_retr_file',
		21=>'curle_quote_error',
		22=>'curle_http_returned_error',
		23=>'curle_write_error',
		25=>'curle_upload_failed',
		26=>'curle_read_error',
		27=>'curle_out_of_memory',
		28=>'curle_operation_timedout',
		30=>'curle_ftp_port_failed',
		31=>'curle_ftp_couldnt_use_rest',
		33=>'curle_range_error',
		34=>'curle_http_post_error',
		35=>'curle_ssl_connect_error',
		36=>'curle_bad_download_resume',
		37=>'curle_file_couldnt_read_file',
		38=>'curle_ldap_cannot_bind',
		39=>'curle_ldap_search_failed',
		41=>'curle_function_not_found',
		42=>'curle_aborted_by_callback',
		43=>'curle_bad_function_argument',
		45=>'curle_interface_failed',
		47=>'curle_too_many_redirects',
		48=>'curle_unknown_telnet_option',
		49=>'curle_telnet_option_syntax',
		51=>'curle_peer_failed_verification',
		52=>'curle_got_nothing',
		53=>'curle_ssl_engine_notfound',
		54=>'curle_ssl_engine_setfailed',
		55=>'curle_send_error',
		56=>'curle_recv_error',
		58=>'curle_ssl_certproblem',
		59=>'curle_ssl_cipher',
		60=>'curle_ssl_cacert',
		61=>'curle_bad_content_encoding',
		62=>'curle_ldap_invalid_url',
		63=>'curle_filesize_exceeded',
		64=>'curle_use_ssl_failed',
		65=>'curle_send_fail_rewind',
		66=>'curle_ssl_engine_initfailed',
		67=>'curle_login_denied',
		68=>'curle_tftp_notfound',
		69=>'curle_tftp_perm',
		70=>'curle_remote_disk_full',
		71=>'curle_tftp_illegal',
		72=>'curle_tftp_unknownid',
		73=>'curle_remote_file_exists',
		74=>'curle_tftp_nosuchuser',
		75=>'curle_conv_failed',
		76=>'curle_conv_reqd',
		77=>'curle_ssl_cacert_badfile',
		78=>'curle_remote_file_not_found',
		79=>'curle_ssh',
		80=>'curle_ssl_shutdown_failed',
		81=>'curle_again',
		82=>'curle_ssl_crl_badfile',
		83=>'curle_ssl_issuer_error',
		84=>'curle_ftp_pret_failed',
		84=>'curle_ftp_pret_failed',
		85=>'curle_rtsp_cseq_error',
		86=>'curle_rtsp_session_error',
		87=>'curle_ftp_bad_file_list',
		88=>'curle_chunk_failed',
		89=>'curle_no_connection_available ',
		90=>'curle_ssl_pinnedpubkeynotmatch',
		91=>'curle_ssl_invalidcertstatus',
		92=>'curle_http2_stream'
	];
	
	/*
	 * protected $errors = [
	 * 0=>'curle_ok',
	 * 1=>'curle_unsupported_protocol',
	 * 2=>'curle_failed_init',
	 * 3=>'curle_url_malformat',
	 * 4=>'curle_url_malformat_user',
	 * 5=>'curle_couldnt_resolve_proxy',
	 * 6=>'curle_couldnt_resolve_host',
	 * 7=>'curle_couldnt_connect',
	 * 8=>'curle_ftp_weird_server_reply',
	 * 9=>'curle_remote_access_denied',
	 * 10=>'curle_ftp_accept_failed',
	 * 11=>'curle_ftp_weird_pass_reply',
	 * 12=>'curle_ftp_accept_timeout',
	 * 13=>'curle_ftp_weird_pasv_reply',
	 * 14=>'curle_ftp_weird_227_format',
	 * 15=>'curle_ftp_cant_get_host',
	 * 16=>'curle_http2',
	 * 17=>'curle_ftp_couldnt_set_type',
	 * 18=>'curle_partial_file',
	 * 19=>'curle_ftp_couldnt_retr_file',
	 * 21=>'curle_quote_error',
	 * 22=>'curle_http_returned_error',
	 * 23=>'curle_write_error',
	 * 25=>'curle_upload_failed',
	 * 26=>'curle_read_error',
	 * 27=>'curle_out_of_memory',
	 * 28=>'curle_operation_timedout',
	 * 30=>'curle_ftp_port_failed',
	 * 31=>'curle_ftp_couldnt_use_rest',
	 * 33=>'curle_range_error',
	 * 34=>'curle_http_post_error',
	 * 35=>'curle_ssl_connect_error',
	 * 36=>'curle_bad_download_resume',
	 * 37=>'curle_file_couldnt_read_file',
	 * 38=>'curle_ldap_cannot_bind',
	 * 39=>'curle_ldap_search_failed',
	 * 41=>'curle_function_not_found',
	 * 42=>'curle_aborted_by_callback',
	 * 43=>'curle_bad_function_argument',
	 * 45=>'curle_interface_failed',
	 * 47=>'curle_too_many_redirects',
	 * 48=>'curle_unknown_telnet_option',
	 * 49=>'curle_telnet_option_syntax',
	 * 51=>'curle_peer_failed_verification',
	 * 52=>'curle_got_nothing',
	 * 53=>'curle_ssl_engine_notfound',
	 * 54=>'curle_ssl_engine_setfailed',
	 * 55=>'curle_send_error',
	 * 56=>'curle_recv_error',
	 * 58=>'curle_ssl_certproblem',
	 * 59=>'curle_ssl_cipher',
	 * 60=>'curle_ssl_cacert',
	 * 61=>'curle_bad_content_encoding',
	 * 62=>'curle_ldap_invalid_url',
	 * 63=>'curle_filesize_exceeded',
	 * 64=>'curle_use_ssl_failed',
	 * 65=>'curle_send_fail_rewind',
	 * 66=>'curle_ssl_engine_initfailed',
	 * 67=>'curle_login_denied',
	 * 68=>'curle_tftp_notfound',
	 * 69=>'curle_tftp_perm',
	 * 70=>'curle_remote_disk_full',
	 * 71=>'curle_tftp_illegal',
	 * 72=>'curle_tftp_unknownid',
	 * 73=>'curle_remote_file_exists',
	 * 74=>'curle_tftp_nosuchuser',
	 * 75=>'curle_conv_failed',
	 * 76=>'curle_conv_reqd',
	 * 77=>'curle_ssl_cacert_badfile',
	 * 78=>'curle_remote_file_not_found',
	 * 79=>'curle_ssh',
	 * 80=>'curle_ssl_shutdown_failed',
	 * 81=>'curle_again',
	 * 82=>'curle_ssl_crl_badfile',
	 * 83=>'curle_ssl_issuer_error',
	 * 84=>'curle_ftp_pret_failed',
	 * 84=>'curle_ftp_pret_failed',
	 * 85=>'curle_rtsp_cseq_error',
	 * 86=>'curle_rtsp_session_error',
	 * 87=>'curle_ftp_bad_file_list',
	 * 88=>'curle_chunk_failed',
	 * 89=>'curle_no_connection_available ',
	 * 90=>'curle_ssl_pinnedpubkeynotmatch',
	 * 91=>'curle_ssl_invalidcertstatus',
	 * 92=>'curle_http2_stream'
	 * ];
	 */
	
	/**
	 * public void function __construct(void)
	 */
	public function __construct() {
		$this->curl = curl_init();
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		curl_close($this->curl);
	}
	
	/**
	 * public string function cert(string $full_name)
	 */
	public function cert(string $full_name): string {
		$this->cert = $full_name;
		return $full_name;
	}
	
	/**
	 * public string function key(string $full_name)
	 */
	public function key(string $full_name): string {
		$this->key = $full_name;
		return $full_name;
	}
	/**
	 * public string function get(string $url, ?array $qry_params = null, integer $seconds = 30, boolean $ssl = false)
	 */
	public function get(string $url, array $qry_params = null, int $seconds = 30, bool $ssl = false): string {
		$this->prepare($url, $qry_params, $seconds, $ssl);
		curl_setopt($this->curl, CURLOPT_HTTPGET, true);
		return $this->fire();
	}
	
	/**
	 * public string function post(string url, string $data, ?array $qry_params = null, integer $seconds = 30, boolean $ssl = false)
	 */
	public function post(string $url, string $data, array $qry_params = null, int $seconds = 30, bool $ssl = false): string {
		$this->prepare($url, $qry_params, $seconds, $ssl);
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		return $this->fire();
	}
	
	/**
	 * protected void function prepare(string $url, ?array $qry_params, integer $seconds, boolean $ssl)
	 */
	protected function prepare(string $url, array $qry_params, int $seconds, bool $ssl): void {
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($this->curl, CURLOPT_HEADER, false);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $seconds);
		if(is_array($qry_params)){
			foreach($qry_params as $key => $qry){
				if(is_string($key) && is_string($qry)) $datas[] = $key . '=' . rawurlencode($qry);
			}
			if(isset($datas)) $url .= '?' . implode('&', $datas);
		}
		curl_setopt($this->curl, CURLOPT_URL, $url);
		if($ssl){
			curl_setopt($this->curl, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($this->curl, CURLOPT_SSLCERT, $this->cert);
			curl_setopt($this->curl, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($this->curl, CURLOPT_SSLKEY, $this->key);
		}
	}
	
	/**
	 * protected string function get_error_message(integer $err_no)
	 */
	protected function get_error_message(int $err_no): string {
		return $this->errors[$err_no] ?? 'curle_unknown_error';
	}
	
	/**
	 * protected string function fire(void)
	 */
	protected function fire(): string {
		$end = curl_exec($this->curl);
		if(false === $end){
			$code = curl_errno($this->curl);
			$message = $this->get_error_message($code);
			throw new Exception($message, $code);
		}
		return $end;
	}
	// -- END --
}

