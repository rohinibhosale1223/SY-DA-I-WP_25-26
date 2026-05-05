# The Jersey Adda ⚽

The Jersey Adda is a modern, premium e-commerce platform for purchasing football (soccer) jerseys. Built with a sleek dark-mode aesthetic and glassmorphism UI, it features a fully functional frontend cart system and a secure PHP/MySQL backend for processing orders and managing users.

## 🚀 Tech Stack

- **Frontend**: HTML5, Vanilla CSS3 (Custom Properties, Grid, Flexbox), Vanilla JavaScript (ES6+).
- **Backend**: PHP 8+ (PDO for secure database interactions).
- **Database**: MySQL.

---

## 🛠️ How to Run the Project Locally

You do not need a heavy web server like Apache or Nginx to run this application! You can use PHP's built-in development server.

### 1. Database Setup
1. Ensure you have **MySQL** running on your machine (via XAMPP, WAMP, or standalone).
2. Open your preferred database manager (like phpMyAdmin or MySQL Workbench).
3. Import the `backend/schema.sql` file. This script will automatically:
   - Create the `jersey_adda` database.
   - Set up the tables (`users`, `products`, `product_sizes`, `orders`, `order_items`).
   - Seed the database with initial products and inventory stock.

### 2. Start the Server
Open your terminal (PowerShell, Command Prompt, or bash), navigate to the root directory of this project, and run the following command to start the PHP built-in server:

```bash
php -S localhost:8000
```
*(Note: If you have XAMPP installed on Windows, your PHP executable might be located at `C:\xampp\php\php.exe`. In that case, run `C:\xampp\php\php.exe -S localhost:8000`)*

### 3. View the Site
Open your web browser and go to:
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 🔌 API Endpoints Reference

All backend requests route through a centralized API controller located at `backend/api.php`. 

### 1. Fetch Products
- **Endpoint:** `GET /backend/api.php?action=get_products`
- **Description:** Returns a JSON array of all active products in the database.
- **Response Format:**
  ```json
  {
      "success": true,
      "products": [
          {
              "id": 1,
              "name": "Modern Home Kit",
              "price": "$89.99",
              "image": "jersey_1_1777954650801.png",
              "tag": "Bestseller"
          }
      ]
  }
  ```

### 2. User Registration
- **Endpoint:** `POST /backend/api.php?action=register`
- **Payload (JSON):**
  ```json
  {
      "full_name": "John Doe",
      "email": "john@example.com",
      "password": "securepassword123"
  }
  ```
- **Description:** Hashes the password and creates a new user record.

### 3. User Login
- **Endpoint:** `POST /backend/api.php?action=login`
- **Payload (JSON):**
  ```json
  {
      "email": "john@example.com",
      "password": "securepassword123"
  }
  ```
- **Description:** Verifies credentials and sets a secure PHP Session for the user.

### 4. Create Order (Checkout)
- **Endpoint:** `POST /backend/api.php?action=create_order`
- **Payload (JSON):**
  ```json
  {
      "shipping": {
          "full_name": "John Doe",
          "address": "123 Football Lane",
          "city": "Madrid",
          "state": "MD",
          "zip": "28001",
          "country": "Spain"
      },
      "cart": [
          { "id": 1, "size": "M", "quantity": 2, "price": 89.99 }
      ]
  }
  ```
- **Description:** Uses MySQL Transactions to safely store the order, link items, and reduce inventory stock dynamically. Returns an official `order_id`.
