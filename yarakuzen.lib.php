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
 * YarakuZenClient
 *
 * @category YarakuZen
 * @package  ClientLibrary
 * @author   Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license  http://www.yaraku.co.jp Proprietary
 * @link     http://www.yarakuzen.com
 */
class YarakuZenClient{

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


	protected $_source = "jp";
	protected $_target = "en";


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

	////////////////////////////
	// Some Extra Parameters //
	//////////////////////////

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

	////////////////////
	// Text API Method //
	////////////////////

	////////////////////
	// Inner Working //
	//////////////////

	/**
	 * Sign the given payload, returning it.
	 */
	protected function __sign($payload){
		$payload->publicKey = $this->key;
		$payload->timestamp = time(); // A Unix timestamp
		$payload->signature = hash_hmac('sha1', $payload->timestamp.$this->key, $this->secret); // Create a sha1 hash
	}

	/**
	 * Return the given payload as a JSON encoded string of an object.
	 * Make sure it's actually signed before returning.
	 */
	protected function __toJson($payload){
		if(is_object($payload))
			$obj = clone $payload;
		elseif($is_array($payload))
			$obj = (object) $payload;
		else
			$obj = (object) array();

		return $this->__sign($obj);
	}

	/**
	 * Calls the API with the given payload returning the response in proper PHP object.
	 */
	protected function __callApi($httpMethod, $apiMethod, $payload){

		$curl = curl_init();
		$pl = $this->__toJson($payload);

		curl_setopt($curl, CURLOPT_URL, $this->_url."/".$apiMethod);

		curl_setopt($curl, CURLOPT_TIMEOUT, $this->_timeout);

		// we need the response as a string
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);

		if($this->_httpUser != null)
			curl_setopt($curl, CURLOPT_USERPWD, $this->_httpUser.':'.$this->_httpPass);
		}

		switch($httpMethod){
			case YarakuClient::HTTP_GET:
				curl_setopt($curl,CURLOPT_GET, true); // just being verbose.. didn't really need it
			case YarakuClient::HTTP_POST:
				curl_setopt($curl,CURLOPT_POST, true);
				curl_setopt($curl,CURLOPT_POSTFIELDS, $pl);
				break;
			default:
		}

		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, !$this->_insecure);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_useragent);
		curl_setopt($curl, CURLOPT_REFERER, $this->_referer);

		$this->_response = curl_exec($curl);
		$this->_status = curl_getinfo($curl,CURLINFO_HTTP_CODE);

		curl_close($curl); 

		// TODO: treat the response
		return json_decode($this->_response);
	}
}


?>
