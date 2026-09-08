<?php
    // 127.0.0.1 força a conexão TCP com o MariaDB do LAMPP em vez do soquete local do sistema.
    $host = '127.0.0.1';
    $usuario = 'root';
    $senha = '';
    $database = 'db_psycology';
    $porta = 3306;

    $mysqli = new mysqli($host, $usuario, $senha, $database, $porta);

    if ($mysqli->error) {
        die("Falha ao conectar no banco de dados: " . $mysqli->error);
    }

    date_default_timezone_set("America/Sao_Paulo");
?>
