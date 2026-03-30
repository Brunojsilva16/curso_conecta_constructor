<div class="container mx-auto px-4 py-8">
    <?php
    // Exibe mensagens de sessão (sucesso ou erro)
    if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <p><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        </div>
    <?php unset($_SESSION['success_message']);
    endif;

    if (isset($_SESSION['error_message'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
            <p><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        </div>
    <?php unset($_SESSION['error_message']);
    endif;
    ?>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Gerenciar Cursos</h1>
        <a href="<?= BASE_URL ?>/admin/courses/create" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-300">
            Adicionar Novo Curso
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Título
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap"><?= htmlspecialchars($course['title']) ?></p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <span class="relative inline-block px-3 py-1 font-semibold leading-tight <?= $course['status'] === 'published' ? 'text-green-900' : 'text-yellow-900' ?>">
                            <span aria-hidden class="absolute inset-0 <?= $course['status'] === 'published' ? 'bg-green-200' : 'bg-yellow-200' ?> opacity-50 rounded-full"></span>
                            <span class="relative"><?= $course['status'] === 'published' ? 'Publicado' : 'Rascunho' ?></span>
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <a href="<?= BASE_URL ?>/admin/courses/<?= $course['id'] ?>/content" class="text-green-600 hover:text-green-900 font-semibold">Conteúdo</a>
                        <a href="<?= BASE_URL ?>/admin/courses/edit/<?= $course['id'] ?>" class="text-indigo-600 hover:text-indigo-900 ml-4">Editar</a>
                        <a href="<?= BASE_URL ?>/admin/courses/delete/<?= $course['id'] ?>" class="text-red-600 hover:text-red-900 ml-4" onclick="return confirm('Tem certeza que deseja excluir este curso?');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>