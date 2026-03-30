<!-- O HTML permanece o mesmo -->
<div id="dashboard-view" class="min-h-screen">

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-8">

        <h2 class="text-3xl font-bold text-slate-900">Meus Cursos</h2>
        <p class="mt-1 text-slate-600">Bem-vindo(a) de volta, <strong class="font-medium"><?= htmlspecialchars($userName ?? 'Visitante') ?></strong>! Aqui estão os seus cursos.</p>

        <?php if (!empty($userCourses)) : ?>
            <!-- Seção de Cursos em Andamento -->
            <div id="in-progress-section" class="mt-10">
                <h3 class="text-2xl font-semibold text-slate-800 border-b pb-2 mb-6">Em Andamento</h3>
                <div id="in-progress-courses-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                </div>
            </div>

            <!-- Seção de Cursos Finalizados -->
            <div id="finished-section" class="mt-12">
                <h3 class="text-2xl font-semibold text-slate-800 border-b pb-2 mb-6">Finalizados</h3>
                <div id="finished-courses-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                </div>
            </div>

        <?php else : ?>
            <div class="text-center py-12 bg-gray-50 rounded-lg mt-8">
                <h3 class="text-xl font-semibold text-gray-700">Nenhum curso encontrado</h3>
                <p class="text-gray-500">Parece que ainda não se inscreveu em nenhum curso.</p>
                <a href="<?= BASE_URL ?>/" class="mt-4 inline-block bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-300">
                    Explorar Cursos
                </a>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- Script para a lógica dinâmica -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const coursesData = <?= json_encode($userCourses ?? []) ?>;
        const inProgressContainer = document.getElementById('in-progress-courses-container');
        const finishedContainer = document.getElementById('finished-courses-container');
        const inProgressSection = document.getElementById('in-progress-section');
        const finishedSection = document.getElementById('finished-section');

        function renderCourses() {
            if (!inProgressContainer || !finishedContainer) return;

            inProgressContainer.innerHTML = '';
            finishedContainer.innerHTML = '';

            const coursesInProgress = coursesData.filter(c => c.user_status === 'Em Andamento');
            const coursesFinished = coursesData.filter(c => c.user_status === 'Finalizado');

            const createCourseCard = (course) => {
                const isFinished = course.user_status === 'Finalizado';
                // Garante que o BASE_URL seja aplicado corretamente e usa uma imagem padrão se a URL estiver vazia
                const defaultImageUrl = `<?= BASE_URL ?>/assets/img/default_course.svg`;
                const imageUrl = course.image_url ? `<?= BASE_URL ?>${course.image_url}` : defaultImageUrl;

                return `
                <div class="bg-white rounded-xl shadow-lg border border-slate-200 flex flex-col justify-between transition-transform hover:-translate-y-1 overflow-hidden">
                    <a href="<?= BASE_URL ?>/curso/${course.id}">
                        <img src="${imageUrl}" alt="Capa do curso ${course.title}" class="w-full h-40 object-cover" onerror="this.src='${defaultImageUrl}'">
                    </a>
                    <div class="p-6 flex flex-col flex-grow">
                        <!-- Título e Status -->
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 mb-4">${course.title}</h3>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full mb-4 inline-block ${isFinished ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                                ${course.user_status}
                            </span>
                        </div>
                        
                        <!-- Bloco de Informações (Movido para o final) -->
                        <div class="space-y-2 mb-6 mt-auto"> <!-- mt-auto aqui para empurrar este bloco para baixo -->
                            <div class="flex items-center text-sm text-slate-600">
                                <svg class="w-4 h-4 mr-2 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>${course.workload}h</span>
                            </div>
                            <div class="flex items-center text-sm text-slate-600">
                                <svg class="w-4 h-4 mr-2 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span>${course.instructor}</span>
                            </div>
                        </div>
                        
                        <!-- Botões -->
                        <div class="flex items-center space-x-2">
                            ${isFinished ? `
                                <button class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-md text-sm transition-colors" data-action="reopen" data-id="${course.id}">Reabrir Curso</button>
                            ` : `
                                <a href="<?= BASE_URL ?>/curso/${course.id}/assistir" class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md text-sm transition-colors text-center">Continuar</a>
                                <button class="w-1/2 bg-white hover:bg-slate-100 text-slate-700 font-semibold py-2 px-4 rounded-md border border-slate-300 text-sm transition-colors" data-action="finish" data-id="${course.id}">Finalizar</button>
                            `}
                        </div>
                    </div>
                </div>`;
            };

            if (coursesInProgress.length > 0) {
                inProgressContainer.innerHTML = ''; // Limpa antes de adicionar
                coursesInProgress.forEach(course => inProgressContainer.innerHTML += createCourseCard(course));
                inProgressSection.style.display = 'block';
            } else {
                inProgressContainer.innerHTML = '<p class="text-slate-500 col-span-3">Não há cursos em andamento.</p>';
            }

            if (coursesFinished.length > 0) {
                finishedContainer.innerHTML = ''; // Limpa antes de adicionar
                coursesFinished.forEach(course => finishedContainer.innerHTML += createCourseCard(course));
                finishedSection.style.display = 'block';
            } else {
                finishedContainer.innerHTML = '<p class="text-slate-500 col-span-3">Nenhum curso finalizado ainda.</p>';
            }
        }

        async function updateStatusOnServer(courseId, newStatus) {
            try {
                const response = await fetch('<?= BASE_URL ?>/progress/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        course_id: courseId,
                        status: newStatus
                    })
                });
                const result = await response.json();

                if (!result.success) {
                    alert('Ocorreu um erro ao salvar o progresso. Tente recarregar a página.');
                }
            } catch (error) {
                console.error('Erro de rede:', error);
                alert('Erro de rede ao salvar o progresso. Verifique a sua conexão.');
            }
        }

        document.getElementById('dashboard-view').addEventListener('click', function(e) {
            const button = e.target.closest('button[data-action]');
            if (!button) return;

            const courseId = parseInt(button.dataset.id);
            const action = button.dataset.action;
            const course = coursesData.find(c => c.id === courseId);
            if (!course) return;

            const newStatus = action === 'finish' ? 'Finalizado' : 'Em Andamento';

            course.user_status = newStatus;
            renderCourses();
            updateStatusOnServer(courseId, newStatus);
        });

        renderCourses();
    });
</script>