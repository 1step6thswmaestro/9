<?
$Dir="../";
include_once($Dir."lib/init.php");

if (strpos(getenv("HTTP_REFERER"),getenv("HTTP_HOST"))==-1) exit;
$file_name=$_REQUEST["file_name"];
?>

<script>
try {
	opener.writeForm.up_filename.value = "<?=$file_name?>";
} catch (e) {}
window.close();
</script>