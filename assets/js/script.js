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

    const noEmailCheckbox = document.getElementById("noEmail");
    const emailField = document.getElementById("email");

    dateElement.textContent = today.toLocaleDateString(undefined, options);
    noEmailCheckbox.addEventListener("change", function () {
        if (this.checked) {
            emailField.value = "This visitor has no email.";
            emailField.disabled = true;
        } else {
            emailField.disabled = false;
        }
    });

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