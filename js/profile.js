document.addEventListener('DOMContentLoaded', function() {
    const backBtn = document.getElementById('back-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    
    // Back button functionality
    backBtn.addEventListener('click', function() {
        // Check if user has existing booking to determine where to go back
        fetch('includes/my_bookings.php')
            .then(response => response.json())
            .then(bookings => {
                if (bookings.length > 0) {
                    window.location.href = 'status.php';
                } else {
                    window.location.href = 'booking.php';
                }
            })
            .catch(error => {
                console.error('Error checking bookings:', error);
                // Default to booking page if error
                window.location.href = 'booking.php';
            });
    });
    
    // Cancel button functionality (logout and go to login)
    cancelBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to logout and return to the login page?')) {
            fetch('includes/logout.php', {
                method: 'POST'
            })
            .then(() => {
                window.location.href = 'index.php';
            })
            .catch(error => {
                console.error('Error logging out:', error);
                // Redirect anyway
                window.location.href = 'index.php';
            });
        }
    });
});
