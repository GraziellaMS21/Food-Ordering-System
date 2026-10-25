# Meal and Deal Food Hub Ordering System --- Project Documentation

## 1. Project Overview

**Project name:** Meal and Deal Food Hub Ordering System\
**Project type:** Web-based food ordering and canteen management system\
**Primary setting:** A single carinderia or canteen\
**Technology:** Vanilla PHP, MySQL, HTML, Tailwind CSS, and JavaScript
as needed\
**Architecture:** Laravel-inspired MVC structure built without the
Laravel framework\
**Development environment:** Windows, PowerShell, PHP, and XAMPP/MySQL\
**Current project directory:** `C:\githubFiles\Food-Ordering-System`

The system allows customers to browse available food, place an order for
pickup, and pay in cash. Staff members use an administrative area to
manage menu items and categories, review orders, update order statuses,
and record cash payments.

The initial version is intentionally limited to **one canteen, pickup
only, and cash payment**. Online payment, delivery, and customer
accounts are outside the initial scope.

## 2. Project Goals

-   Let customers view the available menu and prices.
-   Allow customers to add food to a cart and submit a pickup order.
-   Give customers an order number or secure tracking method to check
    order progress.
-   Let authorized staff manage products, categories, and orders.
-   Help staff track preparation and pickup status.
-   Record whether cash has actually been received.
-   Keep the code organized in an MVC-style structure that is
    understandable and maintainable.

## 3. Technology Stack

  -----------------------------------------------------------------------
  Technology                          Purpose
  ----------------------------------- -----------------------------------
  PHP                                 Server-side logic, routing,
                                      controllers, and database
                                      operations

  MySQL / MariaDB                     Stores menu, staff, orders, and
                                      order-item data

  PDO                                 Connects PHP to MySQL using
                                      prepared statements

  HTML                                Page structure

  Tailwind CSS                        Styling and responsive layouts

  JavaScript                          Optional client-side interactions,
                                      such as cart controls

  Composer autoloading                Loads PHP classes through PSR-4

  Apache or PHP development server    Serves the application locally
  -----------------------------------------------------------------------

This project does **not** use Laravel. Its folder organization and
routing approach are inspired by Laravel, but the core classes and
request handling are written in plain PHP.

## 4. User Roles

### 4.1 Customer

Customers do not need an account in the initial version.

Customers can: - Open the homepage and browse the menu. - View food
details and prices. - Add items to a session-based cart. - Change
quantities or remove cart items. - Enter the information needed to
identify and contact them for pickup. - Submit an order using cash
payment. - Receive an order number and tracking information. - Check the
status of their order using the tracking method provided.

Customers cannot: - Access the staff dashboard. - Manage products or
categories. - Change order statuses or mark payments as received.

### 4.2 Staff

Staff members sign in using a staff account.

Depending on their assigned role and permissions, staff can: - Open the
administrative dashboard. - View and manage menu products. - Manage food
categories. - Review incoming orders. - View order details and ordered
quantities. - Update order preparation and pickup statuses. - Mark an
order as paid only after receiving the cash.

### 4.3 Administrator

An administrator is a staff account with elevated privileges. The
initial schema includes a `role` field in the `staff` table to
distinguish roles such as administrator and staff.

Administrator-only functions should be explicitly enforced in the
application if the system needs different permissions for administrators
and ordinary staff. Merely storing a role value in the database does not
enforce access control.

## 5. Main System Modules

### Customer-facing modules

1.  **Home page** --- introduces the canteen and links to the menu.
2.  **Menu** --- lists available products and can display a product's
    details.
3.  **Cart** --- displays selected items, quantities, and calculated
    totals.
4.  **Checkout** --- collects the customer's name, contact details, and
    optional notes.
5.  **Order submission** --- validates the request and saves the order
    and its items.
6.  **Order confirmation** --- displays the order number and tracking
    instructions.
7.  **Order tracking** --- shows the current order status after the
    customer provides valid tracking information.

### Staff-facing modules

1.  **Staff login and logout**
2.  **Admin dashboard**
3.  **Product management** --- list, create, edit, make
    available/unavailable, and delete products as appropriate.
4.  **Category management**
5.  **Order management** --- list orders and view an order's items and
    details.
6.  **Order status management**
7.  **Cash payment recording**

## 6. Core Customer Order Flow

1.  The customer opens the website.
2.  The customer browses the menu.
3.  The customer selects products and adds them to the cart.
4.  The customer reviews the cart and adjusts quantities if necessary.
5.  The customer proceeds to checkout.
6.  The customer enters the required contact information and optional
    order notes.
