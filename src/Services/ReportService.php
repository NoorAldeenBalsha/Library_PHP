<?php
declare(strict_types=1);

class ReportService
{
    private ReportRepository $reportRepo;
    // Constructor to initialize the ReportRepository instance
    public function __construct(ReportRepository $reportRepo) {
        $this->reportRepo = $reportRepo;
    }

    // This method formats and returns the current status of the library, including available books and borrowers
    public function printLibraryStatus(): array {
        $status = $this->reportRepo->getLibraryStatus();
        if (empty($status)) {
            return ["No books found in the library system."];
        }
        $reportLines = []; 
        foreach ($status as $row) {
            $borrowerInfo = $row['borrower_name'] 
                ? "Borrowed by: {$row['borrower_name']} (Due: {$row['due_date']})" 
                : "Available in Library";
                
            $reportLines[] = "- Id: {$row['id']} | {$row['title']} (Copies: {$row['total_copies']}) -> $borrowerInfo";
        }
        return $reportLines;
    }
    
    // This method formats and returns a list of all available books in the library
    public function printAvailableBooks(): array {
        $books = $this->reportRepo->getAvailableBooks();
        if (empty($books)) {
            return ["No books are currently available."];
        }
        $reportLines = [];
        foreach ($books as $book) {
            $reportLines[] = "- Id: {$book['id']} | Title: '{$book['title']}' (Available Copies: {$book['total_copies']})";
        }
        return $reportLines;
    }

    // This method formats and returns a list of all borrowed books along with their borrowers and due dates
    public function printBorrowedBooks(): array {
        $loans = $this->reportRepo->getBorrowedBooks();
        if (empty($loans)) {
            return ["No books are currently borrowed."];
        }
        $reportLines = [];
        foreach ($loans as $loan) {
            $reportLines[] = "- '{$loan['title']}' -> Borrowed by: {$loan['borrower_name']} (Due: {$loan['due_date']})";
        }
        return $reportLines;
    }

    // This method formats and returns a report of all overdue books along with the number of days they are overdue
    public function printOverdueReport(): array {
        $overdueLoans = $this->reportRepo->getOverdueLoans();
        if (empty($overdueLoans)) {
            return ["Good news! There are no overdue books."];
        }
        $reportLines = [];
        foreach ($overdueLoans as $loan) {
            $reportLines[] = "[LATE] Book: '{$loan['book_title']}' | Borrower: {$loan['member_name']} | " . 
                              "Due: {$loan['due_date']} ({$loan['days_late']} days late!)";
        }
        return $reportLines;
    }

    // This method formats and returns a list of all memberships along with their card details and status
    public function printAllMemberships(): array {
        $memberships = $this->reportRepo->getAllMemberships();
        if(empty($memberships)) {
            return ["No memberships found in the system."];
        }
        $reportLines = [];
        foreach ($memberships as $m) {
            $status = ($m['is_active'] && strtotime($m['expiry_date']) >= time()) ? 'Active' : 'Inactive/Expired';
            $reportLines[] = "- {$m['id']} | {$m['name']} ({$m['type']}) | Card: {$m['card_number']} | Exp: {$m['expiry_date']} | Status: [$status]";
        }
        return $reportLines;
    }

    // This method formats and returns a list of all expired or inactive memberships
    public function printExpiredMemberships(): array {
        $memberships = $this->reportRepo->getExpiredMemberships();
        $reportLines = [];

        if (empty($memberships)) {
            return ["No expired memberships found."];
        }

        foreach ($memberships as $m) {
            $reportLines[] = "- {$m['id']} | {$m['name']} | Card: {$m['card_number']} | Expired On: {$m['expiry_date']}";
        }
        return $reportLines;
    }
}