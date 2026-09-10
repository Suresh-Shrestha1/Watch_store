# Project Summary — PHP Watch E-Commerce

## 1. Short Description

A PHP + MySQL watch e-commerce application that provides a customer storefront and an admin panel for managing products, brands, and orders. Customers can browse watches, add items to wishlist/cart, and complete purchases using Cash on Delivery or eSewa. The project is designed for small retailers or developers who need a lightweight, self-hosted online watch store with inventory and order workflows.

## 2. Long Description

This project implements a complete store flow for selling watches online. On the customer side it provides product listing and detail pages, strap-size handling where applicable, wishlist and cart functionality, and a checkout workflow that collects shipping information and lets customers choose COD or pay online with eSewa. During checkout the application validates stock, creates an order snapshot (order + order_items), decrements product inventory inside a database transaction, clears the cart, and then either finishes the order (COD) or redirects the customer into the eSewa payment flow.

On the admin side the application includes a secure admin login (admins are users with role = 'admin') and pages to add/edit/delete products (with multiple image uploads and a main-image flag), manage brands, view customers, and manage orders. Order management supports controlled status transitions (pending → confirmed → processing → shipped → delivered) and associated timestamps; COD payments can be marked paid by admin while eSewa payments are updated automatically after verification.

The backend is PHP with MySQL. Database tables store users, brands, products (including strap and dial metadata), product_images, cart, wishlists, orders, and order_items (snapshots). File uploads (product and brand images) are saved under `assets/uploads/`. eSewa integration is implemented server-side: the app generates an HMAC-SHA256 signature for the payment request, posts the required fields to eSewa, and verifies the returned payload and the eSewa server response before marking an order paid.

Overall, the system connects PHP pages, action controllers, and the MySQL database with session-based authentication and prepared statements to manage data and state across storefront and admin workflows.

## 3. Problem Statement

Small retailers and developers need a manageable way to present a watch catalog online, accept orders, and track inventory and fulfillment without adopting a full e-commerce platform. Manually tracking products, strap variants, stock, and order states leads to errors and wasted time. This project addresses those problems by providing:

- An online product catalog that captures watch-specific attributes (model number, strap options, movement type, images).
- A cart and checkout workflow that validates stock and records exact snapshots of ordered items.
- Order management for administrators to track status and payment, avoiding manual spreadsheets.
- Integrated online payment via eSewa to accept digital wallet payments and reconcile them with orders.

The application reduces manual errors in product and order handling and provides a contained, self-hosted solution for selling watches online.

## 4. My Approach

I designed and built the project to be straightforward to install and maintain while covering the primary ecommerce needs:

- **Database design:** I designed the schema to store users, brands, products (with strap and dial metadata), product images, cart, wishlist, orders, and order_items as snapshots so order history remains accurate even if products change.
- **Authentication:** I implemented session-based authentication for customers and a distinct admin session flow; passwords are hashed using PHP’s `password_hash`.
- **Customer interface:** I built server-rendered PHP pages for product listing, product detail, cart, checkout, and account pages, using lightweight JS where needed.
- **Cart & strap logic:** I implemented cart logic that supports strap-size selection, validates sizes and stock before adding, and prevents invalid additions.
- **Order processing:** I implemented checkout and order processing using a DB transaction: insert `orders`, insert `order_items`, decrement stock, and clear cart — with rollback on failure.
- **eSewa integration:** I wrote server-side signature generation, constructed the form posted to eSewa, decoded and verified eSewa payloads, and performed server-to-server verification before marking an order paid.
- **Admin panel:** I built admin pages for product CRUD (multi-image upload, main image selection), brand management, and order lifecycle management with timestamped transitions.
- **Security measures:** I used prepared statements for DB queries and server-side validation to reduce injection risk and enforce business rules.

## 5. Key Features

