// assets/app.js

import './styles/app.css';
// assets/app.js

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import '@fullcalendar/core/index.js';
import '@fullcalendar/daygrid/index.js';
console.log("i was here")
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        var calendar = new Calendar(calendarEl, {
            plugins: [ dayGridPlugin, interactionPlugin ],
            initialView: 'dayGridMonth',
            selectable: true,
            dateClick: function(info) {
                alert('Clicked on: ' + info.dateStr);
            },
            select: function(info) {
                alert('Selected from ' + info.startStr + ' to ' + info.endStr);
            }
        });

        calendar.render();
    }
});
