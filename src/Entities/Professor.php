<?php 
declare(strict_types=1);
require_once __DIR__ . '/Member.php';
class Professor extends Member {
    // This constructor initializes the properties of the class Professor
    public function __construct( string $name,int $id=0,) {
        parent::__construct( $name, 'Professor', $id);
    }
    // This method returns the maximum number of books allowed for a professor
    public function getMaxBookAllowed(): int {return 5;}
    // This method returns the loan duration in days for a professor
    public function getLoanDurationDays(): int {return 30;}
    // This method returns the daily fine amount for a professor
    public function getDailyFineAmount(): int { return 200;}
}