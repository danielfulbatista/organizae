<?php
require_once __DIR__ . '/../config/bootstrap.php';
if (is_logged_in()) redirect('/organizae/dashboard.php');

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, nome, senha FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['senha'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['nome'];
        redirect('/organizae/dashboard.php');
    }
    $error = 'E-mail ou senha incorretos.';
}
page_header('Entrar');
?>
<section class="auth-card">
    <p class="eyebrow">BEM-VINDO DE VOLTA</p>
    <h1>Entrar no Organizaê</h1>
    <p class="muted">Acesse seu painel de organização acadêmica.</p>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <?= csrf_field() ?>
        <label>E-mail<input type="email" name="email" value="<?= e($email) ?>" required></label>
        <label>Senha<input type="password" name="password" required></label>
        <button class="btn btn-primary btn-full">Entrar</button>
    </form>
    <p class="form-foot">Ainda não tem conta? <a href="/organizae/auth/cadastro.php">Cadastre-se</a>.</p>
</section>
<?php page_footer(); ?>
