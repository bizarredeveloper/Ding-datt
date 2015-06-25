<?php
		if ($_GET['DeviceId']!='' && $_GET['Message']!='') 
		{
		$Msg = $_GET['Message'];
		$Msg = str_replace("_", " ", $Msg);
		$deviceToken = $_GET['DeviceId'];
		$passphrase = 'apple';
		$pem_url = '';	
		$PEM_path = 'DINGDATT.pem';
		$pemfile = $pem_url.$PEM_path;
		$message = $Msg;
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $pemfile);
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err,$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		if (!$fp) exit("Failed to connect: $err $errstr" . PHP_EOL);
		$body['aps'] = array('alert' => $message,'sound' => 'default');
		$payload = json_encode($body);
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		$result = fwrite($fp, $msg, strlen($msg));
		fclose($fp);
		}
?>