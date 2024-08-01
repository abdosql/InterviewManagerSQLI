import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    let selectedEventId = null; // Variable to store the selected event ID

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
                selectedEventId = info.event.id; // Store the selected event ID
                alert('Selected Event: ' + info.event.title);
            },
            eventDrop: function(info) {
                alert('Event dropped on ' + info.event.start.toISOString());
                // Here you can make an AJAX call to save the new date to your server
            },
            eventResize: function(info) {
                alert('Event resized to end on ' + info.event.end.toISOString());
                // Here you can make an AJAX call to save the new end date to your server
            }
        });
        calendar.render();

        // Delete button event listener
        const deleteInterviewButton = document.getElementById('delete-interview');
        deleteInterviewButton.addEventListener('click', function() {
            const selectedEvent = calendar.getEventById(selectedEventId);
            if (selectedEvent) {
                if (confirm('Are you sure you want to delete this interview?')) {
                    // Perform deletion logic here
                    selectedEvent.remove();
                    updateUpcomingInterviews();
                    // You may want to add an API call to delete the event from the server
                }
            } else {
                alert('Please select an interview to delete.');
            }
        });

        // Function to update the upcoming interviews list
        function updateUpcomingInterviews() {
            const upcomingInterviewsList = document.getElementById('upcoming-interviews');
            const events = calendar.getEvents();
            const now = new Date();
            const upcomingEvents = events
                .filter(event => event.start > now)
                .sort((a, b) => a.start - b.start)
                .slice(0, 5); // Get the next 5 upcoming interviews

            upcomingInterviewsList.innerHTML = ''; // Clear the current list

            upcomingEvents.forEach(event => {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = `
                    <strong>${event.title}</strong><br>
                    <small>${event.start.toLocaleString()}</small>
                `;
                upcomingInterviewsList.appendChild(li);
            });

            if (upcomingEvents.length === 0) {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.textContent = 'No upcoming interviews';
                upcomingInterviewsList.appendChild(li);
            }
        }

        // Initial update of upcoming interviews
        updateUpcomingInterviews();

        // Update upcoming interviews whenever the calendar data changes
        calendar.on('eventAdd', updateUpcomingInterviews);
        calendar.on('eventRemove', updateUpcomingInterviews);
        calendar.on('eventChange', updateUpcomingInterviews);
    }
});

export default Calendar;
