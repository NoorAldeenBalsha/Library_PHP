<?php

declare(strict_types=1);

class Book
{
    // Properties of the Book class
    private int $id;
    private string $title;
    private string $author;
    private int $yearPublished;
    private int $totalCopies;
    private int $sectionId;
    // This constructor initializes the properties of the Book class
    public function __construct( string $title,string $author,int $yearPublished,int $totalCopies,int $sectionId,int $id = 0) {
        $this->title = $title;
        $this->author = $author;
        $this->yearPublished = $yearPublished;
        $this->totalCopies = $totalCopies;
        $this->sectionId = $sectionId;
        $this->id = $id;
    }
    // This method returns the ID of the book
    public function getId(): int { return $this->id; }
    // This method returns the title of the book
    public function getTitle(): string { return $this->title; }
    // This method returns the author of the book
    public function getAuthor(): string { return $this->author; }
    // This method returns the year the book was published
    public function getYearPublished(): int { return $this->yearPublished; }
    // This method returns the total number of copies available for the book
    public function getTotalCopies(): int { return $this->totalCopies; }
    // This method returns the section ID associated with the book
    public function getSectionId(): int { return $this->sectionId; }
}