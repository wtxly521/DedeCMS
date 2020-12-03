<?php 
require_once(dirname(__FILE__)."/inc_typelink.php");
require_once(dirname(__FILE__)."/pub_dedetag.php");
require_once(dirname(__FILE__)."/pub_splitword_www.php");
/******************************************************
//Copyright 2004-2006 by DedeCms.com itprato
//本类的用途是用于文档搜索
******************************************************/
@set_time_limit(0);
//-----------------------
//搜索类
//-----------------------
class SearchView
{
	var $dsql;
	var $dtp;
	var $dtp2;
	var $TypeID;
	var $TypeLink;
	var $PageNo;
	var $TotalPage;
	var $TotalResult;
	var $PageSize;
	var $ChannelType;
	var $TempInfos;
	var $Fields;
	var $PartView;
	var $StartTime;
	var $Keywords;
	var $OrderBy;
	var $SearchType;
	var $KType;
	var $Keyword;
	//-------------------------------
	//php5构造函数
	//-------------------------------
	function __construct($typeid,$keyword,$orderby,$achanneltype="0",
	                        $searchtype="",$starttime=0,$upagesize=20,$kwtype=1)
 	{
 		if(empty($upagesize)) $upagesize = 10;
 		$this->TypeID = $typeid;
 		$this->Keyword = $keyword;
	  $this->OrderBy = $orderby;
	  $this->KType = $kwtype;
	  $this->PageSize = $upagesize;
	  $this->StartTime = $starttime;
 		$this->ChannelType = $achanneltype;
	  
	  if($searchtype=="") $this->SearchType = "titlekeyword";
	  else $this->SearchType = $searchtype;
 		
 		$this->dsql = new DedeSql(false);
 		$this->dtp = new DedeTagParse();
 		$this->dtp->SetNameSpace("dede","{","}");
 		$this->dtp2 = new DedeTagParse();
 		$this->dtp2->SetNameSpace("field","[","]");
 		$this->TypeLink = new TypeLink($typeid);
 		$this->Keywords = $this->GetKeywords($keyword);
 
 		//设置一些全局参数的值
 		foreach($GLOBALS['PubFields'] as $k=>$v) $this->Fields[$k] = $v;
 		
 		$this->CountRecord();
 		
 		$tempfile = $GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir']."/".$GLOBALS['cfg_df_style']."/search.htm";
 		if(!file_exists($tempfile)||!is_file($tempfile)){
 			$this->Close();
 			echo "模板文件：'".$tempfile."' 不存在，无法解析！";
 			exit();
 		}
 		$this->dtp->LoadTemplate($tempfile);
 		$this->TempInfos['tags'] = $this->dtp->CTags;
 		$this->TempInfos['source'] = $this->dtp->SourceString;
 		if($this->PageSize=="") $this->PageSize = 20;
    $this->TotalPage = ceil($this->TotalResult/$this->PageSize);
    
    if($this->PageNo==1){
    	$this->dsql->ExecuteNoneQuery("Update #@__search_keywords set result='".$this->TotalResult."' where keyword='".addslashes($keyword)."'; ");
    }
    
  }
  //php4构造函数
 	//---------------------------
 	function SearchView($typeid,$keyword,$orderby,$achanneltype="all",
 	                     $searchtype="",$starttime=0,$upagesize=20,$kwtype=1)
  {
 		$this->__construct($typeid,$keyword,$orderby,$achanneltype,$searchtype,$starttime,$upagesize,$kwtype);
 	}
 	//---------------------------
 	//关闭相关资源
 	//---------------------------
 	function Close()
 	{
 		$this->dsql->Close();
 		$this->TypeLink->Close();
 	}
 	//获得关键字的分词结果，并保存到数据库
  //--------------------------------
  function GetKeywords($keyword){
	   $keyword = cn_substr($keyword,50);
	   $row = $this->dsql->GetOne("Select spwords From #@__search_keywords where keyword='".addslashes($keyword)."'; ");
	   if(!is_array($row)){
		   if(strlen($keyword)>7){
		      $sp = new SplitWord();
	        $keywords = $sp->SplitRMM($keyword);
	        $sp->Clear();
	        $keywords = ereg_replace("[ ]{1,}"," ",trim($keywords));
	     }else{
	        $keywords = $keyword;
	     }
	     $inquery = "INSERT INTO `#@__search_keywords`(`keyword`,`spwords`,`count`,`result`,`lasttime`) 
          VALUES ('".addslashes($keyword)."', '".addslashes($keywords)."', '1', '0', '".mytime()."');
       ";
	     $this->dsql->ExecuteNoneQuery($inquery);
	  }else{
		   $this->dsql->ExecuteNoneQuery("Update #@__search_keywords set count=count+1,lasttime='".mytime()."' where keyword='".addslashes($keyword)."'; ");
		   $keywords = $row['spwords'];
	  }
	  return $keywords;
  }
 	//----------------
 	//获得关键字SQL
 	//----------------
  function GetKeywordSql()
 	{
 		$ks = explode(" ",$this->Keywords);
 		$kwsql = "";
 		foreach($ks as $k){
 			$k = trim($k);
 			if(strlen($k)<2) continue;
 			//if(ord($k[0])>0x80 && strlen($k)<3) continue;
 			$k = addslashes($k);
 			if($this->SearchType=="title"){
 				 if($this->KType==1) $kwsql .= " Or #@__archives.title like '%$k%' ";
 				 else $kwsql .= " And #@__archives.title like '%$k%' ";
 			}else{
 				 if($this->KType==1) $kwsql .= " Or CONCAT(#@__archives.title,' ',#@__archives.description,' ',#@__archives.writer,' ',#@__archives.source) like '%$k%' Or CONCAT(' ',#@__archives.keywords,' ') like '%$k %' ";
 			   else $kwsql .= " And (CONCAT(#@__archives.title,' ',#@__archives.description,' ',#@__archives.writer,' ',#@__archives.source) like '%$k%' Or CONCAT(' ',#@__archives.keywords,' ') like '%$k %') ";
 			}
 		}
 		if($this->KType==1) $kwsql = " ( ".ereg_replace("^ Or","",$kwsql)." ) ";
 		else $kwsql = " ( ".ereg_replace("^ And","",$kwsql)." ) ";
 		
 		if($kwsql==" (  ) ") return "";
 		else return $kwsql;
 	}
 	//-------------------
 	//获得相关的关键字
 	//-------------------
 	function GetLikeWords($num=8){
 		$ks = explode(" ",$this->Keywords);
 		$lsql = "";
 		foreach($ks as $k){
 			$k = trim($k);
 	    if(strlen($k)<2) continue;
 			if(ord($k[0])>0x80 && strlen($k)<3) continue;
 			$k = addslashes($k);
 			if($lsql=="") $lsql = $lsql." CONCAT(spwords,' ') like '%$k %' ";
 			else $lsql = $lsql." Or CONCAT(spwords,' ') like '%$k %' ";
 		}
 		if($lsql=="") return "";
 		else{
 			$likeword = "";
 			$lsql = "(".$lsql.") And Not(keyword like '".addslashes($this->Keyword)."') ";
 			$this->dsql->SetQuery("Select keyword,count From #@__search_keywords where $lsql order by lasttime desc limit 0,$num; ");
 			$this->dsql->Execute('l');
 			while($row=$this->dsql->GetArray('l')){
 				 if($row['count']>1000) $fstyle=" style='font-size:11pt;color:red'";
 				 else if($row['count']>300) $fstyle=" style='font-size:10pt;color:green'";
 				 else $style = "";
 				 $likeword .= "　<a href='search.php?keyword=".urlencode($row['keyword'])."&searchtype=titlekeyword'".$style."><u>".$row['keyword']."</u></a> ";
 			}
 			return $likeword;
 		}
 	}
 	//----------------
 	//加粗关键字
 	//----------------
 	function GetRedKeyWord($fstr)
 	{
 		$ks = explode(" ",$this->Keywords);
 		$kwsql = "";
 		foreach($ks as $k){
 			$k = trim($k);
 			if($k=="") continue;
 			if(ord($k[0])>0x80 && strlen($k)<3) continue;
 			$fstr = str_replace($k,"<font color='red'>$k</font>",$fstr);
 		}
 		return $fstr;
 	}
 	//------------------
 	//统计列表里的记录
 	//------------------
 	function CountRecord()
 	{
 		$this->TotalResult = -1;
 		if(isset($GLOBALS['TotalResult'])) $this->TotalResult = $GLOBALS['TotalResult'];
 		if(isset($GLOBALS['PageNo'])) $this->PageNo = $GLOBALS['PageNo'];
 		else $this->PageNo = 1;
 		
 		if($this->TotalResult==-1)
 		{
 		  $ksql = $this->GetKeywordSql();
 		  if($ksql!="") $ksql = " And ".$ksql;
 		  $addSql  = " arcrank > -1 $ksql";
 		  if($this->TypeID > 0) $addSql .= " And (".$this->TypeLink->GetSunID($this->TypeID,"#@__archives",0)." Or #@__archives.typeid2='".$this->TypeID."') ";
 		  if($this->StartTime > 0) $addSql .= " And senddate>'".$this->StartTime."' ";
 		  if($this->ChannelType != 0 ) $addSql .= " And channel='".$this->ChannelType."'";
 		
 	    $cquery = "Select count(*) as dd From #@__archives where $addSql";
      $row = $this->dsql->GetOne($cquery);
 		  if(is_array($row)) $this->TotalResult = $row['dd'];
 		  else $this->TotalResult = 0;
 	 }
 		
 	}
 	//------------------
 	//显示列表
 	//------------------
 	function Display()
 	{
 		foreach($this->dtp->CTags as $tagid=>$ctag){
 			$tagname = $ctag->GetName();
 			if($tagname=="list"){
 				$limitstart = ($this->PageNo-1) * $this->PageSize;
 				$row = $this->PageSize;
 				if(trim($ctag->GetInnerText())==""){ $InnerText = GetSysTemplets("list_fulllist.htm"); }
 				else{ $InnerText = trim($ctag->GetInnerText()); }
 				$this->dtp->Assign($tagid,
 				      $this->GetArcList($limitstart,
 				      $row,
 				      $ctag->GetAtt("col"),
 				      $ctag->GetAtt("titlelen"),
 				      $ctag->GetAtt("infolen"),
 				      $ctag->GetAtt("imgwidth"),
 				      $ctag->GetAtt("imgheight"),
 				      $this->ChannelType,
 				      $this->OrderBy,
 				      $InnerText,
 				      $ctag->GetAtt("tablewidth"))
 				);
 			}
 			else if($tagname=="pagelist"){
 				$list_len = trim($ctag->GetAtt("listsize"));
 				if($list_len=="") $list_len = 3;
 				$this->dtp->Assign($tagid,$this->GetPageListDM($list_len));
 			}
 			else if($tagname=="likewords"){
 				$this->dtp->Assign($tagid,$this->GetLikeWords($ctag->GetAtt('num')));
 			}
 			else if($tagname=="hotwords"){
 				$this->dtp->Assign($tagid,
 				GetHotKeywords($this->dsql,$ctag->GetAtt('num'),$ctag->GetAtt('subday'),$ctag->GetAtt('maxlength')));
 			}
 			else if($tagname=="field") //类别的指定字段
 			{
 					if(isset($this->Fields[$ctag->GetAtt('name')]))
 					  $this->dtp->Assign($tagid,$this->Fields[$ctag->GetAtt('name')]);
 					else
 					  $this->dtp->Assign($tagid,"");
 			}
 			else if($tagname=="channel")//下级频道列表
 			{
 				  if($this->TypeID>0){
 				  	$typeid = $this->TypeID; $reid = $this->TypeLink->TypeInfos['reID'];
 				  }
 				  else{ $typeid = 0; $reid=0; }
 				  
 				  $this->dtp->Assign($tagid,
 				      $this->TypeLink->GetChannelList($typeid,
 				          $reid,
 				          $ctag->GetAtt("row"),
 				          $ctag->GetAtt("type"),
 				          $ctag->GetInnerText()
 				      )
 				  );
 			}//End if
 	  }
 	  $this->Close();
 		$this->dtp->Display();
 	}
 	//----------------------------------
  //获得文档列表
//---------------------------------
  function GetArcList($limitstart=0,$row=10,$col=1,$titlelen=30,$infolen=250,
  $imgwidth=120,$imgheight=90,$achanneltype="all",$orderby="default",$innertext="",$tablewidth="100")
  {
    $typeid=$this->TypeID;
		if($row=="") $row = 10;
		if($limitstart=="") $limitstart = 0;
		if($titlelen=="") $titlelen = 30;
		if($infolen=="") $infolen = 250;
    if($imgwidth=="") $imgwidth = 120;
    if($imgheight=="") $imgheight = 120;
    if($achanneltype=="") $achanneltype = "0";
		if($orderby=="") $orderby="default";
		else $orderby=strtolower($orderby);
		$tablewidth = str_replace("%","",$tablewidth);
		if($tablewidth=="") $tablewidth=100;
		if($col=="") $col=1;
		$colWidth = ceil(100/$col); 
		$tablewidth = $tablewidth."%";
		$colWidth = $colWidth."%";
		$innertext = trim($innertext);
		if($innertext=="") $innertext = GetSysTemplets("search_list.htm");
		
		//按不同情况设定SQL条件
		$ksql = $this->GetKeywordSql();
 		if($ksql!="") $ksql = " And ".$ksql;
		$orwhere = " #@__archives.arcrank > -1 $ksql";
 		
 		if($this->StartTime > 0) $orwhere .= " And #@__archives.senddate > '".$this->StartTime."' ";
 		
 		if($this->ChannelType != 0) $orwhere .= " And #@__archives.channel='".$this->ChannelType."'";
 		if($this->TypeID>0) $orwhere .= " And (".$this->TypeLink->GetSunID($this->TypeID,"#@__archives",0)." Or #@__archives.typeid2='".$this->TypeID."') ";
		
		//排序方式
		$ordersql = "";
		if($orderby=="senddate") $ordersql=" order by #@__archives.senddate desc";
		else if($orderby=="pubdate") $ordersql=" order by #@__archives.pubdate desc";
    else if($orderby=="id") $ordersql="  order by #@__archives.ID desc";
		else $ordersql=" order by #@__archives.sortrank desc";
		
		//
		//----------------------------
		$query = "Select #@__archives.ID,#@__archives.title,#@__archives.typeid,#@__archives.ismake,#@__archives.money,
		#@__archives.description,#@__archives.pubdate,#@__archives.senddate,#@__archives.arcrank,#@__archives.click,
		#@__archives.litpic,#@__arctype.typedir,#@__arctype.typename,#@__arctype.isdefault,
		#@__arctype.defaultname,#@__arctype.namerule,#@__arctype.namerule2,#@__arctype.ispart, 
		#@__arctype.moresite,#@__arctype.siteurl
		from #@__archives 
		left join #@__arctype on #@__archives.typeid=#@__arctype.ID
		where $orwhere $ordersql limit $limitstart,$row";
		
		//echo $query;
		
		$this->dsql->SetQuery($query);
		$this->dsql->Execute("al");
    $artlist = "";
    if($col>1) $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
    $this->dtp2->LoadSource($innertext);
    for($i=0;$i<$row;$i++)
		{
       if($col>1) $artlist .= "<tr>\r\n";
       for($j=0;$j<$col;$j++)
			 {
         if($col>1) $artlist .= "<td width='$colWidth'>\r\n";
         if($row = $this->dsql->GetArray("al"))
         {
           //处理一些特殊字段
           $row["arcurl"] = GetFileUrl($row["ID"],$row["typeid"],$row["senddate"],$row["title"],
                        $row["ismake"],$row["arcrank"],$row["namerule"],$row["typedir"],$row["money"],true,$row["siteurl"]);
           $row["description"] = $this->GetRedKeyWord(cn_substr($row["description"],$infolen));
           $row["title"] = $this->GetRedKeyWord(cn_substr($row["title"],$titlelen));
           $row["id"] =  $row["ID"];
           if($row["litpic"]=="") $row["litpic"] = $GLOBALS["cfg_plus_dir"]."/img/dfpic.gif";
           $row["picname"] = $row["litpic"];
           $row["typeurl"] = $this->GetListUrl($row["typeid"],$row["typedir"],$row["isdefault"],$row["defaultname"],$row["ispart"],$row["namerule2"],$row["siteurl"]);
           $row["info"] = $row["description"];
           $row["filename"] = $row["arcurl"];
           $row["stime"] = GetDateMK($row["pubdate"]);
           $row["textlink"] = "<a href='".$row["filename"]."'>".$row["title"]."</a>";
           $row["typelink"] = "[<a href='".$row["typeurl"]."'>".$row["typename"]."</a>]";
           $row["imglink"] = "<a href='".$row["filename"]."'><img src='".$row["picname"]."' border='0' width='$imgwidth' height='$imgheight'></a>";
           $row["image"] = "<img src='".$row["picname"]."' border='0' width='$imgwidth' height='$imgheight'>";
           $row["phpurl"] = $GLOBALS["cfg_plus_dir"];
 		       $row["templeturl"] = $GLOBALS["cfg_templets_dir"];
 		       $row["memberurl"] = $GLOBALS["cfg_member_dir"];
           //---------------------------
           if(is_array($this->dtp2->CTags)){
       	     foreach($this->dtp2->CTags as $k=>$ctag){
       		 	   if(isset($row[$ctag->GetName()])) $this->dtp2->Assign($k,$row[$ctag->GetName()]);
       		 	   else $this->dtp2->Assign($k,"");
       	    }
           }
           $artlist .= $this->dtp2->GetResult();
         }//if hasRow
         else{
         	 $artlist .= "";
         }
         if($col>1) $artlist .= "</td>\r\n";
       }//Loop Col
       if($col>1) $artlist .= "</tr>\r\n";
     }//Loop Line
     if($col>1) $artlist .= "</table>\r\n";
     $this->dsql->FreeResult("al");
     return $artlist;
  }
  //---------------------------------
  //获取动态的分页列表
  //---------------------------------
	function GetPageListDM($list_len)
	{
		$prepage="";
		$nextpage="";
		$prepagenum = $this->PageNo-1;
		$nextpagenum = $this->PageNo+1;
		if($list_len==""||ereg("[^0-9]",$list_len)) $list_len=3;
		$totalpage = ceil($this->TotalResult/$this->PageSize);
		if($totalpage<=1 && $this->TotalResult>0) return "共1页/".$this->TotalResult."条记录"; 
		if($this->TotalResult == 0) return "共0页/".$this->TotalResult."条记录"; 
		
		$purl = $this->GetCurUrl();
		
		$geturl = "keyword=".urlencode($this->Keyword)."&searchtype=".$this->SearchType;
		$geturl .= "&channeltype=".$this->ChannelType."&orderby=".$this->OrderBy;
		$geturl .= "&kwtype=".$this->KType."&pagesize=".$this->PageSize;
		$geturl .= "&typeid=".$this->TypeID."&TotalResult=".$this->TotalResult."&";
		
		$hidenform = "<input type='hidden' name='typeid' value='".$this->TypeID."'>\r\n";
		$hidenform .= "<input type='hidden' name='TotalResult' value='".$this->TotalResult."'>\r\n";
		
		$purl .= "?".$geturl;
		
		//获得上一页和下一页的链接
		if($this->PageNo != 1){
			$prepage.="<td width='50'><a href='".$purl."PageNo=$prepagenum'>上一页</a></td>\r\n";
			$indexpage="<td width='30'><a href='".$purl."PageNo=1'>首页</a></td>\r\n";
		}
		else{
			$indexpage="<td width='30'>首页</td>\r\n";
		}	
		
		if($this->PageNo!=$totalpage && $totalpage>1){
			$nextpage.="<td width='50'><a href='".$purl."PageNo=$nextpagenum'>下一页</a></td>\r\n";
			$endpage="<td width='30'><a href='".$purl."PageNo=$totalpage'>末页</a></td>\r\n";
		}
		else{
			$endpage="<td width='30'>末页</td>\r\n";
		}
		//获得数字链接
		$listdd="";
		$total_list = $list_len * 2 + 1;
		if($this->PageNo >= $total_list) {
    		$j = $this->PageNo-$list_len;
    		$total_list = $this->PageNo+$list_len;
    		if($total_list>$totalpage) $total_list=$totalpage;
		}	
		else{ 
   			$j=1;
   			if($total_list>$totalpage) $total_list=$totalpage;
		}
		for($j;$j<=$total_list;$j++)
		{
   		if($j==$this->PageNo) $listdd.= "<td>$j&nbsp;</td>\r\n";
   		else $listdd.="<td><a href='".$purl."PageNo=$j'>[".$j."]</a>&nbsp;</td>\r\n";
		}
		$plist  =  "<table border='0' cellpadding='0' cellspacing='0'>\r\n";
		$plist .= "<tr align='center' style='font-size:10pt'>\r\n";
		$plist .= "<form name='pagelist' action='".$this->GetCurUrl()."'>$hidenform";
		$plist .= $indexpage;
		$plist .= $prepage;
		$plist .= $listdd;
		$plist .= $nextpage;
		$plist .= $endpage;
		if($totalpage>$total_list){
			$plist.="<td width='36'><input type='text' name='PageNo' style='width:30;height:18' value='".$this->PageNo."'></td>\r\n";
			$plist.="<td width='30'><input type='submit' name='plistgo' value='GO' style='width:24;height:18;font-size:9pt'></td>\r\n";
		}
		$plist .= "</form>\r\n</tr>\r\n</table>\r\n";
		return $plist;
	}
 	//--------------------------
 	//获得一个指定的频道的链接
 	//--------------------------
 	function GetListUrl($typeid,$typedir,$isdefault,$defaultname,$ispart,$namerule2)
  {
  	return GetTypeUrl($typeid,MfTypedir($typedir),$isdefault,$defaultname,$ispart,$namerule2);
  }
  //---------------
  //获得当前的页面文件的url
  //----------------
  function GetCurUrl()
	{
		if(!empty($_SERVER["REQUEST_URI"])){
			$nowurl = $_SERVER["REQUEST_URI"];
			$nowurls = explode("?",$nowurl);
			$nowurl = $nowurls[0];
		}
		else
		{ $nowurl = $_SERVER["PHP_SELF"]; }
		return $nowurl;
	}
}//End Class
?>