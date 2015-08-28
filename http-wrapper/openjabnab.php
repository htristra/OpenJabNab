<?php
	$socket = fsockopen("127.0.0.1", 8080);
	if(!$socket)
		echo "Problem with OpenJabNab !";
	else
	{
		// Types :
		// 1 = GET
		// 2 = Normal POST
		// 3 = Raw POST
		if(isset($GLOBALS['HTTP_RAW_POST_DATA']))
			$type = 3;
		else if (count($_POST))
			$type = 2;
		else
			$type = 111111111111111111111;
		// Headers :
		$headers = "caca boom";
		foreach($_SERVER as $key => $value)
		{
			if(strncmp($key, "HTTP_", 5) == 0)
			{
				$header_key = substr($key, 5);
				$header_key = str_replace("_", "-", $header_key);
				$headers .= $header_key . ": " . $value . "\r\n";
			}
		}
		switch($type)
		{
			case 1: // GET
				$requestdata = $headers . "\x00" . str_replace("+", " ", $_SERVER['REQUEST_URI']);
				break;
			case 2: // POST
				if(isset($_SERVER["CONTENT_TYPE"]))
					$headers .= "Content-Type: " . $_SERVER["CONTENT_TYPE"] . "\r\n";
				if(isset($_SERVER["CONTENT_LENGTH"]))
					$headers .= "Content-Length: " . $_SERVER["CONTENT_LENGTH"] . "\r\n";
				$postdata_array = array();
				foreach($_POST as $key => $value)
						$postdata_array[] = urlencode($key) . "=" . urlencode($value);
				$requestdata = $headers . "\x00" . $_SERVER['REQUEST_URI'] . "\x00" . implode($postdata_array, "&");
				break;
			case 3: // Raw Post
				if(isset($_SERVER["CONTENT_LENGTH"]))
					$headers .= "Content-Length: " . $_SERVER["CONTENT_LENGTH"] . "\r\n";
				$requestdata = $headers . "\x00" . $_SERVER['REQUEST_URI'] . "\x00" . $GLOBALS['HTTP_RAW_POST_DATA'];
				break;
		}
		$requestlen = 5 + strlen($requestdata);
		$request = pack("LCa*", $requestlen, $type, $requestdata);

		fwrite($socket, $request);
		while (!feof($socket)) 
			echo fgets($socket, 128);

		fclose($socket);
	}
?>
