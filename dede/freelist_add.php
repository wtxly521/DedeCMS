<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
$dsql = new DedeSql(false);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>新增自由列表</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script src="main.js" language="javascript"></script>
<script language="JavaScript">
function ChangeListStyle(){
   var itxt = document.getElementById("myinnertext");
   var myems = document.getElementsByName("liststyle");
   if(myems[0].checked) itxt.value = document.getElementById("list1").innerHTML;
   else if(myems[1].checked) itxt.value = document.getElementById("list2").innerHTML;
   else if(myems[2].checked) itxt.value = document.getElementById("list3").innerHTML;
   else if(myems[3].checked) itxt.value = document.getElementById("list4").innerHTML;
   itxt.value = itxt.value.replace("<BR>","<BR/>");
   itxt.value = itxt.value.toLowerCase();
}
function ShowHide(objname){
  var obj = document.getElementById(objname);
  if(obj.style.display == "block" || obj.style.display == "")
	   obj.style.display = "none";
  else
	   obj.style.display = "block";
}
function SelectTemplets(fname)
{
   var posLeft = window.event.clientY-200;
   var posTop = window.event.clientX-300;
   window.open("../include/dialog/select_templets.php?&activepath=<?php echo urlencode($cfg_templets_dir)?>&f="+fname, "poptempWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}
function CheckSubmit(){
   if(document.form1.title.value==""){
       alert("自由列表标题标题不能为空！");
	   document.form1.title.focus();
	   return false;
   }
   return true;
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<center>
<span style="display:none" id="list1">
·[field:textlink/]([field:pubdate function=strftime('%m-%d',@me)/])<br/>
</span>
<span style="display:none" id="list2">
·[field:typelink/] [field:textlink/]<br/>
</span>
<span style="display:none" id="list3">
<table width='98%' border='0' cellspacing='2' cellpadding='0'>
   <tr><td align='center'>[field:imglink/]</td></tr>
   <tr><td align='center'>[field:textlink/]</td></tr>
</table>
</span>
<span style="display:none" id="list4">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tbspan" style="margin-top:6px">
<tr> 
<td height="1" colspan="2" background="[field:templeturl/]/img/dot_hor.gif"></td>
</tr>
<tr> 
<td width="5%" height="26" align="center"><img src="[field:templeturl/]/img/item.gif" width="18" height="17"></td>
<td height="26">
	<b>
		[field:typelink function='str_replace("a ","a class=ulink ",@me)'/]
		<a href="[field:arcurl/]" class="ulink">[field:title/]</a>
	</b>
</td>
</tr>
<tr> 
<td height="20" style="padding-left:3px">&nbsp;</td>
<td style="padding-left:3px">
				<font color="#8F8C89">日期：[field:pubdate function="GetDateTimeMK(@me)"/] 
点击：[field:click/] 评论：[field:postnum/]</font>
				<a href="[field:phpurl/]/feedback.php?arcID=[field:id/]"><img src="[field:templeturl/]/img/comment.gif" width="12" height="12" border="0" title="查看评论"></a>
				</td>
</tr>
<tr> 
<td colspan="2" style="padding-left:3px">
  [field:litpic function="CkLitImageView(@me,80)"/]
	[field:description/]
</td>
</tr>
</table>
</span>
  <table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#98CAEF" align="center">
    <form action="freelist_action.php" method="post"  name="form1" onSubmit="return CheckSubmit();">
      <input type="hidden" name="dopost" value="addnew">
      <tr> 
        <td height="23" background="img/tbg.gif"> <table width="98%" border="0" cellpadding="0" cellspacing="0">
            <tr> 
              <td width="35%" height="18"><strong>＞自自由列表管理 &gt;&gt; 增加一个列表：</strong></td>
              <td width="65%" align="right"> <input type="button" name="b113" value="管理自由列表" onClick="location='freelist_main.php';" style="width:100" class='nbt'> 
                &nbsp; <input type="button" name="bt2" value="更新列表HTML" class="nbt" style="width:80px" onClick="location='makehtml_freelist.php';"> 
              </td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td height="265" valign="top" bgcolor="#FFFFFF"><table width="99%" border="0" align="right" cellpadding="0" cellspacing="0">
            <tr> 
              <td height="56"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr> 
                    <td height="28" colspan="2"><img src="img/help.gif" width="16" height="16">自由列表标记的说明：自由列表标记(freelist)的功能基本等同于arclist标记，区别是freelist标记支持分页，这让生成Google 
                      Map、生成按自定义排序规则的文章列表（如按标题拼音部首排序等）等轻松的实现统一化管理，并且自由列是独立编译的，不与其它模板混在一起，这样不会影响系统生成HTML或访问速度。</td>
                  </tr>
                  <tr> 
                    <td width="16%" height="28">自由列表标题：</td>
                    <td width="84%"><input name="title" type="text" id="title" style="width:35%"></td>
                  </tr>
                  <tr> 
                    <td height="28">列表HTML存放目录：</td>
                    <td><input name="listdir" type="text" id="listdir" style="width:35%" value="{cmspath}/freelist/">
                      {listdir}变量的值</td>
                  </tr>
                  <tr> 
                    <td height="28">目录默认页名称：</td>
                    <td> <input name="defaultpage" type="text" id="defaultpage" style="width:35%" value="index.html"> 
                      <input name="nodefault" type="checkbox" class="np" id="nodefault" value="1">
                      不使用目录默认主页 </td>
                  </tr>
                  <tr> 
                    <td height="28">命名规则：</td>
                    <td><input name="namerule" type="text" id="namerule" style="width:35%" value="{listdir}/index_{listid}_{page}.html"></td>
                  </tr>
                  <tr> 
                    <td height="28">列表模板：</td>
                    <td><input name="templet" type="text" id="templet" style="width:300" value="{style}/list_free.htm"> 
                      <input type="button" name="set4" value="浏览..." style="width:60" onClick="SelectTemplets('form1.templet');" class='nbt'></td>
                  </tr>
                  <tr> 
                    <td height="28">&nbsp;</td>
                    <td>以下选项用于模板里的 &lt;meta name=&quot;keywords|description&quot; 
                      content=&quot;&quot;&gt; 描述</td>
                  </tr>
                  <tr> 
                    <td height="28">关键字：</td>
                    <td><input name="keywords" type="text" id="keywords" style="width:60%"></td>
                  </tr>
                  <tr> 
                    <td height="28">列表描述：</td>
                    <td><textarea name="description" id="description" style="width:60%;height:50px"></textarea></td>
                  </tr>
                </table></td>
            </tr>
            <tr> 
              <td height="26" background="img/menubg.gif"><img src="img/file_tt.gif" width="7" height="8" style="margin-left:6px;margin-right:6px;">列表样式：（这里是定义自由列表模板里的{dede:freelist 
                /}标记的样式和属性）</td>
            </tr>
            <tr> 
              <td height="72"><table width="99%" border="0" cellspacing="0" cellpadding="0">
                  <tr> 
                    <td width="25%" height="126"><img src="img/g_t2.gif" width="130" height="100"> 
                      <input name="liststyle" class="np" type="radio" onClick="ChangeListStyle()" value="1"> 
                    </td>
                    <td width="25%"><img src="img/g_t1.gif" width="130" height="110"> 
                      <input type="radio" class="np" onClick="ChangeListStyle()" name="liststyle" value="2"></td>
                    <td width="25%"><img src="img/g_t3.gif" width="130" height="110"> 
                      <input type="radio" class="np" onClick="ChangeListStyle()" name="liststyle" value="3"></td>
                    <td><img src="img/g_t4.gif" width="130" height="110"> <input name="liststyle" type="radio" class="np" onClick="ChangeListStyle()" value="4" checked></td>
                  </tr>
                </table></td>
            </tr>
            <tr> 
              <td height="28"> 限定栏目： 
                <?php echo GetTypeidSel('form1','typeid','selbt1',0)?>
              </td>
            </tr>
            <tr> 
              <td height="28"> 限定频道： 
                <?php 
       echo "<select name='channel' style='width:100'>\r\n";
       echo "<option value='0' selected>不限...</option>\r\n";
       $dsql->SetQuery("Select ID,typename From #@__channeltype where ID>0");
	   $dsql->Execute();
	   while($row = $dsql->GetObject())
	   {
	      echo "<option value='{$row->ID}'>{$row->typename}</option>\r\n";
	   }
       echo "</select>";
		?>
                　(如果限定了频道内容模型，则允许使用附加表指定的列表字段作为底层变量)</td>
            </tr>
            <tr> 
              <td height="28">附加属性： 
                <?php 
       echo "<select name='att' style='width:100'>\r\n";
       echo "<option value='0' selected>不限...</option>\r\n";
       $dsql->SetQuery("Select * From #@__arcatt");
	   $dsql->Execute();
	   while($row = $dsql->GetObject())
	   {
	      echo "<option value='{$row->att}'>{$row->attname}</option>\r\n";
	   }
       echo "</select>";
		?>
                文档发布时间： 
                <input name="subday" type="text" id="subday2" value="0" size="6">
                天以内 （0 表示不限） </td>
            </tr>
            <tr> 
              <td height="28">每页记录数： 
                <input name="pagesize" type="text" id="pagesize" value="30" size="4">
                　显示列数： 
                <input name="col" type="text" id="col3" value="1" size="4">
                标题长度： 
                <input name="titlelen" type="text" id="titlelen" value="60" size="4">
                （1 字节 = 0.5个中文字）</td>
            </tr>
            <tr>
              <td height="28">摘要长度：</td>
            </tr>
            <tr> 
              <td height="28"> 高级筛选： 
                <input name="types[]" type="checkbox" id="type1" value="image" class="np">
                带缩略图 
                <input name="types[]" type="checkbox" id="type2" value="commend" class="np">
                推荐 
                <input name="types[]" type="checkbox" id="type3" value="spec" class="np">
                专题　关键字： 
                <input name="keyword" type="text" id="keyword">
                （&quot;,&quot;逗号分开）</td>
            </tr>
            <tr> 
              <td height="28">排列顺序： 
                <select name="orderby" id="orderby" style="width:120">
                  <option value="sortrank">置顶权限值</option>
                  <option value="pubdate" selected>发布时间</option>
                  <option value="senddate">录入时间</option>
                  <option value="click">点击量</option>
                  <option value="id">文档ＩＤ</option>
                  <option value="lastpost">最后评论时间</option>
                  <option value="postnum">评论总数</option>
                </select>
                　 
                <input name="order" type="radio"  class="np" value="desc" checked>
                由高到低 
                <input type="radio" name="order" class="np" value="asc">
                由低到高</td>
            </tr>
            <tr> 
              <td height="28">循环内的单行记录样式(InnerText)：[<a href='javascript:ShowHide("innervar");'><img src="img/help.gif" width="16" height="16" border="0">底层变量field参考</a>]</td>
            </tr>
            <tr> 
              <td height="99"> <textarea name="innertext" cols="80" rows="6" id="myinnertext" style="width:80%;height:120px"></textarea> 
                <script language="javascript">document.form1.innertext.value=document.getElementById("list4").innerHTML.toLowerCase();</script> 
              </td>
            </tr>
            <tr> 
              <td height="80" id='innervar' style="display:none"><font color="#CC6600"><img src="img/help.gif" width="16" height="16">支持字段(底层变量[field:varname/])：id,title,color,typeid,ismake,description,pubdate,senddate,arcrank,click,litpic,typedir,typename,arcurl,typeurl,<br>
                stime(pubdate 的&quot;0000-00-00&quot;格式),textlink,typelink,imglink,image 
                普通字段直接用[field:字段名/]表示。<br>
                ·Pubdate发布时间的调用参数 [field:pubdate function=strftime('%Y-%m-%d %H:%M:%S',@me)/]</font> 
              </td>
            </tr>
            <tr> 
              <td height="50"> &nbsp; <input name="Submit2" type="submit" id="Submit2" value="保存一个列表" class='nbt'> 
              </td>
            </tr>
          </table></td>
      </tr>
    </form>
    <tr> 
      <td valign="top" bgcolor="#EFF7E6">&nbsp;</td>
    </tr>
  </table>
</center>
<?php 
$dsql->Close();
?>
</body>
</html>