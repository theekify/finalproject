-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: jobportal
-- ------------------------------------------------------
-- Server version	8.0.40

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
-- Table structure for table `worker`
--

DROP TABLE IF EXISTS `worker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `worker` (
  `Worker_ID` int NOT NULL AUTO_INCREMENT,
  `User_ID` int NOT NULL,
  `Passport_Number` varchar(20) DEFAULT NULL,
  `Visa_Number` varchar(20) DEFAULT NULL,
  `Health_Report` varchar(255) DEFAULT NULL,
  `Training_Status` varchar(20) DEFAULT NULL,
  `Insurance_Status` varchar(20) DEFAULT NULL,
  `Certification_Issued` varchar(10) DEFAULT 'No',
  PRIMARY KEY (`Worker_ID`),
  UNIQUE KEY `User_ID` (`User_ID`),
  UNIQUE KEY `Visa_Number` (`Visa_Number`),
  UNIQUE KEY `Passport_Number` (`Passport_Number`),
  CONSTRAINT `worker_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE,
  CONSTRAINT `worker_chk_1` CHECK ((`Health_Report` in (_utf8mb4'Approved',_utf8mb4'Pending'))),
  CONSTRAINT `worker_chk_2` CHECK ((`Training_Status` in (_utf8mb4'Completed',_utf8mb4'In Progress'))),
  CONSTRAINT `worker_chk_3` CHECK ((`Insurance_Status` in (_utf8mb4'Active',_utf8mb4'Inactive'))),
  CONSTRAINT `worker_chk_4` CHECK ((`Health_Report` in (_utf8mb4'Approved',_utf8mb4'Pending'))),
  CONSTRAINT `worker_chk_5` CHECK ((`Health_Report` in (_utf8mb4'Approved',_utf8mb4'Pending')))
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `worker`
--

LOCK TABLES `worker` WRITE;
/*!40000 ALTER TABLE `worker` DISABLE KEYS */;
INSERT INTO `worker` VALUES (5,40,NULL,NULL,'Pending','In Progress','Inactive','No');
/*!40000 ALTER TABLE `worker` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-04  9:12:10
