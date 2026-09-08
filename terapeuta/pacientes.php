<?php
include('../db/conexao.php');
include('../admin/protect.php');
include('../admin/contador.php');

$id = $_SESSION['id'] ?? '';
$id = trim($id);

$where = "";
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) {
  $pagina = 1;
}

$offset = ($pagina - 1) * $por_pagina;

if (!empty($busca)) {
  $where = "AND p.nome LIKE '%" . mysqli_real_escape_string($mysqli, $busca) . "%'";
}


if(isset($_POST['finalizar-consulta'])) {

try {
  $id_consulta = $_POST['id_consulta'];
  $id_status_consulta = 2;
  $sql = "UPDATE tbl_paciente_terapeuta SET id_status_consulta = ? WHERE id = ?";
  $stmt_update = $mysqli->prepare($sql);
  $stmt_update->bind_param("ii", $id_status_consulta, $id_consulta);
  $stmt_update->execute();
  $stmt_update->close();

  header('Location: pacientes.php');
} catch(Exception $e) {
  echo $e->getMessage();
}

}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CLÍNICA | Terapeuta</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="../admin/bootstrap/css/bootstrap.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../admin/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../admin/dist/css/skins/_all-skins.min.css">
  <!-- Bootstrap -->
  <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">

  <!-- Font Awesome -->
  <style>
    .indisponivel {
      background-color: lightcoral;
    }
  </style>
</head>

