import $ from 'jquery';
import 'chosen-js';
import 'chosen-js/chosen.css';

// Function to initialize Chosen on select elements
function initializeChosen() {
    $('.chosen-select').chosen({
        no_results_text: 'Oops, nothing found!',
        width: '100%',
        allow_single_deselect: true
    });
}

$(document).ready(function() {
    // Initialize Chosen on page load
    initializeChosen();

    // Reinitialize Chosen when the modal is shown
    $('#interviewModal').on('shown.bs.modal', function () {
        initializeChosen();  // Reinitialize Chosen for elements within the modal
    });
});
