# Online Donation System

A web-based donation platform where users can browse causes (Education, Health, Food, Emergency Relief), donate through a simulated payment form, and admins can track all donations. Built as a Final Year Project.

## Tech Stack
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP (form processing, validation)
- **Database:** MySQL

## Team & Modules
| Member | Module |
|---|---|---|
| Taiba Asif | Category Selection (homepage, cause cards) 
| Eiman Asmat| Donation Form (form + client/server validation) 
| Rabia Noor | Database & Donor Management (schema, connection, queries) 
| Areeba Noor| Admin Report (dashboard, donation summaries)

## Setup Instructions (for every team member)

### 1. Install XAMPP
Download from apachefriends.org if not already installed.

### 2. Clone the repository into htdocs
```bash
cd C:\xampp\htdocs
git clone https://github.com/yourusername/online-donation-system.git
```
Make sure the folder ends up at exactly `C:\xampp\htdocs\online-donation-system\` — not nested inside another folder — so URLs match for everyone.

### 3. Start Apache and MySQL
Open XAMPP Control Panel and click **Start** on both.

### 4. Import the database
1. Go to `http://localhost/phpmyadmin`
2. Click **Import**
3. Choose `database/donation_system.sql` from the cloned folder
4. Click **Go**

This creates the `donation_system` database with 3 tables (`causes`, `donors`, `donations`) and seeds the 4 causes.

### 5. Run the project
Open in your browser:
```
http://localhost/online-donation-system/index.php
```

## Database Connection
Default XAMPP credentials are already set in `php/db_connect.php`:
- Host: `localhost`
- User: `root`
- Password: *(empty)*
- Database: `donation_system`

No changes needed unless your local MySQL setup differs from XAMPP defaults.