<body class="hold-transition skin-blue fixed sidebar-mini">
  <!-- Site wrapper -->
  <div class="wrapper">

    <header class="main-header">
      <!-- Logo -->
      <a href="#" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>CL</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>CLÍ</b>NICA</span>
      </a>
      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="../admin/dist/img/user.jpg" class="user-image" alt="User Image">
                <span class="hidden-xs"><?php echo $_SESSION['nome']; ?></span>
              </a>
              <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                  <img src="../admin/dist/img/user.jpg" class="img-circle" alt="User Image">
                  <p>
                    <?php echo $_SESSION['nome']; ?>

                  </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-right">
                    <a href="login.php" class="btn btn-danger">Sair</a>
                  </div>
                </li>
              </ul>
            </li>

          </ul>
        </div>
      </nav>
    </header>

    <!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
          <div class="pull-left image">
            <img src="../admin/dist/img/user.jpg" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p><?php echo $_SESSION['nome']; ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>
        <ul class="sidebar-menu">
          <li class="header">CLINICA MENU</li>
          <li class="treeview">
            <a href="#">
              <i class="fa fa-dashboard"></i> <span>Dashboard</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            </ul>
            <ul class="treeview-menu">
              <li class=""><a href="../index.php"><i class="fa fa-dashboard"></i> Sair</a></li>
            </ul>
          </li>
          <li class="treeview active">
            <a href="#">
              <i class="fa fa-gears"></i>
              <span>Gerenciar</span>
              <span class="label label-primary pull-right"></span>
            </a>
            <ul class="treeview-menu">
                <li class="active"><a href="pacientes.php"><i class="fa fa-plus-square"></i> Pacientes</a></li>
                <li><a href="reserva_sala/reservar-sala-segunda.php"><i class="fa fa-plus-square"></i> Salas Reservadas</a></li>
            </ul>
          </li>
    </aside>

    <!-- =============================================== -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          CLINICA PSICOLOGIA
        </h1>
        <ol class="breadcrumb">
          <li><a href="../../../index.php"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="#">Gerenciar</a></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="callout callout-info">
          <h4>AVISO!</h4>
          <p>Nossa versão ainda encontra-se em fases de testes, se você achar algum bug por favor contate o adminstrador!</p>
        </div>
        <!-- Default box -->
        <div class="box">
          <div class="box-header with-border">

            <style>
              #msg {
                color: green;
              }
            </style>

            <?php
            if (isset($_GET['msg'])) {
              $mensagem = $_GET['msg'];
              echo "<h5 class='box-title' id='msg'>$mensagem</h5><br><br>";
            }
            ?>
            <h3 class="box-title">SEUS PACIENTES</h3>
            <div class="box-tools pull-right">
              <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body">

            <form method="GET">
            <input type="text" name="busca" placeholder="Buscar usuário..." style="padding:0.5%;margin-left:3%;" value="<?php echo htmlspecialchars($busca); ?>">
            <button class="btn btn-primary" type="submit">Buscar</button>
          </form>


            <!-- AQUI COMEÇA SUA APLICAÇÃO -->
            <div>
              <table class="table table-hover text-center">
                <thead class="table-dark">
                  <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Nascimento</th>
                    <th scope="col">Telefone</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Finalizar Consulta</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  //$sql = "SELECT * FROM filiais WHERE id_aprovacao = 2";
                  $sql = "SELECT pt.id AS ptId, pt.id_terapeuta, pt.id_paciente, pt.id_status_consulta, 
                          p.nome AS pacienteNome, t.nome, p.nascimento, c.celular, tp.tipo 
                          FROM tbl_paciente_terapeuta pt 
                          JOIN tbl_paciente p ON p.id = pt.id_paciente 
                          JOIN tbl_user_terapeuta t ON pt.id_terapeuta = t.id 
                          JOIN tbl_contato c ON c.id = p.id_contato 
                          INNER JOIN tbl_tipo_paciente tp ON p.id_tipo_paciente = tp.id 
                          WHERE pt.id_status_consulta = 1 AND pt.id_terapeuta = $id AND p.ativo = 1
                          $where
                          LIMIT $por_pagina  OFFSET $offset";

                  $sql_total = "SELECT COUNT(*) as total
                                FROM tbl_paciente_terapeuta pt
                                JOIN tbl_paciente p ON p.id = pt.id_paciente
                                WHERE pt.id_status_consulta = 1 AND pt.id_terapeuta = $id AND p.ativo = 1 $where";
                  $result_total = mysqli_query($mysqli, $sql_total);
                  $total = mysqli_fetch_assoc($result_total)['total'];
                  $result = mysqli_query($mysqli, $sql);

                  while ($row = mysqli_fetch_assoc($result)) {
                  ?>
                    <tr>
                      <td><?php echo $row["pacienteNome"] ?></td>
                      <td><?php echo date('d/m/Y', strtotime($row["nascimento"])) ?></td>
                      <td><?php echo $row["celular"] ?></td>
                      <td><?php echo $row["tipo"] ?></td>
                      <td >
                        <a 
                        class="btnAtualizar"
                        href="#"
                        data-toggle="modal" 
                        data-target="#exampleModal" 
                        data-id="<?php echo $row['ptId'] ?>"
                        >
                        Finalizar</a>
                      </td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>

              
              <?php
                $total_paginas = ceil($total / $por_pagina);
              ?>
            </div>
            <nav>
              <ul class="pagination justify-content-center">

                <?php
                $range = 2;

                if ($pagina > 1) {
                ?>
                  <li class="page-item">
                    <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>">«</a>
                  </li>
                <?php
                }

                // PRIMEIRA PAGINA
                if ($pagina > ($range + 1)) {
                ?>
                  <li class="page-item">
                    <a class="page-link" href="?pagina=1">1</a>
                  </li>

                  <li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>
                <?php
                }

                // PÁGINAS AO REDOR DA ATUAL
                for ($i = max(1, $pagina - $range); $i <= min($total_paginas, $pagina + $range); $i++) {
                ?>
                  <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $i; ?>">
                      <?php echo $i; ?>
                    </a>
                  </li>
                <?php
                }

                // ULTIMA PAGINA
                if ($pagina < ($total_paginas - $range)) {
                ?>
                  <li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>

                  <li class="page-item">
                    <a class="page-link" href="?pagina=<?php echo $total_paginas; ?>">
                      <?php echo $total_paginas; ?>
                    </a>
                  </li>
                <?php
                }

                // PROXIMO
                if ($pagina < $total_paginas) {
                ?>
                  <li class="page-item">
                    <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>">»</a>
                  </li>
                <?php
                }
                ?>

              </ul>
            </nav>

            </div>


          </div><!-- /.box-body -->
          <div class="box-footer">

          </div><!-- /.box-footer-->
        </div><!-- /.box -->

      </section><!-- /.content -->

      </section><!-- /.content -->
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Consulta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <h3 style="margin-left: 3%">Finalizar Consulta?</h3>
              <form method="post">
                <div class="modal-body">
                  <input type="hidden" id="id_consulta" name="id_consulta">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                  <button type="submit" name="finalizar-consulta" class="btn btn-primary">Finalizar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div><!-- /.content-wrapper -->

    <footer class="main-footer">
      <strong>CLÍNICA DE PSICOLOGIA <br> Equipe de desenvolvimento da Estácio de Sá | Laboratório de Transformação Digital.</strong>
    </footer>
  </div>

  <!-- jQuery 2.1.4 -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../admin/plugins/jQuery/jQuery-2.1.4.min.js"></script>
  <!-- Bootstrap 3.3.5 -->
  <script src="../admin/bootstrap/js/bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="../admin/plugins/slimScroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="../admin/plugins/fastclick/fastclick.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../admin/dist/js/app.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../admin/dist/js/demo.js"></script>
    <script>
    $(".btnAtualizar").click(function() {
        var id = $(this).attr('data-id');
        $("#id_consulta").val(id);
    })
  </script>

</body>

</html>
