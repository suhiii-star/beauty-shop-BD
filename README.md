# Beauty Shop - PHP/MySQL E-Commerce Project

This is a complete beginner-friendly e-commerce project for cosmetics and personal-care products. It follows the submitted proposal features: product catalogue, categories, search/filter, cart, checkout, customer login/registration, MySQL order storage, and an admin panel for product and order management.

## Technology Used

- HTML5
- CSS3
- JavaScript
- PHP
- MySQL
- XAMPP / Apache
- phpMyAdmin

## Main Features

### Customer Side

- Home page with category cards and latest products
- Product catalogue with search, category filter, and sorting
- Product details page
- Add to cart
- Update/remove cart items
- Registration and login
- Checkout with phone, address, and order notes
- Order confirmation
- My Orders page with order details

### Admin Side

- Admin dashboard
- Product list
- Add product
- Edit product
- Delete product
- View all customer orders
- Update order status
- View full order details

## Folder Structure

```text
beauty_shop/
├── admin/
│   ├── index.php
│   ├── orders.php
│   ├── order_view.php
│   ├── products.php
│   └── product_form.php
├── assets/
│   ├── css/style.css
│   ├── js/main.js
│   └── images/
├── config/config.php
├── database/beauty_shop.sql
├── includes/
│   ├── footer.php
│   ├── header.php
│   ├── navbar.php
│   └── product_card.php
├── index.php
├── products.php
├── product.php
├── cart.php
├── checkout.php
├── order_success.php
├── order_view.php
├── my_orders.php
├── register.php
├── login.php
└── logout.php
```

## Setup Instructions for XAMPP

1. Copy the `beauty_shop` folder into:

```text
C:\xampp\htdocs\
```

2. Start **Apache** and **MySQL** from XAMPP Control Panel.

3. Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

4. Import the SQL file:

```text
beauty_shop/database/beauty_shop.sql
```

5. Run the project in browser:

```text
http://localhost/beauty_shop/
```

## Default Admin Login

```text
Email: admin@beautyshop.test
Password: admin123
```

## Customer Testing

You can register a new customer account from the Register page, add products to cart, checkout, and then view the order from My Orders.

## Notes for Viva / Demonstration

- `users` table stores both customers and admins using a `role` field.
- Passwords are stored using PHP `password_hash()`.
- SQL queries for user input use prepared statements.
- Sessions are used for login and cart management.
- Orders are split into `orders` and `order_details` tables to properly store one order with multiple products.
- Admin panel is protected by role-based access.
- The first version does not include online payment or delivery tracking, as mentioned in the proposal limitations.

## GitHub Pages Live Demo Version

This project also includes a static HTML/CSS/JavaScript demo version for GitHub Pages:

- `index.html`
- `products.html`
- `product.html`
- `cart.html`
- `checkout.html`
- `login.html`
- `register.html`
- `admin-demo.html`
- `assets/js/static-store.js`
- `assets/js/static-pages.js`

Use this version only for the public live website URL. It runs without PHP or MySQL and stores demo cart/order data in the browser using localStorage.

The full project is still the PHP + MySQL version. To run the full version, use XAMPP and import `database/beauty_shop.sql`.

### How to publish on GitHub Pages

1. Upload all project files to a GitHub repository.
2. Go to Repository Settings > Pages.
3. Under Build and deployment, select Deploy from a branch.
4. Select branch `main` and folder `/root`.
5. Save and wait for GitHub to publish the site.
6. Your live URL will look like: `https://your-username.github.io/beauty-shop-ecommerce/`
