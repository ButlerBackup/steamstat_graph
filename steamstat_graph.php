<?php 
date_default_timezone_set('Africa/Sao_Tome'); // GMT + 0

function object2array($object) { 
	return @json_decode(@json_encode($object),1);
} 

function convertUnix($date, $time) {
	$unix = date_create_from_format('M d, Y H:i', $date . ' ' . $time);
	return date_timestamp_get($unix);
}

function getData($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

$db = new mysqli('localhost', 'root', '', 'steamstat_graph');

if($db->connect_errno > 0){
	die('Unable to connect to database [' . $db->connect_error . ']');
}

if ($result = $db->query('SELECT unix FROM data ORDER BY id DESC LIMIT 1')) {
	$row = $result->fetch_assoc();
	$latestUnix = $row['unix'];
	$result->free();
}

$url = 'http://store.steampowered.com/stats/userdata.xml';

$xml = object2array(simplexml_load_string(getData($url)));

$previousDate = 'STEAM_NULL_DATE';

foreach($xml['detailNode']['node'] as $node) {

	$nodeDate = $node['@attributes']['dataSection'];

	if (strlen($nodeDate) < 1) {
		$nodeDate = $previousDate;
	} else {
		$previousDate = $nodeDate;
	}
	
	
	$unix = convertUnix($nodeDate, $node['@attributes']['dataInterval']);

	if (empty($latestUnix)) { // then just insert
		$q = "INSERT INTO data (uid, time, date, value, unix) VALUES ('".substr(md5($unix), 0, 8)."', '".$node['@attributes']['dataInterval']."', '".$nodeDate."', '".$node['dataValue']."', '".$unix."')";

		if (!$db->query($q)) {
			echo "[FAILED]: (" . $db->errno . ") " . $db->error . "\n";
		} else {
			echo "[SUCCESS] " . $unix . '-' . $nodeDate . ':' . $node['@attributes']['dataInterval'] . ' ['.$node['dataValue']."]\n";
		}		
	} else {
		if ($unix > $latestUnix) { // if current data is more than the last row in the database
			$q = "INSERT INTO data (uid, time, date, value, unix) VALUES ('".substr(md5($unix), 0, 8)."', '".$node['@attributes']['dataInterval']."', '".$nodeDate."', '".$node['dataValue']."', '".$unix."')";

			if (!$db->query($q)) {
				echo "[FAILED]: (" . $db->errno . ") " . $db->error . "\n";
			} else {
				echo "[SUCCESS] " . $unix . '-' . $nodeDate . ':' . $node['@attributes']['dataInterval'] . ' ['.$node['dataValue']."]\n";
			}			
		}
	}	
}

$db->close();