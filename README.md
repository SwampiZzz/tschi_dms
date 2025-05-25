# TSCHI - Document Management System (DMS)

A web-based platform for securely uploading, reviewing, managing, and retrieving documents for the **Tanauan School of Craftsmanship and Home Industries**.

---

## 📌 Features

### ✅ Core Functionality
- **User Roles**: Admin, Moderator, Regular User
- **Secure Login & Registration**
- **Upload PDFs**: Simple form with validation
- **File Management**: Grid/Table view, filtering, search
- **Review Workflow**: Moderators/Admins approve/reject files with remarks
- **Real-time Feedback**: Status badges and success alerts
- **Audit Logs**: Admin dashboard tracks key actions

### ⚙️ Admin Capabilities
- Manage users (promote, demote, delete)
- Review all uploads
- Access activity logs and file charts
- Modify categories

### 🔐 Security
- Role-based access control
- File access protection (only owners or authorized roles)
- Admin override for file deletions and reviews

----------------------

## 💻 Tech Stack

| Technology | Description                         |
|------------|-------------------------------------|
| PHP        | Backend scripting                   |
| MySQL      | Database                            |
| HTML/CSS   | Frontend structure and styling      |
| Bootstrap  | UI Components and Responsive Design |
| JavaScript | Modal control & interactivity       |

----------------------

## 🚀 Installation & Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-repo/tschi-dms.git
Import the database (SQL file not included here):

Create a database (e.g., tschi_dms)

Import the provided tschi_dms.sql

Configure database settings in config.php:

php
Copy
Edit
$conn = new mysqli("localhost", "root", "", "tschi_dms");
Ensure folders are writable:

uploads/

elems/profile-picture/

Run locally:
Open http://localhost/tschi_dms in your browser.
----------------------
👤 Developers
This system is developed by:
Cesar Janell Medina – Lead Developer
Edmund Sealtiel De Veyra – Backend & Security
Sheila Mae Comandao – UI/UX & Database Design
----------------------
📄 License
This project is developed for educational purposes at TSCHI and is not open-source for redistribution without permission.
