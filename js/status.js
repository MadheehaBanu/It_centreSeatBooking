document.addEventListener('DOMContentLoaded', function() {
    const closeBtn = document.getElementById('close-btn');
    const cancelBookingBtn = document.getElementById('cancel-booking-btn');
    const qrcodeDiv = document.getElementById('qrcode');
    
    // Generate QR code if booking exists and is approved
    if (qrcodeDiv) {
        fetch('includes/my_bookings.php')
            .then(response => response.json())
            .then(bookings => {
                if (bookings.length > 0) {
                    const booking = bookings[0];
                    // Only generate QR code for approved bookings
                    if (booking.status === 'approved') {
                        fetch('includes/get_user_email.php')
                            .then(response => response.json())
                            .then(user => {
                                const qrText = `UniSpace Booking\nEmail: ${user.email}\nSeat: ${booking.seat_number}\nTime: ${booking.booking_time}`;
                                
                                // Check if QRCode library is available
                                if (typeof QRCode !== 'undefined') {
                                    new QRCode(qrcodeDiv, {
                                        text: qrText,
                                        width: 200,
                                        height: 200,
                                        colorDark: "#000000",
                                        colorLight: "#ffffff",
                                        correctLevel: QRCode.CorrectLevel.M
                                    });
                                } else {
                                    // Fallback if QRCode library is not available
                                    qrcodeDiv.innerHTML = '<div style="width: 200px; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #666; font-size: 14px; text-align: center;">QR Code<br>Generation<br>Unavailable</div>';
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching user email:', error);
                            });
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching bookings:', error);
            });
    }
    
    // Cancel booking functionality
    if (cancelBookingBtn) {
        cancelBookingBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel your booking? This action cannot be undone.')) {
                fetch('includes/cancel_booking.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Booking cancelled successfully!');
                        window.location.href = 'booking.php';
                    } else {
                        alert('Failed to cancel booking: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error cancelling booking:', error);
                    alert('An error occurred while cancelling the booking.');
                });
            }
        });
    }
    
    // Close button functionality
    closeBtn.addEventListener('click', function() {
        // Show confirmation dialog
        if (confirm('Are you sure you want to close and return to the login page?')) {
            // Log out the user and redirect to login
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
    
    // Auto-refresh booking status every 30 seconds
    setInterval(function() {
        fetch('includes/my_bookings.php')
            .then(response => response.json())
            .then(bookings => {
                // If user no longer has bookings, redirect to booking page
                if (bookings.length === 0) {
                    window.location.href = 'booking.php';
                }
            })
            .catch(error => {
                console.error('Error checking booking status:', error);
            });
    }, 30000);
});
