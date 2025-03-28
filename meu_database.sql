CREATE SCHEMA IF NOT EXISTS `devsrn` DEFAULT CHARACTER SET utf8 ;

CREATE TABLE IF NOT EXISTS `devsrn`.`associado` (
  `id` INT(11) NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `cpf` VARCHAR(11) NOT NULL,
  `data_filiacao` DATE NOT NULL,
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE,
  UNIQUE INDEX `cpf_UNIQUE` (`cpf` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `devsrn`.`anuidade` (
  `id` INT(11) NOT NULL,
  `ano` YEAR NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `ano_UNIQUE` (`ano` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `devsrn`.`cobranca` (
  `associado_id` INT(11) NOT NULL,
  `anuidade_id` INT(11) NOT NULL,
  `data_vencimento` DATE NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 0,
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  INDEX `fk_cobranca_associado_idx` (`associado_id` ASC) VISIBLE,
  INDEX `fk_cobranca_anuidade1_idx` (`anuidade_id` ASC) VISIBLE,
  PRIMARY KEY (`associado_id`, `anuidade_id`),
  CONSTRAINT `fk_cobranca_associado`
    FOREIGN KEY (`associado_id`)
    REFERENCES `devsrn`.`associado` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cobranca_anuidade1`
    FOREIGN KEY (`anuidade_id`)
    REFERENCES `devsrn`.`anuidade` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

/* Correção: inclusão de auto incremento */
ALTER TABLE `devsrn`.`associado` 
CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT ;

ALTER TABLE `devsrn`.`anuidade` 
CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT ;


/* Alteração para criar espelho do valor e inclusão de data do pagamento */
ALTER TABLE `devsrn`.`cobranca` 
ADD COLUMN `valor` DECIMAL(10,2) NOT NULL AFTER `anuidade_id`,
ADD COLUMN `data_pagamento` DATETIME NULL DEFAULT NULL AFTER `data_vencimento`;