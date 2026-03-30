<?php

namespace App\Controllers;

// use App\Controllers\BaseController; // NOVO: Importa a classe BaseController

class PageController extends BaseController // NOVO: Herda de BaseController
{
    // O método render foi movido para BaseController, removendo a duplicação.

    public function home()
    {
        // O CourseController é o responsável por carregar a home, 
        // mas se a rota for configurada para PageController::home(), ele usará esta versão.
        // Vou mantê-lo simples, chamando apenas o render.
        $this->render('home', [
            'title' => 'Página Inicial',
            // Note: O PageController não deve buscar cursos, o CourseController deve ser usado.
            // Para manter a funcionalidade original, se PageController::home for usado:
            // 'styles' => [BASE_URL . '/css/admin.css', BASE_URL . '/css/style.css'],
            // 'scriptsHeader' => [BASE_URL . '/js/admin.js'],
            // 'scriptsFooter' => [
            //     'https://cdn.jsdelivr.net/npm/sweetalert2@11',
            //     BASE_URL . '/js/admin.js'
            // ]
        ]);
    }

    public function notFound()
    {
        header("HTTP/1.0 404 Not Found");
        $this->render('404', ['title' => 'Página não encontrada']);
    }

    public function plans()
    {
        $this->render('plans', [
            'title' => 'Nossos Planos de Assinatura',
        ]);
    }
}
