<?php

namespace App\Controllers;

/**
 * Classe base para todos os controladores da aplicação.
 * Centraliza a lógica de renderização das views dentro do layout principal.
 */
class BaseController
{
    /**
     * Renderiza uma view dentro do layout principal.
     * * @param string $view O nome do arquivo da view (ex: 'login' para login.php).
     * @param array $options Dados a serem passados para a view (incluindo 'title', 'styles', 'scripts', etc.).
     */
protected function render(string $view, array $options = [])
    {
        // Valores padrão que podem ser sobrescritos
        $pageTitle = $options['title'] ?? 'Plataforma de Cursos';
        $faviconImg = $options['favicon'] ?? (defined('BASE_URL') ? BASE_URL . '/assets/img/favicon_conecta.png' : '/assets/img/favicon_conecta.png');

        $pageStyles = $options['styles'] ?? [];
        $pageScriptsHeader = $options['scriptsHeader'] ?? [];
        $pageScriptsFooter = $options['scriptsFooter'] ?? [];
        
        // NOVO: Permite que uma página use a largura total
        $fullWidthLayout = $options['fullWidthLayout'] ?? false;

        // Extrai os dados para serem usados na view
        extract($options);

        // 1. Captura o conteúdo da view específica
        ob_start();
        
        // Define os possíveis caminhos para a view
        $phpPath = VIEWS_PATH . "/pages/{$view}.php";
        $phtmlPath = VIEWS_PATH . "/pages/{$view}.phtml";

        // Verifica qual extensão existe e faz o require (Prioridade para .php)
        if (file_exists($phpPath)) {
            require $phpPath;
        } elseif (file_exists($phtmlPath)) {
            require $phtmlPath;
        } else {
            // Se a view não existir em nenhum dos formatos, mostra um erro claro
            echo "<p>Erro: View não encontrada. O sistema procurou por:<br> - {$phpPath}<br> - {$phtmlPath}</p>";
        }
        
        $pageContent = ob_get_clean();

        // 2. Renderiza o layout principal, que encapsula $pageContent
        require VIEWS_PATH . '/layout.php'; // ALTERADO: Usa a constante
    }
}
