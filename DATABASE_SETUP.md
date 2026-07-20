# Device Inventory Management System - Setup Guide

## Quick Start

### Option 1: Use Local Database (Recommended)
The application stores all data locally in your browser using SQLite. No server setup required!

1. Open `inventory.html` in any modern web browser
2. Start adding devices immediately
3. Your data is automatically saved to your browser's local storage

### Option 2: Create a Pre-populated Database

Since binary SQLite files cannot be created through text-based commits, follow these steps to create a default database:

#### Step A: Create Using Python (Recommended)
```bash
# Install sqlite3 (usually comes with Python)
python3 -c "
import sqlite3
from datetime import datetime

# Create database
conn = sqlite3.connect('inventory.db')
cursor = conn.cursor()

# Create table
cursor.execute('''
    CREATE TABLE inventory (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        device_name TEXT NOT NULL,
        device_serial_number TEXT UNIQUE,
        device_model TEXT,
        location TEXT,
        assigned_user TEXT,
        device_phone_number TEXT,
        carrier TEXT,
        department TEXT,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
''')

# Insert sample data
sample_data = [
    ('iPhone 14 Pro', 'A2B3C4D5E6F7', 'iPhone14,3', 'New York Office', 'John Smith', '+1-555-0101', 'Verizon', 'Sales', 'Primary device for client calls'),
    ('Samsung Galaxy S23', 'R38M900ABC12', 'SM-S911B', 'Los Angeles Office', 'Sarah Johnson', '+1-555-0102', 'AT&T', 'Marketing', 'Android testing device'),
    ('iPad Air 5', 'F1G2H3I4J5K6', 'iPad Air (5th generation)', 'Chicago Office', 'Michael Chen', 'N/A', 'Wi-Fi Only', 'Design', 'Used for design mockups'),
    ('MacBook Pro 16\"', 'Z7X8W9V0U1T2', 'MacBookPro18,1', 'Remote', 'Emily Rodriguez', 'N/A', 'N/A', 'Engineering', 'Development machine with M2 Max'),
    ('Google Pixel 7 Pro', 'Q4R5S6T7U8V9', 'Pixel 7 Pro', 'Denver Office', 'David Martinez', '+1-555-0103', 'T-Mobile', 'IT Support', 'Test device for Android compatibility')
]

for device in sample_data:
    cursor.execute('''
        INSERT INTO inventory (device_name, device_serial_number, device_model, location, assigned_user, device_phone_number, carrier, department, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ''', device)

conn.commit()
conn.close()
print('✅ inventory.db created successfully with 5 sample devices!')
"
```

#### Step B: Create Using SQLite CLI (Linux/Mac)
```bash
sqlite3 inventory.db << 'EOF'
CREATE TABLE inventory (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    device_name TEXT NOT NULL,
    device_serial_number TEXT UNIQUE,
    device_model TEXT,
    location TEXT,
    assigned_user TEXT,
    device_phone_number TEXT,
    carrier TEXT,
    department TEXT,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO inventory (device_name, device_serial_number, device_model, location, assigned_user, device_phone_number, carrier, department, notes) VALUES
('iPhone 14 Pro', 'A2B3C4D5E6F7', 'iPhone14,3', 'New York Office', 'John Smith', '+1-555-0101', 'Verizon', 'Sales', 'Primary device for client calls'),
('Samsung Galaxy S23', 'R38M900ABC12', 'SM-S911B', 'Los Angeles Office', 'Sarah Johnson', '+1-555-0102', 'AT&T', 'Marketing', 'Android testing device'),
('iPad Air 5', 'F1G2H3I4J5K6', 'iPad Air (5th generation)', 'Chicago Office', 'Michael Chen', 'N/A', 'Wi-Fi Only', 'Design', 'Used for design mockups'),
('MacBook Pro 16"', 'Z7X8W9V0U1T2', 'MacBookPro18,1', 'Remote', 'Emily Rodriguez', 'N/A', 'N/A', 'Engineering', 'Development machine with M2 Max'),
('Google Pixel 7 Pro', 'Q4R5S6T7U8V9', 'Pixel 7 Pro', 'Denver Office', 'David Martinez', '+1-555-0103', 'T-Mobile', 'IT Support', 'Test device for Android compatibility');
EOF
```

