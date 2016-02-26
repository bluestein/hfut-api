<?php
/*Email: humooo@outlook.com
* Date: 15-7-16
* Time: 上午11:22
 */
include_once "USER.php";

class BKS extends USER
{
    public function __construct($user, $password)
    {
        parent::__construct($user, $password);
        $this->url_jw = BK_URL_JW;
    }

    /**
     * 计划查询
     * 示例数据 $xqdm='001'; $kclxdm'1'; $zydm='0120020101'
     * @param $xqdm [学期代码]
     * @param $kclxdm [课程类型代码]
     * @param $zydm [专业代码]
     * @return mixed|null|string
     */
    public function JHCX($xqdm, $kclxdm, $zydm)
    {
        $postData = array(
            'xqdm' => $xqdm,
            'kclxdm' => $kclxdm,
            'ccjbyxzy' => $zydm,
        );

        //$bks_fields = array('no','kc_num','kc_name','credit','kc_period','kc_department');

        array_push($this->replace_patten, '/<font[^>]*>/i', '/<\/font>/i');
        array_push($this->replacement, '', '');

        $info = $this->getJWData(BK_URL_JHCX, $postData);
        preg_match('/<table width="650"[^>]+>[\s\S]*<\/table>/i', $info, $info);
        $info = preg_replace($this->replace_patten, $this->replacement, $info);
        $info = $info[0];
        $info = preg_replace(array('/<ahref="javascript:win_open_kc[^>]+>/', '/<\/a>/'), array('', ''), $info);
        preg_match_all('/(?<=<td>).*?(?=<\/td>)/', $info, $res);

        $result = array();
        if ($res) {
            $res = $res[0];
            $term = $res[0];
            for ($i = 0; $i < 7; $i++) {
                unset($res[$i]);
            }
            if ($res) {
                $temp = array_chunk($res, 6);
                for ($i = 0; $i < sizeof($temp); $i++) {
                    $kcdm = $temp[$i][1];
                    $result[$i]['kc_num'] = $kcdm;
                    $result[$i]['kc_name'] = $temp[$i][2];
                    $result[$i]['credit'] = $temp[$i][3];
                    $result[$i]['kc_period'] = $temp[$i][4];
                    $result[$i]['kc_department'] = $temp[$i][5];
                    $variables = array('xqdm' => $xqdm, 'kcdm' => $kcdm);
                    $result[$i]['variables'] = $variables;
                }
                $result['term'] = $term;
            }
        }
        return $result;
    }

    //计划查询详情--教学班详情
    public function JHCXXQ($xqdm, $kcdm)
    {
        array_push($this->replace_patten, '/<font[^>]*>/i', '/<\/font>/i', '/<ahref="javascript[^>]+>/i', '/<\/a>/i', '/<td><\/td>/');
        array_push($this->replacement, '', '', '', '', '<td> </td>');

        $url = BK_URL_JHCX_XQ . "?xqdm=" . $xqdm . "&kcdm=" . $kcdm;
        $info = $this->getJWData($url);
        preg_match('/<table width="630"[^>]+>[\s\S]*<\/table>/i', $info, $info);
        $info = preg_replace($this->replace_patten, $this->replacement, $info);
        $info = $info[0];
        preg_match_all('/(?<=<td>).*?(?=<\/td>)/', $info, $res);

        $result = null;
        if ($res) {
            $res = $res[0];
            $term = $res[0];
            for ($i = 0; $i < 8; $i++) {
                unset($res[$i]);
            }
            if ($res) {
                $result = array_chunk($res, 7);
                $result['term'] = $term;
            }
        }
        return $result;
    }

