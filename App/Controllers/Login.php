<?php

namespace App\Controllers;

use App\Controller;
use App\Conexao;
use App\Bootgrid;

class login Extends Controller
{
    public function index()
    {
        session_start();
        if(isset($_SESSION['logado']) && $_SESSION['logado']){

           if($_SESSION['tipo']=='cliente'){
               header("location: /testesortable/listaTreinos");
               exit;
           }
           elseif($_SESSION['tipo']=='personal'){
               header("location: /testesortable/formcadastrar");
               exit;
           }
           elseif($_SESSION['tipo']=='admin'){
               header("location: /grupo_muscular");
               exit;
           }
        }

        echo $this->template->twig->render('login/login.html.twig');
    }

    public function verificar()
    {
        session_start();

        $db = Conexao::connect();

        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];

        $senhacriptografada = $this->criptografa($senha);

        $sql = "SELECT * FROM pessoas WHERE usuario=:usuario AND senha=:senha AND status='ativo'";

        $resultados = $db ->prepare($sql);

        $resultados->bindParam(":usuario", $usuario);
        $resultados->bindParam(":senha", $senhacriptografada);
        $resultados->execute();

        if($resultados->rowCount()==1){
            $linha = $resultados->fetchObject();

            $_SESSION['logado'] = true;
            $_SESSION['id'] = $linha->id;
            $_SESSION['tipo'] = $linha->tipo;
            $this->retornaOK('Acesso autorizado.');
        }else{
            $_SESSION['logado'] = false;
            $this->retornaErro('Usuário ou senha inválidos');
        }
    }

    public function sair()
    {
        session_start();
        unset($_SESSION['logado']);
        session_destroy();

        header("Location: /login");
    }
}
