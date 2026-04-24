<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Calendar ICS URI
    |--------------------------------------------------------------------------
    |
    | The URL to your Google Calendar ICS file. This is used to fetch
    | annual leave events and display them in the dashboard widget.
    |
    */

    'google_calendar_ics_uri' => env('ANNUAL_LEAVE_GOOGLE_CALENDAR_ICS_URI'),

    /*
    |--------------------------------------------------------------------------
    | Timezone
    |--------------------------------------------------------------------------
    |
    | Default timezone for parsing calendar events if not specified in the ICS file.
    |
    */

    'timezone' => env('ANNUAL_LEAVE_TIMEZONE', config('app.timezone')),

    /*
    |--------------------------------------------------------------------------
    | Maximum Events to Display
    |--------------------------------------------------------------------------
    |
    | The maximum number of upcoming events to display in the widget.
    |
    */

    'max_events' => env('ANNUAL_LEAVE_MAX_EVENTS', 12),

    /*
    |--------------------------------------------------------------------------
    | Enable Routes
    |--------------------------------------------------------------------------
    |
    | Whether to automatically register the /annual-leave route.
    | Set to false if you want to register routes manually.
    |
    */

    'enable_routes' => env('ANNUAL_LEAVE_ENABLE_ROUTES', true),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix for the annual leave routes.
    |
    */

    'route_prefix' => env('ANNUAL_LEAVE_ROUTE_PREFIX', 'annual-leave'),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware to apply to the annual leave routes.
    |
    */

    'route_middleware' => [],

];
