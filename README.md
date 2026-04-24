# Annual Leave Dashboard Widget

? A Laravel package for displaying annual leave events from Google Calendar ICS feeds in your Laravel 12/13 application.

## Requirements

- PHP 8.5+
- Laravel 12.x or 13.x

## Installation

Install the package via Composer:

```bash
composer require fredbradley/annual-leave-widget
```

The package will automatically register itself via Laravel's package auto-discovery.

## Configuration

### 1. Publish the Configuration File (Optional)

If you want to customize the configuration, publish the config file:

```bash
php artisan vendor:publish --tag=annual-leave-config
```

This will create a `config/annual_leave.php` file in your application.

### 2. Add Your Google Calendar ICS URL

Add your Google Calendar ICS URL to your `.env` file:

```env
GOOGLE_CALENDAR_ICS_URI=https://calendar.google.com/calendar/ical/your-calendar-id/public/basic.ics
```

#### How to Get Your Google Calendar ICS URL

1. Open Google Calendar
2. Click on the three dots next to the calendar you want to share
3. Select "Settings and sharing"
4. Scroll down to "Integrate calendar"
5. Copy the "Secret address in iCal format" URL

![Getting iCal Link](https://support.foxbright.com/hc/article_attachments/360000116493/iCal_link_address.png)

### 3. Optional Environment Variables

You can also configure the following in your `.env` file:

```env
# Timezone for parsing calendar events (default: Europe/London)
ANNUAL_LEAVE_TIMEZONE=Europe/London

# Maximum number of events to display (default: 12)
ANNUAL_LEAVE_MAX_EVENTS=12
```

## Usage

### Basic Usage

Once installed and configured, the package automatically registers a route at `/annual-leave` that displays the widget.

Visit: `https://your-app.test/annual-leave`

### Custom Views

If you want to customize the view, publish the views:

```bash
php artisan vendor:publish --tag=annual-leave-views
```

This will create views in `resources/views/vendor/annual-leave/` which you can customize.

### Using the Widget in Your Own Views

You can also include the widget in your own views:

```blade
@include('annual-leave::widget', ['events' => $events])
```

To get the events in your controller:

```php
use CranleighSchool\AnnualLeave\IcsParser;

public function myMethod()
{
    $parser = new IcsParser(config('annual_leave.google_calendar_ics_uri'));
    $events = $parser->parse();

    return view('your-view', compact('events'));
}
```

### JSON API Endpoint

The package also provides a JSON API endpoint for consuming applications:

**Endpoint:** `GET /annual-leave/json`

**Response Format:**

```json
{
  "success": true,
  "count": 12,
  "events": [
    {
      "title": "John Doe - Annual Leave",
      "start_timestamp": 1735689600,
      "end_timestamp": 1736294399,
      "start_date": "2025-01-01",
      "end_date": "2025-01-07",
      "start_datetime": "2025-01-01T00:00:00+00:00",
      "end_datetime": "2025-01-07T23:59:59+00:00",
      "readable_start": "01 Jan",
      "readable_end": "07 Jan",
      "readable_range": "01 Jan-07 Jan",
      "timezone": "Europe/London",
      "is_active": true
    }
  ]
}
```

**Error Response:**

```json
{
  "error": "GOOGLE_CALENDAR_ICS_URI is not configured"
}
```

**Using the JSON API:**

```javascript
// JavaScript/Fetch example
fetch('/annual-leave/json')
  .then(response => response.json())
  .then(data => {
    console.log(`Found ${data.count} events`);
    data.events.forEach(event => {
      console.log(`${event.title}: ${event.readable_range}`);
    });
  });
```

```php
// PHP/Laravel HTTP Client example
$response = Http::get('https://your-app.test/annual-leave/json');
$events = $response->json('events');
```

### Styling

The widget outputs HTML with the following structure:

```html
<div class="leavedatablock">
    <table>
        <tr class="today"><!-- class 'today' is added for current events -->
            <td>
                <span class="the_date">01 Jan-05 Jan: </span>
                Event Title
            </td>
        </tr>
    </table>
</div>
```

You can add your own CSS to style the widget. Events that are currently active (today is between start and end dates) will have the `today` class applied to the `<tr>` element.

## Configuration Options

The `config/annual_leave.php` file contains the following options:

| Option | Environment Variable | Default | Description |
|--------|---------------------|---------|-------------|
| `google_calendar_ics_uri` | `GOOGLE_CALENDAR_ICS_URI` | `null` | The URL to your Google Calendar ICS file |
| `timezone` | `ANNUAL_LEAVE_TIMEZONE` | `Europe/London` | Default timezone for parsing events |
| `max_events` | `ANNUAL_LEAVE_MAX_EVENTS` | `12` | Maximum number of upcoming events to display |
| `enable_routes` | `ANNUAL_LEAVE_ENABLE_ROUTES` | `true` | Whether to automatically register routes |
| `route_prefix` | `ANNUAL_LEAVE_ROUTE_PREFIX` | `annual-leave` | URL prefix for package routes |
| `route_middleware` | N/A | `[]` | Middleware to apply to routes (set in config file) |

## License

This package is open-sourced software. Please check the repository for license information.

## Credits

- [Fred Bradley](https://github.com/fredbradley)
- [Cranleigh School](https://github.com/cranleighschool)
