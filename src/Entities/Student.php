<?php
declare(strict_types=1);

require_once __DIR__ . '/Member.php';

class Student extends Member {
    // This constructor initializes the properties of the class Student
    public function __construct( string $name,int $id=0,) {
        parent::__construct( $name, 'Student', $id);
    }
    // This method returns the maximum number of books allowed for a student
    public function getMaxBookAllowed(): int {return 3;}
    // This method returns the loan duration in days for a student
    public function getLoanDurationDays(): int {return 14;}
    // This method returns the daily fine amount for a student
    public function getDailyFineAmount(): int {return 500;}
}