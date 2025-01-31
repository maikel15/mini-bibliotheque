<?php

namespace App\Service;

class DataService
{
    private string $booksFile;
    private string $usersFile;
    private string $borrowingsFile;

    public function __construct()
    {
        $this->booksFile = __DIR__ . '/../../data/books.json';
        $this->usersFile = __DIR__ . '/../../data/users.json';
        $this->borrowingsFile = __DIR__ . '/../../data/borrowings.json';
    }

    // Lire un fichier JSON
    private function readJson(string $file): array
    {
        if (!file_exists($file)) {
            return [];
        }
        $content = file_get_contents($file);
        return json_decode($content, true) ?? [];
    }

    // Écrire dans un fichier JSON
    private function writeJson(string $file, array $data): void
    {
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }

    // Méthodes pour gérer les livres
    public function getBooks(): array
    {
        return $this->readJson($this->booksFile);
    }

    public function saveBooks(array $books): void
    {
        $this->writeJson($this->booksFile, $books);
    }

    // Méthodes pour gérer les utilisateurs
    public function getUsers(): array
    {
        return $this->readJson($this->usersFile);
    }

    public function saveUsers(array $users): void
    {
        $this->writeJson($this->usersFile, $users);
    }

    // Méthodes pour gérer les emprunts
    public function getBorrowings(): array
    {
        return $this->readJson($this->borrowingsFile);
    }

    public function saveBorrowings(array $borrowings): void
    {
        $this->writeJson($this->borrowingsFile, $borrowings);
    }
}
