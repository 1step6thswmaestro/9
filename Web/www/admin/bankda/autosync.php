<?
$Dir="../../";
set_time_limit(0);
require_once($Dir."lib/init.php");
require_once($Dir."lib/lib.php");
require_once($Dir."lib/class/XMLParser.php");
require_once($Dir."lib/class/pages.php");

require_once($Dir."lib/class/bankda.php");

$bankda = new bankda();
$bankda->_authMatch();
?>