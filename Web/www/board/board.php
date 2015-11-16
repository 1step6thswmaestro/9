<?
$pagetype=$_REQUEST["pagetype"];
if(strlen($pagetype)==0) $pagetype="list";


if($pagetype!="list" && $pagetype!="view" && $pagetype!="write" && $pagetype!="delete" && $pagetype!="delete_comment" && $pagetype!="comment_result" && $pagetype!="passwd_confirm" && $pagetype!="admin_login" && $pagetype!="admin_logout" && $pagetype!="comment_frame" && $pagetype!="comment_delpop") {
	$pagetype="list";
}

@include($pagetype.".php");
?>