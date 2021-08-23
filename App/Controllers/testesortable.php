<?php


namespace App\Controllers;

use App\Controller;
use App\Conexao;
use App\Bootgrid;
use App\ControllerSeguro;

class testesortable Extends Controller
{
    public function index()
    {
        echo $this->template->twig->render('grupo_muscular/listagem.html.twig');
    }

    public function formCadastrar()
    {
        echo $this->template->twig->render('testesortable/cadastrar.html.twig');
    }

    public function formEditar($id)
    {
        $db = Conexao::connect();

        $sql = "SELECT * FROM grupo_muscular WHERE id=:id";

        $query = $db->prepare($sql);
        $query->bindParam(":id", $id);

        $resultado = $query->execute();

        $linha = $query->fetch();

        echo $this->template->twig->render('grupo_muscular/editar.html.twig', compact('linha'));
    }


    public function salvarCadastrar()
    {
        $db = Conexao::connect();

        $sql = "INSERT INTO grupo_muscular (nome) VALUES(:nome)";
        $query = $db->prepare($sql);
        $query->bindParam(":nome", $_POST['nome']);
        $query->execute();

        if ($query->rowCount()==1) {
            $this->retornaOK('O grupo muscular foi cadastrado com sucesso');
        }

    }


    public function salvarEditar()
    {
        try {
            $db = Conexao::connect();

            $sql = "UPDATE grupo_muscular SET nome=:nome WHERE id=:id";

            $query = $db->prepare($sql);
            $query->bindParam(":nome", $_POST['nome']);
            $query->bindParam(":id", $_POST['id']);
            $query->execute();

            if ($query->rowCount()==1) {
                $this->retornaOK('O grupo muscular foi alterado com sucesso');
            }else{
                $this->retornaOK('Nenhum dado alterado');
            }
        }catch(\Exception $e){
            $this->retornaErro('Erro: ' . $e->getMessage());
        }
    }

    public function excluir(){
        $db = Conexao::connect();

        $sql = "DELETE FROM grupo_muscular WHERE id=:id";

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
        $sql = "SELECT `id`, `nome` FROM grupo_muscular WHERE 1 ";

        if ($busca!=''){
            $sql .= " and (
                            id LIKE '%{$busca}%' OR
                            nome LIKE '%{$busca}%'
                            ) ";
        }

        $bootgrid = new Bootgrid($sql);
        echo $bootgrid->show();
    }

}