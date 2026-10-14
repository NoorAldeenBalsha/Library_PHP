<?php
declare(strict_types=1);


$config = require_once __DIR__ . '/../../config.example.php';
require_once __DIR__ . '/../autoload.php';

echo "=== Comprehensive Test: Borrow & Return ===\n\n";

try {

    // For connection with DB
    $db = new Database($config);
    $pdo = $db->getConnection();
    // Get all Repository
    $reportRepo = new ReportRepository($pdo);
    $cardRepo = new CardRepository($pdo);
    $memberRepo = new MemberRepository($pdo); 
    $bookRepo = new BookRepository($pdo);
    $loanRepo = new LoanRepository($pdo);
    // Get all Service
    $loanService = new LoanService($loanRepo,$bookRepo,$cardRepo,$memberRepo);
    $bookService = new BookService($bookRepo);
    $cardService = new CardService($cardRepo);
    $reportService = new ReportService($reportRepo);


    $student = new Student('Noor Aldeen', 1);
    $book = new Book('Clean Architecture', 'Robert C. Martin', 2017, 3, 1, 1);
    $card = new Card('STU-2026-010', '2026-10-04', '2027-10-04', 1, true, 3);


    echo "[1] Seeding database...\n";
    $memberRepo->save($student);
    $bookRepo->save($book);
    $cardRepo->save($card);
    echo "    -> Student, Book, and Card inserted successfully.\n\n";
    
    echo "[2] Attempting to borrow the book...\n";
    $loanService->borrowBook($student, $book, $card);
    echo "    -> [Success] Book borrowed successfully!\n\n";

    echo "[3] Attempting to return the book for " . $student->getName() . "...\n";
    $result = $loanService->returnBook($student, $book);
    
    if ($result['success']) {
        echo "    -> [Success] Book returned successfully!\n";
        
        if ($result['fine'] > 0) {
            echo "    -> [Fine Alert] You are late by " . $result['days_late'] . " days.\n";
            echo "    -> [Fine Alert] Total fine to pay: " . $result['fine'] . " L.E\n";
        } else {
            echo "    -> [Info] Returned on time. No fines applied.\n";
        }
    }


echo "\n=== Testing Card Service ===\n";

$cardService = new CardService($cardRepo);
$cardNumberToTest = 'STU-2026-001';


echo "[1] Deactivating card...\n";
if ($cardService->deactivateCard($cardNumberToTest)) {
    echo "    -> [Success] Card is now INACTIVE.\n";
}


echo "[2] Renewing card...\n";
$renewResult = $cardService->renewCard($cardNumberToTest);

if ($renewResult['success']) {
    echo "    -> [Success] Card is now ACTIVE again.\n";
    echo "    -> [Info] New Expiry Date: " . $renewResult['new_expiry_date'] . "\n";
}



    echo "\n=== Testing Reports ===\n";
    $reportRepo = new ReportRepository($pdo);
    $reportService = new ReportService($reportRepo);


    $lateLoan = new Loan(1, 1, '2024-01-01', '2024-01-15');
    $loanRepo->save($lateLoan);
    $bookRepo->decrementCopies(1); 


    $reportService->printOverdueReport();
    $reportService->printLibraryStatus();

} catch (Throwable $e) {
    echo "\n[Error] " . $e->getMessage() . "\n";
}