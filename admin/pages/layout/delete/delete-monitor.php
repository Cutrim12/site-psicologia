<?php
include('../../../protect.php');
include('../../../../db/conexao.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $mysqli->prepare('UPDATE tbl_monitor SET id_disponibilidade = 2 WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
header('Location: ../monitor.php?msg=Monitor inativado com sucesso!');
exit;
?>
