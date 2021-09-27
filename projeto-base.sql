-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: projeto-base
-- ------------------------------------------------------
-- Server version	5.7.24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `pessoa` int(11) NOT NULL,
  `personal` int(11) DEFAULT NULL,
  PRIMARY KEY (`pessoa`),
  KEY `FK_clientes_pessoas_idx` (`pessoa`),
  KEY `FK_clientes_personais_idx` (`personal`),
  CONSTRAINT `FK_clientes_personal` FOREIGN KEY (`personal`) REFERENCES `personal` (`pessoa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_clientes_pessoas` FOREIGN KEY (`pessoa`) REFERENCES `pessoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (3,2),(6,2);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercicios`
--

DROP TABLE IF EXISTS `exercicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercicios` (
  `id_exercicio` int(11) NOT NULL AUTO_INCREMENT,
  `nome_exercicio` varchar(45) NOT NULL,
  `url_video` varchar(256) NOT NULL,
  `grupo_muscular` int(11) NOT NULL,
  PRIMARY KEY (`id_exercicio`),
  KEY `fk_exercicios_grupo_muscular1_idx` (`grupo_muscular`),
  CONSTRAINT `fk_exercicios_grupo_muscular1` FOREIGN KEY (`grupo_muscular`) REFERENCES `grupo_muscular` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercicios`
--

LOCK TABLES `exercicios` WRITE;
/*!40000 ALTER TABLE `exercicios` DISABLE KEYS */;
INSERT INTO `exercicios` VALUES (3,'Gustavo','321321',1),(4,'Leonardo','dawdawdwdaw',3),(5,'dwdwadawdwa','dawdwdwadawdwdw',2),(6,'Leonardo','3213213213213',1),(7,'Puxada traseira','dawdawdaw',4);
/*!40000 ALTER TABLE `exercicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercicios_treino`
--

DROP TABLE IF EXISTS `exercicios_treino`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercicios_treino` (
  `id_treino` int(11) NOT NULL,
  `id_exercicio` int(11) NOT NULL,
  `serie` int(11) DEFAULT NULL,
  `repeticao` int(11) DEFAULT NULL,
  `carga` int(11) DEFAULT NULL,
  `ordem` int(11) NOT NULL,
  PRIMARY KEY (`id_treino`,`id_exercicio`),
  KEY `fk_treinos_has_exercicios_exercicios1_idx` (`id_exercicio`),
  KEY `fk_treinos_has_exercicios_treinos1_idx` (`id_treino`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercicios_treino`
--

LOCK TABLES `exercicios_treino` WRITE;
/*!40000 ALTER TABLE `exercicios_treino` DISABLE KEYS */;
/*!40000 ALTER TABLE `exercicios_treino` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupo_muscular`
--

DROP TABLE IF EXISTS `grupo_muscular`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupo_muscular` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupo_muscular`
--

LOCK TABLES `grupo_muscular` WRITE;
/*!40000 ALTER TABLE `grupo_muscular` DISABLE KEYS */;
INSERT INTO `grupo_muscular` VALUES (1,'Triceps'),(2,'Biceps'),(3,'Peito'),(4,'Costas');
/*!40000 ALTER TABLE `grupo_muscular` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensalidades`
--

DROP TABLE IF EXISTS `mensalidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensalidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente` int(11) NOT NULL,
  `valor` decimal(8,2) NOT NULL,
  `desconto` decimal(8,2) DEFAULT NULL,
  `total` decimal(8,2) NOT NULL,
  `data_pagamento` datetime NOT NULL,
  `data_vencimento` date DEFAULT NULL,
  `status` enum('pago','nao pago') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mensalidades_pessoas1_idx` (`cliente`),
  CONSTRAINT `fk_mensalidades_pessoas1` FOREIGN KEY (`cliente`) REFERENCES `pessoas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensalidades`
--

LOCK TABLES `mensalidades` WRITE;
/*!40000 ALTER TABLE `mensalidades` DISABLE KEYS */;
INSERT INTO `mensalidades` VALUES (1,6,11.00,1.00,10.00,'2021-09-27 14:22:03',NULL,NULL);
/*!40000 ALTER TABLE `mensalidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal`
--

DROP TABLE IF EXISTS `personal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal` (
  `pessoa` int(11) NOT NULL,
  `cref` varchar(45) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  PRIMARY KEY (`pessoa`),
  UNIQUE KEY `cref_UNIQUE` (`cref`),
  CONSTRAINT `FK_personal_pessoas` FOREIGN KEY (`pessoa`) REFERENCES `pessoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal`
--

LOCK TABLES `personal` WRITE;
/*!40000 ALTER TABLE `personal` DISABLE KEYS */;
INSERT INTO `personal` VALUES (2,'2','2021-06-23 14:08:34');
/*!40000 ALTER TABLE `personal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pessoas`
--

DROP TABLE IF EXISTS `pessoas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pessoas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `telefone` varchar(45) NOT NULL,
  `tipo` enum('admin','cliente','personal') NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_UNIQUE` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pessoas`
--

LOCK TABLES `pessoas` WRITE;
/*!40000 ALTER TABLE `pessoas` DISABLE KEYS */;
INSERT INTO `pessoas` VALUES (1,'Leonardo Pfeng','44','admin','31231','356a192b7913b04c54574d18c28d46e6395428ab'),(2,'Leonardo Pfeng','2','personal','2','da4b9237bacccdf19c0760cab7aec4a8359010b0'),(3,'Gustavo','55','admin','312312321','da39a3ee5e6b4b0d3255bfef95601890afd80709'),(5,'Leonardoa','42424214','admin','lelis','40bd001563085fc35165329ea1ff5c5ecbdbbeef'),(6,'A','32132132132132','cliente','123','6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2');
/*!40000 ALTER TABLE `pessoas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `treinos`
--

DROP TABLE IF EXISTS `treinos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `treinos` (
  `idtreinos` int(11) NOT NULL AUTO_INCREMENT,
  `clientes_pessoa` int(11) NOT NULL,
  `personal_pessoa` int(11) NOT NULL,
  `status` enum('ativo','desativado') NOT NULL,
  PRIMARY KEY (`idtreinos`),
  KEY `fk_treinos_clientes1_idx` (`clientes_pessoa`),
  KEY `fk_treinos_personal1_idx` (`personal_pessoa`),
  CONSTRAINT `fk_treinos_clientes1` FOREIGN KEY (`clientes_pessoa`) REFERENCES `clientes` (`pessoa`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_treinos_personal1` FOREIGN KEY (`personal_pessoa`) REFERENCES `personal` (`pessoa`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `treinos`
--

LOCK TABLES `treinos` WRITE;
/*!40000 ALTER TABLE `treinos` DISABLE KEYS */;
/*!40000 ALTER TABLE `treinos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-09-27 16:38:53
