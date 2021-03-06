<?php
	require_once("core_functions.php");

	$dbc = getDBConnection();		
	$sql = "SELECT * FROM watm_activities
			LEFT JOIN watm_sports ON watm_activities.sport_id = watm_sports.sport_id
			LEFT JOIN watm_users ON watm_activities.user_ID = watm_users.user_id
			LEFT JOIN watm_buurten ON watm_activities.buurt_id = watm_buurten.buurt_id
			LEFT JOIN watm_locations ON watm_activities.location_id = watm_locations.location_id
			WHERE watm_activities.visible = 1 and date <= CURRENT_DATE()
			ORDER BY date";
	
	$result = $dbc->query($sql);
	$projects = array();
	$index = 0;

	while($row = $result->fetch_assoc()){
		// get media from this activity
		$mediaSQL = "SELECT * FROM watm_media WHERE activity_id =" . $row["activity_id"];
		
		$mediaResult = $dbc->query($mediaSQL);
		$mediaCollection = array();
		
		while($mediarow = $mediaResult->fetch_assoc()){
			$media = array("url" => $mediarow["url"], );
			$mediaCollection[] = $media;
		}

		// get users going to this activity
		$usersSQL = "SELECT * FROM watm_activity_users LEFT JOIN watm_users ON watm_activity_users.user_id = watm_users.user_id WHERE activity_id =" . $row["activity_id"] . " AND watm_activity_users.going='1'";
		$usersResult = $dbc->query($usersSQL);
		$usersCollection = array();
		
		while($userrow = $usersResult->fetch_assoc()){
			$pr = array("user_id" => $userrow["user_id"], "avatar" => $userrow["avatar"], "name" => $userrow["name"]);
			$usersCollection[] = $pr;
		}

		$originalDate =  $row["date"];
		$newDate = date("d-m-Y", strtotime($originalDate));
		$newDateFormat = date("Y-m-d", strtotime($originalDate));;
		$today = false;
		$tomorrow = false;

		$mydatetime = $row["date"];
		$datetimearray = explode(" ", $mydatetime);
		$date = $datetimearray[0];
		$time = $datetimearray[1];
		$now = date('G:i:s',strtotime($time));

		// Analyse dates
		if($newDate == date("d-m-Y")){
		   $newDate = "Vandaag om " . date("H:i",strtotime($row["date"]));
		   $feature = true;
	   	   $today = true;
		}else if($newDate == date("d-m-Y")+1){
		   $newDate = "Morgen om " . date("H:i",strtotime($row["date"]));
		   $tomorrow = true;
		}

		$project = array("sql_index"=> $index, "savedDate" => $newDateFormat, "description"=>$row["activity_description"],"activity_id" => $row["activity_id"], "participants"=>$row["participants"], "sport_title" => $row["sport_title"], "date" => $newDate, "title" => $row["title"], "sport_id" =>$row['sport_id'], "location_id"=>$row['location_id'], "location"=>$row['location'], "coordinates"=>$row['coordinates'], "user_id"=>$row['user_id'], "media" => $mediaCollection, "users" => $usersCollection, "buurt"=>$row['buurt'], "buurt_id"=>$row["buurt_id"], "today"=>$today, "tomorrow"=>$tomorrow, "startTime"=> $now, "stopTime" => $row["stopTime"]);
		$projects[] = $project;
	    $index++;

	}
	
	$dbc->close();
	print json_encode($projects);

?>
