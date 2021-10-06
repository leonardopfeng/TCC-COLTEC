<?php


namespace App\Controllers;

use App\Controller;
use App\Conexao;
use App\Bootgrid;
use App\ControllerSeguro;

class exercicios Extends Controller
{
    public function index()
    {
        echo $this->template->twig->render('exercicios/listagem.html.twig');
    }

    public function formCadastrar()
    {
        $db = Conexao::connect();

        $sql = "SELECT grupo_muscular.id, grupo_muscular.nome FROM grupo_muscular";

        $query = $db->prepare($sql);


        $query->execute();

        $linhagrupomuscular = $query->fetchAll();


        echo $this->template->twig->render('exercicios/cadastrar.html.twig', compact('linhagrupomuscular'));
    }

    public function formEditar($id)
    {
        $db = Conexao::connect();

        $sql = "SELECT * FROM exercicios WHERE id=:id";

        $query = $db->prepare($sql);
        $query->bindParam(":id", $id);

        $resultado = $query->execute();

        $linha = $query->fetch();

        echo $this->template->twig->render('exercicios/editar.html.twig', compact('linha'));
    }



    public function salvarCadastrar()
    {

        $db = Conexao::connect();

        $sql = "INSERT INTO exercicios (nome_exercicio, grupo_muscular, url_video) VALUES(:nome_exercicio, :grupo_muscular, :url_video)";
        $query = $db->prepare($sql);
        $query->bindParam(":nome_exercicio", $_POST['nome_exercicio']);
        $query->bindParam(":grupo_muscular", $_POST['grupo_muscular']);
        $query->bindParam(":url_video", $_POST['url_video']);
        $query->execute();

        if ($query->rowCount()==1) {
            $this->retornaOK('O exercício foi cadastrado com sucesso');
        }
    }

    public function salvarEditar()
    {

        $db = Conexao::connect();

        $sql = "UPDATE pessoas SET nome=:nome, usuario=:usuario, tipo=:tipo, senha=:senha WHERE id=:id";

        $query = $db->prepare($sql);
        $query->bindParam(":nome", $_POST['nome']);
        $query->bindParam(":usuario", $_POST['usuario']);
        $query->bindParam(":tipo", $_POST['tipo']);
        $query->bindParam(":senha", $criptografaSenha);
        $query->bindParam(":id", $_POST['id']);
        $query->execute();

        if ($query->rowCount()==1) {
            $this->retornaOK('A pessoa foi alterada com sucesso');
        }else{
            $this->retornaOK('Nenhum dado alterado');
        }
    }

    public function excluir(){

        $db = Conexao::connect();

        $sql = "DELETE FROM exercicios WHERE id=:id";

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
        $sql = "SELECT exercicios.url_video, exercicios.id_exercicio, exercicios.nome_exercicio, grupo_muscular.nome FROM exercicios INNER JOIN grupo_muscular ON exercicios.grupo_muscular=grupo_muscular.id";



        if ($busca!=''){
            $sql .= " and (
                        
                       url_video LIKE '%{$busca}%' OR
                       id_exercicio LIKE '%{$busca}%' OR
                       nome_exercicio LIKE '%{$busca}%' OR
                       nome LIKE '%{$busca}%'
                        ) ";
        }


        $bootgrid = new Bootgrid($sql);
        echo $bootgrid->show();
    }

}