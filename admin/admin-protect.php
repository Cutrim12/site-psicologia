<?php
// Use este arquivo nas ações exclusivamente administrativas.  O protect.php
// legado confirma apenas que existe uma sessão; isso não é suficiente quando
// diferentes tipos de usuário podem ter o mesmo id.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (($_SESSION['papel'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acesso restrito ao administrador.');
}
?>