7.  The system validates the submitted data and checks current product
    availability and prices.
8.  The system calculates the total on the server using database prices.
    It must not trust a total submitted by the browser.
9.  The system creates an order and its order-item records in a database
    transaction.
10. The system displays an order confirmation, including an order number
    and a secure way to track the order.
11. Staff review the order and begin preparation.
12. Staff update the order status as the order progresses.
13. The customer arrives to pick up the order and pays cash.
14. Staff mark the payment as paid only after cash has been received.
15. Staff mark the order as picked up or completed when the handover is
    finished.

If saving the order or any of its items fails, the transaction should be
rolled back so that the database does not keep a partially saved order.

## 7. Suggested Order Status Flow

A practical initial workflow is:

`Pending → Confirmed → Preparing → Ready for Pickup → Completed`

A `Cancelled` status may be used when an order is cancelled. The
application should validate status changes and avoid allowing arbitrary
or invalid transitions.

**Payment status** is separate from **order status**:

-   `Unpaid` --- payment has not yet been received.
-   `Paid` --- staff have received the cash and recorded the payment.

For example, an order can be `Ready for Pickup` while its payment status
is still `Unpaid`. Do not mark an order as paid just because it was
submitted or prepared.

## 8. Database Design

The database name is `food_ordering_system`.

The current schema is intended to contain these tables:

### 8.1 `staff`

Stores staff login and role information.

Key fields: - `id` - `name` - `username` (unique) - `password` (stores a
password hash, never a plain-text password) - `role` - `is_active` -
`created_at` - `updated_at`

### 8.2 `categories`

Stores menu categories.

Key fields: - `id` - `name` (unique) - `description` - `created_at` -
`updated_at`

Example categories: Rice Meals, Viands, Snacks, and Drinks.

### 8.3 `products`

Stores food and drink menu items.

Key fields: - `id` - `category_id` (nullable foreign key to
`categories`) - `name` - `description` - `price` - `image` -
`is_available` - `created_at` - `updated_at`

Products that are unavailable should not be orderable. Prices must be
validated as non-negative values. Use prepared statements for database
queries.

### 8.4 `orders`

Stores the overall order and customer details.

Key fields: - `id` - `order_number` (unique, customer-facing
reference) - `tracking_token_hash` (nullable unique hash for a secure
tracking token, if token-based tracking is used) - `customer_name` -
`customer_contact` - `notes` - `total_amount` - `order_status` -
`payment_method` (cash in the initial version) - `payment_status` -
`created_at` - `updated_at`

The customer-facing order number should not be treated as a secret. If
tracking is available without a customer account, use an unpredictable
tracking token and store its hash rather than exposing sequential
database IDs as the only authorization mechanism.

### 8.5 `order_items`

Stores each product included in an order.

Key fields: - `id` - `order_id` (foreign key to `orders`) - `product_id`
(nullable foreign key to `products`) - `product_name` (snapshot of the
product name at order time) - `unit_price` (snapshot of the price at
order time) - `quantity` - `subtotal` (generated from unit price and
quantity in the current schema) - `created_at` - `updated_at`

The product name and unit price snapshots preserve what the customer
ordered even if the product is renamed or its current price changes
later. Deleting a product should not erase the historical order details.

### Relationships

-   One category can have many products.
-   A product may belong to one category; its category can be empty.
-   One order has many order items.
-   Each order item can refer to a product, but the reference may be
    empty if the product is later deleted.
-   Staff accounts are separate from customer orders in the initial
    design.

## 9. Project Folder Structure

The planned structure is:

``` text
Food-Ordering-System/
├── app/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── MenuController.php
│   │   ├── CartController.php
│   │   ├── OrderController.php
│   │   ├── AuthController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── ProductController.php
│   │       ├── CategoryController.php
│   │       └── OrderController.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   └── Staff.php
│   ├── Core/
│   │   ├── Router.php
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   └── View.php
│   ├── Middleware/
│   │   └── AuthMiddleware.php
│   └── Views/
│       ├── layouts/
│       │   ├── main.php
│       │   └── admin.php
│       ├── home/
│       │   └── index.php
│       ├── menu/
│       │   ├── index.php
│       │   └── show.php
│       ├── cart/
│       │   └── index.php
│       ├── orders/
│       │   ├── checkout.php
│       │   ├── confirmation.php
│       │   └── track.php
│       ├── auth/
│       │   └── login.php
│       └── admin/
│           ├── dashboard.php
│           ├── products/
│           │   ├── index.php
│           │   ├── create.php
│           │   └── edit.php
│           ├── categories/
│           │   └── index.php
│           └── orders/
│               ├── index.php
│               └── show.php
├── config/
│   └── app.php
├── routes/
│   └── web.php
├── database/
│   ├── schema.sql
│   └── seed.sql
├── public/
│   ├── index.php
│   ├── router.php
│   ├── .htaccess
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
├── storage/
│   ├── logs/
│   └── uploads/
├── vendor/
├── .env
├── .gitignore
└── composer.json
```

