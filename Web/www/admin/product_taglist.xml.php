<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	exit;
}

/*
쇼핑몰 태그 목록
*/

header("Cache-Control: no-cache, must-revalidate"); 
header("Content-Type: text/xml; charset=EUC-KR");

$type=$_GET["type"];
$prcode=$_GET["prcode"];
$org_tagname=$_GET["tagname"];

$sql = "SELECT tag FROM tblproduct WHERE productcode='".$prcode."' ";
$result=mysql_query($sql,get_db_conn());
$taglist="";
$cnt=0;
if($row=mysql_fetch_object($result)) {
	if($type=="del" && strlen($org_tagname)>0 && strlen($prcode)==18) {
		if(_DEMOSHOP=="OK" && getenv("REMOTE_ADDR")!=_ALLOWIP) {

		} else {
			$deltagname="<".$org_tagname.">,";
			$row->tag=str_replace($deltagname,"",$row->tag);
			$sql = "UPDATE tblproduct SET tag='".$row->tag."' WHERE productcode='".$prcode."' ";
			mysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tbltagproduct WHERE productcode='".$prcode."' AND tagname='".$org_tagname."' ";
			mysql_query($sql,get_db_conn());

			$sql = "SELECT COUNT(*) as count FROM tbltagproduct WHERE tagname='".$org_tagname."' ";
			$result2=mysql_query($sql,get_db_conn());
			$row2=mysql_fetch_object($result2);
			mysql_free_result($result2);
			if($row2->count==0) {
				$sql = "DELETE FROM tbltagsearch WHERE tagname='".$org_tagname."' ";
				mysql_query($sql,get_db_conn());
				$sql = "DELETE FROM tbltagsearchall WHERE tagname='".$org_tagname."' ";
				mysql_query($sql,get_db_conn());

				DeleteCache("tbltagsearch".date("Ymd").".cache");
			}
		}
	}

	if(strlen($row->tag)>0) {
		$tag=explode(">,",$row->tag);
		for($i=0;$i<count($tag);$i++) {
			$cnt++;
			if($cnt==count($tag)) {
				$tagname=substr($tag[$i],1,-1);
			} else {
				$tagname=substr($tag[$i],1);
			}

			if(strlen($tagname)>0) {
				$taglist.="<a href=\"javascript:delTagName('".$prcode."','".$tagname."');\"><img src=images/x.gif border=0 hspace=2></a>".$tagname.", ";
			}
		}
	}
}
mysql_free_result($result);

if(strlen($taglist)==0) {
	$taglist="등록된 태그가 없습니다.";
}
echo "<div style=\"line-height:13pt\">\n";
echo "	".$taglist."\n";
echo "</div>";
?>