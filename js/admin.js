document.addEventListener('DOMContentLoaded', function () {

    // ── Live search + status filter ──────────────────────────────
    const searchInput  = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const noResults    = document.getElementById('no-results');

    function filterTable() {
        const query  = searchInput.value.toLowerCase().trim();
        const status = statusFilter.value.toLowerCase();
        const rows   = document.querySelectorAll('#bookings-table tbody tr[data-booking-id]');
        let visible  = 0;

        rows.forEach(row => {
            const matchSearch = !query ||
                row.dataset.name.includes(query) ||
                row.dataset.email.includes(query) ||
                row.dataset.reg.includes(query);
            const matchStatus = !status || row.dataset.status === status;

            const show = matchSearch && matchStatus;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        noResults.style.display = visible === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);

    // ── Bulk actions ─────────────────────────────────────────────
    document.querySelector('.btn-approval').addEventListener('click', function () {
        if (confirm('Approve all pending bookings?')) {
            bulkAction('approve_all');
        }
    });

    document.querySelector('.btn-reject-all').addEventListener('click', function () {
        if (confirm('Reject all pending bookings? This cannot be undone.')) {
            bulkAction('reject_all');
        }
    });

    document.querySelector('.btn-export').addEventListener('click', function () {
        window.open('includes/admin_export.php', '_blank');
    });

    // ── Modal outside-click close ─────────────────────────────────
    document.getElementById('edit-modal').addEventListener('click', function (e) {
        if (e.target === this) closeEditModal();
    });

    document.getElementById('edit-user-modal').addEventListener('click', function (e) {
        if (e.target === this) closeEditUserModal();
    });

    // ── Edit booking form ─────────────────────────────────────────
    document.getElementById('edit-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Saving…';

        fetch('includes/admin_update_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                booking_id:   document.getElementById('edit-booking-id').value,
                seat_number:  document.getElementById('edit-seat').value,
                booking_time: document.getElementById('edit-time').value
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
                btn.disabled = false;
                btn.textContent = 'Update';
            }
        })
        .catch(() => {
            alert('An error occurred. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Update';
        });
    });

    // ── Edit user form ────────────────────────────────────────────
    document.getElementById('edit-user-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Saving…';

        const formData = new FormData();
        formData.append('user_id',             document.getElementById('edit-user-id').value);
        formData.append('first_name',          document.getElementById('edit-first-name').value);
        formData.append('last_name',           document.getElementById('edit-last-name').value);
        formData.append('registration_number', document.getElementById('edit-registration').value);
        formData.append('email',               document.getElementById('edit-user-email').value);
        formData.append('phone_number',        document.getElementById('edit-phone').value);

        fetch('includes/admin_update_user.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
                btn.disabled = false;
                btn.textContent = 'Update User';
            }
        })
        .catch(() => {
            alert('An error occurred. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Update User';
        });
    });
});

// ── Status update ─────────────────────────────────────────────────
function updateBookingStatus(bookingId, status) {
    if (!confirm(`Are you sure you want to ${status} this booking?`)) return;

    fetch('includes/admin_update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: bookingId, status })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
            row.dataset.status = status;
            row.querySelector('.status-cell').innerHTML =
                `<span class="status-badge status-${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(() => alert('An error occurred. Please try again.'));
}

// ── Edit booking modal ────────────────────────────────────────────
function editBooking(bookingId) {
    const row  = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
    const seat = row.querySelector('.seat-number').textContent.trim();
    const time = new Date(row.querySelector('.booking-time').textContent.trim());
    const formatted = isNaN(time) ? '' : time.toISOString().slice(0, 16);

    document.getElementById('edit-booking-id').value = bookingId;
    document.getElementById('edit-seat').value        = seat;
    document.getElementById('edit-time').value        = formatted;
    document.getElementById('edit-modal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

// ── Edit user modal ───────────────────────────────────────────────
function editUser(userId, firstName, lastName, registration, email, phone) {
    document.getElementById('edit-user-id').value        = userId;
    document.getElementById('edit-first-name').value     = firstName;
    document.getElementById('edit-last-name').value      = lastName;
    document.getElementById('edit-registration').value   = registration;
    document.getElementById('edit-user-email').value     = email;
    document.getElementById('edit-phone').value          = phone;
    document.getElementById('edit-user-modal').style.display = 'flex';
}

function closeEditUserModal() {
    document.getElementById('edit-user-modal').style.display = 'none';
}

// ── Bulk action helper ────────────────────────────────────────────
function bulkAction(action) {
    fetch('includes/admin_bulk_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(() => alert('An error occurred. Please try again.'));
}

// ── Logout ────────────────────────────────────────────────────────
function closeAdmin() {
    if (confirm('Log out of the admin panel?')) {
        fetch('includes/logout.php', { method: 'POST' })
            .finally(() => { window.location.href = 'index.php'; });
    }
}
