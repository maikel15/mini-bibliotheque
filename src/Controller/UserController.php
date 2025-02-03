<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private string $usersFile = __DIR__ . '/../../data/users.json';

    #[Route('/users', name: 'user_list', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->getUsers(); // Récupère les utilisateurs
        return $this->render('users/index.html.twig', ['users' => $users]);
    }
    

    #[Route('/users/add', name: 'user_add', methods: ['GET', 'POST'])]
    public function addUser(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $email = $request->request->get('email');

            $users = $this->getUsers();
            $newUser = [
                'id' => count($users) + 1,
                'username' => $username,
                'email' => $email,
            ];
            $users[] = $newUser;
            $this->saveUsers($users);

            return $this->redirectToRoute('user_list');
        }

        return $this->render('users/add.html.twig');
    }

    #[Route('/users/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function editUser(Request $request, int $id): Response
    {
        $users = $this->getUsers();
        foreach ($users as &$user) {
            if ($user['id'] == $id) {
                if ($request->isMethod('POST')) {
                    $user['username'] = $request->request->get('username');
                    $user['email'] = $request->request->get('email');
                    $this->saveUsers($users);
                    return $this->redirectToRoute('user_list');
                }

                return $this->render('users/edit.html.twig', ['user' => $user]);
            }
        }
        return new Response('Utilisateur non trouvé', Response::HTTP_NOT_FOUND);
    }

    #[Route('/users/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(int $id): Response
    {
        $users = $this->getUsers();
        $users = array_filter($users, fn($user) => $user['id'] != $id);
        $this->saveUsers($users);

        return $this->redirectToRoute('user_list');
    }

    #[Route('/users/{id}', name: 'user_show', methods: ['GET'])]
    public function showUser(int $id): Response
    {
        $users = $this->getUsers();
        foreach ($users as $user) {
            if ($user['id'] == $id) {
                return $this->render('users/show.html.twig', ['user' => $user]);
            }
        }
        return new Response('Utilisateur non trouvé', Response::HTTP_NOT_FOUND);
    }

    private function getUsers(): array
    {
        $filePath = __DIR__ . '/../../data/users.json';
    
        if (!file_exists($filePath)) {
            return [];
        }
    
        $data = file_get_contents($filePath);
        $users = json_decode($data, true)['users'] ?? [];
    
        return $users;
    }
    

    private function saveUsers(array $users): void
    {
        file_put_contents($this->usersFile, json_encode(['users' => $users], JSON_PRETTY_PRINT));
    }
}
