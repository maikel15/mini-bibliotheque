<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends AbstractController
{
    private string $usersFile = __DIR__ . '/../../data/users.json';

    #[Route('/login', name: 'user_login', methods: ['GET', 'POST'])]
    public function login(Request $request, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password'); // ⚠️ À hasher en vrai projet

            $user = $this->getUserByEmail($email);
            if ($user && $user['password'] === $password) { // ⚠️ Remplace ceci par une vérification de hash sécurisé
                $session->set('user', [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'firstName' => $user['firstName'],
                    'role' => $user['role']
                ]);
                return $this->redirectToRoute('user_menu'); // 🔥 Redirection vers le menu
            }

            return $this->render('login.html.twig', ['error' => 'Identifiants incorrects.']);
        }

        return $this->render('login.html.twig');
    }

    #[Route('/menu', name: 'user_menu')]
    public function menu(Request $request): Response
    {
        $session = $request->getSession();
        $user = $session->get('user');

        if (!$user) {
            return $this->redirectToRoute('user_login');
        }

        return $this->render('menu.html.twig', ['user' => $user]);
    }

    #[Route('/logout', name: 'user_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('user');
        return $this->redirectToRoute('user_login');
    }

    private function getUserByEmail(string $email): ?array
    {
        if (!file_exists($this->usersFile)) {
            return null;
        }

        $data = file_get_contents($this->usersFile);
        $users = json_decode($data, true)['users'] ?? [];

        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }

        return null;
    }
}