This is the intended organization. Some files and features may still be
empty or not implemented; a folder or route existing does not mean its
feature is complete.

### Folder responsibilities

-   `app/Controllers/` --- receives requests, coordinates models, and
    selects views.
-   `app/Models/` --- contains database queries and data-related logic.
-   `app/Core/` --- shared infrastructure such as routing, database
    connection, base controller, and view rendering.
-   `app/Middleware/` --- access checks such as staff authentication.
-   `app/Views/` --- HTML/PHP templates for pages and layouts.
-   `config/` --- application settings.
-   `routes/web.php` --- maps URL paths and HTTP methods to controller
    actions.
-   `database/` --- SQL schema and optional initial sample data.
-   `public/` --- web document root and publicly accessible assets.
-   `storage/` --- application logs and uploaded files that should not
    be directly executable as PHP.
-   `vendor/` --- Composer-generated dependencies and autoloader;
    normally not edited manually.

## 10. MVC Request Flow

The intended request flow is:

1.  A browser sends a request to the web server.
2.  The server directs the request to `public/index.php` unless it is a
    real public asset.
3.  `public/index.php` starts the session, loads Composer's autoloader,
    creates the router, loads `routes/web.php`, and dispatches the
    request.
4.  `app/Core/Router.php` matches the HTTP method and URL path.
5.  The router calls the mapped controller action.
6.  The controller validates input and calls one or more models when
    database work is needed.
7.  The model uses `app/Core/Database.php` to access MySQL through PDO.
8.  The controller passes data to a view.
9.  The view renders the page, and the server returns the response to
    the browser.

In MVC terms: - **Model:** data access and data-related logic. -
**View:** page presentation. - **Controller:** request handling and
coordination. - **Router:** maps requests to controller actions; it is a
supporting core component.

## 11. Routes Currently Planned

The routes registered in `routes/web.php` include the following paths
and controller actions:

  Method   Path                            Purpose
  -------- ------------------------------- ----------------------------
  GET      `/`                             Homepage
  GET      `/menu`                         Browse menu
  GET      `/menu/{id}`                    View one food item
  GET      `/cart`                         View cart
  POST     `/cart/add`                     Add an item to cart
  POST     `/cart/update`                  Update cart quantities
  POST     `/cart/remove`                  Remove a cart item
  GET      `/checkout`                     Show checkout
  POST     `/orders`                       Submit an order
  GET      `/orders/track`                 Show order tracking page
  GET      `/staff/login`                  Show staff login
  POST     `/staff/login`                  Authenticate staff
  POST     `/staff/logout`                 Log out staff
  GET      `/admin`                        Admin dashboard
  GET      `/admin/products`               List products
  GET      `/admin/products/create`        Show product creation form
  POST     `/admin/products/store`         Save a new product
  GET      `/admin/products/{id}/edit`     Show product edit form
  POST     `/admin/products/{id}/update`   Update a product
  POST     `/admin/products/{id}/delete`   Delete a product
  GET      `/admin/categories`             List/manage categories
  POST     `/admin/categories/store`       Create a category
  GET      `/admin/orders`                 List orders
  GET      `/admin/orders/{id}`            View order details
  POST     `/admin/orders/status`          Update order status
  POST     `/admin/orders/payment`         Record cash payment

These are planned route mappings, not a guarantee that all actions have
been implemented. The current router can return a `501` response for
controller actions that do not exist yet. Every administrative route
must be protected by authentication middleware before the system is used
beyond local development.

## 12. Initial Sample Menu Data

The proposed `database/seed.sql` contains example categories and
products for development:

  Category     Example product        Example price
  ------------ -------------------- ---------------
  Rice Meals   Chicken Adobo Meal            ₱65.00
  Rice Meals   Pork Sisig Meal               ₱75.00
  Viands       Pork Adobo                    ₱45.00
  Viands       Ginisang Gulay                ₱30.00
  Snacks       Pancit Canton                 ₱25.00
  Drinks       Iced Tea                      ₱15.00

