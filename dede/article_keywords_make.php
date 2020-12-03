<?php 
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>关键字维护</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center">
    <tr> 
      <td height="20" background='img/tbg.gif'> <table width="98%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="30%" height="18"><strong>关键字维护：</strong></td>
            <td width="70%" align="right"><input name="kw" type="button" id="kw" value="返回关键字管理页" onClick="location='article_keywords_main.php';" class='nbt'></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="48" bgcolor="#FFFFFF">　　本向导帮助自动分析你系统内的关键字，并统计关键字的数量，对其进行排序，你可以清理掉错误的关键字或无意义的关键字，以提高文档关连和搜索的准确性，本分析器是直接在内存中对关键字进行分析排序后加入到数据库的，因此本操作比较占用内存，如果你的文档数量很大并且使用的又是虚拟主机，可能无法完成本操作。<br/>
        　　本程序只分析文章频道的关键字数据，其它频道或自定义频道请自行手工指定内容的关系字。<br/>
        　　如果你的文章是采集回来的，请先用“自动获取关键字”的功能对未有关键字的文章进行分词索引，然后再检测关键字。</td>
    </tr>
    <tr> 
      
    <td height="31" bgcolor="#F8FBFB" align="center">
	[<a href="article_keywords_analyse.php" target="stafrm"><u>检测已有的关键字</u></a>]
	&nbsp;&nbsp;
	[<a href="article_keywords_fetch.php" target="stafrm"><u>自动获取关键字</u></a>]</td>
    </tr>
  <tr bgcolor="#E5F9FF"> 
    <td height="20"> <table width="100%">
        <tr> 
          <td width="74%"><strong>结果：</strong></td>
          <td width="26%" align="right"> <script language='javascript'>
            	function ResizeDiv(obj,ty)
            	{
            		if(ty=="+") document.all[obj].style.pixelHeight += 50;
            		else if(document.all[obj].style.pixelHeight>80) document.all[obj].style.pixelHeight = document.all[obj].style.pixelHeight - 50;
            	}
            	</script>
            [<a href='#' onClick="ResizeDiv('mdv','+');">增大</a>] [<a href='#' onClick="ResizeDiv('mdv','-');">缩小</a>] 
          </td>
        </tr>
      </table></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td id="mtd"> <div id='mdv' style='width:100%;height:100;'> 
        <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
      </div>
      <script language="JavaScript">
	  document.all.mdv.style.pixelHeight = screen.height - 360;
	  </script> </td>
  </tr>
</table>
</body>
</html>
