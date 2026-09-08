<?php $tipo_paciente_titulo = $tipo_paciente_titulo ?? 'Paciente'; ?>
<div class="wrapper">
  <header class="main-header">
    <a href="../../../index.php" class="logo">
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
              <img src="../../../dist/img/user.jpg" class="user-image" alt="Usuário">
              <span class="hidden-xs"><?php echo $_SESSION['nome']; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="../../../dist/img/user.jpg" class="img-circle" alt="Usuário">
                <p><?php echo $_SESSION['nome']; ?></p>
              </li>
              <li class="user-footer">
                <div class="pull-right"><a href="../../../logout.php" class="btn btn-danger">Sair</a></div>
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
        <div class="pull-left image"><img src="../../../dist/img/user.jpg" class="img-circle" alt="Usuário"></div>
        <div class="pull-left info">
          <p><?php echo $_SESSION['nome']; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Pesquisar...">
          <span class="input-group-btn"><button type="submit" class="btn btn-flat"><i class="fa fa-search"></i></button></span>
        </div>
      </form>
      <ul class="sidebar-menu">
        <li class="header">CLÍNICA MENU</li>
        <li class="treeview"><a href="../../../index.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li class="treeview active">
          <a href="#"><i class="fa fa-gears"></i> <span>Cadastrar paciente</span><i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu menu-open">
            <li><a href="cadastrar-paciente-adulto.php"><i class="fa fa-plus-square"></i> Adulto</a></li>
            <li><a href="cadastrar-paciente-crianca.php"><i class="fa fa-plus-square"></i> Criança</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-gears"></i> <span>Gerenciar</span><i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">
            <li><a href="../pacientes.php"><i class="fa fa-plus-square"></i> Pacientes</a></li>
            <li><a href="../professores.php"><i class="fa fa-plus-square"></i> Professores</a></li>
            <li><a href="../terapeutas.php"><i class="fa fa-plus-square"></i> Estagiário</a></li>
            <li><a href="../monitor.php"><i class="fa fa-plus-square"></i> Monitores</a></li>
            <li><a href="../../reserva_sala/reservar-sala-segunda.php"><i class="fa fa-plus-square"></i> Reservar Sala</a></li>
          </ul>
        </li>
        <li><a href="../../../logout.php"><i class="fa fa-sign-out"></i> Sair</a></li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Clínica de Psicologia</h1>
      <ol class="breadcrumb">
        <li><a href="../../../index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Cadastrar paciente</a></li>
        <li class="active"><?php echo $tipo_paciente_titulo; ?></li>
      </ol>
    </section>
    <section class="content">
      <div class="callout callout-info">
        <h4>AVISO!</h4>
        <p>Nossa versão ainda encontra-se em fases de testes, se você achar algum bug por favor contate o administrador!</p>
      </div>
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Cadastro de paciente - <?php echo $tipo_paciente_titulo; ?></h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Recolher"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Preencha os dados do paciente</h3>
            </div>
            <form method="post" class="form-horizontal">
              <div class="box-body">
