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

namespace YarakuZenApi;

/**
 * Class DocBlock
 * Client
 *
 * @category YarakuZen
 * @package  ClientLibrary
 * @author   Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license  http://www.yaraku.co.jp Proprietary
 * @link     http://www.yarakuzen.com
 */
class Client{

	const HTTP_POST = 1;

	private $publicKey;
	private $privateKey;


	/////////////////////////////////////////////////////////
	// Atributes that shouldn't actually be changed often //
	///////////////////////////////////////////////////////

	// default to Yaraku production env
	protected $_url = "http://api.yarakuzen.com";

	// default to 5 min
	protected $_timeout = 300;

	protected $_httpUser = null;
	protected $_httpPass;


	protected $_userAgent = "YarakuZen PHP Client v1.0";
	protected $_referer = "";


	/**
	 * When using HTTPS disable the certificate validation
	 */
	protected $_insecure = false;


	protected $_source = "jp";
	protected $_target = "en";


	/**
	 * The charset for the request
	 */
	protected $_charset = "UTF-8";


	/**
	 * Initializes the client using the given API Key and Secret generated at Yaraku.
	 *
	 * @param publicKey the API publicKey
	 * @param privateKey the API privateKey
	 */
	function __construct($publicKey, $privateKey){
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
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
	public function insecure($insecure=true){
		$this->_insecure = !isset($insecure) || $insecure == true;
		return $this;
	}

	/**
	 * Use HTTP autentication on top of publicKey/privateKey auth
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
	 * Sets the userAgent informed to the server
	 *
	 * @param $userAgent should be a string that identifies your application to the server.
	 * @return $this
	 */
	public function userAgent($userAgent){
		$this->_userAgent = $userAgent;
		return $this;
	}

	/**
	 * Set the referer informed to the server.
	 *
	 * @param $referer the referer
	 * @return $this
	 */
	public function referer($referer){
		$this->_referer = $referer;
		return $this;
	}

	/**
	 * Sets the charset for this request.
	 *
	 * @param $charset the charset of the request
	 * @return $this
	 */
	public function charset($charset){
		$this->_charset = $charset;
		return $this;
	}

	//////////////////////
	// Text API Method //
	////////////////////

	public function callTexts($payload){
		return $this->__callApi(Client::HTTP_POST, "texts", $payload);
	}

	////////////////////
	// Inner Working //
	//////////////////

	/**
	 * Sign the given payload, returning it.
	 */
	protected function __sign($payload){
		$payload->publicKey = $this->publicKey;
		$payload->timestamp = time(); // A Unix timestamp
		$payload->signature = hash_hmac('sha1', $payload->timestamp.$this->publicKey, $this->privateKey); // Create a sha1 hash

		return $payload;
	}

	/**
	 * Return the given payload as a JSON encoded string of an object.
	 * Make sure it's actually signed before returning.
	 */
	protected function __preparePayload($payload){
		if(is_object($payload))
			$obj = clone $payload;
		elseif($is_array($payload))
			$obj = (object) $payload;
		else
			$obj = (object) array();


		$o = $this->__sign($obj);
		return http_build_query($o);
	}

	/**
	 * Calls the API with the given payload returning the response in proper PHP object.
	 */
	protected function __callApi($httpMethod, $apiMethod, $payload){

		$curl = curl_init();
		$pl = $this->__preparePayload($payload);



		curl_setopt($curl, CURLOPT_URL, $this->_url."/".$apiMethod);

		curl_setopt($curl, CURLOPT_TIMEOUT, $this->_timeout);

		// we need the response as a string
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);

		if($this->_httpUser != null)
			curl_setopt($curl, CURLOPT_USERPWD, $this->_httpUser.':'.$this->_httpPass);

		switch($httpMethod){
			case Client::HTTP_POST:
				curl_setopt($curl,CURLOPT_POST, true);
				curl_setopt($curl,CURLOPT_POSTFIELDS, $pl);
				break;
			default:
				throw new Exception("HTTP Method Not Supported");
		}

		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, !$this->_insecure);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($curl, CURLOPT_REFERER, $this->_referer);


		$contentType = 'application/x-www-form-urlencoded;charset='.$this->_charset;
		curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-type: $contentType"]);

		$this->_response = curl_exec($curl);
		$this->_status = curl_getinfo($curl,CURLINFO_HTTP_CODE);

		curl_close($curl); 


		// TODO: treat the response
		return json_decode($this->_response);
	}
}

/**
 * Class DocBlock
 * TextData
 *
 * @category YarakuZen
 * @package  ClientLibrary
 * @author   Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license  http://www.yaraku.co.jp Proprietary
 * @link     http://www.yarakuzen.com
 */
class TextData{
	public function customData($customData){
		$this->customData = $customData;
		return $this;
	}

	public function text($text){
		$this->text = $text;
		return $this;
	}

	public function machineTranslate($machineTranslate = 1){
		$this->machineTranslate = $machineTranslate != false;
		return $this;
	}
}

/**
 * Class DocBlock
 * RequestPayload
 *
 * @category YarakuZen
 * @package  ClientLibrary
 * @author   Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license  http://www.yaraku.co.jp Proprietary
 * @link     http://www.yarakuzen.com
 */
class RequestPayload{

	public function lcSrc($source){
		$this->lcSrc = $source;
		return $this;
	}

	public function lcTgt($target){
		$this->lcTgt = $target;
		return $this;
	}

	public function machineTranslate($machineTranslate = true){
		$this->machineTranslate = $machineTranslate != false;
		return $this;
	}

	public function persist($persist = true){
		$this->persist = $persist;
		return $this;
	}

	public function addText($text){
		if(!isset($this->texts))
			$this->texts = array();
		$this->texts[] = $text;
		return $this;
	}
}



?>
