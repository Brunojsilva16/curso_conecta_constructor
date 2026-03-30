<?php

namespace App\Models;

use App\Database\DataSource;

class ContentModel
{
    private DataSource $db;

    public function __construct()
    {
        $this->db = DataSource::getInstance();
    }

    // --- MÓDULOS ---

    /**
     * Busca todos os módulos de um curso, já com as lições aninhadas.
     * Este é o método que estava em falta e a causar o erro.
     */
    public function findModulesWithLessons(int $courseId): array
    {
        // 1. Busca módulos
        $sqlModules = "SELECT * FROM course_modules WHERE course_id = :course_id ORDER BY `order` ASC";
        $modules = $this->db->select($sqlModules, ['course_id' => $courseId]);

        // 2. Para cada módulo, busca as lições
        foreach ($modules as $key => $module) {
            $sqlLessons = "SELECT * FROM course_lessons WHERE module_id = :module_id ORDER BY `order` ASC";
            $lessons = $this->db->select($sqlLessons, ['module_id' => $module['id']]);
            $modules[$key]['lessons'] = $lessons;
        }
        return $modules;
    }

    public function findModuleById(int $moduleId): ?array
    {
        $sql = "SELECT * FROM course_modules WHERE id = :id";
        return $this->db->selectOne($sql, ['id' => $moduleId]);
    }

    public function createModule(int $courseId, string $title, int $order): bool
    {
        $sql = "INSERT INTO course_modules (course_id, title, `order`) VALUES (:course_id, :title, :order)";
        return $this->db->execute($sql, [
            'course_id' => $courseId,
            'title' => $title,
            'order' => $order
        ]);
    }

    public function deleteModule(int $moduleId): bool
    {
        // ON DELETE CASCADE no DB deve apagar as lições juntas
        $sql = "DELETE FROM course_modules WHERE id = :id";
        return $this->db->execute($sql, ['id' => $moduleId]);
    }
    
    public function getNextModuleOrder(int $courseId): int
    {
        $sql = "SELECT MAX(`order`) as max_order FROM course_modules WHERE course_id = :course_id";
        $result = $this->db->selectOne($sql, ['course_id' => $courseId]);
        return ($result['max_order'] ?? 0) + 1;
    }

    // --- LIÇÕES ---

    public function findLessonById(int $lessonId): ?array
    {
        $sql = "SELECT * FROM course_lessons WHERE id = :id";
        return $this->db->selectOne($sql, ['id' => $lessonId]);
    }

    public function createLesson(array $data): bool
    {
        $sql = "INSERT INTO course_lessons (module_id, title, content_type, content_path, content_text, `order`) 
                VALUES (:module_id, :title, :content_type, :content_path, :content_text, :order)";
        return $this->db->execute($sql, $data);
    }

    public function updateLesson(int $lessonId, array $data): bool
    {
            $data['id'] = $lessonId;
            $sql = "UPDATE course_lessons 
                    SET title = :title, 
                        content_type = :content_type, 
                        content_path = :content_path, 
                        content_text = :content_text
                    WHERE id = :id";
        return $this->db->execute($sql, $data);
    }

    public function deleteLesson(int $lessonId): bool
    {
        $sql = "DELETE FROM course_lessons WHERE id = :id";
        return $this->db->execute($sql, ['id' => $lessonId]);
    }
    
    public function getNextLessonOrder(int $moduleId): int
    {
        $sql = "SELECT MAX(`order`) as max_order FROM course_lessons WHERE module_id = :module_id";
        $result = $this->db->selectOne($sql, ['module_id' => $moduleId]);
        return ($result['max_order'] ?? 0) + 1;
    }
}