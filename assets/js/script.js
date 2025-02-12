function showForm(formId) {
    document.getElementById('timeInForm').style.display = 'none';
    document.getElementById('timeOutForm').style.display = 'none';
    document.getElementById(formId).style.display = 'block';
}

function hideForm(formId) {
    document.getElementById(formId).style.display = 'none';
}

//for closing alert after 10 seconds automatically in time in and time out 
setTimeout(function () {
    var alert = document.querySelector('.alert');
    if (alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }
}, 10000);

