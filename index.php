<?php 
$db = new mysqli('localhost', 'root', '', 'steamstat_graph');

if($db->connect_errno > 0){
	die('Unable to connect to database [' . $db->connect_error . ']');
}

$data = array();
/*
$data = array(
  array('Jan', 40, 2, 4), array('Feb', 30, 3, 4), array('Mar', 20, 4, 4),
  array('Apr', 10, 5, 4), array('May',  3, 6, 4), array('Jun',  7, 7, 4),
  array('Jul', 10, 8, 4), array('Aug', 15, 9, 4), array('Sep', 20, 5, 4),
  array('Oct', 18, 4, 4), array('Nov', 16, 7, 4), array('Dec', 14, 3, 4),
);
*/
if ($result = $db->query('SELECT * FROM data ORDER BY id DESC LIMIT 10')) {
	while ($row = $result->fetch_assoc()) {
		$data[] = array($row['date'] . ':' . $row['time'], $row['value']);
	}
	$result->free();
}
$data = array_reverse($data);

require 'phplot.php';
$plot = new PHPlot(1200,600);
$plot->SetDataValues($data);
$plot->SetPlotType('bars');
$plot->SetImageBorderType('plain');
$plot->SetDataType('text-data');
$plot->SetDefaultTTFont('font/roboto.ttf');
$plot->SetXTitle('Time');
$plot->SetYTitle('Users');
$plot->SetTitle('Steam Users Logged In');
$plot->DrawGraph();