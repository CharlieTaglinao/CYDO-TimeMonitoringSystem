document.addEventListener('DOMContentLoaded', () => {
    const exportButtons = document.querySelectorAll('#export-today-button-xlsx, #export-todays-month-button-xlsx, #export-today-button-pdf, #export-todays-month-button-pdf');

    exportButtons.forEach(button => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();

            document.body.innerHTML += `
                <div class="background-overlay"></div>
                <div class="loader"></div>
                <div class="image-holder"></div>
                <div class="reminder-text">
                    <p id="reminder-text">Do not close the tab or browser while exporting report.</p>
                </div>
            `;

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
                    reminderText.style.transition = 'opacity 0.3s'; 
                    reminderText.style.opacity = 1;

                    setTimeout(() => {
                        reminderText.style.opacity = 0;
                        setTimeout(() => {
                            index = (index + 1) % messages.length;
                            fadeLoop();
                        }, 300); 
                    }, 1000);
                };

                fadeLoop();
            };

            fadeInOutText();

            try {
                await new Promise(resolve => setTimeout(resolve, 2000));

                const type = button.getAttribute('data-type');
                const format = button.getAttribute('data-format');
                const url = `process/export/AllReport/report-logic.php?type=${type}&format=${format}`;

                window.location.href = url;

                await new Promise(resolve => setTimeout(resolve, 2000));
            } finally {
                document.querySelectorAll('.background-overlay, .loader, .image-holder, .reminder-text').forEach(el => el.remove());
                const modalContainer = document.getElementById('modalContainer');
                if (modalContainer) {
                    modalContainer.innerHTML = '';
                }
            }

            location.reload();
        });
    });
});