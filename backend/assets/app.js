import './styles/app.css';
import { Turbo } from "@hotwired/turbo";

import 'froala-editor/css/froala_editor.pkgd.min.css';
import FroalaEditor from 'froala-editor/js/froala_editor.pkgd.min.js';

document.addEventListener('turbo:load', (event) => {
    const froalaElements = document.querySelectorAll('#froala-editor');
    if (froalaElements.length > 0) {
        froalaElements.forEach(element => {
            new FroalaEditor(element, {
                // Froala Editor options here
                events: {
                    'contentChanged': function () {
                        // Send the updated content to the server
                        updateNotes(this.html.get());
                    }
                }
            });
        });
    }
});

function updateNotes(content) {
    const entityId = document.body.dataset.entityId; // Assuming you add this data attribute to the body
    fetch(`/admin/api/interview/${entityId}/notes`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ notes: content })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Notes updated successfully');
            } else {
                console.error('Failed to update notes');
            }
        })
        .catch(error => console.error('Error:', error));
}