import FroalaEditor from 'froala-editor/js/froala_editor.pkgd.min';
import 'froala-editor/css/froala_editor.pkgd.min.css';
const getElement = (id) => document.getElementById(id);
const querySelector = (selector) => document.querySelector(selector);
document.addEventListener('DOMContentLoaded', () => {
        initializeFroalaEditors();
        addAppreciation();
});

function animateSaveIndicator() {
    const saveIndicator = getElement('save-indicator');
    if (saveIndicator) {
        console.log("clicker");
        // Make the indicator visible
        saveIndicator.style.opacity = '1';
        saveIndicator.style.transform = 'translateY(0)';

        // Hide the indicator after 2 seconds
        setTimeout(() => {
            saveIndicator.style.opacity = '0';
            saveIndicator.style.transform = 'translateY(-20px)';
        }, 2000);
    }
}
function initializeFroalaEditors() {
    const froalaElements = document.querySelectorAll('.froala-editor');
    froalaElements.forEach(element => {
        new FroalaEditor(element, {
            events: {
                'contentChanged': function () {
                    // Add any content change handling logic here
                }
            },
            // Add any additional Froala configuration options here
        });
    });
}

function getFormData() {
    return {
        comment: getElement('froala_editor_comment').value.trim(),
        score: getElement('froala_editor_score').value,
        interviewId: getElement('interviewId').value,

    };
}
function addAppreciation() {
    const saveButton = querySelector('#save-notes');
    saveButton.addEventListener('click', async function(event) {
        event.preventDefault();

        const formData = getFormData();
        saveButton.disabled = true;
        try {
            const success = await sendAppreciation(formData);
            console.log(success)
            if (success === "success") {
                animateSaveIndicator();
                console.log("Appreciation saved successfully!");
            } else {
                console.error("There was an error saving the appreciation.");
            }
        } catch (error) {
            console.error("Error saving appreciation:", error);
        } finally {
            saveButton.disabled = false;
        }
    });
}
// API interaction
async function sendAppreciation(formData) {
    try {
        const response = await fetch("/api/interview/appreciation", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data.status;
    } catch (error) {
        console.error('Error:', error);
        return false;
    }
}


const showEditorBtn = getElement('show-editor');
const editorContainer = getElement('froala-editor-container');

showEditorBtn.addEventListener('click', function() {
    addInterviewStatus();
    editorContainer.style.display = 'block';
    editorContainer.style.opacity = '0';
    editorContainer.style.transform = 'translateY(-20px)';
    editorContainer.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';

    setTimeout(function() {
        editorContainer.style.opacity = '1';
        editorContainer.style.transform = 'translateY(0)';
    }, 50);
});

async function addInterviewStatus() {
    const data = {
        status: "in_progress",
        interviewId: getElement('interviewId').value,
    };
    try {
        const response = await fetch("/api/interviewStatus", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data.status;
    } catch (error) {
        console.error('Error:', error);
        return false;
    }
}