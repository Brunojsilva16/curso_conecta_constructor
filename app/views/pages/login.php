<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div id="loginContainer" class="w-full max-w-sm bg-white p-6 rounded shadow-2xl transition-opacity duration-700 ease-in-out">
        
        <div class="flex flex-col items-center mb-6">
            <img src="<?= BASE_URL ?>/assets/img/conecta_free.png" alt="Logo" class="h-12 w-12 mb-2">
            <h1 class="text-3xl font-bold text-gray-800">Login</h1>
        </div>

        <div id="msg" class="mb-4 text-sm text-center"></div>

        <form id="formLogin" class="space-y-6">
            
            <div>
                <label class="block text-sm font-medium sr-only">Email</label>
                <div class="relative">
                    <input type="email" name="email" class="w-full border-b-2 border-gray-300 p-2 pl-10 focus:border-indigo-600 focus:outline-none placeholder-gray-500" placeholder="Usuário" value="premium@email.com" required>
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium sr-only">Senha</label>
                <div class="relative">
                    <input type="password" name="password" class="w-full border-b-2 border-gray-300 p-2 pl-10 focus:border-indigo-600 focus:outline-none placeholder-gray-500"  placeholder="Senha" value="123456" required>
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                    </span>
                </div>
            </div>

            <div class="text-right text-sm">
                <a href="<?= BASE_URL ?>/esqueci-a-senha" class="font-medium text-gray-600 hover:text-indigo-600">
                    Esqueci a Senha?
                </a>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 rounded-lg font-semibold hover:from-orange-600 hover:to-red-600 transition duration-150">
                Login
            </button>
            
            <div class="text-center pt-2">
                <a href="cadastro" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                    Cadastre-se agora
                </a>
            </div>

        </form>
    </div>
</div>

<script>
document.getElementById('formLogin').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const msgDiv = document.getElementById('msg');
    const container = document.getElementById('loginContainer');

    msgDiv.textContent = '';
    msgDiv.className = "mb-4 text-sm text-center";

    try {
        const response = await fetch('<?= BASE_URL ?>/login', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            msgDiv.textContent = "Acesso concedido. Redirecionando...";
            msgDiv.className = "mb-4 text-lg font-semibold text-green-700 text-center";
            
            form.style.display = 'none';
            container.classList.add('opacity-0');

            setTimeout(() => {
                window.location.href = result.redirect;
            }, 700);

        } else {
            msgDiv.textContent = result.message || "Credenciais inválidas. Verifique seu email e senha.";
            msgDiv.className = "mb-4 text-red-600 text-center";
        }
    } catch (error) {
        msgDiv.textContent = "Erro de rede. Verifique sua conexão.";
        msgDiv.className = "mb-4 text-red-600 text-center";
    }
});
</script>
