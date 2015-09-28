<?php
	#TIME
	$data = array();
	date_default_timezone_set("America/Chicago");
	$time = date('g:i A');
	$data["time"]=$time;
	
	#DATE
	$date = date('l, F j Y');
	$data["date"] = $date;
	
	#TODAYS 
	$timeMin = date("Y-m-d\TH:i:sP");
	$timeMax = date("Y-m-d\T00:00:00P", strtotime("+1 day"));					
	$todays_url = "https://www.googleapis.com/calendar/v3/calendars/dspeboard%40gmail.com/events?singleEvents=true&timeMin=". $timeMin."&maxResults=15&timeMax=".$timeMax."&key=AIzaSyAhRes1Obr_feex1NOb_njJNCh1zWlDg4c&orderBy=startTime";					
	$todays_json = file_get_contents($todays_url);
	$todays_array = json_decode($todays_json,true);	
	$x = 0;
	
	foreach($todays_array['items'] as $event)
	{
		$data["todays_data"][$x]["name"] = $event['summary'];
										
		if(array_key_exists('date', $event['start']))
		{
			//$event_rawtime = strtotime($event['start']['date']);							
			$todays_data[$x]["time"] = "All Day";
		}
		else
		{
			$event_rawtime = strtotime($event['start']['dateTime']);
												
			$check_minutes = date("i", $event_rawtime);

			if($check_minutes != '00')
			{
				$event_time = date("g:i A",$event_rawtime);
			}
			else
			{
				$event_time = date("g A",$event_rawtime);
			}
											
			$data["todays_data"][$x]["time"] = $event_time;
		}
											
			
		$x++;
	}
	
	
	#UPCOMING EVENTS
	$upcoming_url = "https://www.googleapis.com/calendar/v3/calendars/dspeboard%40gmail.com/events?singleEvents=true&timeMin=". $timeMax."&maxResults=10&key=AIzaSyAhRes1Obr_feex1NOb_njJNCh1zWlDg4c&orderBy=startTime";					
	$upcoming_json = file_get_contents($upcoming_url);				
	$upcoming_array = json_decode($upcoming_json,true);
	$data["upcoming_array"] = $upcoming_array;
	
	foreach($upcoming_array['items'] as $event)
	{
		$data["upcoming_data"][$x]["name"] = = $event['summary'];
										
		if(array_key_exists('date', $event['start']))
		{
			$start_date = date("n/j", strtotime($event['start']['date']));
			$end_date = date("n/j", strtotime('-1 day', strtotime($event['end']['date'])));
			if($start_date == $end_date)
			{
				$same_date = date("D", strtotime($event['start']['date']));

				$data["upcoming_data"][$x]["day"] = $same_date;
				$data["upcoming_data"][$x]["time"] = "All Day";
				
			}
			else
			{
				$data["upcoming_data"][$x]["date"] = $start_date.'-'.$end_date;

			}
											
		}
		else
		{
			$check_minutes = date("i", strtotime($event['start']['dateTime']));
			$event_day = date("D", strtotime($event['start']['dateTime']));
			if($check_minutes != '00')
			{
				$data["upcoming_data"][$x]["time"] = date("g:i A",strtotime($event['start']['dateTime']));
			}
			else
			{
				$data["upcoming_data"][$x]["time"] = date("g A",strtotime($event['start']['dateTime']));
			}
										
			
		}
		

	}
	
	
	
	#CURRENT WEATHER DATA
	$zipcode = '65409'; 
	$country = 'us';
	$weather_url = 'http://api.openweathermap.org/data/2.5/weather?zip='.$zipcode.','.$country.'&units=imperial&APPID=b41004938a0ae6a5df2a1d912fcdb92a';					
	$weather_json = file_get_contents($weather_url);
	$weather_array = json_decode($weather_json, true);
	$data["current_weather"]["current_temp"] = $weather_array['main']['temp'];
	$data["current_weather"]["weather_icon"] = $weather_array['weather'][0]['icon'];
	$data["current_weather"]["weather_description"] = $weather_array['weather'][0]['description'];
	$data["current_weather"]["icon_src"] = 'http://openweathermap.org/img/w/'.$weather_icon.'.png';

	#FORECAST
	$forecast_url = 'http://api.openweathermap.org/data/2.5/forecast/daily?zip='.$zipcode.','.$country.'&units=imperial&cnt=7&APPID=b41004938a0ae6a5df2a1d912fcdb92a';
						
	$forecast_json = file_get_contents($forecast_url);

		
	$forecast_array = json_decode($forecast_json, true);
		
					
	for($x = 1; $x <= 6; $x++)
	{
			
		$weather_icon = $forecast_array['list'][$x]['weather'][0]['icon'];
		$icon_src = 'http://openweathermap.org/img/w/'.$weather_icon.'.png';
		$timestamp = $forecast_array['list'][$x]['dt'];
			
		$data['forecast_data'][$x]['day'] = date("D:",$timestamp);
		$data['forecast_data'][$x]['max'] = round($forecast_array['list'][$x]['temp']['max']);
		$data['forecast_data'][$x]['min'] = round($forecast_array['list'][$x]['temp']['min']);
		$data['forecast_data'][$x]['icon_src'] = $icon_src;
		
		//echo '<em>',date("D:",$timestamp),'</em>';
		//echo '<img src="'.$icon_src.'" style="width:45px; height:45px;">';
		//echo '<b>',round($forecast_array['list'][$x]['temp']['max']),'</b>';
		//echo '|';
		//echo round($forecast_array['list'][$x]['temp']['min']);
		//echo str_repeat('&nbsp;', 3);
	}
		
		
	$icon_src = 'http://openweathermap.org/img/w/'.$weather_icon.'.png';
	
	#ECHO JSON DATA
	echo json_encode($data);

?>