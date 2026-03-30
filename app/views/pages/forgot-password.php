<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div id="resetContainer" class="w-full max-w-sm bg-white p-6 rounded shadow-2xl">
        
        <div class="flex flex-col items-center mb-6">
            <img src="<?= BASE_URL ?>/assets/img/conecta_free.png" alt="Logo" class="h-12 w-12 mb-2">
            <h1 class="text-3xl font-bold text-gray-800">Crie uma Nova Senha</h1>
        </div>

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

        <form action="<?= BASE_URL ?>/resetar-senha" method="POST" class="space-y-6">
            
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

            <div>
                <label for="password" class="block text-sm font-medium sr-only">Nova Senha</label>
                <div class="relative">
                    <input type="password" name="password" id="password" class="w-full border-b-2 border-gray-300 p-2 pl-10 focus:border-indigo-600 focus:outline-none placeholder-gray-500" placeholder="Nova Senha (mín. 6 caracteres)" required>
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                    </span>
                </div>
            </div>
            
            <div>
                <label for="password_confirm" class="block text-sm font-medium sr-only">Confirme a Nova Senha</label>
                <div class="relative">
                    <input type="password" name="password_confirm" id="password_confirm" class="w-full border-b-2 border-gray-300 p-2 pl-10 focus:border-indigo-600 focus:outline-none placeholder-gray-500" placeholder="Confirme a Nova Senha" required>
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                    </span>
                </div>
            </div>


            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 rounded-lg font-semibold hover:from-orange-600 hover:to-red-600 transition duration-150">
                Salvar Nova Senha
            </button>
            
            <div class="text-center pt-2">
                <a href="<?= BASE_URL ?>/login" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                    Voltar para o Login
                </a>
            </div>

        </form>
    </div>
</div>
