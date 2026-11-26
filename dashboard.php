<?php
require_once __DIR__ . '/config/bootstrap.php';
require_login();
$uid = current_user_id();

$stmt = $pdo->prepare("SELECT
    COUNT(*) AS total,
    SUM(status = 'pendente') AS pendentes,
    SUM(status = 'concluida') AS concluidas,
    SUM(status = 'pendente' AND prazo < CURDATE()) AS atrasadas
    FROM tarefas WHERE usuario_id = ?");
$stmt->execute([$uid]);
$stats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT t.id, t.titulo, t.prazo, c.nome AS categoria
    FROM tarefas t LEFT JOIN categorias c ON c.id = t.categoria_id
    WHERE t.usuario_id = ? AND t.status = 'pendente'
    ORDER BY t.prazo ASC LIMIT 5");
$stmt->execute([$uid]);
$upcoming = $stmt->fetchAll();

page_header('Painel');
?>
<div class="page-heading">
    <div><p class="eyebrow">SEU ESPAÇO DE ORGANIZAÇÃO</p><h1>Olá, <?= e($_SESSION['user_name']) ?>!</h1><p class="muted">Veja como estão suas atividades acadêmicas.</p></div>
    <a class="btn btn-primary" href="/organizae/tarefas/criar.php">+ Nova tarefa</a>
</div>
<div class="stats-grid">
    <article class="stat-card"><span>Total de tarefas</span><strong><?= (int)($stats['total'] ?? 0) ?></strong><small>Todas as atividades cadastradas</small></article>
    <article class="stat-card"><span>Pendentes</span><strong><?= (int)($stats['pendentes'] ?? 0) ?></strong><small>Aguardando conclusão</small></article>
    <article class="stat-card"><span>Concluídas</span><strong><?= (int)($stats['concluidas'] ?? 0) ?></strong><small>Atividades finalizadas</small></article>
    <article class="stat-card stat-danger"><span>Atrasadas</span><strong><?= (int)($stats['atrasadas'] ?? 0) ?></strong><small>Pendentes com prazo vencido</small></article>
</div>
<section class="panel">
    <div class="panel-heading"><div><h2>Próximas tarefas</h2><p class="muted">Suas cinco atividades pendentes mais próximas.</p></div><a href="/organizae/tarefas/index.php">Ver todas</a></div>
    <?php if (!$upcoming): ?>
        <div class="empty-state"><strong>Nenhuma tarefa pendente por enquanto.</strong><p>Cadastre uma atividade para começar.</p></div>
    <?php else: ?>
        <div class="table-wrap"><table><thead><tr><th>Tarefa</th><th>Categoria</th><th>Prazo</th><th>Situação</th></tr></thead><tbody>
        <?php foreach ($upcoming as $task): $late = $task['prazo'] < date('Y-m-d'); ?>
            <tr><td><?= e($task['titulo']) ?></td><td><?= e($task['categoria'] ?? 'Sem categoria') ?></td><td><?= date('d/m/Y', strtotime($task['prazo'])) ?></td><td><span class="pill <?= $late ? 'pill-danger' : 'pill-warning' ?>"><?= $late ? 'Atrasada' : 'Pendente' ?></span></td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
    <?php endif; ?>
</section>
<?php page_footer(); ?>
