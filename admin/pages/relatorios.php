<?php
include('../protect.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CLÍNICA | RELATÓRIOS</title>
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
  <link rel="shortcut icon" href="../../img/favicon.png" type="image/x-icon">
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
  <div class="wrapper">
    <header class="main-header">
      <a href="../index.php" class="logo">
        <span class="logo-mini"><b>CL</b></span>
        <span class="logo-lg"><b>CLÍ</b>NICA</span>
      </a>
      <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Alternar menu</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="../dist/img/user.jpg" class="user-image" alt="Usuário">
                <span class="hidden-xs"><?php echo htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header">
                  <img src="../dist/img/user.jpg" class="img-circle" alt="Usuário">
                  <p><?php echo htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8'); ?></p>
                </li>
                <li class="user-footer">
                  <div class="pull-right"><a href="../logout.php" class="btn btn-danger">Sair</a></div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>

    <aside class="main-sidebar">
      <section class="sidebar">
        <div class="user-panel">
          <div class="pull-left image"><img src="../dist/img/user.jpg" class="img-circle" alt="Usuário"></div>
          <div class="pull-left info">
            <p><?php echo htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8'); ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>
        <ul class="sidebar-menu">
          <li class="header">CLÍNICA MENU</li>
          <li><a href="../index.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
          <li class="treeview">
            <a href="#"><i class="fa fa-user-plus"></i> <span>Cadastrar paciente</span><i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li><a href="layout/cadastro/cadastrar-paciente-adulto.php"><i class="fa fa-male"></i> Adulto</a></li>
              <li><a href="layout/cadastro/cadastrar-paciente-crianca.php"><i class="fa fa-child"></i> Criança</a></li>
            </ul>
          </li>
          <li class="treeview active">
            <a href="#"><i class="fa fa-cogs"></i> <span>Gerenciar</span><i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li><a href="layout/pacientes.php"><i class="fa fa-users"></i> Pacientes</a></li>
              <li><a href="layout/professores.php"><i class="fa fa-users"></i> Professores</a></li>
              <li><a href="layout/terapeutas.php"><i class="fa fa-users"></i> Estagiários</a></li>
              <li><a href="layout/monitor.php"><i class="fa fa-users"></i> Monitores</a></li>
              <li><a href="reserva_sala/reservar-sala.php"><i class="fa fa-calendar"></i> Reservar Sala</a></li>
              <li class="active"><a href="relatorios.php"><i class="fa fa-bar-chart"></i> Relatórios</a></li>
            </ul>
          </li>
          <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Sair</a></li>
        </ul>
      </section>
    </aside>

    <div class="content-wrapper">
      <section class="content-header">
        <h1>Clínica de Psicologia</h1>
        <ol class="breadcrumb">
          <li><a href="../index.php"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="#">Gerenciar</a></li>
          <li class="active">Relatórios</li>
        </ol>
      </section>
      <section class="content">
        <div class="callout callout-info">
          <h4>AVISO!</h4>
          <p>Nossa versão ainda encontra-se em fases de testes, se você achar algum bug por favor contate o administrador!</p>
        </div>
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bar-chart"></i> Relatórios</h3>
            <div class="box-tools pull-right">
              <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Recolher"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="box-body">
            <p>Área de relatórios da clínica.</p>
          </div>
        </div>
      </section>
    </div>

    <footer class="main-footer">
      <strong>CLÍNICA DE PSICOLOGIA <br> Equipe de desenvolvimento da Estácio de Sá | Laboratório de Transformação Digital.</strong>
    </footer>
  </div>
  <script src="../plugins/jQuery/jQuery-2.1.4.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
  <script src="../plugins/fastclick/fastclick.min.js"></script>
  <script src="../dist/js/app.min.js"></script>
  <script src="../dist/js/demo.js"></script>
</body>
</html>
