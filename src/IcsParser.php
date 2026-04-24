<?php

declare(strict_types=1);

namespace CranleighSchool\AnnualLeave;

use RuntimeException;

class IcsParser
{
    private readonly string $url;

    private string $timezone;

    private int $maxEvents;

    public function __construct(string $url, ?string $timezone = null, ?int $maxEvents = null)
    {
        $this->url = $url;
        $this->timezone = $timezone ?? config('annual_leave.timezone', config('app.timezone'));
        $this->maxEvents = $maxEvents ?? config('annual_leave.max_events', 12);
    }

    /**
     * Parse ICS file and return array of events
     *
     * @return array<IcsEvent>
     * @throws RuntimeException
     */
    public function parse(): array
    {
        $icalString = @file_get_contents($this->url);

        if ($icalString === false) {
            throw new RuntimeException("Unable to fetch ICS file from: {$this->url}");
        }

        $icsDates = [];
        $icsDatesMeta = [];

        // Explode the ICS Data to get data as array according to string 'BEGIN:'
        $icsData = explode("BEGIN:", $icalString);

        // Iterate the icsData to extract VEVENT sections
        foreach ($icsData as $key => $value) {
            if (str_starts_with($value, 'VEVENT')) {
                $icsDatesMeta[$key] = explode("\n", $value);
            }
        }

        // Parse each event section
        foreach ($icsDatesMeta as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $icsDates = $this->parseEventData($key, $subKey, $subValue, $icsDates);
            }
        }

        return $this->filterAndSortEvents($icsDates);
    }

    /**
     * Extract event data from ICS content
     *
     * @param int|string $key
     * @param int|string $subKey
     * @param string $subValue
     * @param  array  $icsDates
     * @return array
     */
    private function parseEventData(int|string $key, int|string $subKey, string $subValue, array $icsDates): array
    {
        if ($key != 0 && $subKey == 0) {
            $icsDates[$key]["BEGIN"] = $subValue;
        } else {
            $subValueArr = explode(":", $subValue, 2);
            if (isset($subValueArr[1])) {
                $icsDates[$key][$subValueArr[0]] = $subValueArr[1];
            }
        }

        return $icsDates;
    }

    /**
     * Filter upcoming events and sort by start date
     *
     * @param array<mixed> $input
     * @return array<IcsEvent>
     */
    private function filterAndSortEvents(array $input): array
    {
        $events = [];

        foreach ($input as $item) {
            if (trim($item['BEGIN']) === "VCALENDAR") {
                $this->extractTimezone($item);
                break;
            }

            $event = new IcsEvent($item, $this->timezone);
            if ($event->endTimestamp > $event->timeNow) {
                $events[] = $event;
            }
        }

        usort($events, $this->compareEventsByStartTime(...));

        return array_slice($events, 0, $this->maxEvents, true);
    }

    /**
     * Extract timezone from ICS calendar metadata
     *
     * @param array<string, string> $item
     */
    private function extractTimezone(array $item): void
    {
        if (isset($item['X-WR-TIMEZONE'])) {
            $this->timezone = trim($item['X-WR-TIMEZONE']);
        } elseif (isset($item['X-LIC-LOCATION'])) {
            $this->timezone = trim($item['X-LIC-LOCATION']);
        }
    }

    /**
     * Compare two events by their start timestamp
     */
    private function compareEventsByStartTime(IcsEvent $a, IcsEvent $b): int
    {
        return $a->startTimestamp <=> $b->startTimestamp;
    }
}
