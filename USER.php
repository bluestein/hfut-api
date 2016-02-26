<?php
/*
 * Created by humooo.
 * Email: humooo@outlook.com
 * Date: 15-7-25
 * Time: 下午6:15
 */
include_once "conf.php";

abstract class USER
{
    const COOKIE_PATH = COOKIE_PATH;
    const USER_AGENT = USER_AGENT;
    const DEBUG = DEBUG;
    const COOKIE_TTL = COOKIE_TTL; //过期时间
    const ECARD_URL_INDEX = ECARD_URL_INDEX; //一卡通
    const ECARD_URL_ACCOUNT = ECARD_URL_ACCOUNT;
    private $user;
    private $password;
    protected $cookie_validate;
    protected $cookie_jw;
    protected $cookie_ecard;
    //研究生与本科生不同
    protected $url_jw;

    protected $replace_patten = array(
        '/\s+/', //去掉空格
        '/<table[^>]*?>/i', '/<\/table>/i', //去掉table样式
        '/<tr[^>]*?>/i', '/<\/tr>/i', //去掉tr样式
        '/<td[^>]*?>/i', '/<\/td>/i', //去掉td样式
        '/\\r\\n|\\n/' //去掉换行\r\n
    );
    protected $replacement = array(
        '',
        '<table>', '</table>',
        '<tr>', '</tr>',
        '<td>', '</td>',
        ''
    );

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->cookie_jw = User::COOKIE_PATH . $user . "_jw.txt";
        $this->cookie_validate = User::COOKIE_PATH . $user . "_validate.txt";
        $this->cookie_ecard = User::COOKIE_PATH . $user . "_ecard.txt";
    }

    public static function VALIDATE_LOGIN($user, $password)
{
    $post = array(
        'IDToken0' => '',
        'IDToken1' => $user,
        'IDToken2' => $password,
        'IDButton' => 'Submit',
        'goto' => '',
        'encoded' => 'false',
        'inputCode' => '',
        'gx_charset' => 'UTF-8'
    );
    $ch = curl_init(HFUT_VALIDATE_URL);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $result = curl_exec($ch);
    curl_close($ch);

    $patten = array(
        "/<body[^>]+?>/i",
        "/\s+/i"
    );
    $replacement = array(
        "<body>",
        ""
    );
    $result = preg_replace($patten, $replacement, $result);
    preg_match("/(?<=<body>).*?(?=<\/body>)/", $result, $result);

    if (!$result[0]) {
        return true;
    }
    return false;
}

    //验证是否为研究生
    public static function IS_YJS($user)
    {
        $length = strlen($user);
        if (10 == $length) {
            if (1 == intval(substr($user, 4, 1)))
                return true;
        }
        return false;
    }

    //验证用户合法
    //TODO 加密验证等等复杂的验证方法
    public static function VALIDATE_USER($user, $pwd)
    {
        $result = false;
        if ($user && $pwd) {
            $result = true;
        }
        return $result;
    }


    public static function setCookie($url, $cookie_save_to_path, $cookie_get_path = null, $post_array = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, User::USER_AGENT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($post_array && is_array($post_array)) {
            if (DEBUG) {
                echo "@setCookie，使用 post<br/>";
            }
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_array));
        }
        if ($cookie_get_path) {
            if (DEBUG) {
                echo "@setCookie，使用 cookie<br/>";
            }
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_get_path);
        }
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_save_to_path);
        $result = curl_exec($ch);
        curl_close($ch);
        if (!$result || !file_exists($cookie_save_to_path)) {
            return false;
        }
        return true;
    }

    public static function getData($url, $cookie_get_path = null, $post_array = null, $header = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, User::USER_AGENT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //跟随链接
        if ($header) {
            if (DEBUG) {
                echo "@getData，使用header<br/>";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if ($cookie_get_path) {
            if (DEBUG) {
                echo "@getData，使用 cookie<br/>";
            }
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_get_path);
        }
        if ($post_array) {
            if (DEBUG) {
                echo "@getData，使用 post <br/>";
            }
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_array));
        }
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function isLogin()
    {
        //先检查身份验证 cookie，再检查教务 cookie，最后是一卡通
        if ($this->checkValidateCookie() && $this->checkJWCookie() && $this->checkECardCookie()) {
            return true;
        }
        return false;
    }


    public function showArgs()
    {
        $args = 'jw url :' . $this->url_jw . '. user: ' . $this->user . '. pwd: ' . $this->password;
        $args .= '. cookie jw: ' . $this->cookie_jw;
        echo $args;
    }

    public function eCardAccountInfo()
    {
        $post = array(
            'ok' => ''
        );
        $result = $this->getECardData(USER::ECARD_URL_ACCOUNT, $post);
        $result = json_decode($result, true);
        $accountInfo = $result['userAccountList'][0];
        unset($result['useraccountheader']);
        unset($result['userAccinfoList']);
        unset($result['userAccountList']);
        $result = $result + $accountInfo;

        $fields = array('name', 'student_no', 'account_no', 'card_type', 'account_balance', 'card_balance', 'tran_balance', 'frozen_state', 'loss_state', 'bank_no');
        $temp = array();
        $i = 0;
        foreach ($result as $item) {
            $temp[$fields[$i]] = $item;
            $i++;
        }
        $result = $temp;

        return $result;
    }


    /**
     * 获取一卡通网页数据
     * @param $url
     * @param null $post_array
     * @return mixed|null|string
     */
    protected function getECardData($url, $post_array = null)
    {
        $result = null;
        $header = array(
            'Host: pcard.hfut.edu.cn:8080'
        );
        if ($post_array && is_array($post_array)) {
            $this->debug("@getECardData，post 方法获取数据<br/>");
            $result = USER::getData($url, $this->cookie_ecard, $post_array, $header);
        } else {
            $this->debug("@getECardData，get 方法获取数据<br/>");
            $result = USER::getData($url, $this->cookie_ecard, null, $header);
        }
        if ($result) {
            $this->debug("正在读取一卡通的数据...<br/><br/>");
        }
        return $result;
    }

    /**
     * 获取门户网页数据
     * @param $url
     * @return mixed
     */
    protected function getMHData($url)
    {
        $result = USER::getData($url, $this->cookie_validate);
        $this->debug("正在读取门户页的数据...<br/><br/>");
        return $result;
    }

    /**
     * 获取教务网页数据
     * @param $url
     * @param null $post_array
     * @return mixed|null|string
     */
    protected function getJWData($url, $post_array = null)
    {
        $result = null;
        if ($post_array && is_array($post_array)) {
            $this->debug("getJWData，post 方法获取数据<br/>");
            $result = USER::getData($url, $this->cookie_jw, $post_array);
        } else {
            $this->debug("getJWData，get 方法获取数据<br/>");
            $result = USER::getData($url, $this->cookie_jw);
        }
        if ($result) {
            $result = iconv("GB2312", "utf-8//IGNORE", $result);
            $this->debug("正在读取教务页的数据...<br/><br/>");
        }
        return $result;
    }

    protected function debug($content)
    {
        if (User::DEBUG) {
            echo $content;
        }
    }

    /**
     * 设置身份验证cookie
     */
    private function setValidateCookie()
    {
        $post = array(
            'IDToken0' => '',
            'IDToken1' => $this->user,
            'IDToken2' => $this->password,
            'IDButton' => 'Submit',
            'goto' => '',
            'encoded' => 'false',
            'inputCode' => '',
            'gx_charset' => 'UTF-8'
        );
        $result = USER::setCookie(HFUT_VALIDATE_URL, $this->cookie_validate, null, $post);

        $patten = array(
            "/<body[^>]+?>/i",
            "/\s+/i"
        );
        $replacement = array(
            "<body>",
            ""
        );
        $result = preg_replace($patten, $replacement, $result);
        preg_match("/(?<=<body>).*?(?=<\/body>)/", $result, $result);

        if ($result[0] || !file_exists($this->cookie_validate)) {
            return false;
        }
        return true;
    }

    /**
     * 设置教务页的cookie，访问其他页时需要它，该cookie一小时过期？
     * @return bool
     */
    private function setJWCookie()
    {
        $result = USER::setCookie($this->url_jw, $this->cookie_jw, $this->cookie_validate);

        if (!$result || !file_exists($this->cookie_jw)) {
            return false;
        }
        return true;
    }

    private function setECardCookie()
    {
        $result = USER::setCookie(USER::ECARD_URL_INDEX, $this->cookie_ecard, $this->cookie_validate);

        if (!$result || !file_exists($this->cookie_ecard)) {
            return false;
        }
        return true;
    }


    /**
     * 检查validate cookie
     * @return bool
     */
    private function checkValidateCookie()
    {
        $debug = null;
        if (file_exists($this->cookie_validate)) {
            $debug .= "身份验证 cookie 存在。<br/>";
            $time = filemtime($this->cookie_validate);
            if (time() - $time < USER::COOKIE_TTL) {
                $debug .= "身份验证 cookie 有效。<br/>";
                $this->debug($debug);
                return true;
            }
            $debug .= "身份验证 cookie 已超时，重新生成。<br/>";
            if (!$this->setValidateCookie()) {
                $debug .= "生成身份验证cookie失败。<br />";
                $this->debug($debug);
                return false;
            }
            $debug .= "生成身份验证cookie成功。<br />";
            $this->debug($debug);
            return true;
        }
        $debug .= "没有身份验证 cookie，重新生成。<br/>";
        if (!$this->setValidateCookie()) {
            $debug .= "身份验证 cookie 生成失败<br />";
            $this->debug($debug);
            return false;
        }
        $debug .= "身份验证 cookie 生成成功，地址：" . $this->cookie_validate . "<br />";
        $this->debug($debug);
        return true;
    }

    /**
     * 检查教务cookie
     * @return bool
     */
    private function checkJWCookie()
    {
        $debug = null;
        if (file_exists($this->cookie_jw)) {
            $debug .= "教务 cookie 存在。<br/>";
            $time = filemtime($this->cookie_jw);
            if (time() - $time < USER::COOKIE_TTL) {
                $debug .= "教务 cookie 有效。<br/>";
                $this->debug($debug);
                return true;
            }
            $debug .= "教务 cookie 已超时，重新生成。<br/>";
            if (!$this->setJWCookie()) {
                $debug .= "生成教务 cookie失败。<br />";
                $this->debug($debug);
                return false;
            }
            $debug .= "生成教务 cookie 成功。<br />";
            $this->debug($debug);
            return true;
        }
        $debug .= "没有教务 cookie，重新生成。<br/>";
        if (!$this->setJWCookie()) {
            $debug .= "教务 cookie 生成失败<br />";
            $this->debug($debug);
            return false;
        }
        $debug .= "教务 cookie 生成成功，地址：" . $this->cookie_jw . "<br />";
        $this->debug($debug);
        return true;
    }

    /**
     * 检查一卡通cookie
     * @return bool
     */
    private function checkECardCookie()
    {
        $debug = null;
        if (file_exists($this->cookie_ecard)) {
            $debug .= "一卡通 cookie 存在。<br/>";
            $time = filemtime($this->cookie_ecard);
            if (time() - $time < USER::COOKIE_TTL) {
                $debug .= "一卡通 cookie 有效。<br/>";
                $this->debug($debug);
                return true;
            }
            $debug .= "一卡通 cookie 已超时，重新生成。<br/>";
            if (!$this->setECardCookie()) {
                $debug .= "生成一卡通 cookie失败。<br />";
                $this->debug($debug);
                return false;
            }
            $debug .= "生成一卡通 cookie 成功。<br />";
            $this->debug($debug);
            return true;
        }
        $debug .= "没有一卡通 cookie，重新生成。<br/>";
        if (!$this->setECardCookie()) {
            $debug .= "一卡通 cookie 生成失败<br />";
            $this->debug($debug);
            return false;
        }
        $debug .= "一卡通 cookie 生成成功，地址：" . $this->cookie_ecard . "<br />";
        $this->debug($debug);
        return true;
    }



    // 参数解释
    // $string： 明文 或 密文
    // $key： 密匙
    // $operation：true表示加密（ENCODE），其他表示解密（DECODE）,
    // $expiry：密文有效期
    public static function HFUTCrypt($string, $key = null, $operation = null, $expiry = 0)
    {
        $operation = $operation === true ? 'ENCODE' : 'DECODE';
        $key = $key ? $key : 'the key';
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $ckey_length = 4;
        // 密匙
        $key = md5($key);
        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else if ($operation == 'ENCODE') {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc . str_replace('=', '', base64_encode($result));
        } else {
            return '';
        }
    }

}