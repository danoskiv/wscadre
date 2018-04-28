<?php

class Data
{
	protected $conn;

	function __construct($pdo)
	{
		$this->conn = $pdo;
	}

	public function addStation($name, $long, $lat)
	{
		$statement = $this->conn->prepare("INSERT INTO stanici(ime_stanica, longituda, latituda) VALUES(:name, :long, :lat)");
		$statement->bindParam(":name", $name);
		$statement->bindParam(":long", $long);
		$statement->bindParam(":lat", $lat);

		if($statement->execute())
		{
			$statement->closeCursor();
			return true;
		}
		else
			return false;
		$statement->closeCursor();
	}

	public function addParameter($name, $description)
	{
		$statement = $this->conn->prepare("INSERT INTO parametar(ime, opis) VALUES(:name, :description)");
		$statement->bindParam(":name", $name);
		$statement->bindParam(":description", $description);

		if($statement->execute())
		{
			$statement->closeCursor();
			return true;
		}
		else
			return false;
		$statement->closeCursor();
	}

	public function addData($dates, $data, $sid, $pid, $table)
	{
		for($i = 0; $i < count($data) - 1; $i++)
		{
			try {
				$statement = $this->conn->prepare("INSERT INTO {$table}(datum, vrednost, sid, pid) 
				VALUES(:dates, :data, :sid, :pid)");
				$statement->bindParam(":dates", $dates[$i]);
				$statement->bindParam(":data", $data[$i]);
				$statement->bindParam(":sid", $sid);
				$statement->bindParam(":pid", $pid);
				$statement->execute();
			} catch (PDOException $e) {
				return $e->getMessage();
			}
		}
	}

	public function getParameterId($name)
	{
		$statement = $this->conn->prepare("SELECT id FROM parametar WHERE ime = :name");
		$statement->bindParam(":name", $name);
		if($statement->execute())
		{
			$result = $statement->fetchColumn();
			$statement->closeCursor();
			return $result;
		}
		else
			return NULL;
		$statement->closeCursor();
	}

	public function getStationId($name)
	{
		$statement = $this->conn->prepare("SELECT id FROM stanici WHERE ime_stanica = :name");
		$statement->bindParam(":name", $name);
		if($statement->execute())
		{
			$result = $statement->fetchColumn();
			$statement->closeCursor();
			return $result;
		}
		else
			return NULL;
		$statement->closeCursor();
	}

	public function getFullDate($table)
	{
		if($result = $this->conn->query("SELECT datum FROM {$table} ORDER BY id DESC LIMIT 1"))
		{
			if($result = $result->fetchColumn())
			{
				return $result;
			}
			else
			{
				$date = date_create('2015-01-01 00:00:00');
				$date = $date->format("Y-m-d H:i:s");
				return $date;
			}
		}
		else
		{
			$date = date_create('2015-01-01 00:00:00');
			$date = $date->format("Y-m-d H:i:s");
			return $date;
		}
	}

	public function getLatestDate($table)
	{
		if($result = $this->conn->query("SELECT datum FROM {$table} ORDER BY id DESC LIMIT 1"))
		{
			if($result = $result->fetchColumn())
			{
				$result = explode(" ", $result);
				$date = date_create($result[0]);
				$date = $date->modify('+1 day');
				$date = $date->format('Y-m-d');
				return $date;
			}
			else
			{
				$date = date_create('2015-01-01');
				$date = $date->format("Y-m-d");
				return $date;
			}
		}
		else
		{
			$date = date_create('2015-01-01');
			$date = $date->format("Y-m-d");
			return $date;
		}	
	}

	public function makeDate($date, $time)
	{
		//"Y-m-d H:i:s" - correct
		$time = explode(":", $time);
		$date = explode(".", $date);
		$day = $date[0];
		$month = $date[1];
		$year = $date[2];
		$hour = $time[0];
		$minute = $time[1];
		return $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute . ":00";
	}

	public function addDataAuto($city, $data)
	{
		$datum = $this->makeDate($data[count($city)-1][0], $data[count($city)-1][1]);
		$format = 'Y-m-d H:i:s';
		$datum2 = date_create_from_format($format, $datum, new DateTimeZone("CET"));
		$datum2->setTimezone(new DateTimeZone("UTC"));
		$statement3 = $this->conn->prepare("SELECT datum FROM podatoci_uhmr ORDER BY id DESC LIMIT 1");
		$statement3->execute();
		$datum_baza = date_create($statement3->fetchColumn());
		$interval = $datum2->diff($datum_baza);
		$hours = $interval->format("%h");
		echo $hours;
		if($hours == '3')
		{
			for($i = 0; $i < count($city); $i++)
			{
				$datum = $this->makeDate($data[$i][0], $data[$i][1]);
				$format = 'Y-m-d H:i:s';
				$datum2 = date_create_from_format($format, $datum, new DateTimeZone("CET"));
				$datum2->setTimezone(new DateTimeZone("UTC"));
				$datum2 = $datum2->format($format);
				$statement = $this->conn->prepare("SELECT id FROM stanici WHERE ime_stanica = :city");
				$statement->bindParam(":city", $city[$i]);
				if($statement->execute())
				{
					try {
					$id = $statement->fetchColumn();
					$statement2 = $this->conn->prepare("INSERT INTO podatoci_uhmr(datum, pritisok, temp, vlaznost, brzina, pravec, dozd, stanica_id) VALUES (:datum, :pritisok, :temp, :vlaznost, :brzina, :pravec, :dozd, :stanica_id)");
					switch ($data[$i][6]) {
						case 'север':
							$pravec = 1;
							break;
						case 'север-североисток':
							$pravec = 2;
							break;
						case 'североисток':
							$pravec = 3;
							break;
						case 'исток-североисток':
							$pravec = 4;
							break;
						case 'исток':
							$pravec = 5;
							break;
						case 'исток-југоисток':
							$pravec = 6;
							break;
						case 'југоисток':
							$pravec = 7;
							break;
						case 'југ-југоисток':
							$pravec = 8;
							break;
						case 'југ':
							$pravec = 9;
							break;
						case 'југ-југозапад':
							$pravec = 10;
							break;
						case 'југозапад':
							$pravec = 11;
							break;
						case 'запад-југозапад':
							$pravec = 12;
							break;
						case 'запад':
							$pravec = 13;
							break;
						case 'запад-северозапад':
							$pravec = 14;
							break;
						case 'северозапад':
							$pravec = 15;
							break;
						case 'север-северозапад':
							$pravec = 16;
							break;
						default:
							$pravec = -1;
							break;
					}
					$statement2->bindParam(":datum", $datum2);
					$statement2->bindParam(":pritisok", $data[$i][2]);
					$statement2->bindParam(":temp", $data[$i][3]);
					$statement2->bindParam(":vlaznost", $data[$i][4]);
					$statement2->bindParam(":brzina", $data[$i][5]);
					$statement2->bindParam(":pravec", $pravec);
					$statement2->bindParam(":dozd", $data[$i][7]);
					$statement2->bindParam(":stanica_id", $id);

					$statement2->execute();
					} catch (PDOException $e) {
						return $e->getMessage();
					}
				}
			}
		}
	}
}