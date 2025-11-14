# UniSpace - IT Workspace Reservation System

## Database Setup Instructions

### Prerequisites
- XAMPP installed with Apache and MySQL services running
- Access to phpMyAdmin or MySQL command line

### Setting Up the Database

1. **Start XAMPP Services**
   - Start Apache and MySQL services from the XAMPP Control Panel

2. **Import the Database**
   - Option 1: Using phpMyAdmin
     - Open your browser and navigate to `http://localhost/phpmyadmin`
     - Click on the "Import" tab at the top of the page
     - Click "Choose File" and select the `database.sql` file from the project directory
     - Click "Go" at the bottom of the page to import the database

   - Option 2: Using MySQL Command Line
     - Open a terminal or command prompt
     - Navigate to your XAMPP MySQL bin directory
     - Run the following command:
       ```
       mysql -u root -p < /path/to/UniSpace/database.sql
       ```
     - If prompted for a password, press Enter (default XAMPP MySQL has no password)

3. **Verify Database Setup**
   - In phpMyAdmin, you should now see the `unispace_db` database
   - The database should contain two tables: `users` and `bookings`
   - The `users` table should contain sample user data

### Database Structure

#### Users Table
- `id`: Auto-incremented unique identifier
- `first_name`: User's first name
- `last_name`: User's last name
- `registration_number`: Unique registration number (e.g., student ID)
- `email`: Unique email address
- `phone_number`: Contact phone number
- `password`: Hashed password
- `created_at`: Timestamp of account creation

#### Bookings Table
- `id`: Auto-incremented unique identifier
- `user_id`: Foreign key referencing the users table
- `seat_number`: The workspace seat number being reserved
- `booking_time`: Timestamp of when the booking was made

### Sample Data

The database includes sample user data with the following credentials:

1. **John Doe**
   - Email: john.doe@example.com
   - Registration: REG001

2. **Jane Smith**
   - Email: jane.smith@example.com
   - Registration: REG002

3. **Alice Johnson**
   - Email: alice.johnson@example.com
   - Registration: REG003

### Troubleshooting

- If you encounter a connection error, ensure that:
  - XAMPP MySQL service is running
  - Database credentials in `includes/db.php` match your MySQL setup
  - The `unispace_db` database exists

- If you encounter issues with the signup or login process:
  - Check that the `users` table structure matches the expected fields
  - Verify that the database connection in `includes/db.php` is correct