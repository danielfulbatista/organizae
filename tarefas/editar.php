<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login(); $uid=current_user_id(); $id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
$stmt=$pdo->prepare('SELECT * FROM tarefas WHERE id=? AND usuario_id=?');$stmt->execute([$id,$uid]);$task=$stmt->fetch();
if(!$task){http_response_code(404);exit('Tarefa não encontrada.');}
$cats=$pdo->prepare('SELECT id,nome FROM categorias WHERE usuario_id=? ORDER BY nome');$cats->execute([$uid]);$categories=$cats->fetchAll();$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$title=trim($_POST['titulo']??'');$description=trim($_POST['descricao']??'');$due=$_POST['prazo']??'';$cat=(int)($_POST['categoria_id']??0);
if($title==='')$errors[]='Informe o título.';if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$due))$errors[]='Informe um prazo válido.';
$check=$pdo->prepare('SELECT id FROM categorias WHERE id=? AND usuario_id=?');$check->execute([$cat,$uid]);if(!$check->fetch())$errors[]='Categoria inválida.';
if(!$errors){$up=$pdo->prepare('UPDATE tarefas SET titulo=?,descricao=?,prazo=?,categoria_id=? WHERE id=? AND usuario_id=?');$up->execute([$title,$description?:null,$due,$cat,$id,$uid]);flash('success','Tarefa atualizada!');redirect('/organizae/tarefas/index.php');}
$task=array_merge($task,['titulo'=>$title,'descricao'=>$description,'prazo'=>$due,'categoria_id'=>$cat]);}
page_header('Editar tarefa');?>
<div class="narrow"><p class="eyebrow">ATUALIZAÇÃO</p><h1>Editar tarefa</h1><?php foreach($errors as $er):?><div class="alert error"><?=e($er)?></div><?php endforeach;?>
<form method="post" class="panel form-panel"><?=csrf_field()?>
<label>Título<input name="titulo" value="<?=e($task['titulo'])?>" required maxlength="180"></label>
<label>Categoria<select name="categoria_id" required><?php foreach($categories as $c):?><option value="<?=$c['id']?>" <?=(int)$task['categoria_id']===(int)$c['id']?'selected':''?>><?=e($c['nome'])?></option><?php endforeach;?></select></label>
<label>Prazo<input type="date" name="prazo" value="<?=e($task['prazo'])?>" required></label>
<label>Descrição<textarea name="descricao" rows="4"><?=e($task['descricao']??'')?></textarea></label>
<div class="form-actions"><button class="btn btn-primary">Salvar alterações</button><a class="btn btn-outline" href="/organizae/tarefas/index.php">Cancelar</a></div></form></div>
<?php page_footer();?>
