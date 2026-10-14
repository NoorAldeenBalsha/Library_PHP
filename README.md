# College Library Management System

A robust, CLI-based Library Management System built with **PHP** and **MySQL**. This project is designed strictly following **Clean Architecture** principles and **Object-Oriented Programming (OOP)** to ensure scalability, maintainability, and a clear separation of concerns.

##  Key Features

* **Member Management (OOP Polymorphism):** Supports different types of members (Students and Professors) using inheritance. Each type enforces its own business rules (e.g., max allowed books, loan duration, and daily fine rates).
* **Library Cards System:** Auto-generates unique library cards with issue and expiry dates, and safely tracks card activation status.
* **Borrowing & Returning Core Logic:** 
  * Strict pre-validation before borrowing (checks if the card is active, copies are available, and member limits are not exceeded).
  * Auto-calculates accurate due dates based on the specific member's type.
  * Prevents members from borrowing the same book twice simultaneously.
* **Smart Fine Calculation:** Automatically calculates financial fines upon late returns using strict database date comparisons.
* **Fail-Fast Validation:** The `Service` layer filters and validates all inputs, throwing descriptive Exceptions before any invalid data reaches the database, ensuring 100% data integrity.
* **Advanced Reporting:** Generates comprehensive reports (Overdue books, active loans, available books, and expired memberships) using complex MySQL `JOIN`s and date functions.

##  Architecture (Clean Architecture)

To achieve a modular design, the project is divided into distinct, decoupled layers:
1. **Entities (`src/Entities/`):** Pure PHP classes representing core business objects (`Book`, `Member`, `Card`, `Loan`).
2. **Repositories (`src/Repositories/`):** Handles all Database operations using `PDO`. Fully safeguarded against SQL Injection using Prepared Statements.
3. **Services (`src/Services/`):** The "Brain" of the application (e.g., `LoanService`, `CardService`). Contains all business logic and validations.
4. **Exceptions (`src/Exceptions/`):** Custom exception classes (e.g., `MaxBooksAllowedException`, `BookUnavailableException`) designed to handle specific business rule violations gracefully without crashing the system.
5. **Presentation (`index.php`):** A thin CLI dashboard that acts purely as a router, handling user inputs, passing them to Services, and cleanly displaying outputs/exceptions.

##  Technologies Used

* **Language:** PHP 8+ (Strict Types enabled)
* **Database:** MySQL
* **Database Driver:** PHP Data Objects (PDO)
* **Design Patterns:** Repository Pattern, Dependency Injection, Service Facade.

##  How to Run the Project (Setup Guide)

To test or run this system locally, please follow these steps:

**1. Database Setup:**
* Open your database manager (e.g., phpMyAdmin) and create a new empty database.
* Import the `schema.sql` file to build the tables and relationships.
* **CRITICAL FOR TESTING:** Import the `seed.sql` file to inject dummy data. This file includes sections, books, members, and a ready-to-test **Overdue Loan** to easily evaluate the fine calculation system without manual date manipulation.

**2. Configuration:**
* Locate the `config.example.php` file in the root directory.
* Duplicate it and rename the copy to `config.php`.
* Update the database credentials inside `config.php` to match your local environment.

**3. Execution:**
* Open your terminal (or CMD) and navigate to the project directory.
* Run the following command to start the interactive dashboard:
  ```bash
  php index.php