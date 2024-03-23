var inactivityTimeout;

function resetInactivityTimer() {
    console.log("Resetting inactivity timer...");
    clearTimeout(inactivityTimeout);
    inactivityTimeout = setTimeout(logout, 15000); // 15 seconds timeout
}

// Reset the timer on user activity
document.addEventListener("mousemove", resetInactivityTimer);
document.addEventListener("keypress", resetInactivityTimer);
document.addEventListener("mousedown", resetInactivityTimer);
document.addEventListener("scroll", resetInactivityTimer);
document.addEventListener("input", resetInactivityTimer);

console.log('Inactivity timer initialized.');

function logout() {
    // Perform logout actions here, e.g., clear session, redirect to login page
    alert("You have been logged out due to inactivity.");
    // Example redirect to login page
    window.location.href = "logout.php?logout_reason=inactivity";
}
