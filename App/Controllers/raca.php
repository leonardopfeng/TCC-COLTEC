<?php


namespace App\Controllers;

use App\Controller;
use App\Conexao;
use App\Bootgrid;
use App\ControllerSeguro;

class Raca Extends Controller
{
    public function index()
    {
        echo $this->template->twig->render('raca/listagem.html.twig');
    }

    public function formCadastrar()
    {
        echo $this->template->twig->render('raca/cadastrar.html.twig');
    }

    public function formEditar($id)
    {
        $db = Conexao::connect();

        $sql = "SELECT * FROM raca WHERE id=:id";

        $query = $db->prepare($sql);
        $query->bindParam(":id", $id);
        $resultado = $query->execute();

        $linha = $query->fetch();

        echo $this->template->twig->render('raca/editar.html.twig', compact('linha'));
    }



    public function salvarCadastrar()
    {
        $db = Conexao::connect();

        $sql = "INSERT INTO raca (nome) VALUES (:nome)";

        $query = $db->prepare($sql);
        $query->bindParam(":nome", $_POST['nome']);
        $query->execute();

        if ($query->rowCount()==1) {
            $this->retornaOK('Raça cadastrada com sucesso');
        }else{
            $this->retornaErro('Erro ao inserir os dados');
        }
    }

    public function salvarEditar()
    {
        $db = Conexao::connect();

        $sql = "UPDATE raca SET nome=:nome WHERE id=:id";

        $query = $db->prepare($sql);
        $query->bindParam(":nome", $_POST['nome']);
        $query->bindParam(":id", $_POST['id']);
        $query->execute();

        if ($query->rowCount()==1) {
            $this->retornaOK('Raça alterada com sucesso');
        }else{
            $this->retornaOK('Nenhum dado alterado');
        }
    }

    public function excluir(){
        $db = Conexao::connect();

        $sql = "DELETE FROM raca WHERE id=:id";

        $query = $db->prepare($sql);
        $query->bindParam(":id", $_POST['id']);
        $query->execute();

        if ($query->rowCount()==1) {
            $this->retornaOK('Excluido com sucesso');
        }else{
            $this->retornaErro('Erro ao excluir os dados');
        }
    }


    public function bootgrid()
    {
        $busca = addslashes($_POST['searchPhrase']);
        $sql = "SELECT `id`, `nome` FROM raca WHERE 1 ";

        if ($busca!=''){
            $sql .= " and (
                        nome LIKE '%{$busca}%'
                        ) ";
        }

        $bootgrid = new Bootgrid($sql);
        echo $bootgrid->show();
    }

}