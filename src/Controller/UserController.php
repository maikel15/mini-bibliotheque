<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private string $usersFile;

    public function __construct()
    {
        $this->usersFile = __DIR__ . '/../../data/users.json';
    }

    #[Route('/users', name: 'user_list', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $currentUser = $session->get('user');
        
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            return new Response('Accès refusé', Response::HTTP_FORBIDDEN);
        }

        return $this->render('users/index.html.twig', [
            'users' => $this->getUsers()
        ]);
    }

    #[Route('/users/add', name: 'user_add', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        $session = $request->getSession();
        $currentUser = $session->get('user');

        if (!$currentUser || $currentUser['role'] !== 'admin') {
            return new Response('Accès refusé', Response::HTTP_FORBIDDEN);
        }

        if ($request->isMethod('POST')) {
            $users = $this->getUsers();
            $newUser = [
                'id' => $this->getNewUserId($users),
                'username' => $request->request->get('username'),
                'email' => $request->request->get('email'),
                'password' => password_hash($request->request->get('password'), PASSWORD_DEFAULT),
                'firstName' => $request->request->get('firstName'),
                'lastName' => $request->request->get('lastName'),
                'role' => $request->request->get('role', 'user')
            ];

            $users[] = $newUser;
            $this->saveUsers($users);

            return $this->redirectToRoute('user_list');
        }

        return $this->render('users/add.html.twig');
    }

    #[Route('/users/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $session = $request->getSession();
        $currentUser = $session->get('user');

        if (!$currentUser || $currentUser['role'] !== 'admin') {
            return new Response('Accès refusé', Response::HTTP_FORBIDDEN);
        }

        $users = $this->getUsers();
        $userIndex = array_search($id, array_column($users, 'id'));

        if ($userIndex === false) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        if ($request->isMethod('POST')) {
            $updatedUser = $users[$userIndex];
            $updatedUser['email'] = $request->request->get('email');
            $updatedUser['firstName'] = $request->request->get('firstName');
            $updatedUser['lastName'] = $request->request->get('lastName');
            $updatedUser['role'] = $request->request->get('role');

            if ($password = $request->request->get('password')) {
                $updatedUser['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $users[$userIndex] = $updatedUser;
            $this->saveUsers($users);

            return $this->redirectToRoute('user_list');
        }

        return $this->render('users/edit.html.twig', [
            'user' => $users[$userIndex]
        ]);
    }

    #[Route('/users/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $users = $this->getUsers();
        $users = array_filter($users, fn($user) => $user['id'] !== $id);
        $this->saveUsers(array_values($users));

        return $this->redirectToRoute('user_list');
    }

    private function getUsers(): array
    {
        if (!file_exists($this->usersFile)) {
            return [];
        }
        
        $data = file_get_contents($this->usersFile);
        $users = json_decode($data, true)['users'] ?? [];

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw $this->createNotFoundException('Erreur lors du décodage des utilisateurs');
        }

        return $users;
    }

    private function saveUsers(array $users): void
    {
        $jsonData = json_encode(['users' => $users], JSON_PRETTY_PRINT);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw $this->createNotFoundException('Erreur lors de l\'encodage des utilisateurs');
        }

        file_put_contents($this->usersFile, $jsonData);
    }

    private function getNewUserId(array $users): int
    {
        $maxId = 0;
        foreach ($users as $user) {
            $maxId = max($maxId, $user['id']);
        }

        return $maxId + 1;
    }
}
