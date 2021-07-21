import { Calendar } from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import bootstrapPlugin from '@fullcalendar/bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    var calendarEl = document.getElementById('calendar-holder');
    var calendar;
    if($(window).width() > 514) {
        calendar = new Calendar(calendarEl, {
            defaultView: 'dayGridMonth',
            locale: 'fr',
            editable: true,
            aspectRatio: 2.25,
            nowIndicator: true,
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
            plugins: [ interactionPlugin, dayGridPlugin, timeGridPlugin, bootstrapPlugin ],
            themeSystem: 'bootstrap',
            timeZone: 'UTC',
            firstDay: 1,
        });
    }
    else {
        calendar = new Calendar(calendarEl, {
            defaultView: 'timeGridDay',
            locale: 'fr',
            editable: true,
            nowIndicator: true,
            contentHeight: 600,
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
                left: 'prev,next',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },
            buttonText: {
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour'
            },
            plugins: [ interactionPlugin, dayGridPlugin, timeGridPlugin, bootstrapPlugin ],
            themeSystem: 'bootstrap',
            timeZone: 'UTC',
            firstDay: 1,
        });
    }
    calendar.render();
});