- **User Registration & Login** — Customers can create an account and securely log in to access shopping and account features.
- **Admin Authentication** — Admin users have a separate login and role-based access control for admin pages.
- **Watch/Product Catalog** — Users can browse watches and view product metadata (model number, brand, movement, strap fields).
- **Product Detail Page** — Detailed product pages show images, model number and strap-size options where applicable.
- **Product Image Uploads** — Admins can upload multiple product images; a main image is tracked for display.
- **Strap Size Support** — Products support adjustable or fixed straps and preserve selected strap sizes in cart/order.
- **Shopping Cart** — Add items to cart, manage quantities, and validate stock on add/update.
- **Wishlist** — Users can add items to wishlist and move wishlist items to cart with validation.
- **Checkout Workflow** — Collect shipping details, compute totals (includes shipping), and place orders.
- **Order Creation with Snapshot** — Orders store snapshot details in `order_items` to preserve historical accuracy.
- **Stock Validation & Atomic Updates** — Order placement validates and decrements stock inside a DB transaction to avoid overselling.
- **Payment Methods: COD & eSewa** — Supports Cash on Delivery and eSewa payment integration.
- **eSewa Signature & Verification** — HMAC-SHA256 signature generation and server-side verification with eSewa before marking order paid.
- **Order Status Lifecycle** — Admins progress orders through defined states with corresponding timestamps.
- **Admin Order Controls** — Update payment status for COD, add admin notes, and delete only pending unpaid orders.
- **Prepared Statements** — Database operations use prepared statements in controllers to reduce SQL injection risk.
- **Shipping Constant** — Centralized `SHIPPING_CHARGE` used in calculations.
- **File cleanup on delete** — Deleting products or images removes files from disk and updates main-image logic.

## 6. What I Did

- **Designed and implemented** the relational database schema for users, products, brands, product_images, cart, wishlists, orders, and order_items.
- **Implemented** secure customer registration and login using `password_hash` and session storage.
- **Built** the product catalog and product detail pages with support for strap-size options and product metadata.
- **Developed** cart functionality to add, update, and remove items while validating stock and strap selections.
- **Implemented** the checkout controller that validates input, calculates totals, writes orders and order_items, decrements stock, and clears the cart inside a transaction.
- **Integrated** eSewa payment: signature generation, form construction, and server-to-server verification before updating payment status.
- **Created** the admin login flow and protected admin pages with session-based role checks.
- **Developed** admin product management (add/edit/delete), including multi-image upload, main-image handling, and deletion of image files.
- **Implemented** brand management and linked brand records to products for catalog organization.
- **Built** admin order management with controlled status transitions, timestamp updates, and payment handling rules.
- **Added** wishlist management with move-to-cart functionality and validation logic.
- **Used** prepared statements for database operations across controllers to improve query safety.
- **Centralized** shipping charge configuration and used it in checkout calculations.
- **Handled** file uploads with server-side MIME and size checks and ensured uploaded images are stored under `assets/uploads`.
- **Implemented** safeguards to prevent deletion of products linked to existing orders.
- **Added** input validation (e.g., phone format checks) and session-based error/success messaging for user feedback.
- **Structured** the project into logical directories (`pages`, `admin`, `config`, `assets`) for maintainability.

## 7. Technologies Used

- **PHP**
- **MySQL / MariaDB**
- **HTML**
- **CSS** (Tailwind used on several pages via CDN)
- **JavaScript**
- **eSewa** (digital wallet integration)

## 8. Project Highlights

- **Full-stack e-commerce workflow** covering product browsing, cart, checkout, and order management with inventory control.
- **Server-side eSewa integration** with HMAC signature generation and verification to reconcile payments securely before marking orders paid.
- **Transactional order processing** that preserves order accuracy by writing snapshots and updating stock atomically.
- **Admin product and image management** including multi-image uploads, main image selection, and safe deletion.
- **Role-based access control** that separates customer and admin functionality via session checks.
- **Order snapshots for historical integrity** so order details remain accurate after product updates.
- **Business-rule enforcement** server-side (strap-size validation, phone format, stock checks) to reduce invalid orders.

---

If you would like this file renamed (for example `SUMMARY.md` or `PROJECT_SUMMARY.md`) or want a one-line README header derived from this summary, tell me which format you prefer and I’ll update it.
