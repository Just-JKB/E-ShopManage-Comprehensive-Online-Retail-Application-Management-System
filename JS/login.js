let selectedRole = '';

function selectRole(role) {
    selectedRole = role;
    document.getElementById('selected-role').value = role;

    document.getElementById('user-icon').classList.remove('selected');
    document.getElementById('admin-icon').classList.remove('selected');

    document.getElementById(role + '-icon').classList.add('selected');

    const message = document.getElementById('login-message');
    message.textContent = (role === 'admin') ? "Admin Login" : "User Login";
}

function validateForm() {
    if (!selectedRole) {
        alert("Please select a role before logging in.");
        return false;
    }
    return true;
}
