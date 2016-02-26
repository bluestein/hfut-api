# hfut-api

包括研究生与本科生文档，源代码

测试接口地址：`http://121.251.19.148/api/eams/eams.php`

##目录

* 文件说明
* 研究生部分
* 本科生部分
* 数据返回格式
* 维护
* 
##文件说明

1. index	入口文件
2. BKS	本科生信息
3. YJS	研究生信息
4. USER	为BKS、YJS基类
5. conf	配置文件（部分需自行配置）
6. GetLibInfo	图书馆信息

##一、研究生部分

###1.1 接口调用

`http://121.251.19.148/api/eams/eams.php?action=ACTION&user=USER&pwd=PASSWORD`

<table class="table table-bordered table-striped">
   <tr>
           <td colspan="2">表1.1 参数</td>
   </tr>
   <tr>
        <th>参数</th>
    	<th>说明</th>
	</tr>
	<tr>
		<td>action</td>
		<td>可选的功能（详情请看下表）</td>
	</tr>
	<tr>
		<td>user</td>
		<td>学号</td>
	</tr>
	<tr>
		<td>pwd</td>
		<td>密码</td>
	</tr>
</table>

###1.2 `action`的取值

<table class="table table-bordered table-striped">
   <tr>
       	<td colspan="10">表1.2 action的取值</td>
   </tr>
    <tr>
        <th>action</th>
		<td>applogin</td>
		<td>pyfa</td>
		<td>pyjh</td>
		<td>pyjd</td>
		<td>zyxx</td>
		<td>ktbg</td>
		<td>xkjg</td>
		<td>grxx</td>
		<td>cjxx</td>
		<td>ecard</td>
	</tr>
	<tr>
    	<th>说明</th>
		<td>登录验证</td>
		<td>培养方案</td>
		<td>培养计划</td>
		<td>培养进度</td>
		<td>专业信息</td>
		<td>开题报告</td>
		<td>选课结果</td>
		<td>个人信</td>
		<td>成绩信息</td>
		<td>一卡通信息</td>
	</tr>
</table>

##二、本科生部分

本科生部分与研究生部分有`3`个功能是一致的（即`参数取值与参数个数均相同`），分别是`grxx`（个人信息）、`cjxx`（成绩信息）、`ecard`（一卡通），其余为独占功能。

###2.1 接口调用

`http://121.251.19.148/api/eams/eams.php?action=ACTION&user=USER&pwd=PASSWORD&...`

注：上述地址中`...`表示根据功能的不同，`可能`需要不同的参数（参数取值与个数均有可能不同）；但前面的三个参数：`action`、`user`、`pwd`是必须的，其含义跟`表1.1`一致。

###2.2 `action`的取值

<table class="table table-bordered table-striped">
   <tr>
        <td colspan="12">表2.1 action的取值</td>
   </tr>
    <tr>
        <th>action</th>
		<td>jhcx</td>
		<td>jhcxxq</td>
		<td>kccx</td>
		<td>kccxxq</td>
		<td>jxbcx</td>
		<td>jxbcxxq</td>
		<td>xjzc</td>
		<td>sfcx</td>
		<td>grxx</td>
		<td>cjxx</td>
		<td>ecard</td>
	</tr>
	<tr>
    	<th>说明</th>
		<td>计划查询</td>
		<td>计划查询详情</td>
		<td>课程查询</td>
		<td>课程查询详情</td>
		<td>教学班查询</td>
		<td>教学班查询详情</td>
		<td>学籍注册</td>
		<td>收费查询</td>
		<td>个人信息</td>
		<td>成绩信息</td>
		<td>一卡通信息</td>
	</tr>
</table>

###2.3 按功能不同，`...`中的参数（即除了`action`、`user`、`pwd`3个参数之外还需要以下参数）

###jhcx（计划查询）

<table class="table table-bordered table-striped">
   <tr>
        <td colspan="2">表2.2 额外参数</td>
   </tr>
   <tr>
        <th>参数</th>
		<th>说明</th>
	</tr>
	<tr>
		<td>xqdm</td>
		<td>学期代码</td>
	</tr>
	<tr>
		<td>zydm</td>
		<td>专业代码</td>
	</tr>
	<tr>
		<td>kclxdm</td>
		<td>课程类型代码</td>
	</tr>
</table>

###jhcxxq（计划查询详情）

<table class="table table-bordered table-striped">
   <tr>
       <td colspan="2">表2.3 额外参数</td>
   </tr>
   <tr>
        <th>参数</th>
    	<th>说明</th>
	</tr>
	<tr>
		<td>xqdm</td>
		<td>学期代码</td>
	</tr>
	<tr>
		<td>kcdm</td>
		<td>课程代码</td>
	</tr>
</table>

###kccx（课程查询）

<table class="table table-bordered table-striped">
   <tr>
        <td colspan="2">表2.4 额外参数</td>
   </tr>
   <tr>
        <th>参数</th>
        <th>说明</th>
	</tr>
	<tr>
		<td>xqdm</td>
		<td>学期代码</td>
	</tr>
	<tr>
		<td>kcdm</td>
		<td>课程代码</td>
	</tr>
	<tr>
		<td>kcmc</td>
		<td>课程名称</td>
	</tr>
</table>

 * 注：至少有`kcdm`, `kcmc`其中1个即可

###kccxxq（课程查询详情）、jxbcxxq（教学班查询详情）

<table class="table table-bordered table-striped">
   <tr>
        <td colspan="2">表2.5 额外参数</td>
   </tr>
   <tr>
        <th>参数</th>
        <th>说明</th>
    </tr>
	<tr>
		<td>xqdm</td>
		<td>学期代码</td>
	</tr>
	<tr>
		<td>kcdm</td>
		<td>课程代码</td>
	</tr>
	<tr>
		<td>jxbh</td>
		<td>教学班号</td>
	</tr>
</table>

#####jxbcx（教学班查询）, xjzc（学籍注册）, sfcx（收费查询）, cjxx（成绩信息）, ecard（一卡通）
 * 不需要额外参数，即，只需要`action`、`user`、`pwd`这3个参数

##三、数据返回格式

使用get方法访问上述接口获取数据

###返回的数据格式（json）如下

```JSON
{"errorno":"错误代码", "description":"描述", "data":"数据"}
```

##四、维护

 * humooo@outlook.com
 * [github](https://github.com/bluestein)
 
###其他