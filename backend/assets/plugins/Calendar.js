import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '/api/events', // Replace with your actual API endpoint
            editable: true,
            selectable: true,
            select: function(info) {
                alert('Selected ' + info.startStr + ' to ' + info.endStr);
            },
            eventClick: function(info) {
                alert('Event: ' + info.event.title);
            }
        });
        calendar.render();
    }
});

export default Calendar;