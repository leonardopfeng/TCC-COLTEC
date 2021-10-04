<?php


namespace App\Controllers;

use App\Conexao;
use App\Bootgrid;
use App\ControllerSeguro;

class testesortable Extends ControllerSeguro
{
    public function __construct()
    {
        parent::__construct();

//        if ($_SESSION['tipo']!='personal'){
//            $this->errorPermission();
//        }
    }

    public function index()
    {
        echo $this->template->twig->render('grupo_muscular/listagem.html.twig');
    }

    public function formCadastrar()
    {
        $db = Conexao::connect();

      /*  $sql_grupomuscular = "SELECT id, nome FROM grupo_muscular";

      $sql_grupomuscular = "SELECT exercicios.nome_exercicio, grupo_muscular.id, grupo_muscular.nome FROM grupo_muscular INNER JOIN exercicios ON grupo_muscular.id=exercicios.grupo_muscular;";

        $query_grupomuscular = $db->prepare($sql_grupomuscular);

        $query_grupomuscular->execute();

        $grupomuscular = $query_grupomuscular->fetchAll();

        print_r($grupomuscular);*/



       /* $sql_aaaa = "SELECT exercicios.nome_exercicio, grupo_muscular.id, grupo_muscular.nome
                              FROM grupo_muscular
                              INNER JOIN exercicios ON grupo_muscular.id=exercicios.grupo_muscular";*/



        $sql_grupomuscular = "SELECT * FROM grupo_muscular ORDER by nome";

        $query_grupomuscular = $db->prepare($sql_grupomuscular);

        $query_grupomuscular->execute();
        $i=0;
        while ($linhaGrupo =  $query_grupomuscular->fetchObject()){
            $sql_exercicios = "SELECT * FROM exercicios where grupo_muscular={$linhaGrupo->id}";
            $query_exercicios = $db->query($sql_exercicios);
            $grupomuscular[$i] = $linhaGrupo;
            $grupomuscular[$i]->exercicios = $query_exercicios->fetchAll(\PDO::FETCH_OBJ);
            $i++;
        };



        $sqlCliente = "SELECT clientes.pessoa, pessoas.id, pessoas.nome FROM clientes INNER JOIN pessoas ON clientes.pessoa=pessoas.id";
        $query = $db->prepare($sqlCliente);
        $query->execute();

        $Clientes = $query->fetchAll();




        echo $this->template->twig->render('testesortable/cadastrar.html.twig', compact('grupomuscular','Clientes'));
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
        try {

            $cliente = $_POST['clientes'];

            $db = Conexao::connect();
            $db->beginTransaction();
            $query = $db->prepare("INSERT INTO treinos (clientes_pessoa, personal_pessoa, status) VALUES (:clientes, :personal, 'ativo') ");
            $query->bindParam(':clientes', $cliente);
            $query->bindParam(':personal', $_SESSION['id']);
            $query->execute();


           //Puxar o ultimo treino inserido

            $id_treino = $db->lastInsertId();

            for($ordem=0; $ordem<count($_POST['id_exercicio']); $ordem++){
                //checar se já não foi inserido o exercício no treino

                $sql = "INSERT INTO exercicios_treino(id_treino, id_exercicio, serie, repeticao, carga, ordem) VALUES(:id_treino, :id_exercicio, :serie, :repeticao, :carga, :ordem)";
                $query = $db->prepare($sql);
                $query->bindParam(":id_treino",$id_treino);
                $query->bindParam(":id_exercicio",$_POST['id_exercicio'][$ordem]);
                $query->bindParam(":serie",$_POST['serie'][$ordem]);
                $query->bindParam(":carga",$_POST['carga'][$ordem]);
                $query->bindParam(":repeticao",$_POST['repeticao'][$ordem]);
                $query->bindValue(":ordem",($ordem+1));
                $query->execute();

            }
            $db->commit();

           header('Location: /exercicios');

        }catch(\Exception $error){
            $db->rollBack();
            echo 'Erro ao inserir';
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