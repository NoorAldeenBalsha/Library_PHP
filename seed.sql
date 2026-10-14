-- 1. Entering the sections (3 sections)
INSERT INTO section (name) VALUES 
('Computer Science & Programming'),
('Mathematics & Physics'),
('Novels & Literature');

-- 2. Entering books (8 books distributed across the sections)
INSERT INTO book (title, author, year_published, total_copies, section_id) VALUES 
('Clean Code', 'Robert C. Martin', 2008, 5, 1),
('Design Patterns', 'Erich Gamma', 1994, 3, 1),
('Algorithms to Live By', 'Brian Christian', 2016, 4, 1),
('Calculus Early Transcendentals', 'James Stewart', 2015, 10, 2),
('Linear Algebra', 'Gilbert Strang', 2009, 6, 2),
('Crime and Punishment', 'Fyodor Dostoevsky', 1866, 2, 3),
('1984', 'George Orwell', 1949, 7, 3),
('The Art of War', 'Sun Tzu', -500, 3, 3);

-- 3. Adding members (4 members: 2 students and 2 teachers)
INSERT INTO member (Name, TYPE, fines_balance) VALUES 
('Noor Aldeen Balsha', 'student', 0.00),
('Ahmad Ali', 'student', 0.00),
('Dr. Adnan', 'professor', 0.00),
('Dr. Sami', 'professor', 0.00);

-- 4. Card entry (4 active cards linked to members)
-- Student cards
INSERT INTO card (card_number, issue_date, expiry_date, is_active, member_id) VALUES 
('STU-2026-0001', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 1, 1),
('STU-2026-0002', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 1, 2);
-- Professor cards
INSERT INTO card (card_number, issue_date, expiry_date, is_active, member_id) VALUES 
('PRO-2026-0003', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 1, 3),
('PRO-2026-0004', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 1, 4);

-- 5. Entering loans (various test cases to evaluate the system)

-- First case (Very late - OVERDUE):
-- The student (Noor Aldeen) borrowed the book (Clean Code) 40 days ago, and he was supposed to return it 26 days ago!
-- (Ready test to calculate a big fine upon return)
INSERT INTO loan (member_id, book_id, loan_date, due_date, return_date) VALUES 
(1, 1, DATE_SUB(CURRENT_DATE, INTERVAL 40 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 26 DAY), NULL);

-- The second case (active and natural borrowing - ACTIVE):
-- Dr. Adnan borrowed the book (Design Patterns) today, and he has 30 days to return it.
INSERT INTO loan (member_id, book_id, loan_date, due_date, return_date) VALUES 
(3, 2, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), NULL);

-- The third case (a little late - OVERDUE):
-- The student (Ahmed) borrowed the book (Calculus) and was 5 days late in returning it.
INSERT INTO loan (member_id, book_id, loan_date, due_date, return_date) VALUES 
(2, 4, DATE_SUB(CURRENT_DATE, INTERVAL 19 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 5 DAY), NULL);

-- Case Four (Returned Successfully - RETURNED):
-- Dr. (Sami) borrowed the book (1984) and returned it, so the return_date field is not empty.
INSERT INTO loan (member_id, book_id, loan_date, due_date, return_date) VALUES 
(4, 7, DATE_SUB(CURRENT_DATE, INTERVAL 15 DAY), DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), DATE_SUB(CURRENT_DATE, INTERVAL 2 DAY));