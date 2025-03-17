function showForm(formId) {
    document.getElementById('timeInForm').style.display = 'none';
    document.getElementById('timeOutForm').style.display = 'none';
    document.getElementById(formId).style.display = 'block';
}

function hideForm(formId) {
    document.getElementById(formId).style.display = 'none';
}

setTimeout(function () {
    var alert = document.querySelector('.alert');
    if (alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }
}, 10000);

function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

if (getQueryParam('showLoginModal') === 'true') {
    const LoginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    LoginModal.show();
}

function updateClock() {
    const clockElement = document.getElementById("clock");
    const now = new Date();
    const hours = now.getHours() > 12 ? now.getHours() - 12 : now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, "0");
    const seconds = now.getSeconds().toString().padStart(2, "0");
    const amPm = now.getHours() >= 12 ? "PM" : "AM";
    const timeString = `${hours}:${minutes}:${seconds} ${amPm}`;
    clockElement.textContent = timeString;
}

setInterval(updateClock, 1000);
updateClock();

document.addEventListener('DOMContentLoaded', () => {
    const dateElement = document.getElementById('date');
    const today = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

    dateElement.textContent = today.toLocaleDateString(undefined, options);

    // Check for mobile device and request camera access
    if (/Mobi|Android/i.test(navigator.userAgent)) {
        console.log("Mobile device detected. Requesting camera access...");
        Instascan.Camera.getCameras()
            .then(function (cameras) {
                if (cameras.length > 0) {
                    console.log("Cameras found on mobile device:", cameras);
                    scanner.start(cameras[0]);
                } else {
                    alert("No camera found on mobile device");
                }
            })
            .catch(function (e) {
                console.error("Error getting cameras on mobile device:", e);
            });
    }
    // Live validation for form fields
    // Dynamic feedback text
    const feedbackMessages = {
        firstName: "Please provide a first name.",
        middleName: "Please provide a middle name.",
        lastName: "Please provide a last name.",
        email: "Please provide a valid e-mail address.",
        purpose: "Please provide a purpose.",
        age: "Please provide a valid age between 1 and 150."
    };

    
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('input', function (event) {
            const target = event.target;
            const feedbackElement = target.nextElementSibling;
            if (target.checkValidity()) {
                target.classList.add('is-valid');
                target.classList.remove('is-invalid');
                feedbackElement.textContent = "";
            } else {
                target.classList.add('is-invalid');
                target.classList.remove('is-valid');
                feedbackElement.textContent = feedbackMessages[target.id] || "Invalid input.";
            }
        }, false);

        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                Array.prototype.slice.call(form.elements).forEach(function (element) {
                    if (!element.checkValidity() && element.required) {
                        element.classList.add('is-invalid');
                        const feedbackElement = element.nextElementSibling;
                        feedbackElement.textContent = feedbackMessages[element.id] || "Invalid input.";
                    }
                });
            }
        }, false);
    });

    const emailField = document.getElementById('email');
    const emailFeedback = document.querySelector('.valid-feedback.optional-email');
    const middleNameField = document.getElementById('middleName');
    const middleNameFeedback = document.querySelector('.valid-feedback.optional-middle-name');
    emailField.addEventListener('input', function () {
        const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
        if (emailField.value === "") {
            emailFeedback.style.display = "block";
            emailField.classList.remove('is-invalid');
            emailField.classList.remove('is-valid');
        } else if (emailPattern.test(emailField.value)) {
            emailFeedback.style.display = "none";
            emailField.classList.remove('is-invalid');
        } else {
            emailFeedback.style.display = "none";
            emailField.classList.add('is-invalid');
            emailField.classList.remove('is-valid');
        }
    });

    middleNameField.addEventListener('input', function () {
        const middleNamePattern = /^[a-zA-Z\s]*$/;
        if (middleNameField.value === "") {
            middleNameFeedback.style.display = "block";
            middleNameField.classList.remove('is-invalid');
            middleNameField.classList.remove('is-valid');
        } else if (middleNamePattern.test(middleNameField.value)) {
            middleNameFeedback.style.display = "none";
            middleNameField.classList.remove('is-invalid');
        } else {
            middleNameFeedback.style.display = "none";
            middleNameField.classList.add('is-invalid');
            middleNameField.classList.remove('is-valid');
        }
    });

    // Age validation
    const ageField = document.getElementById('age');
    ageField.addEventListener('input', function () {
        const ageValue = parseInt(ageField.value, 10);
        if (isNaN(ageValue) || ageValue < 1 || ageValue > 150) {
            ageField.setCustomValidity("Invalid age");
        } else {
            ageField.setCustomValidity("");
        }
    });

    // Remove 'is-valid' class if both middle name and email are null
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('input', function () {
            if (middleNameField.value === "" && emailField.value === "") {
                middleNameField.classList.remove('is-valid');
                emailField.classList.remove('is-valid');
            }
        });
    });
});

// Initialize the scanner
let scanner = new Instascan.Scanner({ video: document.getElementById("preview") });
if (document.getElementById("preview")) {
    Instascan.Camera.getCameras()
        .then(function (cameras) {
            if (cameras.length > 0) {
                console.log("Cameras found:", cameras);
                // Start the scanner with the first available camera
                scanner.start(cameras[0]);
            } else {
                alert("No camera found");
            }
        })
        .catch(function (e) {
            console.error("Error getting cameras:", e);
        });
} else {
    console.error("Video element not found");
}

// Add a listener for the scan event
scanner.addListener("scan", function (c) {
    document.getElementById("code").value = c;

    // Play the beep sound
    let beepAudio = document.getElementById("beep");
    console.log("Beep audio element:", beepAudio); 
    beepAudio.play().then(() => {
        console.log("Beep sound played successfully");
        setTimeout(function() {
            document.getElementById("btnTimeOut").click();
        }, 700); //since 1 second yung audio file kaya nag lagay tayo dito ng timeout na kahit .7s na delay para maplay muna yung audio bago masubmit 
    }).catch((error) => {
        console.error("Error playing beep sound:", error);
    });
});