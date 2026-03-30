// Scripts para o Modal

const modal = document.getElementById('interest-modal');
const modalContent = document.getElementById('interest-modal-content');
const modalCourseTitle = document.getElementById('modal-course-title');
const modalFormContainer = document.getElementById('modal-form-container');
const showFormBtn = document.getElementById('show-form-btn');
let originalModalContent = modalFormContainer.innerHTML;

function openInterestModal(courseTitle) {
    modalCourseTitle.textContent = courseTitle;
    modal.classList.remove('hidden');
}

function closeInterestModal() {
    modal.classList.add('hidden');
    // Reseta o formulário para o estado inicial
    modalFormContainer.innerHTML = originalModalContent;
}

function showInterestForm() {
    const formHTML = `
            <form action="<?= BASE_URL ?>/register-interest" method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Seu Nome</label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Seu Melhor E-mail</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                 <input type="hidden" name="course_title" value="${modalCourseTitle.textContent}">
                <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition duration-300">
                    ENVIAR
                </button>
            </form>
        `;
    modalFormContainer.innerHTML = formHTML;
}

// Fecha o modal se clicar fora do conteúdo
modal.addEventListener('click', function (event) {
    if (event.target === modal) {
        closeInterestModal();
    }
});
