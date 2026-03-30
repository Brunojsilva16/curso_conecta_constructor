<div class="bg-gray-100 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">
            Escolha o plano ideal para sua jornada
        </h1>
        <p class="mt-4 text-xl text-gray-600">
            Acesso ilimitado a conteúdos que irão transformar sua carreira em psicologia.
        </p>

        <!-- Seletor Mensal/Anual -->
        <div class="mt-10 flex justify-center items-center space-x-4">
            <div id="billing-toggle" class="relative flex items-center bg-gray-200 rounded-full p-1 cursor-pointer">
                <button type="button" data-period="monthly" class="billing-option relative w-28 text-center text-sm font-semibold py-2 px-4 rounded-full transition-colors duration-300">Mensalmente</button>
                <button type="button" data-period="annually" class="billing-option relative w-28 text-center text-sm font-semibold py-2 px-4 rounded-full transition-colors duration-300">Anualmente</button>
            </div>
            <span id="economy-badge" class="ml-2 inline-block bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded-full" style="display: none;">ECONOMIZE 2 MESES</span>
        </div>
    </div>

    <!-- Cards de Planos -->
    <div class="mt-12 max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        
        <!-- Plano Essencial -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200 flex flex-col h-full">
            <h3 class="text-2xl font-bold text-gray-900">Plano Essencial</h3>
            <p class="mt-4 text-gray-600">Ideal para quem busca conhecimento focado e acesso a cursos específicos.</p>

            <div class="mt-8">
                <div class="flex items-baseline">
                    <span class="text-5xl font-extrabold tracking-tight text-gray-900" data-price-monthly="R$ 21,90" data-price-annually="R$ 219,00">R$ 21,90</span>
                    <span class="ml-2 text-xl font-semibold text-gray-500" data-period-monthly="/mês" data-period-annually="/ano">/mês</span>
                </div>
            </div>

            <ul class="mt-8 space-y-4 text-left">
                <li class="flex items-center space-x-3">
                    <svg class="flex-shrink-0 w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-gray-700">Acesso completo a <strong>2 cursos</strong> de sua escolha</span>
                </li>
                <li class="flex items-center space-x-3">
                     <svg class="flex-shrink-0 w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-gray-700">Certificado de conclusão</span>
                </li>
                <li class="flex items-center space-x-3">
                     <svg class="flex-shrink-0 w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-gray-700">Materiais de apoio em PDF</span>
                </li>
            </ul>
            
            <form action="/subscribe" method="POST" class="mt-auto pt-6">
                <input type="hidden" name="plan_name" value="essential">
                <input type="hidden" name="billing_period" id="essential_period" value="monthly">
                <button type="submit" class="w-full bg-gray-800 text-white text-center font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-gray-700 transition-colors duration-300">
                    Assinar Plano Essencial
                </button>
            </form>
        </div>

        <!-- Plano Premium (Destaque) -->
        <div class="bg-indigo-700 rounded-2xl shadow-2xl p-8 text-white relative border-4 border-indigo-400 flex flex-col h-full">
             <div class="absolute top-0 -translate-y-1/2 left-1/2 -translate-x-1/2">
                <span class="inline-flex items-center px-4 py-1 bg-indigo-200 text-indigo-800 text-sm font-bold rounded-full">MAIS POPULAR</span>
            </div>
            <h3 class="text-2xl font-bold">Plano Premium</h3>
            <p class="mt-4 opacity-80">A experiência completa para uma formação contínua e aprofundada.</p>

            <div class="mt-8">
                <div class="flex items-baseline">
                    <span class="text-5xl font-extrabold tracking-tight" data-price-monthly="R$ 27,90" data-price-annually="R$ 279,00">R$ 27,90</span>
                    <span class="ml-2 text-xl font-semibold opacity-80" data-period-monthly="/mês" data-period-annually="/ano">/mês</span>
                </div>
            </div>

            <ul class="mt-8 space-y-4 text-left">
                <li class="flex items-center space-x-3">
                    <svg class="flex-shrink-0 w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold">Acesso ilimitado aos cursos (Essential e Premium)</span>
                </li>
                 <li class="flex items-center space-x-3">
                    <svg class="flex-shrink-0 w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span>Certificados para todos os cursos concluídos</span>
                </li>
                <li class="flex items-center space-x-3">
                    <svg class="flex-shrink-0 w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span>Acesso à comunidade exclusiva de alunos</span>
                </li>
            </ul>

            <form action="/subscribe" method="POST" class="mt-auto pt-6">
                <input type="hidden" name="plan_name" value="premium">
                <input type="hidden" name="billing_period" id="premium_period" value="monthly">
                <button type="submit" class="w-full bg-white text-indigo-700 text-center font-bold py-3 px-6 rounded-lg shadow-md hover:bg-indigo-100 transition-colors duration-300">
                    Assinar Plano Premium
                </button>
            </form>
        </div>
    </div>
    
    <!-- SEÇÃO MOVIDA E REESTILIZADA -->
    <div class="max-w-4xl mx-auto mt-16">
        <div class="bg-gray-800 text-white rounded-2xl p-10 text-center shadow-lg">
            <h2 class="text-3xl font-bold">Ainda não tem certeza?</h2>
            <p class="mt-3 text-lg text-gray-300 max-w-2xl mx-auto">
                Crie uma conta gratuita para explorar nossos cursos básicos e decidir qual plano é o melhor para você.
            </p>
            <div class="mt-8">
                <a href="<?= BASE_URL ?>/cadastro" class="inline-block bg-white text-gray-800 font-bold py-3 px-8 rounded-lg shadow-md hover:bg-gray-200 transition-transform transform hover:scale-105 duration-300">
                    Cadastre-se Gratuitamente
                </a>
            </div>
        </div>
    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleContainer = document.getElementById('billing-toggle');
        const options = toggleContainer.querySelectorAll('.billing-option');
        const prices = document.querySelectorAll('[data-price-monthly]');
        const periods = document.querySelectorAll('[data-period-monthly]');
        const essentialPeriodInput = document.getElementById('essential_period');
        const premiumPeriodInput = document.getElementById('premium_period');
        const economyBadge = document.getElementById('economy-badge');
        
        let currentPeriod = 'monthly';

        function updateContent() {
            const isAnnual = currentPeriod === 'annually';

            // Mostra ou esconde o aviso de economia
            economyBadge.style.display = isAnnual ? 'inline-block' : 'none';

            prices.forEach(priceEl => {
                priceEl.textContent = isAnnual ? priceEl.dataset.priceAnnually : priceEl.dataset.priceMonthly;
            });

            periods.forEach(periodEl => {
                periodEl.textContent = isAnnual ? periodEl.dataset.periodAnnually : periodEl.dataset.periodMonthly;
            });
            
            const newPeriod = isAnnual ? 'annually' : 'monthly';
            if(essentialPeriodInput) essentialPeriodInput.value = newPeriod;
            if(premiumPeriodInput) premiumPeriodInput.value = newPeriod;

            // Atualiza a aparência dos botões
            options.forEach(opt => {
                if (opt.dataset.period === currentPeriod) {
                    opt.classList.add('bg-gray-800', 'text-white');
                    opt.classList.remove('bg-gray-200', 'text-gray-800');
                } else {
                    opt.classList.add('text-gray-800');
                    opt.classList.remove('bg-gray-800', 'text-white');
                }
            });
        }

        options.forEach(option => {
            option.addEventListener('click', () => {
                currentPeriod = option.dataset.period;
                updateContent();
            });
        });

        // Initialize on page load
        updateContent();
    });
</script>