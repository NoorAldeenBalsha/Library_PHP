<?php
declare(strict_types=1);

class CardService
{
    private CardRepository $cardRepo;
    // Constructor to initialize the CardRepository instance
    public function __construct(CardRepository $cardRepo) {
        $this->cardRepo = $cardRepo;
    }
    
    // This method processes the renewal of a card for one year from today's date
    public function renewCard(string $cardNumber): array {
        $card = $this->cardRepo->findByCardNumber($cardNumber);
        if (!$card) { 
            throw new InvalidArgumentException("Card '$cardNumber' not found in the system.");
            }

        $newExpiryDate = date('Y-m-d', strtotime('+1 year'));
        
        $isRenewed = $this->cardRepo->renew($cardNumber, $newExpiryDate);

        return [
            'success' => $isRenewed,
            'new_expiry_date' => $newExpiryDate
        ];
    }

    // This method processes the deactivation of a card
    public function deactivateCard(string $cardNumber): bool {
        $card = $this->cardRepo->findByCardNumber($cardNumber);
        if (!$card) {
            throw new InvalidArgumentException("Card '$cardNumber' not found in the system.");
        }

        return $this->cardRepo->deactivate($cardNumber);
    }
}