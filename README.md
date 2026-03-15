# 🏠 Smart Land Management System

A full-stack real estate web application with separate User and Admin 
portals for managing, listing, searching, and tracking land properties 
for sale and rent.

🔗 Live Demo: https://realestate.zya.me/

---

## ✨ Features

### 👤 User Portal
- 🔐 Secure registration & login with password_hash() / password_verify()
- 🏠 List properties for Sale or Rent with image uploads
- 🔍 Search properties by unique Property ID
- 💙 Wishlist — save and manage favourite properties
- 📩 Message Admin directly with inbox & unread notifications
- 📋 View all available Sale and Rental properties

### 🛡️ Admin Portal
- 📊 Admin Dashboard with full property and user overview
- ✏️ Add, Edit, Delete properties (Sale + Rental)
- 👥 Manage registered users
- 💬 Reply to user messages
- 📈 Analytics dashboards for Sale and Rental properties separately
- 🗂️ Activity tracking

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP |
| Database | MySQL |
| Frontend | HTML5, CSS3, Bootstrap |
| Interactivity | jQuery, JavaScript |
| Auth | PHP Sessions + password_hash() |
| Server | Apache (XAMPP) |

---

## 🗄️ Database Structure

7 tables across separate modules:

- users — registered user accounts
- addproperty — sale property listings
- add_rent_property — rental property listings
- wishlist — user saved properties
- admin_messages — user-to-admin messaging
- admin_activity — admin action logs
- tbladmin — admin credentials

---

## 🚀 How to Run Locally

1. Clone the repository
   git clone https://github.com/Godofthunder09/Real-Estate.git

2. Import the database
   - Open phpMyAdmin
   - Create a database named: lrsdb
   - Import all .sql files from the /SQL File/ folder

3. Configure database connection
   - Open: admin/includes/dbconnection.php
   - Set your MySQL username and password

4. Start Apache & MySQL in XAMPP

5. Visit: http://localhost/landrecordsys/

---

## 📁 Project Structure

smart-land-management/
│
├── index.php              # Landing page
├── user/                  # User portal (login, dashboard, properties)
├── 1admin/                # Admin portal (dashboard, manage, analytics)
├── admin/includes/        # Database connection
├── assets/                # CSS, JS, images, fonts
├── SQL File/              # All database .sql files
└── README.md

---

## 🔐 Security Features

- password_hash() / password_verify() for user passwords
- Prepared statements to prevent SQL injection
- Session-based authentication for both portals
- Input validation and sanitization on forms

---

## 📸 Screenshots
![Homepage](https://github.com/user-attachments/assets/d2f0c21f-5f1b-458e-a994-ab622b981e29)
![Property-View](https://github.com/user-attachments/assets/d81ab1c9-4c59-4144-8991-bb8c028c3636)
![Admin-Dashboard](https://github.com/user-attachments/assets/4a195b13-09f8-4fb6-a140-84ab532c17e8)
![Property-Listings](https://github.com/user-attachments/assets/7a752d66-e199-4eb9-9500-7c1fcc311a7c)


---

## 👨‍💻 Author

**Yash Patil**
📧 patil.yash.dev@gmail.com
🔗 linkedin.com/in/yash-patil-8168a9309

---

## 📄 License

MIT License — free to use and modify.
