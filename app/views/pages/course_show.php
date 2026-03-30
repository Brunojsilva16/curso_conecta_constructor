<?php

use App\Core\Auth;
?>

<div class="bg-white p-8 rounded-lg shadow-xl max-w-6xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start">

        <!-- Coluna Esquerda: Imagem e Detalhes -->
        <div class="md:col-span-1">
            <?php
            $defaultImage = BASE_URL . '/assets/img/default_course.svg';
            $imageUrl = $defaultImage; // Valor Padrão

            // Verifica se a URL da imagem não está vazia E se o arquivo físico existe no servidor
            if (!empty($course['image_url']) && file_exists(PUBLIC_APP_PATH . $course['image_url'])) {
                $imageUrl = BASE_URL . htmlspecialchars($course['image_url']);
            }
            ?>
            <img src="<?= $imageUrl ?>" alt="Capa do curso <?= htmlspecialchars($course['title']) ?>" class="w-full rounded-lg shadow-md object-cover mb-8">

            <div class="space-y-4 text-sm text-gray-700 border-t pt-6">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <strong>Carga Horária:</strong>
                    <span class="ml-2"><?= htmlspecialchars($course['workload'] ?? 'N/A') ?>h</span>
                </div>
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-indigo-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.282-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.282.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <strong>Público-alvo:</strong>
                    <span class="ml-2 flex-1"><?= htmlspecialchars($course['target_audience'] ?? 'N/A') ?></span>
                </div>
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <strong>Formato:</strong>
                    <span class="ml-2"><?= htmlspecialchars($course['format'] ?? 'N/A') ?></span>
                </div>
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <strong>Nível:</strong>
                    <span class="ml-2"><?= htmlspecialchars($course['level'] ?? 'N/A') ?></span>
                </div>
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <strong>Modalidade:</strong>
                    <span class="ml-2"><?= htmlspecialchars($course['modality'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>


        <!-- Coluna Direita: Título, Descrição e Compra -->
        <div class="md:col-span-2 flex flex-col h-full">
            <div class="relative">
                <h1 class="text-4xl font-bold text-gray-900 tracking-tight pr-24"><?= htmlspecialchars($course['title']) ?></h1>
                <span class="absolute top-0 right-0 text-sm font-semibold px-3 py-1.5 rounded-full category-<?= strtolower($course['category']) ?>"><?= ucfirst($course['category']) ?></span>
            </div>

            <p class="mt-2 text-lg text-gray-600">com <?= htmlspecialchars($course['instructor']) ?></p>

            <h2 class="text-2xl font-semibold text-gray-800 mt-8 border-b pb-2">Sobre o curso</h2>
            <div class="mt-4 text-gray-700 space-y-4 text-base flex-grow">
                <p><?= nl2br(htmlspecialchars($course['description'])) ?></p>
            </div>

            <div class="mt-auto pt-8">
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <?php if ($userHasAccess) : ?>
                        <p class="text-center text-green-700 font-medium mb-4">Você já tem acesso a este conteúdo.</p>
                        <a href="<?= BASE_URL ?>/curso/<?= $course['id'] ?>/assistir" class="w-full flex justify-center items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Acessar Curso
                        </a>
                    <?php else : ?>
                        <p class="text-xl font-medium text-gray-600">Invista no seu conhecimento:</p>
                        <p class="text-5xl font-extrabold text-gray-900 mt-2">
                            R$ <?= htmlspecialchars(number_format($course['price'] ?? 0, 2, ',', '.')) ?>
                        </p>

                        <div class="mt-6">
                            <?php if (Auth::isLogged()) : ?>
                                <?php if (Auth::userPlan() === 'none' && in_array($course['category'], ['premium', 'platinum'])) : ?>
                                    <a href="<?= BASE_URL ?>/planos" class="w-full flex justify-center items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                                        Adquirir Plano para Acessar
                                    </a>
                                <?php else : ?>
                                    <form action="<?= BASE_URL ?>/checkout/pagamento" method="POST">
                                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                        <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700">
                                            Comprar Agora
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php else : ?>
                                <a href="<?= BASE_URL ?>/login" class="w-full flex justify-center items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-800 hover:bg-gray-900">
                                    Faça login para comprar
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>