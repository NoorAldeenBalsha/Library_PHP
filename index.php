<?php
declare(strict_types=1);

$config = require_once __DIR__ . '/config.example.php';
require_once __DIR__ . '/src/autoload.php';

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

    echo "=============================================\n";
    echo "     COLLEGE LIBRARY MANAGEMENT SYSTEM     \n";
    echo "=============================================\n";
    echo "Welcome to the Librarian Interactive Dashboard.\n";

    $handle = fopen("php://stdin", "r");

    while (true) {
        echo "\n---------------------------------------------\n";
        echo "Please select an operation:\n";
        echo "[1] Show Reports List\n";
        echo "[2] Show Memberships & Cards List\n";
        echo "[3] Add a New Book\n";
        echo "[4] Borrow a Book\n";
        echo "[5] Return a Book\n";
        echo "[0] Exit System\n";
        echo "---------------------------------------------\n";
        echo "Enter your choice: ";
        $choice = trim(fgets($handle));
        switch ($choice) {
            case '1':
                echo "---------------------------------------------\n";
                echo "Please select an operation:\n";
                echo "[1] Comprehensive Library Report (All Books)\n";
                echo "[2] Available Books Only\n";
                echo "[3] Borrowed Books Only\n";
                echo "[4] Overdue Books Report (Alerts)\n";
                echo "[0] Back to Main Menu\n";
                echo "---------------------------------------------\n";
                echo "Enter your choice: ";
                $choiceReport = trim(fgets($handle));
                Switch ($choiceReport) {
                    case '1':
                        echo "\n===  GENERAL LIBRARY STATUS  ===\n";
                        $lines =$reportService->printLibraryStatus();
                        foreach ($lines as $line) {
                            echo $line . "\n";
                        }
                        break;
                    case '2':
                        echo "\n===  AVAILABLE BOOKS REPORT  ===\n";
                        $lines = $reportService->printAvailableBooks();
                        foreach ($lines as $line) {
                            echo $line . "\n";
                        }
                        break;
                    case '3':
                        echo "\n===  BORROWED BOOKS REPORT  ===\n";
                        $lines = $reportService->printBorrowedBooks();
                        foreach ($lines as $line) {
                            echo $line . "\n";
                        }
                        break;
                    case '4':
                        echo "\n===  OVERDUE BOOKS REPORT   ===\n";
                        $lines = $reportService->printOverdueReport();
                        foreach ($lines as $line) {
                            echo $line . "\n";
                        }
                        break;
                    case '0':
                        echo "\nReturning to Main Menu...\n";
                        break; 
                    default:
                        echo "\n Invalid choice.\n";
                        break;
                }
                break;
            case '2':
                echo "[1] Show All Memberships & Cards\n";
                echo "[2] Show Expired / Inactive Memberships\n";
                echo "[3] Renew a Membership Card\n";
                echo "[4] Deactivate a Membership Card\n";
                echo "[5] Register a New Member & Issue Card\n";
                echo "[0] Back to Main Menu\n";
                echo "---------------------------------------------\n";
                echo "Enter your choice: ";
                $choiceMembership = trim(fgets($handle));
                Switch ($choiceMembership) {
                    case '1':
                        echo "\n===  ALL MEMBERSHIPS  ===\n";
                        $lines = $reportService->printAllMemberships();
                        foreach ($lines as $line) {
                            echo $line . "\n";
                        }
                        break;
                    case '2':
                        $lines = $reportService->printExpiredMemberships();
                        foreach ($lines as $line) {
                            echo $line . "\n";
                        }
                        break;
                    case '3':
                        echo "Enter the Card Number to RENEW: ";
                        $cardNumber = trim(fgets($handle));
                        if (empty($cardNumber)) {
                            echo "\n Error: Card number cannot be empty.\n";
                            break;
                        }
                        try {
                            $result = $cardService->renewCard($cardNumber);
                            echo "\n SUCCESS: Card '$cardNumber' renewed until {$result['new_expiry_date']}.\n";
                        } catch (InvalidArgumentException $e) {
                            echo "\n RENEW FAILED: " . $e->getMessage() . "\n";
                        }
                        break;
                    case '4':
                        echo "Enter the Card Number to DEACTIVATE: ";
                        $cardNumber = trim(fgets($handle));
                        if (empty($cardNumber)) {
                            echo "\n Error: Card number cannot be empty.\n";
                            break;
                        }
                        try {
                            $cardService->deactivateCard($cardNumber);
                            echo "\n SUCCESS: Card '$cardNumber' has been deactivated.\n";
                        } catch (InvalidArgumentException $e) {
                            echo "\n DEACTIVATE FAILED: " . $e->getMessage() . "\n";
                        }
                        break;
                    case '5':
                        // Add New Member & Issue Card
                        echo "Enter the Member's Full Name: ";
                        $name = trim(fgets($handle));
                        
                        echo "Select Member Type ([1] for Student, [2] for Professor): ";
                        $typeChoice = trim(fgets($handle));
                        
                        if ($typeChoice === '1') {
                            $member = new Student($name);
                            $prefix = 'STU';
                        } elseif ($typeChoice === '2') {
                            $member = new Professor($name);
                            $prefix = 'PRO';
                        } else {
                            echo "\n Invalid type selected. Registration cancelled.\n";
                            break;
                        }
                        
                        // Save the member and get the generated ID
                        $memberId = $memberRepo->saveAndGetId($member);
                        
                        // Create a unique card number using the prefix, current year, and member ID
                        $uniqueCardNumber = $prefix . '-' . date('Y') . '-' .str_pad((string)$memberId, 4, '0', STR_PAD_LEFT);
                        
                        // Create a new Card object with the generated card number, issue date, expiry date, and member ID
                        $issueDate = date('Y-m-d');
                        $expiryDate = date('Y-m-d', strtotime('+1 year'));
                        
                        $newCard = new Card($uniqueCardNumber, $issueDate, $expiryDate, $memberId);
                        
                        // Save the card to the database
                        if ($cardRepo->save($newCard)) {
                            echo "\n SUCCESS: Member '$name' registered successfully.\n";
                            echo "  Issued Card Number: $uniqueCardNumber (Expires: $expiryDate)\n";
                        }
                        break;
                    case '0':
                        echo "\nReturning to Main Menu...\n";
                        break;
                    default:
                        echo "\n Invalid choice.\n";
                        break;
                }
                break;
            case '3':
                echo "Enter Book Title: ";
                $title = trim(fgets($handle));
                echo "Enter Author Name: ";
                $author = trim(fgets($handle));
                echo "Enter Year Published (e.g., 2024): ";
                $yearInput = trim(fgets($handle));
                echo "Enter Total Copies: ";
                $copiesInput = trim(fgets($handle));
                echo "Enter Section ID (e.g., 1 for Programming): ";
                $sectionInput = trim(fgets($handle));
               try {
                $bookService->addNewBook($title, $author, $yearInput, $copiesInput, $sectionInput);
                echo "\n SUCCESS: Book '$title' added to the library successfully.\n";
                }   
                catch (InvalidArgumentException $e) { echo "\n VALIDATION ERROR: " . $e->getMessage() . "\n";}
                catch (Throwable $e) {echo "\n SYSTEM ERROR: Could not save the book.\n";}
                break;
            case '4':
                // --- Operation: Borrow Book (Clean & Thin) ---
                echo "Enter Member's Card Number (e.g., STU-2026-0001): ";
                $cardNumber = trim(fgets($handle));
                echo "Enter Book ID: ";
                $bookInput = trim(fgets($handle));
                // Early input validation
                if (empty($cardNumber) || !ctype_digit($bookInput)) {
                    echo "\n Error: Invalid input. Card number is required and Book ID must be a number.\n";
                    break;
                }
                $bookId = (int)$bookInput;
                try {
                    // Send inputs to the service and let it handle the entire flow
                    $result = $loanService->processBorrow($cardNumber, $bookId);
                    echo "\n SUCCESS: Book '{$result['book_title']}' has been successfully borrowed by {$result['member_name']}.\n";
                } catch (InvalidArgumentException $e) {echo "\n VALIDATION ERROR: " . $e->getMessage() . "\n";
                } catch (Throwable $e) {echo "\n BORROW REJECTED: " . $e->getMessage() . "\n";}
                break;
            
            case '5':
                // --- Operation: Return Book ---
                echo "Enter Member's Card Number: ";
                $cardNumber = trim(fgets($handle));
                echo "Enter Book ID to Return: ";
                $bookId = (int) trim(fgets($handle));
                try {
                    $result = $loanService->processReturn($cardNumber, $bookId);
                    echo "\n SUCCESS: Book '{$result['book_title']}' returned successfully.\n";
                    if ($result['fine'] > 0) {
                        echo " FINE APPLIED: {$result['fine']} L.E due to a delay of {$result['days_late']} days.\n";
                        echo " The fine has been recorded on the member's account.\n";
                    } else {echo " Returned on time. No fines.\n";}
                
                    if (count($result['remaining_books']) > 0) {
                        echo "\n REMINDER: {$result['member_name']}, you still have the following books:\n";
                        foreach ($result['remaining_books'] as $remBook) {
                            echo "   - '{$remBook['title']}' (Due Date: {$remBook['due_date']})\n";
                        }
                    } else {echo "\n {$result['member_name']} has returned all borrowed books. Account is clear!\n";}
                    
                } catch (InvalidArgumentException $e) {echo "\n VALIDATION ERROR: " . $e->getMessage() . "\n";
                } catch (Throwable $e) {echo "\n RETURN FAILED: " . $e->getMessage() . "\n"; }
                break;

            case '0':
                echo "\nExiting Library System. Have a great day!\n";
                fclose($handle);
                exit;
            default:
                echo "\n Invalid choice. Please select a number from 0 to 10.\n";
                break;
            }
    }

} catch (Throwable $e) {
    echo "\n [System Error] " . $e->getMessage() . "\n";
}