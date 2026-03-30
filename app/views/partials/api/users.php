<?php

// CORREÇÃO: O caminho para o autoload estava incorreto
require __DIR__ . '/../../../../vendor/autoload.php';

// CORREÇÃO: O Model chama-se UserModel
use App\Models\UserModel;

// Define o cabeçalho da resposta como JSON
header("Content-Type: application/json; charset=UTF-8");
// Permite requisições de qualquer origem (CORS) - ajuste se necessário para produção
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// O navegador envia uma requisição OPTIONS "preflight" para verificar as permissões CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
// CORREÇÃO: Instanciar a classe correta
$userModel = new UserModel();

// Função para enviar respostas JSON
function json_response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

try {
    switch ($method) {
        case 'GET':
            // NOTA: UserModel não tem um método getAll(). 
            // Isto falhará a menos que o adicione ao UserModel.
            // Vou assumir que deveria existir.
            // $users = $userModel->getAll(); 
            // json_response($users);
            json_response(['message' => 'Método GET não implementado no UserModel.'], 501);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (empty($data['name']) || empty($data['email']) || empty($data['password_hash'])) {
                json_response(['message' => 'Nome, email e password_hash são obrigatórios.'], 400);
            }
            $newUserId = $userModel->create($data);
            $newUser = $userModel->findById($newUserId);
            json_response($newUser, 201);
            break;

        case 'PUT':
            // Extrai o ID da URL, ex: /api/users.php?id=123
            if (!isset($_GET['id'])) {
                json_response(['message' => 'ID do usuário não fornecido.'], 400);
            }
            $id = (int)$_GET['id'];
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['name']) || empty($data['email'])) {
                json_response(['message' => 'Nome e email são obrigatórios.'], 400);
            }
            
            // NOTA: UserModel não tem um método update() genérico.
            // Tem updateProfile() e updatePassword().
            if ($userModel->findById($id)) {
                // $userModel->update($id, $data); // Este método não existe
                $userModel->updateProfile($id, ['name' => $data['name']]); // Apenas um exemplo
                $updatedUser = $userModel->findById($id);
                json_response($updatedUser);
            } else {
                json_response(['message' => 'Usuário não encontrado.'], 404);
            }
            break;

        case 'DELETE':
            // Extrai o ID da URL
            if (!isset($_GET['id'])) {
                json_response(['message' => 'ID do usuário não fornecido.'], 400);
            }
            $id = (int)$_GET['id'];
            
            // NOTA: UserModel não tem um método delete().
            if ($userModel->findById($id)) {
                // $userModel->delete($id); // Este método não existe
                json_response(['message' => 'Método DELETE não implementado no UserModel.'], 501);
            } else {
                json_response(['message' => 'Usuário não encontrado.'], 404);
            }
            break;

        default:
            json_response(['message' => 'Método não suportado.'], 405);
            break;
    }
} catch (Exception $e) {
    // Captura qualquer exceção (ex: erro de banco de dados) e retorna um erro 500
    json_response(['message' => 'Ocorreu um erro interno no servidor.', 'error' => $e->getMessage()], 500);
}