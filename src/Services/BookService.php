<?php
declare(strict_types=1);

class BookService
{
    private BookRepository $bookRepo;
    // constructor to initialize the BookRepository instance
    public function __construct(BookRepository $bookRepo) {
        $this->bookRepo = $bookRepo;
    }
    // This method to create a new book
    public function addNewBook(string $title, string $author, string $yearInput, string $copiesInput, string $sectionInput): bool  {
        // Those for title checks
        if (empty(trim($title))) {throw new InvalidArgumentException("Title cannot be empty.");}
        if (is_numeric($title)) {throw new InvalidArgumentException("Book title cannot be just numbers.");}
        //========================================================================================
        // Those for author checks
        if (empty(trim($author))) {throw new InvalidArgumentException("Author cannot be empty.");}
        if (preg_match('/[0-9]/', $author)) {throw new InvalidArgumentException("Author name cannot contain numbers.");}
        //========================================================================================
        // This for year checks
        if (!ctype_digit(trim($yearInput)) || strlen(trim($yearInput)) !== 4) {
        throw new InvalidArgumentException("Invalid year. Must be exactly 4 digits.");
        }
        //========================================================================================
        // This for copies checks
        if (!ctype_digit(trim($copiesInput)) || (int)$copiesInput <= 0) {
            throw new InvalidArgumentException("Copies must be a valid number greater than 0.");
        }
        //========================================================================================
        // This for section checks
        if (!ctype_digit(trim($sectionInput))) {throw new InvalidArgumentException("Section ID must be a number.");}
        //========================================================================================
        $book = new Book(trim($title), trim($author), (int)$yearInput, (int)$copiesInput, (int)$sectionInput);

        return $this->bookRepo->save($book);
    }
}