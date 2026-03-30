<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Controllers\BaseController;
use App\Core\Auth;

class CourseController extends BaseController
{
    /**
     * Exibe a página principal com a lista de cursos.
     */
    public function index()
    {
        $courseModel = new CourseModel();
        $courses = $courseModel->findAllPublished();

        $this->render('home', [
            'title' => 'Nossos Cursos',
            'courses' => $courses
        ]);
    }

    /**
     * Exibe os detalhes de um curso específico.
     */
    public function show($id)
    {
        $courseModel = new CourseModel();
        $course = $courseModel->findById((int)$id);

        if (!$course || $course['status'] !== 'published') {
            (new PageController())->notFound();
            return;
        }

        $userHasAccess = $this->checkUserAccess((int)$id);

        $this->render('course_show', [
            'title' => $course['title'],
            'course' => $course,
            'userHasAccess' => $userHasAccess
        ]);
    }

    /**
     * Exibe a página de visualização do curso (player).
     */
    public function watch($id)
    {
        if (!Auth::isLogged()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if (!$this->checkUserAccess((int)$id)) {
            $_SESSION['error_message'] = 'Você não tem permissão para acessar este curso.';
            header('Location: ' . BASE_URL . '/curso/' . $id);
            exit;
        }

        $courseModel = new CourseModel();
        $course = $courseModel->findCourseWithContent((int)$id);

        if (!$course) {
            (new PageController())->notFound();
            return;
        }

        $this->render('course_player', [
            'title' => "Assistindo: " . $course['title'],
            'course' => $course,
        ]);
    }


    /**
     * Processa a compra de um curso.
     */
    public function purchase($id)
    {
        if (!Auth::isLogged()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = Auth::userId();
        $courseModel = new CourseModel();
        $course = $courseModel->findById((int)$id);

        if (!$course) {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Regra de negócio: Usuário 'none' não pode comprar cursos 'premium' ou 'platinum'
        $userPlan = Auth::userPlan();
        if ($userPlan === 'none' && in_array($course['category'], ['premium', 'platinum'])) {
             $_SESSION['error_message'] = 'Você precisa de um plano para comprar este curso.';
             header('Location: ' . BASE_URL . '/planos');
             exit;
        }

        $courseModel->grantCourseAccess($userId, (int)$id);

        $_SESSION['success_message'] = 'Compra realizada com sucesso! Você já pode acessar o curso.';
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    /**
     * Função auxiliar para verificar se o usuário tem acesso a um curso.
     */
    private function checkUserAccess(int $courseId): bool
    {
        $userId = Auth::userId();
        if (!$userId) {
            return false;
        }

        $courseModel = new CourseModel();
        $course = $courseModel->findById($courseId);

        if (!$course) {
            return false;
        }

        // 1. Verifica se já comprou o curso individualmente
        if ($courseModel->checkUserHasCourse($userId, $courseId)) {
            return true;
        }

        // 2. Verifica acesso via plano de assinatura
        $userPlan = Auth::userPlan();
        if (
            ($userPlan === 'premium' && in_array($course['category'], ['essential', 'premium', 'platinum'])) ||
            ($userPlan === 'essential' && $course['category'] === 'essential')
        ) {
            return true;
        }

        return false;
    }
}
