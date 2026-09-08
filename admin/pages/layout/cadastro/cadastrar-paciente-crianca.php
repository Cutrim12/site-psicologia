<?php
    include('../../../protect.php');
    include('../../../../db/conexao.php');
    require_once '../../../../db/paciente-validacao.php';

$erros = [];

  if (isset($_POST['enviar'])) {

    try {

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli->begin_transaction();
    validarCadastroPaciente($_POST);

    // Esta tela cadastra exclusivamente crianças; não depende de um valor enviado pelo navegador.
    $id_tipo_paciente = 2;
    $nome_responsavel = $_POST['nome_responsavel'];
    $nome = $_POST['nome'];
    $id_genero = $_POST['sexo'];
    $idade = $_POST['idade'];
    $nascimento = $_POST['nascimento'];
    $localidade = $_POST['localidade'];

    $id_escolaridade = $_POST['escolaridade'];
    $profissao = $_POST['profissao'];
    $id_renda_familiar = $_POST['renda_familiar'];
    $rg = trim($_POST['rg']);
    $cpf = trim($_POST['cpf']);

    $id_estado_civil = $_POST['estado_civil'];
    $composicao_familiar = $_POST['composicao_familiar'];
    $mora_com = $_POST['mora_com'];
    //$endereco = $_POST['endereco'];
    $bairro = $_POST['bairro'];

    $cidade = $_POST['cidade'];
    $cep = $_POST['cep'];
    $telefone_residencial = $_POST['telefone_residencial'];
    $telefone_recado = $_POST['telefone_recado'];
    $celular = $_POST['celular'];
    $email = $_POST['email'];

    // $id_contato = 1;
    // $id_profissao = 1;
    // $id_endereco = 1;
    $id_maturidade = 2;

        //INSERIR CONTATO
        $stmt_id_contato = $mysqli->prepare("INSERT INTO tbl_contato (id, email, telefone_residencial, telefone_recado, celular) VALUES (NULL, ?, ?, ?, ?)");
        $stmt_id_contato->bind_param("ssss", $email, $telefone_residencial, $telefone_recado, $celular);
        $stmt_id_contato->execute();
        $id_contato = $mysqli->insert_id;
        $stmt_id_contato->close();

        //INSERT PROFISSAO
        $stmt_id_profissao = $mysqli->prepare("INSERT INTO tbl_profissao (id, profissao) VALUES (NULL, ?)");
        $stmt_id_profissao->bind_param("s", $profissao);
        $stmt_id_profissao->execute();
        $id_profissao = $mysqli->insert_id;
        $stmt_id_profissao->close();

        //INSERT BAIRRO
        $stmt_id_bairro = $mysqli->prepare("INSERT INTO tbl_bairro (id, bairro) VALUES (NULL, ?)");
        $stmt_id_bairro->bind_param("s", $bairro);
        $stmt_id_bairro->execute();
        $id_bairro = $mysqli->insert_id;
        $stmt_id_bairro->close();

        //INSERT LOGRADOURO
        $stmt_id_logradouro = $mysqli->prepare("INSERT INTO tbl_logradouro (id, logradouro) VALUES (NULL, ?)");
        $stmt_id_logradouro->bind_param("s", $localidade);
        $stmt_id_logradouro->execute();
        $id_logradouro = $mysqli->insert_id;
        $stmt_id_logradouro->close();

        //INSERT ENDEREÇO
        $stmt_id_endereco = $mysqli->prepare("INSERT INTO tbl_endereco (id, id_bairro, id_logradouro, cep) VALUES (NULL, ?, ?, ?)");
        $stmt_id_endereco->bind_param("iis", $id_bairro, $id_logradouro, $cep);
        $stmt_id_endereco->execute();
        $id_endereco = $mysqli->insert_id;
        $stmt_id_endereco->close();

        // INSERIR PACIENTE
        $stmt_id_paciente = $mysqli->prepare("INSERT INTO tbl_paciente (id, nome, nascimento, rg, cpf, id_tipo_paciente, id_genero, id_contato, id_escolaridade, id_profissao, id_renda_familiar, id_estado_civil, id_endereco, id_maturidade, nome_responsavel) 
        VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_id_paciente->bind_param("ssssiiiiiiiiis", $nome, $nascimento, $rg, $cpf, $id_tipo_paciente, $id_genero, $id_contato, $id_escolaridade, $id_profissao, $id_renda_familiar, $id_estado_civil, $id_endereco, $id_maturidade, $nome_responsavel);
        $stmt_id_paciente->execute();
        $id_paciente = $mysqli->insert_id;
        $stmt_id_paciente->close();

        //ATUALIZAR TABELAS
        // $stmt_update_contato = $mysqli->prepare("UPDATE tbl_contato SET id_paciente = ? WHERE id = ?");
        // $stmt_update_contato->bind_param("ii", $id_paciente, $id_contato);
        // $stmt_update_contato->execute();
        // $stmt_update_contato->close();

        // $stmt_update_contato = $mysqli->prepare("UPDATE tbl_endereco SET id_paciente = ? WHERE id = ?");
        // $stmt_update_contato->bind_param("ii", $id_paciente, $id_endereco);
        // $stmt_update_contato->execute();
        // $stmt_update_contato->close();

        $mysqli->commit();
        header('Location: cadastro-sucesso.php');
        exit;

} catch (PacienteValidationException $e) {
        $mysqli->rollback();
    $erros = $e->erros;
    } catch (Exception $e) {
    $mysqli->rollback();
    $erros['geral'] = 'Não foi possível concluir o cadastro. Tente novamente.';
    }
}

$tipo_paciente_titulo = 'Criança';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../../dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="../../../dist/css/skins/_all-skins.min.css">
    <link rel="shortcut icon" href="../../../../img/favicon.png" type="image/x-icon">
    <title>CLÍNICA | ADMIN</title>
    <style>
        .field-error { color: #a94442; margin: -8px 0 10px; }
        .patient-form label { margin-top: 8px; }
        .patient-form input, .patient-form select { margin-bottom: 12px; }
    </style>
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
    <?php include 'paciente-layout-header.php'; ?>
            <div class="patient-form">

            <label>Nome do(a) Responsável:</label>
            <input class="form-control" type="text" id="nome_responsavel" name="nome_responsavel" value="<?php echo valorCampoPaciente('nome_responsavel'); ?>" placeholder="Nome do Responsável" required>
            <?php echo erroCampoPaciente($erros, 'nome_responsavel'); ?>
           
            <label>Nome da Criança:</label>
            <input class="form-control" type="text" id="nome" name="nome" value="<?php echo valorCampoPaciente('nome'); ?>" placeholder="Nome da criança" required>
            <?php echo erroCampoPaciente($erros, 'nome'); ?>
            
            <label for="sexo">Sexo:</label>
            <select class="form-control" id="sexo" name="sexo" required>
                <option value='' disabled selected>Sexo</option>
                <?php
                    $sql_genero = "SELECT * FROM tbl_genero";
                    $result_genero = mysqli_query($mysqli, $sql_genero);
                    while ($row = mysqli_fetch_assoc($result_genero)) {
                ?>
                        <option value='<?php echo $row['id'] ?>'<?php echo opcaoSelecionadaPaciente('sexo', $row['id']); ?>> <?php echo $row['sexo'] ?> </option>
                <?php
                    }
                ?>
            </select>
            <?php echo erroCampoPaciente($erros, 'sexo'); ?>
            
            <label>Idade:</label>
            <input class="form-control" type="number" id="idade" name="idade" value="<?php echo valorCampoPaciente('idade'); ?>" placeholder="Idade" required>
            
            <label>Nascimento:</label>
            <input class="form-control" type="date" id="nascimento" name="nascimento" value="<?php echo valorCampoPaciente('nascimento'); ?>" placeholder="Nascimento" required>
            <?php echo erroCampoPaciente($erros, 'nascimento'); ?>
            
            <label>Localidade:</label>
            <input class="form-control" type="text" id="localidade" name="localidade" value="<?php echo valorCampoPaciente('localidade'); ?>" placeholder="Localidade" required>
            
            <label for="escolaridade">Escolaridade:</label>
            <select class="form-control" id="escolaridade" name="escolaridade" required>
                <option value='' disabled selected>Escolaridade</option>
                <?php
                    $sql_escolaridade = "SELECT * FROM tbl_escolaridade";
                    $result_escolaridade = mysqli_query($mysqli, $sql_escolaridade);
                    while ($row = mysqli_fetch_assoc($result_escolaridade)) {
                ?>
                        <option value='<?php echo $row['id'] ?>'<?php echo opcaoSelecionadaPaciente('escolaridade', $row['id']); ?>> <?php echo $row['escolaridade'] ?> </option>
                <?php
                    }
                ?>
            </select>
            <?php echo erroCampoPaciente($erros, 'escolaridade'); ?>
            
            <label>Profissão do(a) Responsável:</label>
            <input class="form-control" type="text" id="profissao" name="profissao" value="<?php echo valorCampoPaciente('profissao'); ?>" placeholder="Profissão" required>
            <?php echo erroCampoPaciente($erros, 'profissao'); ?>
            
            <label>Renda Familiar:</label>
            <select class="form-control" id="renda_familiar" name="renda_familiar" required>
                    <option value='' disabled selected>Renda Familiar</option>
                    <?php
                        $sql_renda_familiar = "SELECT * FROM tbl_renda_familiar";
                        $result_renda_familiar = mysqli_query($mysqli, $sql_renda_familiar);
                        while($row = mysqli_fetch_assoc($result_renda_familiar)) {
                    ?>
                            <option value='<?php echo $row['id']?>'<?php echo opcaoSelecionadaPaciente('renda_familiar', $row['id']); ?>> <?php echo $row['renda'] ?> </option>
                    <?php
                        }
                    ?>
            </select>
            <?php echo erroCampoPaciente($erros, 'renda_familiar'); ?>
            
            <label>RG:</label>
            <input class="form-control" type="text" id="rg" name="rg" value="<?php echo valorCampoPaciente('rg'); ?>" placeholder="RG ORGÃO EXPEDIDOR" maxlength="20" required>
            <?php echo erroCampoPaciente($erros, 'rg'); ?>
            
            <label>CPF:</label>
            <input class="form-control" type="text" id="cpf" name="cpf" value="<?php echo valorCampoPaciente('cpf'); ?>" placeholder="CPF" inputmode="numeric" maxlength="14" required>
            <?php echo erroCampoPaciente($erros, 'cpf'); ?>
            
            <label>Composição Familiar:</label>
            <input class="form-control" type="text" id="composicao_familiar" name="composicao_familiar" placeholder="Composição familiar" required>
            
            <label>Estado Civil do(a) Responsável:</label>
            <select class="form-control" id="estado_civil" name="estado_civil" required>
                <option value='' disabled selected>Estado Civil</option>
                <?php
                    $sql_estado_civil = "SELECT * FROM tbl_estado_civil";
                    $result_estado_civil = mysqli_query($mysqli, $sql_estado_civil);
                    while ($row = mysqli_fetch_assoc($result_estado_civil)) {
                ?>
                        <option value='<?php echo $row['id'] ?>'<?php echo opcaoSelecionadaPaciente('estado_civil', $row['id']); ?>> <?php echo $row['estado_civil'] ?> </option>
                <?php
                    }
                ?>
            </select>
            <?php echo erroCampoPaciente($erros, 'estado_civil'); ?>
            
            <label>Mora com quem?</label>
            <input class="form-control" type="text" id="mora_com" name="mora_com" placeholder="Mora Com Quem?" required>
            
            <label>Bairro:</label>
            <input class="form-control" type="text" id="bairro" name="bairro" value="<?php echo valorCampoPaciente('bairro'); ?>" placeholder="Bairro" required>
            <?php echo erroCampoPaciente($erros, 'bairro'); ?>
            
            <label>Cidade:</label>
            <input class="form-control" type="text" id="cidade" name="cidade" value="<?php echo valorCampoPaciente('cidade'); ?>" placeholder="Cidade" required>
            
            <label>CEP:</label>
            <input class="form-control" type="text" id="cep" name="cep" value="<?php echo valorCampoPaciente('cep'); ?>" placeholder="CEP" required>
            <?php echo erroCampoPaciente($erros, 'cep'); ?>
            
            <label>Telefone (Residencial):</label>
            <input class="form-control" type="tel" id="telefone_residencial" name="telefone_residencial" value="<?php echo valorCampoPaciente('telefone_residencial'); ?>" placeholder="Telefone (Residencial)" required>
            <?php echo erroCampoPaciente($erros, 'telefone_residencial'); ?>
            
            <label>Telefone (Recado):</label>
            <input class="form-control" type="tel" id="telefone_recado" name="telefone_recado" value="<?php echo valorCampoPaciente('telefone_recado'); ?>" placeholder="Telefone (Recado)" required>
            <?php echo erroCampoPaciente($erros, 'telefone_recado'); ?>
            
            <label>Celular do(a) Responsável:</label>
            <input class="form-control" type="tel" id="celular" name="celular" value="<?php echo valorCampoPaciente('celular'); ?>" placeholder="Celular" required>
            <?php echo erroCampoPaciente($erros, 'celular'); ?>
            
            <label>Email do(a) Responsável:</label>
            <input class="form-control" type="email" id="email" name="email" value="<?php echo valorCampoPaciente('email'); ?>" placeholder="Email" required>
            <?php echo erroCampoPaciente($erros, 'email'); ?>
            </div>
    <?php include 'paciente-layout-footer.php'; ?>
