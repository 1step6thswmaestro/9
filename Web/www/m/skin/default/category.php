<?
//���� ī�װ��� ���ϱ�



if($_GET[codeD]!="000" && $_GET[codeD])
{
	$query_cg_name = mysql_query("SELECT code_name FROM tblproductcode WHERE codeA = '$_GET[codeA]' AND codeB = '$_GET[codeB]' AND codeC = '$_GET[codeC]' AND codeD = '$_GET[codeD]'");
	$depth = "D";
}
else if($_GET[codeC]!="000" && $_GET[codeC])
{
	$query_cg_name = mysql_query("SELECT code_name FROM tblproductcode WHERE codeA = '$_GET[codeA]' AND codeB = '$_GET[codeB]' AND codeC = '$_GET[codeC]'");
	$depth = "C";
	
}
else if($_GET[codeB]!="000" && $_GET[codeB])
{
	$query_cg_name = mysql_query("SELECT code_name FROM tblproductcode WHERE codeA = '$_GET[codeA]' AND codeB = '$_GET[codeB]'");
	$depth = "B";
	
}
else if($_GET[codeA]!="000" && $_GET[codeA])
{
	$query_cg_name = mysql_query("SELECT code_name FROM tblproductcode WHERE codeA = '$_GET[codeA]'");
	$depth = "A";
}

if($depth)
{
	$row_cg_name = mysql_fetch_array($query_cg_name);
	$cg_name = $row_cg_name[0];
}
else {	$cg_name = "ī�װ�"; }

?>



<div id="content">
	<div class="h_area2">
		<h2><?=$cg_name?></h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	<!-- ī�װ� ����Ʈ -->
	<div class="category_list">
		<ul class="list_type02">
<?
	
	if($depth=="D")
	{
		$query = "SELECT codeA , codeB, codeC, codeD, type, code_name FROM tblproductcode WHERE group_code!='NO' and codeA = '$_GET[codeA]' and codeB = '$_GET[codeB]' and codeC = '$_GET[codeC]' and codeD = '$_GET[codeD]' $qry_mobile_display ORDER BY sequence DESC ";
	}
	else if($depth=="C")
	{
		$query = "SELECT codeA , codeB, codeC, codeD, type, code_name FROM tblproductcode WHERE group_code!='NO' and codeA = '$_GET[codeA]' and codeB = '$_GET[codeB]' and codeC = '$_GET[codeC]'  $qry_mobile_display ORDER BY sequence DESC ";
	}	
	else if($depth=="B")
	{
		$query = "SELECT codeA , codeB, codeC, codeD, type, code_name FROM tblproductcode WHERE group_code!='NO' and codeA = '$_GET[codeA]' and codeB = '$_GET[codeB]'  $qry_mobile_display ORDER BY sequence DESC ";
	}
	else if($depth=="A")
	{
		$query = "SELECT codeA , codeB, codeC, codeD, type, code_name FROM tblproductcode WHERE group_code!='NO' and codeA = '$_GET[codeA]' $qry_mobile_display ORDER BY sequence DESC ";
	}
	else
	{
		$query = "SELECT codeA , codeB, codeC, codeD, type, code_name FROM tblproductcode WHERE group_code!='NO' AND (type='L' OR type='T' OR type='LX' OR type='TX')  $qry_mobile_display ORDER BY sequence DESC ";		
	}
	
	//echo $query;
	$result = mysql_query($query);
	$i = 0;
	while($row_cg = mysql_fetch_array($result))
	{
		$i++;
		//����ī�װ��� ������ ī�װ���		
		$code = $row_cg[codeA].$row_cg[codeB].$row_cg[codeC].$row_cg[codeD];

		//����ī�װ��� ���� ī�װ����
		/*if( strstr($row_cg[type],"X")) 	{		$str_page = "productlist.php";		}
		else	{		$str_page = "category.php";		}	*/
		$str_page = "productlist.php";	
		//���� ī�װ����� ������Ͽ� ������� �ʴ´�
		if($depth=="C")
		{ 
			//depth �� C ��� D�ܰ踸 ���
			if($row_cg[codeD]!="000") 
			{	
				?>
				<li><a href="<?=$str_page?>?code=<?=$code?>&codeA=<?=$row_cg[codeA]?>&codeB=<?=$row_cg[codeB]?>&codeC=<?=$row_cg[codeC]?>&codeD=<?=$row_cg[codeD]?>" rel="external"><?=$row_cg[code_name]?></a></li>
				<?		
			}			
		}
		else if($depth=="B")
		{ 
			//depth �� B ��� D�ܰ踸 ���
			if($row_cg[codeC]!="000" && $row_cg[codeD]=="000") 
			{	
				?>
				<li><a href="<?=$str_page?>?code=<?=$code?>&codeA=<?=$row_cg[codeA]?>&codeB=<?=$row_cg[codeB]?>&codeC=<?=$row_cg[codeC]?>&codeD=<?=$row_cg[codeD]?>" rel="external"><?=$row_cg[code_name]?></a></li>
				<?		
			}			
		}
		else if($depth=="A")
		{ 
			//depth �� A ��� B�ܰ踸 ���
			if($row_cg[codeB]!="000" && $row_cg[codeC]=="000" && $row_cg[codeD]=="000") 
			{	
				?>
				<li><a href="<?=$str_page?>?code=<?=$code?>&codeA=<?=$row_cg[codeA]?>&codeB=<?=$row_cg[codeB]?>&codeC=<?=$row_cg[codeC]?>&codeD=<?=$row_cg[codeD]?>" rel="external"><?=$row_cg[code_name]?></a></li>
				<?		
			}			
		}
		else
		{
				?>
				<li><a href="<?=$str_page?>?code=<?=$code?>&codeA=<?=$row_cg[codeA]?>&codeB=<?=$row_cg[codeB]?>&codeC=<?=$row_cg[codeC]?>&codeD=<?=$row_cg[codeD]?>" rel="external"><?=$row_cg[code_name]?></a></li>
				<?	
		
		}
				
		?>
		

		
		<!-- <li><a href="productlist.php?code=<?=$row_cg[codeA]?>"><?=$row_cg[code_name]?></a></li> -->
		<!-- <li><a href="<?=$_SERVER[PHP_SELF]?>?code=<?=$row_cg[codeA]?>"><?=$row_cg[code_name]?></a></li> -->
		<!-- <li><a href="productlist_type01.php"><?=$row_cg[code_name]?></a></li> -->
		<?
	}

	if($i==1)
	{
		?><li style="height:30px;text-align:center;padding-top:15px">����ī�װ��� �����Ǿ� ���� �ʽ��ϴ�.</li><?
	
	}

?>
			
		</ul>
	</div>
	<!-- //ī�װ� ����Ʈ -->
	
	<!-- ���ְ��� ���� -->
	<ul class="svc_list">

<?
	$query_t = "SELECT * FROM tblmobiledirectmenu ORDER BY date DESC";
	$result_t = mysql_query($query_t,get_db_conn());
	while($row_t=mysql_fetch_array($result_t))
	{

		
		?>
		<li><a href="http://<?=$row_t[url]?>" rel="external"><div class="icon_area"><img src="<?=$upload_path.$row_t[image]?>" class="img_large"></div><div class="txt_area"><?=$row_t[title]?></div></a></li>
		<?	
	}
?>

	</ul>
	<!-- //���ְ��� ���� -->
</div>