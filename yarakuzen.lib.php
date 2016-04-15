<?php namespace YarakuZenApi;

/**
 * Client library for the Yaraku REST API.
 *
 * PHP Version 5
 *
 * @category YarakuZen
 * @package ClientLibrary
 * @author Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license   MIT
 * @link      http://www.yarakuzen.com
 */

use Exception;

/**
 * Class DocBlock
 * Tier
 *
 * Used to declare the available tiers for using with the API.
 *
 * @category YarakuZen
 * @package  ClientLibrary
 * @author   Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license  MIT
 * @link     http://www.yarakuzen.com
 */
abstract class Tier
{
    abstract public function toString();
}

class StandardTier extends Tier
{
    public function toString()
    {
        return "standard";
    }
}

class BusinessTier extends Tier
{
    public function toString()
    {
        return "business";
    }
}


/**
 * Class DocBlock
 * TextData
 *
 * A text block that will be sent to the YarakuZen API.
 *
 * Each method actually sets the variable for the same name.
 *
 * For more information, see the official API documentation.
 *
 * @category YarakuZen
 * @package  ClientLibrary
 * @author   Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license  MIT
 * @link     http://www.yarakuzen.com
 */
class TextData
{
    /** @var string */
    public $customData;

    /** @var string */
    public $text;

    public function customData($customData)
    {
        $this->customData = $customData;

        return $this;
    }

    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    public function textFromFile($fileName)
    {
        $this->text = file_get_contents($fileName);

        return $this;
    }
}

/**
 * Class DocBlock
 * RequestPayload
 *
 * This represents the data being sent to the API.
 *
 * Each method actually sets the variable for the same name, except for the addText method.
 *
 * For more information, see the official API documentation.
 *
 * @category YarakuZen
 * @package  ClientLibrary
 * @author   Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license  MIT
 * @link     http://www.yarakuzen.com
 */
class RequestPayload
{
    /** @var string */
    public $lcSrc;

    /** @var string */
    public $lcTgt;

    /** @var bool */
    public $machineTranslate;

    /** @var bool */
    public $persist;

    /** @var int */
    public $quote;

    /** @var TextData[] */
    public $texts = [];

    /** @var string */
    public $tier;

    public function lcSrc($source)
    {
        $this->lcSrc = $source;
        return $this;
    }

    public function lcTgt($target)
    {
        $this->lcTgt = $target;
        return $this;
    }

    public function machineTranslate($machineTranslate = true)
    {
        $this->machineTranslate = $machineTranslate != false;
        return $this;
    }

    public function tier(Tier $tier)
    {
        $this->tier = $tier->toString();
        return $this;
    }

    public function quote($quote = true)
    {
        $this->quote = $quote ? 1 : 0;
        return $this;
    }

    public function persist($persist = true)
    {
        $this->persist = $persist;
        return $this;
    }

    public function addText(TextData $text)
    {
        $this->texts[] = $text;
        return $this;
    }
}


/**
 * Class DocBlock
 * Client
 *
 * The client is responsible for actually handling the call and signing the request.
 *
 * @category YarakuZen
 * @package  ClientLibrary
 * @author   Marcelo C. de Freitas <marcelo@yaraku.com>
 * @copyright 2015 Yaraku, Inc.
 * @license  MIT
 * @link     http://www.yarakuzen.com
 */
class Client
{
    const HTTP_POST = 1;
    const HTTP_GET = 2;

    /** @var string */
    private $publicKey;

    /** @var string  */
    private $privateKey;


    /////////////////////////////////////////////////////////
    // Attributes that shouldn't actually be changed often //
    ///////////////////////////////////////////////////////

    /**
     * Default to Yaraku production env
     * @var string
     */
    protected $url = "https://api.yarakuzen.com";

    /**
     * Default to 5 min
     * @var int
     */
    protected $timeout = 300;

    /** @var string | null */
    protected $httpUser = null;

    /** @var string */
    protected $httpPass;

    /** @var string */
    protected $userAgent = "YarakuZen PHP Client v1.0";
    
    /** @var string */
    protected $referer = "";

    /**
     * When using HTTPS disable the certificate validation
     * @var bool
     */
    protected $insecure = false;

    /**
     * The charset for the request
     * @var string
     */
    protected $charset = "UTF-8";

    /** @var mixed */
    protected $response;

    /** @var mixed */
    protected $status;


    /**
     * Initializes the client using the given API Key and Secret generated at Yaraku.
     *
     * @param string $publicKey The API publicKey
     * @param string $privateKey The API privateKey
     */
    public function __construct($publicKey, $privateKey)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    ////////////////////////////
    // Some Extra Parameters //
    //////////////////////////

