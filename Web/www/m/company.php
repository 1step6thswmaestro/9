<? include_once('header.php'); ?>
<?
//ȸ��Ұ�
$row_company = mysql_fetch_array(mysql_query("select content from tbldesign"));
include $skinPATH."company.php";
?>
<? include_once('footer.php'); ?>