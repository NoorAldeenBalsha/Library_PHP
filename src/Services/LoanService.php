<?php
declare(strict_types=1);

class LoanService
{
    private LoanRepository $loanRepo;
    private BookRepository $bookRepo;
    private CardRepository $cardRepo;
    private MemberRepository $memberRepo;

    // constructor to initialize the repositories
    public function __construct(LoanRepository $loanRepo,BookRepository $bookRepo,CardRepository $cardRepo,MemberRepository $memberRepo) {
        $this->loanRepo = $loanRepo;
        $this->bookRepo = $bookRepo;
        $this->cardRepo = $cardRepo;
        $this->memberRepo = $memberRepo;
    }

    // This method handles the entire borrowing process from raw inputs, acting as the Service Facade
    public function processBorrow(string $cardNumber, int $bookId): array {
        // Fetch and validate Card
        $card = $this->cardRepo->findByCardNumber($cardNumber);
        if (!$card) {throw new InvalidArgumentException("Library Card '$cardNumber' not found.");}
        // Fetch and validate Member
        $member = $this->memberRepo->findById($card->getMemberId());
        if (!$member) {throw new Exception("Member details not found for this card.");}
        // Fetch and validate Book
        $book = $this->bookRepo->findById($bookId);
        if (!$book) {throw new InvalidArgumentException("Book with ID $bookId not found in the library.");}
        // Proceed with core borrowing logic
        $success = $this->borrowBook($member, $book, $card);
        if (!$success) { throw new Exception("System error: Could not complete the borrowing process."); }
        // Return a clean array for the UI
        return [
            'success' => true,
            'member_name' => $member->getName(),
            'book_title' => $book->getTitle()
        ];
    }

    // This method handles the borrowing of a book by a member using their library card
    private function borrowBook(Member $member, Book $book, Card $card): bool {
        // check if the member's card is active
        if (!$card->isActive()) {throw new CardExpiredException("Cannot borrow: The library card is not active.");}
        // check if the book is available for borrowing
        if ($book->getTotalCopies() <= 0) {throw new BookUnavailableException("Cannot borrow: No copies available for this book.");}
        // check if the member has reached the maximum allowed books
        $currentLoans = $this->loanRepo->getActiveLoansCountByMember($member->getId());
        if ($currentLoans >= $member->getMaxBookAllowed()) {
            throw new MaxBooksAllowedException("Cannot borrow: Member has reached the maximum allowed books (" . $member->getMaxBookAllowed() . ").");
        }
        // check if the member already has an active loan for this book
        if ($this->loanRepo->hasActiveLoanForBook($member->getId(), $book->getId())) {
            throw new Exception("Cannot borrow: You already have an active loan for this book.");
        }
        // check if all conditions are met, then proceed to create a new loan
        $loanDate = date('Y-m-d');
        
        // get the due date based on the member's loan duration
        $days = $member->getLoanDurationDays();
        $dueDate = date('Y-m-d', strtotime("+{$days} days"));
        $loan = new Loan($member->getId(), $book->getId(), $loanDate, $dueDate);
        // save the loan and decrement the book copies
        if ($this->loanRepo->save($loan)) {return $this->bookRepo->decrementCopies($book->getId());}
        return false;
    }

    // This method handles the return of a book by a member using their library card
    public function processReturn(string $cardNumber, int $bookId): array {
        // Check if the card exists and is active
        $card = $this->cardRepo->findByCardNumber($cardNumber);
        if (!$card) {throw new InvalidArgumentException("Library Card '$cardNumber' not found.");}
        // Check if the member exists
        $member = $this->memberRepo->findById($card->getMemberId());
        if (!$member) {throw new Exception("Member details not found for this card.");}
        // check if the book exists
        $book = $this->bookRepo->findById($bookId); 
        if (!$book) { throw new InvalidArgumentException("Book with ID $bookId not found in the library.");}
        // Call the returnBook method to handle the return process and fine calculation
        $result = $this->returnBook($member, $book);
        //  Record the fine in the member's account if applicable
        if ($result['fine'] > 0) {$this->memberRepo->addFine($member->getId(), $result['fine']);}
        //Get the list of remaining books for the member after returning this book
        $remainingBooks = $this->loanRepo->getActiveBooksForMember($member->getId());
        // Return an array containing the result of the return process, including any fines and remaining books
        return [
            'success' => true,
            'member_name' => $member->getName(),
            'book_title' => $book->getTitle(),
            'fine' => $result['fine'],
            'days_late' => $result['days_late'],
            'remaining_books' => $remainingBooks
        ];
    }
    
    // This method handles the return of a book by a member and calculates any applicable fines
    private function returnBook(Member $member, Book $book): array {
        $memberId = $member->getId();
        $bookId = $book->getId();
        // Check if the member has an active loan for this book
        if (!$this->loanRepo->hasActiveLoanForBook($memberId, $bookId)) {
            throw new LoanNotFoundException("Error: No active loan found for this book and member.");
        }
        // Get the due date of the active loan for this book and member
        $dueDateStr = $this->loanRepo->getActiveLoanDueDate($memberId, $bookId);
        // Calculate the fine based on the due date and the member's daily fine amount
        $today = new DateTime(date('Y-m-d'));
        $dueDate = new DateTime($dueDateStr);
        $fine = 0;
        $daysLate = 0;
        // Check if the book is returned late and calculate the fine accordingly
        if ($today > $dueDate) {
            $daysLate = $today->diff($dueDate)->days;
            $fine = $daysLate * $member->getDailyFineAmount();
        }
        // Record the return of the book in the loan repository and increment the available copies of the book
        $returnDate = $today->format('Y-m-d');
        $this->loanRepo->markAsReturned($memberId, $bookId, $returnDate);
        $this->bookRepo->incrementCopies($bookId);
        // Return an array containing the success status, days late, and fine amount for the return process
        return [
            'success' => true,
            'days_late' => $daysLate,
            'fine' => $fine
        ];
    }
}