    //课程查询
    //kcdm,kcmc两者至少有1个，如果有kcdm，kcmc忽略不计
    public function KCCX($xqdm, $kcdm = null, $kcmc = null)
    {

        $postData = array(
            'xqdm' => $xqdm,
            'kcdm' => $kcdm,
            'kcmc' => $kcmc
        );

        array_push($this->replace_patten, '/<font[^>]*>/i', '/<\/font>/i');
        array_push($this->replacement, '', '');

        $info = $this->getJWData(BK_URL_KCCX, $postData);
        $source = $info;
        preg_match('/<table width="650"[^>]+>[\s\S]*<\/table>/i', $info, $info);
        $info = preg_replace($this->replace_patten, $this->replacement, $info);
        $info = $info[0];
        $info = preg_replace(array('/<ahref="javascript:win_open_kc[^>]+>/', '/<\/a>/', '/<td><\/td>/'), array('', '', '<td> </td>'), $info);
        preg_match_all('/(?<=<td>).*?(?=<\/td>)/', $info, $res);

        $result = array();
        if ($res) {
            $res = $res[0];
            $term = $res[0];
            for ($i = 0; $i < 8; $i++) {
                unset($res[$i]);
            }
            if ($res) {
                $temp = array_chunk($res, 7);
                for ($i = 0; $i < sizeof($temp); $i++) {
                    $kcdm = $temp[$i][1];
                    $jxbh = $temp[$i][3];
                    $result[$i]['kc_name'] = $temp[$i][2];
                    $result[$i]['class_capacity'] = $temp[$i][4];
                    $result[$i]['teacher'] = $temp[$i][5];
                    $result[$i]['kc_type'] = $temp[$i][6];
                    $result[$i]['kc_num'] = $kcdm;
                    $result[$i]['class_num'] = $jxbh;
                    $variables = array('xqdm' => $xqdm, 'kcdm' => $kcdm, 'jxbh' => $jxbh);
                    $result[$i]['variables'] = $variables;
                }
                $result['term'] = $term;
            }
        }
        if (DEBUG) {
            $result['debug'] = array($xqdm, $kcdm, $kcmc, mb_detect_encoding($kcmc));
        }

        return $result;
    }

    //课程查询详情--教学班详情
    public function KCCXXQ($xqdm, $kcdm, $jxbh)
    {
        array_push($this->replace_patten, '/<td><tr><td>/', '/<\/td><\/tr><\/td>/', '/&nbsp;/', '/<font[^>]*>/i', '/<\/font>/i', '/<divalign="center">/i', '/<\/div>/i', '/<td><\/td>/');
        array_push($this->replacement, '<tr><td>', '<\td><\tr>', '', '', '', '', '', '<td> </td>');

        $url = BK_URL_KCCX_XQ . "?xqdm=" . $xqdm . "&kcdm=" . $kcdm . '&jxbh=' . $jxbh;
        $info = $this->getJWData($url);
        preg_match('/<table width="600"[^>]+>[\s\S]*<\/table>/i', $info, $info);
        $info = preg_replace($this->replace_patten, $this->replacement, $info);
        $info = $info[0];
        preg_match_all('/(?<=<td>).*?(?=<\/td>)/', $info, $res);
        $result = null;
        //todo 详细
        if (1 != count($res[0])) {
            $result = $res;
        }

        return $result;
    }

    //教学班查询
    public function JXBCX()
    {
        array_push($this->replace_patten, '/<td>&nbsp;<\/td>/i', '/<input[^>]*?>/i', '/<img[^>]+?>/i', '/<td><\/td>/');
        array_push($this->replacement, '', '', '', '');

        $info = $this->getJWData(BK_URL_JXBCX);
        preg_match('/<table width="600"[^>]+>[\s\S]*<\/table>/i', $info, $info);
        preg_match('/(?<=<INPUT type="hidden" name="xqdm" value=)\d{3}(?=>)/i', $info[0], $xqdm);
        $xqdm = $xqdm[0];
        $info = preg_replace($this->replace_patten, $this->replacement, $info);
        $info = $info[0];

        preg_match_all('/(?<=<td>).*?(?=<\/td>)/', $info, $res);
        $result = array();
        if ($res) {
            $res = $res[0];
            for ($i = 0; $i < 3; $i++) {
                unset($res[$i]);
            }
            if ($res && 1 != count($res)) {
                $temp = array_chunk($res, 3);
                for ($i = 0; $i < sizeof($temp); $i++) {
                    $kcdm = $temp[$i][0];
                    $jxbh = $temp[$i][2];
                    $result[$i]['kc_name'] = $temp[$i][1];
                    $result[$i]['kc_num'] = $kcdm;
                    $result[$i]['class_num'] = $jxbh;
                    $variables = array('xqdm' => $xqdm, 'kcdm' => $kcdm, 'jxbh' => $jxbh);
                    $result[$i]['variables'] = $variables;
                }
            }
        }

        return $result;
    }

