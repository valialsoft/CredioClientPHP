<?php
/*
MIT License

Copyright (c) 2016 Valialsoft, LLC

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

//! %Credio interfaces and implementations
namespace Credio;

include_once "CredioClientError.php";

/**
 * @brief %Exception class used for Exceptions in Client.
 */
class Exception extends \Exception
{
    public function __construct($msg, $code = 0, \Exception $prev = null)
    {
        parent::__construct($msg, $code, $prev);

        if (!is_null($prev)) $this->previous = $prev;
    }

    public function __toString()
    {
        return __CLASS__ . ": " . $this->message . " (" . $this->code . ")\n";
    }
}

/**
 * @brief used to store %Credio %Token.
 */
class Token
{
    private $value;
    private $expire;

    public function __construct($tokenString = null, $expire = null)
    {
        $this->value = $tokenString;
        $this->expire = $expire;
    }

    public static function fromDOM($domIn)
    {
        $xpath = new \DOMXpath($domIn);

        $res = $xpath->query("//token");

        if ($res != null && $res->length == 1) {
            $res = $res->item(0);
            $tokenString = $res->nodeValue;
            $expire = $res->getAttribute("expire");

            return new self($tokenString, $expire);
        }

        return null;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}

/**
 * @brief used to define %Credio Permission flags.
 */
class PermissionFlag
{
    const NONE = 0x00000000;
    const READ = 0x00000001;
    const WRITE = 0x00000002;
    const EXEC = 0x00000004;
    const RESERVED_1 = 0x00000008;
    const RESERVED_2 = 0x00000010;
    const RESERVED_3 = 0x00000020;
    const RESERVED_4 = 0x00000040;
    const RESERVED_5 = 0x00000080;
}

/**
 * @brief represents %Credio %Permission object.
 */
class Permission
{
    public $perm;///< stores permission value. first 24 bits can be used for definition of user permission flags.

    public function __construct($perm = PermissionFlag::NONE)
    {
        $this->perm = $perm;
    }

    public static function fromElement($xElement)
    {
        $perm = $xElement->nodeValue;

        return new self($perm);
    }

    /**
     * @brief check for READ permissions
     *
     * @return Boolean true if have READ permissions
     */
    public function isRead()
    {
        return (($this->perm & PermissionFlag::READ) === PermissionFlag::READ ? true : false);
    }

    /**
     * @brief check for WRITE permissions
     *
     * @return Boolean true if have WRITE permissions
     */
    public function isWrite()
    {
        return (($this->perm & PermissionFlag::WRITE) === PermissionFlag::WRITE ? true : false);
    }

    /**
     * @brief check for EXEC permissions
     *
     * @return Boolean true if have EXEC permissions
     */
    public function isExec()
    {
        return (($this->perm & PermissionFlag::EXEC) === PermissionFlag::EXEC ? true : false);
    }

    /**
     * @brief check for Custom PermissionFlag permissions
     *
     * @param PermissionFlag $flag
     * @return bool
     */
    public function isPermSet(PermissionFlag $flag)
    {
        return (($this->perm & $flag) === $flag ? true : false);
    }
}

/**
 * @brief used to define %Credio Attribute types.
 */
abstract class AttributeType
{
    const BOOLEAN = 0x01;
    const NUMBER = 0x02;
    const DECIMAL = 0x03;
    const STRING = 0x04;
    const BYTES = 0x05;
}

/**
 * @brief used to define %Credio Attribute target type.
 */
abstract class AttributeTargetType
{
    const DOMAIN = 0x00;
    const RESOURCE = 0x01;
    const GROUP = 0x02;
    const USER = 0x03;
    const AUTHCLIENT = 0x04;
}

/**
 * @brief represent %Credio Attribute.
 */
class Attribute
{
    public $id;
    public $key;
    public $type;
    public $value;

