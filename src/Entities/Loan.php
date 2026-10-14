<?php
declare(strict_types=1);

class Loan {
    // Properties of the Loan class
    private int $id;
    private int $memberId;
    private int $bookId;
    private string $loanDate;
    private string $dueDate;
    private ?string $returnDate;
    // This constructor initializes the properties of the Loan class
    public function __construct(int $memberId,int $bookId,string $loanDate,string $dueDate,?string $returnDate = null,int $id = 0) {
        $this->memberId = $memberId;
        $this->bookId = $bookId;
        $this->loanDate = $loanDate;
        $this->dueDate = $dueDate;
        $this->returnDate = $returnDate;
        $this->id = $id;
    }
    // This method returns the ID of the loan
    public function getId(): int {return $this->id;}
    // This method returns the member ID associated with the loan
    public function getMemberId(): int {return $this->memberId;}
    // This method returns the book ID associated with the loan
    public function getBookId(): int {return $this->bookId;}
    // This method returns the loan date
    public function getLoanDate(): string { return $this->loanDate;}
    // This method returns the due date for the loan
    public function getDueDate(): string {return $this->dueDate;}
    // This method returns the return date for the loan, which can be null if the book has not been returned yet
    public function getReturnDate(): ?string {return $this->returnDate;}
}