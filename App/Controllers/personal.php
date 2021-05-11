<?php


namespace App\Controllers;

use App\Controller;
use App\Conexao;
use App\Bootgrid;
use App\ControllerSeguro;

class Personal Extends Controller
{
    public function index()
    {
        echo $this->template->twig->render('pessoas/listagem.html.twig');
    }

    public function formCadastrar()
    {
        echo $this->template->twig->render('personal/cadastrar.html.twig');
    }

    public function formEditar($pessoa)
    {
        $db = Conexao::connect();

        $sql = "SELECT * FROM personal WHERE pessoa=:pessoa";

        $query = $db->prepare($sql);
        $query->bindParam(":pessoa", $pessoa);

        $resultado = $query->execute();

        $linha = $query->fetch();

        echo $this->template->twig->render('personal/editar.html.twig', compact('linha'));
    }




    public function salvarCadastrar()
    {
        $db = Conexao::connect();

        $sql = "UPDATE personal SET cref=:cref, data_cadastro=NOW() WHERE pessoa=:pessoa";
        $query = $db->prepare($sql);
        $query->bindParam(":pessoa",$_POST['pessoa']);
        $query->bindParam(":cref", $_POST['cref']);
        $query->execute();

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