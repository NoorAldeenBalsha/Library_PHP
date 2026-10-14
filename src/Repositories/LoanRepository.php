<?php
declare(strict_types=1);

class LoanRepository
{
    private PDO $pdo;
    // Constructor to initialize the PDO instance
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // This method saves a new loan record to the database
    public function save(Loan $loan): bool {
        $sql = "INSERT INTO loan (member_id, book_id, loan_date, due_date, return_date) 
                VALUES (:member_id, :book_id, :loan_date, :due_date, :return_date)";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':member_id'   => $loan->getMemberId(),
            ':book_id'     => $loan->getBookId(),
            ':loan_date'   => $loan->getLoanDate(),
            ':due_date'    => $loan->getDueDate(),
            // The return date can be null if the book hasn't been returned yet, so we handle that case
            ':return_date' => $loan->getReturnDate() 
        ]);
    }

    // This method retrieves the count of active loans for a specific member
    public function getActiveLoansCountByMember(int $memberId): int {
        $sql = "SELECT COUNT(*) FROM loan WHERE member_id = :member_id AND return_date IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':member_id' => $memberId]);
        return (int) $stmt->fetchColumn();
    }

    // This method to fetches the due date of an active book loan
    public function getActiveLoanDueDate(int $memberId, int $bookId): ?string {
        $sql = "SELECT due_date FROM loan WHERE member_id = :member_id AND book_id = :book_id AND return_date IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':member_id' => $memberId,
            ':book_id' => $bookId
        ]);
        $dueDate = $stmt->fetchColumn();
        return $dueDate !== false ? (string)$dueDate : null;
    }

    // This method is to prevent borrowing two copies of the same book
    public function hasActiveLoanForBook(int $memberId, int $bookId): bool {
        $sql = "SELECT COUNT(*) FROM loan WHERE member_id = :member_id AND book_id = :book_id AND return_date IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':member_id' => $memberId,
            ':book_id' => $bookId
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // This method is for registering the return of the book
    public function markAsReturned(int $memberId, int $bookId, string $returnDate): bool {
        $sql = "UPDATE loan SET return_date = :return_date 
                WHERE member_id = :member_id AND book_id = :book_id AND return_date IS NULL";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':return_date' => $returnDate,
            ':member_id'   => $memberId,
            ':book_id'     => $bookId
        ]);
    }

    // This method to get all active loans for a specific member, including book titles and due dates
    public function getActiveBooksForMember(int $memberId): array {
        $sql = "SELECT b.title, l.due_date 
                FROM loan l 
                JOIN book b ON l.book_id = b.id 
                WHERE l.member_id = ? AND l.return_date IS NULL";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$memberId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}