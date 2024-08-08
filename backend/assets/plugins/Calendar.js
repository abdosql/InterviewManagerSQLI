import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import 'bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const addInterviewButton = document.getElementById('add-interview');
    const interviewModalEl = document.getElementById('interviewModal');
    const interviewModal = new bootstrap.Modal(interviewModalEl, { keyboard: false });
    const interviewForm = document.getElementById('interviewForm');
    let selectedEventId = null;
    let calendar = null;

    function addInterviewToCalendar(id, interviewData) {
        const eventTitle = `${interviewData.location} - ${interviewData.candidate}`;
        const evaluators = interviewData.evaluators.join(', ');

        calendar.addEvent({
            id: id,
            title: eventTitle,
            start: interviewData.date,
            extendedProps: {
                location: interviewData.location,
                candidate: interviewData.candidate,
                evaluators: evaluators
            }
        });
    }

    function showEventDetails(info) {
        const event = info.event;
        const props = event.extendedProps;
        alert(`
            Interview Details:
            Location: ${props.location}
            Candidate: ${props.candidate}
            Evaluators: ${props.evaluators}
            Date: ${event.start.toLocaleString()}
        `);
    }
    function showInterviewDetails(info) {
        const event = info.event;
        const props = event.extendedProps;
        const interviewDetailsModalEl = document.getElementById('interviewDetailsModal');
        const interviewDetailsModal = new bootstrap.Modal(interviewDetailsModalEl, { keyboard: false });

        // Populate the modal content
        document.getElementById('interview-details-location').innerText = props.location;
        document.getElementById('interview-details-candidate').innerText = props.candidate;
        document.getElementById('interview-details-evaluators').innerText = props.evaluators;
        document.getElementById('interview-details-date').innerText = event.start.toLocaleString();

        // Show the modal
        interviewDetailsModal.show();
    }
    if (calendarEl) {
        calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '/api/interviews', // Replace with your actual API endpoint
            editable: true,
            selectable: true,
            select: function(info) {
                alert('Selected ' + info.startStr + ' to ' + info.endStr);
            },
            eventClick: showInterviewDetails, // Attach the function here
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
    }

    addInterviewButton.addEventListener('click', function() {
        interviewModal.show();
    });

    interviewForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = {};
        const inputs = this.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    if (input.checked) {
                        formData[input.name] = input.value;
                    }
                } else if (input.multiple) {
                    formData[input.name] = Array.from(input.selectedOptions).map(option => option.value);
                } else {
                    formData[input.name] = input.value;
                }
            }
        });

        // Transform formData keys to a simple structure
        const transformedData = {
            location: formData['interview[interview_location]'],
            candidate: formData['interview[candidate]'],
            evaluators: formData['interview[evaluators][]'],
            date: formData['interview[interview_date]'],
            token: formData['interview[_token]']
        };

        fetch('/api/interviews', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(transformedData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add the new event to the calendar
                    addInterviewToCalendar(5, transformedData);

                    interviewModal.hide();
                    this.reset();
                } else {
                    alert('Error saving interview: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the interview.');
            });
    });
});