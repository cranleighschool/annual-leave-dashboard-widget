<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 22/05/2018
 * Time: 11:34
 */

namespace CranleighSchool\AnnualLeave;

use DateTime;
use DateTimezone;

class icsEvent {
	public $timezone;
	public $title;

	private $allDayEvent;
	private $start_time;
	public $start_timestamp;
	public $readable_start;

	private $end_time;
	public $end_timestamp;
	public $readable_end;

	public function __construct($item, $timezone)
	{

		$item = array_map('trim', $item);

		if (isset($item['DTSTART;VALUE=DATE'])) {
			$this->allDayEvent = true;
			$this->start_time = ( $item[ 'DTSTART;VALUE=DATE' ] );
			$this->end_time   = ( $item[ 'DTEND;VALUE=DATE' ] );
		} elseif (isset($item['DTSTART'])) {
			$this->allDayEvent = false;
			$this->start_time = ( $item[ 'DTSTART' ] );
			$this->end_time = ( $item[ 'DTEND' ] );
		}
		$this->title = ($item['SUMMARY']);
		$this->timezone = $timezone;

		$startDt = new DateTime ( $this->start_time );
		$startDt->setTimeZone ( new DateTimezone ( $this->timezone ) );
		$this->start_timestamp = $startDt->getTimestamp();

		if ($this->allDayEvent===true) {
			$this->readable_start = $startDt->format( "d M" );
		} else {
			$this->readable_start = $this->formatMinutesOut($startDt->format("d M g:ia"));
		}

		$endDt = new DateTime($this->end_time);
		$endDt->setTimeZone(new DateTimeZone($this->timezone));
		$this->end_timestamp = $endDt->getTimestamp()-1; // Minus 1 millisecond to push into 'yesterday'

		if ($this->allDayEvent===true) {
			$this->end_timestamp = $endDt->getTimestamp()-1;
			$this->readable_end = date("d M", $this->end_timestamp);
		} else {
			if ($endDt->format("d M")===$startDt->format("d M")) {
				$this->readable_end = $this->formatMinutesOut($endDt->format("g:ia"));
			} else {
				$this->readable_end = $this->formatMinutesOut($endDt->format( "d M g:ia" ));
			}
		}

		$this->html = $this->setDisplay();
		$this->time_now = time();
	}

	public function formatMinutesOut($input) {
		if (strpos($input, ":00")) {
			$parts = explode(":00", $input);
			return implode("", $parts);
		}
		return $input;
	}

	private function setDisplay() {
		$class = "";
		if (time() < ($this->end_timestamp) && time() > $this->start_timestamp) {
			$class = "today";
		}
		if ($this->readable_start === $this->readable_end) {
			$date = $this->readable_start;
		} else {
			$date = $this->readable_start.'-'.$this->readable_end;
		}

		return '<tr class="'.$class.'"><td><span class="the_date">'.$date.': </span>'.$this->title.'</td></tr>';

	}
}
