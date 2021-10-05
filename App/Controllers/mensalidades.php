<?php


namespace App\Controllers;

use App\Controller;
use App\Conexao;
use App\Bootgrid;
use App\ControllerSeguro;

class mensalidades Extends Controller
{
    public function index()
    {
        echo $this->template->twig->render('mensalidades/listagem.html.twig');
    }

    public function formCadastrar()
    {
        $db = Conexao::connect();

        $sql = "SELECT pessoas.id, pessoas.nome FROM pessoas WHERE tipo='cliente' ";

        $query = $db->prepare($sql);

        $query->execute();

        $clientes = $query->fetchAll();

        echo $this->template->twig->render('mensalidades/cadastrar.html.twig', compact ('clientes'));
    }

    public function formEditar($id)
    {
        $db = Conexao::connect();

        $sql = "SELECT * FROM grupo_muscular WHERE id=:id";

        $query = $db->prepare($sql);
        $query->bindParam(":id", $id);

        $resultado = $query->execute();

        $linha = $query->fetch();

        echo $this->template->twig->render('mensalidades/editar.html.twig', compact('linha'));
    }


    public function salvarCadastrar()
    {



        $total = $_POST['valor'] - $_POST['desconto'];
        print_r($total);

        $db = Conexao::connect();

        if($_POST['desconto']<$_POST['valor']) {

            $sql = "INSERT INTO mensalidades (cliente, valor, desconto, total, data_pagamento, data_vencimento) VALUES(:cliente, :valor, :desconto, :total, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH))";
            $query = $db->prepare($sql);
            $query->bindParam(":cliente", $_POST['cliente']);
            $query->bindParam(":valor", $_POST['valor']);
            $query->bindParam(":desconto", $_POST['desconto']);
            $query->bindParam(":total", $total);
            $query->execute();

            if ($query->rowCount() == 1) {
                $this->retornaOK('A respectiva mensalidade foi cadastrada com sucesso');
            }

        }else{
            $this->retornaErro('O valor do desconto é maior que o valor da mensalidade');
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
        $sql = "SELECT mensalidades.id, mensalidades.cliente, mensalidades.total,
                       mensalidades.data_pagamento, mensalidades.data_vencimento,
                       pessoas.nome
                FROM mensalidades 
                INNER JOIN pessoas ON pessoas.id = mensalidades.cliente                
                WHERE 1 ";

        if ($busca!=''){
            $sql .= " and (
                            id LIKE '%{$busca}%' OR
                            nome LIKE '%{$busca}%' OR
                            total LIKE '%{$busca}%' OR
                            data_pagamento LIKE '%{$busca}%' OR
                            data_vencimento LIKE '%{$busca}%'
                            ) ";
        }

        $bootgrid = new Bootgrid($sql);
        echo $bootgrid->show();
    }

}