<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();
$uid=current_user_id(); $errors=[]; $title=''; $description=''; $due=''; $category=0;
$stmt=$pdo->prepare('SELECT id,nome FROM categorias WHERE usuario_id=? ORDER BY nome'); $stmt->execute([$uid]); $categories=$stmt->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $title=trim($_POST['titulo']??''); $description=trim($_POST['descricao']??''); $due=$_POST['prazo']??''; $category=(int)($_POST['categoria_id']??0);
 if($title==='')$errors[]='Informe o título.'; if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$due))$errors[]='Informe um prazo válido.';
 $check=$pdo->prepare('SELECT id FROM categorias WHERE id=? AND usuario_id=?'); $check->execute([$category,$uid]); if(!$check->fetch())$errors[]='Selecione uma categoria válida.';
 if(!$errors){$stmt=$pdo->prepare('INSERT INTO tarefas(usuario_id,categoria_id,titulo,descricao,prazo,status) VALUES(?,?,?,?,?,?)');$stmt->execute([$uid,$category,$title,$description?:null,$due,'pendente']);flash('success','Tarefa cadastrada!');redirect('/organizae/tarefas/index.php');}
}
page_header('Nova tarefa');
?>
<div class="narrow"><p class="eyebrow">NOVA ATIVIDADE</p><h1>Cadastrar tarefa</h1><?php foreach($errors as $er):?><div class="alert error"><?=e($er)?></div><?php endforeach;?>
<form method="post" class="panel form-panel"><?=csrf_field()?>
<label>Título da tarefa<input name="titulo" value="<?=e($title)?>" required maxlength="180" placeholder="Ex.: Entregar atividade de PHP"></label>
<label>Categoria / disciplina<select name="categoria_id" required><option value="">Selecione</option><?php foreach($categories as $c):?><option value="<?=$c['id']?>" <?=$category===(int)$c['id']?'selected':''?>><?=e($c['nome'])?></option><?php endforeach;?></select></label>
<label>Prazo de entrega<input type="date" name="prazo" value="<?=e($due)?>" required></label>
<label>Descrição (opcional)<textarea name="descricao" rows="4"><?=e($description)?></textarea></label>
<div class="form-actions"><button class="btn btn-primary">Salvar tarefa</button><a class="btn btn-outline" href="/organizae/tarefas/index.php">Cancelar</a></div>
</form></div>
<?php page_footer(); ?>