    public static function fromXElement($xElement)
    {
        $inst = new self();

        $inst->id = $xElement->getAttribute("id");
        $inst->key = $xElement->getAttribute("k");
        $inst->type = $xElement->getAttribute("t");
        $v = $xElement->nodeValue;
        switch ($inst->type) {
            case AttributeType::BOOLEAN:
                $inst->value = ($v == "0" ? FALSE : TRUE);
                break;
            case AttributeType::NUMBER:
            case AttributeType::DECIMAL:
            case AttributeType::STRING:
                $inst->value = $v;
                break;
            case AttributeType::BYTES:
                $inst->value = base64_decode($v);
                break;
        }

        return $inst;
    }

    public function toXml()
    {
        $s = '<a';
        if (isset($this->id)) $s .= ' id="' . $this->id . '"';
        $s .= ' name="' . $this->key . '"';
        $s .= ' type="' . $this->type . '"';
        $s .= '>';
        $s .= $this->value;
        $s .= '</a>';

        return $s;
    }

    public function toXmlElement($dom)
    {
        $a = $dom->createElement('a');
        if (isset($this->id)) {
            $i = $dom->createAttribute('id');
            $i->value = $this->id;
            $a->appendChild($i);
        }
        if (isset($this->key)) {
            $i = $dom->createAttribute('name');
            $i->value = $this->key;
            $a->appendChild($i);
        }
        if (isset($this->type)) {
            $i = $dom->createAttribute('type');
            $i->value = $this->type;
            $a->appendChild($i);
        }

        if (isset($this->type) && isset($this->value)) {
            switch ($this->type) {
                case AttributeType::BOOLEAN :
                    $a->appendChild($dom->createTextNode($this->value ? "true" : "false"));
                    break;
                default :
                    $a->appendChild($dom->createTextNode($this->value));
                    break;
            }
        }

        return $a;
    }
}

/**
 * @brief represents %Credio %Group
 */
class Group
{
    public $id;
    public $name;
    public $memberOf;///< array of %Group

    public static function fromXElement($xElement)
    {
        $inst = new self();
        $inst->id = $xElement->getAttribute("id");
        $inst->name = $xElement->getAttribute("name");
        $memberof = $xElement->getElementsByTagName("memberof");
        if ($memberof->length == 1) {
            $inst->memberOf = array();

            foreach ($memberof->item(0)->childNodes as $xGroup) {
                $g = Group::fromXElement($xGroup);
                array_push($inst->memberOf, $g);
            }
        }

        return $inst;
    }

    public function toXmlElement(\DOMDocument $dom, $includeMemberOf = false)
    {
        $a = $dom->createElement('g');
        if (isset($this->id)) {
            $i = $dom->createAttribute('id');
            $i->value = $this->id;
            $a->appendChild($i);
        }

        if (isset($this->name)) {
            $i = $dom->createAttribute('name');
            $i->value = $this->name;
            $a->appendChild($i);
        }

        if ($includeMemberOf && isset($this->memberOf) && is_array($this->memberOf) && count($this->memberOf) > 0) {
            $i = $dom->createElement('memberof');
            foreach ($this->memberOf as $key => $val) {
                $i->appendChild($val->toXmlElement($dom));
            }
            $a->appendChild($i);
        }

        return $a;
    }
}

/**
 * @brief represents %Credio %User
 */
class User
{
    public $id;
    public $uname;
    public $memberOf;///< array of %Group

    public static function fromXElement($xElement)
    {
        $inst = new self();
        $inst->id = $xElement->getAttribute("id");
        $inst->uname = $xElement->getAttribute("uname");
        $memberof = $xElement->getElementsByTagName("memberof");
        if ($memberof->length == 1) {
            $inst->memberOf = array();

            foreach ($memberof->item(0)->childNodes as $xGroup) {
                $g = Group::fromXElement($xGroup);
                array_push($inst->memberOf, $g);
            }
        }
        return $inst;
    }

