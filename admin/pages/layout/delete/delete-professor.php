<?php
include('../../../protect.php');
include('../../../../db/conexao.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $mysqli->prepare('UPDATE tbl_professor SET id_disponibilidade = 2 WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
header('Location: ../professores.php?msg=Professor inativado com sucesso!');
exit;
?>
