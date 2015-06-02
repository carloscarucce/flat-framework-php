<?php

namespace flat;

class Url {
    
    private $scheme = '';
    private $host = '';
    private $port = '';
    private $user = '';
    private $pass = '';
    private $path = '';
    private $query = '';
    private $fragment = '';
    
    /**
     * Verify if a given string is a valid URL
     * and return TRUE if is valid or FALSE in case its not valid
     * 
     * @param string $str
     * @return boolean
     */
    public static function isValid($str){
        
        return filter_var($str, FILTER_VALIDATE_URL) !== false;
            
    }
    
    /**
     * Get current url
     * 
     * @return \Flat\Url
     */
    public static function getCurrent(){
        
        $urlObj = new static();
        
        $pageURL = 'http';
        if (!empty($_SERVER["HTTPS"])) {
            $pageURL .= 's';
        }
        
        $pageURL .= "://";
        
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        
        $urlObj->ParseString($pageURL);
        return $urlObj;
        
    }
    
    /**
     * 
     * @return string
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * 
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    public function getPort() {
        return $this->port;
    }

    /**
     * 
     * @return string
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * 
     * @return string
     */
    public function getPass() {
        return $this->pass;
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * 
     * @return string
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * 
     * @return string
     */
    public function getFragment() {
        return $this->fragment;
    }

    /**
     * 
     * @return string
     */
    public function setScheme($scheme) {
        $this->scheme = $scheme;
    }

    /**
     * 
     * @return string
     */
    public function setHost($host) {
        $this->host = $host;
    }
    
    /**
     * 
     * @param string $port
     */
    public function setPort($port) {
        $this->port = $port;
    }

    /**
     * 
     * @param string $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * 
     * @param string $pass
     */
    public function setPass($pass) {
        $this->pass = $pass;
    }

    /**
     * 
     * @param string $path
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * 
     * @param string $querystring
     */
    public function setQuery($query) {
        $this->query = $query;
    }

    /**
     * 
     * @param string $fragment
     */
    public function setFragment($fragment) {
        $this->fragment = $fragment;
    }
    
    /**
     * 'query' getter/setter (treats as array)
     * @param array $value
     * @return array
     */
    public function queryArray($value = null){
        
        if(!\is_null($value)){
            
            if(!empty($value))
                $this->query = http_build_query($value);
            else
                $this->query = '';
            
        }
        
        $qryArr = array();
        \parse_str($this->query, $qryArr);
        
        return $qryArr;
        
    }

    /**
     * Fill Attributes from given string
     * @param string $str
     * @throws \Exception
     */
    public function parseString($str){
        
        if(self::isValid($str) === false){
            throw new \Exception("Could not parse given string to URL: '$str'");
        }
        
        $info = \parse_url($str);
        
        $this->scheme = isset($info['scheme'])? $info['scheme'] : '';
        $this->host = isset($info['host'])? $info['host'] : '';
        $this->port = isset($info['port'])? $info['port'] : '';
        $this->user = isset($info['user'])?  $info['user'] : '';
        $this->pass = isset($info['pass'])? $info['pass'] : '';
        $this->path = isset($info['path'])? $info['path'] : '';
        $this->query = isset($info['query'])? $info['query'] : '';
        $this->fragment = isset($info['fragment'])? $info['fragment'] : '';
        
    }
    
    /**
     * Compatible url in string format
     * @return string
     */
    public function stringfy(){
        
        $scheme   = !empty($this->scheme) ? $this->scheme . '://' : ''; 
        $host     = !empty($this->host) ? $this->host : ''; 
        $port     = !empty($this->port) ? ':' . $this->port : ''; 
        $user     = !empty($this->user) ? $this->user : ''; 
        $pass     = !empty($this->pass) ? ':' . $this->pass  : ''; 
        $pass     = ($user || $pass) ? "$pass@" : ''; 
        $path     = !empty($this->path) ? $this->path : ''; 
        $query    = !empty($this->query) ? '?' . $this->query : ''; 
        $fragment = !empty($this->fragment) ? '#' . $this->fragment : ''; 
        return "$scheme$user$pass$host$port$path$query$fragment"; 
        
    }
    
    
    /**
     * Read content from the current URL
     * 
     * @param boolean $forceCURL Use cURL even if 'file_get_contents' is available
     * @return string
     */
    public function getContents($forceCURL = false){
        
        $contents = '';
        $URL = $this->Stringfy();
        
        if(\ini_get('allow_url_fopen') && !$forceCURL){ // try an faster approach
            
            $contents = \file_get_contents($URL);
            
        }else{ //try using cURL
            
            $curl = \curl_init();
            \curl_setopt($curl, CURLOPT_URL, $URL);
            \curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            \curl_setopt($curl, CURLOPT_HEADER, false);
            $contents = \curl_exec($curl);
            \curl_close($curl);
            
        }
        
        return $contents;
        
    }
    
    /**
     * Send post data to the current URL and retrieve its answer
     * (cURL extension must be active)
     * 
     * @param mixed $postData
     * @return string
     */
    public function sendPost($postData = array()){
        
        $contents = '';
        $URL = $this->stringfy();
        
        $curl = \curl_init();
        \curl_setopt($curl, CURLOPT_URL, $URL);
        \curl_setopt($curl,CURLOPT_POST, count($postData));
        \curl_setopt($curl,CURLOPT_POSTFIELDS, http_build_query($postData));
        \curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        \curl_setopt($curl, CURLOPT_HEADER, false);
        $contents = \curl_exec($curl);
        \curl_close($curl);
        
        return $contents;
        
    }

    /**
     * 
     * @param string $str - URL Formatted string
     */
    public function __construct($str = null) {
        
        if(!\is_null($str)){
            $this->parseString($str);
        }
            
    }
    
    /**
     * 
     * @return string
     */
    public function __toString() {
        return $this->stringfy();
    }
    
}