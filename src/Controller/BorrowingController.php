<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BorrowingController extends AbstractController
{
    private string $borrowingsFile = __DIR__ . '/../../data/borrowings.json';

    #[Route('/borrowings', name: 'borrowing_list', methods: ['GET'])]
    public function index(): Response
    {
        $borrowings = $this->getBorrowings();
        return $this->render('borrowings/index.html.twig', ['borrowings' => $borrowings]);
    }
    

    #[Route('/borrowings/add', name: 'borrowing_add', methods: ['GET', 'POST'])]
    public function addBorrowing(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $userId = $request->request->get('user_id');
            $bookId = $request->request->get('book_id');
            $borrowDate = $request->request->get('borrow_date');
            $returnDate = $request->request->get('return_date');

            $borrowings = $this->getBorrowings();
            $newBorrowing = [
                'id' => count($borrowings) + 1,
                'user_id' => $userId,
                'book_id' => $bookId,
                'borrow_date' => $borrowDate,
                'return_date' => $returnDate,
            ];
            $borrowings[] = $newBorrowing;
            $this->saveBorrowings($borrowings);

            return $this->redirectToRoute('borrowing_list');
        }

        return $this->render('borrowings/add.html.twig');
    }

    #[Route('/borrowings/{id}/edit', name: 'borrowing_edit', methods: ['GET', 'POST'])]
    public function editBorrowing(Request $request, int $id): Response
    {
        $borrowings = $this->getBorrowings();
        foreach ($borrowings as &$borrowing) {
            if ($borrowing['id'] == $id) {
                if ($request->isMethod('POST')) {
                    $borrowing['user_id'] = $request->request->get('user_id');
                    $borrowing['book_id'] = $request->request->get('book_id');
                    $borrowing['borrow_date'] = $request->request->get('borrow_date');
                    $borrowing['return_date'] = $request->request->get('return_date');
                    $this->saveBorrowings($borrowings);
                    return $this->redirectToRoute('borrowing_list');
                }

                return $this->render('borrowings/edit.html.twig', ['borrowing' => $borrowing]);
            }
        }
        return new Response('Emprunt non trouvé', Response::HTTP_NOT_FOUND);
    }

    #[Route('/borrowings/{id}/delete', name: 'borrowing_delete', methods: ['POST'])]
    public function deleteBorrowing(int $id): Response
    {
        $borrowings = $this->getBorrowings();
        $borrowings = array_filter($borrowings, fn($borrowing) => $borrowing['id'] != $id);
        $this->saveBorrowings($borrowings);

        return $this->redirectToRoute('borrowing_list');
    }

    private function getBorrowings(): array
{
    $filePath = __DIR__ . '/../../data/borrowings.json';

    if (!file_exists($filePath)) {
        return [];
    }

    $data = file_get_contents($filePath);
    $borrowings = json_decode($data, true)['borrowings'] ?? [];

    return $borrowings;
}


    private function saveBorrowings(array $borrowings): void
    {
        file_put_contents($this->borrowingsFile, json_encode(['borrowings' => $borrowings], JSON_PRETTY_PRINT));
    }
}
