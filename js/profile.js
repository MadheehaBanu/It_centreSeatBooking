document.addEventListener('DOMContentLoaded', function () {

    // Back — go to status if booking exists, otherwise booking page
    document.getElementById('back-btn').addEventListener('click', function () {
        fetch('includes/my_bookings.php')
            .then(r => r.json())
            .then(bookings => {
                window.location.href = bookings.length > 0 ? 'status.php' : 'booking.php';
            })
            .catch(() => {
                window.location.href = 'booking.php';
            });
    });

    // Logout
    document.getElementById('logout-btn').addEventListener('click', function () {
        if (confirm('Are you sure you want to logout?')) {
            fetch('includes/logout.php', { method: 'POST' })
                .finally(() => { window.location.href = 'index.php'; });
        }
    });
});
