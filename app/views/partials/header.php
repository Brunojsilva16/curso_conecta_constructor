<?php
use App\Core\Auth;
// Verifica a variável global definida no layout.php
$isFixed = $GLOBALS['isFixedLayout'] ?? false;
?>
<!-- A classe 'sticky' é adicionada condicionalmente -->
<header class="bg-white shadow-md z-30 sticky top-0">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <div>
            <!-- O link da plataforma agora fica visível apenas em telas maiores quando a sidebar está ativa -->
            <a href="<?= BASE_URL ?>/">
                <img src="<?= BASE_URL ?>/assets/img/conecta2.png" alt="Logo Assista Conecta" class="h-8 w-auto <?php if ($isFixed) echo 'lg:hidden'; ?>">
            </a>
        </div>
        <nav class="space-x-6 flex items-center sticky top-0" >
            <a href="<?= BASE_URL ?>/" class="text-gray-600 hover:text-indigo-600">Cursos</a>
            <a href="<?= BASE_URL ?>/planos" class="text-gray-600 hover:text-indigo-600">Planos</a>
            
            <?php if (Auth::isLogged()): ?>
                
                <?php if (Auth::isAdmin()): ?>
                    <a href="<?= BASE_URL ?>/admin/courses" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                        Admin Cursos
                    </a>
                <?php endif; ?>

                <?php // ATUALIZADO: Se a sidebar NÃO estiver visível, mostra os links de navegação do usuário aqui. ?>
                <?php if (!$isFixed): ?>
                    <a href="<?= BASE_URL ?>/dashboard" class="text-gray-600 hover:text-indigo-600 font-semibold">
                        Meus Cursos
                    </a>
                    <a href="<?= BASE_URL ?>/logout" class="bg-red-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-600 transition duration-300">
                        Sair
                    </a>
                <?php endif; ?>

            <?php else: ?>
                <a href="<?= BASE_URL ?>/login" class="bg-gray-800 text-white font-semibold py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-300">Acesse sua conta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
