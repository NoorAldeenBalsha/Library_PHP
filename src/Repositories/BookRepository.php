<?php
declare(strict_types=1);

class BookRepository
{
    private PDO $pdo;
    // Constructor to initialize the PDO instance
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // This method saves a Book entity to the database
    public function save(Book $book): bool {
        // The SQL query to insert a new book record into the 'books' table
        $sql = "INSERT INTO book (title, author, year_published, total_copies, section_id) 
                VALUES (:title, :author, :year_published, :total_copies, :section_id)";
        
        $stmt = $this->pdo->prepare($sql);

        // Execute the prepared statement with the book's data
        return $stmt->execute([
            ':title'          => $book->getTitle(),
            ':author'         => $book->getAuthor(),
            ':year_published' => $book->getYearPublished(),
            ':total_copies'   => $book->getTotalCopies(),
            ':section_id'     => $book->getSectionId()
        ]);
    }

    // This method decrements the total copies of a book in the database by 1
    public function decrementCopies(int $bookId): bool {
        $sql = "UPDATE book SET total_copies = total_copies - 1 WHERE id = :id AND total_copies > 0";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $bookId]);
    }

    // This method increments the total copies of a book in the database by 1
    public function incrementCopies(int $bookId): bool {
        $sql = "UPDATE book SET total_copies = total_copies + 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $bookId]);
    }

    // Search for a book by its ID and return a Book entity if found, otherwise return null
    public function findById(int $id): ?Book {
        $stmt = $this->pdo->prepare("SELECT * FROM book WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        
        return new Book($row['title'], $row['author'], (int)$row['year_published'], (int)$row['total_copies'], (int)$row['section_id'], (int)$row['id']);
    }

}