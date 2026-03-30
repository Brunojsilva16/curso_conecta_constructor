<?php

use App\Controllers\PageController;
use App\Controllers\CourseController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\AdminController;
use App\Controllers\ProgressController;
use App\Controllers\InterestController; // NOVO: Importar
use App\Controllers\CheckoutController; // NOVO: Importar

/**
 * --- ROTAS DE CURSOS ---
 */
$router->get('curso/{id}', [CourseController::class, 'show']);
$router->post('curso/comprar/{id}', [CourseController::class, 'purchase']);
// NOVA ROTA PARA ASSISTIR AO CURSO
$router->get('curso/{id}/assistir', [CourseController::class, 'watch']);

/**
 * --- ROTA DE CHECKOUT (NOVO) ---
 */ 
// $router->post('checkout/createPayment/{courseId}', [CheckoutController::class, 'payment']);
// $router->post('checkout/checkout/pay', [CheckoutController::class, 'createPayment']);
// $router->post('checkout/pagbank/{courseId}', [CheckoutController::class, 'webhook']);

$router->post('checkout/createPayment', [CheckoutController::class, 'createPayment']);
$router->get('checkout/pagamento', [CheckoutController::class, 'paymentPage']);
$router->post('webhook/pagbank', [CheckoutController::class, 'webhook']);
$router->get('pagamento/sucesso', [CheckoutController::class, 'success']);
$router->get('pagamento/erro', [CheckoutController::class, 'failure']);

/**
 * --- ROTAS PRINCIPAIS ---
 */
$router->get('', [CourseController::class, 'index']);
$router->get('home', [CourseController::class, 'index']);
// NOVO: Rota para registar interesse (usado no modal)
$router->post('register-interest', [InterestController::class, 'register']);


/**
 * --- ROTAS DE AUTENTICAÇÃO ---
 */
$router->get('login', [AuthController::class, 'loginForm']);
$router->post('login', [AuthController::class, 'login']);
$router->get('logout', [AuthController::class, 'logout']);

// NOVAS ROTAS DE CADASTRO
$router->get('cadastro', [AuthController::class, 'registerForm']);
$router->post('cadastro', [AuthController::class, 'register']);

// ROTAS PARA ESQUECI A SENHA (CORRIGIDAS)
$router->get('esqueci-a-senha', [AuthController::class, 'forgotPasswordForm']);
$router->post('esqueci-a-senha', [AuthController::class, 'sendPasswordResetLink']);
// Rota para exibir o formulário de nova senha
$router->get('resetar-senha', [AuthController::class, 'resetPassword']);
// Rota para processar a nova senha
$router->post('resetar-senha', [AuthController::class, 'resetPassword']);


/**
 * --- ROTAS DO PAINEL DO ALUNO ---
 */
$router->get('dashboard', [DashboardController::class, 'index']);
$router->post('progress/update', [ProgressController::class, 'update']);


/**
 * --- ROTAS DE PERFIL DO USUÁRIO ---
 */
$router->get('perfil', [ProfileController::class, 'index']);
$router->post('perfil/atualizar', [ProfileController::class, 'update']);


/**
 * --- ROTAS DE ADMINISTRAÇÃO DE CURSOS ---
 */
$router->get('admin/courses', [AdminController::class, 'listCourses']);
$router->get('admin/courses/create', [AdminController::class, 'createCourseForm']);
$router->post('admin/courses/create', [AdminController::class, 'createCourse']);
$router->get('admin/courses/edit/{id}', [AdminController::class, 'editCourseForm']);
$router->post('admin/courses/edit/{id}', [AdminController::class, 'updateCourse']);
$router->get('admin/courses/delete/{id}', [AdminController::class, 'deleteCourse']);

/**
 * --- ROTAS DE ADMINISTRAÇÃO DE CONTEÚDO (CORRIGIDAS) ---
 */
// CORREÇÃO: Apontar para 'manageCourseContent'
$router->get('admin/courses/{id}/content', [AdminController::class, 'manageCourseContent']);
$router->post('admin/courses/{id}/modules/create', [AdminController::class, 'createModule']);

// CORREÇÃO: As rotas de lição precisam do ID do curso e do módulo
$router->get('admin/courses/{courseId}/modules/{moduleId}/lessons/create', [AdminController::class, 'createLessonForm']);
$router->post('admin/courses/{courseId}/modules/{moduleId}/lessons/create', [AdminController::class, 'createLesson']);


/**
 * --- ROTAS DE PÁGINAS ESTÁTICAS ---
 */
$router->get('planos', [PageController::class, 'plans']);

// Rota 404 de fallback (usando GET na última posição)
$router->get('{any}', [PageController::class, 'notFound']);


// CORREÇÃO: Remover '}' extra