    //教学班查询详情
    public function JXBCXXQ($xqdm, $kcdm, $jxbh)
    {
        $postData = array(
            'xqdm' => $xqdm,
            'kcdm' => $kcdm,
            'jxbh' => $jxbh,
            'button' => "%B2%E9%D4%C4" //urlencode("查阅")
        );

        array_push($this->replace_patten, '/<font[^>]*>/i', '/<\/font>/i', '/<td>&nbsp;<\/td>/i');
        array_push($this->replacement, '', '', '');

        $info = $this->getJWData(BK_URL_JXBCX_XQ, $postData);
        preg_match('/<table width="500"[^>]+>[\s\S]*<\/table>/i', $info, $info);
        $info = preg_replace($this->replace_patten, $this->replacement, $info);
        $info = $info[0];
        preg_match_all('/(?<=<td>).*?(?=<\/td>)/', $info, $res);

        $result = array();

        if ($res) {
            $res = $res[0];
            $term = $res[0];
            $class = $res[1];

            $result['term'] = $term;
            $result['class'] = $class;

            for ($i = 0; $i < 5; $i++) {
                unset($res[$i]);
            }
            if ($res) {
                $temp = array_chunk($res, 3);
                for ($j = 0; $j < sizeof($temp); $j++) {
                    $result[$j]['stu_num'] = $temp[$j][1];
                    $result[$j]['stu_name'] = $temp[$j][2];
                }
            }
        }

        return $result;
    }

    //---------------------以下为jwx代码--------------------------------------//
    /**
     *个人信息查询
     */
    public function GRXX()
    {
        $info = $this->getJWData(BK_URL_XXCX);
        $info = preg_replace(array('/<td[^>]*>/', '/&nbsp;/', '/\n/', '/学号:/', '/姓名:/', '/性别:/', '/能否选课:/ ', '/注册状态:/', '/学籍状态:/'), array('<td>', '', '', '', '', '', '', '', ''), $info);
        preg_match_all('/(?<=<td>).*?(?=<\/td>)/', $info, $matches);
        $arr = $matches[0];
        $arr1 = array();
        if (array_filter($arr)) {
            $arr1['sno'] = trim($arr[0]);
            $arr1['name'] = trim($arr[1]);
            $arr1['sex'] = trim($arr[2]);
            $arr1['select'] = trim($arr[4]);
            $arr1['zc_state'] = trim($arr[5]);
            $arr1['xj_state'] = $arr[6];
            $arr1['college'] = $arr[10];
            $arr1['major'] = $arr[11];
            $arr1['class'] = $arr[12];
            $arr1['exam_num'] = trim($arr[16]);
            $arr1['nation'] = trim($arr[17]);
            $arr1['birth'] = trim($arr[18]);
            $arr1['stuplace'] = trim($arr[23]);
            $arr1['poli'] = trim($arr[24]);
            $arr1['ID'] = trim($arr[25]);
            $arr1['marriage'] = trim($arr[26]);
            $arr1['fami_address'] = trim($arr[31]);
            $arr1['fami_contact'] = trim($arr[32]);
            $arr1['highschool'] = trim($arr[33]);
            $arr1['native'] = trim($arr[34]);
            $arr1['contact'] = trim($arr[39]);
            $arr1['forelanguage'] = trim(strip_tags($arr[43]));
        }
        return $arr1;
    }

    /**
     *学籍注册
     **/
    public function XJZC()
    {
        $info = $this->getJWData(BK_URL_XJZC);
        $info = preg_replace(array('/\n/', '/&nbsp;/'), array('', ''), $info);
        // print_r($info);
        // exit();
        preg_match('/(?<=<TD\sbgcolor=\"#D6D3CE\"\swidth=\"460\"\sclass=\"td01\">).*(?=<\/TD>)/', $info, $res);
        $res1 = array();
        $res2 = array();
        $res3 = array();
        $res4 = array();
        $res5 = array();
        $res_new = array();
        preg_match_all('/(?<= 注册学期:).*?(?=<\/br><\/br>)/', $res[0], $res1);
        preg_match_all('/(?<=注册时间:).*?(?=<\/br><\/br>)/', $res[0], $res2);
        preg_match_all('/(?<=注册学号:).*?(?=<\/br><\/br>)/', $res[0], $res3);
        preg_match_all('/(?<=学籍状态:).*?(?=<\/br><\/br>)/', $res[0], $res4);
        preg_match_all('/(?<=正常<\/br><\/br>).*?(?=<\/TD>)/', $res[0], $res5);
        if ($res1 && $res2 && $res3 && $res3 && $res5) {
            $res_new['zcxq'] = trim($res1[0][0]);
            // preg_replace('/[^\d-:]/', '<a>', $res2[0][0]);
            $res_new['zcsj'] = strip_tags(trim($res2[0][0]));
            $res_new['zcxh'] = trim($res3[0][0]);
            $res_new['zczt'] = trim($res4[0][0]) . " " . trim($res5[0][0]);
        }
        return $res_new;
    }

