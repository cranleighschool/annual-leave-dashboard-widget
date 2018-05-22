<?php

namespace CranleighSchool\AnnualLeave;

class parseIcs
{
    public $file;

    public $timezone;

    public function __construct($url)
    {
        $this->file = $url;
    }

    public function displayTable()
    {
        $html = "";
        foreach ($this->getIcsEventsAsArray() as $event) {
            $html .= $event->html;
        }

        return "<table>".$html."</table>";
    }

    /* Function is to get all the contents from ics and explode all the datas according to the events and its sections */
    function getIcsEventsAsArray()
    {
        $icalString = file_get_contents($this->file);
        $icsDates = array();
        /* Explode the ICs Data to get datas as array according to string ‘BEGIN:’ */
        $icsData = explode("BEGIN:", $icalString);
        /* Iterating the icsData value to make all the start end dates as sub array */
        foreach ($icsData as $key => $value) {
            $icsDatesMeta [$key] = explode("\n", $value);
        }
        /* Itearting the Ics Meta Value */
        foreach ($icsDatesMeta as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                /* to get ics events in proper order */
                $icsDates = $this->getICSDates($key, $subKey, $subValue, $icsDates);
            }
        }

        return $this->sortEvents($icsDates);
    }

    function getICSDates($key, $subKey, $subValue, $icsDates)
    {
        if ($key != 0 && $subKey == 0) {
            $icsDates [$key] ["BEGIN"] = $subValue;
        } else {
            $subValueArr = explode(":", $subValue, 2);
            if (isset ($subValueArr [1])) {
                $icsDates [$key] [$subValueArr [0]] = $subValueArr [1];
            }
        }

        return $icsDates;
    }

    function sortEvents($input)
    {

        $events = array();

        foreach ($input as $item) {
            if (trim($item['BEGIN']) === "VCALENDAR") {
                $this->getTimezone($item);
                continue;
            }

            $event = new icsEvent($item, $this->timezone);
            if ($event->end_timestamp > $event->time_now) {
                $events[] = $event;
            }
        }
        usort($events, array($this, 'sorter'));

        return array_slice($events, 0, 12, true);
    }

    function getTimezone($item)
    {
        if (isset($item['X-WR-TIMEZONE'])) {
            $this->timezone = trim($item['X-WR-TIMEZONE']);
        }
    }

    /* funcion is to avaid the elements wich is not having the proper start, end  and summary informations */

    function sorter($a, $b)
    {
        if ($a->start_timestamp == $b->start_timestamp) {
            return 0;
        }

        return ($a->start_timestamp > $b->start_timestamp) ? 1 : -1;
    }
}
