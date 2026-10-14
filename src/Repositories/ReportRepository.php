<?php
declare(strict_types=1);

class ReportRepository
{
    private PDO $pdo;
    // Constructor to initialize the PDO instance
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // This method retrieves all overdue loans from the database
    public function getOverdueLoans(): array {
        // we are using DATEDIFF to calculate the number of days late for each overdue loan
        $sql = "SELECT 
                    m.name AS member_name, 
                    b.title AS book_title, 
                    l.due_date, 
                    DATEDIFF(CURRENT_DATE, l.due_date) AS days_late
                FROM loan l
                JOIN member m ON l.member_id = m.id
                JOIN book b ON l.book_id = b.id
                WHERE l.return_date IS NULL AND l.due_date < CURRENT_DATE";
                
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // This method retrieves the current status of the library, including available books and their borrowers
    public function getLibraryStatus(): array {
        $sql = "SELECT 
                    b.id,
                    b.title, 
                    b.total_copies,
                    m.name AS borrower_name,
                    l.due_date
                FROM book b
                LEFT JOIN loan l ON b.id = l.book_id AND l.return_date IS NULL
                LEFT JOIN member m ON l.member_id = m.id
                ORDER BY b.id";
                
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // This method retrieves all available books (those that have copies available for borrowing)
    public function getAvailableBooks(): array {
        $sql = "SELECT id, title, total_copies FROM book WHERE total_copies > 0 ORDER BY title";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // This method retrieves all borrowed books along with their borrowers and due dates
    public function getBorrowedBooks(): array {
        $sql = "SELECT 
                    b.title, 
                    m.name AS borrower_name, 
                    l.due_date 
                FROM loan l
                JOIN book b ON l.book_id = b.id
                JOIN member m ON l.member_id = m.id
                WHERE l.return_date IS NULL
                ORDER BY l.due_date ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // This method retrieves all memberships along with their card details
    public function getAllMemberships(): array {
        $sql = "SELECT m.id, m.name, m.type, c.card_number, c.issue_date, c.expiry_date, c.is_active 
                FROM member m 
                JOIN card c ON m.id = c.member_id 
                ORDER BY m.name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // This method retrieves all expired memberships (either expired or inactive)
    public function getExpiredMemberships(): array {
        $sql = "SELECT m.id, m.name, m.type, c.card_number, c.expiry_date, c.is_active 
                FROM member m 
                JOIN card c ON m.id = c.member_id 
                WHERE c.expiry_date < CURRENT_DATE OR c.is_active = 0
                ORDER BY c.expiry_date ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}