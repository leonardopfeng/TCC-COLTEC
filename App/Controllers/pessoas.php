<?php


namespace App\Controllers;

use App\Controller;
use App\Conexao;
use App\Bootgrid;
use App\ControllerSeguro;

class Pessoas Extends ControllerSeguro
{
    protected $nivel = [ 'admin' ];

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

        $query->execute();

        $linha = $query->fetch();

        echo $this->template->twig->render('pessoas/editar.html.twig', compact('linha'));
    }


    public function salvarCadastrar()
    {

        //ve se as senhas estao iguais
        if($_POST['senha']!=$_POST['confsenha']){
            $this->retornaErro('As senhas não estão iguais');
        }

        $criptografaSenha = $this->criptografa($_POST['senha']);


        $db = Conexao::connect();

        // ve se já nao tem usuario cadastrado com esse login
        $sqlUsuario = "SELECT * FROM pessoas WHERE usuario=:usuario";
        $queryUsuario = $db->prepare($sqlUsuario);
        $queryUsuario->bindParam(":usuario", $_POST['usuario']);
        $queryUsuario->execute();
        if($queryUsuario->rowCount()==1){
            $this->retornaErro('Erro ao cadatrar, nome de usuário em uso');
        }

        // ve se já nao tem usuario cadastrado com esse telefone
        $sqlTelefone = "SELECT * FROM pessoas WHERE telefone=:telefone";
        $queryTelefone = $db->prepare($sqlTelefone);
        $queryTelefone->bindParam(":telefone", $_POST['telefone']);
        $queryTelefone->execute();
        if($queryTelefone->rowCount()==1){
            $this->retornaErro('Erro ao cadastrar, telefone em uso');
        }



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

        // ve se já nao tem usuario cadastrado com esse login
        $sqlUsuario = "SELECT * FROM pessoas WHERE usuario=:usuario and id!=:id";
        $queryUsuario = $db->prepare($sqlUsuario);
        $queryUsuario->bindParam(":id", $_POST['id']);
        $queryUsuario->bindParam(":usuario", $_POST['usuario']);
        $queryUsuario->execute();
        if($queryUsuario->rowCount()==1){
            $this->retornaErro('Erro ao editar, nome de usuário em uso');
        }

        // ve se já nao tem usuario cadastrado com esse telefone
        $sqlTelefone = "SELECT * FROM pessoas WHERE telefone=:telefone and id!=:id";
        $queryTelefone = $db->prepare($sqlTelefone);
        $queryTelefone->bindParam(":id", $_POST['id']);
        $queryTelefone->bindParam(":telefone", $_POST['telefone']);
        $queryTelefone->execute();
        if($queryTelefone->rowCount()==1){
            $this->retornaErro('Erro ao editar, telefone em uso');
        }

        if ($_POST['senha']!=''){
            $sql = "UPDATE pessoas SET nome=:nome, telefone=:telefone, usuario=:usuario, senha=:senha WHERE id=:id";

            $query = $db->prepare($sql);
            $query->bindParam(":nome", $_POST['nome']);
            $query->bindParam(":telefone", $_POST['telefone']);
            $query->bindParam(":usuario", $_POST['usuario']);
            $query->bindParam(":senha", $criptografaSenha);
            $query->bindParam(":id", $_POST['id']);
            $query->execute();

            if ($query->rowCount()==1) {
                $this->retornaOK('A pessoa foi alterada com sucesso');
            }else{
                $this->retornaErro('Nenhum dado alterado');
            }
        }else{
            $sql = "UPDATE pessoas SET nome=:nome, telefone=:telefone, usuario=:usuario WHERE id=:id";

            $query = $db->prepare($sql);
            $query->bindParam(":nome", $_POST['nome']);
            $query->bindParam(":telefone", $_POST['telefone']);
            $query->bindParam(":usuario", $_POST['usuario']);
            $query->bindParam(":id", $_POST['id']);
            $query->execute();


            if ($query->rowCount()==1) {
                $this->retornaOK('A pessoa foi alterada com sucesso');
            }else{
                $this->retornaErro('Nenhum dado alterado');
            }
        }




    }

    public function excluir(){

        try{
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
        }catch(\Exception $exception){
            $this->retornaErro($exception->getMessage());
        }

    }

    public function desativar()
    {
        try{
            $db = Conexao::connect();

            $sql = "UPDATE pessoas SET status='desativado' WHERE id=:id";

            $query = $db->prepare($sql);
            $query->bindParam(":id", $_POST['id']);
            $query->execute();

            if ($query->rowCount()==1) {
                $this->retornaOK('Alterado com sucesso');
            }else{
                $this->retornaErro('Erro ao excluir os dados');
            }
        }catch(\Exception $exception){
            $this->retornaErro($exception->getMessage());
        }
    }

    public function ativar()
    {
        try{
            $db = Conexao::connect();

            $sql = "UPDATE pessoas SET status='ativo' WHERE id=:id";

            $query = $db->prepare($sql);
            $query->bindParam(":id", $_POST['id']);
            $query->execute();

            if ($query->rowCount()==1) {
                $this->retornaOK('Alterado com com sucesso');
            }else{
                $this->retornaErro('Erro ao excluir os dados');
            }
        }catch(\Exception $exception){
            $this->retornaErro($exception->getMessage());
        }
    }


    public function bootgrid()
    {
        $busca = addslashes($_POST['searchPhrase']);
        $sql = "SELECT `id`, `nome`, `telefone`, `usuario`, `tipo`, IF(STRCMP(status,'ativo') = 0, 0, 1) as status  FROM pessoas WHERE 1 ";
//        $sql = "SELECT `id`, `nome`, `telefone`, `usuario`, `tipo`, `status`  FROM pessoas WHERE 1 ";

        if ($busca!=''){
            $sql .= " and (
                        id LIKE '%{$busca}%' OR
                        nome LIKE '%{$busca}%' OR
                        telefone LIKE '%{$busca}%' OR
                        usuario LIKE '%{$busca}%' OR
                        tipo LIKE '%{$busca}%' OR
                        status LIKE '%{$busca}%'
                        ) ";
        }

        $bootgrid = new Bootgrid($sql);
        echo $bootgrid->show();
    }

}