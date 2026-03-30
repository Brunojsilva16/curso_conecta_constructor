<?php

use App\Core\Auth; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Plataforma de Cursos' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSS global -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="icon" type="image/png" href="<?= $faviconImg ?? (BASE_URL . '/assets/img/favicon_conecta.png') ?>">

    <!-- Estilos para as categorias (mantidos) -->
    <style>
        body {
            background-color: rgb(223 223 223);
        }

        .category-tag {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.25;
            text-transform: capitalize;
        }

        .category-platinum {
            background-color: #e5e7eb;
            /* gray-200 */
            color: #4b5563;
            /* gray-600 */
            border: 1px solid #d1d5db;
            /* gray-300 */
        }

        .category-premium {
            background-color: #fffbeb;
            /* amber-50 */
            color: #b45309;
            /* amber-700 */
            border: 1px solid #fde68a;
            /* amber-200 */
        }
    </style>

    <!-- CSS dinâmico (mantido) -->
    <?php if (isset($styles) && is_array($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($style) ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <?php
    // Define se a barra lateral deve ser mostrada
    $showSidebar = Auth::isLogged() && !in_array(trim($_GET['url'] ?? '', '/'), ['planos', 'login', 'esqueci-a-senha']);
    ?>

    <!-- 
      ESTRUTURA CORRIGIDA:
      1. A Sidebar é 'fixed', por isso é carregada, mas não faz parte do fluxo.
    -->
    <?php if ($showSidebar): ?>
        <!-- Barra Lateral Fixa (position: fixed) -->
        <?php require_once __DIR__ . '/partials/course_sidebar.php'; ?>
    <?php endif; ?>

    <!-- 
      2. A Coluna de Conteúdo é o único filho principal do 'body'.
         - Ela tem 'min-h-screen' e 'flex-col' para o footer ficar em baixo.
         - Ela recebe 'lg:ml-64' para não ficar por baixo da sidebar.
         - Como este é o 'div' de scroll principal, o 'sticky' do header vai funcionar.
    -->
    <div class="flex flex-col min-h-screen <?= $showSidebar ? 'lg:ml-64' : '' ?>">

        <!-- 1. HEADER (Filho direto do 'div' de scroll) -->
        <!-- O 'header.php' incluirá as classes 'sticky top-0 z-10' -->
        <?php require_once __DIR__ . '/partials/header.php'; ?>

        <!-- 2. CONTEÚDO PRINCIPAL -->
        <!-- 'flex-grow' empurra o footer para baixo -->
        <main class="flex-grow p-6">
            <?php
            if (!empty($pageContent)) echo $pageContent;
            ?>
        </main>

        <!-- 3. FOOTER -->
        <?php require_once __DIR__ . '/partials/footer.php'; ?>
    </div>

    <script type="module">
        <?= $showSidebar ?
            'import { Dropdown, initTWE, } from "tw-elements";
            initTWE({ Dropdown });' : '' ?>
    </script>

    <!-- JS dinâmico no rodapé (mantido) -->
    <?php if (isset($scripts) && is_array($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= htmlspecialchars($script) ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>