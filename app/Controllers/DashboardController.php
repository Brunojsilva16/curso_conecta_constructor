<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\UserModel;
use App\Models\CourseModel;
use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!Auth::isLogged()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userId = Auth::userId();
        $userPlan = Auth::userPlan();

        $userModel = new UserModel();
        $courseModel = new CourseModel();

        $user = $userModel->findById($userId);

        // Define as categorias de cursos permitidas com base no plano do utilizador
        $allowedCategories = [];
        if ($userPlan === 'premium') {
            $allowedCategories = ['essential', 'premium'];
        } else if ($userPlan === 'essential') {
            $allowedCategories = ['essential'];
        }

        // Chama o novo método unificado que busca cursos do plano E cursos comprados avulso
        $coursesData = $courseModel->findAllForUserDashboard($userId, $allowedCategories);

        // Verifica se o ficheiro de imagem de cada curso existe no servidor
        $finalCoursesData = array_map(function ($course) {
            $defaultImageUrl = '/assets/img/default_course.svg';
            $filePath = PUBLIC_APP_PATH . ($course['image_url'] ?? '');

            if (empty($course['image_url']) || !file_exists($filePath)) {
                $course['image_url'] = $defaultImageUrl;
            }

            return $course;
        }, $coursesData);


        $this->render('dashboard', [
            'title' => 'Meus Cursos',
            'userName' => $user['name'] ?? 'Usuário',
            'userCourses' => $finalCoursesData
        ]);
    }
}
