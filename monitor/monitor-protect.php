<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (($_SESSION['papel'] ?? '') !== 'monitor') {
    http_response_code(403);
    exit('Acesso restrito ao monitor autenticado.');
}
?>
