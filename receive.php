<?php
	$json_str = file_get_contents('php://input'); //接收request的body
	$json_obj = json_decode($json_str); //轉成json格式
	
	$myfile = fopen("log.txt", "w+") or die("Unable to open file!"); //設定一個log.txt來印訊息
	//fwrite($myfile, "\xEF\xBB\xBF".$json_str); //在字串前面加上\xEF\xBB\xBF轉成utf8格式
	
	$sender_userid = $json_obj->events[0]->source->userId; //取得訊息發送者的id
	$sender_txt = $json_obj->events[0]->message->text; //取得訊息內容
	$sender_replyToken = $json_obj->events[0]->replyToken; //取得訊息的replyToken

	$sql = "SELECT * FROM course";
	$result = sql_select_fetchALL($sql);
	$course_name = "";
	foreach($result as $a){
		$course_name = $a['course_name'];
	}
	
	$response = array (
		"replyToken" => $sender_replyToken,
		"messages" => welcome()
	);
	
	fwrite($myfile, "\xEF\xBB\xBF".$response); //在字串前面加上\xEF\xBB\xBF轉成utf8格式
	
	//fwrite($myfile, "\xEF\xBB\xBF".json_encode($response)); //在字串前面加上\xEF\xBB\xBF轉成utf8格式
	$header[] = "Content-Type: application/json";
	$header[] = "Authorization: Bearer ch4DaSxjxOTPaO7PR8pKHu67uotfCaPYuLK5zSw70ACvqemT77GTnzqr2b/7+jMIshCmLWf0U7bPLXsqreKz7tGzKkS6e51W8aM18nt+Jshj7DXtIUjvfUV2BZpQxM+NAXrBizCWLCDHc2/XCgrCGwdB04t89/1O/w1cDnyilFU=";
	$ch = curl_init("https://api.line.me/v2/bot/message/reply");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                                                                                                   
	$result = curl_exec($ch);
	curl_close($ch);
  
	function welcome(){
		$json = '{
		  "type": "template",
		  "altText": "this is a buttons template",
		  "template": {
			"type": "buttons",
			"actions": [
			  {
				"type": "postback",
				"label": "報名課程",
				"text": "apply",
				"data": "aaa"
			  },
			  {
				"type": "postback",
				"label": "我的課程",
				"text": "my",
				"data": "ccc"
			  }
			],
			"title": "歡迎來到龍鳳行銷",
			"text": "您可以報名新課程或查看已報名的課程"
		  }
		}';
		return json_decode($json);
	}
	
	function sql_select_fetchALL($sql)
	{   
		$db_server = "localhost";
		$db_name = "dpcleader_course";
		$db_user = "dpcleader_sup";
		$db_passwd = "Ab09800878390";
		
		$con=mysqli_connect($db_server, $db_user, $db_passwd) or die("資料庫登入錯誤");
		if(mysqli_connect_errno($con)){
			echo "ERROR1";
		}
		
		print_r(con);
		mysqli_query($con,"SET NAMES utf8");
		mysqli_select_db($con,$db_name) or die("資料庫連結錯誤");
		
		$row = mysqli_query($con,$sql);
		mysqli_close($con);
		return $row;
	}
?>
