<?php

namespace App\Enum;

enum EventMomentTypeEnum: string
{
    case TITLE_CHANGE = 'event.change.title';
    case DATE_CHANGE = 'event.change.date';
    case TIME_CHANGE = 'event.change.time';
    case ADDRESS_CHANGE = 'event.change.address';
    case TICKET_OPTION_ADDED = 'event.change.ticket.option.added';
    case EVENT_ORGANISER_ADDED = 'event.change.organiser.add';
    case EVENT_ORGANISER_REMOVED = 'event.change.organiser.remove';
    case EVENT_CANCELED = 'event.change.canceled';
}
