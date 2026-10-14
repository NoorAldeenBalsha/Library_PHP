<?php

declare(strict_types=1);


$config = require_once __DIR__ . '/../../config.example.php';

require_once __DIR__ . '/../autoload.php';

echo "=== Starting Tests ===\n\n";

try {
    $student = new Student('Amer',3);
    $professor = new Professor('Dr. Adnan',4);
    $book = new Book('Clean Architecture', 'Robert C. Martin', 2017, 3, 1);
    $card = new Card('STU-2026-001', '2026-10-03', '2027-10-03', 1);
    $loan = new Loan(1, 1, '2026-10-03', '2026-10-17');

    // First check: Database connection
    echo "Database Connection Test:\n";
    $db = new Database($config);
    $pdo = $db->getConnection();

    // Repository test
    echo "\nRepository Insertion Test:\n";

    // Save a student
    $memberRepo = new MemberRepository($pdo);
    if ($memberRepo->save($student)) {
        echo "[Success] Student inserted into DB.\n";
    }

    // Save a professor
    $memberRepo = new MemberRepository($pdo);
    if ($memberRepo->save($professor)) {
        echo "[Success] Professor inserted into DB.\n";
    }

    // Save a book
    $bookRepo = new BookRepository($pdo);
    if ($bookRepo->save($book)) {
        echo "[Success] Book inserted into DB.\n";
    }

    // Save a card
    $cardRepo = new CardRepository($pdo);
    if ($cardRepo->save($card)) {
        echo "[Success] Card inserted into DB.\n";
    }

    // Save a loan
    $loanRepo = new LoanRepository($pdo);
    if ($loanRepo->save($loan)) {
        echo "[Success] Loan inserted into DB.\n";
    }

    echo "\n=== All Tests Passed ===\n";

} catch (Throwable $e) {
    echo "[Error] Test failed: " . $e->getMessage() . "\n";
}