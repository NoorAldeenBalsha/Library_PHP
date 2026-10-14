<?php 
declare(strict_types=1);
abstract class Member {
    // Those are the common properties for all member types
    protected int $id;
    protected string $name;
    protected string $type;

    // This constructor initializes the properties of the class Member
    public function __construct( string $name, string $type, int $id=0) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
    }
    // This method returns the member's ID
    public function getId(): int {return $this->id;}
    // This method returns the member's name
    public function getName(): string {return $this->name;}
    // This method returns the member's type
    public function getType(): string {return $this->type;}
    //Those are abstract methods that will be implemented by the subclasses 
    abstract public function getMaxBookAllowed(): int;
    abstract public function getLoanDurationDays(): int;
    abstract public function getDailyFineAmount(): int;
}