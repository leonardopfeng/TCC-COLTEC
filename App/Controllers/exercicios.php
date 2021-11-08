<?php


namespace App\Controllers;

use App\Controller;
use App\Conexao;
use App\Bootgrid;
use App\ControllerSeguro;

class exercicios Extends ControllerSeguro
{

   protected $nivel = ['admin'];

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

        $sql = "SELECT * FROM exercicios WHERE id_exercicio=:id_exercicio";

        $query = $db->prepare($sql);
        $query->bindParam(":id_exercicio", $id);
        $query->execute();
        $linha = $query->fetch();

        $sqlCategoria = "SELECT grupo_muscular.id, grupo_muscular.nome FROM grupo_muscular";
        $queryCategoria = $db->prepare($sqlCategoria);
        $queryCategoria->execute();
        $linhaCategoria = $queryCategoria->fetchAll();


        echo $this->template->twig->render('exercicios/editar.html.twig', compact('linha','linhaCategoria'));
    }

    public function video($id)
    {
        $db = Conexao::connect();

        $sql = "SELECT * FROM exercicios WHERE id_exercicio=:id_exercicio";

        $query = $db->prepare($sql);
        $query->bindParam(":id_exercicio", $id);
        $query->execute();
        $linha = $query->fetch();

        $sqlCategoria = "SELECT grupo_muscular.id, grupo_muscular.nome FROM grupo_muscular";
        $queryCategoria = $db->prepare($sqlCategoria);
        $queryCategoria->execute();
        $linhaCategoria = $queryCategoria->fetchAll();


        echo $this->template->twig->render('exercicios/editar.html.twig', compact('linha','linhaCategoria'));
    }



    public function salvarCadastrar()
    {

        $db = Conexao::connect();

        $video = $this->idVideo($_POST['url_video']);
        if (!$video){
            $this->retornaErro('a');
        }

        // ve se já nao tem exercicio cadastrado com esse nome
        $sqlNome = "SELECT * FROM exercicios WHERE nome_exercicio=:nome_exercicio";
        $queryNome = $db->prepare($sqlNome);
        $queryNome->bindParam(":nome_exercicio", $_POST['nome_exercicio']);
        $queryNome->execute();
        if($queryNome->rowCount()==1){
            $this->retornaErro('Erro ao cadatrar, exercício ja cadastrado');
        }

        // ve se já nao tem exercicio cadastrado com essa URL
        $sqlNome = "SELECT * FROM exercicios WHERE url_video=:url_video";
        $queryNome = $db->prepare($sqlNome);
        $queryNome->bindParam(":url_video", $video);
        $queryNome->execute();
        if($queryNome->rowCount()==1){
            $this->retornaErro('Erro ao cadatrar, exercício ja cadastrado com esta URL');
        }

        $sql = "INSERT INTO exercicios (nome_exercicio, grupo_muscular, url_video) VALUES(:nome_exercicio, :grupo_muscular, :url_video)";
        $query = $db->prepare($sql);
        $query->bindParam(":nome_exercicio", $_POST['nome_exercicio']);
        $query->bindParam(":grupo_muscular", $_POST['grupo_muscular']);
        $query->bindParam(":url_video", $video);
        $query->execute();

        if ($query->rowCount()==1) {
            $this->retornaOK('O exercício foi cadastrado com sucesso');
        }
    }

    public function salvarEditar()
    {

        $db = Conexao::connect();

        // ve se já nao tem exercicio cadastrado com esse nome
        $sqlNome = "SELECT * FROM exercicios WHERE nome_exercicio=:nome_exercicio AND id_exercicio!=:id_exercicio";
        $queryNome = $db->prepare($sqlNome);
        $queryNome->bindParam(":nome_exercicio", $_POST['nome_exercicio']);
        $queryNome->bindParam(":id_exercicio", $_POST['id_exercicio']);
        $queryNome->execute();
        if($queryNome->rowCount()==1){
            $this->retornaErro('Erro ao editar, exercício ja cadastrado');
        }

        $video = $this->idVideo($_POST['url_video']);
        if (!$video){
            $this->retornaErro('URL inválida');
        }

        // ve se já nao tem exercicio cadastrado com essa URL
        $sqlNome = "SELECT * FROM exercicios WHERE url_video=:url_video AND id_exercicio!=:id_exercicio";
        $queryNome = $db->prepare($sqlNome);
        $queryNome->bindParam(":url_video", $_POST['url_video']);
        $queryNome->bindParam(":id_exercicio", $_POST['id_exercicio']);
        $queryNome->execute();
        if($queryNome->rowCount()==1){
            $this->retornaErro('Erro ao editar, exercício ja cadastrado com esta URL');
        }


        $sql = "UPDATE exercicios SET nome_exercicio=:nome_exercicio, grupo_muscular=:grupo_muscular, url_video=:video WHERE id_exercicio=:id_exercicio";

        $query = $db->prepare($sql);
        $query->bindParam(":nome_exercicio", $_POST['nome_exercicio']);
        $query->bindParam(":grupo_muscular", $_POST['grupo_muscular']);
        $query->bindParam(":video", $video);
        $query->bindParam(":id_exercicio", $_POST['id_exercicio']);
        $query->execute();

        if ($query->rowCount()==1) {
            $this->retornaOK('O exercício foi alterado com sucesso');
        }else{
            $this->retornaOK('Nenhum dado alterado');
        }
    }

    public function excluir(){


        $db = Conexao::connect();

        $sql = "DELETE FROM exercicios WHERE id_exercicio=:id_exercicio";

        $query = $db->prepare($sql);
        $query->bindParam(":id_exercicio", $_POST['id']);
        $query->execute();

        if ($query->rowCount()==1) {
            $this->retornaOK('Excluido com sucesso');
        }else{
            $this->retornaErro('Erro ao excluir');
        }


    }


    public function bootgrid()
    {
        $busca = addslashes($_POST['searchPhrase']);
        $sql = "SELECT exercicios.url_video, exercicios.id_exercicio, exercicios.nome_exercicio, grupo_muscular.nome FROM exercicios INNER JOIN grupo_muscular ON exercicios.grupo_muscular=grupo_muscular.id";



        if ($busca!=''){
            $sql .= " and (
                       id_exercicio LIKE '%{$busca}%' OR
                       nome_exercicio LIKE '%{$busca}%' OR
                       nome LIKE '%{$busca}%'
                        ) ";
        }


        $bootgrid = new Bootgrid($sql);
        echo $bootgrid->show();
    }

    public function idVideo($url)
    {
        if (strlen($url)<15) return $url;
        parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
        return (isset($my_array_of_vars['v']) && $my_array_of_vars['v']!='' ? $my_array_of_vars['v'] : false);
    }

}