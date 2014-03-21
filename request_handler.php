<?php
class Handler {
	public static function deliver_response($status, $status_message, $data) {
		header ( "HTTP/1.1 $status $status_message" );
		$response ['status'] = $status;
		$response ['status_meassage'] = $status_message;
		$response ['data'] = $data;
		
		$json_response = json_encode ( $response );
		echo $json_response;
	}
	
	
	public static function &get_db_connection() {
		$config = new Phalcon\Config\Adapter\Ini ( './config/config.ini' );
		$connection = new Phalcon\Db\Adapter\Pdo\Mysql ( array (
				"host" => $config->database->host,
				"username" => $config->database->username,
				"password" => $config->database->password,
				"dbname" => $config->database->name 
		) );
		return $connection;
	}
	
	
	public static function upload_video($name, $ext) {
		$savedFile = $name . "." . $ext;
		$file = fopen ( "video/" . $savedFile, "w" );
		if (! $file) {
			deliver_response ( 300, "Failed!", null );
			die ();
		}
		$xml = fopen ( 'php://input', "r" );
		stream_copy_to_stream ( $xml, $file );
		fclose ( $file );
		deliver_response ( 200, "Successfully uploaded " + $name, null );
	}
	
	
	public static function add_new_post() {
	}
	
	
	public static function add_new_user() {
		$inputJSON = file_get_contents ( 'php://input' );
		$input = json_decode ( $inputJSON, TRUE );
		$connection = Handler::get_db_connection ();
		
		$sql = "INSERT INTO `USER`(`USER_ID`, `USER_NAME`) VALUES (?, ?)";
		$success = $connection->execute ( $sql, array (
				$input ['uid'],
				$input ['uname'] 
		) );
		
		if ($success) {
			Handler::deliver_response ( 200, "Success", "" );
		} else {
			Handler::deliver_response ( 400, "Invalid Request", "" );
		}
	}
	
	
	public static function add_new_comment($post_id) {
		echo "add_new_comment" + $post_id;
		$inputJSON = file_get_contents ( 'php://input' );
		$input = json_decode ( $inputJSON, TRUE );
		$connection = Handler::get_db_connection ();
		$success = $connection->insert ( "COMMENTS", array (
				$post_id,
				$input ["uid"],
				$input ["content"],
				false 
		), array (
				"POST_ID",
				"USER_ID",
				"CONTENT",
				"COMMENT_SPAM" 
		) );
		if ($success) {
			deliver_response ( 200, "Success", "" );
		} else {
			deliver_response ( 400, "Invalid Request", "" );
		}
	}
	
	
	public static function get_post_by_postion($long, $lat) {
		$connection = Handler::get_db_connection ();
		$sql = "select * from POST where latitude >= :latup and latitude <= :latdown and longitude >= :latup and longitude <= :latdown";
		$result = $connection->query ( $sql, array (
				"latup" => $long,
				"latdown" => $lat 
		) );
		while ( $comment = $result->fetch () ) {
			echo json_encode ( $comment );
		}
	}
	
	
	public static function get_post_comments($post_id) {
		$connection = Handler::get_db_connection ();
		$sql = "select * from POST INNER JOIN COMMENTS on POST.POST_ID=COMMENTS.POST_ID where POST.POST_ID = ?";
		$result = $connection->query ( $sql, array (
				$post_id 
		) );
		while ( $comment = $result->fetch () ) {
			echo json_encode ( $comment );
		}
	}
}