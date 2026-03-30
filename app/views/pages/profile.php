<?php
// Verifica e exibe mensagens de sessão (sucesso ou erro)
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

<!-- ATUALIZADO: Layout do formulário ajustado para o novo container -->
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Configurações do Perfil</h1>
    <div class="bg-white p-8 rounded-lg shadow-xl">
        <form action="<?= BASE_URL ?>/perfil/atualizar" method="POST" class="space-y-6">

            <!-- Campo Nome (Editável) -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>

            <!-- Campo CPF (Editável) -->
            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                <input type="text" name="cpf" id="cpf" value="<?= htmlspecialchars($user['cpf'] ?? '') ?>"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" placeholder="000.000.000-00" maxlength="14">
            </div>

            <!-- Campo E-mail (Não Editável) -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email (Não Editável)</label>
                <input type="email" id="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                    class="mt-1 block w-full border border-gray-200 bg-gray-50 rounded-md shadow-sm p-3 cursor-not-allowed" readonly>
            </div>

            <!-- Botão de Submissão -->
            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition duration-300">
                Salvar Alterações
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cpfInput = document.getElementById('cpf');

    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
            
            // Limita o tamanho para 11 dígitos
            value = value.substring(0, 11);

            // Aplica a máscara
            if (value.length > 9) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            } else if (value.length > 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            } else if (value.length > 3) {
                value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            }
            
            e.target.value = value;
        });
    }
});
</script>

