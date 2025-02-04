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
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $user = $session->get('user');

        if (!$user) {
            return $this->redirectToRoute('user_login');
        }

        return $this->render('books/index.html.twig', [
            'books' => $this->getBooks(),
            'user' => $user // Permet d'afficher le rôle dans Twig
        ]);
    }

    #[Route('/books/add', name: 'book_add', methods: ['GET', 'POST'])]
    public function addBook(Request $request): Response
    {
        $session = $request->getSession();
        $user = $session->get('user');

        if (!$user || $user['role'] !== 'admin') {
            return new Response('Accès interdit', Response::HTTP_FORBIDDEN);
        }

        if ($request->isMethod('POST')) {
            $books = $this->getBooks();
            $newBook = [
                'id' => count($books) + 1,
                'title' => $request->request->get('title'),
                'author' => $request->request->get('author'),
                'year' => $request->request->get('year'),
                'description' => $request->request->get('description'),
                'available' => $request->request->get('available') ? true : false,
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
        $session = $request->getSession();
        $user = $session->get('user');

        if (!$user || $user['role'] !== 'admin') {
            return new Response('Accès interdit', Response::HTTP_FORBIDDEN);
        }

        $books = $this->getBooks();
        foreach ($books as &$book) {
            if ($book['id'] == $id) {
                if ($request->isMethod('POST')) {
                    $book['title'] = $request->request->get('title');
                    $book['author'] = $request->request->get('author');
                    $book['year'] = $request->request->get('year');
                    $book['description'] = $request->request->get('description');
                    $this->saveBooks($books);
                    return $this->redirectToRoute('book_list');
                }
                return $this->render('books/edit.html.twig', ['book' => $book]);
            }
        }
        return new Response('Livre non trouvé', Response::HTTP_NOT_FOUND);
    }

    #[Route('/books/{id}/delete', name: 'book_delete', methods: ['POST'])]
    public function deleteBook(Request $request, int $id): Response
    {
        $session = $request->getSession();
        $user = $session->get('user');

        if (!$user || $user['role'] !== 'admin') {
            return new Response('Accès interdit', Response::HTTP_FORBIDDEN);
        }

        $books = array_filter($this->getBooks(), fn($book) => $book['id'] != $id);
        $this->saveBooks($books);

        return $this->redirectToRoute('book_list');
    }

    #[Route('/books/{id}', name: 'book_show', methods: ['GET'])]
    public function showBook(Request $request, int $id): Response
    {
        $session = $request->getSession();
        $user = $session->get('user');

        if (!$user) {
            return $this->redirectToRoute('user_login');
        }

        foreach ($this->getBooks() as $book) {
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
