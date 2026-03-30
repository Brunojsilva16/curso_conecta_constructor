<?php
// Extrai os módulos da variável principal do curso para facilitar o uso e evitar erros.
$modules = $course['modules'] ?? [];

// NOVO: Prepara os dados das lições para o JavaScript
$lessonsData = [];
if (!empty($modules)) {
    foreach ($modules as $module) {
        if (!empty($module['lessons'])) {
            foreach ($module['lessons'] as $lesson) {
                // Adiciona a lição ao array JS, indexada pelo seu ID
                $lessonsData[$lesson['id']] = [
                    'id' => $lesson['id'],
                    'title' => $lesson['title'],
                    'type' => $lesson['content_type'],
                    'path' => $lesson['content_path'], // (ID do YouTube ou caminho do PDF)
                    'text' => $lesson['content_text'],
                    // 'duration' => $lesson['duration'] ?? 'N/A' // Adicionar duração se existir
                ];
            }
        }
    }
}
?>
<div class="flex flex-col lg:flex-row h-screen bg-gray-100 font-sans">

    <!-- Barra Lateral com a Lista de Aulas (Sidebar) --><aside class="w-full lg:w-80 bg-white shadow-lg flex-shrink-0 overflow-y-auto">
        <div class="p-6 border-b">
            <a href="<?= BASE_URL ?>/dashboard" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar para Meus Cursos
            </a>
            <h2 class="text-xl font-bold text-gray-800 mt-4"><?= htmlspecialchars($course['title']) ?></h2>
        </div>
        
        <div class="p-4">
            <div class="space-y-4">
                <?php if (!empty($modules)) : ?>
                    <?php foreach ($modules as $module) : ?>
                        <div>
                            <h3 class="font-bold text-gray-700 px-3 mb-2"><?= htmlspecialchars($module['title']) ?></h3>
                            <ul class="space-y-1">
                                <?php foreach ($module['lessons'] as $lesson) : ?>
                                    <li>
                                        <!-- NOVO: Adiciona data-lesson-id e a classe 'lesson-link' --><a href="#" class="lesson-link flex items-center p-3 text-sm rounded-md text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 transition-colors" data-lesson-id="<?= $lesson['id'] ?>">
                                            
                                            <!-- Ícone dinâmico baseado no tipo --><?php if ($lesson['content_type'] === 'video'): ?>
                                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <?php elseif ($lesson['content_type'] === 'pdf'): ?>
                                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <?php else: // 'text' ?>
                                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <?php endif; ?>

                                            <span class="flex-1"><?= htmlspecialchars($lesson['title']) ?></span>
                                            <!-- <span class="text-xs text-gray-500">10:45</span> --></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                     <p class="text-sm text-gray-500 px-3">Nenhum conteúdo adicionado a este curso ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <!-- Área de Conteúdo Principal --><main class="flex-1 flex flex-col">
        <!-- Cabeçalho do Conteúdo --><header class="bg-white shadow-sm p-4 flex justify-between items-center z-10">
            <div>
                 <!-- NOVO: IDs para atualizar o título --><h1 id="lesson-header-title" class="text-2xl font-bold text-gray-900">Bem-vindo ao curso!</h1>
                 <p id="lesson-header-subtitle" class="text-sm text-gray-600">Selecione uma aula para começar.</p>
            </div>
            <div>
                 <span class="text-sm font-semibold text-gray-700">Progresso: 0%</span>
                 <div class="w-40 bg-gray-200 rounded-full h-2.5 mt-1">
                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div>
                 </div>
            </div>
        </header>

        <!-- Player de Vídeo e Conteúdo --><div class="flex-1 p-6 lg:p-8 overflow-y-auto">
             <!-- Placeholder para quando nenhuma aula foi selecionada --><div id="lesson-content-placeholder" class="flex items-center justify-center h-full bg-gray-200 rounded-lg">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma aula selecionada</h3>
                    <p class="mt-1 text-sm text-gray-500">Escolha uma aula na barra lateral para começar a assistir.</p>
                </div>
            </div>

             <!-- O conteúdo da aula será carregado aqui por JavaScript --><div id="lesson-content-area" class="hidden">
                
                <!-- Container do Vídeo - AGORA COM TAMANHO FIXO --><div id="video-container" class="hidden relative overflow-hidden rounded-lg mb-6" style="padding-top: 56.25%;"> <!-- Proporção 16:9 --><iframe id="video-player" class="absolute inset-0 w-full h-full" src="" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                </div>

                <!-- Container do Texto --><div id="text-container" class="hidden prose max-w-none mt-4 text-gray-700 bg-white p-6 rounded-lg shadow">
                </div>

                <!-- Container do PDF --><div id="pdf-container" class="hidden mt-6">
                    <a id="pdf-download-link" href="" target="_blank" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Baixar PDF da Aula
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- NOVO: JavaScript para carregar conteúdo --><script>
document.addEventListener('DOMContentLoaded', function() {
    // Converte os dados PHP para um objeto JS
    const lessonsData = <?= json_encode($lessonsData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES); ?>;
    
    // Elementos do DOM
    const placeholder = document.getElementById('lesson-content-placeholder');
    const contentArea = document.getElementById('lesson-content-area');
    
    const lessonHeaderTitle = document.getElementById('lesson-header-title');
    const lessonHeaderSubtitle = document.getElementById('lesson-header-subtitle');

    // Containers de conteúdo
    const videoContainer = document.getElementById('video-container');
    const videoPlayer = document.getElementById('video-player');
    
    const textContainer = document.getElementById('text-container');
    
    const pdfContainer = document.getElementById('pdf-container');
    const pdfLink = document.getElementById('pdf-download-link');

    // Links das lições
    const lessonLinks = document.querySelectorAll('.lesson-link');
    let activeLink = null;

    lessonLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const lessonId = this.dataset.lessonId;
            const lesson = lessonsData[lessonId];

            if (lesson) {
                loadLessonContent(lesson);
                
                // Atualiza o link ativo
                if(activeLink) {
                    activeLink.classList.remove('bg-indigo-100', 'text-indigo-700', 'font-semibold');
                }
                this.classList.add('bg-indigo-100', 'text-indigo-700', 'font-semibold');
                activeLink = this;
            }
        });
    });

    function loadLessonContent(lesson) {
        // 1. Esconde o placeholder e mostra a área de conteúdo
        placeholder.classList.add('hidden');
        contentArea.classList.remove('hidden');

        // 2. Atualiza o cabeçalho
        lessonHeaderTitle.textContent = lesson.title;
        lessonHeaderSubtitle.textContent = `Você está assistindo: ${lesson.title}`;

        // 3. Reseta (esconde) todos os containers de conteúdo
        videoContainer.classList.add('hidden');
        textContainer.classList.add('hidden');
        pdfContainer.classList.add('hidden');
        
        // 4. Para o player de vídeo (se estiver a tocar)
        videoPlayer.src = '';

        // 5. Mostra o container correto baseado no tipo
        if (lesson.type === 'video') {
            videoPlayer.src = `https://www.youtube.com/embed/${lesson.path}`;
            videoContainer.classList.remove('hidden');
        } 
        else if (lesson.type === 'text') {
            // Converte quebras de linha (NL) para <br>
            textContainer.innerHTML = lesson.text.replace(/\r\n|\r|\n/g, '<br>');
            textContainer.classList.remove('hidden');
        } 
        else if (lesson.type === 'pdf') {
            pdfLink.href = `<?= BASE_URL ?>${lesson.path}`;
            pdfContainer.classList.remove('hidden');
        }
    }
});
</script>