CREATE TABLE IF NOT EXISTS `b_itserw_lotoswcr_cert` (
    `ID` int NOT NULL AUTO_INCREMENT,
    `ACTIVE` varchar(1) NOT NULL,
    `USER_ID` int NOT NULL,
    `ORDER_ID` int NOT NULL,
    `CITY` varchar(64) NOT NULL,
    `MODEL` varchar(64) NOT NULL,
    `FIO` varchar(64) NOT NULL,
    `EMAIL` varchar(64) NOT NULL,
    `FILE_ID` varchar(100),
    `SENDED` varchar(1) NOT NULL,
    `DATE_INSERT` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `ORDER_DATE_INSERT` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ID`)
);