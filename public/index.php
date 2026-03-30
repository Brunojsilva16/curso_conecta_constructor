<?php
// Ativa exibição de erros (em desenvolvimento)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// --- MODIFICAÇÃO IMPORTANTE ---
// No ambiente local (XAMPP), para chegar na pasta raiz do projeto (bk_conecta) 
// a partir de 'app.assistaconecta.com.br', precisamos subir apenas 1 nível.
// ATENÇÃO: Se for enviar para o servidor de produção e a estrutura for diferente, 
// você pode precisar voltar para 2.
define('ROOT_PATH', dirname(__DIR__, 1)); 

use App\Core\Router;

// =======================
// Definição do BASE_URL
// =======================
// Isso irá definir BASE_URL como '/app.assistaconecta.com.br' (ou o caminho correto)
$baseUrl = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
define("BASE_URL", $baseUrl);

// O VIEWS_PATH agora usará o ROOT_PATH correto 
define("VIEWS_PATH", ROOT_PATH . '/app/views');

// --- NOVA CONSTANTE ---
// Define o caminho ABSOLUTO para a pasta pública da aplicação (esta pasta)
// Usaremos isso para uploads e verificações de file_exists()
define("PUBLIC_APP_PATH", __DIR__);

// =======================
// Autoload (Composer)
// =======================
// Agora buscará /vendor/autoload.php apenas 1 nível acima
require_once ROOT_PATH . '/vendor/autoload.php';

// O .env também deve estar no ROOT_PATH
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

// =======================
// Definição das rotas
// =======================
$router = new Router();
// Agora buscará /app/Routes/routes.php apenas 1 nível acima
require ROOT_PATH . '/app/Routes/routes.php';

// =======================
// Despacho da rota
// =======================
$url    = $_GET['url'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($url, $method);