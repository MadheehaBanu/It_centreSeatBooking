document.addEventListener('DOMContentLoaded', function () {

    // ── Toast helper ──────────────────────────────────────────────
    function showToast(msg, type = '') {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.className = 'toast' + (type ? ' toast-' + type : '');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3500);
    }

    // ── QR code generation ────────────────────────────────────────
    const qrcodeDiv = document.getElementById('qrcode');
    if (qrcodeDiv) {
        Promise.all([
            fetch('includes/my_bookings.php').then(r => r.json()),
            fetch('includes/get_user_email.php').then(r => r.json())
        ])
        .then(([bookings, user]) => {
            if (!bookings.length) return;
            const b = bookings[0];
            if (b.status !== 'approved') return;

            const qrText = `UniSpace\nEmail: ${user.email}\nSeat: ${b.seat_number}\nBooked: ${b.booking_time}`;

            if (typeof QRCode !== 'undefined') {
                new QRCode(qrcodeDiv, {
                    text: qrText,
                    width: 200,
                    height: 200,
                    colorDark: '#1e1e2e',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });
            } else {
                qrcodeDiv.innerHTML = '<p style="color:#6c757d;font-size:0.875rem;">QR unavailable</p>';
            }
        })
        .catch(() => {
            if (qrcodeDiv) qrcodeDiv.innerHTML = '<p style="color:#6c757d;font-size:0.875rem;">QR unavailable</p>';
        });
    }

    // ── Cancel booking ────────────────────────────────────────────
    const cancelBtn = document.getElementById('cancel-booking-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            if (!confirm('Cancel your booking? This cannot be undone.')) return;

            cancelBtn.disabled = true;
            cancelBtn.textContent = 'Cancelling…';

            fetch('includes/cancel_booking.php', { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('Booking cancelled.', 'success');
                        setTimeout(() => { window.location.href = 'booking.php'; }, 900);
                    } else {
                        showToast(data.message || 'Failed to cancel.', 'error');
                        cancelBtn.disabled = false;
                        cancelBtn.textContent = 'Cancel Booking';
                    }
                })
                .catch(() => {
                    showToast('An error occurred. Please try again.', 'error');
                    cancelBtn.disabled = false;
                    cancelBtn.textContent = 'Cancel Booking';
                });
        });
    }

    // ── Auto-refresh every 30s if status is pending ───────────────
    const isPending = document.querySelector('.status-banner-pending');
    if (isPending) {
        setTimeout(() => location.reload(), 30000);
    }
});
