document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin panel
    console.log('Admin panel loaded');
});

function showAdminInfo() {
    alert('Admin User\nEmail: admin@gmail.com\nRole: Administrator\n\nClick "Close" to logout and return to login page.');
}

function updateBookingStatus(bookingId, status) {
    if (confirm(`Are you sure you want to ${status} this booking?`)) {
        fetch('includes/admin_update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                booking_id: bookingId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the status in the table
                const row = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
                const statusCell = row.querySelector('.status-cell');
                statusCell.innerHTML = `<span class="status-badge status-${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
                
                // Show success message
                alert(`Booking ${status} successfully!`);
            } else {
                alert('Error updating booking status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the booking status.');
        });
    }
}

function editBooking(bookingId) {
    // Get current booking data from the table row
    const row = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
    const seatNumber = row.querySelector('.seat-number').textContent;
    const bookingTimeText = row.querySelector('.booking-time').textContent;
    
    // Parse the booking time and convert to datetime-local format
    const bookingDate = new Date(bookingTimeText);
    const formattedDate = bookingDate.toISOString().slice(0, 16);
    
    // Populate the edit form
    document.getElementById('edit-booking-id').value = bookingId;
    document.getElementById('edit-seat').value = seatNumber;
    document.getElementById('edit-time').value = formattedDate;
    
    // Show the modal
    document.getElementById('edit-modal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

function editUser(userId, firstName, lastName, registration, email, phone) {
    document.getElementById('edit-user-id').value = userId;
    document.getElementById('edit-first-name').value = firstName;
    document.getElementById('edit-last-name').value = lastName;
    document.getElementById('edit-registration').value = registration;
    document.getElementById('edit-user-email').value = email;
    document.getElementById('edit-phone').value = phone;
    
    document.getElementById('edit-user-modal').style.display = 'flex';
}

function closeEditUserModal() {
    document.getElementById('edit-user-modal').style.display = 'none';
}

function closeAdmin() {
    if (confirm('Are you sure you want to close the admin panel and return to login?')) {
        fetch('includes/logout.php', {
            method: 'POST'
        })
        .then(() => {
            window.location.href = 'index.php';
        })
        .catch(error => {
            console.error('Error logging out:', error);
            window.location.href = 'index.php';
        });
    }
}

// Handle edit form submission
document.getElementById('edit-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const bookingId = document.getElementById('edit-booking-id').value;
    const seatNumber = document.getElementById('edit-seat').value;
    const bookingTime = document.getElementById('edit-time').value;
    
    fetch('includes/admin_update_booking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            booking_id: bookingId,
            seat_number: seatNumber,
            booking_time: bookingTime
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Booking updated successfully!');
            location.reload(); // Reload to show updated data
        } else {
            alert('Error updating booking: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the booking.');
    });
});

// Handle edit user form submission
document.getElementById('edit-user-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('user_id', document.getElementById('edit-user-id').value);
    formData.append('first_name', document.getElementById('edit-first-name').value);
    formData.append('last_name', document.getElementById('edit-last-name').value);
    formData.append('registration_number', document.getElementById('edit-registration').value);
    formData.append('email', document.getElementById('edit-user-email').value);
    formData.append('phone_number', document.getElementById('edit-phone').value);
    
    fetch('includes/admin_update_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User details updated successfully!');
            location.reload();
        } else {
            alert('Error updating user: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating user details.');
    });
});

// Handle bulk actions
document.querySelector('.btn-approval').addEventListener('click', function() {
    if (confirm('Approve all pending bookings?')) {
        fetch('includes/admin_bulk_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'approve_all'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('All pending bookings approved!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
});

document.querySelector('.btn-reject-all').addEventListener('click', function() {
    if (confirm('Reject all pending bookings? This action cannot be undone.')) {
        fetch('includes/admin_bulk_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'reject_all'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('All pending bookings rejected!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
});

document.querySelector('.btn-export').addEventListener('click', function() {
    window.open('includes/admin_export.php', '_blank');
});

// Close modal when clicking outside
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Close user edit modal when clicking outside
document.getElementById('edit-user-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditUserModal();
    }
});
