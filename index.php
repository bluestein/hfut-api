<?PHP
/* Created by humooo.
* Email: humooo@outlook.com
* Date: 15-7-15
* Time: 下午9:31
*/
include_once "YJS.php";
include_once "BKS.php";

$action = isset($_GET['action']) ? trim($_GET['action']) : null;
$user = isset($_GET['user']) ? trim($_GET['user']) : null;
$pwd = isset($_GET['pwd']) ? trim(urldecode($_GET['pwd'])) : null;


$result = null;
$flag = true;
if ($action && USER::VALIDATE_USER($user, $pwd)) {
    switch ($action) {
        case "applogin":
            $flag = false;
            if (USER::VALIDATE_LOGIN($user, $pwd)) {
                $result = array(
                    "errorno" => 888,
                    "description" => "登录成功"
                ); //此处cxp添加，暂放
            } else {
                $result = array(
                    "errorno" => -1,
                    "description" => "登录失败"
                ); //此处cxp添加，暂放
            }
            break;
        default:
            if (User::IS_YJS($user)) {
                $yjs = new YJS($user, $pwd);
                if ($yjs->isLogin()) {
                    switch ($action) {
                        //专业信息
                        //示例：http://localhost/padhfut/pages/refactoring/GetStudyInfo.php?action=zyxx&user=2014170331&pwd=
                        case 'zyxx':
                            $result = $yjs->ZYXX();
                            break;
                        //培养方案
                        case 'pyfa':
                            $result = $yjs->PYFA();
                            break;
                        //培养计划
                        case 'pyjh':
                            $result = $yjs->PYJH();
                            break;
                        //培养进度
                        case 'pyjd':
                            $result = $yjs->PYJD();
                            break;
                        //开题报告
                        case 'ktbg':
                            $result = $yjs->KTBG();
                            break;
                        //一卡通账户信息
                        // http://localhost/padhfut/pages/refactoring/GetStudyInfo.php?action=ecard&user=2014170331&pwd=
                        case 'ecard':
                            $result = $yjs->eCardAccountInfo();
                            break;
                        //--------------------------jwx---------------------------//
                        //个人信息
                        case 'grxx':
                            $result = $yjs->GRXX();
                            break;
                        //选课结果
                        case 'xkjg':
                            $result = $yjs->XKJG();
                            break;
                        //成绩信息
                        case 'cjxx':
                            $result = $yjs->CJXX();
                            break;
                        //默认
                        default:
                            $flag = false;
                            $result = array(
                                "errorno" => 3,
                                "description" => "暂时无此操作，请等待更新"
                            );
                    }
                } else {
                    $flag = false;
                    if ($action == "applogin") {
                        $result = array(
                            "errorno" => -1,
                            "description" => "登录失败，用户名或密码问题"
                        );
                    } else {
                        $result = array(
                            "errorno" => 2,
                            "description" => "1. 服务器 cookie 问题，请确保对路径：" . USER::COOKIE_PATH . " 有读写权限<br/>2. 用户名密码问题，请确保正确。"
                        );
                    }
                }
            } else {
                $bks = new BKS($user, $pwd);

                $result = null;
                if ($bks->isLogin()) {
                    switch ($action) {
                        //计划查询
                        //示例数据 xqdm=001&kclxdm=1&zydm=0120020101
                        //示例：http://localhost/padhfut/pages/refactoring/GetStudyInfo.php?action=jhcx&user=2012211617&pwd=baijie/123&xqdm=001&kclxdm=1&zydm=0120020101
                        case "jhcx":
                            $xqdm = isset($_GET['xqdm']) ? $_GET['xqdm'] : null; //学期代码
                            $kclxdm = isset($_GET['kclxdm']) ? $_GET['kclxdm'] : null; //课程类型代码
                            $zydm = isset($_GET['zydm']) ? $_GET['zydm'] : null; //专业代码

                            if ($xqdm && $kclxdm && $zydm) {
                                $result = $bks->JHCX($xqdm, $kclxdm, $zydm);
                            } else {
                                $flag = false;
                                $result = array(
                                    'errorno' => 4,
                                    'description' => "计划查询，需要提供的参数：学期代码（xqdm）、课程类型代码（kclxdm）、专业代码（zydm）"
                                );
                            }
                            break;

                        //计划查询详情
                        //示例：http://localhost/PadHfut/Pages/refactoring/GetStudyInfo.php?action=jhcxxq&user=2013211881&pwd=cp123456&xqdm=001&kcdm=02003310
                        case "jhcxxq":
                            $xqdm = isset($_GET['xqdm']) ? $_GET['xqdm'] : null; //学期代码
                            $kcdm = isset($_GET['kcdm']) ? $_GET['kcdm'] : null; //课程代码

                            if ($xqdm && $kcdm) {
                                $result = $bks->JHCXXQ($xqdm, $kcdm);
                            } else {
                                $flag = false;
                                $result = array(
                                    'errorno' => 4,
                                    'description' => "计划查询-教学班详情，需要提供的参数：学期代码（xqdm）、课程代码（kcdm）"
                                );
                            }
                            break;

                        //课程查询
                        //说明：kcdm,kcmc两者至少有1个，如果有kcdm，kcmc忽略不计
                        //示例数据 xqdm=001&kcdm=10000110&kcmc=%B8%DF%B5%C8%B4%FA%CA%FD，其中 %B8%DF%B5%C8%B4%FA%CA%FD 表示‘高等代数’
                        //示例：http://localhost/PadHfut/Pages/refactoring/GetStudyInfo.php?action=kccx&user=2013211881&pwd=cp123456&xqdm=001&kcdm=10000110&kcmc=%B8%DF%B5%C8%B4%FA%CA%FD
                        case "kccx":
                            $xqdm = isset($_GET['xqdm']) ? $_GET['xqdm'] : null; //学期代码
                            $kcdm = isset($_GET['kcdm']) ? $_GET['kcdm'] : null; //课程代码
                            $kcmc = isset($_GET['kcmc']) ? urldecode($_GET['kcmc']) : null; //课程名称
                            $kcmc = mb_convert_encoding($kcmc, 'gbk', 'utf-8'); //转换成gbk编码，不转换查不到数据

                            if (($xqdm && $kcdm) || ($xqdm && $kcmc) || ($xqdm && $kcdm && $kcmc)) {
                                $result = $bks->KCCX($xqdm, $kcdm, $kcmc);
                            } else {
                                $flag = false;
                                $result = array(
                                    'errorno' => 4,
                                    'description' => "课程查询，需要提供的参数：学期代码（xqdm）和课程代码（kcdm）或课程名称（kcmc）。即，xqdm必须，但kcdm,kcmc两者至少有1个"
                                );
                            }
                            break;

                        //课程查询详情
                        //说明：kcdm,kcmc两者至少有1个，如果有kcdm，kcmc忽略不计
                        //示例数据 xqdm=001&kcdm=10000110&jxbh=0000
                        //示例：http://localhost/PadHfut/Pages/refactoring/GetStudyInfo.php?action=kccxxq&user=2013211881&pwd=cp123456&xqdm=001&kcdm=10000110&jxbh=0001
                        case "kccxxq":
                            $xqdm = isset($_GET['xqdm']) ? $_GET['xqdm'] : null; //学期代码
                            $kcdm = isset($_GET['kcdm']) ? $_GET['kcdm'] : null; //课程代码
                            $jxbh = isset($_GET['jxbh']) ? $_GET['jxbh'] : null; //教学班号

                            if ($xqdm && $kcdm && $jxbh) {
                                $result = $bks->KCCXXQ($xqdm, $kcdm, $jxbh);
                            } else {
                                $flag = false;
                                $result = array(
                                    'errorno' => 4,
                                    'description' => "课程查询-教学班详情，需要提供的参数：学期代码（xqdm）、课程代码（kcdm）教学班号（jxbh）。即，xqdm必须，但kcdm,kcmc两者至少有1个"
                                );
                            }
                            break;

                        //教学班查询详情
                        //示例数据 xqdm=001&kcdm=10000110&jxbh=0000
                        //示例：http://localhost/PadHfut/Pages/refactoring/GetStudyInfo.php?action=jxbcxxq&user=2012211617&pwd=baijie/123&xqdm=026&kcdm=0521122B&jxbh=0001
                        case "jxbcxxq":
                            $xqdm = isset($_GET['xqdm']) ? $_GET['xqdm'] : null; //学期代码
                            $kcdm = isset($_GET['kcdm']) ? $_GET['kcdm'] : null; //课程代码
                            $jxbh = isset($_GET['jxbh']) ? $_GET['jxbh'] : null; //教学班号

                            if ($xqdm && $kcdm && $jxbh) {
                                $result = $bks->JXBCXXQ($xqdm, $kcdm, $jxbh);
                            } else {
                                $flag = false;
                                $result = array(
                                    'errorno' => 4,
                                    'description' => "教学班查询-教学班详情，需要提供的参数：学期代码（xqdm）、课程代码（kcdm）教学班号（jxbh）"
                                );
                            }
                            break;

                        //教学班查询
                        //示例：http://localhost/PadHfut/Pages/refactoring/GetStudyInfo.php?action=jxbcx&user=2013211881&pwd=cp123456
                        case "jxbcx":
                            $result = $bks->JXBCX();
                            break;

                        case 'ecard':
                            $result = $bks->eCardAccountInfo();
                            break;

                        //-------------------------------------jwx---------------------------//
                        //个人信息
                        case 'grxx':
                            $result = $bks->GRXX();
                            break;
                        //学籍注册
                        case 'xjzc':
                            $result = $bks->XJZC();
                            break;
                        //成绩信息
                        case 'cjxx':
                            $result = $bks->CJXX();

                            //print_r($result);
                            //exit();
                            break;
                        //收费查询
                        case 'sfcx':
                            $result = $bks->SFCX();
                            break;

                        //默认
                        default:
                            $flag = false;
                            $result = array(
                                "errorno" => 3,
                                "description" => "暂时无此操作：" . $action . "，请等待更新"
                            );
                    }
                } else {
                    $flag = false;
                    if ($action == "applogin") {
                        $result = array(
                            "errorno" => -1,
                            "description" => "登录失败，用户名或密码问题"
                        );
                    } else {
                        $result = array(
                            "errorno" => 2,
                            "description" => "1. 服务器 cookie 问题，请确保对路径：" . USER::COOKIE_PATH . " 有读写权限<br/>2. 用户名密码问题，请确保正确。"
                        );
                    }
                }
            }
    }
} else {
    $flag = false;
    $result = array(
        "errorno" => 1,
        "description" => "请检查下列参数是否都正常：1、操作名（action）非空；2、用户名（user）非空； 3、密码（pwd）非空。"
    );
}

if (!$result && $flag) {
    $result = array(
        'errorno' => 5,
        'description' => "没有结果"
    );
} else if ($result && $flag) {
    $result = array(
        'errorno' => 0,
        'description' => "正常",
        'data' => $result
    );
}

if (DEBUG) {
    print_r($result);
} else {
    $result = json_encode($result);
    echo $result;
    //print_r($result);
}