    /**
     * Sets an alternative URL for calling the API.
     *
     * @param string $url The URL for YarakuZen.
     * @return $this
     */
    public function url($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Timeout for each request, in sec
     * @param int $timeout
     * @return $this
     */
    public function timeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Disables the certificate validation for HTTPS requests if parameter is unset or true.
     * @param bool|true $insecure
     * @return $this
     */
    public function insecure($insecure = true)
    {
        $this->insecure = !isset($insecure) || $insecure == true;
        return $this;
    }

    /**
     * Use HTTP authentication on top of publicKey/privateKey auth
     *
     * @param string $username The username for HTTP auth
     * @param string $password The password for HTTP auth
     * @return $this
     */
    public function auth($username, $password)
    {
        $this->httpUser = $username;
        $this->httpPass = $password;

        return $this;
    }

    /**
     * Sets the userAgent informed to the server
     *
     * @param string $userAgent Should be a string that identifies your application to the server.
     * @return $this
     */
    public function userAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Set the referer informed to the server.
     *
     * @param string $referer The referer
     * @return $this
     */
    public function referer($referer)
    {
        $this->referer = $referer;
        return $this;
    }

    /**
     * Sets the charset for this request.
     *
     * @param string $charset The charset of the request
     * @return $this
     */
    public function charset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    //////////////////////
    // Text API Method //
    ////////////////////

    /**
     * @deprecated Use Client::postTexts
     * @param mixed $payload
     * @return mixed
     * @throws Exception
     */
    public function callTexts($payload)
    {
        return $this->postTexts($payload);
    }

    /**
     * @param mixed $payload
     * @return mixed
     * @throws Exception
     */
    public function postTexts($payload)
    {
        return $this->__callApi(Client::HTTP_POST, "texts", $payload);
    }

    /**
     * @param string $customData
     * @param int $count
     * @return mixed
     * @throws Exception
     */
    public function getTextsByCustomData($customData, $count = 10)
    {
        $params = ["customData" => $customData];
        return $this->getTexts($params, $count);
    }

    /**
     * @param array $params
     * @param int $count
     * @return mixed
     * @throws Exception
     */
    private function getTexts(array $params, $count = 10)
    {
        $params['count'] = $count;
        return $this->__callApi(Client::HTTP_GET, "texts", $params);
    }

    ////////////////////
    // Inner Working //
    //////////////////

    /**
     * Sign the given payload, returning it.
     * @param $payload
     * @return mixed
     */
    protected function __sign($payload)
    {
        $payload->publicKey = $this->publicKey;
        // A Unix timestamp
        $payload->timestamp = time();
        // Create a sha1 hash
        $payload->signature = hash_hmac('sha1', $payload->timestamp . $this->publicKey, $this->privateKey);

        return $payload;
    }

    /**
     * Return the given payload as a JSON encoded string of an object.
     * Make sure it's actually signed before returning.
     * @param mixed $payload
     * @return string
     */
    protected function __preparePayload($payload)
    {
        if (is_object($payload)) {
            $obj = clone $payload;
        } elseif (is_array($payload)) {
            $obj = (object)$payload;
        } else {
            $obj = (object)array();
        }

        $o = $this->__sign($obj);

        return http_build_query($o);
    }

    /**
     * Calls the API with the given payload returning the response in proper PHP object.
     * @param int $httpMethod
     * @param string $apiMethod
     * @param mixed $payload
     * @return mixed
     * @throws Exception
     */
    protected function __callApi($httpMethod, $apiMethod, $payload)
    {
        $curl = curl_init();
        $pl = $this->__preparePayload($payload);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);

        // we need the response as a string
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($this->httpUser != null) {
            curl_setopt($curl, CURLOPT_USERPWD, $this->httpUser . ':' . $this->httpPass);
        }

        switch ($httpMethod) {
            case Client::HTTP_POST:
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $pl);
                $curlOptUrl = $this->url . "/" . $apiMethod;
                break;
            case Client::HTTP_GET:
                $curlOptUrl = $this->url . "/" . $apiMethod . "?" . $pl;
                break;
            default:
                throw new Exception("HTTP Method Not Supported");
        }

        curl_setopt($curl, CURLOPT_URL, $curlOptUrl);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, !$this->insecure);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($curl, CURLOPT_REFERER, $this->referer);

        $contentType = 'application/x-www-form-urlencoded;charset=' . $this->charset;
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-type: $contentType"]);

        $this->response = curl_exec($curl);
        $this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // TODO: treat the response
        return json_decode($this->response);
    }
}
