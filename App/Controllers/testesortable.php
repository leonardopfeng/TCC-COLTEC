<?php


namespace App\Controllers;

use App\Conexao;
use App\Bootgrid;
use App\ControllerSeguro;

class testesortable Extends ControllerSeguro
{
    protected $nivel = [ 'admin', 'personal', 'cliente' ];
    public function index()
    {
        echo $this->template->twig->render('testesortable/listagem.html.twig');
    }

    public function exibirExercicios($idTreino)
    {

      $_SESSION['idTreino'] = $idTreino;

      echo $this->template->twig->render('testesortable/exibirExercicios.html.twig');
    }

    public function listaTreinos(){

        $db = Conexao::connect();

        $sql = "SELECT * FROM treinos WHERE clientes_pessoa = $_SESSION[id] and status ='ativo'";
        $query = $db->prepare($sql);
        $query->execute();

        $treinos = $query->fetchAll();

        echo $this->template->twig->render('testesortable/listaTreinos.html.twig',compact('treinos'));
    }

    public function listaExercicios($idTreino){

        $_SESSION['idTreino'] = $idTreino;

        $db = Conexao::connect();

        $sqlExercicio = "SELECT exercicios_treino.id_exercicio, exercicios.nome_exercicio FROM exercicios_treino
                        INNER JOIN exercicios ON exercicios_treino.id_exercicio = exercicios.id_exercicio
                        WHERE id_treino = $_SESSION[idTreino]";
        $queryExercicio = $db->prepare($sqlExercicio);
        $queryExercicio->execute();
        $i=0;
        while($linhaExercicio = $queryExercicio->fetchObject()){
            $sqlInfo = "SELECT exercicios_treino.id_exercicio, exercicios_treino.serie,
                        exercicios_treino.repeticao, exercicios_treino.carga, exercicios.url_video
                        FROM treinos                    
                        INNER JOIN exercicios_treino ON exercicios_treino.id_treino=treinos.idtreinos
                        INNER JOIN exercicios ON exercicios_treino.id_exercicio=exercicios.id_exercicio
                        WHERE treinos.idtreinos = $_SESSION[idTreino] and exercicios_treino.id_exercicio ={$linhaExercicio->id_exercicio}";
            $queryInfo = $db->query($sqlInfo);
            $informacoes[$i] = $linhaExercicio;
            $informacoes[$i]->info = $queryInfo->fetchAll(\PDO::FETCH_OBJ);
            $i++;
        }
    
        echo $this->template->twig->render('testesortable/listaExercicios.html.twig', compact('informacoes'));
    }

    public function formCadastrar()
    {
        $db = Conexao::connect();

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
            $nome_treino = $_POST['nome'];


            $db = Conexao::connect();
            $db->beginTransaction();
            $query = $db->prepare("INSERT INTO treinos (clientes_pessoa, personal_pessoa, nome, status, data_inicio) VALUES (:clientes, :personal, :nome, 'ativo', NOW()) ");
            $query->bindParam(':clientes', $cliente);
            $query->bindParam(':personal', $_SESSION['id']);
            $query->bindParam(':nome', $nome_treino);
            $query->execute();


           //Puxar o ultimo treino inserido

            $id_treino = $db->lastInsertId();

            for($ordem=0; $ordem<count($_POST['id_exercicio']); $ordem++){
                //checar se j?? n??o foi inserido o exerc??cio no treino

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
            if($query->rowCount()==1){
                $this->retornaOK('Treino cadastrado com sucesso');
                header('Location: /exercicios');
            }
            else{
                $this->retornaErro('Erro ao cadastrar treino');
            }

        }catch(\Exception $error){
            $db->rollBack();
            $this->retornaErro('Erro ao inserir');
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

        $sql = "DELETE FROM treinos WHERE idtreinos=:id";

        $query = $db->prepare($sql);
        $query->bindParam(":id", $_POST['id']);
        $query->execute();

        if ($query->rowCount()==1) {
            $this->retornaOK('Excluido com sucesso');
        }else{
            $this->retornaErro('Erro ao excluir os dados');
        }
    }

    public function ativar()
    {
        try{
            $db = Conexao::connect();

            $sql = "UPDATE treinos SET status='ativo' WHERE idtreinos=:id";

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

    public function desativar()
    {
        try{
            $db = Conexao::connect();

            $sql = "UPDATE treinos SET status='desativado' WHERE idtreinos=:id";

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


    public function bootgrid()
    {
        $busca = addslashes($_POST['searchPhrase']);
        $sql = "SELECT treinos.idtreinos, pessoas.nome, IF(STRCMP(treinos.status,'ativo') = 0, 0, 1) as status, treinos.nome as nome_treino, data_inicio
                FROM TREINOS 
                INNER JOIN pessoas ON pessoas.id=treinos.clientes_pessoa 
                WHERE personal_pessoa = $_SESSION[id]
                ";

        if ($busca!=''){
            $sql .= " and (
                            treinos.idtreinos LIKE '%{$busca}%' OR
                            pessoas.nome LIKE '%{$busca}%' OR
                            treinos.status LIKE '%{$busca}%' OR
                            treinos.nome LIKE '%{$busca}%'
                            ) ";
        }

        $bootgrid = new Bootgrid($sql);
        echo $bootgrid->show();
    }


}