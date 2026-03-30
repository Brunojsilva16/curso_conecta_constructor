<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\UserModel;
use App\Controllers\BaseController;

class ProfileController extends BaseController
{
    public function __construct()
    {
        // Protege a área do perfil
        if (!Auth::isLogged()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    /**
     * Exibe o formulário de perfil do usuário logado.
     */
    public function index()
    {
        $userId = Auth::userId();
        $userModel = new UserModel();
        $user = $userModel->findById($userId);

        if (!$user) {
            // Se o usuário não for encontrado (embora esteja logado), força logout
            Auth::logout();
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $this->render('profile', [
            'title' => 'Meu Perfil',
            'user' => $user,
        ]);
    }

    /**
     * Processa a atualização do perfil (apenas nome e telefone).
     */
    public function update()
    {
        // Redireciona se não for POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/perfil');
            exit;
        }

        $userId = Auth::userId();
        $name = $_POST['name'] ?? '';
        $cpf = $_POST['cpf'] ?? null;

        if (empty($name)) {
            $_SESSION['error_message'] = 'O nome é obrigatório.';
            header('Location: ' . BASE_URL . '/perfil');
            exit;
        }

        $userModel = new UserModel();
        
        // Dados a serem atualizados: apenas nome e CPF (telefone não estava no BD, usando CPF)
        $data = [
            'name' => $name,
            'cpf' => $cpf 
            // Adicionar 'telefone' se ele for incluído no database.sql no futuro
        ];

        try {
            $success = $userModel->updateProfile($userId, $data);
            
            if ($success) {
                $_SESSION['success_message'] = 'Perfil atualizado com sucesso!';
            } else {
                $_SESSION['error_message'] = 'Nenhuma alteração foi feita ou ocorreu um erro.';
            }
        } catch (\Exception $e) {
            error_log("Erro ao atualizar perfil: " . $e->getMessage());
            $_SESSION['error_message'] = 'Erro interno ao tentar atualizar o perfil.';
        }

        header('Location: ' . BASE_URL . '/perfil');
        exit;
    }
}
