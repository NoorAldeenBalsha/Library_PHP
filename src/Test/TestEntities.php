<?php

declare(strict_types=1);


$config = require_once __DIR__ . '/../../config.example.php';

require_once __DIR__ . '/../autoload.php';

echo "=== Starting Tests ===\n\n";

try {
    // Student test
    $student = new Student('Noor Aldeen',1);
    echo "[Success] Student: " . $student->getName() . " |  Allowed: " . $student->getMaxBookAllowed() . " Books | Fine: " . $student->getDailyFineAmount() . " L.E\n";

    // Professor test
    $professor = new Professor('Dr. Ahmad',2);
    echo "[Success] Professor: " . $professor->getName() . " | Allowed: " . $professor->getMaxBookAllowed() . " Books | Fine: " . $professor->getDailyFineAmount() . " L.E\n";

    // Book test
    $book = new Book('Clean Architecture', 'Robert C. Martin', 2017, 3, 1);
    echo "[Success] Book: " . $book->getTitle() . " | Author: " . $book->getAuthor() . "\n";

    // Card test
    $card = new Card( 'STU-2026-001', '2026-10-03', '2027-10-03', 1);
    echo "[Success] Card: " . $card->getCardNumber() . " | Status: " . ($card->isActive() ? 'Active' : 'Expired') . "\n";

    // Loan test 
    $loan = new Loan(1, 1, '2026-10-03', '2026-10-17');
    echo "[Success] Loan: " . $loan->getBookId() . " | Due Date: " . $loan->getDueDate() . "\n";

    echo "\n=== All Tests Passed ===\n";

} catch (Throwable $e) {
    echo "[Error] Test failed: " . $e->getMessage() . "\n";
}