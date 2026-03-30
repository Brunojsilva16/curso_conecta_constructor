<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\CourseModel;
use App\Models\ContentModel;
use App\Controllers\BaseController;

class AdminController extends BaseController
{
    private ContentModel $contentModel;
    private CourseModel $courseModel;

    public function __construct()
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        $this->contentModel = new ContentModel();
        $this->courseModel = new CourseModel();
    }

    // Validação dos dados do curso
    private function validateCourseData(array $postData): ?string
    {
        $requiredFields = [
            'title' => 'Título',
            'description' => 'Descrição',
            'instructor' => 'Instrutor',
            'workload' => 'Carga Horária',
            'target_audience' => 'Público-alvo',
            'format' => 'Formato',
            'level' => 'Nível',
            'modality' => 'Modalidade',
            'category' => 'Categoria',
            'status' => 'Status'
        ];

        foreach ($requiredFields as $field => $fieldName) {
            // Verifica se a chave existe antes de tentar aceder-lhe
            if (!isset($postData[$field]) || empty(trim($postData[$field]))) {
                return "O campo '{$fieldName}' é obrigatório.";
            }
        }
        return null;
    }

    // Upload da imagem do curso
    private function handleImageUpload(): array
    {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['path' => null, 'error' => null];
        }

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
             // Erro 1 (UPLOAD_ERR_INI_SIZE) ou Erro 2 (UPLOAD_ERR_FORM_SIZE) 
             // indicam que o ficheiro é demasiado grande (configuração do servidor)
            if ($_FILES['image']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['image']['error'] === UPLOAD_ERR_FORM_SIZE) {
                    return ['path' => null, 'error' => 'Erro: O ficheiro é demasiado grande. Verifique o limite de upload do servidor (upload_max_filesize ou post_max_size).'];
            }
            return ['path' => null, 'error' => 'Erro no upload. Código: ' . $_FILES['image']['error']];
        }

        // --- INÍCIO DA VALIDAÇÃO DE TAMANHO (15MB) ---
        // Esta verificação é feita *após* o upload ser aceite pelo servidor (UPLOAD_ERR_OK)
        // O seu .user.ini deve permitir > 15MB (ex: 100M), e este código rejeitará a *imagem* se for > 15MB.
        $maxImageSize = 15 * 1024 * 1024; // 15MB in bytes
        if ($_FILES['image']['size'] > $maxImageSize) {
            return ['path' => null, 'error' => 'Erro: A imagem do curso não pode exceder 15MB. O ficheiro enviado tem ' . round($_FILES['image']['size'] / 1024 / 1024, 2) . 'MB.'];
        }
        // --- FIM DA VALIDAÇÃO DE TAMANHO ---

        $tmpName = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];

        // --- INÍCIO DA VALIDAÇÃO DE TIPO (O SEU PEDIDO) ---
        // 1. Verificar tipo MIME
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpName);
        $allowedMimes = [
            'image/jpeg', // para .jpg e .jpeg
            'image/png'   // para .png
        ];

        // 2. Verificar extensão do ficheiro
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (!in_array($mime, $allowedMimes) || !in_array($fileExtension, $allowedExtensions)) {
             return ['path' => null, 'error' => 'Tipo de ficheiro inválido. Apenas JPG, JPEG, e PNG são permitidos.'];
        }
        // --- FIM DA VALIDAÇÃO DE TIPO ---

        $uploadSubDir = 'assets' . DIRECTORY_SEPARATOR . 'img-courses';
        
        // --- CORREÇÃO DE CAMINHO ---
        // $uploadDir = ROOT_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $uploadSubDir; (Linha antiga)
        // Usamos a nova constante que aponta para /public_html/app.assistaconecta.com.br
        $uploadDir = PUBLIC_APP_PATH . DIRECTORY_SEPARATOR . $uploadSubDir;

        if (!is_dir($uploadDir)) {
            if (!@mkdir($uploadDir, 0777, true)) {
                 $error = error_get_last();
                 return ['path' => null, 'error' => "Falha ao criar diretório: " . ($error['message'] ?? 'erro desconhecido')];
            }
        }

        // Renomeia o ficheiro para evitar conflitos
        $safeFileName = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", basename($fileName));
        $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $safeFileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            return ['path' => '/' . str_replace(DIRECTORY_SEPARATOR, '/', $uploadSubDir . '/' . $safeFileName), 'error' => null];
        } else {
            return ['path' => null, 'error' => 'Falha ao mover o ficheiro. Verifique as permissões do servidor.'];
        }
    }

    // --- CRUD CURSOS ---

    public function createCourse()
    {
        // --- CORREÇÃO: VERIFICA SE $_POST ESTÁ VAZIO (erro post_max_size) ---
        if (empty($_POST)) {
             $_SESSION['error_message'] = 'Erro ao processar o formulário. O envio pode ser demasiado grande (verifique post_max_size no .user.ini).';
             header('Location: ' . BASE_URL . '/admin/courses/create');
             exit;
        }
        
        $validationError = $this->validateCourseData($_POST);
        if ($validationError) {
            $_SESSION['error_message'] = $validationError;
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/admin/courses/create');
            exit;
        }

        $uploadResult = $this->handleImageUpload();
        if ($uploadResult['error']) {
            $_SESSION['error_message'] = $uploadResult['error'];
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/admin/courses/create');
            exit;
        }

        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'instructor' => $_POST['instructor'],
            'price' => empty($_POST['price']) ? 0.00 : $_POST['price'],
            'image_url' => $uploadResult['path'],
            'status' => $_POST['status'],
            'workload' => $_POST['workload'],
            'target_audience' => $_POST['target_audience'],
            'format' => $_POST['format'],
            'level' => $_POST['level'],
            'modality' => $_POST['modality'],
            'category' => $_POST['category'],
        ];

        if ($this->courseModel->create($data)) {
            $_SESSION['success_message'] = 'Curso criado com sucesso!';
            unset($_SESSION['form_data']);
        } else {
            $_SESSION['error_message'] = 'Erro ao salvar o curso no banco de dados.';
            $_SESSION['form_data'] = $_POST;
        }

        header('Location: ' . BASE_URL . '/admin/courses');
        exit;
    }

    public function updateCourse($id)
    {
        // --- CORREÇÃO: VERIFICA SE $_POST ESTÁ VAZIO (erro post_max_size) ---
        if (empty($_POST)) {
             $_SESSION['error_message'] = 'Erro ao processar o formulário. O envio pode ser demasiado grande (verifique post_max_size no .user.ini).';
             header('Location: ' . BASE_URL . '/admin/courses/edit/' . $id);
             exit;
        }
        
        $validationError = $this->validateCourseData($_POST);
        if ($validationError) {
            $_SESSION['error_message'] = $validationError;
            header('Location: ' . BASE_URL . '/admin/courses/edit/' . $id);
            exit;
        }

        $uploadResult = $this->handleImageUpload();
        if ($uploadResult['error']) {
            $_SESSION['error_message'] = $uploadResult['error'];
            header('Location: ' . BASE_URL . '/admin/courses/edit/' . $id);
            exit;
        }

        // Se um novo upload foi feito, usa o novo caminho. Senão, usa o caminho atual.
        $imageUrl = $uploadResult['path'] ?? $_POST['current_image_url'] ?? null;

        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'instructor' => $_POST['instructor'],
            'price' => empty($_POST['price']) ? 0.00 : $_POST['price'],
            'image_url' => $imageUrl,
            'status' => $_POST['status'],
            'workload' => $_POST['workload'],
            'target_audience' => $_POST['target_audience'],
            'format' => $_POST['format'],
            'level' => $_POST['level'],
            'modality' => $_POST['modality'],
            'category' => $_POST['category'],
        ];

        if ($this->courseModel->update((int)$id, $data)) {
            $_SESSION['success_message'] = 'Curso atualizado com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao atualizar o curso no banco de dados.';
        }

        header('Location: ' . BASE_URL . '/admin/courses');
        exit;
    }

    public function listCourses()
    {
        $courses = $this->courseModel->findAll();
        $this->render('admin/courses', ['title' => 'Gerenciar Cursos', 'courses' => $courses]);
    }

    public function createCourseForm()
    {
        $courseData = $_SESSION['form_data'] ?? null;
        unset($_SESSION['form_data']);
        
        $this->render('admin/course_form', [
            'title' => 'Adicionar Novo Curso',
            'action' => BASE_URL . '/admin/courses/create',
            'course' => $courseData
        ]);
    }

    public function editCourseForm($id)
    {
        $course = $this->courseModel->findById((int)$id);
        if (!$course) {
            header("Location: " . BASE_URL . "/admin/courses");
            exit;
        }
        $this->render('admin/course_form', [
            'title' => 'Editar Curso',
            'action' => BASE_URL . '/admin/courses/edit/' . $id,
            'course' => $course
        ]);
    }

    public function deleteCourse($id)
    {
        $course = $this->courseModel->findById((int)$id);
        if ($course && !empty($course['image_url'])) {
            
            // --- CORREÇÃO DE CAMINHO ---
            // $filePath = ROOT_PATH . DIRECTORY_SEPARATOR . 'public' . $course['image_url']; (Linha antiga)
            $filePath = PUBLIC_APP_PATH . $course['image_url']; // (Caminho corrigido)
            
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $this->courseModel->delete((int)$id);
        $_SESSION['success_message'] = 'Curso apagado com sucesso!';
        header('Location: ' . BASE_URL . '/admin/courses');
        exit;
    }

    // --- GERENCIAR CONTEÚDO (MÓDULOS E LIÇÕES) ---

    public function manageCourseContent($courseId)
    {
        $course = $this->courseModel->findById((int)$courseId);
        if (!$course) {
            $_SESSION['error_message'] = 'Curso não encontrado.';
            header('Location: '. BASE_URL . '/admin/courses');
            exit;
        }
        
        // NOVO: Busca módulos E as lições aninhadas
        $modules = $this->contentModel->findModulesWithLessons((int)$courseId);

        $this->render('admin/manage_content', [
            'title' => 'Gerenciar Conteúdo: ' . $course['title'],
            'course' => $course,
            'modules' => $modules
        ]);
    }

    public function createModule($courseId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['title'])) {
            $_SESSION['error_message'] = 'Nome do módulo é obrigatório.';
            header('Location: ' . BASE_URL . '/admin/courses/' . $courseId . '/content');
            exit;
        }

        $title = $_POST['title'];
        $order = $this->contentModel->getNextModuleOrder((int)$courseId);

        if ($this->contentModel->createModule((int)$courseId, $title, $order)) {
            $_SESSION['success_message'] = 'Módulo criado com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao criar o módulo.';
        }
        header('Location: ' . BASE_URL . '/admin/courses/' . $courseId . '/content');
        exit;
    }

    public function deleteModule($courseId, $moduleId)
    {
        // Adicionar lógica de verificação (se o módulo pertence ao curso)
        if ($this->contentModel->deleteModule((int)$moduleId)) {
            $_SESSION['success_message'] = 'Módulo excluído com sucesso.';
        } else {
            $_SESSION['error_message'] = 'Erro ao excluir o módulo.';
        }
        header('Location: ' . BASE_URL . '/admin/courses/' . $courseId . '/content');
        exit;
    }

    public function createLessonForm($courseId, $moduleId) // Corrigido para receber $courseId da rota
    {
        $module = $this->contentModel->findModuleById((int)$moduleId);
        if (!$module || $module['course_id'] != $courseId) { // Verifica se o módulo pertence ao curso
            $_SESSION['error_message'] = 'Módulo inválido.';
            header('Location: ' . BASE_URL . '/admin/courses');
            exit;
        }
        
        $course = $this->courseModel->findById((int)$courseId);
        if (!$course) {
            $_SESSION['error_message'] = 'Curso associado ao módulo não encontrado.';
            header('Location: ' . BASE_URL . '/admin/courses');
            exit;
        }

        $this->render('admin/lesson_form', [
            'title' => 'Adicionar Nova Lição',
            'course' => $course,
            'module' => $module,
            'lesson' => $_SESSION['form_data'] ?? null,
            // Rota corrigida para incluir courseId
            'action' => BASE_URL . '/admin/courses/' . $courseId . '/modules/' . $moduleId . '/lessons/create'
        ]);
        unset($_SESSION['form_data']);
    }

    // Lógica de upload de PDF
    private function handlePdfUpload(): array
    {
        if (!isset($_FILES['content_path_pdf']) || $_FILES['content_path_pdf']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['path' => null, 'error' => null];
        }

        if ($_FILES['content_path_pdf']['error'] !== UPLOAD_ERR_OK) {
             if ($_FILES['content_path_pdf']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['content_path_pdf']['error'] === UPLOAD_ERR_FORM_SIZE) {
                 return ['path' => null, 'error' => 'Erro: O ficheiro PDF é demasiado grande. Verifique o limite de upload do servidor (php.ini).'];
            }
            return ['path' => null, 'error' => 'Erro no upload do PDF. Código: ' . $_FILES['content_path_pdf']['error']];
        }

        // Validação do tipo de ficheiro
        $mimeType = mime_content_type($_FILES['content_path_pdf']['tmp_name']);
        if ($mimeType !== 'application/pdf') {
             return ['path' => null, 'error' => 'Tipo de ficheiro inválido. Apenas PDFs são permitidos.'];
        }

        $uploadSubDir = 'assets' . DIRECTORY_SEPARATOR . 'pdfs';
        
        // --- CORREÇÃO DE CAMINHO ---
        // $uploadDir = ROOT_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $uploadSubDir; (Linha antiga)
        $uploadDir = PUBLIC_APP_PATH . DIRECTORY_SEPARATOR . $uploadSubDir; // (Caminho corrigido)

        if (!is_dir($uploadDir)) {
            if (!@mkdir($uploadDir, 0777, true)) {
                 return ['path' => null, 'error' => "Falha ao criar diretório de PDFs."];
            }
        }

        $fileName = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", basename($_FILES['content_path_pdf']['name']));
        $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if (move_uploaded_file($_FILES['content_path_pdf']['tmp_name'], $targetPath)) {
            // Retorna o caminho relativo para o browser
            return ['path' => '/' . str_replace(DIRECTORY_SEPARATOR, '/', $uploadSubDir . '/' . $fileName), 'error' => null];
        } else {
            return ['path' => null, 'error' => 'Falha ao mover o ficheiro PDF.'];
        }
    }


    public function createLesson($courseId, $moduleId) // Rota corrigida
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/courses');
            exit;
        }

        $module = $this->contentModel->findModuleById((int)$moduleId);
        // Verifica se o módulo e o curso coincidem
        if (!$module || $module['course_id'] != $courseId) {
             $_SESSION['error_message'] = 'Módulo ou Curso não encontrado.';
             header('Location: ' . BASE_URL . '/admin/courses');
             exit;
        }

        $contentType = $_POST['content_type'] ?? 'video';
        $title = $_POST['title'] ?? '';
        $contentPath = null;
        $contentText = null;
        
        // Rota de retorno em caso de erro
        $errorRedirectUrl = BASE_URL . '/admin/courses/' . $courseId . '/modules/' . $moduleId . '/lessons/create';

        if (empty($title)) {
            $_SESSION['error_message'] = 'O título da lição é obrigatório.';
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . $errorRedirectUrl);
            exit;
        }

        if ($contentType === 'video') {
            $contentPath = $_POST['content_path_video'] ?? null;
        } elseif ($contentType === 'text') {
            $contentText = $_POST['content_text'] ?? null;
        } elseif ($contentType === 'pdf') {
            $uploadResult = $this->handlePdfUpload();
            if ($uploadResult['error']) {
                $_SESSION['error_message'] = $uploadResult['error'];
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . $errorRedirectUrl);
                exit;
            }
            $contentPath = $uploadResult['path'];
        }

        $order = $this->contentModel->getNextLessonOrder((int)$moduleId);

        $data = [
            'module_id' => $moduleId,
            'title' => $title,
            'content_type' => $contentType,
            'content_path' => $contentPath,
            'content_text' => $contentText,
            'order' => $order
        ];

        if ($this->contentModel->createLesson($data)) {
            $_SESSION['success_message'] = 'Lição adicionada com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao adicionar a lição.';
        }
        header('Location: ' . BASE_URL . '/admin/courses/' . $courseId . '/content');
        exit;
    }


    public function editLessonForm($courseId, $moduleId, $lessonId) // Rota corrigida
    {
        $lesson = $this->contentModel->findLessonById((int)$lessonId);
        $module = $this->contentModel->findModuleById((int)$moduleId);
        
        // Validação forte
        if (!$lesson || !$module || $lesson['module_id'] != $moduleId || $module['course_id'] != $courseId) {
            $_SESSION['error_message'] = 'Lição ou Módulo inválido.';
            header('Location: ' . BASE_URL . '/admin/courses');
            exit;
        }
        
        $course = $this->courseModel->findById((int)$courseId);

        $this->render('admin/lesson_form', [
            'title' => 'Editar Lição',
            'course' => $course,
            'module' => $module,
            'lesson' => $lesson,
            'action' => BASE_URL . '/admin/courses/' . $courseId . '/modules/' . $moduleId . '/lessons/update/' . $lessonId
        ]);
    }

    public function updateLesson($courseId, $moduleId, $lessonId) // Rota corrigida
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             header('Location: ' . BASE_URL . '/admin/courses');
             exit;
        }

        $lesson = $this->contentModel->findLessonById((int)$lessonId);
        $module = $this->contentModel->findModuleById((int)$moduleId);

        // Validação forte
        if (!$lesson || !$module || $lesson['module_id'] != $moduleId || $module['course_id'] != $courseId) {
             $_SESSION['error_message'] = 'Lição ou Módulo inválido.';
             header('Location: ' . BASE_URL . '/admin/courses');
             exit;
        }

        $contentType = $_POST['content_type'] ?? 'video';
        $title = $_POST['title'] ?? '';
        $contentPath = $lesson['content_path']; // Mantém o valor antigo por defeito
        $contentText = $lesson['content_text']; // Mantém o valor antigo por defeito
        
        // Rota de retorno em caso de erro
        $errorRedirectUrl = BASE_URL . '/admin/courses/' . $courseId . '/modules/' . $moduleId . '/lessons/edit/' . $lessonId;


        if (empty($title)) {
            $_SESSION['error_message'] = 'O título da lição é obrigatório.';
            header('Location: ' . $errorRedirectUrl);
            exit;
        }

        if ($contentType === 'video') {
            $contentPath = $_POST['content_path_video'] ?? null;
            $contentText = null;
        } elseif ($contentType === 'text') {
            $contentText = $_POST['content_text'] ?? null;
            $contentPath = null;
        } elseif ($contentType === 'pdf') {
            $uploadResult = $this->handlePdfUpload();
            if ($uploadResult['error']) {
                $_SESSION['error_message'] = $uploadResult['error'];
                header('Location: ' . $errorRedirectUrl);
                exit;
            }
            if ($uploadResult['path']) {
                 // Se um novo PDF foi enviado, atualiza o caminho
                 $contentPath = $uploadResult['path'];
                 // (Opcional: apagar o PDF antigo do servidor)
                 if (!empty($lesson['content_path']) && $lesson['content_path'] != $contentPath) {
                      @unlink(PUBLIC_APP_PATH . $lesson['content_path']);
                 }
            }
            $contentText = null;
        }
        
        // Se mudou o tipo, apaga o conteúdo antigo
        if ($contentType !== $lesson['content_type']) {
            if($lesson['content_type'] === 'pdf' && !empty($lesson['content_path'])) {
                 @unlink(PUBLIC_APP_PATH . $lesson['content_path']);
            }
             // Se mudou de PDF para Texto/Vídeo, $contentPath é atualizado acima
             // Se mudou de Texto/Vídeo para PDF, $contentText é atualizado acima
        }


        $data = [
            'title' => $title,
            'content_type' => $contentType,
            'content_path' => $contentPath,
            'content_text' => $contentText,
        ];

        if ($this->contentModel->updateLesson((int)$lessonId, $data)) {
            $_SESSION['success_message'] = 'Lição atualizada com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao atualizar a lição.';
        }
        header('Location: ' . BASE_URL . '/admin/courses/' . $courseId . '/content');
        exit;
    }


    public function deleteLesson($courseId, $moduleId, $lessonId) // Rota corrigida
    {
        $lesson = $this->contentModel->findLessonById((int)$lessonId);
        $module = $this->contentModel->findModuleById((int)$moduleId);
        
         // Validação forte
        if (!$lesson || !$module || $lesson['module_id'] != $moduleId || $module['course_id'] != $courseId) {
             $_SESSION['error_message'] = 'Lição ou Módulo inválido.';
             header('Location: ' . BASE_URL . '/admin/courses');
             exit;
        }

        // Apaga o ficheiro PDF do servidor se existir
        if ($lesson['content_type'] === 'pdf' && !empty($lesson['content_path'])) {
             @unlink(PUBLIC_APP_PATH . $lesson['content_path']);
        }

        if ($this->contentModel->deleteLesson((int)$lessonId)) {
            $_SESSION['success_message'] = 'Lição excluída com sucesso.';
        } else {
            $_SESSION['error_message'] = 'Erro ao excluir a lição.';
        }
        header('Location: ' . BASE_URL . '/admin/courses/' . $courseId . '/content');
        exit;
    }
}
