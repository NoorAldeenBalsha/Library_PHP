#  System Design & Architecture (DESIGN.md)

This document explains the architectural decisions, class design, application of SOLID principles, and the iterative development process of the College Library Management System.

1. Classes Created and Their Purpose
To achieve a clean, modular architecture, the system is divided into three main layers:

A. Entities (The Core Business Objects)
Book, Card, Loan: Created to represent real-world library concepts. They hold data and encapsulate properties securely.

Member (Abstract/Base Class), Student, Professor: Created to define the users of the library. We separated them to encapsulate specific business rules (e.g., limits, loan durations, and fine rates) using Object-Oriented Polymorphism.

B. Repositories (Database Interaction)
BookRepository, CardRepository, LoanRepository, MemberRepository, ReportRepository:

Why? To strictly isolate SQL queries and PDO logic from the rest of the application. If the database schema changes, we only update these files.

C. Services (Business Logic & Use Cases)
BookService, CardService, LoanService, ReportService:

Why? They act as the "Brain" of the system. They receive raw input from the UI, validate it (Fail-Fast), apply business rules (like checking card expiration or calculating fines), and then interact with the Repositories.

2. Application of SOLID Principles
Here is how SOLID principles were applied, and the consequences of ignoring them:

Single Responsibility Principle (SRP)
Where it is applied: Separating ReportService (data formatting) from ReportRepository (SQL data fetching) and index.php (UI printing).

What would happen if not applied? index.php would become a massive "God File" containing SQL queries, fine calculation math, and CLI styling. It would be impossible to read, test, or maintain without causing unintended bugs.

Open/Closed Principle (OCP)
Where it is applied: The Member entity hierarchy (Student and Professor extending Member).

What would happen if not applied? We would have a giant switch or if/else statement inside a single Member class to check if ($type == 'Student') { return 14; } else { return 30; }. Every time rules change, we would risk breaking existing working code.

Dependency Inversion Principle (DIP)
Where it is applied: Injecting repositories into Service constructors (e.g., public function __construct(LoanRepository $loanRepo) in LoanService.php).

What would happen if not applied? LoanService would have to instantiate new LoanRepository() internally. It would be tightly coupled to a specific database implementation, making it impossible to switch databases or write automated unit tests with Mock databases.

3. System Scalability: Adding a Third Member Type
Scenario: Adding an "External Member" (from outside the university).

Thanks to the Open/Closed Principle (OCP) applied in our design, adding a new member type is incredibly safe and requires minimal changes.

Exact files to touch:

src/Entities/ExternalMember.php (New File): Create this class extending Member. Implement the required methods: getMaxBookAllowed(), getLoanDurationDays(), and getDailyFineAmount() specific to external members.

src/Repositories/MemberRepository.php (Modified): Update the findById() method to check if the database type string matches 'External', and if so, return new ExternalMember(...).

index.php (Modified): Update the UI menu in the registration section (Case 5) to prompt the user: [3] for External Member.

What we DO NOT touch:
We do not need to touch LoanService or CardService. Because of polymorphism, the borrowBook and processReturn methods already know how to interact with the abstract Member interface, regardless of the specific child class.

4. Discoveries & Adjustments During Development
During the coding phase, several logical issues were discovered that were not apparent during the initial theoretical analysis. The analysis was adjusted accordingly:

Discovery 1: The UNIQUE Constraint Bug on Loans:

Initial Analysis: The database schema had member_id INT UNIQUE in the loan table to prevent a member from borrowing the same book twice.

Correction: I realized this would restrict a member to borrowing only one book in their entire lifetime. I removed the UNIQUE constraint from the schema.sql and moved this validation to the PHP logic by creating LoanRepository->hasActiveLoanForBook().

Discovery 2: Silent Failures & UI Coupling:

Initial Analysis: I assumed index.php could directly collect data and pass it to Repositories.

Correction: I discovered this leads to fatal errors if inputs are invalid, or "silent failures" where the user doesn't know what went wrong. I adjusted the design by implementing Service Facades (like processBorrow and processReturn). Now, Services catch invalid data, throw Exceptions, and index.php simply uses try/catch to display clean error messages.

Discovery 3: UI-Agnostic Services:

Initial Analysis: The ReportService was using echo directly to print data.

Correction: I realized this violates Clean Architecture because it ties the service strictly to a CLI environment. I refactored ReportService methods (like getLibraryStatusReport) to return arrays instead, moving the echo responsibility entirely to index.php.