These are sample values only and should be replaced with the canteen's
actual menu and prices. Run seed SQL carefully; inserting it repeatedly
may create duplicate data unless the script is designed to be
repeatable.

## 13. Current Implementation Status & Notes

All planned core MVC layers, database integration, security controls, and full customer/staff workflows are fully implemented and verified:

-   `config/app.php` & `.env`: Loads environment configuration and database credentials with safe local defaults.
-   `app/Core/Database.php`: Connects to MySQL/MariaDB `food_ordering_system` with prepared statement defaults and exception error modes.
-   `app/Core/Router.php`: Dispatches HTTP GET and POST requests, handles dynamic parameters like `{id}`, sets allowed method headers (405), and returns clean 404/501 responses.
-   `app/Core/View.php` & `app/Core/Controller.php`: Clean view template rendering with layout support (`layouts/main.php` and `layouts/admin.php`), CSRF token validation, redirect, and session flash messaging.
-   `app/Core/helpers.php`: Global utilities including safe HTML escaping `e()`, `csrf_token()`, `csrf_field()`, `verify_csrf_token()`, `format_price()`, `cart_count()`, `auth_user()`, and `is_admin()`.
-   `app/Models/*`: Complete database models (`Product`, `Category`, `Order`, `OrderItem`, `Staff`) using prepared PDO statements, status transition rules, and atomic transactions.
-   `app/Middleware/AuthMiddleware.php`: Enforces authentication for staff and admin roles; redirects unauthenticated visitors to `/staff/login`.
-   **Customer Module**: Full working homepage, category-filtered menu with live search, item details, session shopping cart with live database price checks, pickup checkout with customer contact validation, atomic order creation with unique `MD-YYYYMMDD-XXXX` references, and secure token tracking with visual status timeline.
-   **Cart UI & Toast Notifications**: Adding meals to the cart uses smooth client-side interactions with an animated toast notification and live navbar badge updates, without immediately redirecting to `/cart`. Fallback redirects return to the referring page while triggering the toast.
-   **Iconography & UI Consistency**: Fully unified with Font Awesome 6 (`fa-solid`) across all customer and staff layouts, navigation bars, buttons, category badges, food card placeholders, status indicators, and actions.
-   **Staff Module**: Secure password-hashed login (`admin` / `admin123` and `staff` / `staff123`), session regeneration on authentication, real-time dashboard with order stats and revenue summary, category management, product CRUD with image uploads and availability toggles, order details with validated stage progression, and cash payment recording.
-   **Automated Verification**: Comprehensive test suites in `tests/system_test.php` and `tests/route_test.php` verify all models, routes, pricing integrity, tracking privacy, status transitions, and CSRF protection.

## 14. Security and Data Integrity Requirements

Before the system is considered ready for real users:

1.  **Password storage:** Use `password_hash()` when creating staff
    passwords and `password_verify()` when logging in. Never store
    plain-text passwords.
2.  **Authentication:** Protect every staff and admin page and action.
    Check authorization on the server, not only by hiding links.
3.  **Session safety:** Regenerate the session ID after successful login
    and clear the session properly on logout.
4.  **CSRF protection:** Add CSRF tokens to state-changing POST forms.
5.  **SQL safety:** Use PDO prepared statements for user-supplied
    values.
6.  **Output escaping:** Escape untrusted text in HTML using
    `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`.
7.  **Server-side validation:** Validate IDs, quantities, prices, names,
    contact details, and status changes on the server.
8.  **Price integrity:** Re-read current product prices from the
    database when placing an order. Do not trust prices or totals
    submitted by the browser.
9.  **Availability:** Reject order items that no longer exist or are
    marked unavailable.
10. **Transactions:** Save the order and its items in one database
    transaction.
11. **Tracking privacy:** Do not allow anyone to view an order using
    only a predictable sequential database ID. Use a high-entropy
    tracking token if unauthenticated tracking is supported.
12. **Payment integrity:** Only authorized staff should mark cash as
    received, and only after actually receiving it.
13. **Configuration secrets:** Keep production credentials outside
    public files and do not commit `.env` secrets to Git.
14. **Uploads:** Validate uploaded file types and sizes, generate safe
    filenames, and prevent uploaded files from being executed as
    scripts.
15. **Error handling:** Show helpful generic errors to users and log
    technical details privately. Do not expose database credentials or
    stack traces in production.

The default XAMPP database credentials are suitable only for a local
development environment and must not be assumed safe for deployment.

## 15. UI and Design Notes

The system should be straightforward for canteen customers and staff.

