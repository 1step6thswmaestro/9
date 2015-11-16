<?php
require_once(dirname(__FILE__).'/twitteroauth/twitteroauth.php');

class tempTwitter{
	var $request = array();
	var $oauth_callback = NULL;
	var $connection = NULL;
	var $status = false;

	function tempTwitter(){
		$this->__construct();
	}

	function __construct(){
		$this->oauth_callback = 'http://'.$_SERVER['HTTP_HOST'].'/front/twitter.php';
		$this->_getAuthcode();
		if(isset($_REQUEST['oauth_token'])){
			 $this->_callback();
		}else if($this->status){
			//$this->_redirect();
			$this->connection = new TwitterOAuth(TWITTER_ID, TWITTER_SECRET, $this->request['oauth_token'], $this->request['oauth_token2']);
			$content = $this->connection->get('account/verify_credentials');

			if($content->id == $this->request['user_id']){
				$sql = "update tblmembersnsinfo set screen_name	= '".iconv("UTF-8","EUC-KR", $content->name)."',state='Y', profile_img	= '".$content->profile_image_url."' where id='".$this->request['id']."' and type='t'";
				@mysql_query($sql,get_db_conn());
			}else{
				$this->_clearSync();
			}
		}
	}

	function _clearSync(){
		$this->status = false;
		if(isset($_SESSION)) $_SESSION = array();
		$this->request = array();
		$sql = "delete from tblmembersnsinfo  where id='".$this->request['id']."' and type='t'";
		@mysql_query($sql,get_db_conn());
	}

	function _postMsg($comment){
		$result = $this->connection->post('statuses/update', array('status' => $comment));
		return $result;
	}

	function _dumy(){
		/* If access tokens are not available redirect to connect page. */
		if(empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
			header('Location: ./clearsessions.php');
		}
		/* Get user access tokens out of the session. */
		$access_token = $_SESSION['access_token'];

		/* Create a TwitterOauth object with consumer/user tokens. */
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

		/* If method is set change API call made. Test is called by default. */
		$content = $connection->get('account/verify_credentials');

	}

	function _status(){
		return $this->status;
	}

	function _callback(){
		global $_ShopInfo;
	//	if(!_empty($this->request['oauth_token']) && !_empty($this->request['oauth_token2'])){
		if($_SESSION['oauth_token'] === $_REQUEST['oauth_token']){
			$this->connection = new TwitterOAuth(TWITTER_ID, TWITTER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			$access_token = $this->connection->getAccessToken($_REQUEST['oauth_verifier']);
			$_SESSION['access_token'] = $access_token;
			unset($_SESSION['oauth_token']);
			unset($_SESSION['oauth_token_secret']);

			if (200 == $this->connection->http_code && !_empty($_ShopInfo->getMemid())){
				/* If method is set change API call made. Test is called by default. */
				$content = $connection->get('account/verify_credentials');


				$_SESSION['status'] = 'verified';
				$sql = "REPLACE tblmembersnsinfo SET ";
				$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
				$sql.= "type		= 't', ";
				$sql.= "user_id		= '".$access_token['user_id']."', ";
				$sql.= "oauth_token	= '".$access_token['oauth_token']."', ";
				$sql.= "oauth_token2= '".$access_token['oauth_token_secret']."', ";
				$sql.= "screen_name	= '".iconv("UTF-8","EUC-KR", $content->name)."', ";
				$sql.= "profile_img	= '".$content->profile_image_url."', ";
				$sql.= "state		= 'Y', ";
				$sql.= "regidate	= '".time()."' ";
				mysql_query($sql,get_db_conn());
			  	return true;
			} else {
			 	header('Location: ./clearsessions.php');
			 	$this->_clearSession();
				//  $_SESSION = array();
			}
		}
		return false;
	}

	function _getAuthcode(){
		global $_data,$_ShopInfo;
		$this->request = array();
		if($_data->sns_ok == "Y" && !_empty($_ShopInfo->getMemid())){
			$sql = "SELECT * FROM tblmembersnsinfo WHERE id='".$_ShopInfo->getMemid()."' AND type ='t' ";
			if(false !== $res = mysql_query($sql,get_db_conn())){
				if(mysql_num_rows($res)){
					list($this->request['id'],,$this->request['user_id'],$this->request['oauth_token'],$this->request['oauth_token2']) = mysql_fetch_row($res);
					if(!_empty($this->request['oauth_token']) && !_empty($this->request['oauth_token2'])) $this->status = true;
				}
			}
		}
	}

	function _requestAuth(){
		$this->connection = new TwitterOAuth(TWITTER_ID, TWITTER_SECRET);
		$request_token = $this->connection->getRequestToken($this->oauth_callback);
		$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		switch ($this->connection->http_code){
		  case 200:
			$url = $this->connection->getAuthorizeURL($token);
			header('Location: ' . $url);
			break;
		  default:
			echo 'Could not connect to Twitter. Refresh the page or try again later.';
		}
	}


	function _redirect(){
		$this->connection = new TwitterOAuth(TWITTER_ID, TWITTER_SECRET, $this->request['oauth_token'], $this->request['oauth_token2']);
		//$request_token = $this->connection->getRequestToken($this->oauth_callback);

		if($this->status){
			_pr($this->connection);
			$url = $this->connection->authorizeURL() ;
			$param = array('oauth_token'=>$this->request['oauth_token']);
			$result = $this->connection->get($url,$param);
			_pr($this->connection);
			_pr($result);
			exit;
		}
	}

	function _clearSession(){
		session_start();
		session_destroy();
		/* Redirect to page with the connect to Twitter option. */
		header('Location: ./twitter.php');
	}
}
?>