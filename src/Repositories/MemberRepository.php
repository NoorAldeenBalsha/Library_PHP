<?php
declare(strict_types=1);

class MemberRepository
{
    private PDO $pdo;
    // Constructor to initialize the PDO instance
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // This method saves a new member to the database
    public function save(Member $member): bool {
        $sql = "INSERT INTO member (name, type) VALUES (:name, :type)";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':name' => $member->getName(),
            ':type' => $member->getType()
        ]);
    }

    // This method saves a new member and returns their generated ID
    public function saveAndGetId(Member $member): int {
        $sql = "INSERT INTO member (name, type) VALUES (:name, :type)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $member->getName(),
            ':type' => $member->getType()
        ]);
        
        return (int) $this->pdo->lastInsertId();
    }

    // This method searches for a member by ID and returns the correct child object (Student/Professor)
    public function findById(int $id): ?Member {
        $stmt = $this->pdo->prepare("SELECT * FROM member WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) return null;

        $type = $row['TYPE'] ?? '';

        if (stripos($type, 'Student') !== false) {
            return new Student($row['NAME'], (int)$row['id']);
        }else if (stripos($type, 'Professor') !== false) {
            return new Professor($row['NAME'], (int)$row['id']);
        }
        return null;
    }

    // This method to add a fine to a member's account by their ID
    public function addFine(int $memberId, float $amount): void {
        $stmt = $this->pdo->prepare("UPDATE member SET fines_balance = fines_balance + ? WHERE id = ?");
        $stmt->execute([$amount, $memberId]);
    }
}