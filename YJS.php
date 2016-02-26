<?php
 /* Email: humooo@outlook.com
 * Date: 15-7-16
 * Time: 上午9:28
 */
include_once "USER.php";

class YJS extends USER
{
    const YJS_URL_KTBG = YJS_URL_KTBG;
    const YJS_URL_PYJH = YJS_URL_PYJH;
    const YJS_URL_PYFA = YJS_URL_PYFA;
    const YJS_URL_PYJD = YJS_URL_PYJD;
    const YJS_URL_ZYXX = YJS_URL_ZYXX;
    //jwx
    const YJS_URL_BASEINFO = YJS_URL_BASEINFO;
    const YJS_URL_SCHEDULE = YJS_URL_SCHEDULE;
    const YJS_URL_SCORE = YJS_URL_SCORE;

    public function __construct($user, $password)
    {
        parent::__construct($user, $password);
        $this->url_jw = YJS_URL_JW;
    }

    /**
     * 获取开题报告信息
     */
    public function KTBG()
    {

        $result = array(); //初始化最后输出结果

        //各个字段名
        $yjs_fields = array(
            'num', 'person', 'situation',
            'conclusion', 'suggestion'
        );

        $info = $this->getJWData(YJS::YJS_URL_KTBG); ////YJS_URL_KTBG 研究生开题报告url
        preg_match('/(?<=<div align=center>)[\s\S]*<\/table>\s+(?=<table)/i', $info, $info);
        $info = preg_replace('/<tr[^>]*bgcolor="#AFC5DA"[^>]*>[\s\S]*?<\/tr>/i', '', $info[0]);
        $info = preg_replace($this->replace_patten, $this->replacement, $info);
        $info = preg_replace(array('/<BR>/', '/<td><\/td>/', '/<img[^>]*(?<=images\/)(.+?)(?=")[^>]*>/i'), array('', '<td> </td>', '$1'), $info);
        preg_match_all('/(?<=<td>).*?(?=<\/td>)/', $info, $res);
        $res = array_chunk(array_slice($res[0], 5), 5);
        //把索引数组转换成关联数组
        foreach ($res as $index => $item) {
            for ($k = 0; $k < count($item); $k++) {
                $result[$index][$yjs_fields[$k]] = $item[$k];
            }
        }
        //print_r($result);
        return $result;
    }

    /**
     * 获取方案信息
     */
    public function PYFA()
    {

        $result = array(); //初始化最后输出结果

        //各个字段名
        $yjs_fields = array(
            'cour_category', 'cname', 'chour',
            'credit', 'exam_time', 'way',
            'select', 'remark'
        );

        $info = $this->getJWData(YJS::YJS_URL_PYFA); ////YJS_URL_PYFA 研究生培养方案信息url
        $info = preg_replace(array('/<td[^>]*?>\s+<font[^>]*?>(.*?)<\/font>\s+<\/td>/i', '/<select[^>]*?>([\s\S]*?)<\/select>/i'), array('', '非学位课'), $info);
        //print_r($info);
        preg_match_all('/(?<=rowspan=")\d+(?=")/', $info, $rowspan); //rowspan的值，即跨越的行数
        preg_match_all('/<td.*?rowspan[^>]*?>(.*?)<\/td>/i', $info, $rowspan_value); //rowspan对应td的值
        $rowspan = $rowspan[0];
        $rowspan_value = $rowspan_value[1];
        $info = preg_replace('/<td.*?rowspan[^>]*?>(.*?)<\/td>/i', '', $info);
        $info = preg_replace($this->replace_patten, $this->replacement, $info);
        $info = preg_replace('/<td><\/td>/', '', $info);
        preg_match_all('/(?<=<td>)(.+?)(?=<\/td>)/s', $info, $res);

	if($res[0] && $rowspan){
        $res = array_chunk(array_slice($res[0], 8), 6);

        //把rowspan的值分配到每一行
        $tmp = 0;
        foreach ($rowspan as $index => $value) {
            for ($i = 0; $i < $value; $i++) {
                $res[$tmp][6] = '';
                $res[$tmp][7] = $rowspan_value[$index];
                $tmp++;
            }
        }
        //把索引数组转换成关联数组
        foreach ($res as $index => $item) {
            for ($k = 0; $k < count($item); $k++) {
                $result[$index][$yjs_fields[$k]] = $item[$k];
            }
        }
	}
        //print_r($result);
        return $result;
    }

