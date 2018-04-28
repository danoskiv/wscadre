<?php
 error_reporting(E_ALL); ini_set('display_errors', '1');

include 'Data.php';
$pdo = require 'includes/bootstrap.php';

$dat = new Data($pdo);

$htmlContent = file_get_contents("http://www.meteo.gov.mk/index.php?option=com_content&view=article&id=61%3Aavtomatski-met-stanici&catid=30%3Abazi-na-meteoroloski-podatoci&Itemid=59&lang=mk");
		
$DOM = new DOMDocument();
$DOM->loadHTML($htmlContent);

$xpath = new DOMXpath($DOM);

$j = 0;
$k = 0;

for($i = 3; $i < 114; $i++)
{
	$data[$k++] = $xpath->query("//tr[@class=\" ari-tbl-row-".$i."\"]/td");
	$cities[$j++] = $xpath->query("//tr[@class=\" ari-tbl-row-".$i."\"]/th");
}

$j=0;

for($i=0; $i < (count($cities)); $i++)
{
	if(!is_null($cities[$i][0]->nodeValue))
		$gradovi[$j++] = $cities[$i][0]->nodeValue;
}

$j = 0;

for($i = 0; $i < (count($data)); $i++)
{
	$j=0;
	foreach($data[$i] as $d)
	{
		if($d->nodeValue == NULL)
		{
			$niza[$i][$j++] = NULL;
		}
		else
		{
			$niza[$i][$j++] = $d->nodeValue;
		}
	}
}


$ret = $dat->addDataAuto($gradovi, $niza);

