<?php
    include('../../../db/conexao.php');
    include('../../../admin/admin-protect.php');

if (!isset($_GET['compact']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Location: reservar-sala.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? 1;
$status_manutencao = 5;

$stmt_reserva_atual = $mysqli->prepare('SELECT sala_cod, id_status FROM tbl_sala_reservada WHERE id = ?');
$stmt_reserva_atual->bind_param('i', $id);
$stmt_reserva_atual->execute();
$reserva_atual = $stmt_reserva_atual->get_result()->fetch_assoc();
$stmt_reserva_atual->close();

if (!$reserva_atual) {
    http_response_code(404);
    exit('Sala não encontrada.');
}

if (isset($_POST['bloquear_manutencao'])) {
    $stmt_bloquear = $mysqli->prepare("UPDATE tbl_sala_reservada SET id_status = ?, id_terapeuta = NULL, id_paciente = NULL, sala = 'EM MANUTENÇÃO' WHERE id = ? AND id_status = 2");
    $stmt_bloquear->bind_param('ii', $status_manutencao, $id);
    $stmt_bloquear->execute();
    $bloqueada = $stmt_bloquear->affected_rows === 1;
    $stmt_bloquear->close();

    if (!$bloqueada) {
        exit('A sala só pode ser colocada em manutenção quando estiver disponível.');
    }

    // O bloqueio é imediato: atualiza a grade principal e fecha este popup.
    echo '<script>if (window.parent !== window) { window.parent.postMessage({type: "reserva-atualizada"}, window.location.origin); } else if (window.opener) { window.opener.location.reload(); window.close(); }</script>';
    exit;
}

if (isset($_POST['liberar_manutencao'])) {
    $stmt_liberar = $mysqli->prepare("UPDATE tbl_sala_reservada SET id_status = 2, sala = '---' WHERE id = ? AND id_status = ?");
    $stmt_liberar->bind_param('ii', $id, $status_manutencao);
    $stmt_liberar->execute();
    $liberada = $stmt_liberar->affected_rows === 1;
    $stmt_liberar->close();

    if (!$liberada) {
        http_response_code(409);
        exit('A sala não está bloqueada para manutenção.');
    }

    echo '<script>if (window.parent !== window) { window.parent.postMessage({type: "reserva-atualizada"}, window.location.origin); } else { window.location.href = "reservar-sala.php"; }</script>';
    exit;
}

$where = "AND pt.id_status_consulta = 3";
$id_terapeuta = isset($_GET['id_terapeuta_selecionado']) ? $_GET['id_terapeuta_selecionado'] : '';

if (!empty($id_terapeuta)) {
  $where = "AND pt.id_status_consulta = 3  AND t.id = " . $id_terapeuta;
}

if (isset($_POST['salvar'])) {

    if ((int) $reserva_atual['id_status'] === $status_manutencao) {
        http_response_code(409);
        exit('Não é possível reservar esta sala: ela está em manutenção.');
    }
    if ((int) ($_POST['id_status'] ?? 0) === $status_manutencao) {
        http_response_code(422);
        exit('Use o botão de manutenção para bloquear a sala.');
    }

    try {
        if(!empty($_POST['id_terapeuta']) && !empty($_POST['id_paciente'])) {
            $id_terapeuta = !empty($_POST['id_terapeuta']) ? $_POST['id_terapeuta'] : NULL;
            $id_paciente = !empty($_POST['id_paciente']) ? $_POST['id_paciente'] : NULL;
            $id_status = $_POST['id_status'];
    
            //PEGAR O NOME DO TERAPEUTA
            $stmt_terapeuta = $mysqli->prepare("SELECT nome FROM tbl_user_terapeuta WHERE id = ?");
            $stmt_terapeuta->bind_param("i",  $id_terapeuta);
            $stmt_terapeuta->execute();
    
            $result_terapeuta = $stmt_terapeuta->get_result();
            $terapeuta = $result_terapeuta->fetch_assoc();
            $nome_terapeuta = $terapeuta['nome'] ?? "-";
            $stmt_terapeuta->close();
            
    
            //PEGAR O NOME DO PACIENTE
            $stmt_paciente = $mysqli->prepare("SELECT nome FROM tbl_paciente WHERE id = ?");
            $stmt_paciente->bind_param("i", $id_paciente);
            $stmt_paciente->execute();
    
            $result_paciente = $stmt_paciente->get_result();
            $paciente = $result_paciente->fetch_assoc();
            $nome_paciente = $paciente['nome'] ?? "-";
            $stmt_paciente->close();
    
            $sala =  $nome_terapeuta . " - " . $nome_paciente;
    
            //ATUALIZAR SALA
            $id_terapeuta_bind = $id_terapeuta != null ? $id_terapeuta : null;
            $id_paciente_bind = $id_paciente != null ? $id_paciente : null;
    
            $stmt_sala_reservada = $mysqli->prepare("UPDATE tbl_sala_reservada 
                                                    SET id_status = ?, 
                                                    id_terapeuta = ?, 
                                                    id_paciente = ?,
                                                    sala = ? 
                                                    WHERE id = ?");

            $stmt_sala_reservada->bind_param("iiisi",  
                                            $id_status, 
                                            $id_terapeuta_bind, 
                                            $id_paciente_bind,
                                            $sala,
                                            $id);
    
            $stmt_sala_reservada->execute();
            $stmt_sala_reservada->close();

            // ATUALIZAR CONSULTA PARA 'AGENDADO'
            $aguardando_consulta = 3;
            $agendado = 1;
            $stmt_id_consulta = $mysqli->prepare("
                SELECT pt.id AS id_consulta
                FROM tbl_paciente_terapeuta pt
                WHERE pt.id_paciente = ? AND pt.id_terapeuta = ? AND pt.id_status_consulta = ?
                LIMIT 1
            ");
            $stmt_id_consulta->bind_param("iii", $id_paciente, $id_terapeuta, $aguardando_consulta);
            $stmt_id_consulta->execute();
            $result = $stmt_id_consulta->get_result();
            $id_consulta = null;

            if($row = $result->fetch_assoc()) {
                $id_consulta = $row['id_consulta'];
            }
            $stmt_consulta = $mysqli->prepare("UPDATE tbl_paciente_terapeuta 
            SET id_status_consulta = ? WHERE id = ?");
            $stmt_consulta->bind_param("ii", $agendado, $id_consulta);
            $stmt_consulta->execute();
            $stmt_consulta->close();
    
    
            // INSERIR DADOS NO TBL_SALA_RESERVADA_HISTORICO
            $stmt_sala_reservada_dados = $mysqli->prepare("SELECT * FROM tbl_sala_reservada WHERE id = ?");
            $stmt_sala_reservada_dados->bind_param("i", $id);
            $stmt_sala_reservada_dados->execute();
    
            $result_sala_reservada_dados = $stmt_sala_reservada_dados->get_result();
            $sala_reservada_dados = $result_sala_reservada_dados->fetch_assoc();
            $id_turno = $sala_reservada_dados['id_turno'];
            $id_horario = $sala_reservada_dados['id_horario'];
            $id_semana = $sala_reservada_dados['id_semana'];
            $id_status = $sala_reservada_dados['id_status'];
            $id_terapeuta = $sala_reservada_dados['id_terapeuta'];
            $id_paciente = $sala_reservada_dados['id_paciente'];
            $sala_cod = $sala_reservada_dados['sala_cod'];
            $sala = $sala_reservada_dados['sala'];
            $stmt_sala_reservada_dados->close();
    
            $stmt_sala_reservada_historico = $mysqli->prepare("INSERT INTO tbl_sala_reservada_historico 
            (id_turno, id_horario, id_semana, id_status, id_terapeuta, id_paciente, sala_cod, sala) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_sala_reservada_historico->bind_param("iiiiiiss",
                                                    $id_turno,
                                                    $id_horario,
                                                    $id_semana,
                                                    $id_status,
                                                    $id_terapeuta,
                                                    $id_paciente,
                                                    $sala_cod,
                                                    $sala
            );
            $stmt_sala_reservada_historico->execute();
            $stmt_sala_reservada_historico->close();

            echo '<script>if (window.parent !== window) { window.parent.postMessage({type: "reserva-atualizada"}, window.location.origin); } else if (window.opener) { window.opener.location.reload(); window.close(); }</script>';
        }

    } catch(Exception $e) {
        echo "Error " . $e;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="shortcut icon" href="../../img/favicon.png" type="image/x-icon">
    <title>Mudar Cor</title>
    <style>
        body {
            background-color: rgb(185, 232, 232);
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container-fluid {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
            margin-top: 10%;
        }

        input {
            width: 100%;
            margin-top: 5%;
            text-align: center;
        }

        select {
            width: 80%;
            padding: .6%;
            text-align: center;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            border-radius: 10px;
            margin-bottom: 10%;
        }

        select option {
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
        }

        .div-button {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        button {
            margin-top: 8%;
            padding: 15px 30px;
            font-size: 16px;
            cursor: pointer;
            width: 80%;
            border: none;
            border-radius: 10px;
            font-size: bold;
            font-weight: bold;
            background-color:rgb(0, 109, 148);
            color: white;
            transition: .5s;
        }

        button:hover {
            background-color: rgb(0, 65, 89);
            color: rgb(201, 200, 200);
        }

        .fechar {
            background-color: rgb(238, 41, 41);
        }

        .fechar:hover {
            color: rgb(201, 200, 200);
            background-color: rgb(179, 30, 30);
        }

        .deixarEmBracnho {
            width: 50%;
            color: black;
            background-color: white;
        }

        .deixarEmBracnho:hover {
            color: black;
            background-color: rgb(224, 224, 224);
        }

        .reservada {
            background-color: yellow;
        }

        .reservada:hover {
            background-color: rgb(152, 152, 0);
        }

        .livre {
            background-color: white;
        }

        .livre:hover {
            background-color: rgb(148, 147, 147);
        }


        .encaixe {
            background-color: rgb(198, 89, 17);
        }

        .encaixe:hover {
            background-color: rgb(103, 47, 9);
        }


        .triagem {
            background-color: rgb(0, 176, 240);
        }

        .triagem:hover {
            background-color: rgb(0, 109, 148);
        }

        .mudar_cor {
            background-color: lightgray;
        }

        .grupo {
            background-color: green;
        }

        .infantil {
            background-color: pink;
        }

        .divSala {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            font-family: Arial, Helvetica, sans-serif;
            color:black;
        }

        .titulo-sala {
            margin: 0 0 8px;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 5px;
        }

        .numero-sala {
            color: #111;
            font-size: 72px;
            font-weight: 800;
            letter-spacing: 4px;
            line-height: 1;
            margin: 0;
        }

        .sala-em-manutencao {
            margin: 25% 0 0;
            padding: 25px;
            text-align: center;
            background: #b02a37;
            color: #fff;
            border-radius: 10px;
            font-weight: bold;
        }

        
        label {
            font-weight: bold;
            margin-bottom: 1%;
        }

        body.compact {
            background: #fff;
            display: block;
            height: auto;
            min-height: 100%;
        }

        body.compact .container-fluid {
            height: auto;
            padding: 14px 18px;
        }

        body.compact form {
            height: auto;
            width: 100%;
            margin-top: 8px;
        }

        body.compact input,
        body.compact select {
            width: 100%;
            margin-top: 4px;
            margin-bottom: 10px;
            padding: 7px;
            border-radius: 4px;
        }

        body.compact button {
            width: 100%;
            margin-top: 8px;
            padding: 9px 12px;
            border-radius: 4px;
        }

        body.compact .titulo-sala { font-size: 14px; letter-spacing: 3px; }
        body.compact .numero-sala { font-size: 42px; letter-spacing: 2px; }
        body.compact .sala-em-manutencao { margin: 12px 0 0; padding: 14px; }
        body.compact .sala-em-manutencao h2 { font-size: 20px; margin-top: 0; }
        body.compact .deixarEmBracnho { width: 100%; }
    </style>
</head>
<body class="<?php echo isset($_GET['compact']) ? 'compact' : ''; ?>">
    <div class="container-fluid">
        <?php if ((int) $reserva_atual['id_status'] === $status_manutencao) { ?>
            <div class="divSala"><span class="titulo-sala">SALA</span><h1 class="numero-sala"><?php echo htmlspecialchars(str_pad(preg_replace('/^.*_s/', '', $reserva_atual['sala_cod']), 2, '0', STR_PAD_LEFT)); ?></h1></div>
            <div class="sala-em-manutencao">
                <h2>EM MANUTENÇÃO</h2>
                <p>Esta sala está bloqueada e não pode receber reservas.</p>
                <form method="post"><button type="submit" name="liberar_manutencao">LIBERAR SALA</button></form>
            </div>
        <?php } else { ?>
        <form method="post" id="salvar">
            

            <?php

                $sql_sala_status_name_sala = "SELECT id, sala, sala_cod, id_status FROM tbl_sala_reservada WHERE id = ?";
                $stmt_sala_status_name_sala = $mysqli->prepare($sql_sala_status_name_sala);
                $stmt_sala_status_name_sala->bind_param("i", $id);
                $stmt_sala_status_name_sala->execute();

                $stmt_sala_status_name_sala_result = $stmt_sala_status_name_sala->get_result();
                $sala_status = $stmt_sala_status_name_sala_result->fetch_assoc();
                $name_sala = $sala_status['sala'];
                $numero_sala = preg_replace('/^.*_s/', '', $sala_status['sala_cod'] ?? '');
                $sala_id = $sala_status['id_status'];
            ?>

            <div class="divSala">
                <span class="titulo-sala">SALA</span>
                <h1 class="numero-sala"><?php echo htmlspecialchars(str_pad($numero_sala, 2, '0', STR_PAD_LEFT)) ?></h1>
            </div> <br><br>
        

            <label for="">Sala</label>
            <select name="id_status" required>
                <option class="mudar_cor" value="<?php echo htmlspecialchars($sala_id) ?>" selected>Mudar cor</option>
                <?php
                    // Manutenção é uma transição administrativa própria; não
                    // pode ser escolhida pelo seletor comum de reserva.
                    $sql_sala_status = "SELECT s.id, s.status FROM tbl_status_sala s WHERE s.id != ?";
                    $stmt_sala_status = $mysqli->prepare($sql_sala_status);
                    $stmt_sala_status->bind_param("i", $status_manutencao);
                    $stmt_sala_status->execute();
                    $stmt_sala_status_result = $stmt_sala_status->get_result();

                    
                    while ($sala_status_row = $stmt_sala_status_result->fetch_assoc()) {
                ?>
                        <option value='<?php echo htmlspecialchars($sala_status_row['id']) ?>' 
                                class='<?php echo htmlspecialchars($sala_status_row['status']) ?>'>

                            <?php echo htmlspecialchars($sala_status_row['status']) ?> 
                            
                        </option>
                <?php
                    }
                ?>
            </select>


            <label for="">Terapeuta:</label>
            <select name="id_terapeuta" id="select_terapeuta" required>
                <?php
                    $sql_terapeuta_fixo = "";
                    $terapeuta_fixo = null;
                    
                    if (isset($_GET['id_terapeuta_selecionado']) && !empty($_GET['id_terapeuta_selecionado'])) {
                        $id_terapeuta_selecionado = (int) $_GET['id_terapeuta_selecionado'];

                        $sql_terapeuta_fixo = "
                            SELECT id, nome 
                            FROM tbl_user_terapeuta 
                            WHERE id = ?
                        ";

                        $stmt_terapeuta_fixo = $mysqli->prepare($sql_terapeuta_fixo);
                        $stmt_terapeuta_fixo->bind_param("i", $id_terapeuta_selecionado);
                        $stmt_terapeuta_fixo->execute();
                        $stmt_terapeuta_fixo_result = $stmt_terapeuta_fixo->get_result();
                        $terapeuta_fixo = $stmt_terapeuta_fixo_result->fetch_assoc();
                    } else {
                        $sql_terapeuta_fixo = "SELECT t.id, t.nome 
                            FROM tbl_user_terapeuta t 
                            INNER JOIN tbl_sala_reservada s 
                            ON s.id_terapeuta = t.id 
                            WHERE s.id = ?";
                        
                        $stmt_terapeuta_fixo = $mysqli->prepare($sql_terapeuta_fixo);
                        $stmt_terapeuta_fixo->bind_param("i", $id);
                        $stmt_terapeuta_fixo->execute();
                        $stmt_terapeuta_fixo_result = $stmt_terapeuta_fixo->get_result();
                        $terapeuta_fixo = $stmt_terapeuta_fixo_result->fetch_assoc();
                    }

                    $id_terapeuta_fixo = $terapeuta_fixo["id"] ?? 0;
                    $nome_terapeuta_fixo = $terapeuta_fixo['nome'] ?? "";

                    if(!is_null($nome_terapeuta_fixo) && $nome_terapeuta_fixo != "") {
                        $id_terapeuta_fixo = $terapeuta_fixo['id'];
                        echo '<option value="' . htmlspecialchars($id_terapeuta_fixo) . '" selected> '. htmlspecialchars($nome_terapeuta_fixo) .' </option>';
                    } else if($name_sala == '---') {
                        echo '<option value="' . NULL . '" select> </option>';
                    }
                ?>
                    
                <?php
                    $sql_terapeuta = "SELECT t.id, t.nome 
                                        FROM tbl_user_terapeuta t 
                                        WHERE t.id != ? AND t.id_disponibilidade = 1 
                                        ORDER BY t.nome";

                    $stmt_terapeuta = $mysqli->prepare($sql_terapeuta);
                    $stmt_terapeuta->bind_param("i", $id_terapeuta_fixo);
                    $stmt_terapeuta->execute();
                    $stmt_terapeuta_result = $stmt_terapeuta->get_result();

                    while ($terapeuta = $stmt_terapeuta_result->fetch_assoc()) {
                ?>
                        <option value='<?php echo htmlspecialchars($terapeuta['id']) ?>' class='livre'> 
                            <?php echo htmlspecialchars($terapeuta['nome']) ?> 
                        </option>
                <?php
                    }
                ?>
            </select>


            <label for="">Paciente:</label>
            <select name="id_paciente" required>
                <?php
                    $sql_paciente_fixo = "SELECT p.id, p.nome 
                                        FROM tbl_paciente p 
                                        INNER JOIN tbl_sala_reservada s 
                                        ON s.id_paciente = p.id 
                                        WHERE s.id = ?";

                    $stmt_paciente_fixo = $mysqli->prepare($sql_paciente_fixo);
                    $stmt_paciente_fixo->bind_param("i", $id);
                    $stmt_paciente_fixo->execute();
                    $stmt_paciente_fixo_result = $stmt_paciente_fixo->get_result();
                    $paciente_fixo = $stmt_paciente_fixo_result->fetch_assoc();

                    $id_paciente_fixo = $paciente_fixo["id"] ?? 0;
                    $nome_paciente_fixo = $paciente_fixo["nome"] ?? "";

                    if(!is_null($nome_paciente_fixo) && $nome_paciente_fixo != "") {
                        $id_paciente_fixo = $paciente_fixo["id"];
                        echo '<option value="' . htmlspecialchars($id_paciente_fixo) . '" selected> '. htmlspecialchars($nome_paciente_fixo) .' </option>';
                    } else if($name_sala == '---') {
                        echo '<option value="' . NULL . '" select> </option>';
                    }
                ?>

                <?php
                    $sql_paciente = "SELECT DISTINCT p.id, p.nome
                                            FROM tbl_paciente_terapeuta pt 
                                            JOIN tbl_paciente p ON p.id = pt.id_paciente 
                                            JOIN tbl_user_terapeuta t ON pt.id_terapeuta = t.id 
                                            WHERE p.id != ? AND p.ativo = 1 $where
                                            GROUP BY p.id
                                            ORDER BY p.nome;";

                    //echo $sql_paciente;

                    $stmt_paciente = $mysqli->prepare($sql_paciente);
                    $stmt_paciente->bind_param("i", $id_paciente_fixo);
                    $stmt_paciente->execute();
                    $stmt_paciente_result = $stmt_paciente->get_result();
                    
                    while ($paciente = $stmt_paciente_result->fetch_assoc()) {
                ?>
                        <option value='<?php echo $paciente['id'] ?>' class='livre'> 
                            <?php echo $paciente['nome'] ?> 
                        </option>
                <?php
                    }
                ?>
            </select>
            
            <div class='div-button'>
                <button type="submit" form="salvar" name="salvar" onclick="reloadWindow()">SALVAR</button>
                <button class="fechar" type="button" onclick="closePopup()">FECHAR</button>
            </div>
        </form>

        <form method="GET">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="compact" value="1">
            <input type="hidden" id="id_terapeuta_selecionado" name="id_terapeuta_selecionado" value="">
            <input type="hidden" id="id_consulta" name="id_consulta" value="">
            <button type="submit" id="btn_id_terapeuta_selecionado" style="display:none"></button>
        </form>

        <form action="deixar-em-branco.php" id="deixar_livre" method="GET">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <button form="deixar_livre" class="deixarEmBracnho" type="submit" onclick='reloadWindow()'>Deixar Livre</button>
        </form>
        <form method="post">
            <button class="fechar" type="submit" name="bloquear_manutencao">BLOQUEAR PARA MANUTENÇÃO</button>
        </form>
        <?php } ?>
    </div>
    <script>
        const selectTerapeuta = document.getElementById("select_terapeuta");
        if (selectTerapeuta) selectTerapeuta.addEventListener("change", function(event) {
            var id_terapeuta = document.getElementById("select_terapeuta").value;
            document.getElementById("id_terapeuta_selecionado").value = id_terapeuta;
            document.querySelector("#btn_id_terapeuta_selecionado").click();
        });

        function reloadWindow() {
            if(window.opener) {
                window.opener.location.reload();
            }
        }

        function closePopup() {
            if(window.parent !== window) {
                window.parent.postMessage({type: "fechar-reserva-modal"}, window.location.origin);
            } else if(window.opener) {
                window.close();
            }
        }
    </script>
</body>
</html>
