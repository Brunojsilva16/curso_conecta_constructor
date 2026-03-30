<?php

namespace App\Controllers;

use App\Database\Connection;
use PDO;
use Exception;

class CheckoutController extends BaseController
{
    private string $token = 'SEU_TOKEN_DO_PAGBANK';
    private bool $isSandbox = true;

    private function getApiUrl(): string
    {
        return $this->isSandbox
            ? 'https://sandbox.api.pagseguro.com/orders'
            : 'https://api.pagseguro.com/orders';
    }

    private function getWebhookUrl(): string
    {
        return 'https://SEU_DOMINIO_OU_NGROK/webhook/pagbank';
    }

    private function getBaseUrl(): string
    {
        return 'https://SEU_DOMINIO_OU_NGROK';
    }

    public function payment()
    {
        $this->render('payment', [
            'title' => 'Página Inicial',
        ]);
    }

    public function paymentPage()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $paymentData = $_SESSION['payment_data'] ?? null;

        if (!$paymentData) {
            header('Location: /meus-cursos');
            exit;
        }

        $this->render('checkout/payment', [
            'title' => 'Finalizar pagamento',
            'fullWidthLayout' => true,
            'courseTitle' => $paymentData['course_title'] ?? 'Curso',
            'coursePrice' => $paymentData['course_price'] ?? '0,00',
            'checkoutUrl' => $paymentData['checkout_url'] ?? '#',
        ]);
    }

    public function createPayment()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $courseId = $_POST['course_id'] ?? null;
        $userId   = $_SESSION['user']['id'] ?? null;

        if (!$courseId || !$userId) {
            http_response_code(400);
            exit('Erro: usuário não logado ou curso inválido.');
        }

        $db = Connection::getInstance();

        $stmt = $db->prepare("
            SELECT id, title, price, description
            FROM courses
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$course) {
            http_response_code(404);
            exit('Curso não encontrado.');
        }

        $stmt = $db->prepare("
            SELECT id, name, email, cpf
            FROM users
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404);
            exit('Usuário não encontrado.');
        }

        if (empty($user['cpf'])) {
            http_response_code(400);
            exit('CPF do usuário é obrigatório para gerar cobrança no PagBank.');
        }

        $stmt = $db->prepare("
            SELECT id
            FROM user_courses
            WHERE user_id = ? AND course_id = ?
            LIMIT 1
        ");
        $stmt->execute([$userId, $courseId]);

        if ($stmt->fetch()) {
            http_response_code(409);
            exit('Este curso já foi liberado para este usuário.');
        }

        $referenceId = 'REF_' . $userId . '_' . $courseId . '_' . time();
        $amount = (int) round(((float)$course['price']) * 100);

        if ($amount <= 0) {
            http_response_code(400);
            exit('Valor do curso inválido.');
        }

        $stmt = $db->prepare("
            INSERT INTO orders (user_id, course_id, reference_id, status, amount)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $courseId, $referenceId, 'PENDING', $amount]);

        $payload = [
            'reference_id' => $referenceId,
            'customer' => [
                'name'   => $user['name'],
                'email'  => $user['email'],
                'tax_id' => preg_replace('/\D/', '', $user['cpf']),
            ],
            'items' => [
                [
                    'reference_id' => 'COURSE_' . $course['id'],
                    'name'         => $course['title'],
                    'quantity'     => 1,
                    'unit_amount'  => $amount,
                ]
            ],
            'notification_urls' => [
                $this->getWebhookUrl()
            ],
            'redirect_urls' => [
                'success' => $this->getBaseUrl() . '/pagamento/sucesso',
                'failure' => $this->getBaseUrl() . '/pagamento/erro',
            ]
        ];

        $responseData = $this->sendPagBankRequest($payload);

        if (isset($responseData['error_messages']) || isset($responseData['curl_error'])) {
            echo '<pre>';
            print_r($responseData);
            echo '</pre>';
            exit('Erro ao gerar cobrança no PagBank.');
        }

        $checkoutUrl = $this->extractPayLink($responseData);

        if (!$checkoutUrl) {
            echo '<pre>';
            print_r($responseData);
            echo '</pre>';
            exit('Não foi possível obter o link de pagamento.');
        }

        try {
            $stmt = $db->prepare("
                UPDATE orders
                SET gateway_response = ?
                WHERE reference_id = ?
            ");
            $stmt->execute([json_encode($responseData, JSON_UNESCAPED_UNICODE), $referenceId]);
        } catch (Exception $e) {
            // ignora se a coluna não existir
        }

        $_SESSION['payment_data'] = [
            'course_title' => $course['title'],
            'course_price' => number_format((float)$course['price'], 2, ',', '.'),
            'checkout_url' => $checkoutUrl,
        ];

        header('Location: /checkout/pagamento');
        exit;
    }

    private function sendPagBankRequest(array $payload): array
    {
        $ch = curl_init($this->getApiUrl());

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            return [
                'curl_error' => $curlError,
                'http_code' => $httpCode,
            ];
        }

        $decoded = json_decode($response, true);

        return is_array($decoded)
            ? $decoded
            : [
                'raw_response' => $response,
                'http_code' => $httpCode,
            ];
    }

    private function extractPayLink(array $responseData): ?string
    {
        if (!isset($responseData['links']) || !is_array($responseData['links'])) {
            return null;
        }

        foreach ($responseData['links'] as $link) {
            if (($link['rel'] ?? null) === 'PAY') {
                return $link['href'] ?? null;
            }
        }

        return null;
    }



    public function webhook()
    {
        $payload = file_get_contents('php://input');
        $data = json_decode($payload, true);

        @file_put_contents(
            __DIR__ . '/../../storage/logs/pagbank_webhook.log',
            date('Y-m-d H:i:s') . ' - ' . $payload . PHP_EOL,
            FILE_APPEND
        );

        if (!is_array($data)) {
            http_response_code(200);
            echo 'OK';
            return;
        }

        $referenceId = $data['reference_id'] ?? null;
        $status = $data['charges'][0]['status'] ?? null;

        if (!$referenceId || !$status) {
            http_response_code(200);
            echo 'OK';
            return;
        }

        $db = Connection::getInstance();

        $stmt = $db->prepare("
            UPDATE orders
            SET status = ?, updated_at = NOW()
            WHERE reference_id = ?
        ");
        $stmt->execute([$status, $referenceId]);

        try {
            $stmt = $db->prepare("
                UPDATE orders
                SET webhook_payload = ?
                WHERE reference_id = ?
            ");
            $stmt->execute([json_encode($data, JSON_UNESCAPED_UNICODE), $referenceId]);
        } catch (Exception $e) {
            // ignora se a coluna não existir
        }

        if ($status === 'PAID') {
            $stmt = $db->prepare("
                SELECT user_id, course_id
                FROM orders
                WHERE reference_id = ?
                LIMIT 1
            ");
            $stmt->execute([$referenceId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                $userId = $order['user_id'];
                $courseId = $order['course_id'];

                $check = $db->prepare("
                    SELECT id
                    FROM user_courses
                    WHERE user_id = ? AND course_id = ?
                    LIMIT 1
                ");
                $check->execute([$userId, $courseId]);

                if (!$check->fetch()) {
                    $insert = $db->prepare("
                        INSERT INTO user_courses (user_id, course_id, status)
                        VALUES (?, ?, ?)
                    ");
                    $insert->execute([$userId, $courseId, 'Em Andamento']);
                }
            }
        }

        http_response_code(200);
        echo 'OK';
    }

    public function success()
    {
        $this->render('checkout/success', [
            'title' => 'Pagamento realizado',
            'fullWidthLayout' => true,
        ]);
    }

    public function failure()
    {
        $this->render('checkout/failure', [
            'title' => 'Pagamento não concluído',
            'fullWidthLayout' => true,
        ]);
    }
}