    /**
     * 获取培养计划
     */
    public function PYJH()
    {


        $result = array(); //初始化最后输出结果

        //各个字段名
        $yjs_fields = array(
            'cour_category', 'cname', 'chour',
            'credit', 'exam_time', 'way'
        );
        //direct limit 另外赋值

        $info = $this->getJWData(YJS::YJS_URL_PYJH); ////YJS_URL_PYJH 研究生培养计划url
        preg_match_all('/<td[^>]*?>\s+<font[^>]*?>(.*?)<\/font>\s+<\/td>/is', $info, $head_value); //把表头（样式是表头）的值放到 $head_value
        $info = preg_replace('/<td[^>]*?>\s+<font[^>]*?>.*?<\/font>\s+<\/td>/is', '', $info); //去除表头
        $info = preg_replace($this->replace_patten, $this->replacement, $info); //去除表格样式
        $info = preg_replace('/<td><\/td>/', '', $info);
        preg_match_all('/(?<=<td>)(.+?)(?=<\/td>)/s', $info, $res); //取一般的值

	if($head_value[1] && $res[0]){
        	//direct limit先赋值, $head_value[1][0]是表格名称
        	$result['direct'] = $head_value[1][1]; //方向
        	$result['limit'] = $head_value[1][2]; //学习年限

        	$res = array_chunk(array_slice($res[0], 6), 6);
        //把索引数组转换成关联数组
        foreach ($res as $index => $item) {
            for ($k = 0; $k < count($item); $k++) {
                $result[$index][$yjs_fields[$k]] = $item[$k];
            }
        }
	}
//        print_r($result);
        return $result;
    }

    /**
     * 获取专业信息
     */
    public function ZYXX()
    {


        $result = array(); //初始化最后输出结果

        //各个字段名
        $yjs_fields = array(
            'stu_category', 'xue_min_credit', 'major',
            'xue_max_credit', 'auth', 'min_credit',
            'way_num', 'max_credit', 'add_max_credit',
            'intro', 'direct', 'limit'
        );

        $info = $this->getJWData(YJS::YJS_URL_ZYXX); ////YJS_URL_ZYXX 研究生专业信息url
        $info = preg_replace('/<td[^>]*?><font[^>]*?>(.*?)<\/font><\/td>/i', '', $info); //去掉表头
        $info = preg_replace($this->replace_patten, $this->replacement, $info);
        $info = preg_replace('/<td><\/td>/', '', $info);
        preg_match_all('/(?<=<td>)(.+?)(?=<\/td>)/s', $info, $res);
        $res = array_chunk($res[0], 2);
        //把索引数组转换成关联数组
        $k = 0;
        foreach ($res as $item) {
            $result[$yjs_fields[$k]] = $item[1]; //item[0]是字段的中文名,item[1]是字段的值
            $k++;
        }
        //print_r($result);
        return $result;
    }

    /**
     * 获取进度信息
     */
    public function PYJD()
    {
        $result = array(); //初始化最后输出结果

        //各个字段名
        $yjs_fields = array(
            'name', 'stu_category', 'train_plan',
            'report', 'check', 'reply',
            'undo_plan'
        );

        $info = $this->getJWData(YJS::YJS_URL_PYJD); ////YJS_URL_PYJD 研究生培养进度url
        $info = preg_replace(array('/<font[^>]*?>.*?<\/font>/i', '/&nbsp;+/', '/:+/'), array('未完成', '', ''), $info); //替换

        /*preg_match_all('/<td[^>]*?><b>(.*?)<\/b><\/td>/i',$info,$res_head);//取标题*/
        //$res_head = array_slice($res_head[1],1);//去掉表格名称

        preg_match_all('/<td[^>]*?><u>\s*(.*?)\s*<\/u><\/td>/i', $info, $res_value); //取值
	
        $res_value = $res_value[1];
	if(array_filter($res_value)){
        //把索引数组转换成关联数组
        foreach ($res_value as $index => $item) {
            $result[$yjs_fields[$index]] = $item;
        }
	}
	//$result['debug']=$res_value;
        //print_r($result);
        return $result;
    }


    //---------------------以下为jwx代码，我（humooo）只是更改了函数名与权限--------------------------------------//

