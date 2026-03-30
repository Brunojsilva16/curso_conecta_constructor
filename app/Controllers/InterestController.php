<?php

namespace App\Controllers;

use App\Models\InterestModel;

class InterestController
{
    /**
     * Registra o interesse de um usuário em um curso.
     */
    public function register()
    {
        // Validação simples dos dados recebidos via POST
        if (
            !isset($_POST['name']) || empty($_POST['name']) ||
            !isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ||
            !isset($_POST['course_title']) || empty($_POST['course_title'])
        ) {
            // Se os dados forem inválidos, redireciona de volta com um erro
            $_SESSION['error_message'] = "Por favor, preencha todos os campos corretamente.";
            header('Location: ' . BASE_URL);
            exit;
        }

        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $courseTitle = trim($_POST['course_title']);

        $interestModel = new InterestModel();
        $success = $interestModel->saveInterest($name, $email, $courseTitle);

        if ($success) {
            $_SESSION['success_message'] = "Obrigado! Avisaremos você assim que novas vagas abrirem.";
        } else {
            $_SESSION['error_message'] = "Ocorreu um erro ao registrar seu interesse. Tente novamente.";
        }

        // Redireciona para a página inicial
        header('Location: ' . BASE_URL);
        exit;
    }
}
