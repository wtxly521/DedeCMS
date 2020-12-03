<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_AddNote');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($action)) $action = "";
if(empty($exrule)) $exrule = "";

if($action=="select"){
	require_once(dirname(__FILE__)."/co_sel_exrule.php");
	exit();
}

if($exrule==""){
	ShowMsg("请先选择一个导入规则！","co_sel_exrule.php");
	exit();
}

require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__co_exrule where aid='$exrule'");
$dsql->Close();
$ruleset = $row['ruleset'];
$dtp = new DedeTagParse();
$dtp->LoadString($ruleset);
$noteid = 0;
if(is_array($dtp->CTags))
{
	foreach($dtp->CTags as $ctag){
		if($ctag->GetName()=='field') $noteid++;
		if($ctag->GetName()=='note') $noteinfos = $ctag;
	}
}
else
{
	ShowMsg("该规则不合法，无法进行生成采集配置!","-1");
	$dsql->Close();
	exit();
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<script language='javascript' src='main.js'></script>
<script language='javascript'>
function ShowHide(objname)
{
   var obj = document.getElementById(objname);
   if(obj.style.display=="none") obj.style.display = "block";
	 else obj.style.display="none";
}

function ShowItem(objname)
{
 	var obj = document.getElementById(objname);
 	obj.style.display = "block";
}

</script>
<title>新增采集节点</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<form name="form1" method="post" action="co_add_action.php">
<input type='hidden' name='exrule' value='<?php echo $exrule?>'>
  <table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#98CAEF" align="center" style="margin-bottom:6px">
    <tr> 
      <td height="20" background='img/tbg.gif'><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="47%" height="18"><b>＞新增采集节点：</b></td>
            <td width="53%" align="right">
			<input type="button" name="b11" value="节点管理"  class='nbt' style="width:80" onClick="location.href='co_main.php';"> 
			&nbsp;
			<input type="button" name="b12" value="节点管理"  class='nbt' style="width:80" onClick="location.href='co_url.php';">
            &nbsp;
			</td>
          </tr>
        </table> </td>
    </tr>
</table>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="head1">
    <tr> 
      <td colspan="2"> <table border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="84" height="24" align="center" background="img/itemnote1.gif">&nbsp;网址获取</td>
            <td width="84" align="center" background="img/itemnote2.gif"><a href="#" onClick="ShowItem2()"><u>内容规则</u></a>&nbsp;</td>
          </tr>
        </table></td>
    </tr>
  </table>
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="head2" style="display:none">
    <tr> 
      <td colspan="2"> <table height="24" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="84" align="center" background="img/itemnote2.gif"><a href="#" onClick="ShowItem1()"><u>网址获取</u></a>&nbsp;</td>
            <td width="84" align="center" background="img/itemnote1.gif">内容规则</td>
          </tr>
        </table></td>
    </tr>
  </table>
  <table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center" style="margin-bottom:6px" id="needset"">
    <tr> 
      <td bgcolor="#F2F6E5"> <table width="400" border="0" cellspacing="0" cellpadding="0">
          <tr class="top" onClick="ShowHide('sitem');" style="cursor:hand"> 
            <td width="26" align="center"><img src="img/file_tt.gif" width="7" height="8"></td>
            <td width="374">节点基本信息<a name="d1"></a></td>
          </tr>
        </table></td>
    </tr>
    <tr id="sitem" style="display:block"> 
      <td bgcolor="#FFFFFF">
<table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="18%" height="24">节点名称：</td>
            <td width="32%"><input name="notename" type="text" id="notename" style="width:200"></td>
            <td width="18%">页面编码：</td>
            <td width="32%"> <input type="radio" name="language" class="np" value="gb2312" checked>
              GB2312 
              <input type="radio" name="language" class="np" value="utf-8">
              UTF8 
              <input type="radio" name="language" class="np" value="big5">
              BIG5 </td>
          </tr>
          <tr> 
            <td height="24">图片相对网址： </td>
            <td> 
              <?php 
		$aburl = "";
		$curl = GetCurUrl();
		$curls = explode("/",$curl);
		for($i=0;$i<count($curls)-2;$i++){
			if($i!=0) $aburl .= "/".$curls[$i];
		}
		$aburl .= "/upimg";
          ?>
              <input name="imgurl" type="text" id="imgurl" style="width:200" value="<?php echo $aburl?>"></td>
            <td>物理路径：</td>
            <td><input name="imgdir" type="text" id="imgdir2" style="width:150" value="../upimg"></td>
          </tr>
          <tr> 
            <td height="24">内容匹配模式：</td>
            <td colspan="3"> <input type="radio" class="np" name="matchtype" value="regex">
              正则表达式 
              <input name="matchtype" class="np" type="radio" value="string" checked>
              字符串 </td>
          </tr>
          <tr bgcolor="#F0F2EE"> 
            <td height="24" colspan="4">以下选项仅在开启防盗链模式才需设定，如果目标网站没有防盗链功能，请不要开启，否则会降低采集速度。</td>
          </tr>
          <tr> 
            <td height="24">防盗链模式：</td>
            <td><input name="isref" type="radio" class="np" value="no" checked>
              不开启 
              <input name="isref" type="radio" class="np" value="yes">
              开启</td>
            <td>资源下载超时时间：</td>
            <td><input name="exptime" type="text" id="exptime" value="10" size="8">
              秒</td>
          </tr>
          <tr> 
            <td height="24">引用网址：</td>
            <td colspan="3"><input name="refurl" type="text" id="refurl" size="30">
              （一般为目标网站其中一个文章页的网址，需加http://）</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#F2F6E5"> <table width="400" border="0" cellspacing="0" cellpadding="0">
          <tr class="top" onClick="ShowHide('slist');" style="cursor:hand"> 
            <td width="26" align="center"><img src="img/file_tt.gif" width="7" height="8"></td>
            <td width="374">采集列表获取规则</td>
          </tr>
        </table></td>
    </tr>
    <tr id="slist" style="display:block"> 
      <td height="76" bgcolor="#FFFFFF"><table width="98%" border="0" cellspacing="0" cellpadding="0">
          
          <tr>
            <td height="24">来源网址获取方式：</td>
            <td colspan="2"><input name="source" type="radio" id="radio" value="var" class="np" checked>
符合特定序列的网址
  <input name="source" type="radio" id="source" value="app" class="np"> 
  我自己手工指定这些网址</td>
          </tr>
          
          <tr>
            <td height="24">来源网址属性：</td>
            <td colspan="2"><input name="sourcetype" type="radio" class="np" value="list" checked>
包含文章网址的列表网址
  <input type="radio" name="sourcetype" class="np" value="archives">
文章网址</td>
          </tr>
          <tr> 
            <td width="18%" height="24">来源网址：</td>
            <td colspan="2"><input name="sourceurl" type="text" id="sourceurl" style="width:70%" value="http://">            </td>
          </tr>
          <tr> 
            <td height="24">&nbsp;</td>
            <td colspan="2">对于比较规则分多页的列表网址，用 http://abc.com/list.php?page=[var:分页] 
              的形式，然后指定&quot;分页变量起始值&quot;。</td>
          </tr>
          <tr>
            <td height="24" colspan="3" bgcolor="#FBFDF2"><strong>如果来源网址是列表网址，请指定下面文章内容网址获取规则的属性：</strong></td>
          </tr>
          <tr>
            <td height="24">分页变量起始值：</td>
            <td colspan="2"><input name="varstart" type="text" id="varstart2" size="15">
　　变量结束值：
  <input name="varend" type="text" id="varend2" size="15">
　表示 [var:分页] 的范围） </td>
          </tr>
          <tr> 
            <td height="24">文章网址需包含：</td>
            <td colspan="2"><input name="need" type="text" id="cannot" size="15">
              　网址不能包含： 
              <input name="cannot" type="text" id="cannot" size="15">
              　(正则)</td>
          </tr>
          <tr> 
            <td height="100">链接区域规则：<br>
              （如果用正则的形式无法正确获得需要的网址，请设置此选项）<br>            </td>
            <td width="42%">
			起始HTML：<br>
			<textarea name="linkareas" style="width:90%" rows="5" id="linkareas"></textarea>            </td>
            <td width="40%">
			结束HTML：<br>
			<textarea name="linkareae" style="width:90%" rows="5" id="linkareae"></textarea>			</td>
          </tr>
          
          <tr>
            <td height="24" colspan="3" bgcolor="#FBFDF2"><strong>如果你想手工指定要采集的网址或除了规则网址外，还有其它网址，请在下面指定：</strong></td>
          </tr>
          <tr>
            <td height="110" valign="top"><strong>手工指定网址：</strong><a href="javascript:ShowHide('handurlhelp');"><img src="img/help.gif " width="16" height="16" border="0"></a><br>
              (每行一条网址，<br>
              不支持使用变量)</td>
            <td colspan="2">
			<span id='handurlhelp' style='display:none;background-color:#efefef'>
			对于部份符合规则，部份不符合规则的网址，可以把不符合规则的放在这里，例：像<br>
http://xx.com/aaa/index.html<br>
http://xx.com/aaa/list_2.html<br>
http://xx.com/aaa/list_3.html...<br>
这样的网址，你可以用变量指定 list_[var:分页].html，<br>
然后把 
            http://xx.com/aaa/index.html(该网址不符分页规则) 填写在下面。			</span>
<textarea name="sourceurls" id="sourceurls" style="width:95%;height:100"></textarea></td>
          </tr>
          
          <!--
		  //暂时无时间完成此项
		  tr align="center"> 
            <td height="49" colspan="3"><input name="test1" type="button" class="nbt" id="test1" value="测试列表获取规则">            </td>
          </tr-->
        </table></td>
    </tr>
</table>

  <table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center" id="adset" style="display:none">
    <tr> 
      <td bgcolor="#F2F6E5"><table width="400" border="0" cellspacing="0" cellpadding="0">
          <tr class="top" onClick="ShowHide('sart');" style="cursor:hand"> 
            <td width="26" align="center"><img src="img/file_tt.gif" width="7" height="8"></td>
            <td width="374">文档内容获取规则<a name="d2"></a></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:6px">
          <tr> 
            <td height="24" colspan="3">　测试单页网址： 
              <input name="testurl" type="text" id="testurl" value="http://" size="50">
              （仅用于编辑规则完成后测试，测试时不会本地化远程媒体）</td>
          </tr>
          <tr> 
            <td height="60" colspan="3"><strong>　字段设置说明：</strong><br/>
              　１、规则：如果采集的内容为分页文档，请在文章body字段&quot;分页内容字段&quot;这个选项打勾。<br/>
              　２、变量：如果你的字段值使用的不是[var:内容]，而是指定的其它值，则导出时直接使用该值，并且采集时不会分析该项目。<br>
              　３、过滤规则：如果有多个规则，请用{dede:teim}规则一{/dede:trim}换行{dede:teim}规则二{/dede:trim}...表示</td>
          </tr>
          <tr bgcolor="#EBEFD1"> 
            <td height="24"><strong>　文档是否分页：</strong></td>
            <td colspan="2"> <input name="sptype" type="radio" class="np" value="none" checked>
              不分页 
              <input name="sptype" type="radio" value="full" class="np">
              全部列出的分页列表 
              <input type="radio" name="sptype" class="np" value="next">
              上下页形式或不完整的分页列表</td>
          </tr>
          <tr> 
            <td width="18%" height="60">分页链接区域匹配规则：<br/>
              文档分多页时才需选此项</td>
            <td> 分页链接区域开始HTML： <br> <textarea name="sppages" rows="3" id="sppages" style="width:90%"></textarea> 
            </td>
            <td width="40%"> 分页链接区域结束HTML： <br> <textarea name="sppagee" rows="3" id="textarea10" style="width:90%"></textarea> 
            </td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:6px">
          <tr> 
            <td width="98%" height="26" colspan="3" background="img/menubg.gif" bgcolor="#66CCFF">　<strong>＞内容字段列表：</strong>(一般“自定义处理接口”带有处理程序的字段[黑色字]不需要理会)</td>
          </tr>
        </table>
        <?php 
          if(is_array($dtp->CTags))
          {
	          $s = 0;
	          foreach($dtp->CTags as $ctag)
	          {
		           if($ctag->GetName()=='field')
		           {
		             if($ctag->GetAtt('source')=='value') continue;
		             
		             $tagv = "[var:内容]";
		             //if($ctag->GetAtt('source')=='function') 
		             //else $fnv = "";
		             $fnv = $ctag->GetInnerText();
		             
		             $cname = $ctag->GetAtt('name');
		             
		             if($ctag->GetAtt('intable')!="" 
		                  && $ctag->GetAtt('intable')!=$noteinfos->GetAtt('tablename') )
		             {
		             	  $cname = $ctag->GetAtt('intable').'.'.$cname;
		             }
		             $comment = $ctag->GetAtt('comment');
		             $s++;
          ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:6px">
          <tr bgcolor="#EBEFD1"> 
            <td width="18%" height="24" bgcolor="#EBEFD1"> &nbsp; 
			<?php 
			if($ctag->GetAtt('source')!='function'){ $fcolor=" style='color:red' "; $tstyle=""; }
			else{ $fcolor=""; $tstyle=" style='display:none' "; }
			?>
			<a href="javascript:ShowHide('fieldlist<?php echo $s; ?>');"<?php echo $fcolor?>><b>＞<u><?php echo $comment?></u></b></a>
			<input type="hidden" name="comment<?php echo $s; ?>" id="comment<?php echo $s; ?>4" value="<?php echo $comment?>"> 
            </td>
            <td width="22%"> <input name="field<?php echo $s; ?>" type="text" id="field<?php echo $s; ?>4" value="<?php echo $cname?>" size="22"></td>
            <td width="20%" align="right">字段值：</td>
            <td width="40%"> <input name="value<?php echo $s; ?>" type="text" id="value<?php echo $s; ?>4" value="<?php echo $tagv?>" size="25"> 
            </td>
          </tr>
          <tr> 
            <td colspan="4">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" id="fieldlist<?php echo $s; ?>"<?php echo $tstyle?>>
                <tr> 
                  <td height="80">匹配内容：</td>
                  <td height="20">开始无重复HTML：<br> <textarea name="matchs<?php echo $s; ?>" rows="4" id="textarea11" style="width:90%"></textarea> 
                  </td>
                  <td height="20">结尾无重复HTML：<br> <textarea name="matche<?php echo $s; ?>" rows="4" id="textarea12" style="width:90%"></textarea> 
                  </td>
                </tr>
                <tr> 
                  <td height="63">过滤规则：</td>
                  <td height="63"> <textarea name="trim<?php echo $s; ?>" cols="20" rows="3" id="textarea13" style="width:90%"></textarea> 
                  </td>
                  <td height="63"> <input name="isunit<?php echo $s; ?>" type="checkbox" id="isunit<?php echo $s; ?>4" value="1" class="np">
                    分页内容字段（规则中只允许单一的该类型字段）<br/> <input name="isdown<?php echo $s; ?>" type="checkbox" id="isdown<?php echo $s; ?>4" value="1" class="np">
                    下载字段里的多媒体资源 </td>
                </tr>
                <tr> 
                  <td width="18%" height="60">自定义处理接口：</td>
                  <td width="42%" height="20"><textarea name="function<?php echo $s; ?>" cols="20" rows="3" id="textarea14" style="width:90%"><?php echo $fnv?></textarea> 
                  </td>
                  <td width="40%" height="20">函数或程序的变量<br>
                    @body 表示原始网页 @litpic 缩略图<br>
                    @me 表示当前标记值和最终结果</td>
                </tr>
              </table></td>
          </tr>
        </table>
        <?php  } } } ?>
      </td>
    </tr>
    <tr> 
      <td height="50" align="center" bgcolor="#FFFFFF">
<!--input name="test122" type="button" class="nbt" id="test124" value="测试内容获取规则"-->
        　
<input type="submit" name="b1222" value="保存节点" class="nbt" style="width:80"></td>
    </tr>
    <tr id="sart" style="display:block"> 
      <td valign="top" bgcolor="#FFFFFF"> </td>
    </tr>
  </table>
	</form>
</body>
</html>