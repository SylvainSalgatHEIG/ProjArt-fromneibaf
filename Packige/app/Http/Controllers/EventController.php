<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    
private function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

public function getEvents() {
	$wsEvents = "https://age.heig-vd.ch/activites/";

	$stream = fopen($wsEvents, 'r', false, $context);
	$string = stream_get_contents($stream);

	$d = new DOMDocument();
	$d->loadHTML($string);


	$xpath = new DOMXPath($d);

	// Get data all events
	$eventsDayList = $xpath->query("//*[@class='event-day']");
	$eventsMonthList = $xpath->query("//*[@class='event-month']");


	$events = $xpath->query("//*[@class='activities']");

	$eventsNamesList = array();
	$eventsLinkList = array();
	$eventsImageLinkList = array();

	foreach($events as $event) {
		foreach ($event->getElementsByTagName('a')->item(0)->attributes as $attr) {
			$name = $attr->nodeName;
			$value = $attr->nodeValue;

			if ($name == "href") {
				array_push($eventsLinkList, $value);
			}
		
			if ($name == "title") {
				array_push($eventsNamesList, $value);
			}
		}
		foreach ($event->getElementsByTagName('img')->item(0)->attributes as $attr) {
			$name = $attr->nodeName;
			$value = $attr->nodeValue;
		
			if ($name == "src") {
				array_push($eventsImageLinkList, $value);
			}
		}
	}

	$eventsList = array();

	for ($i=0; $i < count($eventsNamesList) ; $i++) { 
		$event = new stdClass;

		$event->name = $eventsNamesList[$i];
		$event->day = $eventsDayList[$i]->nodeValue;
		$event->month = $eventsMonthList[$i]->nodeValue;
		$event->link = $eventsLinkList[$i];
		$event->imageLink = $eventsImageLinkList[$i];

		array_push($eventsList, $event);
	}

	// echo '<pre>'; 
	dd($eventsList); 
	// echo '</pre>';;

	$jsonEvents = json_encode($eventsList);
}

}