    public function toXmlElement(\DOMDocument $dom)
    {
        $a = $dom->createElement('u');
        if (isset($this->id)) {
            $i = $dom->createAttribute('id');
            $i->value = $this->id;
            $a->appendChild($i);
        }
        if (isset($this->uname)) {
            $i = $dom->createAttribute('uname');
            $i->value = $this->uname;
            $a->appendChild($i);
        }
        if (isset($this->memberOf) && is_array($this->memberOf) && count($this->memberOf) > 0) {
            $i = $dom->createElement('memberof');
            foreach ($this->memberOf as $key => $val) {
                $i->appendChild($val->toXmlElement($dom));
            }
            $a->appendChild($i);
        }

        return $a;
    }
}

/**
 * @brief provides Client API for working with <a href="http://www.crediosys.com/">Credio server</a>.
 */
class Client
{
    public $IsCacheEnabled = true;
    private $cachePerm = array();
    private $cacheAttr = array();

    public $IsExceptionEnabled = true;    ///< if true Client will throw exceptions

    public $CredioServerURL = "https://10.60.0.44";

    public $domain;                        ///< domain
    public $token = NULL;///< token within Client class.

    private $isCookieInstance = false;    ///< determine whether this instance is created with cookie support.
    private $cookies = NULL;        ///< used to store PHP _COOKIE variable
    private $cookieName = "CREDIO_TOKEN";    ///< name for cookie stored in client browser

    private $errorCallbacks = array();///< callback handlers

    const defaultDomain = "CREDIO";

