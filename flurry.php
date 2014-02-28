<?php
    /**
     * flurry class
     * 
     * PHP version 5
     *
     *
     * @copyright	Copyright 2014 Appublisher
     * @link		http://www.appublisher.com
     * @author 		devforma
     * @version		1.0
     * GOD BLESS YOU
     **/
    
    
    //PHP code

class Flurry{
    
    //the unique code given to you to access Flurry APIs and the unique code for each application
    private $apiAccessCode = '';
    private $apiKey = '';
    
    //an array of AppMetrics'names supported
    private $AMnames = array(
	'ActiveUsers',
	'ActiveUsersByWeek',
	'ActiveUsersByMonth',
	'NewUsers',
	'MedianSessionLength',
	'AvgSessionLength',
	'Sessions',
	'RetainedUsers',
	'PageViews',
	'AvgPageViewsPerSession'
    );
    
    //store events from EventMetrics summary data
    private $EMevents = array();
    
    /**
     * request metrics data for one of your applications by METRIC_NAME
     * 
     * @param string $metricName  the supported AppMetrics'name to request for
     * @param string $startDate  request data began from which day, should be in YYYY-MM-DD format
     * @param string $endDate  request data end on which day, should be in YYYY-MM-DD format
     * @param string $country  the name of the country in abbreviated form, see http://support.flurry.com/index.php?title=Countries
     * @param string $versionName  the name set by the developer for each version of the application
     * @return array
     */
    public function getAppMetrics($metricName, $startDate, $endDate, $country, $versionName){
	$url = "http://api.flurry.com/appMetrics/{$metricName}?apiAccessCode={$this->apiAccessCode}&apiKey={$this->apiKey}&startDate={$startDate}&endDate={$endDate}&country={$country}&versionName={$versionName}";
	return $this->do_curl($url);
    }
    
    /**
     * request all supported AppMetrics data for one of your applications 
     * 
     * @param string $startDate  request data began from which day, should be in YYYY-MM-DD format
     * @param string $endDate  request data end on which day, should be in YYYY-MM-DD format
     * @param string $country  the name of the country in abbreviated form, see http://support.flurry.com/index.php?title=Countries
     * @param string $versionName  the name set by the developer for each version of the application
     * @return array
     */
    public function getAllAppMetries($startDate, $endDate, $country, $versionName){
	$AMetries = array();
	foreach($this->AMnames as $AMname){
	    $AMetries[$AMname] = $this->getAppMetrics($AMname, $startDate, $endDate, $country, $versionName);
	}
	return $AMetries;
    }
    
    /**
     * request EventMetrics summary for one of your applications 
     * 
     * @param string $startDate  request data began from which day, should be in YYYY-MM-DD format
     * @param string $endDate  request data end on which day, should be in YYYY-MM-DD format
     * @param string $versionName  the name set by the developer for each version of the application
     * @return array
     */
    public function getEventSummary($startDate, $endDate, $versionName){
	$url = "http://api.flurry.com/eventMetrics/Summary?apiAccessCode={$this->apiAccessCode}&apiKey={$this->apiKey}&startDate={$startDate}&endDate={$endDate}&versionName={$versionName}";
	$summary = $this->do_curl($url);
	
	if(is_array($summary['event']) && !empty($summary['event'])){
	    foreach($summary['event'] as $event){
		$this->EMevents[] = $event['@eventName'];
	    }
	}
	
	return $summary;
    }
    
    /**
     * request single EventMetrics(exist in summary) data for one of your applications by EventName 
     * 
     * @param string $startDate  request data began from which day, should be in YYYY-MM-DD format
     * @param string $endDate  request data end on which day, should be in YYYY-MM-DD format
     * @param string $versionName  the name set by the developer for each version of the application
     * @return array
     */
    public function getEventMetrics($eventName, $startDate, $endDate, $versionName){
	$url = "http://api.flurry.com/eventMetrics/Event?apiAccessCode={$this->apiAccessCode}&apiKey={$this->apiKey}&startDate={$startDate}&endDate={$endDate}&eventName={$eventName}&versionName={$versionName}";
	$event = $this->do_curl($url);
	return $event;
    }
    
    /**
     * request all EventMetrics(exist in summary) data for one of your applications 
     * 
     * @param string $startDate  request data began from which day, should be in YYYY-MM-DD format
     * @param string $endDate  request data end on which day, should be in YYYY-MM-DD format
     * @param string $versionName  the name set by the developer for each version of the application
     * @return array
     */
    public function getAllEventMetrics($startDate, $endDate, $versionName){
	$events = array();
	
	if(!empty($this->EMevents)){
	    foreach($this->EMevents as $event){
		$events[$event] = $this->getEventMetrics($event, $startDate, $endDate, $versionName);
	    }
	}
	return $events;
    }
    
    /**
     * request all EventMetrics(exist in summary) data for one of your applications 
     * 
     * @param string $url  the url to send request to(method GET)
     * @return array
     */
    private function do_curl($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);
	
	return json_decode($data, true);
    }

}

?>