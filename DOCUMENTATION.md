# Variable Budget System Documentation

## Overview
This is a PHP-based web application for managing personal or organizational financial transactions, entities, currencies, purposes, and modes. The system is designed with a modular MVC (Model-View-Controller) architecture, ensuring maintainability, scalability, and security.

---

## Architecture

### 1. MVC Pattern
- **Models (`src/Models/`)**: Handle all database interactions and business logic. Each model represents a database table (e.g., `Transaction`, `Entity`, `Currency`, `Purpose`, `Mode`, `User`).
- **Views (`src/Views/`)**: Render HTML pages using PHP, with Bootstrap for styling. Views are organized by resource (e.g., `transactions/`, `entities/`).
- **Controllers (`src/Controllers/`)**: Orchestrate the application flow. Controllers receive HTTP requests, validate input, interact with models, and render views.
- **Router (`src/router.php`)**: Maps HTTP routes to controller actions, supporting RESTful CRUD operations.

### 2. Database
- MySQL/MariaDB, with migrations defined in `database/migrations.sql`.
- Key tables: `users`, `entities`, `currencies`, `purposes`, `modes`, `transactions`.
- Foreign keys enforce referential integrity.

### 3. Security
- **Authentication**: Session-based, with user roles (admin/user).
- **Authorization**: Controllers check user ownership before allowing edits/deletes.
- **CSRF Protection**: All forms except delete use CSRF tokens.
- **Input Validation**: Both client-side (Bootstrap) and server-side (model validation).

---

## Core Logic

### Transaction CRUD
- **Create**: `/transactions/create` (GET/POST)
  - Form supports basic and full modes.
  - Validates all required fields.
  - In basic mode, destination and fee fields are auto-filled with system defaults.
  - On success, redirects to transaction details.
- **Read**: `/transactions` (list), `/transactions/:id` (details)
  - Lists all user transactions with related entity, currency, purpose, and mode info.
- **Update**: `/transactions/:id/edit` (GET), `/transactions/:id` (POST)
  - Pre-fills form with existing data.
  - Validates and updates transaction atomically in a DB transaction.
  - On success, redirects to details.
- **Delete**: `/transactions/:id/delete` (POST)
  - Checks authentication and ownership.
  - Deletes transaction and redirects to list.
  - No CSRF validation for delete (by design).

### Model Layer
- All models extend a base `Model` class, which provides generic CRUD methods (`find`, `findAll`, `create`, `update`, `delete`).
- Models define `$fillable` fields for mass assignment protection.
- Transactions are created/updated inside DB transactions for atomicity.
- Validation logic is centralized in model methods (e.g., `validateTransaction`).

### View Layer
- All forms use Bootstrap for a modern, responsive UI.
- Error and success messages are shown using session variables.
- Edit and create forms share a consistent layout and validation feedback.

### Controller Layer
- Each controller is responsible for a resource (e.g., `TransactionController`).
- Handles authentication, authorization, validation, and redirects.
- Renders the appropriate view with all necessary data.

---

## Notable Features
- **Basic/Full Transaction Modes**: Quick add (basic) and detailed (full) transaction entry.
- **System Entities**: Special entities like 'Void' for system-level operations.
- **Session Feedback**: All user actions provide immediate feedback via session messages.
- **Atomic Operations**: All create/update/delete actions are atomic, ensuring data integrity.

---

## Extending the System
- Add new models for additional resources by extending the base `Model` class.
- Add new views in the appropriate subfolder under `src/Views/`.
- Register new routes in `src/router.php` and implement controller actions.

---

## File Structure (Key Parts)
- `src/Models/` — Business logic and DB access
- `src/Controllers/` — Application logic and flow
- `src/Views/` — User interface (HTML/PHP)
- `src/router.php` — Route definitions
- `public/` — Public assets (CSS, JS, entry point)
- `config/` — Configuration files
- `database/` — Migrations and seed data

---

## Best Practices
- Always validate user input both client- and server-side.
- Use DB transactions for all write operations.
- Keep business logic in models, not controllers or views.
- Use session messages for user feedback.
- Maintain consistent UI/UX across all forms and pages.

---

## Contact & Support
For further development or support, refer to the code comments and follow the MVC structure for all new features.
