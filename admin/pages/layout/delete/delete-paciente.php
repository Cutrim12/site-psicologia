<?php
include('../../../protect.php');
include('../../../../db/conexao.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ../pacientes.php?msg=Paciente inválido.');
    exit;
}
$stmt = $mysqli->prepare('UPDATE tbl_paciente SET ativo = 0 WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
header('Location: ../pacientes.php?msg=Paciente inativado com sucesso!');
exit;
?>
