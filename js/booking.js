document.addEventListener('DOMContentLoaded', function () {
    const seats        = document.querySelectorAll('.seat');
    const btnBook      = document.getElementById('btn-book');
    const btnCancel    = document.getElementById('btn-cancel');
    const selectedLabel = document.getElementById('selected-label');
    let selectedSeat   = null;

    // ── Toast helper ──────────────────────────────────────────────
    function showToast(msg, type = '') {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.className = 'toast' + (type ? ' toast-' + type : '');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // ── Redirect if user already has a booking ────────────────────
    fetch('includes/my_bookings.php')
        .then(r => r.json())
        .then(bookings => {
            if (bookings.length > 0) {
                window.location.href = 'status.php';
                return;
            }
            loadBookedSeats();
        })
        .catch(() => loadBookedSeats());

    // ── Load booked seats from server ─────────────────────────────
    function loadBookedSeats() {
        fetch('includes/get_bookings.php')
            .then(r => r.json())
            .then(bookedSeats => {
                seats.forEach(seat => {
                    if (bookedSeats.includes(parseInt(seat.dataset.seatNumber))) {
                        seat.classList.add('booked');
                        seat.disabled = true;
                        seat.setAttribute('aria-disabled', 'true');
                    }
                });
            })
            .catch(() => showToast('Could not load seat availability.', 'error'));
    }

    // ── Seat selection ────────────────────────────────────────────
    seats.forEach(seat => {
        seat.addEventListener('click', () => {
            if (seat.classList.contains('booked')) return;

            const num = parseInt(seat.dataset.seatNumber);

            if (seat.classList.contains('selected')) {
                seat.classList.remove('selected');
                selectedSeat = null;
            } else {
                seats.forEach(s => s.classList.remove('selected'));
                seat.classList.add('selected');
                selectedSeat = num;
            }

            updateUI();
        });
    });

    function updateUI() {
        if (selectedSeat) {
            selectedLabel.textContent = 'Seat ' + selectedSeat;
            btnBook.disabled = false;
        } else {
            selectedLabel.textContent = '';
            btnBook.disabled = true;
        }
    }

    // ── Book Now ──────────────────────────────────────────────────
    btnBook.addEventListener('click', () => {
        if (!selectedSeat || btnBook.classList.contains('loading')) return;

        btnBook.classList.add('loading');
        btnBook.textContent = 'Booking…';
        btnBook.disabled = true;

        fetch('includes/book_seats.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ seats: [selectedSeat] })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Seat ' + selectedSeat + ' booked!', 'success');
                setTimeout(() => { window.location.href = 'status.php'; }, 800);
            } else {
                showToast(data.message || 'Booking failed.', 'error');
                btnBook.classList.remove('loading');
                btnBook.textContent = 'Book Now';
                btnBook.disabled = false;
            }
        })
        .catch(() => {
            showToast('An error occurred. Please try again.', 'error');
            btnBook.classList.remove('loading');
            btnBook.textContent = 'Book Now';
            btnBook.disabled = false;
        });
    });

    // ── Clear selection ───────────────────────────────────────────
    btnCancel.addEventListener('click', () => {
        seats.forEach(s => s.classList.remove('selected'));
        selectedSeat = null;
        updateUI();
    });
});
