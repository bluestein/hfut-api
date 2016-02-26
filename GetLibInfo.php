<?php
/* File: GetLibInfo.php
 * Created by humooo.
 * Email: humooo@outlook.com
 * Date: 15-6-20
 * Time: 下午11:16
 */
require_once('conf.php');

$user = $_GET['user'];
$pwd = $_GET['pwd'];

define('COOKIE_LIB_INDEX', COOKIE_PATH . $user . '_lib_index.txt');
define('COOKIE_LIB', COOKIE_PATH . $user . '_lib.txt');
define('COOKIE_LIB_TIME', COOKIE_PATH . $user . '_lib_time.txt');
define('USER', $user);
define('PWD', $pwd);

//-------------------------------------test area-------------------------------//

login();
$res = getOPAC();
if ($res) {
    //print_r($res);
    echo json_encode($res);

} else
    echo '你现在没有借阅图书！';

//-------------------------------------test area-------------------------------//

$replace_patten = array(
    '/\s+/', //去掉空格
    '/<table[^>]*?>/i', '/<\/table>/i', //去掉table样式
    '/<tr[^>]*?>/i', '/<\/tr>/i', //去掉tr样式
    '/<td[^>]*?>/i', '/<\/td>/i', //去掉td样式
    '/\\r\\n|\\n/' //去掉换行\r\n
);
$replacement = array(
    '',
    '<table>', '</table>',
    '<tr>', '</tr>',
    '<td>', '</td>',
    ''
);

/**
 * 借阅信息
 * @return array
 */
function getOPAC()
{
    //各个字段名
    $lib_fields = array(
        'num', 'person', 'situation',
        'conclusion', 'suggestion'
    );

    $info = getPageData(LIB_URL_OPAC);

    preg_match('/(?<=我的借阅：)\d+?(?=本)/', $info, $num);
    preg_match_all('/<table[^>]*?>[\s\S]*?<\/table>/i', $info, $match);

    $info = array_chunk($match[0], 2);
    $books = array();
    if ($info) {
        $books['numOfBooks'] = $num[0];
        foreach ($info as $item) {
            $item_title = preg_replace(array('/<span><\/span>/', '/\s/'), array('', ''), $item[0]);
            preg_match_all('/(?<=<thclass="sheetHd">)[\s\S]*?(?=<\/th>)|(?<=<ahref=").*?(?=")/', $item_title, $title);
            $item_content = preg_replace('/<th[^>]*?>/', '<th>', $item[1]);
            preg_match_all('/(?<=<th>).*?(?=<\/th>)|(?<=<td>).*?(?=<\/td>)/', $item_content, $content);
            $books[] = array_merge($title[0], $content[0]);
        }
    }
    //todo 细化books
    return $books;
}

/**
 * 网页数据
 * @param $url
 * @return mixed
 */
function getPageData($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //跟随链接
    curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE_LIB);
    $return = curl_exec($ch);
    curl_close($ch);
    if (DEBUG) {
        echo "正在取数据...<br/><br/>";
    }
    return $return;
}


function login()
{
    $debug = null;
    if (!file_exists(COOKIE_LIB_INDEX)) {
        $debug .= "没有index cookie，重新生成。<br/>";
        setIndexCookie();
    }
    $debug .= "index cookie存在。<br/>";
    if (file_exists(COOKIE_LIB_TIME)) {
        $debug .= "lib cookie存在。<br/>";
        $time = intval(file_get_contents(COOKIE_LIB_TIME));
        if (time() - $time < 3600) {
            $debug .= "lib cookie有效。<br/>";
            if (DEBUG) {
                echo $debug;
            }
            return true;
        }
        $debug .= "lib cookie已超时，重新生成。<br/>";
    } else {
        $debug .= "lib cookie不存在，重新生成。<br/>";
        setLoginCookie();
    }
    if (DEBUG) {
        echo $debug;
    }
    return false;
}

function setLoginCookie()
{
    $post = array(
        'backurl' => "", //LIB_URL_OPAC, //回调地址
        'schoolid' => 482,
        'userType' => 0,
        'username' => USER,
        'password' => PWD
    );
    $ch = curl_init(LIB_URL_LOGIN);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE_LIB_INDEX);
    curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE_LIB);
    curl_exec($ch);
    curl_close($ch);
    file_put_contents(COOKIE_LIB_TIME, time());
}

/**
 * 设置初始页的cookie，访问其他页时需要它，该cookie可以永久不变
 */
function setIndexCookie()
{
    $ch = curl_init(LIB_URL_INDEX);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE_LIB_INDEX);
    curl_exec($ch);
    curl_close($ch);
}