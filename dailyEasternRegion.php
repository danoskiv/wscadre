<?php
error_reporting(E_ALL); ini_set('display_errors', '1');

include 'Data.php';
$pdo = require 'includes/bootstrap.php';

$table = "vrednosti_eastern";
$dat = new Data($pdo);
$latestDate = $dat->getFullDate($table);
$date = date_create($latestDate);
$currentDate = date_create('now');
$currentDate->setTimezone(new DateTimeZone("Europe/Skopje"));

$interval = $currentDate->diff($date);
$days = $interval->format("%a"); 
$hours = $interval->format("%h");

$parametri = [
	"PM25", "PM10", "CO", "NO2", "SO2", "O3"
];

if($days == '0')
{
	if($hours == '1')
	{
		$beginDate = $date->format("Y-m-d");
		$endDate = $currentDate->format("Y-m-d");
		$beginHour = $date->format("H:i");
		$endHour = $endDate->format("H:i");
		for($j = 0; $j < count($parametri)-3; $j++)
		{		
			// Get cURL resource
			$curl = curl_init();
			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => 'http://air.moepp.gov.mk/graphs/site/pages/MakeGraph.php?station=EasternRegion&parameter='.$parametri[$j].'&beginDate='.$beginDate.'&beginTime='. $beginHour .'&endDate='.$endDate.'&endTime='. $endHour .'&i=1521227934243&lang=en',
			    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
			));
			// Send the request & save response to $resp
			$resp = curl_exec($curl);
			// Close request to clear up some resources
			curl_close($curl);

			$result = json_decode($resp,true);

			for($i = 0; $i < count($result["measurements"]); $i++)
			{
				$sid = $dat->getStationId($result["stations"][$i]);
				$pid = $dat->getParameterId($result["parameter"]);
				$data = $result["measurements"][$i]["data"];
				$dates = $result["times"];
				$dat->addData($dates, $data, $sid, $pid, $table);
			}	
		}

		sleep(5);

		for($j = 3; $j < count($parametri); $j++)
		{
			// Get cURL resource
			$curl = curl_init();
			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => 'http://air.moepp.gov.mk/graphs/site/pages/MakeGraph.php?station=EasternRegion&parameter='.$parametri[$j].'&beginDate='.$beginDate.'&beginTime='. $beginHour .'&endDate='.$endDate.'&endTime='. $endHour .'&i=1521227934243&lang=en',
			    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
			));
			// Send the request & save response to $resp
			$resp = curl_exec($curl);
			// Close request to clear up some resources
			curl_close($curl);

			$result = json_decode($resp,true);

			for($i = 0; $i < count($result["measurements"]); $i++)
			{
				$sid = $dat->getStationId($result["stations"][$i]);
				$pid = $dat->getParameterId($result["parameter"]);
				$data = $result["measurements"][$i]["data"];
				$dates = $result["times"];
				$dat->addData($dates, $data, $sid, $pid, $table);
			}
		}
	}
}
else
{
	$beginDate = $dat->getLatestDate($table);
	$endDate = date_create($beginDate);
	$endDate = $endDate->modify('+1 day');
	$endDate = $endDate->format("Y-m-d");

	for($j = 0; $j < count($parametri)-3; $j++)
	{		
	// Get cURL resource
	$curl = curl_init();
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => 'http://air.moepp.gov.mk/graphs/site/pages/MakeGraph.php?station=EasternRegion&parameter='.$parametri[$j].'&beginDate='.$beginDate.'&beginTime=00:00&endDate='.$endDate.'&endTime=00:00&i=1521227934243&lang=en',
	    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	));
	// Send the request & save response to $resp
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);

	$result = json_decode($resp,true);

	for($i = 0; $i < count($result["measurements"]); $i++)
	{
		$sid = $dat->getStationId($result["stations"][$i]);
		$pid = $dat->getParameterId($result["parameter"]);
		$data = $result["measurements"][$i]["data"];
		$dates = $result["times"];
		$dat->addData($dates, $data, $sid, $pid, $table);
	}	
	}

	sleep(5);

	for($j = 3; $j < count($parametri); $j++)
	{
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => 'http://air.moepp.gov.mk/graphs/site/pages/MakeGraph.php?station=EasternRegion&parameter='.$parametri[$j].'&beginDate='.$beginDate.'&beginTime=00:00&endDate='.$endDate.'&endTime=00:00&i=1521227934243&lang=en',
		    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		// Close request to clear up some resources
		curl_close($curl);

		$result = json_decode($resp,true);

		for($i = 0; $i < count($result["measurements"]); $i++)
		{
			$sid = $dat->getStationId($result["stations"][$i]);
			$pid = $dat->getParameterId($result["parameter"]);
			$data = $result["measurements"][$i]["data"];
			$dates = $result["times"];
			$dat->addData($dates, $data, $sid, $pid, $table);
		}
	}
}