<?
if(substr(getenv("SCRIPT_NAME"),-9)=="/bottom.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
	</td>
</tr>
</table>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>