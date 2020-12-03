<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
$dsql = new DedeSql(false);
$aid = ereg_replace("[^0-9]","",$aid);
$row = $dsql->GetOne("Select * From #@__freelist where aid='$aid' ");
$dtp = new DedeTagParse();
$dtp->SetNameSpace("dede","{","}");
$dtp->LoadSource("--".$row['listtag']."--");
$ctag = $dtp->GetTag('list');
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>更改自由列表</title>
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
  <table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#98CAEF" align="center">
    <form action="freelist_action.php" method="post"  name="form1" onSubmit="return CheckSubmit();">
      <input type="hidden" name="dopost" value="edit">
      <input type="hidden" name="aid" value="<?php echo $row['aid']?>">
      <tr> 
        <td height="23" background="img/tbg.gif"> <table width="98%" border="0" cellpadding="0" cellspacing="0">
            <tr> 
              <td width="35%" height="18"><a href="mytag_main.php"><strong>＞自自由列表管理</strong></a> 
                <strong>&gt;&gt; 更改自由列表：</strong></td>
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
                    <td width="84%"><input name="title" type="text" id="title" style="width:35%" value="<?php echo $row['title']?>"></td>
                  </tr>
                  <tr> 
                    <td height="28">列表HTML存放目录：</td>
                    <td><input name="listdir" type="text" id="listdir" style="width:35%" value="<?php echo $row['listdir']?>">
                      {listdir}变量的值</td>
                  </tr>
                  <tr> 
                    <td height="28">目录默认页名称：</td>
                    <td><input name="defaultpage" type="text" id="defaultpage" style="width:35%" value="<?php echo $row['defaultpage']?>"> 
                      <input name="nodefault" type="checkbox" class="np" id="nodefault" value="1"<?php  if($row['nodefault']==1) echo " checked"; ?>>
                      不使用目录默认主页
                    </td>
                  </tr>
                  <tr> 
                    <td height="28">命名规则：</td>
                    <td><input name="namerule" type="text" id="namerule" style="width:35%" value="<?php echo $row['namerule']?>"></td>
                  </tr>
                  <tr> 
                    <td height="28">列表模板：</td>
                    <td><input name="templet" type="text" id="templet" style="width:300" value="<?php echo $row['templet']?>"> 
                      <input type="button" name="set4" value="浏览..." style="width:60" onClick="SelectTemplets('form1.templet');" class='nbt'></td>
                  </tr>
                  <tr> 
                    <td height="28">&nbsp;</td>
                    <td>以下选项用于模板里的 &lt;meta name=&quot;keywords|description&quot; 
                      content=&quot;&quot;&gt; 描述</td>
                  </tr>
                  <tr> 
                    <td height="28">关键字：</td>
                    <td><input name="keywords" type="text" id="keywords" style="width:60%" value="<?php echo $row['keyword']?>"></td>
                  </tr>
                  <tr> 
                    <td height="28">列表描述：</td>
                    <td><textarea name="description" id="description" style="width:60%;height:50px"><?php echo $row['description']?></textarea></td>
                  </tr>
                </table></td>
            </tr>
            <tr> 
              <td height="26" background="img/menubg.gif"><img src="img/file_tt.gif" width="7" height="8" style="margin-left:6px;margin-right:6px;">
              	列表样式：（这里是定义自由列表模板里的{dede:freelist/}标记的样式和属性）
              </td>
            </tr>
            <tr> 
              <td height="28">
              	限定栏目： 
               <?php 
                $typeid = $ctag->GetAtt('typeid');
                if(!empty($typeid)){
                    $typeinfos = $dsql->GetOne("Select ID,typename From #@__arctype where ID='$typeid' ");
                    echo GetTypeidSel('form1','typeid','selbt1',0,$typeinfos['ID'],$typeinfos['typename']);
                }else{
                	echo GetTypeidSel('form1','typeid','selbt1',0);
                }
               ?>
              </td>
            </tr>
            <tr> 
              <td height="28"> 限定频道： 
                <?php 
       $channel  = $ctag->GetAtt('channel');
       echo "<select name='channel' style='width:100'>\r\n";
       echo "<option value='0'>不限...</option>\r\n";
       $dsql->SetQuery("Select ID,typename From #@__channeltype where ID>0");
	   $dsql->Execute();
	   while($nrow = $dsql->GetObject())
	   {
	      if($nrow->ID==$channel) echo "<option value='{$nrow->ID}' selected>{$nrow->typename}</option>\r\n";
	      else echo "<option value='{$nrow->ID}'>{$nrow->typename}</option>\r\n";
	   }
       echo "</select>";
		?>
                　(如果限定了频道内容模型，则允许使用附加表指定的列表字段作为底层变量)</td>
            </tr>
            <tr> 
              <td height="28">附加属性： 
                <?php 
       $att  = $ctag->GetAtt('att');    
       echo "<select name='att' style='width:100'>\r\n";
       echo "<option value='0'>不限...</option>\r\n";
       $dsql->SetQuery("Select * From #@__arcatt");
	   $dsql->Execute();
	   while($nrow = $dsql->GetObject())
	   {
	      if($att==$nrow->att) echo "<option value='{$nrow->att}' selected>{$nrow->attname}</option>\r\n";
	      else echo "<option value='{$nrow->att}'>{$nrow->attname}</option>\r\n";
	   }
       echo "</select>";
		?>
                文档发布时间： 
                <input name="subday" type="text" id="subday" size="6" value="<?php echo $ctag->GetAtt('subday')?>">
                天以内 （0 表示不限） </td>
            </tr>
            <tr> 
              <td height="28">每页记录数： 
                <input name="pagesize" type="text" id="pagesize"  value="<?php echo $ctag->GetAtt('pagesize')?>" size="4">
                　显示列数： 
                <input name="col" type="text" id="col" value="<?php  $col = $ctag->GetAtt('col'); $v = ( empty($col) ? '1' :  $col ); echo $v; ?>" size="4">
                标题长度： 
                <input name="titlelen" type="text" id="titlelen" value="<?php echo $ctag->GetAtt('titlelen')?>" size="4">
                （1 字节 = 0.5个中文字）</td>
            </tr>
            <tr> 
              <td height="28">
              	<?php 
              	$setype = $ctag->GetAtt('type');
              	if($setype=='') $setype = 'X';
              	?>
              	高级筛选： 
                <input name="types[]" type="checkbox" id="type1" value="image" class="np"<?php if(eregi('image',$setype)) echo ' checked';?>>
                带缩略图 
                <input name="types[]" type="checkbox" id="type2" value="commend" class="np"<?php if(eregi('commend',$setype)) echo ' checked';?>>
                推荐 
                <input name="types[]" type="checkbox" id="type3" value="spec" class="np"<?php if(eregi('spec',$setype)) echo ' checked';?>>
                专题　关键字： 
                <input name="keyword" type="text" id="keyword" value="<?php echo $ctag->GetAtt('keyword')?>">
                （&quot;,&quot;逗号分开）
                </td>
            </tr>
            <tr> 
              <td height="28">排列顺序： 
                <?php 
                $orderby = $ctag->GetAtt('orderby');
                $sorta = "sortrank,置顶权限值;pubdate,发布时间;senddate,录入时间;click,点击量;id,文档ＩＤ,lastpost,最后评论时间;postnum,评论总数;rand,随机获取";
                $sortas = explode(';',$sorta);
                foreach($sortas as $v){
                	$vs = explode(',',$v);
                	$vs[0] = trim($vs[0]);
                	$sortarrs[$vs[0]] = $vs[1];
                }
                ?>  
                <select name="orderby" id="orderby" style="width:120">
                	<?php 
                	echo "<option value=\"$orderby\" selected>{$sortarrs[$orderby]}</option>\r\n";
                	?>
                  <option value="sortrank">置顶权限值</option>
                  <option value="pubdate">发布时间</option>
                  <option value="senddate">录入时间</option>
                  <option value="click">点击量</option>
                  <option value="id">文档ＩＤ</option>
                  <option value="lastpost">最后评论时间</option>
                  <option value="postnum">评论总数</option>
                </select>
                　 
                <input name="order" type="radio"  class="np" value="desc"<?php if($ctag->GetAtt('orderway')=='desc') echo " checked";?>>
                由高到低 
                <input type="radio" name="order" class="np" value="asc"<?php if($ctag->GetAtt('orderway')=='asc') echo " checked";?>>
                由低到高</td>
            </tr>
            <tr> 
              <td height="28">循环内的单行记录样式(InnerText)：[<img src="img/help.gif" width="16" height="16"><a href='javascript:ShowHide("innervar");'>底层变量field参考</a>]</td>
            </tr>
            <tr> 
              <td height="80">
			  <textarea name="innertext" cols="80" rows="6" id="myinnertext" style="width:80%;height:120px"><?php echo $ctag->GetInnerText()?></textarea> 
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
              <td height="50"> &nbsp; 
                <input name="Submit2" type="submit" id="Submit2" value="保存一个列表" class='nbt'>
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