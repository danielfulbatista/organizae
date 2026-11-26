<?php
require_once __DIR__ . '/../config/bootstrap.php';
if (is_logged_in()) redirect('/organizae/dashboard.php');

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '') $errors[] = 'Informe seu nome.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Informe um e-mail válido.';
    if (strlen($password) < 8) $errors[] = 'A senha deve ter pelo menos 8 caracteres.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Este e-mail já está cadastrado.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $userId = (int)$pdo->lastInsertId();
            $seed = $pdo->prepare('INSERT INTO categorias (usuario_id, nome) VALUES (?, ?)');
            foreach (['Programação Web', 'Banco de Dados', 'Projeto Integrador', 'Outras atividades'] as $category) {
                $seed->execute([$userId, $category]);
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            flash('success', 'Conta criada com sucesso. Bem-vindo(a) ao Organizaê!');
            redirect('/organizae/dashboard.php');
        }
    }
}

page_header('Criar conta');
?>
<section class="auth-card">
    <p class="eyebrow">COMECE A SE ORGANIZAR</p>
    <h1>Criar sua conta</h1>
    <p class="muted">Cadastre-se para organizar suas atividades acadêmicas.</p>
    <?php foreach ($errors as $error): ?><div class="alert error"><?= e($error) ?></div><?php endforeach; ?>
    <form method="post">
        <?= csrf_field() ?>
        <label>Nome completo<input name="name" value="<?= e($name) ?>" required maxlength="120"></label>
        <label>E-mail<input type="email" name="email" value="<?= e($email) ?>" required maxlength="190"></label>
        <label>Senha<input type="password" name="password" required minlength="8"></label>
        <button class="btn btn-primary btn-full">Criar conta</button>
    </form>
    <p class="form-foot">Já tem uma conta? <a href="/organizae/auth/login.php">Faça login</a>.</p>
</section>
<?php page_footer(); ?>
