<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit();
}

// Get all bookings with user details
require 'includes/db.php';

$sql = "SELECT b.id, b.seat_number, b.booking_time, b.status, 
               u.id as user_id, u.first_name, u.last_name, u.email, u.registration_number, u.phone_number
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.booking_time DESC";
$result = $conn->query($sql);
$bookings = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - UniSpace</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="main-container">
        <div class="admin-header">
            <div class="header-left">
                <img src="images/logo-main.png" alt="Logo" class="logo-header">
                <span class="unispace-title">UniSpace Admin</span>
            </div>
            <div class="profile-icon" onclick="showAdminInfo()"></div>
        </div>
        
        <div class="admin-content">
            <div class="content-area">
                <h2>All Booking Details</h2>
                <div class="bookings-table-container">
                    <table class="bookings-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registration</th>
                                <th>Phone</th>
                                <th></th>Seat</th>
                                <th>Booking Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="9" class="no-bookings">No bookings found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr data-booking-id="<?php echo $booking['id']; ?>" data-user-id="<?php echo $booking['user_id']; ?>">
                                        <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                        <td class="user-name"><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                                        <td class="user-email"><?php echo htmlspecialchars($booking['email']); ?></td>
                                        <td class="user-registration"><?php echo htmlspecialchars($booking['registration_number']); ?></td>
                                        <td class="user-phone"><?php echo htmlspecialchars($booking['phone_number']); ?></td>
                                        <td class="seat-number"><?php echo htmlspecialchars($booking['seat_number']); ?></td>
                                        <td class="booking-time"><?php echo date('M d, Y - h:i A', strtotime($booking['booking_time'])); ?></td>
                                        <td class="status-cell">
                                            <span class="status-badge status-<?php echo strtolower($booking['status'] ?? 'pending'); ?>">
                                                <?php echo ucfirst($booking['status'] ?? 'Pending'); ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <button class="btn-action btn-approve" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'approved')">Approve</button>
                                            <button class="btn-action btn-reject" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'rejected')">Reject</button>
                                            <button class="btn-action btn-edit" onclick="editBooking(<?php echo $booking['id']; ?>)">Edit Booking</button>
                                            <button class="btn-action btn-edit-user" onclick="editUser(<?php echo $booking['user_id']; ?>)">Edit User</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="action-buttons">
                <button class="btn-approval">Approval</button>
                <button class="btn-update">Edit</button>
                <button class="btn-reject-all">Reject</button>
                <button class="btn-export">Export</button>
                <button class="btn-close" onclick="closeAdmin()">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div id="edit-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h3>Edit Booking Details</h3>
            <form id="edit-form">
                <input type="hidden" id="edit-booking-id">
                <div class="form-group">
                    <label for="edit-seat">Seat Number:</label>
                    <input type="number" id="edit-seat" min="1" max="40" required>
                </div>
                <div class="form-group">
                    <label for="edit-time">Booking Time:</label>
                    <input type="datetime-local" id="edit-time" required>
                </div>
                <div class="form-actions">
                    <button type="submit">Update</button>
                    <button type="button" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="edit-user-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditUserModal()">&times;</span>
            <h3>Edit User Details</h3>
            <form id="edit-user-form">
                <input type="hidden" id="edit-user-id">
                <div class="form-group">
                    <label for="edit-first-name">First Name:</label>
                    <input type="text" id="edit-first-name" required>
                </div>
                <div class="form-group">
                    <label for="edit-last-name">Last Name:</label>
                    <input type="text" id="edit-last-name" required>
                </div>
                <div class="form-group">
                    <label for="edit-registration">Registration Number:</label>
                    <input type="text" id="edit-registration" required>
                </div>
                <div class="form-group">
                    <label for="edit-user-email">Email:</label>
                    <input type="email" id="edit-user-email" required>
                </div>
                <div class="form-group">
                    <label for="edit-phone">Phone Number:</label>
                    <input type="tel" id="edit-phone" required>
                </div>
                <div class="form-actions">
                    <button type="submit">Update User</button>
                    <button type="button" onclick="closeEditUserModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
