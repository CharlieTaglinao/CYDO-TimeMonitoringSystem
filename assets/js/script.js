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