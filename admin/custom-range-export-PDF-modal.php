<div class="modal fade" id="customRangeExportPDFModal" tabindex="-1" aria-labelledby="customRangeExportPDFModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customRangeExportPDFModalLabel">Select Custom Date Range</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="customRangeFormPDF"
                    action="process/export/AllReport/report-logic.php?type=custom&format=pdf">
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDatePDF" name="startDate">
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDatePDF" name="endDate">
                    </div>
                    <button type="submit" class="btn btn-primary" id="applyDateRange">Download</button>
                </form>

            </div>
        </div>
    </div>
</div>
<script>
   const exportForm = document.getElementById('customRangeFormPDF');
    const applyButton = document.getElementById('applyDateRange');

    if (applyButton) {
        applyButton.addEventListener('click', async () => {
            // Validate form fields
            if (!exportForm.checkValidity()) {
                exportForm.reportValidity();
                return;
            }

            // Hide the modal
            const modal = document.getElementById('customRangeExportPDFModal');
            if (modal) {
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                if (bootstrapModal) {
                    bootstrapModal.hide();
                }
            }

            // Ensure the modal backdrop is removed
            const modalBackdrop = document.querySelector('.modal-backdrop');
            if (modalBackdrop) {
                modalBackdrop.remove();
            }

            // Show loader
            document.body.innerHTML += `
                <div class="background-overlay"></div>
                <div class="loader"></div>
                <div class="image-holder"></div>
                <div class="reminder-text">
                    <p id="reminder-text">Do not close the tab or browser while generating.</p>
                </div>
            `;

            // Function to fade in and out the reminder text
            const fadeInOutText = () => {
                const reminderText = document.getElementById('reminder-text');
                const messages = [
                    'Do not close the tab or browser while exporting report.',
                    'Please wait patiently.',
                    'Your report is being prepared.',
                    'This may take a few moments.',
                    'Thank you for your patience.'
                ];
                let index = 0;

                const fadeLoop = () => {
                    reminderText.textContent = messages[index];
                    reminderText.style.transition = 'opacity 0.3s'; // Faster fade-in
                    reminderText.style.opacity = 1;

                    setTimeout(() => {
                        reminderText.style.opacity = 0;
                        setTimeout(() => {
                            index = (index + 1) % messages.length;
                            fadeLoop();
                        }, 300); // Faster fade-out
                    }, 1000); // Shorter display time
                };

                fadeLoop();
            };

            fadeInOutText();

            // Submit the form using fetch
            const formData = new FormData(exportForm);
            const url = exportForm.action;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    const contentDisposition = response.headers.get('Content-Disposition');
                    let fileName = 'Custom-Office-Report.xlsx';

                    // Extract file name from Content-Disposition header if available
                    if (contentDisposition && contentDisposition.includes('filename=')) {
                        const match = contentDisposition.match(/filename="(.+?)"/);
                        if (match && match[1]) {
                            fileName = match[1];
                        }
                    }

                    const blob = await response.blob();

                    // Add a 2-second delay before providing the file
                    await new Promise(resolve => setTimeout(resolve, 2000));

                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;
                    link.click();

                    // Reload the page after success
                    location.reload();
                } else {
                    const errorData = await response.json();
                    alert(errorData.error || 'Failed to generate the report. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while generating the report.');
            } finally {
                // Remove loader
                document.querySelectorAll('.background-overlay, .loader, .image-holder, .reminder-text').forEach(el => el.remove());
            }
        });
    }
</script>