function showPermissions() {
    var userSelect = document.getElementById('userSelect');
    var permissionsContainer = document.getElementById('permissionsContainer');
    if (userSelect.value) {
        var selectedOption = userSelect.options[userSelect.selectedIndex];
        var userRole = selectedOption.getAttribute('data-role');

        if (userRole === '2') {
            var restrictedPermissions = ['8sAygcnqpOXP8aAAG7IAWI4Cg', 'ubmssiHKw9GEPDulEVpDtOudM'];
            document.querySelectorAll('#permissionsContainer input[type="checkbox"]').forEach(checkbox => {
                if (restrictedPermissions.includes(checkbox.value)) {
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            });

            // Hide specific cards for role = 2
            document.getElementById('permission_View_User_Permission').style.display = 'none';
            document.getElementById('permission_Add_Permission').style.display = 'none';
            
        } else {
            document.querySelectorAll('#permissionsContainer input[type="checkbox"]').forEach(checkbox => {
                checkbox.disabled = false;
            });

            // Show all cards for other roles
            document.getElementById('permission_View_User_Permission').style.display = 'block';
            document.getElementById('permission_Add_Permission').style.display = 'block';
        }

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
                checkbox.addEventListener('change', function() {
                    toggleRelatedPermissions(checkbox);
                });
            });
        })
        .catch(error => console.error('Error:', error));
}

// list of permissions id
// T9rPHeL7ectsYwT6Ih2AswTeZ  for Add Account
// c5pwoB1uPkzwwZgFokRZZ85fE  for Monitor Visitor
// GfsdZkrEFuNhmIUmxIm8e7fS8  for Download Reports
// qD0mEzTMK6Toi4u8aR1Pdusag  for View Analytics
// JyCQULxjmYOycJFVOyceWb8BA  for Monitor Dashboard
// 906IZi3K8od7FBS518t5I31jY  for Edit/Delete Account
// 8sAygcnqpOXP8aAAG7IAWI4Cg  for Add Permission
// ubmssiHKw9GEPDulEVpDtOudM  for View Permission

function toggleRelatedPermissions(checkbox) {

    // Allowed to View Permission and Add Permission
    // Allowed to View Permission but Not allowed to Add Permission
    
    if (checkbox.value === '8sAygcnqpOXP8aAAG7IAWI4Cg' && checkbox.checked) {
        document.querySelector('#permissionsContainer input[value="ubmssiHKw9GEPDulEVpDtOudM"]').checked = true;
    } else if (checkbox.value === 'ubmssiHKw9GEPDulEVpDtOudM' && checkbox.checked) {
        document.querySelector('#permissionsContainer input[value="8sAygcnqpOXP8aAAG7IAWI4Cg"]').checked = false;
    } else if (checkbox.value === 'ubmssiHKw9GEPDulEVpDtOudM' && !checkbox.checked) {
        document.querySelector('#permissionsContainer input[value="8sAygcnqpOXP8aAAG7IAWI4Cg"]').checked = false;
    } 
    
    // // Allowed to Add Account and Edit/Delete Permission
    // // Allowed to Add Account but Not Edit/Delete Permission

    // else if (checkbox.value === 'T9rPHeL7ectsYwT6Ih2AswTeZ' && checkbox.checked){
    //     document.querySelector('#permissionsContainer input[value="906IZi3K8od7FBS518t5I31jY"]').checked = true;
    // } else if (checkbox.value === '906IZi3K8od7FBS518t5I31jY' && checkbox.checked){
    //     document.querySelector('#permissionsContainer input[value="T9rPHeL7ectsYwT6Ih2AswTeZ"]').checked = false;
    // } else if (checkbox.value === '906IZi3K8od7FBS518t5I31jY' && !checkbox.checked){
    //     document.querySelector('#permissionsContainer input[value="T9rPHeL7ectsYwT6Ih2AswTeZ"]').checked = false;
    // } else if (checkbox.value === 'T9rPHeL7ectsYwT6Ih2AswTeZ' && !checkbox.checked){
    //     document.querySelector('#permissionsContainer input[value="906IZi3K8od7FBS518t5I31jY"]').checked = false;
    // }
}
