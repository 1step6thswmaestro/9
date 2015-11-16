<?
if(!eregi(getenv("HTTP_HOST"),getenv("HTTP_REFERER"))) {
	header("HTTP/1.0 404 Not Found");
	exit;
} else {
?>
function ACodeSendIt(f,obj) {
	if(obj.value.length>0) {
		f.codeB_name.value = "";
		f.codeC_name.value = "";
		f.codeD_name.value = "";
		f.codeA_name.value = obj.text;
		f.category_view.value = f.codeA_name.value;

		if(obj.ctype=="X") {
			f.code.value = obj.value+"000000000";
		} else {
			f.code.value = obj.value;
		}

		burl = "product_register.ctgr.php?code=" + obj.value;
		curl = "product_register.ctgr.php";
		durl = "product_register.ctgr.php";
		BCodeCtgr.location.href = burl;
		CCodeCtgr.location.href = curl;
		DCodeCtgr.location.href = durl;
	}
}

function sectSendIt(f,obj,x) {
	if(obj.value.length>0) {
		if(x == 2) {
			f.codeC_name.value = "";
			f.codeD_name.value = "";
			if(obj.ctype=="X") {
				f.code.value = obj.value+"000000";
			} else {
				f.code.value = obj.value;
			}
			durl = "product_register.ctgr.php";
			f.codeB_name.value = obj.text;
			f.category_view.value = f.codeA_name.value + " > " + f.codeB_name.value;
			url = "product_register.ctgr.php?code="+obj.value;
			parent.CCodeCtgr.location.href = url;
			parent.DCodeCtgr.location.href = durl;
		} else if(x == 3) {
			f.codeD_name.value = "";
			f.codeC_name.value = obj.text;
			if(obj.ctype=="X") {
				f.code.value = obj.value+"000";
			} else {
				f.code.value = obj.value;
			}
			f.category_view.value = f.codeA_name.value + " > " + f.codeB_name.value + " > " + f.codeC_name.value;
			url = "product_register.ctgr.php?code="+obj.value;
			parent.DCodeCtgr.location.href = url;
		} else if(x == 4) {
			f.code.value = obj.value;
			f.codeD_name.value = obj.text;
			f.category_view.value = f.codeA_name.value + " > " + f.codeB_name.value + " > " + f.codeC_name.value + " > " + f.codeD_name.value;
		}
	}
}
<?
}
?>