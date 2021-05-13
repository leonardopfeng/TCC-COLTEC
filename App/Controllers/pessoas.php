<?php


namespace App\Controllers;

use App\Controller;
use App\Conexao;
use App\Bootgrid;
use App\ControllerSeguro;

class Pessoas Extends Controller
{
    public function index()
    {
        echo $this->template->twig->render('pessoas/listagem.html.twig');
    }

    public function formCadastrar()
    {
        $db = Conexao::connect();

        $sql = "SELECT personal.pessoa, pessoas.id, pessoas.nome FROM personal INNER JOIN pessoas ON personal.pessoa=pessoas.id";

        $query = $db->prepare($sql);

        $query->execute();

        $linhapersonal = $query->fetchAll();

        echo $this->template->twig->render('pessoas/cadastrar.html.twig', compact('linhapersonal'));
    }

    public function formEditar($id)
    {
        $db = Conexao::connect();

        $sql = "SELECT * FROM pessoas WHERE id=:id";

        $query = $db->prepare($sql);
        $query->bindParam(":id", $id);

        $resultado = $query->execute();

        $linha = $query->fetch();

        echo $this->template->twig->render('pessoas/editar.html.twig', compact('linha'));
    }

    public function buscaPessoa()
    {
    }


    public function salvarCadastrar()
    {

        if($_POST['senha']!=$_POST['confsenha']){
            $this->retornaErro('As senhas não estão iguais');
        }

        $criptografaSenha = $this->criptografa($_POST['senha']);


        $db = Conexao::connect();

        $sql = "INSERT INTO pessoas (nome, telefone, tipo, usuario, senha) VALUES (:nome, :telefone, :tipo, :usuario, :senha)";
        $query = $db->prepare($sql);
        $query->bindParam(":nome", $_POST['nome']);
        $query->bindParam(":telefone", $_POST['telefone']);
        $query->bindParam(":tipo", $_POST['tipo']);
        $query->bindParam(":usuario", $_POST['usuario']);
        $query->bindParam(":senha", $criptografaSenha);
        $query->execute();


        switch ($_POST['tipo']){

            case 'personal':

                $sqlpessoa = "INSERT INTO personal(pessoa, cref, data_cadastro) VALUES(:pessoa, :cref, NOW())";
                $querypessoa = $db->prepare($sqlpessoa);
                $querypessoa->bindValue(':pessoa', $db->lastInsertId());
                $querypessoa->bindValue(':cref', $_POST['cref']);
                $querypessoa->execute();

                break;

            case 'cliente':

                $sqlpessoa = "INSERT INTO clientes(pessoa, personal) VALUES(:pessoa, :personal)";
                $querypessoa = $db->prepare($sqlpessoa);
                $querypessoa->bindValue(':pessoa', $db->lastInsertId());
                $querypessoa->bindValue(':personal', $_POST['personal']);
                $querypessoa->execute();

                break;

        }
        
        if ($query->rowCount()==1) {
            $this->retornaOK('A pessoa foi cadastrada com sucesso');
        }else{
            $this->retornaErro('Erro ao inserir os dados');
        }
    }

    public function salvarEditar()
    {

        if($_POST['senha']!=$_POST['confsenha']){
            $this->retornaErro('Senha inserida não está igual');
        }

        $criptografaSenha = sha1($_POST['senha']);

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

        $sql = "DELETE FROM pessoas WHERE id=:id";

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
        $sql = "SELECT `id`, `nome`, `telefone`, `usuario`, `tipo`  FROM pessoas WHERE 1 ";

        if ($busca!=''){
            $sql .= " and (
                        id LIKE '%{$busca}%' OR
                        nome LIKE '%{$busca}%' OR
                        telefone LIKE '%{$busca}%' OR
                        usuario LIKE '%{$busca}%' OR
                        tipo LIKE '%{$busca}%' OR
                      
                        ) ";
        }

        $bootgrid = new Bootgrid($sql);
        echo $bootgrid->show();
    }

}