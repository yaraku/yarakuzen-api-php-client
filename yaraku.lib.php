<?php
/**
 * Client library for the Yaraku REST API. 
 *
 * PHP Version 5
 *
 * @category YarakuZen
 * @package ClientLibrary
 * @author Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license   http://www.yaraku.co.jp Proprietary
 * @link      http://www.yarakuzen.com
 */

/**
 * Class DocBlock
 * YarakuClient
 *
 * @category YarakuZen
 * @package  ClientLibrary
 * @author   Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license  http://www.yaraku.co.jp Proprietary
 * @link     http://www.yarakuzen.com
 */
class YarakuClient{

	private $key;
	private $secret;


	/////////////////////////////////////////////////////////
	// Atributes that shouldn't actually be changed often //
	///////////////////////////////////////////////////////

	// default to Yaraku production env
	protected $_url = "http://api.yarakuzen.com";

	// default to 5 min
	protected $_timeout = 300;

	protected $_httpUser = null;
	protected $_httpPass;



	/**
	 * When using HTTPS disable the certificate validation
	 */
	protected $_insecure = false;


	protected static const 
		HTTP_GET = 1,
		HTTP_POST = 2,
		HTTP_PUT = 3;

	/**
	 * Initializes the client using the given API Key and Secret generated at Yaraku.
	 *
	 * @param key the API key
	 * @param secret the API secret
	 */
	public __construct($key, $secret){
		$this->key = $key;
		$this->secret = $secret;
		$this->url = $url;
	}


	/**
	 * Sets an alternative URL for calling the API.
	 *
	 * @param url (optional) the URL for YarakuZen.
	 * @return $this
	 */
	public function url($url){
		$this->_url = $url;
		return $this;
	}

	/**
	 * Timeout for each request, in sec
	 * @return $this
	 */
	public function timeout($timeout){
		$this->_timeout = $timeout;
		return $this;
	}

	/**
	 * Disables the certificate validation for HTTPS requests if parameter is unset or true.
	 * @return $this
	 */
	public function insecure($insecure){
		$this->_insecure = !isset($insecure) || $insecure == true;
		return $this;
	}

	/**
	 * Use HTTP autentication on top of key/secret auth
	 *
	 * @param $username the username for HTTP auth
	 * @param $password the password for HTTP auth
	 * @return $this
	 */
	public function auth($username, $password){
		$this->_httpUser = $username;
		$this->_httpPass = $password;
		return $this;
	}

	/**
	 * Calls the API with the given payload returning the response in proper PHP object.
	 */
	protected function __callApi($httpMethod, $apiMethod, $payload){

		$s = curl_init();

		curl_setopt($s,CURLOPT_URL,$this->_url . "/" . $apiMethod);
		curl_setopt($s,CURLOPT_TIMEOUT, $this->_timeout);
		// we need the response as a string
		curl_setopt($s,CURLOPT_RETURNTRANSFER, true);

		if($this->_httpUser != null)
			curl_setopt($s, CURLOPT_USERPWD, $this->_httpUser.':'.$this->_httpPass);
		}

		switch($httpMethod){
			case YarakuClient::HTTP_GET:
				curl_setopt($s,CURLOPT_GET, true); // just being verbose.. didn't really need it
			case YarakuClient::HTTP_POST:
				curl_setopt($s,CURLOPT_POST, true);
				curl_setopt($s,CURLOPT_POSTFIELDS, json_encode($this->_postFields));
				break;
			default:
		}


		curl_setopt($s, CURLOPT_SSL_VERIFYPEER, $this->_insecure);
		curl_setopt($s, CURLOPT_USERAGENT, $this->_useragent);
		curl_setopt($s, CURLOPT_REFERER, $this->_referer);

		$this->_response = curl_exec($s);
		$this->_status = curl_getinfo($s,CURLINFO_HTTP_CODE);

		curl_close($s); 

		// TODO: treat the response
		return json_decode($this->_response);
	}
}


?>
