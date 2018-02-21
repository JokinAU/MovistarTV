<html>
<head>
	<title>MovistarTV</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
</head>
<body>
	<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);

	require_once('functions.php');
	require_once('config.php');

	$startingTime=startClock();
	
	putenv('RES_OPTIONS=retrans:1 retry:1 timeout:1 attempts:1'); // Set timeout and retries to 1 to have a maximum execution time of 1 second for the DNS lookup
	//$hostip=gethostbyname($DNSHostname);
	$hostip=getAddrByHost($DNSHostname);
	?>
	<table align="center" border="1">
		<tr><td colspan="11" align="center"><h2><?php echo $DNSHostname.': '.$hostip;?></h2></td></tr>
		<tr>
			<th>IP</th>
			<th>Hostname</th>
			<th>Port</th>
			<th>Code</th>
			<th>Link</th>
			<th>Shodan</th>
			<th>City</th>
			<th>State</th>
			<th>AddedOn</th>
			<th>LastGood</th>
			<th>DNS</th>
		</tr>
<?php
	$mysqli=mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
	if (mysqli_connect_errno()) die('Falló la conexión: '.$mysqli->connect_error);

	// load data from DB
	//$query='SELECT IP, Hostname, Port, City, Added, LastGood FROM Nodes ORDER BY IP ASC';
	$query='SELECT IP, Hostname, Port, City, Added, LastGood FROM Nodes ORDER BY LastGood DESC, Added DESC';
	$result=mysqli_query($mysqli, $query);
	if (mysqli_num_rows($result)>0):
		while($row=mysqli_fetch_assoc($result)):
			$nodesFromDB[]=$row; // add row to array
		endwhile;
	else:
		//echo "0 results";
	endif;

	$requestdata=checkNodeReponse($nodesFromDB); // get response code from each node in real time

	for ($Cont=0; $Cont<count($nodesFromDB); $Cont++):
		$movistartvTMP=$nodesFromDB[$Cont];
		$movistartvTMP['URL']='http://'.$movistartvTMP['IP'].':'.$movistartvTMP['Port'].'/accion=login&p=movistartv';
		$movistartvTMP['State']=city2state($mysqli, $movistartvTMP['City']);
		$movistartvTMP['httpcode']=$requestdata[$Cont]['http_code'];
		$movistartv[]=$movistartvTMP;
		updateNodeCode($mysqli, $movistartvTMP['IP'], $movistartvTMP['httpcode']); // Update node info on DB
	endfor;

	// Sort data
//	usort($movistartv, function($a, $b) { return $a['httpcode'] - $b['httpcode']; });

	// Display data
	foreach ($movistartv as $movistarElement):
		if ($hostip==$movistarElement['IP']):
			$linebg='lime';
		else:
			$linebg='white';
		endif;

		echo "\t\t<tr bgcolor=\"$linebg\">\n";
		echo "\t\t\t<td>"."<input type=\"text\" value=\"".$movistarElement['IP']."\" size=\"10\" OnClick=\"this.select();\" readonly></td>\n";
		echo "\t\t\t<td>".$movistarElement['Hostname']."</td>\n";
		echo "\t\t\t<td>".$movistarElement['Port']."</td>\n";
		echo "\t\t\t<td align=\"right\"><a class=\"".code2color($movistarElement['httpcode'])."\">".$movistarElement['httpcode']."</a></td>\n";
		echo "\t\t\t<td align=\"center\"><a href=\"".$movistarElement['URL']."\" target=\"_blank\">&num;</a></td>\n";
		echo "\t\t\t<td align=\"center\"><a href=\"https://www.shodan.io/host/".$movistarElement['IP']."\" target=\"_blank\">&num;</a></td>\n";
		echo "\t\t\t<td>".$movistarElement['City']."</td>\n"; // Adapt characters: tilde, ñ...
		echo "\t\t\t<td>".$movistarElement['State']."</td>\n";
		echo "\t\t\t<td>".$movistarElement['Added']."</td>\n";
		echo "\t\t\t<td>".$movistarElement['LastGood']."</td>\n";
		// $DNSURL, $DNSpassword and $DNSDomain come from config.php:
		echo "\t\t\t<form method=\"post\" action=\"$DNSURL\" target=\"_blank\"><td>\n";
		echo "\t\t\t\t<input type=\"hidden\" name=\"password\" value=\"$DNSpassword\">\n";
		echo "\t\t\t\t<input type=\"hidden\" name=\"domain\" value=\"$DNSHostname\">\n";
		echo "\t\t\t\t<input type=\"hidden\" name=\"ip\" value=\"".$movistarElement['IP']."\">\n";
		echo "\t\t\t\t<input type=\"submit\" value=\"Set\">\n";
		echo "\t\t\t</td></form>\n";
		echo "\t\t</tr>\n";
	endforeach;
?>
	</table>
	<span align="center">
		<hr width="50%" />
		<p>Shodan: <a href="tvtimer.php" target="_blank">Update</a> <a href="https://www.shodan.io/search?query=Server%3AMovistarTV" target="_blank">Search</a></p>
		<p><a href="https://www.digwebinterface.com/?hostnames=<?php echo $DNSHostname;?>&type=A&showcommand=on&colorize=on&norecursive=on&useresolver=8.8.4.4&ns=auth&nameservers=" target="_blank">Dig Web Interface</a></p>
		<p><a href="/phpmyadmin/" target="_blank">phpMyAdmin</a></p>
		<hr width="50%" />
		<table align="center">
			<tr><td align="center" colspan="2">HTTP Codes:</td></tr>
			<tr><td align="right" class="red">0</td><td>No connection</td></tr>
			<tr><td align="right" class="green">200</td><td>Ok</td></tr>
			<tr><td align="right" class="yellow">401</td><td>Unauthorized</td></tr>
		</table>
		<hr width="50%" />
<?php
	echo "\t\t<p>".getdatetime().", ".stopClock($startingTime)." seconds</p>\n";
?>
	</span>
</body>
</html>