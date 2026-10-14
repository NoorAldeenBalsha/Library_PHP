<?php
declare(strict_types=1);

class CardRepository
{
    private PDO $pdo;
    // Constructor to initialize the PDO instance
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // This method saves a new card to the database
    public function save(Card $card): bool {
        $sql = "INSERT INTO card (card_number, issue_date, expiry_date, is_active, member_id) 
                VALUES (:card_number, :issue_date, :expiry_date, :is_active, :member_id)";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':card_number' => $card->getCardNumber(),
            ':issue_date'  => $card->getIssueDate(),
            ':expiry_date' => $card->getExpiryDate(),
            // Convert boolean to integer for database storage
            ':is_active'   => (int) $card->isActive(), 
            ':member_id'   => $card->getMemberId()
        ]);
    }

    // This method renews a card by updating its expiry date and setting it as active
    public function renew(string $cardNumber, string $newExpiryDate): bool {
        $sql = "UPDATE card SET expiry_date = :expiry_date, is_active = 1 WHERE card_number = :card_number";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':expiry_date' => $newExpiryDate,
            ':card_number' => $cardNumber
        ]);
    }

    // This method deactivates a card by its number
    public function deactivate(string $cardNumber): bool {
        $sql = "UPDATE card SET is_active = 0 WHERE card_number = :card_number";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':card_number' => $cardNumber]);
    }
    
    // This method to search for a card by number 
    public function findByCardNumber(string $cardNumber): ?Card {
        $stmt = $this->pdo->prepare("SELECT * FROM card WHERE card_number = ? LIMIT 1");
        $stmt->execute([$cardNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) return null;
        
        return new Card(
            $row['card_number'], 
            $row['issue_date'], 
            $row['expiry_date'], 
            (int)$row['member_id'], 
            (bool)$row['is_active'], 
            (int)$row['id']
        );
    }

}