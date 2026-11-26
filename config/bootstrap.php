<?php
declare(strict_types=1);
session_start();

const DB_HOST = '127.0.0.1';
const DB_NAME = 'organizae';
const DB_USER = 'root';
const DB_PASS = '';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Não foi possível conectar ao banco de dados. Confira se o MySQL está ativo e se o banco organizae foi importado.');
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        flash('error', 'Faça login para continuar.');
        redirect('/organizae/auth/login.php');
    }
}

function current_user_id(): int {
    return (int)($_SESSION['user_id'] ?? 0);
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        exit('Solicitação inválida. Atualize a página e tente novamente.');
    }
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}

function page_header(string $title): void {
    $userName = $_SESSION['user_name'] ?? '';
    echo '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . e($title) . ' | Organizaê</title>';
    echo '<link rel="stylesheet" href="/organizae/assets/css/style.css"></head><body>';
    echo '<header class="topbar"><a class="brand" href="/organizae/dashboard.php">Organizaê</a>';
    if (is_logged_in()) {
        echo '<nav><a href="/organizae/dashboard.php">Painel</a><a href="/organizae/tarefas/index.php">Tarefas</a><a href="/organizae/categorias/index.php">Categorias</a></nav>';
        echo '<div class="user-area"><span>' . e($userName) . '</span><a class="btn btn-outline btn-small" href="/organizae/auth/logout.php">Sair</a></div>';
    } else {
        echo '<nav><a href="/organizae/auth/login.php">Entrar</a><a href="/organizae/auth/cadastro.php">Criar conta</a></nav>';
    }
    echo '</header><main class="container">';
    $flash = get_flash();
    if ($flash) {
        echo '<div class="alert ' . e($flash['type']) . '">' . e($flash['message']) . '</div>';
    }
}

function page_footer(): void {
    echo '</main><footer class="footer">Organizaê · Projeto Integrador — Programação Web</footer>';
    echo '<script src="/organizae/assets/js/main.js"></script></body></html>';
}
