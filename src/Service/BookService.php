<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

class BookService
{
    private $filePath;
    private $filesystem;
    private $serializer;

    public function __construct(Filesystem $filesystem, SerializerInterface $serializer)
    {
        $this->filePath = __DIR__ . '/../../data/books.json';
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;
    }

    public function getAllBooks(): array
    {
        if (!$this->filesystem->exists($this->filePath)) {
            return [];
        }
        $data = file_get_contents($this->filePath);
        return json_decode($data, true)['books'];
    }

    public function getBookById(int $id): ?array
    {
        $books = $this->getAllBooks();
        foreach ($books as $book) {
            if ($book['id'] == $id) {
                return $book;
            }
        }
        return null;
    }

    public function addBook(array $data): void
    {
        $books = $this->getAllBooks();
        $data['id'] = count($books) + 1;
        $books[] = $data;
        $this->saveBooks($books);
    }

    public function editBook(int $id, array $data): void
    {
        $books = $this->getAllBooks();
        foreach ($books as &$book) {
            if ($book['id'] == $id) {
                $book = array_merge($book, $data);
                break;
            }
        }
        $this->saveBooks($books);
    }

    public function deleteBook(int $id): void
    {
        $books = $this->getAllBooks();
        $books = array_filter($books, function ($book) use ($id) {
            return $book['id'] != $id;
        });
        $this->saveBooks($books);
    }

    private function saveBooks(array $books): void
    {
        $data = ['books' => array_values($books)];
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}