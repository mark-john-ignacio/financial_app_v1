var inactivityTimeout;

function resetInactivityTimer() {
    clearTimeout(inactivityTimeout);
    inactivityTimeout = setTimeout(logout, 300000); // 1 minute timeout
}

// Reset the timer on user activity
document.addEventListener("mousemove", resetInactivityTimer);
document.addEventListener("keypress", resetInactivityTimer);
// Add more events as needed (e.g., touch events for mobile devices)

function logout() {
    // Perform logout actions here, e.g., clear session, redirect to login page
    alert("You have been logged out due to inactivity.");
    // Example redirect to login page
    window.location.href = "logout.php";
}
