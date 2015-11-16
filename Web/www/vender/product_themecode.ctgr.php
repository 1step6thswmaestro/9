<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
INCLUDE ("access.php");

$code=$_REQUEST["code"];

?>

<html>
<head>
<title></title>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<link rel=stylesheet href="style.css" type=text/css>
</head>
<body leftmargin=0 topmargin=0>
<form name="iForm" method="post" action="">
<table border="0" cellpadding="0">
<tr>
	<td>
	<select name="theme_sectcode" style=width:170 onchange="parent.ThemeSelCtgrPrdtList()">
	<option value="0">--선택하세요--</option>
<?
	if(strlen($code)==3) {
		$sql = "SELECT codeA,codeB,code_name FROM tblvenderthemecode ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND codeA='".$code."' AND codeB!='000' ";
		$sql.= "ORDER BY sequence DESC ";
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			echo "<option value=\"".$row->codeA.$row->codeB."\">".$row->code_name."</option>\n";
		}
		mysql_free_result($result);
	}
?>
	</select>
	</td>
</tr>
</table>
</form>
</body>
</html>