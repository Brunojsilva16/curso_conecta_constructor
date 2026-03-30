<footer class="bg-white mt-auto border-t">
    <div class="container mx-auto px-6 py-4 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        
        <!-- Logo da Plataforma (Esquerda) -->
        <div class="flex-shrink-0">
            <a href="<?= BASE_URL ?>/">
                <img src="<?= BASE_URL ?>/assets/img/conecta2.png" alt="Logo Assista Conecta" class="h-8 w-auto">
            </a>
        </div>

        <!-- Texto de Copyright (Centro) -->
        <div class="text-center text-sm text-gray-600">
            &copy; <?= date('Y') ?> Plataforma de Cursos Assista Conecta. Todos os direitos reservados.
        </div>

        <!-- Créditos do Desenvolvedor (Direita) -->
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Desenvolvido por</span>
            <a href="https://github.com/Brunojsilva16" target="_blank" rel="noopener noreferrer" class="font-semibold text-gray-700 hover:text-indigo-600">
                Bruno J. Silva
            </a>
        </div>

    </div>
</footer>

<!-- JS dinâmico no footer -->
<?php if (!empty($pageScriptsFooter)): ?>
    <?php foreach ($pageScriptsFooter as $script): ?>
        <script src="<?= htmlspecialchars($script, ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
