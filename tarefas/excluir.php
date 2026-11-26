<?php
require_once __DIR__ . '/../config/bootstrap.php'; require_login();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Método não permitido.');}
verify_csrf();$id=(int)($_POST['id']??0);$stmt=$pdo->prepare('DELETE FROM tarefas WHERE id=? AND usuario_id=?');$stmt->execute([$id,current_user_id()]);flash('success','Tarefa excluída.');redirect('/organizae/tarefas/index.php');
