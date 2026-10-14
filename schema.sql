-- First , we need to drop the existing tables in reverse order (child tables first, then parent tables) to avoid foreign key constraint issues.
DROP TABLE IF EXISTS loan;
DROP TABLE IF EXISTS card;
DROP TABLE IF EXISTS book;
DROP TABLE IF EXISTS member;
DROP TABLE IF EXISTS section;

-- Second, we will create the new tables in the correct order (parent tables first, then child tables) to ensure that foreign key constraints are satisfied.

-- 1. Table sections 
CREATE TABLE section (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- 2. Table books 
CREATE TABLE book (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    year_published INT NOT NULL,
    total_copies INT NOT NULL,
    section_id INT NOT NULL,
    -- Define the relationship (One-to-Many)
    FOREIGN KEY (section_id) REFERENCES section(id)
);

-- 3. Table members 
CREATE TABLE member(
    id INT Auto_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    TYPE ENUM('student', 'professor') NOT NULL,
    fines_balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00
);

-- 4. Table cards 
CREATE TABLE card (
    id INT AUTO_INCREMENT PRIMARY KEY,
    card_number VARCHAR(20) NOT NULL UNIQUE,
    issue_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    member_id INT NOT NULL UNIQUE,
    -- Define the relationship (One-to-One)
    FOREIGN KEY (member_id) REFERENCES member(id)
);

-- 5. Table loans 
CREATE TABLE loan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    book_id INT NOT NULL,
    loan_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    -- Define the relationships (Many-to-Many via Pivot)
    FOREIGN KEY (book_id) REFERENCES book(id),
    FOREIGN KEY (member_id) REFERENCES member(id)
);