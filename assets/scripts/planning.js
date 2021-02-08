import { Calendar } from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';

document.addEventListener('DOMContentLoaded', () => {
    var calendarEl = document.getElementById('calendar-holder');
    var calendar = new Calendar(calendarEl, {
        defaultView: 'dayGridMonth',
        locale: 'fr',
        editable: true,
        aspectRatio: 2.2,
        eventSources: [
            {
                url: calendarEl.dataset.srcUrl,
                method: "POST",
                extraParams: {
                    filters: JSON.stringify({})
                },
                failure: () => {},
            },
        ],
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour'
        },
        plugins: [ interactionPlugin, dayGridPlugin, timeGridPlugin ],
        timeZone: 'UTC',
        firstDay: 1,
    });
    calendar.render();
});