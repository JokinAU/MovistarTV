<?php
function getAddrByHost($host, $timeout=3) { // gethostbyname is not very reliable, it caches too long, so I used this
	$query = `nslookup -timeout=$timeout -retry=1 $host`;
	if(preg_match('/\nAddress: (.*)\n/', $query, $matches))
		return trim($matches[1]);
	return $host;
}

function getdatetime() {
		date_default_timezone_set('Europe/Madrid');
		return date('Y-m-d H:i:s', time());
}

function startClock() {
	//call this with $startingTime=startClock(); at page top
	$time=explode(' ', microtime());
	return $time[1] + $time[0];
}

function stopClock($startingTime) {
	//call this with stopClock($startingTime); at (almost) page end
	$time=explode(' ', microtime());
	$finishTime=$time[1] + $time[0];
	return round(($finishTime - $startingTime), 3);
}

function updateNodeCode($mysqli, $IP, $LastCode) {
	// Update a node's info
	if ($LastCode==200):
		$query="UPDATE Nodes SET LastCode=$LastCode, LastGood='".getdatetime()."' WHERE IP='$IP'";
	else:
		$query="UPDATE Nodes SET LastCode=$LastCode WHERE IP='$IP'";
	endif;
	if (mysqli_query($mysqli, $query)):
		//echo "New record updated successfully<br />\n";
	else:
		echo 'Error: '.$query.'<br />'.mysqli_error($mysqli);
	endif;
}

function RequestHTTPCodes($data) {
	// https://www.phpied.com/simultaneuos-http-requests-in-php-with-curl/
	$curltimeout=5;
	$curlconnecttimeout=5;
	$curly=array();
	$result=array();
	$mh=curl_multi_init();
	foreach ($data as $id=>$d) {// loop through $data and create curl handles, then add them to the multi-handle
		$curly[$id]=curl_init();
		$url=(is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
		curl_setopt($curly[$id], CURLOPT_URL,            $url);
		curl_setopt($curly[$id], CURLOPT_HEADER,         true);
		curl_setopt($curly[$id], CURLOPT_NOBODY,         true);
		curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curly[$id], CURLOPT_CONNECTTIMEOUT, $curlconnecttimeout);
		curl_setopt($curly[$id], CURLOPT_TIMEOUT, $curltimeout);
		curl_multi_add_handle($mh, $curly[$id]);
	}
	//$running=null;
	do {
		curl_multi_exec($mh, $running);
	} while($running > 0);
	foreach($curly as $id=>$c):// get request info and remove handles
		$result[$id]=curl_getinfo($c);
		curl_multi_remove_handle($mh, $c);
	endforeach;
	curl_multi_close($mh);
	return $result;
}

function checkNodeReponse($nodeList) {
	// create array with access URLs
	foreach ($nodeList as $node):
		$urlList[]='http://'.$node['IP'].':'.$node['Port'].'/accion=login&p=movistartv';
	endforeach;
	
	// get all the nodes' requests status simultaneously (speed it up!)
	return RequestHTTPCodes($urlList);
}

function city2state($mysqli, $City) {// Given a city, retrieve province/state
	$query="SELECT City, State FROM Geo WHERE City='$City'";
	$result=mysqli_query($mysqli, $query);
	if (mysqli_num_rows($result)>0):
		//
		$row=mysqli_fetch_assoc($result);
		$State=$row['State'];
	else:
		//echo "0 results";
		// Info: https://developers.google.com/maps/documentation/geocoding/intro
		$googlefullquery="https://maps.googleapis.com/maps/api/geocode/json?key=$googlegeoapikey&language=es&region=es&address=".rawurlencode($City);  // $googlegeoapikey comes from config.php
		$json=file_get_contents($googlefullquery);
		$cityinfo=json_decode($json, true);
		
		$State=$cityinfo['results'][0]['address_components'][2]['long_name'];

		$query="INSERT INTO Geo(City, State) VALUES('$City', '$State')";
		if (mysqli_query($mysqli, $query)):
			//echo "New record created successfully<br />\n";
		else:
			echo 'Error: '.$sql.'<br />'.mysqli_error($conn);
		endif;
	endif;
	return $State;
}

function code2color($code) {// https://httpstatuses.com/
	switch ($code):
		case 0:// ERROR
			return 'red';
			break;
		case 200: // OK
			return 'green';
			break;
		case 401: // Unauthorized
			return 'yellow';
			break;
		default:
			return 'black';
	endswitch;
}
?>