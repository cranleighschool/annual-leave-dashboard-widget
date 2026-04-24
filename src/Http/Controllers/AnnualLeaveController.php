<?php

declare(strict_types=1);

namespace CranleighSchool\AnnualLeave\Http\Controllers;

use CranleighSchool\AnnualLeave\IcsParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class AnnualLeaveController extends Controller
{
    /**
     * Display the annual leave dashboard widget.
     */
    public function index(): View
    {
        $icsUri = config('annual_leave.google_calendar_ics_uri');

        if (empty($icsUri)) {
            abort(500, 'GOOGLE_CALENDAR_ICS_URI is not configured');
        }

        $parser = new IcsParser($icsUri);
        $events = $parser->parse();

        return view('annual-leave::widget', [
            'events' => $events,
        ]);
    }

    /**
     * Return annual leave events as JSON.
     */
    public function json(): JsonResponse
    {
        $icsUri = config('annual_leave.google_calendar_ics_uri');

        if (empty($icsUri)) {
            return response()->json([
                'error' => 'GOOGLE_CALENDAR_ICS_URI is not configured',
            ], 500);
        }

        try {
            $parser = new IcsParser($icsUri);
            $events = $parser->parse();

            $formattedEvents = array_map(function ($event) {
                $now = time();
                $isActive = $now >= $event->startTimestamp && $now <= $event->endTimestamp;

                return [
                    'title' => $event->title,
                    'start_timestamp' => $event->startTimestamp,
                    'end_timestamp' => $event->endTimestamp,
                    'start_date' => date('Y-m-d', $event->startTimestamp),
                    'end_date' => date('Y-m-d', $event->endTimestamp),
                    'start_datetime' => date('c', $event->startTimestamp), // ISO 8601
                    'end_datetime' => date('c', $event->endTimestamp), // ISO 8601
                    'readable_start' => $event->readableStart,
                    'readable_end' => $event->readableEnd,
                    'readable_range' => $event->readableStart === $event->readableEnd
                        ? $event->readableStart
                        : $event->readableStart . '-' . $event->readableEnd,
                    'timezone' => $event->timezone,
                    'is_active' => $isActive,
                ];
            }, $events);

            return response()->json([
                'success' => true,
                'count' => count($formattedEvents),
                'events' => $formattedEvents,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'error' => 'Unable to fetch calendar data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
