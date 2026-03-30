<div class="container mx-auto px-4 py-8">
    <a href="<?= BASE_URL ?>/admin/courses" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">&larr; Voltar para Cursos</a>
    
    <?php
    // --- INÍCIO DA CORREÇÃO ---
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
    // --- FIM DA CORREÇÃO ---
    ?>
    
    
    <h1 class="text-3xl font-bold text-gray-800 mt-2">Gerenciar Conteúdo</h1>
    <h2 class="text-xl text-gray-600 mb-6"><?= htmlspecialchars($course['title']) ?></h2>

    <!-- Formulário para Adicionar Módulo -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h3 class="text-lg font-semibold mb-4">Adicionar Novo Módulo</h3>
        <form action="<?= BASE_URL ?>/admin/courses/<?= $course['id'] ?>/modules/create" method="POST">
            <div class="flex items-center gap-4">
                <input type="text" name="title" placeholder="Nome do Módulo" class="flex-grow mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" required>
                <button type="submit" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 transition duration-300">Criar Módulo</button>
            </div>
        </form>
    </div>

    <!-- Lista de Módulos e Lições -->
    <div class="space-y-6">
        <?php if (empty($modules)) : ?>
            <p class="text-center text-gray-500 py-8">Nenhum módulo encontrado. Comece adicionando um acima.</p>
        <?php else : ?>
            <?php foreach ($modules as $module) : ?>
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-4 border-b flex justify-between items-center">
                        <h4 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($module['title']) ?></h4>
                        <div>
                            <a href="#" class="text-sm text-blue-500 hover:underline mr-4">Editar</a>
                            <a href="#" class="text-sm text-red-500 hover:underline">Excluir</a>
                        </div>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($module['lessons'] as $lesson) : ?>
                            <li class="p-4 flex justify-between items-center">
                                <div>
                                    <span class="text-sm font-semibold uppercase text-indigo-600"><?= htmlspecialchars($lesson['content_type']) ?></span>
                                    <p class="text-gray-900"><?= htmlspecialchars($lesson['title']) ?></p>
                                </div>
                                <div>
                                    <a href="#" class="text-sm text-blue-500 hover:underline mr-4">Editar</a>
                                    <a href="#" class="text-sm text-red-500 hover:underline">Excluir</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                         <?php if (empty($module['lessons'])) : ?>
                             <li class="p-4 text-center text-gray-500">Nenhuma lição neste módulo.</li>
                        <?php endif; ?>
                    </ul>
                    <div class="p-4 bg-gray-50 rounded-b-lg">
                        <!-- CORREÇÃO: O link deve incluir o ID do curso e o ID do módulo -->
                        <a href="<?= BASE_URL ?>/admin/courses/<?= $course['id'] ?>/modules/<?= $module['id'] ?>/lessons/create" class="text-indigo-600 font-semibold hover:underline">+ Adicionar Lição</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>