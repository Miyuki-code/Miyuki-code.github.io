-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: registro
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asistencias`
--

DROP TABLE IF EXISTS `asistencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `tipo_trabajador` varchar(50) NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `hora` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencias`
--

LOCK TABLES `asistencias` WRITE;
/*!40000 ALTER TABLE `asistencias` DISABLE KEYS */;
INSERT INTO `asistencias` VALUES (1,'Juan','Pérez','12345678','Vigilante','entrada','2025-05-13 06:11:33'),(2,'Juan','Pérez','12345678','Vigilante','salida','2025-05-13 06:11:38'),(3,'variedades','Yose','28688249','Vigilante','entrada','2025-05-13 06:15:09'),(4,'kervin','diaz','30993371','Cocinero','entrada','2025-05-13 06:15:14'),(5,'variedades','Yose','28688249','Vigilante','salida','2025-05-13 06:15:19'),(6,'kervin','diaz','30993371','Cocinero','entrada','2025-05-13 06:15:23'),(7,'kervin','diaz','30993371','Cocinero','salida','2025-05-13 06:15:29'),(8,'susu ledys','jkjkj','12761541','Obrero','entrada','2025-05-13 06:51:49'),(9,'susu ledys','jkjkj','12761541','Obrero','salida','2025-05-13 06:51:53'),(10,'variedades','Yose','28688249','Vigilante','entrada','2025-05-17 06:57:42'),(11,'variedades','Yose','28688249','Vigilante','salida','2025-05-17 06:57:53'),(12,'kervin','diaz','30993371','Vigilante','entrada','2025-05-22 10:35:33'),(13,'kervin','diaz','30993371','Vigilante','salida','2025-05-22 10:35:37'),(14,'variedades','Yose','28688249','Maestro','entrada','2025-05-31 10:54:44'),(15,'variedades','Yose','28688249','Maestro','entrada','2025-05-31 13:07:33'),(16,'variedades','Yose','28688249','Maestro','salida','2025-05-31 13:19:42');
/*!40000 ALTER TABLE `asistencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backup`
--

DROP TABLE IF EXISTS `backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup`
--

LOCK TABLES `backup` WRITE;
/*!40000 ALTER TABLE `backup` DISABLE KEYS */;
INSERT INTO `backup` VALUES (16,'registro.sql','2025-05-22 09:57:22'),(17,'registro.sql','2025-05-22 09:57:41'),(18,'registro.sql','2025-05-22 10:38:26'),(19,'registro.sql','2025-05-22 10:46:26'),(20,'registro.sql','2025-06-03 00:43:25');
/*!40000 ALTER TABLE `backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargos`
--

DROP TABLE IF EXISTS `cargos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cargos` (
  `id_cargo` int(11) NOT NULL AUTO_INCREMENT,
  `cargo` varchar(255) NOT NULL,
  PRIMARY KEY (`id_cargo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargos`
--

LOCK TABLES `cargos` WRITE;
/*!40000 ALTER TABLE `cargos` DISABLE KEYS */;
INSERT INTO `cargos` VALUES (1,'Cocinero'),(2,'Maestro'),(3,'Vigilante'),(4,'Obrero');
/*!40000 ALTER TABLE `cargos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `level_user`
--

DROP TABLE IF EXISTS `level_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `level_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roles` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `level_user`
--

LOCK TABLES `level_user` WRITE;
/*!40000 ALTER TABLE `level_user` DISABLE KEYS */;
INSERT INTO `level_user` VALUES (1,'Administrador'),(2,'Moderador'),(3,'Usuario');
/*!40000 ALTER TABLE `level_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medical_rest`
--

DROP TABLE IF EXISTS `medical_rest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medical_rest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_trabajador` int(11) NOT NULL,
  `expedicion` date NOT NULL,
  `Vence` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_medical_rest_trabajadores` (`id_trabajador`),
  CONSTRAINT `fk_medical_rest_trabajadores` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medical_rest`
--

LOCK TABLES `medical_rest` WRITE;
/*!40000 ALTER TABLE `medical_rest` DISABLE KEYS */;
INSERT INTO `medical_rest` VALUES (7,32,'2025-04-01','2025-06-05'),(8,33,'2025-05-01','2025-06-04'),(9,51,'2025-06-03','2025-07-03');
/*!40000 ALTER TABLE `medical_rest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (1,1,'d74ba29078dd06208c459d6cc84248c10339d5f68ed463553de0b71974215231','2025-06-03 02:26:55','2025-06-03 05:26:55'),(2,1,'69b55e33f47e40f3f556fca685645fe140341dbc58f5c509e67a79016ef7ace6','2025-06-03 02:27:35','2025-06-03 05:27:35'),(4,1,'0e024c3c775cf39f60eb95876147ebc41a8bdc26246463ca1f2df28bf614257b','2025-06-03 02:31:58','2025-06-03 05:31:58');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trabajadores`
--

DROP TABLE IF EXISTS `trabajadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trabajadores` (
  `id_trabajador` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `cargos` int(11) NOT NULL,
  PRIMARY KEY (`id_trabajador`),
  KEY `fk_trabajadores_cargos` (`cargos`),
  CONSTRAINT `fk_trabajadores_cargos` FOREIGN KEY (`cargos`) REFERENCES `cargos` (`id_cargo`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trabajadores`
--

LOCK TABLES `trabajadores` WRITE;
/*!40000 ALTER TABLE `trabajadores` DISABLE KEYS */;
INSERT INTO `trabajadores` VALUES (32,'kervin','diaz','30993371','04249677356',3),(33,'variedades','Yose','28688249','04148503709',1),(51,'susu ledys','jkjkj','28688249','04249677356',2);
/*!40000 ALTER TABLE `trabajadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL DEFAULT 3,
  PRIMARY KEY (`ID`),
  KEY `fk_usuarios_level_user` (`rol_id`),
  CONSTRAINT `fk_usuarios_level_user` FOREIGN KEY (`rol_id`) REFERENCES `level_user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Kervin Días ','kervindiaz2017@gmail.com','Craft','dcbfced1b4314250ca9f14cab5407cd0c8d58fc7ca2a3d2a4addfb3347247a16a35bfaf09d9151ccd40a98c3c35d43b26cfe1a6f8eea5d6318a7eb6b78fbe057',1),(22,'maria f','elieljuliansanchez@gmail.com','kervin','d404559f602eab6fd602ac7680dacbfaadd13630335e951f097af3900e9de176b6db28512f2e000b9d04fba5133e8b1c6e8df59db3a8ab9d60be4b97cc9e81db',2),(24,'Kervin Diaz','kervindiaz2021@gmail.com','CrafeosK','dcbfced1b4314250ca9f14cab5407cd0c8d58fc7ca2a3d2a4addfb3347247a16a35bfaf09d9151ccd40a98c3c35d43b26cfe1a6f8eea5d6318a7eb6b78fbe057',2),(25,'hola yo','siontv2021@gmail.com','hola','dcbfced1b4314250ca9f14cab5407cd0c8d58fc7ca2a3d2a4addfb3347247a16a35bfaf09d9151ccd40a98c3c35d43b26cfe1a6f8eea5d6318a7eb6b78fbe057',3),(26,'fdhgfdh','dfgdfg@gmail.com','agfdagh','f17413b1c781d8bde01c5e63d2bff4919e478665f039837c5891f491348b34056c47a1143db70c51cf89cbf157c8f82ca4fd2c4550ceb14b5576154e174b6e9b',3);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-03  1:35:04
