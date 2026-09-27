# 📚 Library Management System

A comprehensive and modern **Library Management System** designed to streamline the operations of a library. This project is built using a decoupled architecture with a **React.js (Vite)** frontend and a **Laravel** backend API.

---

## 🏗️ System Architecture

This project is built using a **Decoupled Architecture (Client-Server Model)**:

*   **Frontend (Client-side):** Acts as a **Single Page Application (SPA)** running in the browser. It handles the UI, user interactions, and state management.
*   **Backend (Server-side):** Acts as a **RESTful API Server**. It receives requests from the frontend, executes business logic, interacts with the MySQL database via Eloquent ORM, and returns data in JSON format.
*   **Communication:** The Frontend and Backend communicate securely via HTTP requests (`Axios`) using **Token-based Authentication** (Laravel Sanctum).

---

## 💻 Tech Stack

### Frontend (`/dashboard`)
*   **Framework**: React.js (built with Vite for fast HMR)
*   **Styling**: Tailwind CSS (Fully responsive with Dark/Light mode)
*   **State Management**: Redux Toolkit
*   **Routing**: React Router DOM (with protected routes)
*   **Animations**: Framer Motion
*   **Icons**: React Icons (Io5, Fa, Md)

### Backend (`/library_project_final`)
*   **Framework**: Laravel (PHP)
*   **Database**: MySQL
*   **API Structure**: RESTful API
*   **Authentication**: Laravel Sanctum

---

## 🗂️ Core Features & Functionalities

### 1. 📦 Book Management (Master Data)
*   **Category, Authors & Publishers**: Manage the core metadata for library inventory.
*   **Books**: Manage general book information (Title, Cover, Publish Year).
*   **Author Books**: Link multiple authors to a single book using many-to-many relationships.
*   **Book Copies**: Manage physical inventory. Generates specific barcodes/IDs for individual physical copies of a book to track their availability status.

### 2. 🔄 Circulation (Loans & Returns)
*   **Loans**: Process book checkouts. Automatically updates a Book Copy's status from `Available` to `Borrowed` to prevent double-loaning. Tracks borrower details and due dates.
*   **Book Return**: Process check-ins. Reverts the book status to `Available`.
*   **Loans Detail**: View the historical breakdown of specific loan transactions.

### 3. 👥 People Management
*   **Users**: Create and manage accounts for library staff (Admins/Librarians) with secure login access.
*   **Members**: Register and track library borrowers (students/customers). Members are tracked for borrowing history but do not have system login access.

### 4. 💰 Finance & Penalties
*   **Fines**: Automatically calculates late fees when a book is returned past its due date. Keeps a record of the penalty amount and reason.
*   **Fine Payments**: Track and manage the settlement of fines by members.

---

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing.

### Prerequisites
Make sure you have the following installed:
- [Node.js](https://nodejs.org/) & npm
- [PHP](https://www.php.net/) (>= 8.1)
- [Composer](https://getcomposer.org/)
- [MySQL](https://www.mysql.com/)

---

### 1. Backend Setup (Laravel)

Open your terminal and navigate to the backend directory:

```bash
cd library_project_final
