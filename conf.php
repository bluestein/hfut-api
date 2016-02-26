<?php
/*
 * File: conf.php
 * Created by humooo.
 * Email: humooo@outlook.com
 * Date: 15-6-9
 * Time: 上午10:48
 */
//debug true:开发模式启用；false：关闭
define('DEBUG', false);

//合工大统一身份验证地址
define("HFUT_VALIDATE_URL", "http://ids1.hfut.edu.cn:81/amserver/UI/Login");

//合工大门户网站
define("HFUT_MH", "http://my.hfut.edu.cn");

//cookie 过期时间
define("COOKIE_TTL", 3600); //暂定1个小时

//cookie 存放路径
define('COOKIE_PATH', dirname(__FILE__) . '/cookiefiles/');

//模拟客户端
define('USER_AGENT', 'Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.39 Safari/537.36');

//一卡通
define("ECARD_URL_INDEX", "http://pcard.hfut.edu.cn:8080/loginpotaljz.action");
define("ECARD_URL_ACCOUNT", "http://pcard.hfut.edu.cn:8080/getAccountShow.action");

//服务器地址
define("MY_HOST", "http://121.251.19.148");
define("MY_URL_STUDY", MY_HOST . "/PadHfut/Pages/refactoring/GetStudyInfo.php");
define("MY_URL_LIBRARY", MY_HOST . "/PadHfut/Pages/refactoring/GetLibInfo.php");

//研究生系统url
define("YJS_URL_PREFIX", "http://yjsjw.hfut.edu.cn"); //前缀
define("YJS_URL_JW", YJS_URL_PREFIX . "/StuIndex.asp"); //研究生教务
define("YJS_URL_ZYXX", YJS_URL_PREFIX . "/student/asp/CxPyfaxx.asp"); //研究生培养方案信息（研究生教务系统里：专业信息）
define("YJS_URL_PYFA", YJS_URL_PREFIX . "/student/asp/CxPyfa.asp"); //研究生培养方案
define("YJS_URL_PYJH", YJS_URL_PREFIX . "/student/asp/CxPyjhkc.asp"); //研究生培养计划课程
define("YJS_URL_PYJD", YJS_URL_PREFIX . "/student/asp/Pyjdb.asp"); //研究生培养进度表
define("YJS_URL_KTBG", YJS_URL_PREFIX . "/student/asp/Ktbg.asp"); //研究生开题报告
//jwx
define("YJS_URL_BASEINFO",YJS_URL_PREFIX."/student/asp/xsjbxx.asp");//研究生个人信息
define("YJS_URL_SCHEDULE",YJS_URL_PREFIX."/student/asp/select_xkjg.asp");//研究生选课结果
define("YJS_URL_SCORE",YJS_URL_PREFIX."//student/asp/Select_Success.asp");//研究生成绩结果

//本科生系统url
define("BK_URL_PREFIX", "http://bkjw.hfut.edu.cn"); //前缀http://bkjw.hfut.edu.cn 和 http://121.251.19.51
define("BK_URL_JW", BK_URL_PREFIX . "/StuIndex.asp"); //计划查询
define("BK_URL_JHCX", BK_URL_PREFIX . "/student/asp/xqkb2.asp"); //计划查询
define("BK_URL_JHCX_XQ", BK_URL_PREFIX . "/student/asp/xqkb2_1.asp"); //计划查询-教学班详情
define("BK_URL_KCCX", BK_URL_PREFIX . "/student/asp/xqkb1.asp"); //课程查询
define("BK_URL_KCCX_XQ", BK_URL_PREFIX . "/student/asp/xqkb1_1.asp"); //课程查询-教学班详情
define("BK_URL_JXBCX", BK_URL_PREFIX . "/student/asp/jxbmdcx.asp"); //教学班查询
define("BK_URL_JXBCX_XQ", BK_URL_PREFIX . "/student/asp/jxbmdcx_1.asp"); //教学班查询-详情
//jwx
define("BK_URL_XXCX", BK_URL_PREFIX . "/student/asp/xsxxxxx.asp"); //个人信息查询
define("BK_URL_XJZC", BK_URL_PREFIX . "/student/asp/zhuce.asp"); //学籍注册信息
define("BK_URL_CJCX", BK_URL_PREFIX . "/student/asp/Select_Success.asp"); //成绩查询信息
define("BK_URL_SFCX", BK_URL_PREFIX . "/student/asp/Xfsf_Count.asp"); //收费查询信息

//图书馆系统url
define('LIB_URL_INDEX', 'http://m.lib.hfut.edu.cn');
define('LIB_URL_LOGIN', 'http://mc.lib.hfut.edu.cn/irdUser/login/opac/opacLogin.jspx');
define('LIB_URL_OPAC', 'http://mc.lib.hfut.edu.cn/cmpt/opac/opacLink.jspx?stype=1'); //回调地址
define('LIB_URL_XJ_PREFIX', 'http://210.45.242.51:8080/sms/opac/user/'); //续借url前缀

//数据库
define("MYSQL_HOST", 'YOUR_HOST'); // 修改此处
define("MYSQL_USER", 'YOUR_USER'); // 修改此处
define("MYSQL_PWD", 'YOUR_PASSWORD'); // 修改此处
define("MYSQL_DB", 'YOUR_DATABASE'); // 修改此处

