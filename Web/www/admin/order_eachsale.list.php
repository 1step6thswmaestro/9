<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

$mode=$_POST["mode"];
$code=$_POST["code"];
$prcode=$_POST["prcode"];

?>

<? INCLUDE "header.php"; ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 height="100%">
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<TR>
	<TD width="100%" height="100%"><select name=prcode size=15 style="width:100%;height:100%" onchange="parent.prcode=this.options[this.selectedIndex].value" class="select">
<?
		$count=0;
		if (strlen($code)==12) {
			$likecode=substr($code,0,3);
			if(substr($code,3,3)!="000") {
				$likecode.=substr($code,3,3);
				if(substr($code,6,3)!="000") {
					$likecode.=substr($code,6,3);
					if(substr($code,9,3)!="000") {
						$likecode.=substr($code,9,3);
					}
				}
			}

			$sql = "SELECT productcode,productname FROM tblproduct ";
			$sql.= "WHERE productcode LIKE '".$likecode."%' ORDER BY date DESC";
			$result = mysql_query($sql,get_db_conn());
			while ($row = mysql_fetch_object($result)) {
				$count++;
				$sale="";
				//if($row->quantity<=0 && $row->quantity<>NULL) $sale=" (Ç°Àý)";
				if ($prcode == $row->productcode) {
					echo "<option selected value=\"".$row->productcode."\">".$count.". ".$row->productname.$sale;
					$productname=$row->productname;
				} else {
				  echo "<option value=\"".$row->productcode."\">".$count.". ".$row->productname.$sale;
				}
			}
			echo "</option>\n";
		}
		mysql_free_result($result);
?>
	</select></TD>
</TR>
</form>
</TABLE>
</body>
</html>