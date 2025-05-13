document.addEventListener('DOMContentLoaded', () => {
    const userSelect = document.getElementById('userSelect');
    const permissionsContainer = document.getElementById('permissionsContainer');

    userSelect.addEventListener('change', () => {
        const selectedOption = userSelect.options[userSelect.selectedIndex];
        const userRole = selectedOption.getAttribute('data-role');

        if (userRole === '2') {
            const restrictedPermissions = ['8sAygcnqpOXP8aAAG7IAWI4Cg', 'ubmssiHKw9GEPDulEVpDtOudM'];
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.disabled = restrictedPermissions.includes(checkbox.value);
            });
        } else {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.disabled = false;
            });
        }

        permissionsContainer.style.display = 'block';
        fetchPermissions(userSelect.value);
    });

    function fetchPermissions(userId) {
        fetch(`process/fetch-user-permissions.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = data.permissions.includes(checkbox.value);
                    checkbox.addEventListener('change', () => toggleRelatedPermissions(checkbox));
                });
            })
            .catch(error => console.error('Error:', error));
    }
    const permissions = {
        'T9rPHeL7ectsYwT6Ih2AswTeZ': 'Add Account',
        'c5pwoB1uPkzwwZgFokRZZ85fE': 'Monitor Visitor',
        'GfsdZkrEFuNhmIUmxIm8e7fS8': 'Download Reports',
        'qD0mEzTMK6Toi4u8aR1Pdusag': 'View Analytics',
        '906IZi3K8od7FBS518t5I31jY': 'Edit/Delete Account',
        '8sAygcnqpOXP8aAAG7IAWI4Cg': 'Add Permission',
        'ubmssiHKw9GEPDulEVpDtOudM': 'View User Permission',
        '6QmUceiC4tYAFPR8ablg55KFZ': 'Accept/Decline Application',
        '3DijIfOkS1uljCeXsJoyJAIbt': 'Activate/Deactivate Member',
        'M24yNFhXnUXIgzruLUVLrr1dQ': 'View Member Codes',
        'nLpx3AoiT6FJB8BVTGy4D6VE7': 'Add Account Type',
        'p1YjKW0Ny5bLE64YdGreQJ92w': 'View Account Type',
    };

    function toggleRelatedPermissions(checkbox) {
        if (checkbox.value === '8sAygcnqpOXP8aAAG7IAWI4Cg' && checkbox.checked) {
            document.querySelector('.permission-checkbox[value="ubmssiHKw9GEPDulEVpDtOudM"]').checked = true;
        } else if (checkbox.value === 'ubmssiHKw9GEPDulEVpDtO udM' && checkbox.checked) {
            document.querySelector('.permission-checkbox[value="8sAygcnqpOXP8aAAG7IAWI4Cg"]').checked = false;
        } else if (checkbox.value === 'ubmssiHKw9GEPDulEVpDtOudM' && !checkbox.checked) {
            document.querySelector('.permission-checkbox[value="8sAygcnqpOXP8aAAG7IAWI4Cg"]').checked = false;
        }
    }
});
