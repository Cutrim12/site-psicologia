<?php
include('../../../db/conexao.php');
include('../../../admin/admin-protect.php');

$grade_salas = [];

$sql = "SELECT s.id, s.id_horario, s.sala_cod, s.sala, s.id_semana, h.horario, st.status
        FROM tbl_sala_reservada s
        INNER JOIN tbl_horario_sala h ON h.id = s.id_horario
        INNER JOIN tbl_status_sala st ON st.id = s.id_status
        ORDER BY s.id_semana, h.id, s.sala_cod";
$resultado = mysqli_query($mysqli, $sql);

while ($reserva = mysqli_fetch_assoc($resultado)) {
      preg_match('/_s([1-7])$/', $reserva['sala_cod'], $sala_match);
      $numero_sala = isset($sala_match[1]) ? (int) $sala_match[1] : 0;
      if ($numero_sala > 0) {
        $grade_salas[(int) $reserva['id_semana']][(int) $reserva['id_horario']][$numero_sala] = [
          'id' => (int) $reserva['id'],
          'sala' => $reserva['sala'],
          'status' => $reserva['status']
        ];
      }
}

    $horarios = [];
    $resultado_horarios = mysqli_query($mysqli, "SELECT id, horario FROM tbl_horario_sala ORDER BY id");
    while ($horario = mysqli_fetch_assoc($resultado_horarios)) {
      $horarios[] = $horario;
    }
    $dias_semana = [1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CLÍNICA | RESERVA DE SALA</title>
  <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
  <link rel="shortcut icon" href="../../../img/favicon.png" type="image/x-icon">
  <style>
    #room-grid { overflow-x: auto; }
    .room-day { margin-bottom: 24px; }
    .room-day-title { margin: 0; padding: 10px 12px; background: #3c8dbc; color: #fff; font-size: 16px; }
    .room-grid-table { min-width: 820px; margin-bottom: 0; table-layout: fixed; }
    .room-grid-table th, .room-grid-table td { vertical-align: middle; text-align: center; height: 54px; }
    .room-grid-table th:first-child, .room-grid-table td:first-child { width: 100px; }
    .room-cell { display: block; min-height: 34px; padding: 8px 3px; color: #fff; text-decoration: none; font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .room-cell:hover { color: #fff; opacity: .85; }
    .room-cell.status-livre { background: #00a65a; }
    .room-cell.status-reservada { background: #f39c12; }
    .room-cell.status-encaixe { background: #dd4b39; }
    .room-cell.status-triagem { background: #00c0ef; }
    .room-cell.status-grupo { background: #605ca8; }
    .room-cell.status-infantil { background: #e83e8c; }
    .room-cell.status-manutencao { background: #b02a37; }
    .reserva-modal-body { padding: 0; height: 500px; }
    #reserva-frame { display: block; width: 100%; height: 100%; border: 0; }
    .fc { font-family: inherit; }
    .fc-event { border: 0; cursor: pointer; }
    .status-reservada { background: #f39c12; }
    .status-livre, .status-disponivel { background: #00a65a; }
    .status-encaixe { background: #dd4b39; }
    .status-triagem { background: #00c0ef; }
    .status-grupo { background: #605ca8; }
    .status-infantil { background: #e83e8c; }
    .status-manutencao { background: #b02a37; }
    @media (max-width: 767px) { #calendar { min-height: 520px; } }
  </style>
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
<div class="wrapper">
  <header class="main-header">
    <a href="../../index.php" class="logo"><span class="logo-mini"><b>CL</b></span><span class="logo-lg"><b>CLÍ</b>NICA</span></a>
    <nav class="navbar navbar-static-top" role="navigation">
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"><span class="sr-only">Alternar menu</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></a>
      <div class="navbar-custom-menu"><ul class="nav navbar-nav"><li class="dropdown user user-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="../../dist/img/user.jpg" class="user-image" alt="Usuário"><span class="hidden-xs"><?php echo htmlspecialchars($_SESSION['nome']); ?></span></a>
        <ul class="dropdown-menu"><li class="user-header"><img src="../../dist/img/user.jpg" class="img-circle" alt="Usuário"><p><?php echo htmlspecialchars($_SESSION['nome']); ?></p></li><li class="user-footer"><div class="pull-right"><a href="../../logout.php" class="btn btn-danger">Sair</a></div></li></ul>
      </li></ul></div>
    </nav>
  </header>
  <aside class="main-sidebar"><section class="sidebar">
    <div class="user-panel"><div class="pull-left image"><img src="../../dist/img/user.jpg" class="img-circle" alt="Usuário"></div><div class="pull-left info"><p><?php echo htmlspecialchars($_SESSION['nome']); ?></p><a href="#"><i class="fa fa-circle text-success"></i> Online</a></div></div>
    <ul class="sidebar-menu">
      <li class="header">CLÍNICA MENU</li>
      <li><a href="../../index.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
      <li class="treeview"><a href="#"><i class="fa fa-user-plus"></i> <span>Cadastrar paciente</span><i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
          <li><a href="../layout/cadastro/cadastrar-paciente-adulto.php"><i class="fa fa-male"></i> Adulto</a></li>
          <li><a href="../layout/cadastro/cadastrar-paciente-crianca.php"><i class="fa fa-child"></i> Criança</a></li>
        </ul>
      </li>
      <li class="treeview active"><a href="#"><i class="fa fa-cogs"></i> <span>Gerenciar</span><i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
          <li><a href="../layout/pacientes.php"><i class="fa fa-users"></i> Pacientes</a></li>
          <li><a href="../layout/professores.php"><i class="fa fa-users"></i> Professores</a></li>
          <li><a href="../layout/terapeutas.php"><i class="fa fa-users"></i> Estagiários</a></li>
          <li><a href="../layout/monitor.php"><i class="fa fa-users"></i> Monitores</a></li>
          <li class="active"><a href="reservar-sala.php"><i class="fa fa-calendar"></i> Reservar Sala</a></li>
          <li><a href="../relatorios.php"><i class="fa fa-bar-chart"></i> Relatórios</a></li>
        </ul>
      </li>
      <li><a href="../../logout.php"><i class="fa fa-sign-out"></i> Sair</a></li>
    </ul>
  </section></aside>
  <div class="content-wrapper">
    <section class="content-header"><h1>Reserva de sala</h1><ol class="breadcrumb"><li><a href="../../index.php"><i class="fa fa-dashboard"></i> Home</a></li><li class="active">Reservar sala</li></ol></section>
    <section class="content"><div class="box box-primary"><div class="box-header with-border"><h3 class="box-title"><i class="fa fa-calendar"></i> Agenda de salas</h3></div><div class="box-body">
      <div id="room-grid">
        <?php foreach ($dias_semana as $id_dia => $nome_dia): ?>
          <div class="room-day">
            <h4 class="room-day-title"><?php echo $nome_dia; ?></h4>
            <table class="table table-bordered room-grid-table">
              <thead><tr><th>Horário</th><?php for ($numero_sala = 1; $numero_sala <= 7; $numero_sala++): ?><th>Sala <?php echo $numero_sala; ?></th><?php endfor; ?></tr></thead>
              <tbody>
                <?php foreach ($horarios as $horario): ?>
                  <tr><th><?php echo htmlspecialchars($horario['horario'], ENT_QUOTES, 'UTF-8'); ?></th>
                  <?php for ($numero_sala = 1; $numero_sala <= 7; $numero_sala++): $reserva = $grade_salas[$id_dia][(int) $horario['id']][$numero_sala] ?? null; ?>
                    <td><?php if ($reserva): ?><a class="room-cell status-<?php echo preg_replace('/[^a-z0-9_-]/i', '-', strtolower($reserva['status'])); ?>" href="mudar-cor.php?id=<?php echo $reserva['id']; ?>&compact=1" data-reserva-id="<?php echo $reserva['id']; ?>" title="<?php echo htmlspecialchars($reserva['sala'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($reserva['sala'] ?: 'Disponível', ENT_QUOTES, 'UTF-8'); ?></a><?php endif; ?></td>
                  <?php endfor; ?></tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endforeach; ?>
      </div>
    </div></div></section>
  </div>
  <div class="modal fade" id="reserva-modal" tabindex="-1" role="dialog" aria-labelledby="reserva-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="reserva-modal-title"><i class="fa fa-calendar-check-o"></i> Operações da sala</h4>
        </div>
        <div class="modal-body reserva-modal-body"><iframe id="reserva-frame" title="Operações da reserva" src="about:blank"></iframe></div>
      </div>
    </div>
  </div>
  <footer class="main-footer"><strong>CLÍNICA DE PSICOLOGIA <br> Equipe de desenvolvimento da Estácio de Sá | Laboratório de Transformação Digital.</strong></footer>
</div>
<script src="../../plugins/jQuery/jQuery-2.1.4.min.js"></script>
<script src="../../bootstrap/js/bootstrap.min.js"></script>
<script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="../../dist/js/app.min.js"></script>
<script>
  $(function () {
    $(document).on('click', '.room-cell', function (event) {
      event.preventDefault();
      abrirModalReserva(this.href);
    });
    $('#reserva-modal').on('hidden.bs.modal', function () {
      $('#reserva-frame').attr('src', 'about:blank');
    });
    window.addEventListener('message', function (event) {
      if (event.origin !== window.location.origin || !event.data) return;
      if (event.data.type === 'fechar-reserva-modal') {
        $('#reserva-modal').modal('hide');
      } else if (event.data.type === 'reserva-atualizada') {
        $('#reserva-modal').modal('hide');
        window.location.reload();
      }
    });
    function abrirModalReserva(url) {
      $('#reserva-frame').attr('src', url);
      $('#reserva-modal').modal('show');
    }
  });
</script>
</body>
</html>
