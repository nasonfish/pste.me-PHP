<?php
class PsteMe {

    public $username;

    public $password;

    public function __construct($username, $key){
        $this->username = $username;
        $this->password = $key;
    }

    /**
     * Send a request to $file at $location using the specified method and params. This returns the contents of the page.
     *
     * @param string $file Name of the file - start this with a /.
     * @param bool $auth If you want to send your authentication username and key with the request with basic authentication.
     * @param string $method The method you want to use - GET or POST.
     * @param array $params an accociative array of values you want to GET/POST.
     * @param string $location The base location you're sending the request to. This is set up to use SSL (HTTPS)
     * @return string Data given by the page.
     */
    private function sendRequest($file, $auth = true, $method = 'GET', $params = array(), $location = 'http://beta.pste.me'){
        $method = strtoupper($method);
        if($method === 'GET'){
            $p = '';
            foreach($params as $key => $val){
                if($p === ''){
                    $p .= urlencode($key) . '=' . urlencode($val);
                } else {
                    $p .= '&' . urlencode($key) . '=' . urlencode($val);
                }
            }
        } else {
            $p = http_build_query($params);
        }
        $headers = array();
        if($auth){
            $headers[] = 'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password);
        }
        if($method === "POST"){
            $headers[] = "Content-type: application/x-www-form-urlencoded";
            $headers[] = 'Content-Length: ' . strlen($p);
        }
        $options = array(
            'http' => array_merge(array(
                'method' => $method,
                'header' => implode("\r\n", $headers)
            ), $method === 'POST' ? array('content' => $p) : array()) // Yuck. :(
        );
        $context = stream_context_create($options);

        $r = fopen($location . $file . ($method === 'GET' && $p != '' ? '?' . $p : ''), 'r', false, $context); // Also yuck.
        $data = stream_get_contents($r);
        fclose($r);
        return $data;
    }

    private function json_get($file, $params = array()){
        return json_decode($this->sendRequest($file, true, 'GET', $params), true);
    }

    private function json_post($file, $params = array()){
        return json_decode($this->sendRequest($file, true, 'POST', $params), true);
    }

    /**
     * Get information about a paste
     * @param $slug string of the paste that identifies it.
     * @return Paste Object that holds all the information about the paste.
     */
    public function view($slug){
        return new Paste($this->json_get('/api/v1/paste/', array('paste'=>$slug)));
    }


    /**
     * Create a new paste and return the data of it.
     * @param string $paste The text you are pasting
     * @param string $name The name of the new paste
     * @param int|string $access Integer of access level for your paste. Use Privacy::_Public or Privacy::_Private.
     * @param string $expires String of amount of time to expiry, compatible with strtotime(). Default one month.
     * @param string $mode Syntax highlighting type. use Language::YourLanguage.
     * @return Paste Information about the new paste you've created!
     */
    public function create($paste, $name="New Paste", $access=1, $expires="+1 month", $mode="text/plain"){
        /*$expires = date('r', strtotime($expires));*/

        return new Paste($this->json_post('/api/v1/paste/', array(
            'paste'=>$paste,
            'name'=>$name,
            'access'=>$access,
            'expires'=>$expires,
            'mode'=>$mode
        )));
    }
}

class Privacy{
    const _PUBLIC = 1;
    const _PRIVATE = 0;
}

class Language{
    const Plain = 'text/plain';
    const C = 'text/x-csrc';
    const Cpp = 'text/x-c++src';
    const Csharp = 'text/x-csharp';
    const Clojure = 'text/x-clojure';
    const CoffeeScript = 'text/x-coffeescript';
    const CSS = 'text/css';
    const Diff = 'text/x-diff';
    const Groovy = 'text/x-groovy';
    const Haml = 'text/x-haml';
    const HTML = 'text/html';
    const HTTP = 'message/http';
    const Jade = 'text/x-jade';
    const Java = 'text/x-java';
    const JavaScript = 'text/javascript';
    const JSON = 'application/json';
    const LESS = 'text/x-less';
    const Lua = 'text/x-lua';
    const Markdown = 'text/x-markdown';
    const Perl = 'text/x-perl';
    const PHP = 'text/x-php';
    const PHP_WITH_HTML = 'application/x-httpd-php';
    const Properties = 'text/x-properties';
    const Python = 'text/x-python';
    const Ruby = 'text/x-ruby';
    const SASS = 'text/x-sass';
    const Scala = 'text/x-scala';
    const Shell = 'text/x-sh';
    const SQL = 'text/x-sql';
    const TCL = 'text/x-tcl';
    const VB = 'text/x-vb';
    const VBScript = 'text/vbscript';
    const XML = 'application/xml';
    const XQuery = 'application/xquery';
    const Yaml = 'text/x-yaml';
}

class Paste{
    public $json;

    public function __construct($json){
        $this->json = $json;
    }

    public function getRaw(){
        return $this->json;
    }

    public function getDateCreated(){
        return $this->json['date_created'];
    }

    public function getDateExpire(){
        return $this->json['date_expires'];
    }

    public function getSlug(){
        return $this->json['slug'];
    }

    public function getAccess(){
        return $this->json['access'];
    }

    public function getMode(){
        return $this->json['mode'];
    }

    public function getPaste(){
        return $this->json['paste'];
    }

    public function getName(){
        return $this->json['name'];
    }

}