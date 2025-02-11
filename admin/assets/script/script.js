const sidebar = document.getElementById('sidebar');
const toggleButton = document.getElementById('toggleButton');

toggleButton.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
});

//SWAL FOR LOGOUT IN ADMIN
document.getElementById('logout-button').addEventListener('click', function (e) {
    e.preventDefault(); 
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of the session!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to logout.php
            window.location.href = 'logout.php';
        }
    });
});