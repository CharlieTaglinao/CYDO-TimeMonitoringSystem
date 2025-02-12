document.addEventListener("DOMContentLoaded", function () {
    const logoutButton = document.getElementById("logout-button");

    if (logoutButton) {
        logoutButton.addEventListener("click", function (e) {
            e.preventDefault(); // Prevent default link action

            Swal.fire({
                title: "Are you sure?",
                text: "You will be logged out of your session.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#a9a9a9",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "process/logout.php";
                }
            });
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const visitorTable = document.getElementById('visitor-table');

    if (searchInput) {
        searchInput.addEventListener('input', (event) => {
            const query = event.target.value;
            fetch(`fetch-visitors.php?search=${encodeURIComponent(query)}`)
                .then(response => response.text())
                .then(data => {
                    visitorTable.innerHTML = data;
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    }
});
