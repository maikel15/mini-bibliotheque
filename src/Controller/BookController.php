<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    private string $booksFile = __DIR__ . '/../../data/books.json';

    #[Route('/books', name: 'book_list', methods: ['GET'])]
    public function index(): Response
    {
        $books = $this->getBooks();
        return $this->render('books/index.html.twig', ['books' => $books]);
    }

    #[Route('/books/add', name: 'book_add', methods: ['GET', 'POST'])]
    public function addBook(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $author = $request->request->get('author');
            $year = $request->request->get('year');
            $description = $request->request->get('description');
            $available = $request->request->get('available') ? true : false; // Récupérer la valeur de la disponibilité

            $books = $this->getBooks();
            $newBook = [
                'id' => count($books) + 1,
                'title' => $title,
                'author' => $author,
                'year' => $year,
                'description' => $description,
                'available' => $available,  // Assurer que la disponibilité est prise en compte
            ];
            $books[] = $newBook;
            $this->saveBooks($books);

            return $this->redirectToRoute('book_list');
        }

        return $this->render('books/add.html.twig');
    }

    #[Route('/books/{id}/edit', name: 'book_edit', methods: ['GET', 'POST'])]
    public function editBook(Request $request, int $id): Response
    {
        $books = $this->getBooks();
        foreach ($books as &$book) {
            if ($book['id'] == $id) {
                if ($request->isMethod('POST')) {
                    $book['title'] = $request->request->get('title');
                    $book['author'] = $request->request->get('author');
                    $book['year'] = $request->request->get('year');
                    $book['description'] = $request->request->get('description');
                    $book['available'] = $request->request->get('available') ? true : false; // Mise à jour de la disponibilité

                    $this->saveBooks($books);
                    return $this->redirectToRoute('book_list');
                }

                return $this->render('books/edit.html.twig', ['book' => $book]);
            }
        }
        return new Response('Livre non trouvé', Response::HTTP_NOT_FOUND);
    }

    #[Route('/books/{id}/delete', name: 'book_delete', methods: ['POST'])]
    public function deleteBook(int $id): Response
    {
        $books = $this->getBooks();
        $books = array_filter($books, fn($book) => $book['id'] != $id);
        $this->saveBooks($books);

        return $this->redirectToRoute('book_list');
    }

    #[Route('/books/{id}', name: 'book_show', methods: ['GET'])]
    public function showBook(int $id): Response
    {
        $books = $this->getBooks();
        foreach ($books as $book) {
            if ($book['id'] == $id) {
                return $this->render('books/show.html.twig', ['book' => $book]);
            }
        }
        return new Response('Livre non trouvé', Response::HTTP_NOT_FOUND);
    }

    private function getBooks(): array
    {
        if (!file_exists($this->booksFile)) {
            return [];
        }
        $data = file_get_contents($this->booksFile);
        return json_decode($data, true)['books'] ?? [];
    }

    private function saveBooks(array $books): void
    {
        file_put_contents($this->booksFile, json_encode(['books' => $books], JSON_PRETTY_PRINT));
    }
}
