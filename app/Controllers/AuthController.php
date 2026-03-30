<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\UserModel;
use App\Controllers\BaseController;
use App\Mail\Mailer; // CORREÇÃO: Usar o Mailer correto

class AuthController extends BaseController
{
    public function loginForm()
    {
        if (Auth::isLogged()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        $this->render('login', ['title' => 'Acesse sua conta']);
    }

    public function login()
    {
        header('Content-Type: application/json');
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Credenciais inválidas.']);
            return;
        }

        // ATUALIZADO: Passa nome, perfil e plano para a função de login
        $userName = $user['name'] ?? 'Usuário';
        $userRole = $user['role'] ?? 'user';
        $userPlan = $user['subscription_plan'] ?? 'none';
        Auth::login((int) $user['id'], $userName, $userRole, $userPlan);
        
        echo json_encode([
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'redirect' => BASE_URL . '/dashboard'
        ]);
    }

    public function registerForm()
    {
        if (Auth::isLogged()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        $this->render('register', ['title' => 'Crie sua conta']);
    }

    public function register()
    {
        header('Content-Type: application/json');

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Por favor, insira um e-mail válido.']);
            return;
        }
        
        if (strlen($password) < 6) {
             echo json_encode(['success' => false, 'message' => 'A senha deve ter pelo menos 6 caracteres.']);
            return;
        }

        $userModel = new UserModel();
        if ($userModel->findByEmail($email)) {
            echo json_encode(['success' => false, 'message' => 'Este e-mail já está em uso.']);
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $newUserId = $userModel->create([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash
        ]);

        if ($newUserId) {
            // Faz o login automático do usuário após o cadastro
            Auth::login((int)$newUserId, $name, 'user', 'none');
            echo json_encode([
                'success' => true,
                'message' => 'Cadastro realizado com sucesso!',
                'redirect' => BASE_URL . '/dashboard'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ocorreu um erro ao criar a sua conta. Tente novamente.']);
        }
    }

    public function logout()
    {
        Auth::logout();
        header('Location: ' . BASE_URL . '/');
        exit;
    }

    // --- MÉTODOS DE RESET DE SENHA (NOVOS) ---

    /**
     * Exibe o formulário "Esqueci a senha".
     */
    public function forgotPasswordForm()
    {
        $this->render('forgot-password', ['title' => 'Recuperar Senha']);
    }

    /**
     * Processa a solicitação de redefinição de senha.
     */
    public function sendPasswordResetLink()
    {
        header('Content-Type: application/json');
        $email = $_POST['email'] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Por favor, insira um e-mail válido.']);
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        $message = "Se um utilizador com este e-mail existir, um link de redefinição foi enviado.";

        if ($user) {
            try {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora

                $userModel->updatePasswordResetToken($user['id'], $token, $expires);

                $resetLink = BASE_URL . '/resetar-senha?token=' . $token;
                
                $body = "Olá, {$user['name']}.<br><br>";
                $body .= "Recebemos uma solicitação para redefinir sua senha. Se não foi você, ignore este e-mail.<br>";
                $body .= "Para redefinir sua senha, clique no link abaixo:<br>";
                $body .= "<a href='{$resetLink}'>{$resetLink}</a><br><br>";
                $body .= "Este link expira em 1 hora.";

                $mailer = new Mailer();
                $mailer->send($user['email'], $user['name'], 'Redefinição de Senha', $body);
                
            } catch (\Exception $e) {
                // Logar o erro real (ex: error_log($e->getMessage());)
                // Não informa o utilizador sobre a falha no envio por segurança
            }
        }
        
        // Resposta de sucesso genérica para evitar enumeração de utilizadores
        echo json_encode(['success' => true, 'message' => $message]);
    }

    /**
     * Processa a redefinição de senha a partir do token.
     */
    public function resetPassword()
    {
        // Esta parte normalmente teria um formulário (GET) e um processador (POST)
        // Para simplificar, vou assumir que a rota /resetar-senha lida com ambos
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $token = $_GET['token'] ?? '';
            if (empty($token)) {
                $_SESSION['error_message'] = 'Token inválido ou em falta.';
                header('Location: ' . BASE_URL . '/login');
                exit;
            }
            // Renderiza a view para inserir a nova senha
            $this->render('reset-password-form', ['title' => 'Crie uma Nova Senha', 'token' => $token]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (empty($token) || empty($password) || $password !== $passwordConfirm) {
                $_SESSION['error_message'] = 'Dados inválidos. As senhas não coincidem ou o token está em falta.';
                header('Location: ' . BASE_URL . '/resetar-senha?token=' . $token);
                exit;
            }

            if (strlen($password) < 6) {
                 $_SESSION['error_message'] = 'A senha deve ter pelo menos 6 caracteres.';
                header('Location: ' . BASE_URL . '/resetar-senha?token=' . $token);
                exit;
            }

            $userModel = new UserModel();
            $user = $userModel->findByResetToken($token);

            if (!$user) {
                $_SESSION['error_message'] = 'Token inválido ou expirado. Tente novamente.';
                header('Location: ' . BASE_URL . '/esqueci-a-senha');
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $userModel->updatePassword($user['id'], $passwordHash);
            
            // Limpa o token
            $userModel->updatePasswordResetToken($user['id'], null, null);

            $_SESSION['success_message'] = 'Senha redefinida com sucesso! Pode fazer login.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}
