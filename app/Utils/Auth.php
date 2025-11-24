<?php
namespace App\Utils;

class Auth
{
    public static function verificarAutenticacao()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    public static function redirecionarSeAutenticado()
    {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: index.php');
            exit;
        }
    }
}