import FroalaEditor from 'froala-editor/js/froala_editor.pkgd.min';
import 'froala-editor/css/froala_editor.pkgd.min.css';
const getElement = (id) => document.getElementById(id);
document.addEventListener('DOMContentLoaded', () => {
        initializeFroalaEditors();
        if (getElement('save-notes')) {
            addAppreciation();
        }
        refreshAccordionContent();
        initializeInterviewStatusButtons();
        initializeReformulateButton();
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
    const saveButton = getElement('save-notes');
    if (saveButton) {
        saveButton.addEventListener('click', function(event) {
            event.preventDefault();

            const formData = getFormData();

            // Check if fields are empty
            if (!formData.comment || !formData.score) {
                alert('Please fill in both the comment and score fields before saving.');
                return;
            }

            saveButton.disabled = true;

            sendAppreciation(formData)
                .then(success => {
                    if (success === "success") {
                        animateSaveIndicator();
                        console.log("Appreciation saved successfully!");
                        return refreshAccordionContent();
                    } else {
                        console.error("There was an error saving the appreciation.");
                    }
                })
                .catch(error => {
                    console.error("Error saving appreciation:", error);
                })
                .finally(() => {
                    saveButton.disabled = false;
                });
        });
    }
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

if (showEditorBtn && editorContainer) {
    showEditorBtn.addEventListener('click', function() {
        InterviewStatusInProgress().then(r => showEditorBtn.disabled =  true);
        editorContainer.style.display = 'block';
        editorContainer.style.opacity = '0';
        editorContainer.style.transform = 'translateY(-20px)';
        editorContainer.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';

        setTimeout(function() {
            editorContainer.style.opacity = '1';
            editorContainer.style.transform = 'translateY(0)';
        }, 50);
    });
}

async function InterviewStatusInProgress() {
    const data = {
        status: "IN_PROGRESS",
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

        const responseData = await response.json();
        return responseData.status;
    } catch (error) {
        console.error('Error:', error);
        return false;
    }
}


async function refreshAccordionContent() {
    const interviewId = getElement('interviewId').value;
    try {
        const response = await fetch(`/api/interview/${interviewId}/appreciations`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const appreciations = await response.json();
        const appreciationsList = getElement('appreciationsList');

        if (appreciations.length === 0) {
            appreciationsList.innerHTML = `
                <li class="list-group-item text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    No notes available for this interview.
                </li>
            `;
        } else {
            appreciationsList.innerHTML = appreciations.map(appreciation => `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="d-flex justify-content-center align-items-center">
                        <i style="font-size: 1.5rem;" class="fas fa-comment-alt me-2 text-primary"></i>
                        <div class="p-4">
                            ${appreciation.comment}
                        </div>
                    </span>
                    <span class="badge bg-primary rounded-pill text-white">
                        <i class="fas fa-star me-1"></i>
                        ${appreciation.score}
                    </span>
                </li>
            `).join('');
        }
    } catch (error) {
        console.error('Error refreshing accordion content:', error);
        getElement('appreciationsList').innerHTML = `
            <li class="list-group-item text-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error loading appreciations. Please try again later.
            </li>
        `;
    }
}

function initializeInterviewStatusButtons() {
    const acceptButton = getElement('accept-interview');
    const rejectButton = getElement('reject-interview');

    if (acceptButton) {
        acceptButton.addEventListener('click', () => updateInterviewStatus('IS_PASSED'));
    }

    if (rejectButton) {
        rejectButton.addEventListener('click', () => updateInterviewStatus('IS_FAILED'));
    }
}

async function updateInterviewStatus(status) {
    const interviewId = getElement('interviewId').value;
    const data = {
        status: status,
        interviewId: interviewId,
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

        const responseData = await response.json();
        if (responseData.status === 'success') {
            alert('Interview status updated successfully');
            location.reload(); // Refresh the page to reflect the changes
        } else {
            alert('Failed to update interview status');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating the interview status');
    }
}

function initializeReformulateButton() {
    const reformulateButton = getElement('reformulate');
    if (reformulateButton) {
        reformulateButton.addEventListener('click', handleReformulate);
    }
}

async function handleReformulate() {
    const commentEditor = FroalaEditor.INSTANCES[0];
    const commentTextarea = getElement('froala_editor_comment');
    const scoreInput = getElement('froala_editor_score');
    const reformulateButton = getElement('reformulate');

    const comment = commentEditor.html.get();
    const score = scoreInput.value;

    if (!comment || !score) {
        alert('Please fill in both the comment and score fields before reformulating.');
        return;
    }

    const promptContent = `{ 'comment': '${comment.replace(/'/g, "\\'")}', 'score': ${score}/20 }`;
    const prompt = JSON.stringify({
        prompt: promptContent
    });
    reformulateButton.disabled = true;
    reformulateButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Reformulating...';

    try {
        const response = await fetch('/interview/ai-reformulation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: prompt
        });

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
        }

        const data = await response.json();
        const reformulation = data.aiReformulation;

        if (!reformulation || !reformulation.comment || !reformulation.score) {
            throw new Error('Invalid response format from AI service');
        }

        commentEditor.html.set(reformulation.comment);
        commentTextarea.value = reformulation.comment;
        scoreInput.value = reformulation.score.toString().split('/')[0];

        commentEditor.events.trigger('contentChanged');

    } catch (error) {
        console.error('Error:', error);
        alert(`An error occurred while reformulating: ${error.message}. Please check the console for more details and try again.`);
    } finally {
        reformulateButton.disabled = false;
        reformulateButton.innerHTML = 'Reformulate with AI';
    }
}