    /**
     * 个人信息
     * @return array
     */
    public function GRXX()
    {
        $info = $this->getJWData(YJS::YJS_URL_BASEINFO);
        //$info=iconv("GB2312","utf-8//IGNORE",$info);  //成绩页面转码
        preg_match_all("/(?<=<td>).*(?=<\/td>)|(?<=<td\swidth=\"180\"\sheight=\"16\">).*(?=<\/td>)/", $info, $res); //匹配出基本信息列表
        $baseinfo_list = array();
        $baseinfo = $res[0];
	if($res[0]){
        $baseinfo_list["name"] = $this->clean_strhtml($baseinfo[0]);
        $baseinfo_list["sno"] = $this->clean_strhtml($baseinfo[1]);
        $baseinfo_list["stu_type"] = $this->clean_strhtml($baseinfo[2]);
        $baseinfo_list["grade"] = $this->clean_strhtml($baseinfo[3]);
        $baseinfo_list["college"] = $this->clean_strhtml($baseinfo[4]);
        $baseinfo_list["major"] = $this->clean_strhtml($baseinfo[5]);
        $baseinfo_list["class"] = $this->clean_strhtml($baseinfo[6]);
        $baseinfo_list["sex"] = $this->clean_strhtml($baseinfo[7]);
        $baseinfo_list["xj_status"] = $this->clean_strhtml($baseinfo[8]);
        $baseinfo_list["select"] = $this->clean_strhtml($baseinfo[9]);
        $baseinfo_list["zc_status"] = null;
        $baseinfo_list["photo"] = null;
	}
        return $baseinfo_list;
    }

    /**
     * 选课结果
     * @return array
     */
    public function XKJG()
    {
        $info = $this->getJWData(YJS::YJS_URL_SCHEDULE);
        //$info = iconv("GB2312","utf-8//IGNORE",$info);
        preg_match_all("/(?<=<TD\salign=\"center\"\sbgcolor=\"#ffffff\".class=td04>).*(?=<\/TD>)/", $info, $res);
        $listnum = count($res[0]);
        $schedule_list = array();
        $schedule = $res[0];
        for ($i = 0; $i < $listnum; $i++) {
            $b = $i / 5 - 1;
            $a = $i % 5;
            if ($b >= 0) {
                switch ($a) {
                    case 0:
                        $schedule_list[$b]["term"] = $this->clean_strhtml($schedule[$i]);
                        break;
                    case 1:
                        $schedule_list[$b]["cname"] = $this->clean_strhtml($schedule[$i]);
                        break;
                    case 2:
                        $schedule_list[$b]["cno"] = $this->clean_strhtml($schedule[$i]);
                        break;
                    case 3:
                        $schedule_list[$b]["t_num"] = $this->clean_strhtml($schedule[$i]);
                        break;
                    case 4:
                        $schedule_list[$b]["teacher"] = $this->clean_strhtml($schedule[$i]);
                        break;
                }
            }
        }
        return $schedule_list;
    }

    /**
     * 成绩信息
     * @return array
     */
    public function CJXX()
    {
        $info = $this->getJWData(YJS::YJS_URL_SCORE);
        //$info = iconv("GB2312","utf-8//IGNORE",$info);  //成绩页面转码
        preg_match("/(?<=table)[\s\S]*(?=<\/TABLE>)/", $info, $res);
        preg_match_all("/(?<=<TD>).*(?=<\/TD>)|(?<=<TD.align=.center.>)[\s\S]*?(?=<\/TD>)/", $res[0], $res_new);
        $listnum = count($res_new[0]);
        $score_list = array();
        $scorename = $res_new[0];
        for ($i = 0; $i < $listnum; $i++) {
            $a = $i % 6;
            $b = $i / 6;
            switch ($a) {
                case 0:
                    $score_list[$b]["term"] = $this->clean_strhtml($scorename[$i]);
                    break;
                case 1:
                    $score_list[$b]["cno"] = $this->clean_strhtml($scorename[$i]);
                    break;
                case 2:
                    $score_list[$b]["cname"] = $this->clean_strhtml($scorename[$i]);
                    break;
                case 3:
                    $score_list[$b]["t_num"] = $this->clean_strhtml($scorename[$i]);
                    break;
                case 4:
                    $score_list[$b]["grade"] = $this->clean_strhtml($scorename[$i]);
                    break;
                case 5:
                    $score_list[$b]["credit"] = $this->clean_strhtml($scorename[$i]);
                    break;
            }
        }

        return $score_list;
    }


    private function clean_strhtml($string)
    {
        $string = strip_tags($string);
        $string = preg_replace('/\n/is', '', $string);
        $string = preg_replace('/ |　/is', '', $string);
        $string = preg_replace('/&nbsp;/is', '', $string);
        return trim($string);
    }

} 