    private function __construct($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @brief create DOM document with root tag element for %Credio API
     *
     * @return DOMDocument
     */
    private function createDom()
    {
        $dom = new \DOMDocument();

        $root = $dom->createElement("credio");
        $v = $dom->createAttribute("v");
        $v->value = "1.0";

        $root->appendChild($v);

        $dom->appendChild($root);

        return $dom;
    }

    /**
     * @brief create DOM element for API.
     *
     * @param $dom
     * @param $api
     * @param $token
     * @return DOMElement
     */
    private function createApiElement($dom, $api, $token)
    {
        $l = $dom->createElement($api);
        $e = $dom->createAttribute("domain");
        $e->appendChild($dom->createTextNode($this->domain));
        $l->appendChild($e);

        if ($token != NULL) {
            $t = $dom->createElement("token");
            $t->appendChild($dom->createTextNode($token->__toString()));
            $l->appendChild($t);
        }

        $dom->documentElement->appendChild($l);

        return $l;
    }

    /**
     * @brief validate $token
     *
     * if $token is not set, function replaces $token with $this->token
     * @param $token
     * @throws Exception
     * @return null
     */
    private function validateToken($token)
    {
        if (!isset($token)) $token = $this->token;

        if (!isset($token)) {
            if ($this->IsExceptionEnabled)
                throw new Exception("token is missing.");
            return NULL;
        }

        return $token;
    }

    private function _send_xml_req($xml)
    {
        $c = curl_init();

        curl_setopt($c, CURLOPT_URL, $this->CredioServerURL);
        //curl_setopt($c, CURLOPT_HEADER, true);
        curl_setopt($c, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=utf-8", "Expect:"));
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($c, CURLOPT_TIMEOUT, 5);
        curl_setopt($c, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);

        $r = curl_exec($c);
        $ret['err'] = curl_errno($c);
        if (!$ret['err']) {
            $ret['code'] = curl_getinfo($c, CURLINFO_HTTP_CODE);
            $ret['result'] = $r;
        } else
            $ret['err'] = "code:" . $ret['err'] . ":" . curl_error($c) . ".(host: " . $this->CredioServerURL . ")";

        curl_close($c);

        return $ret;
    }

    private function netGetResponse(&$domOut)
    {
        $xmlOut = $domOut->saveXML();

        $nr = $this->_send_xml_req($xmlOut);

        if ($nr['err']) {
            if ($this->IsExceptionEnabled) throw new Exception("net error: " . $nr['err'], 200);
            return false;
        }

        if ($nr['code'] != 200) {
            if ($this->IsExceptionEnabled) throw new Exception("net error: server return code:" . $nr['code'], 201);
            return false;
        }

        $domIn = new \DOMDocument();
        if (!@$domIn->loadXML($nr['result'])) {
            if ($this->IsExceptionEnabled) throw new Exception("net error: returned data is not xml.:" . $nr['result'], 210);
            return false;
        }

        $xpath = new \DOMXpath($domIn);

        $res = $xpath->query("/credio/res");
        if ($res->length != 1) {
            if ($this->IsExceptionEnabled) throw new Exception("net error: can not find response tag.", 202);
            return false;
        }
        $res = $res->item(0);
        $res_code = $res->getAttribute('code');
        $res_msg = $res->getAttribute('msg');
        if ($res_code != '0') {
            if (isset($this->errorCallbacks[$res_code])) {
                foreach ($this->errorCallbacks[$res_code] as $key => $value) {
                    call_user_func($value, $this);
                }
            } else {
                if ($this->IsExceptionEnabled) throw new Exception($res_msg, $res_code);
            }
            return false;
        }

        return $domIn;
    }

    /**
     * @brief navigate browser to Sso portal.
     *
     * Sso portal URL address is obtained from %Credio domain
     * @param null $loginPortalUri
     * @throws Exception
     * @return bool
     */
    private function navigateToSsoPortal($loginPortalUri = null)
    {
        //get SSO portal from %Credio server
        $portalAttr = 'sso_web_portal';
        $attrs = $this->getAttributes('/=' . $portalAttr);
        if (isset($attrs) && \is_object($attrs[0]) && $attrs[0]->key == $portalAttr) {
            $uri = $attrs[0]->value . "?cbk=" . urlencode(base64_encode((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
            $uri .= "&d=" . urlencode(base64_encode($this->domain));
            header("Location: " . $uri);
            die("your browser does not support redirect. you should manually navigate  to: " . $uri);
        }

        if ($this->IsExceptionEnabled)
            throw new Exception("Uri for SSO web portal is missing.", 11);

        return false;
    }

    /**
     * @brief create new Client instance.
     *
     * @param string $domain
     * @return Client
     */
    public static function getInstance($domain = Client::defaultDomain)
    {
        return new self($domain);
    }

    /**
     * @brief create new Client instance with <a href="https://en.wikipedia.org/wiki/HTTP_cookie">HTTP Cookie</a> support.
     *
     * Cookie support will automatically set cookie on client browser on Client::getToken method,
     * and will remove cookie on Client::releaseToken method.
     * @param string $domain
     * @param PHP_COOKIE $cookies
     * @throws Exception
     * @return Client
     */
    public static function getCookieInstance($domain, &$cookies = NULL)
    {
        $inst = new self($domain);

        $inst->isCookieInstance = true;

        if (isset($cookies)) $inst->cookies = $cookies;

        if (isset($cookies, $cookies[$inst->cookieName])) {
            $inst->token = \unserialize(\base64_decode($cookies[$inst->cookieName]));
        }

        return $inst;
    }

    /**
     * @brief create new Client instance with <a href="https://en.wikipedia.org/wiki/HTTP_cookie">HTTP Cookie</a> support.
     *
     * same as Client::getCookieInstance, but will automatically redirect to SSO portal, if session is not yet authenticated.
     * @param $domain
     * @param $cookies PHP _COOKIE variable
     * @throws Exception
     * @return Client
     */
    public static function getCookieSsoPortalInstance($domain, &$cookies = NULL)
    {
        $inst = new self($domain);

        $inst->isCookieInstance = true;

        if (isset($cookies)) $inst->cookies = $cookies;

        if (isset($cookies, $cookies[$inst->cookieName])) {
            $inst->token = \unserialize(\base64_decode($cookies[$inst->cookieName]));
        } else {
            if (isset($_GET, $_GET['token'])) {
                $inst->token = new Token(\base64_decode(\urldecode($_GET['token'])));

                \setcookie($inst->cookieName, \base64_encode(\serialize($inst->token)), time() + 10000, '/');

                $u = parse_url((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                $q = array();
                parse_str($u['query'], $q);
                $nq = array();
                foreach ($q as $k => $v)
                    if ($k != 'token') array_push($nq, $k . "=" . $v);

                header("Location: " . $u['scheme'] . "://" . $u['host'] . $u['path'] . (count($nq) > 0 ? implode('&', $nq) : ''));
                die();
            } else {
                $inst->navigateToSsoPortal();
            }
        }

        return $inst;
    }

    /**
     * @brief set callback $handler
     *
     * @param $errorCode
     * @param $handler
     */
    public function setCallback($errorCode, $handler)
    {
        if (!isset($this->errorCallbacks[$errorCode]))
            $this->errorCallbacks[$errorCode] = array();

        array_push($this->errorCallbacks[$errorCode], $handler);
    }

    /**
     * @brief remove callback $handler
     *
     * @param $errorCode
     * @param $handler
     */
    public function removeCallback($errorCode, $handler)
    {
        foreach ($this->errorCallbacks[$errorCode] as $k => $v) {
            if ($v === $handler) {
                unset($this->errorCallbacks[$errorCode][$k]);
                break;
            }
        }
    }

    public function haveToken()
    {
        return isset($this->token);
    }

    /**
     * @brief get permissions by path
     *
     * check <a href="http://www.crediosys.com/doc/api/#getPermissions" target="_blank">%Credio API getPermissions</a> for full specification
     * @param $path
     * @param null $token
     * @throws Exception
     * @return Permission[]
     */
    public function getPermissions($path, $token = null)
    {
        if ($this->IsCacheEnabled && isset($token) && isset($this->cacheAttr[$token->__toString()])) {
            if (isset($this->cachePerm[$token][$path]))
                return $this->cachePerm[$token][$path];
        }

        $domOut = $this->createDom();
        $domOut->formatOutput = true;
        $rootOut = $domOut->documentElement;

        $p = $domOut->createElement("getPermissions");
        $a = $domOut->createAttribute("domain");
        $a->appendChild($domOut->createTextNode($this->domain));
        $p->appendChild($a);

        if (isset($user)) {
            $t = $domOut->createAttribute("user");
            $t->appendChild($domOut->createTextNode($user));
            $p->appendChild($t);
        } else {
            if (!isset($token)) $token = $this->token;
            if (isset($token)) {
                $t = $domOut->createAttribute("token");
                $t->appendChild($domOut->createTextNode($token->__toString()));
                $p->appendChild($t);
            } else {
                if ($this->IsExceptionEnabled)
                    throw new Exception("getPermissions() requires 'token' or 'user' to be set.");
                return false;
            }
        }

        $x = $domOut->createElement("path");
        $x->appendChild($domOut->createTextNode($path));

        $p->appendChild($x);
        $rootOut->appendChild($p);

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        $xpath = new \DOMXpath($domIn);
        $xres = $xpath->query("/credio/perms/p");

        $res = array();

        for ($i = 0; $i < $xres->length; $i++) {
            $xelement = $xres->item($i);
            $p = array('name' => $xelement->getAttribute('p'), 'perm' => $xelement->nodeValue);
            array_push($res, $p);
        }

        if ($this->IsCacheEnabled && isset($token)) {
            $this->cacheAttr[$token->__toString()] = array();
            $this->cacheAttr[$token->__toString()][$path] = $res;
        }

        return $res;
    }

    /**
     * @brief get attributes by $path
     *
     * check <a href="http://www.crediosys.com/doc/api/#getAttributes" target="_blank">%Credio API getAttributes</a> for full specification
     * @param $path
     * @param null $token
     * @throws Exception
     * @return Attribute[]
     */
    public function getAttributes($path, $token = null)
    {
        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "getAttributes", $token ? $token : $this->token);

        $x = $domOut->createElement("path");
        $x->appendChild($domOut->createTextNode($path));
        $l->appendChild($x);

        $domIn = $this->netGetResponse($domOut);

        $xpath = new \DOMXpath($domIn);
        $xres = $xpath->query("/credio/attrs/a");

        $res = array();

        for ($i = 0; $i < $xres->length; $i++) {
            $a = Attribute::fromXElement($xres->item($i));
            array_push($res, $a);
        }

        return $res;
    }

    /**
     * @brief create new Attribute.
     *
     * check <a href="http://www.crediosys.com/doc/api/#attrInsert" target="_blank">%Credio API attrInsert</a> for full specification
     * @param $target
     * @param $targetType
     * @param Attribute $attribute
     * @param null $token
     * @throws Exception
     * @return bool | Attribute
     */
    public function attrInsert($target, $targetType, Attribute &$attribute, $token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "attrInsert", $token);

        $lt = $domOut->createAttribute("target");
        $lt->value = $target;
        $l->appendChild($lt);
        $lt = $domOut->createAttribute("targettype");
        $lt->value = $targetType;
        $l->appendChild($lt);

        $l->appendChild($attribute->toXmlElement($domOut));

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        $xpath = new \DOMXpath($domIn);
        $xres = $xpath->query("//insertedId");

        if ($xres->length == 1) {
            $xres = $xres->item(0);
            $attribute->id = $xres->nodeValue;
        }

        return $attribute;
    }

    /**
     * @brief change Attribute.
     *
     * check <a href="http://www.crediosys.com/doc/api/#attrEdit" target="_blank">%Credio API attrEdit</a> for full specification
     * @param $target
     * @param $targetType
     * @param $attribute
     * @param null $token
     * @throws Exception
     * @return bool
     */
    public function attrEdit($target, $targetType, $attribute, $token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "attrEdit", $token);

        $lt = $domOut->createAttribute("target");
        $lt->value = $target;
        $l->appendChild($lt);
        $lt = $domOut->createAttribute("targettype");
        $lt->value = $targetType;
        $l->appendChild($lt);

        $l->appendChild($attribute->toXmlElement($domOut));

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        return $attribute;
    }

    /**
     * @brief remove Attribute
     *
     * check <a href="http://www.crediosys.com/doc/api/#attrRemove" target="_blank">%Credio API attrRemove</a> for full specification
     * @param $target
     * @param $targetType
     * @param $attribute
     * @param null $token
     * @throws Exception
     * @return bool
     */
    public function attrRemove($target, $targetType, $attribute, $token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "attrRemove", $token);

        $lt = $domOut->createAttribute("target");
        $lt->value = $target;
        $l->appendChild($lt);
        $lt = $domOut->createAttribute("targettype");
        $lt->value = $targetType;
        $l->appendChild($lt);

        $l->appendChild($attribute->toXmlElement($domOut));

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        return true;
    }

    /**
     * @brief used to verify user credentials.
     *
     * check <a href="http://www.crediosys.com/doc/api/#authenticate" target="_blank">%Credio API authenticate</a> for full specification
     * @param $username
     * @param $password
     * @throws Exception
     * @return bool
     */
    public function authenticate($username, $password)
    {
        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "authenticate", NULL);

        $u = $domOut->createElement("u");
        $u->appendChild($domOut->createTextNode($username));
        $l->appendChild($u);

        $p = $domOut->createElement("p");
        $p->appendChild($domOut->createTextNode($password));
        $l->appendChild($p);

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        return true;
    }

    /**
     * @brief used to obtain Token from Credio server.
     *
     * check <a href="http://www.crediosys.com/doc/api/#getToken" target="_blank">%Credio API getToken</a> for full specification
     * @param $username - username
     * @param $password - password
     * @throws Exception
     * @return Token
     */
    public function getToken($username, $password)
    {
        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "getToken", NULL);

        $u = $domOut->createElement("u");
        $u->appendChild($domOut->createTextNode($username));
        $l->appendChild($u);

        $p = $domOut->createElement("p");
        $p->appendChild($domOut->createTextNode($password));
        $l->appendChild($p);

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        $token = Token::fromDOM($domIn);
        if ($token === false) return false;

        $this->token = $token;
        if ($this->isCookieInstance && isset($this->cookies)) {
            \setcookie($this->cookieName, \base64_encode(\serialize($this->token)), time() + 1000, '/');
        }

        return $this->token;
    }

    /**
     * @brief used to release Token from Credio server.
     *
     * check <a href="http://www.crediosys.com/doc/api/#releaseToken" target="_blank">%Credio API releaseToken</a> for full specification
     * @param $token - token to release. if not set Client::$token is used.
     * @throws Exception
     * @return boolean
     */
    public function releaseToken($token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $this->createApiElement($domOut, "releaseToken", $token);

        if ($this->isCookieInstance && isset($this->cookies))
            \setcookie($this->cookieName, NULL, -1, '/');

        $this->cachePerm = array();
        $this->cacheAttr = array();
        $this->token = NULL;

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        return true;
    }

    /**
     * @brief check whether %Token is valid or not on %Credio server.
     *
     * using isValidToken API will couse %Credio server to reset %Token expire timer.
     * check <a href="http://www.crediosys.com/doc/api/#isValidToken" target="_blank">%Credio API isValidToken</a> for full specification
     * @param[in] $token - if not present Client::$token is used.
     * @throws Exception
     * @return Boolean true if $token is valid on %Credio server.
     */
    public function isValidToken($token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $domOut->createElement("isValidToken");

        $ld = $domOut->createAttribute("domain");
        $ld->value = $this->domain;
        $l->appendChild($ld);

        $lt = $domOut->createElement("token");
        $lt->appendChild($domOut->createTextNode($token->__toString()));
        $l->appendChild($lt);

        $domOut->documentElement->appendChild($l);

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        $this->cachePerm = array();
        $this->cacheAttr = array();

        return true;
    }

    /**
     * @brief searching for Users matching pattern.
     *
     * check <a href="http://www.crediosys.com/doc/api/#userSearch" target="_blank">%Credio API userSearch</a> for full specification
     * @param $pattern - regex pattern
     * @param $token
     * @param $opt - options for search
     * @throws Exception
     * @return User[]
     */
    public function userSearch($pattern, $token = NULL, $opt = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "userSearch", $token);

        $s = $domOut->createElement("s");
        if ($opt != NULL) {
            $o = $domOut->createAttribute("opt");
            $o->value = $opt;
            $s->appendChild($o);
        }
        $s->appendChild($domOut->createTextNode($pattern));
        $l->appendChild($s);

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        $xpath = new \DOMXpath($domIn);
        $xres = $xpath->query("/credio/users/u");

        $res = array();

        for ($i = 0; $i < $xres->length; $i++) {
            $u = User::fromXElement($xres->item($i));
            array_push($res, $u);
        }

        return $res;
    }