    /**
     *成绩信息
     **/
    public function CJXX()
    {
        $info = $this->getJWData(BK_URL_CJCX);
        // print_r($info);
        // exit();
        $info = preg_replace(array('/<TD[^>]*>/', '/\n/'), array('<TD>', ''), $info);
        // print_r($info);
        // exit();
        preg_match_all('/(?<=<TD>).*?(?=<\/TD>)/', $info, $res);
        // print_r($res[0]);
        // exit();
        $listnum = count($res[0]);
        $array_list = array();
        $arr = $res[0];
        for ($i = 7; $i < $listnum - 1; $i++) {
            $a = $i / 7 - 1;
            $b = $i % 7;
            switch ($b) {
                case 0:
                    $array_list[$a]["term"] = $arr[$i];
                    break;
                case 1:
                    $array_list[$a]["kc_num"] = $arr[$i];
                    break;
                case 2:
                    $array_list[$a]["kc_name"] = $arr[$i];
                    break;
                case 3:
                    $array_list[$a]["jx_class"] = $arr[$i];
                    break;
                case 4:
                    $array_list[$a]["grade"] = trim(strip_tags($arr[$i]));
                    break;
                case 5:
                    $array_list[$a]["bk_grade"] = $arr[$i];
                    break;
                case 6:
                    $array_list[$a]["credit"] = trim(strip_tags($arr[$i]));
                    break;
            }
        }
        //$array_list["total_credit"] = $arr[$listnum - 1];
        return $array_list;

    }

    /**
     *收费查询
     **/
    public function SFCX()
    {
        $info = $this->getJWData(BK_URL_SFCX);
        // print_r($info);
        // exit();
        $info = preg_replace(array('/<TR[^>]*>/', '/\n/', '/&nbsp;/'), array('<TR>', '', ''), $info);
        preg_match_all('/(?<=<TR>).*?(?=<\/TR>)/', $info, $res);
        // print_r($res[0]);
        // exit();
        $listnum = count($res[0]);
        $array_list = array();
        $arr = $res[0];
        $a = 0;
        $b = 0;
        for ($i = 1; $i < $listnum - 1; $i++) {
            preg_match_all('/(?<=<TD>).*?(?=<\/TD>)/', $arr[$i], $result);
            // print_r($result);
            // exit();
            preg_match_all('/(?<=<TD\salign=\"center\">).*?(?=<\/TD>)/', $arr[$i], $result1);
            // print_r($result1);
            // exit();
            preg_match_all('/(?<=<TD\salign=\"right\">).*?(?=<\/TD>)/', $arr[$i], $result2);
            // print_r($result2[0]);
            // exit();<TD colspan=6 align="right">
            preg_match_all('/(?<=<TD\scolspan=6\salign=\"right\">).*(?=<\/TD>)/', $arr[$i], $result3);
            // print_r($result3);
            // exit();
            if ($result[0] && $result1[0] && $result2[0]) {
                $array_list[$b][$a]['term'] = trim($result[0][0]);
                $array_list[$b][$a]['kc_name'] = trim($result[0][1]);
                $array_list[$b][$a]['kc_num'] = trim($result1[0][0]);
                $array_list[$b][$a]['class'] = trim($result1[0][1]);
                $array_list[$b][$a]['credit'] = trim($result1[0][2]);
                $array_list[$b][$a]['charge'] = trim($result2[0][0]);
                $a++;
            } elseif ($result3[0]) {
                $array_list[$b]['s-total'] = trim($result3[0][0]);
                $b++;
                $a = 0;
            }
        }
        preg_match_all('/(?<=<TD\salign=\"right\"\scolspan=\"6\">).*?(?=<\/TD>)/', $arr[$listnum - 1], $arr1);
        if (trim($arr1[0][0])) {
            $array_list['total'] = $arr1[0][0];
        }
        return $array_list;
    }
} 