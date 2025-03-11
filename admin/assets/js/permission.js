function showPermissions() {
    var userSelect = document.getElementById('userSelect');
    var permissionsContainer = document.getElementById('permissionsContainer');
    if (userSelect.value) {
        permissionsContainer.style.display = 'block';
        fetchPermissions(userSelect.value);
    } else {
        permissionsContainer.style.display = 'none';
    }
}

function fetchPermissions(userId) {
    fetch(`process/fetch-user-permissions.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('#permissionsContainer input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = data.permissions.includes(checkbox.value);
            });
        })
        .catch(error => console.error('Error:', error));
}
