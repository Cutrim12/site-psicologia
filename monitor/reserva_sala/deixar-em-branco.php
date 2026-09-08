<?php
    include('../../db/conexao.php');
    include('../monitor-protect.php');
?>

<?php

$id = $_GET['id'] ?? NULL;

try {

    // ATUALIZAR CONSULTA PARA 'FINALIZADO'
    $stmt_paciente_terapeuta = $mysqli->prepare("
            SELECT sr.id_paciente, sr.id_terapeuta FROM tbl_sala_reservada sr WHERE sr.id = ?
    ");
    $stmt_paciente_terapeuta->bind_param("i", $id);
    $stmt_paciente_terapeuta->execute();
    $result = $stmt_paciente_terapeuta->get_result();

    $id_paciente = NULL;
    $id_terapeuta = NULL;

    if($row = $result->fetch_assoc()) {
        $id_paciente = $row['id_paciente'];
        $id_terapeuta = $row['id_terapeuta'];
    }

    $agendado = 1;
    $finalizada = 2;

    $stmt_id_consulta = $mysqli->prepare("
        SELECT pt.id AS id_consulta
        FROM tbl_paciente_terapeuta pt
        WHERE pt.id_paciente = ? AND pt.id_terapeuta = ? AND pt.id_status_consulta = ?
        LIMIT 1
    ");
    $stmt_id_consulta->bind_param("iii", $id_paciente, $id_terapeuta, $agendado);
    $stmt_id_consulta->execute();
    $result = $stmt_id_consulta->get_result();
    $id_consulta = null;

    if($row = $result->fetch_assoc()) {
        $id_consulta = $row['id_consulta'];
    }
    $stmt_consulta = $mysqli->prepare("UPDATE tbl_paciente_terapeuta 
    SET id_status_consulta = ? WHERE id = ?");
    $stmt_consulta->bind_param("ii", $finalizada, $id_consulta);
    $stmt_consulta->execute();
    $stmt_consulta->close();

    // DEIXAR SALA LIVRE
    $null = NULL;
    $id_status = 2;

    $sala = "---";

    $stmt_sala_reservada = $mysqli->prepare("UPDATE tbl_sala_reservada 
                                            SET id_status = ?, 
                                            id_terapeuta = ?, 
                                            id_paciente = ?,
                                            sala = ? 
                                            WHERE id = ?");
    $stmt_sala_reservada->bind_param("iiisi",  
                                    $id_status, 
                                    $null, 
                                    $null,
                                    $sala,
                                    $id);

    $stmt_sala_reservada->execute();
    $stmt_sala_reservada->close();

    header('Location: mudar-cor.php');
} catch(Exception $e) {
    echo "Error " . $e;
}

?>
