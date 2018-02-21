<?php
	// IF THIS SCRIPT RUNS CORRECTLY, IT DISPLAYS NOTHING, NO OUTPUT! If it does display something, crontab mails the output to you

	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);

	require_once('config.php');

	// get nodes from Shodan
	$shodanquery='Server:MovistarTV';
	$json=file_get_contents("https://api.shodan.io/shodan/host/search?key=$shodanapikey&query=$shodanquery"); // $shodanapikey comes from config.php
	$shodanList=json_decode($json, true);

	// connect to MySQL
	$mysqli=mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
	if (mysqli_connect_errno()) die('Fallo la conexi&otilde;n: '.$mysqli->connect_error);

	function insertNode($mysqli, $IP, $Hostname, $Port, $City, $Added) {
		// Insert row into MySQL
		$query="INSERT INTO Nodes(IP, Hostname, Port, City, Added) VALUES('$IP', '$Hostname', $Port, '$City', '$Added')";
		if (mysqli_query($mysqli, $query)):
			//echo "New record created successfully<br />\n";
		else:
			echo 'Error: '.$sql.'<br />'.mysqli_error($conn);
		endif;
	}

	function updateNode($mysqli, $IP, $Hostname, $Port, $City, $Added) {
		// Insert row into MySQL
		$query="UPDATE Nodes SET Hostname='$Hostname', Port=$Port, City='$City', Added='$Added' WHERE IP='$IP'";
		if (mysqli_query($mysqli, $query)):
			//echo "New record updated successfully<br />\n";
		else:
			echo 'Error: '.$query.'<br />'.mysqli_error($mysqli);
		endif;
	}

	function processNode($mysqli, $IP, $Hostname, $Port, $City, $Added) {
		// Insert row into MySQL
		$query="SELECT IP FROM Nodes WHERE IP='$IP'";
		if ($result=mysqli_query($mysqli, $query)): // was the query correct?
			if (mysqli_num_rows($result)==1): // was the IP already at the DB?
				updateNode($mysqli, $IP, $Hostname, $Port, $City, $Added); // update it
			else:
				insertNode($mysqli, $IP, $Hostname, $Port, $City, $Added); // add it
			endif;
			mysqli_free_result($result);
		else:
			echo 'Error: '.$query.'<br />'.mysqli_error($mysqli);
		endif;
	}

	// loop through all the rows, adding them to the DB
	foreach ($shodanList['matches'] as $shodanelement):
		$element['IP']=$shodanelement['ip_str'];
		if (count($shodanelement['hostnames'])==0): // Some Movistar IP don't resolve to DNS names!
			$element['Hostname']='';
		else:
			$element['Hostname']=$shodanelement['hostnames'][0];
		endif;
		$element['Port']=$shodanelement['port'];
		$element['City']=$shodanelement['location']['city'];
		$element['Added']=$shodanelement['timestamp'];
		processNode($mysqli, $element['IP'], $element['Hostname'], $element['Port'], $element['City'], $element['Added']);
		unset($element); // just in case, delete previous array data
	endforeach;

	$mysqli->close(); // free resources
?>