<?php 
declare(strict_types=1);

class Card {
    // Properties of the Card class
    private int $id;
    private string $cardNumber;
    private string $issueDate;
    private string $expiryDate;
    private bool $isActive;
    private int $memberId;

    // This constructor initializes the properties of the Card class
    public function __construct(string $cardNumber,string $issueDate,string $expiryDate,int $memberId,bool $isActive=true,int $id=0) {
        $this->cardNumber = $cardNumber;
        $this->issueDate = $issueDate;
        $this->expiryDate = $expiryDate;
        $this->memberId = $memberId;
        $this->isActive = $isActive;
        $this->id = $id;
    }
    // This method returns the ID of the card
    public function getId(): int {return $this->id;}
    // This method returns the card number
    public function getCardNumber(): string {return $this->cardNumber;}
    // This method returns the issue date of the card
    public function getIssueDate(): string {return $this->issueDate;}
    // This method returns the expiry date of the card
    public function getExpiryDate(): string {return $this->expiryDate;}
    // This method returns the member ID associated with the card
    public function getMemberId(): int {return $this->memberId;}
    // This method checks if the card is active
    public function isActive(): bool {return $this->isActive;}
}