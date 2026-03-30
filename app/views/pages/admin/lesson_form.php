<div class="container mx-auto px-4 py-8">
    <a href="<?= BASE_URL ?>/admin/courses/<?= $course['id'] ?>/content" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">&larr; Voltar para o Conteúdo</a>
    
    <?php
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

    <!-- CORREÇÃO: A variável $title é passada pelo controller -->
    <h1 class="text-3xl font-bold text-gray-800 mt-2"><?= htmlspecialchars($title) ?></h1>
    <h2 class="text-xl text-gray-600 mb-6">Módulo: <?= htmlspecialchars($module['title']) ?></h2>

    <div class="bg-white p-6 rounded-lg shadow-xl max-w-2xl mx-auto">
        <!-- CORREÇÃO: Usar a variável $action passada pelo controller -->
        <form action="<?= htmlspecialchars($action) ?>" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ type: '<?= htmlspecialchars($lesson['content_type'] ?? 'video') ?>' }">

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Título da Lição</label>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($lesson['title'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>

            <div>
                <label for="content_type" class="block text-sm font-medium text-gray-700">Tipo de Conteúdo</label>
                <select name="content_type" id="content_type" x-model="type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="video">Vídeo (YouTube)</option>
                    <option value="text">Texto</option>
                    <option value="pdf">Documento (PDF)</option>
                </select>
            </div>

            <!-- Campo para Vídeo -->
            <div x-show="type === 'video'">
                <label for="content_path_video" class="block text-sm font-medium text-gray-700">ID do Vídeo do YouTube</label>
                <!-- CORREÇÃO: Preencher o valor se for edição de vídeo -->
                <input type="text" name="content_path_video" id="content_path_video" placeholder="Ex: dQw4w9WgXcQ" value="<?= ($lesson['content_type'] ?? '') === 'video' ? htmlspecialchars($lesson['content_path'] ?? '') : '' ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Cole apenas o ID do vídeo, não a URL completa. Ex: para 'youtube.com/watch?v=d...', use 'd...'.</p>
            </div>

            <!-- Campo para Texto -->
            <div x-show="type === 'text'">
                <label for="content_text" class="block text-sm font-medium text-gray-700">Conteúdo em Texto</label>
                <!-- CORREÇÃO: Preencher o valor se for edição de texto -->
                <textarea name="content_text" id="content_text" rows="8" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500"><?= ($lesson['content_type'] ?? '') === 'text' ? htmlspecialchars($lesson['content_text'] ?? '') : '' ?></textarea>
            </div>

            <!-- Campo para PDF -->
            <div x-show="type === 'pdf'">
                <label for="content_path_pdf" class="block text-sm font-medium text-gray-700">Ficheiro PDF</label>
                <input type="file" name="content_path_pdf" id="content_path_pdf" accept=".pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <!-- CORREÇÃO: Mostrar PDF atual se existir -->
                <?php if (($lesson['content_type'] ?? '') === 'pdf' && !empty($lesson['content_path'])): ?>
                    <p class="text-xs text-gray-500 mt-1">Ficheiro atual: <?= htmlspecialchars($lesson['content_path']) ?> (Deixe em branco para manter)</p>
                <?php endif; ?>
            </div>

            <div class="flex justify-end">
                <a href="<?= BASE_URL ?>/admin/courses/<?= $course['id'] ?>/content" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-300 mr-4">
                    Cancelar
                </a>
                <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-300">
                    Salvar Lição
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>