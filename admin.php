<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit();
}

require 'includes/db.php';

$sql = "SELECT b.id, b.seat_number, b.booking_time, b.status,
               u.id as user_id, u.first_name, u.last_name, u.email, u.registration_number, u.phone_number
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        ORDER BY b.booking_time DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total    = count($bookings);
$pending  = count(array_filter($bookings, fn($b) => ($b['status'] ?? 'pending') === 'pending'));
$approved = count(array_filter($bookings, fn($b) => ($b['status'] ?? '') === 'approved'));
$rejected = count(array_filter($bookings, fn($b) => ($b['status'] ?? '') === 'rejected'));
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

        <!-- Header -->
        <div class="admin-header">
            <div class="header-left">
                <img src="images/logo-main.png" alt="Logo" class="logo-header">
                <span class="unispace-title">UniSpace Admin</span>
            </div>
            <button class="btn-logout" onclick="closeAdmin()">Logout</button>
        </div>

        <div class="admin-content">

            <!-- Stats bar -->
            <div class="stats-bar">
                <div class="stat-card">
                    <span class="stat-number"><?php echo $total; ?></span>
                    <span class="stat-label">Total</span>
                </div>
                <div class="stat-card stat-pending">
                    <span class="stat-number"><?php echo $pending; ?></span>
                    <span class="stat-label">Pending</span>
                </div>
                <div class="stat-card stat-approved">
                    <span class="stat-number"><?php echo $approved; ?></span>
                    <span class="stat-label">Approved</span>
                </div>
                <div class="stat-card stat-rejected">
                    <span class="stat-number"><?php echo $rejected; ?></span>
                    <span class="stat-label">Rejected</span>
                </div>
            </div>

            <div class="content-area">
                <div class="table-toolbar">
                    <h2>All Bookings</h2>
                    <div class="toolbar-controls">
                        <input type="text" id="search-input" placeholder="Search by name, email or reg. no…" class="search-input">
                        <select id="status-filter" class="filter-select">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="bookings-table-container">
                    <table class="bookings-table" id="bookings-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registration</th>
                                <th>Phone</th>
                                <th>Seat</th>
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
                                    <tr data-booking-id="<?php echo $booking['id']; ?>"
                                        data-user-id="<?php echo $booking['user_id']; ?>"
                                        data-name="<?php echo strtolower(htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name'])); ?>"
                                        data-email="<?php echo strtolower(htmlspecialchars($booking['email'])); ?>"
                                        data-reg="<?php echo strtolower(htmlspecialchars($booking['registration_number'])); ?>"
                                        data-status="<?php echo strtolower($booking['status'] ?? 'pending'); ?>">
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
                                            <button class="btn-action btn-approve" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'approved')" title="Approve">✓</button>
                                            <button class="btn-action btn-reject"  onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'rejected')"  title="Reject">✕</button>
                                            <button class="btn-action btn-edit"
                                                onclick="editBooking(<?php echo $booking['id']; ?>)"
                                                title="Edit Booking">✎ Booking</button>
                                            <button class="btn-action btn-edit-user"
                                                onclick="editUser(
                                                    <?php echo $booking['user_id']; ?>,
                                                    '<?php echo addslashes($booking['first_name']); ?>',
                                                    '<?php echo addslashes($booking['last_name']); ?>',
                                                    '<?php echo addslashes($booking['registration_number']); ?>',
                                                    '<?php echo addslashes($booking['email']); ?>',
                                                    '<?php echo addslashes($booking['phone_number']); ?>'
                                                )" title="Edit User">✎ User</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div id="no-results" class="no-bookings" style="display:none;">No bookings match your search.</div>
                </div>
            </div>

            <!-- Bulk action bar -->
            <div class="action-buttons">
                <button class="btn-approval">✓ Approve All Pending</button>
                <button class="btn-reject-all">✕ Reject All Pending</button>
                <button class="btn-export">↓ Export CSV</button>
            </div>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div id="edit-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h3>Edit Booking</h3>
            <form id="edit-form">
                <input type="hidden" id="edit-booking-id">
                <div class="form-group">
                    <label for="edit-seat">Seat Number</label>
                    <input type="number" id="edit-seat" min="1" max="40" required>
                </div>
                <div class="form-group">
                    <label for="edit-time">Booking Time</label>
                    <input type="datetime-local" id="edit-time" required>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeEditModal()">Cancel</button>
                    <button type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="edit-user-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditUserModal()">&times;</span>
            <h3>Edit User</h3>
            <form id="edit-user-form">
                <input type="hidden" id="edit-user-id">
                <div class="form-group">
                    <label for="edit-first-name">First Name</label>
                    <input type="text" id="edit-first-name" required>
                </div>
                <div class="form-group">
                    <label for="edit-last-name">Last Name</label>
                    <input type="text" id="edit-last-name" required>
                </div>
                <div class="form-group">
                    <label for="edit-registration">Registration Number</label>
                    <input type="text" id="edit-registration" required>
                </div>
                <div class="form-group">
                    <label for="edit-user-email">Email</label>
                    <input type="email" id="edit-user-email" required>
                </div>
                <div class="form-group">
                    <label for="edit-phone">Phone Number</label>
                    <input type="tel" id="edit-phone" required>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeEditUserModal()">Cancel</button>
                    <button type="submit">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
