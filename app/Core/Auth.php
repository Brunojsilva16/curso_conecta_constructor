<?php

namespace App\Core;

/**
 * Classe de Autenticação Estática
 *
 * Gere a sessão do utilizador.
 * (Este ficheiro estava em falta e foi recriado com base no seu uso na aplicação)
 */
class Auth
{
    /**
     * Inicia a sessão se ainda não estiver ativa.
     */
    private static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Regista o login do utilizador na sessão.
     *
     * @param integer $id O ID do utilizador.
     * @param string $name O nome do utilizador.
     * @param string $role O perfil (role) do utilizador (ex: 'user', 'admin').
     * @param string $plan O plano de subscrição (ex: 'none', 'essential', 'premium').
     */
    public static function login(int $id, string $name, string $role, string $plan): void
    {
        self::start();
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_plan'] = $plan;
        $_SESSION['is_logged_in'] = true;
    }

    /**
     * Encerra a sessão do utilizador (logout).
     */
    public static function logout(): void
    {
        self::start();
        session_unset();
        session_destroy();
    }

    /**
     * Verifica se o utilizador está atualmente logado.
     *
     * @return boolean True se estiver logado, false caso contrário.
     */
    public static function isLogged(): bool
    {
        self::start();
        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
    }

    /**
     * Retorna o ID do utilizador logado.
     *
     * @return integer|null O ID do utilizador ou null se não estiver logado.
     */
    public static function userId(): ?int
    {
        self::start();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Retorna o Nome do utilizador logado.
     *
     * @return string|null O Nome do utilizador ou null se não estiver logado.
     */
    public static function userName(): ?string
    {
        self::start();
        return $_SESSION['user_name'] ?? null;
    }

    /**
     * Retorna o Perfil (role) do utilizador logado.
     *
     * @return string|null O perfil (ex: 'admin') ou null.
     */
    public static function userRole(): ?string
    {
        self::start();
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Retorna o Plano de subscrição do utilizador logado.
     *
     * @return string O plano (ex: 'none', 'premium').
     */
    public static function userPlan(): string
    {
        self::start();
        return $_SESSION['user_plan'] ?? 'none';
    }

    /**
     * Verifica se o utilizador logado é um administrador.
     *
     * @return boolean True se for admin, false caso contrário.
     */
    public static function isAdmin(): bool
    {
        self::start();
        return self::userRole() === 'admin';
    }
}
