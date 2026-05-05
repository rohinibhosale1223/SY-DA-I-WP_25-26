# Shoefy E-Commerce Website

A modern, responsive, and dynamically database-driven e-commerce platform for curated footwear.

## Tech Stack
- **Frontend**: HTML5, CSS3 (Custom Premium Styles), Vanilla JavaScript
- **Backend**: PHP 8+ (PDO), RESTful API Architecture
- **Database**: MySQL

## Features
- **Dynamic Product Catalog**: Products are fetched and rendered directly from the MySQL database.
- **Real-Time Search**: Filter through products instantly via the frontend interface.
- **User Authentication**: Secure Sign Up and Login system using `PASSWORD_BCRYPT`.
- **Database-Driven Cart**: Shopping cart securely stores item quantities per-user in the database. Add, update quantities, and remove items seamlessly.
- **Fully Functional Checkout**: A beautifully styled checkout page that calculates totals dynamically and properly manages database state by emptying the cart upon successful payment.
- **Responsive Design**: Premium CSS layouts utilizing Flexbox and Grid that look fantastic on both desktop and mobile devices.

## Setup & Running Locally with XAMPP

1. **Install XAMPP**
   Download and install XAMPP. Ensure that both the **Apache** and **MySQL** modules are running in the XAMPP Control Panel.

2. **Project Placement**
   Place this entire project folder (`practice`) into your XAMPP `htdocs` directory (typically `C:\xampp\htdocs\practice`).

3. **Database Setup**
   - Open your browser and go to `http://localhost/phpmyadmin`.
   - Create a new database named exactly: `shoefy_ecommerce_site`.
   - Import the `backend/schema.sql` file into this database. This will create all the required tables and seed the initial product data.
   - Ensure your MySQL user has the credentials configured in `backend/db_connection.php` (User: `aditya`, Password: `8767`). If you are using default XAMPP settings, you may need to update `db_connection.php` to use username `root` and an empty password `''`, or create the user `aditya` in phpMyAdmin.

4. **Running the App (Option A: XAMPP Apache)**
   - Open your browser and navigate to: `http://localhost/practice/index.html` (adjust the path if you named the folder differently in `htdocs`).

5. **Running the App (Option B: PHP Built-in Server)**
   - If you prefer not to use Apache, you can use PHP's built-in server while still keeping XAMPP's MySQL running.
   - Open a terminal in the root of the `practice` folder.
   - Run the command: `php -S localhost:8000`
   - Open your browser and navigate to: `http://localhost:8000`
   
6. **Usage**
   - Create an account using the Sign Up form on the Login page.
   - Browse products and start shopping!
   - Add items to your cart, proceed to the checkout page, and simulate a payment to see your cart dynamically clear.

## Project Structure
- `/css`: Contains all stylesheets (e.g., `index.css`, `product.css`, `cart.css`, `login.css`).
- `/images`: Stores product images and assets.
- `/js`: Contains frontend JavaScript logic including the centralized `api.js` fetch wrapper.
- `/pages`: Contains sub-pages like About, Contact, Cart, Login, and Product Detail.
- `/backend`: Contains the PHP backend files (`api.php`, `db_connection.php`) and the database schema (`schema.sql`).
