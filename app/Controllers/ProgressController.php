<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\CourseModel;

class ProgressController
{
    public function update()
    {
        header('Content-Type: application/json');

        if (!Auth::isLogged()) {
            echo json_encode(['success' => false, 'message' => 'Utilizador não autenticado.']);
            return;
        }

        // Lê o corpo da requisição POST
        $data = json_decode(file_get_contents('php://input'), true);
        $courseId = $data['course_id'] ?? null;
        $status = $data['status'] ?? null;
        $userId = Auth::userId();

        if (!$courseId || !$status) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
            return;
        }

        $courseModel = new CourseModel();
        $success = $courseModel->updateUserCourseStatus($userId, $courseId, $status);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Falha ao atualizar o estado do curso.']);
        }
    }
}
