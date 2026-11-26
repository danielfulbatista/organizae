<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();
$uid = current_user_id();
$category = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT) ?: 0;
$status = $_GET['status'] ?? '';
$date = $_GET['prazo'] ?? '';
$where = ['t.usuario_id = ?'];
$params = [$uid];
if ($category > 0) { $where[] = 't.categoria_id = ?'; $params[] = $category; }
if (in_array($status, ['pendente', 'concluida'], true)) { $where[] = 't.status = ?'; $params[] = $status; }
if ($status === 'atrasada') { $where[] = "t.status = 'pendente' AND t.prazo < CURDATE()"; }
if ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) { $where[] = 't.prazo = ?'; $params[] = $date; }
$sql = "SELECT t.*, c.nome AS categoria FROM tarefas t LEFT JOIN categorias c ON c.id=t.categoria_id WHERE " . implode(' AND ', $where) . " ORDER BY t.prazo ASC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $tasks = $stmt->fetchAll();
$cats = $pdo->prepare('SELECT id,nome FROM categorias WHERE usuario_id=? ORDER BY nome'); $cats->execute([$uid]); $categories = $cats->fetchAll();
page_header('Tarefas');
?>
<div class="page-heading"><div><p class="eyebrow">ACOMPANHAMENTO</p><h1>Minhas tarefas</h1><p class="muted">Organize, acompanhe e atualize suas atividades.</p></div><a class="btn btn-primary" href="/organizae/tarefas/criar.php">+ Nova tarefa</a></div>
<section class="panel">
<form method="get" class="filters">
<label>Categoria<select name="categoria"><option value="">Todas</option><?php foreach($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $category===(int)$c['id']?'selected':'' ?>><?= e($c['nome']) ?></option><?php endforeach; ?></select></label>
<label>Status<select name="status"><option value="">Todos</option><option value="pendente" <?= $status==='pendente'?'selected':'' ?>>Pendentes</option><option value="concluida" <?= $status==='concluida'?'selected':'' ?>>Concluídas</option><option value="atrasada" <?= $status==='atrasada'?'selected':'' ?>>Atrasadas</option></select></label>
<label>Prazo<input type="date" name="prazo" value="<?= e($date) ?>"></label>
<button class="btn btn-secondary">Filtrar</button><a class="btn btn-outline" href="/organizae/tarefas/index.php">Limpar</a>
</form>
<?php if (!$tasks): ?><div class="empty-state"><strong>Nenhuma tarefa encontrada.</strong><p>Altere os filtros ou cadastre uma nova tarefa.</p></div><?php else: ?>
<div class="table-wrap"><table><thead><tr><th>Tarefa</th><th>Categoria</th><th>Prazo</th><th>Status</th><th>Ações</th></tr></thead><tbody>
<?php foreach($tasks as $t): $late=$t['status']==='pendente' && $t['prazo']<date('Y-m-d'); ?>
<tr><td><strong><?= e($t['titulo']) ?></strong><br><small><?= e($t['descricao'] ?? '') ?></small></td><td><?= e($t['categoria'] ?? 'Sem categoria') ?></td><td><?= date('d/m/Y',strtotime($t['prazo'])) ?></td><td><span class="pill <?= $late?'pill-danger':($t['status']==='concluida'?'pill-success':'pill-warning') ?>"><?= $late?'Atrasada':($t['status']==='concluida'?'Concluída':'Pendente') ?></span></td><td class="actions">
<a href="/organizae/tarefas/editar.php?id=<?= (int)$t['id'] ?>">Editar</a>
<form method="post" action="/organizae/tarefas/alternar.php" class="inline-form"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$t['id'] ?>"><button class="link-button"><?= $t['status']==='concluida'?'Reabrir':'Concluir' ?></button></form>
<form method="post" action="/organizae/tarefas/excluir.php" class="inline-form" data-confirm="Tem certeza que deseja excluir esta tarefa?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$t['id'] ?>"><button class="link-button danger-text">Excluir</button></form>
</td></tr>
<?php endforeach; ?>
</tbody></table></div><?php endif; ?>
</section>
<?php page_footer(); ?>
