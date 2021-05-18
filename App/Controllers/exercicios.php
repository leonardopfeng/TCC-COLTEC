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

        $sql = "SELECT categoriaexercicio.id, categoriaexercicio.nome FROM categoriaexercicio";

        $query = $db->prepare($sql);


        $query->execute();

        $linhacategoria = $query->fetchAll();


        echo $this->template->twig->render('exercicios/cadastrar.html.twig', compact('linhacategoria'));
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

        $sql = "INSERT INTO exercicios (nome, categoria) VALUES(:nome, :categoria)";
        $query = $db->prepare($sql);
        $query->bindParam(":nome", $_POST['nome']);
        $query->bindParam(":categoria", $_POST['categoria']);
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
        $sql = "SELECT exercicios.id, exercicios.nome, categoriaexercicio.nome as nomecategoria FROM exercicios INNER JOIN categoriaexercicio ON exercicios.categoria=categoriaexercicio.id";



        if ($busca!=''){
            $sql .= " and (
                        id LIKE '%{$busca}%' OR
                        nome LIKE '%{$busca}%' OR
                        nomecategoria LIKE '%{$busca}%' 
                        ) ";
        }


        $bootgrid = new Bootgrid($sql);
        echo $bootgrid->show();
    }

}