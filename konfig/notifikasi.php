<?php
define( 'API_ACCESS_KEY', 'AIzaSyBTw67PiWqdmo5R_zAcHA-Xau5KzY7ATWs' );

function send_notification($device_token, $message_title, $message_body) {
	#API access key from Google API's Console
	$registrationIds = $device_token;

	#prep the bundle
	$msg = array(
		'body' 	=> $message_body,
		'title'	=> $message_title
	);

	$data = array (
		'tes' => 'berhasil boy'
	);

	$fields = array(
		'to'			=> $registrationIds,
		'notification'	=> $msg,
		'data' 			=> $data
	);
	
	$headers = array(
		'Authorization: key=' . API_ACCESS_KEY,
		'Content-Type: application/json'
	);

	#Send Reponse To FireBase Server	
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	$result = curl_exec($ch );
	curl_close( $ch );

	#Echo Result Of FireBase Server
	echo $result;
}

//send_notification($_POST['deviceToken'], $_POST['messageTitle'], $_POST['messageBody']);
