<?php
use App\Core\Auth;
// O nome do usuário é recuperado da sessão
$userName = Auth::userName() ?? 'Usuário';
$isAdmin = Auth::isAdmin();
$currentUrl = trim($_GET['url'] ?? '', '/');

// Função auxiliar (adaptada da versão anterior)
function isSidebarLinkActive(string $linkUrl, string $currentUrl): bool
{
    if ($linkUrl === $currentUrl) {
        return true;
    }
    if ($linkUrl === 'home' && $currentUrl === '') {
        return true;
    }
    // Verifica se a URL atual começa com a URL do link
    if (strpos($currentUrl, $linkUrl) === 0 && $linkUrl !== 'home' && $linkUrl !== 'dashboard') {
        return true;
    }
    return false;
}
?>

<!-- 
  Barra de Navegação Lateral Fixa 
  - `hidden lg:flex`: A barra fica escondida em telas pequenas e vira um flex container (visível) em telas grandes (lg).
-->
<div class="fixed top-0 left-0 w-64 h-full bg-slate-900 text-white shadow-lg z-40 flex-col hidden lg:flex">
    
    <!-- Perfil do Usuário (Dropdown) -->
    <div class="p-6 border-b border-slate-700">
        <div class="relative">
            <!-- Botão que ativa o menu -->
            <button id="profileMenuButton" class="flex items-center w-full text-left space-x-4 focus:outline-none">
                <!-- Placeholder para a foto -->
                <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8 text-slate-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-bold text-white truncate"><?= htmlspecialchars($userName) ?></h2>
                    <span class="text-sm text-slate-400 hover:text-slate-200 transition-colors inline-flex items-center">
                        Minha Conta
                        <!-- Ícone Chevron (seta) -->
                        <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </span>
                </div>
            </button>

            <!-- Menu Dropdown (fica escondido) -->
            <!-- Estilizado para aparecer sobre o conteúdo escuro -->
            <div id="profileMenu" 
                 class="hidden absolute top-full mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="profileMenuButton">
                    
                    <a href="<?= BASE_URL ?>/perfil" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>Ver Perfil</span>
                    </a>

                    <a href="<?= BASE_URL ?>/perfil" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <span>Editar Perfil</span>
                    </a>

                    <a href="<?= BASE_URL ?>/perfil" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h3.75" />
                        </svg>
                        <span>Configurações</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Links de Navegação Principal -->
    <nav class="flex-grow p-4 space-y-2">
        <?php
            // Lógica para links ativos
            $dashboardActive = isSidebarLinkActive('dashboard', $currentUrl) ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:bg-slate-700 hover:text-white';
            $homeActive = (isSidebarLinkActive('home', $currentUrl) && !isSidebarLinkActive('dashboard', $currentUrl)) ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:bg-slate-700 hover:text-white';
        ?>

        <a href="<?= BASE_URL ?>/home" class="flex items-center px-4 py-2 rounded-md transition-colors <?= $homeActive ?>">
            Cursos
        </a>
        <a href="<?= BASE_URL ?>/dashboard" class="flex items-center px-4 py-2 rounded-md transition-colors <?= $dashboardActive ?>">
            Meus Cursos
        </a>
        <a href="#" class="flex items-center px-4 py-2 rounded-md transition-colors <?= $dashboardActive ?>">
            Meus Certificados
        </a>
        
        <!-- Links do Administrador -->
        <?php if ($isAdmin): ?>
            <hr class="my-4 border-slate-700">
            <h6 class="text-xs font-semibold text-slate-500 uppercase px-4 mb-2">Admin</h6>
            
            <?php
                $adminActive = isSidebarLinkActive('admin/courses', $currentUrl) ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:bg-slate-700 hover:text-white';
            ?>
            <a href="<?= BASE_URL ?>/admin/courses" class="flex items-center px-4 py-2 rounded-md transition-colors <?= $adminActive ?>">
                Gerenciar Cursos
            </a>
        <?php endif; ?>

        <!-- Links antigos (comentados ou removidos para limpar) -->
        <!-- <a href="#" class="flex items-center px-4 py-2 text-slate-300 hover:bg-slate-700 hover:text-white rounded-md transition-colors">
            Certificados e histórico
        </a> -->
    </nav>
    
    <!-- Botão de Sair -->
    <div class="p-4 border-t border-slate-700 mt-auto">
        <a href="<?= BASE_URL ?>/logout" class="flex items-center w-full px-4 py-2 text-slate-300 hover:bg-red-600 hover:text-white rounded-md transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span>Sair</span>
        </a>
    </div>
</div>

<!-- Script para controlar o dropdown -->
<script>
    // Usamos uma IIFE (Função Imediatamente Invocada) para não poluir o escopo global
    (function() {
        const button = document.getElementById('profileMenuButton');
        const menu = document.getElementById('profileMenu');
        
        // Verifica se os elementos existem antes de adicionar ouvintes
        if (button && menu) {
            
            // Ouve o clique no BOTÃO
            button.addEventListener('click', function(e) {
                e.stopPropagation(); // Impede que o clique se propague para o 'window'
                menu.classList.toggle('hidden'); // Alterna a visibilidade do menu
            });

            // Ouve cliques na JANELA (para fechar o menu se clicar fora)
            window.addEventListener('click', function(e) {
                // Se o menu NÃO estiver escondido E o clique NÃO foi no botão E NÃO foi dentro do menu
                if (!menu.classList.contains('hidden') && 
                    !button.contains(e.target) && 
                    !menu.contains(e.target)) {
                        
                    menu.classList.add('hidden'); // Esconde o menu
                }
            });
        }
    })();
</script>