#### Step C: Using Online SQLite Tools
1. Visit: https://sqliteonline.com/
2. Copy and paste the SQL schema below
3. Click "Export" and save as `inventory.db`

## Database Schema

### Table: `inventory`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INTEGER | PRIMARY KEY, AUTO_INCREMENT | Unique record identifier |
| device_name | TEXT | NOT NULL | Name/model of the device |
| device_serial_number | TEXT | UNIQUE | Serial number (must be unique) |
| device_model | TEXT | | Model identifier |
| location | TEXT | | Physical location |
| assigned_user | TEXT | | User assigned to device |
| device_phone_number | TEXT | | Phone number if applicable |
| carrier | TEXT | | Carrier/Provider name |
| department | TEXT | | Department assignment |
| notes | TEXT | | Additional notes |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Record creation timestamp |
| updated_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Record update timestamp |

## SQL to Create Empty Database

```sql
CREATE TABLE inventory (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    device_name TEXT NOT NULL,
    device_serial_number TEXT UNIQUE,
    device_model TEXT,
    location TEXT,
    assigned_user TEXT,
    device_phone_number TEXT,
    carrier TEXT,
    department TEXT,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## SQL to Insert Sample Data

```sql
INSERT INTO inventory (device_name, device_serial_number, device_model, location, assigned_user, device_phone_number, carrier, department, notes) VALUES
('iPhone 14 Pro', 'A2B3C4D5E6F7', 'iPhone14,3', 'New York Office', 'John Smith', '+1-555-0101', 'Verizon', 'Sales', 'Primary device for client calls'),
('Samsung Galaxy S23', 'R38M900ABC12', 'SM-S911B', 'Los Angeles Office', 'Sarah Johnson', '+1-555-0102', 'AT&T', 'Marketing', 'Android testing device'),
('iPad Air 5', 'F1G2H3I4J5K6', 'iPad Air (5th generation)', 'Chicago Office', 'Michael Chen', 'N/A', 'Wi-Fi Only', 'Design', 'Used for design mockups and presentations'),
('MacBook Pro 16"', 'Z7X8W9V0U1T2', 'MacBookPro18,1', 'Remote (Employee Home)', 'Emily Rodriguez', 'N/A', 'N/A', 'Engineering', 'Development machine with M2 Max chip'),
('Google Pixel 7 Pro', 'Q4R5S6T7U8V9', 'Pixel 7 Pro', 'Denver Office', 'David Martinez', '+1-555-0103', 'T-Mobile', 'IT Support', 'Test device for Android compatibility');
```

## How to Use the Application

### Adding Records
1. Fill in device information in the form
2. Click "💾 Save Record"
3. Record is automatically saved to your local database

### Editing Records
1. Find the device in the table
2. Click "Edit" button
3. Modify the information
4. Click "💾 Save Record"

### Deleting Records
1. Click "Delete" button next to the device
2. Confirm the deletion

### Searching
1. Use the search box at the top
2. Search works across all fields (name, serial, location, user, etc.)

### Bulk Import CSV
1. Prepare a CSV file with headers: `device_name, device_serial_number, device_model, location, assigned_user, device_phone_number, carrier, department, notes`
2. Click "📥 Import CSV"
3. Select your file
4. Records are added to the database

### Export Data
1. **Export as CSV**: Click "📥 Export as CSV" to get all records as a spreadsheet
2. **Download Database**: Click "⬇️ Download Database" to download the complete SQLite database file
3. **Import Database**: Click "⬆️ Import Database" to restore from a saved database file

## Data Persistence

- **Local Storage**: Data is stored in your browser's localStorage (5-10MB limit per domain)
- **Export Regularly**: Download your database file regularly as a backup
- **Cross-Device Sync**: Export from one device and import on another to sync data

## Browser Compatibility

- Chrome/Edge: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support
- Opera: ✅ Full support

## No Server Required

This application is completely client-side:
- ✅ No backend server needed
- ✅ No database server needed
- ✅ No internet connection required (after initial load)
- ✅ All data stays on your device
- ✅ Complete privacy and security
