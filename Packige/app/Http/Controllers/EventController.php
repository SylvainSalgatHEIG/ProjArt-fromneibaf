<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{

	/**
     * Get all events from the api
     *
     * @return array Events list
     */
	public function getEvents() {
		$wsEvents = "https://age.heig-vd.ch/activite/";


		function file_get_contents_curl($url) {
			$ch = curl_init();
		
			curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);   
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    
		
			$data = curl_exec($ch);
			curl_close($ch);
		
			return $data;
		}
		$string = file_get_contents_curl($wsEvents);
		
		// the backslash is to escape the DOMDocument so it doesn't think it's a controller.
		$d = new \DOMDocument();

		// LIBXML_NOERROR is to prevent errors from being thrown.
		$d->loadHtml($string, LIBXML_NOERROR);

		$xpath = new \DOMXPath($d);

		// Get data all events
		$eventsDayList = $xpath->query("//*[@class='ect-list-post']");
		$eventsMonthList = $xpath->query("//*[@class='ect-list-post']");

		$events = $xpath->query("//*[@class='ect-list-post']");

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
			$event = new \stdClass;

			$event->name = $eventsNamesList[$i];
			$event->day = $eventsDayList[$i]->nodeValue;
			$event->month = $eventsMonthList[$i]->nodeValue;
			$event->link = $eventsLinkList[$i];
			$event->imageLink = $eventsImageLinkList[$i];

			array_push($eventsList, $event);
		}
		
		return json_encode($eventsList);
	}

}
