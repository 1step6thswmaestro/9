<?
if(!eregi(getenv("HTTP_HOST"),getenv("HTTP_REFERER"))) {
	header("HTTP/1.0 404 Not Found");
	exit;
} else {
?>
function ClipCopy(url) {
	var tmp;
	tmp = window.clipboardData.setData('Text', url);
	if(tmp) {
		alert('�ּҰ� ����Ǿ����ϴ�.');
	}
}

function custRegistMinishop() {
	if(document.custregminiform.memberlogin.value!="Y") {
		alert("�α��� �� �̿��� �����մϴ�.");
		return;
	}
	owin=window.open("about:blank","miniregpop","width=100,height=100,scrollbars=no");
	owin.focus();
	document.custregminiform.target="miniregpop";
	document.custregminiform.action="minishop.regist.pop.php";
	document.custregminiform.submit();
}

function GoItem(productcode) {
	document.location.href="productdetail.php?productcode="+productcode;
}

function GoSection(sellvidx,tgbn,code) {
	//tgbn : 10=>�Ϲ�ī�װ�, 20=>�׸�ī�װ�
	//code : 6�ڸ�
	if(tgbn.length>0) {
		document.location.href="minishop.productlist.php?sellvidx="+sellvidx+"&tgbn="+tgbn+"&code="+code;
	} else {
		document.location.href="minishop.php?sellvidx="+sellvidx+"&code="+code;
	}
}

function GoNoticeList(sellvidx,block,gotopage) {
	url="minishop.notice.php?sellvidx="+sellvidx;
	if(typeof block!="undefined") url+="&block="+block;
	if(typeof gotopage!="undefined") url+="&gotopage="+gotopage;
	document.location.href=url;
}

function GoNoticeView(sellvidx,artid,block,gotopage) {
	url="minishop.notice.php?type=view&sellvidx="+sellvidx+"&artid="+artid;
	if(typeof block!="undefined") url+="&block="+block;
	if(typeof gotopage!="undefined") url+="&gotopage="+gotopage;
	document.location.href=url;
}
<?
}
?>