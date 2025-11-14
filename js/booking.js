function fetchMyBookings() {
    fetch('includes/my_bookings.php')
        .then(response => response.json())
        .then(bookings => {
            const myBookingsDiv = document.getElementById('my-bookings');
            if (bookings.length === 0) {
                myBookingsDiv.textContent = 'You have no bookings yet.';
            } else {
                const seatList = bookings.map(b => b.seat_number).join(', ');
                myBookingsDiv.innerHTML = `<strong>Your booked seat(s):</strong> ${seatList}`;
            }
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const seats = document.querySelectorAll('.seat');
    const bookButton = document.querySelector('.btn-book');
    const cancelButton = document.querySelector('.btn-cancel');
    let selectedSeats = [];

    // Check if user already has a booking
    fetch('includes/my_bookings.php')
        .then(response => response.json())
        .then(bookings => {
            if (bookings.length > 0) {
                // User already has a booking, redirect to status page
                window.location.href = 'status.php';
                return;
            }
            
            // Continue with normal booking page functionality
            fetchMyBookings();
            
            // Fetch and update seat status on page load
            fetch('includes/get_bookings.php')
                .then(response => response.json())
                .then(bookedSeats => {
                    seats.forEach(seat => {
                        const seatNumber = seat.dataset.seatNumber;
                        if (bookedSeats.includes(parseInt(seatNumber))) {
                            seat.classList.add('booked');
                        }
                    });
                });
        })
        .catch(error => {
            console.error('Error checking user bookings:', error);
        });

    seats.forEach(seat => {
        seat.addEventListener('click', () => {
            if (!seat.classList.contains('booked')) {
                const seatNumber = parseInt(seat.dataset.seatNumber);
                
                // If this seat is already selected, deselect it
                if (seat.classList.contains('selected')) {
                    seat.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                } else {
                    // Deselect all other seats (only one seat allowed)
                    seats.forEach(s => s.classList.remove('selected'));
                    selectedSeats = [];
                    
                    // Select this seat
                    seat.classList.add('selected');
                    selectedSeats.push(seatNumber);
                }
            }
        });
    });

    bookButton.addEventListener('click', () => {
        if (selectedSeats.length > 0) {
            fetch('includes/book_seats.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ seats: selectedSeats })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to status page instead of showing modal
                    window.location.href = 'status.php';
                } else {
                    alert('Booking failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while booking. Please try again.');
            });
        } else {
            alert('Please select a seat.');
        }
    });

    cancelButton.addEventListener('click', () => {
        selectedSeats = [];
        seats.forEach(seat => {
            seat.classList.remove('selected');
        });
    });
}); 