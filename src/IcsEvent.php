<?php

declare(strict_types=1);

namespace CranleighSchool\AnnualLeave;

use DateTime;
use DateTimeZone;

readonly class IcsEvent
{
    public string $timezone;

    public string $title;

    public int $startTimestamp;

    public string $readableStart;

    public int $endTimestamp;

    public string $readableEnd;

    public int $timeNow;

    private bool $allDayEvent;

    /**
     * @param array<string, string> $item
     * @param string $timezone
     */
    public function __construct(array $item, string $timezone)
    {
        $item = array_map('trim', $item);

        $this->timezone = $timezone;
        $this->title = $item['SUMMARY'] ?? '';
        $this->timeNow = time();

        // Determine if all-day event
        if (isset($item['DTSTART;VALUE=DATE'])) {
            $this->allDayEvent = true;
            $startTime = $item['DTSTART;VALUE=DATE'];
            $endTime = $item['DTEND;VALUE=DATE'];
        } else {
            $this->allDayEvent = false;
            $startTime = $item['DTSTART'] ?? '';
            $endTime = $item['DTEND'] ?? '';
        }

        // Parse start date/time
        $startDt = new DateTime($startTime);
        $startDt->setTimeZone(new DateTimeZone($this->timezone));
        $this->startTimestamp = $startDt->getTimestamp();

        if ($this->allDayEvent) {
            $this->readableStart = $startDt->format("d M");
        } else {
            $this->readableStart = $this->formatTime($startDt->format("d M g:ia"));
        }

        // Parse end date/time
        $endDt = new DateTime($endTime);
        $endDt->setTimeZone(new DateTimeZone($this->timezone));
        $this->endTimestamp = $endDt->getTimestamp() - 1; // Minus 1 second to account for all-day event boundaries

        if ($this->allDayEvent) {
            $this->readableEnd = date("d M", $this->endTimestamp);
        } else {
            if ($endDt->format("d M") === $startDt->format("d M")) {
                $this->readableEnd = $this->formatTime($endDt->format("g:ia"));
            } else {
                $this->readableEnd = $this->formatTime($endDt->format("d M g:ia"));
            }
        }
    }

    /**
     * Format time string by removing ":00" minutes when on the hour
     */
    private function formatTime(string $input): string
    {
        if (str_contains($input, ":00")) {
            return str_replace(":00", "", $input);
        }

        return $input;
    }
}