    /**
     * @brief create new User.
     *
     * check <a href="http://www.crediosys.com/doc/api/#userInsert" target="_blank">%Credio API userInsert</a> for full specification
     * @param $username
     * @param $password
     * @param null $token
     * @throws Exception
     * @return bool | User
     */
    public function userInsert($username, $password, $token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "userInsert", $token);

        $user = new User();
        $user->uname = $username;
        $xu = $user->toXmlElement($domOut);
        $l->appendChild($xu);
        if (isset($password)) {
            $p = $domOut->createElement('p');
            $p->appendChild($domOut->createTextNode($password));
            $xu->appendChild($p);
        }

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        $xpath = new \DOMXpath($domIn);
        $xres = $xpath->query("//insertedId");

        $res = NULL;

        if ($xres->length == 1) {
            $xres = $xres->item(0);
            $user->id = $xres->nodeValue;
        }

        return $user;
    }

    /**
     * @brief change User.
     *
     * check <a href="http://www.crediosys.com/doc/api/#userEdit" target="_blank">%Credio API userEdit</a> for full specification
     * @param User $user
     * @param null $token
     * @throws Exception
     * @return bool | User
     */
    public function userEdit(User $user, $token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "userEdit", $token);

        $xu = $user->toXmlElement($domOut);
        $l->appendChild($xu);
        if (isset($password)) {
            $p = $domOut->createElement('p');
            $p->appendChild($domOut->createTextNode($password));
            $xu->appendChild($p);
        }

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        return $user;
    }

    /**
     * @brief remove User.
     *
     * check <a href="http://www.crediosys.com/doc/api/#userRemove" target="_blank">%Credio API userRemove</a> for full specification
     * @param User $user
     * @param null $token
     * @throws Exception
     * @return bool
     */
    public function userRemove(User $user, $token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "userRemove", $token);

        $l->appendChild($user->toXmlElement($domOut));

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        return true;
    }

    /**
     * @brief searching for Groups matching pattern.
     *
     * check <a href="http://www.crediosys.com/doc/api/#groupSearch" target="_blank">%Credio API groupSearch</a> for full specification
     * @param $pattern - regex pattern
     * @param $token
     * @param $opt - options for search
     * @throws Exception
     * @return Group[]
     */
    public function groupSearch($pattern, $token = NULL, $opt = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "groupSearch", $token);

        $s = $domOut->createElement("s");
        if ($opt != NULL) {
            $o = $domOut->createAttribute("opt");
            $o->value = $opt;
            $s->appendChild($o);
        }
        $s->appendChild($domOut->createTextNode($pattern));
        $l->appendChild($s);

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        $xpath = new \DOMXpath($domIn);
        $xres = $xpath->query("/credio/groups/g");

        $res = array();

        for ($i = 0; $i < $xres->length; $i++) {
            $g = Group::fromXElement($xres->item($i));
            array_push($res, $g);
        }

        return $res;
    }

    /**
     * @brief create new Group.
     *
     * check <a href="http://www.crediosys.com/doc/api/#groupInsert" target="_blank">%Credio API groupInsert</a> for full specification
     * @param string | Group $group
     * @param null $token
     * @throws Exception
     * @return bool | Group
     */
    public function groupInsert($group, $token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "groupInsert", $token);

        $obj = $group;
        if (!is_object($group) || !($group instanceof Group)) {
            $obj = new Group();
            $obj->name = $group;
        }

        $l->appendChild($obj->toXmlElement($domOut, true));

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        $xpath = new \DOMXpath($domIn);
        $xres = $xpath->query("//insertedId");

        if ($xres->length == 1) {
            $xres = $xres->item(0);
            $obj->id = $xres->nodeValue;
        }

        return $obj;
    }

    /**
     * @brief change Group.
     *
     * check <a href="http://www.crediosys.com/doc/api/#groupEdit" target="_blank">%Credio API groupEdit</a> for full specification
     * @param Group $group
     * @param null $token
     * @throws Exception
     * @return bool | Group
     */
    public function groupEdit(Group $group, $token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "groupEdit", $token);

        $l->appendChild($group->toXmlElement($domOut, true));

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        return $group;
    }

    /**
     * @brief remove Group.
     *
     * check <a href="http://www.crediosys.com/doc/api/#groupRemove" target="_blank">%Credio API groupRemove</a> for full specification
     * @param Group $group
     * @param null $token
     * @throws Exception
     * @return bool
     */
    public function groupRemove(Group $group, $token = NULL)
    {
        if (($token = $this->validateToken($token)) == NULL) return false;

        $domOut = $this->createDom();
        $l = $this->createApiElement($domOut, "groupRemove", $token);

        $l->appendChild($group->toXmlElement($domOut));

        $domIn = $this->netGetResponse($domOut);
        if ($domIn === false) return false;

        return true;
    }
}

?>
