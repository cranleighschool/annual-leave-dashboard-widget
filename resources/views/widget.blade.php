<div class="leavedatablock">
    <table>
        @foreach($events as $event)
            <tr class="{{ (time() < $event->endTimestamp && time() > $event->startTimestamp) ? 'today' : '' }}">
                <td>
                    <span class="the_date">
                        @if($event->readableStart === $event->readableEnd)
                            {{ $event->readableStart }}
                        @else
                            {{ $event->readableStart }}-{{ $event->readableEnd }}
                        @endif
                        :
                    </span>
                    {{ $event->title }}
                </td>
            </tr>
        @endforeach
    </table>
</div>
