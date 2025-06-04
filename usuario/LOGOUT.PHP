<?php
// Inicia a sessão
session_start();

// Limpa todas as variáveis de sessão
session_unset();

// Destroi a sessão
session_destroy();

// (Opcional) Destruir o cookie de sessão, se necessário
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redireciona para a página de login ou homepage
header("Location: ../login/login.php"); // Altere para a página desejada
exit;
?>
