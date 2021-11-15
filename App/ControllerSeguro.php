<?php

namespace App;

class ControllerSeguro extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] != true) {
            header("Location: /login");
        }

        $this->template->twig->addGlobal('session', $_SESSION);

        $this->checarPermissao();
    }

    public function checarPermissao()
    {
        if (!isset($this->nivel)) return;

        if (! in_array($_SESSION['tipo'], $this->nivel)){
            $this->errorPermission();
        }
    }

}
