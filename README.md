# 📚 Facilísimo

Facilísimo is a web platform designed to improve learning experiences by allowing students to submit academic questions and receive personalized answers through scheduled Google Meet sessions with teachers.

---

## ✨ Features

- 👨‍🎓 Student registration and login
- 📩 Question submission form
- 👨‍🏫 Teacher dashboard to view, filter, and respond to questions
- 📅 Google Meet session creation via Google Calendar API
- ✉️ Email notifications with PHPMailer
- 🛠 Admin panel to monitor activity and manage users

---

## 🧱 Tech Stack

| Layer       | Technology                      |
|-------------|----------------------------------|
| Backend     | PHP (Custom MVC structure)       |
| Frontend    | HTML, CSS, Bootstrap             |
| Database    | MySQL                            |
| APIs        | Google Calendar API, Gmail API   |
| Auth        | Session-based authentication     |
| Email       | PHPMailer                        |

---

## 🧪 Project Status

🚧 Currently in MVP phase.  
✅ Core features are functional.  
🛠 UI and feature enhancements in progress.  
📍 See [`ROADMAP.md`](./ROADMAP.md) for planned features.

---

## 📸 Screenshots

> Coming soon — design updates and UI captures will be added here.

---

## 🚀 Setup Instructions

```bash
# Clone the repo
git clone https://github.com/miguelsarabia-dev/facilisimo.git

# Install Composer dependencies
composer install

# Copy .env example and configure
cp .env.example .env

# Set up your credentials.json for Google API
# Activate required APIs: Calendar, Gmail

# Run locally using XAMPP or similar