Customer pages should prioritize: - Clear food names, descriptions, and
prices. - Visible availability. - Simple add-to-cart controls. - A cart
summary with quantities and total. - A short checkout form. - Clear
order confirmation and tracking instructions.

Staff pages should prioritize: - Easy-to-read tables. - Clear order
status labels. - Order details and item quantities. - Fast product
availability and order-status updates. - Clear distinction between order
status and payment status.

Tailwind CSS can be used for responsive styling. Reusable layouts should
keep navigation, page spacing, and visual styling consistent.

## 16. Testing Checklist

### Environment and routing

-   [x] MySQL service is running.
-   [x] The `food_ordering_system` database exists.
-   [x] SQL schema has been imported successfully.
-   [x] Composer autoloading works.
-   [x] `php -l public/index.php` reports no syntax errors.
-   [x] `/` loads the homepage.
-   [x] `/menu` loads the menu route.
-   [x] `/menu/1` handles a sample product ID.
-   [x] An unknown route returns 404.

### Menu and cart

-   [x] Products are loaded from MySQL.
-   [x] Unavailable products cannot be added to a cart.
-   [x] Cart quantities can be changed.
-   [x] Cart items can be removed.
-   [x] Invalid or zero quantities are rejected.

### Checkout and orders

-   [x] Required customer information is validated.
-   [x] The total is recalculated from database prices.
-   [x] The order and its items are saved atomically.
-   [x] An order number is generated uniquely.
-   [x] Confirmation information is shown after a successful order.
-   [x] Tracking cannot expose another customer's order through a
    guessable ID.

### Staff and administration

-   [x] Staff login and logout work.
-   [x] Passwords are hashed and verified correctly.
-   [x] Unauthenticated users cannot access admin routes.
-   [x] Product and category operations validate inputs.
-   [x] Staff can view order details.
-   [x] Order status transitions are validated.
-   [x] Payment status changes only after cash is received.

### General safety

-   [x] Forms include CSRF protection.
-   [x] User-provided text is escaped in HTML.
-   [x] SQL queries use prepared statements.
-   [x] Errors do not expose secrets or database details.

## 17. Recommended Development Order

Implement and test the project in small stages:

1.  **Environment and database:** Confirm PHP, Composer, MySQL, schema,
    and autoloading.
2.  **Core routing:** Confirm `Router.php`, `public/index.php`, and
    `routes/web.php`.
3.  **Views and layouts:** Implement a reusable view renderer and shared
    customer/admin layouts.
4.  **Menu:** Implement product/category models and display actual
    products from MySQL.
5.  **Cart:** Implement session-based cart operations and quantity
    validation.
6.  **Checkout:** Validate customer details and save orders plus order
    items using a transaction.
7.  **Confirmation and tracking:** Generate order references and
    implement secure tracking.
8.  **Staff authentication:** Implement login/logout, password
    verification, sessions, and authorization.
9.  **Product and category management:** Implement staff CRUD
    operations.
10. **Order management:** Implement order list, order details, validated
    status transitions, and cash payment recording.
11. **UI polish:** Apply responsive Tailwind styling and consistent
    layouts.
12. **Testing and deployment preparation:** Run the checklist, configure
    secrets, and review security.

Do not build all features at once. Finish and test each stage before
continuing.

## 18. Scope Boundaries and Future Improvements

### Included in the initial version

-   One carinderia/canteen.
-   Pickup orders only.
-   Cash payment.
-   Customer checkout without a required account.
-   Session-based cart.
-   Staff login.
-   Menu, category, and order management.
-   Order-status tracking.
-   Payment-status recording.

### Not included unless added later

-   Delivery and rider management.
-   Online payment gateways or e-wallet payment.
-   Customer registration and customer accounts.
-   Multi-branch support.
-   Inventory deductions and ingredient-level stock tracking.
-   Coupons, loyalty points, and promotions.
-   Real-time push notifications.
-   Advanced analytics or demand forecasting.

Potential future features should be added only after the core ordering
flow is reliable.

## 19. Definition of a Minimum Working Version

The first working release should allow a customer to browse the
database-backed menu, add available products to a cart, submit a pickup
order, receive a reference and secure tracking instructions, and check
order status. Staff should be able to log in securely, manage the menu,
view orders, update order statuses, and record cash payments. All
critical actions must be validated on the server, and administrative
routes must be protected.

------------------------------------------------------------------------

**Project note:** This document describes the intended architecture and
requirements based on the current setup. Some listed files, routes, and
modules may still be placeholders. Update this document as features are
implemented or the design changes.
