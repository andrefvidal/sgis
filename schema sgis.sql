-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema sgis
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `sgis` ;

-- -----------------------------------------------------
-- Schema sgis
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `sgis` DEFAULT CHARACTER SET utf8 ;
USE `sgis` ;

-- -----------------------------------------------------
-- Table `sgis`.`agenda_apoio_diaria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`agenda_apoio_diaria` (
  `idagenda_apoio` INT(11) NOT NULL AUTO_INCREMENT,
  `chamadofilho_id` INT(11) NOT NULL,
  `chamadoapoio_id` INT(11) NOT NULL,
  `data_agenda` DATE NOT NULL,
  `rdm_apoio` VARCHAR(45) NOT NULL,
  `status_rdm_apoio` VARCHAR(45) NOT NULL,
  `tecnico_campo_apoio` VARCHAR(45) NULL,
  `tecnico_executor_apoio` VARCHAR(45) NULL,
  `status_apoio` VARCHAR(45) NOT NULL,
  `logs_agenda_apoio` VARCHAR(45) NULL,
  `justificativa_apoio` VARCHAR(45) NULL,
  `observacao_ag_apoio` VARCHAR(3000) NULL,
  PRIMARY KEY (`idagenda_apoio`),
  UNIQUE INDEX `chamadofilho_id_UNIQUE` (`chamadoapoio_id` ASC, `data_agenda` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`agenda_diaria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`agenda_diaria` (
  `idagenda_diaria` INT(11) NOT NULL AUTO_INCREMENT,
  `chamado_id` VARCHAR(45) NOT NULL,
  `data_agenda` DATE NOT NULL,
  `rdm_agenda` VARCHAR(45) NOT NULL,
  `status_rdm_agenda` VARCHAR(45) NOT NULL,
  `tecnico_campo_agenda` VARCHAR(45) NULL,
  `tecnico_executor_agenda` VARCHAR(45) NULL,
  `status_agenda` VARCHAR(45) NOT NULL,
  `logs_agenda` VARCHAR(45) NULL,
  `justificativa_agenda` VARCHAR(500) NULL,
  `observacao_agenda` VARCHAR(1000) NULL,
  PRIMARY KEY (`idagenda_diaria`),
  UNIQUE INDEX `chamado_id_UNIQUE` (`chamado_id` ASC, `data_agenda` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 59
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`setor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`setor` (
  `idsetor` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_setor` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idsetor`),
  UNIQUE INDEX `nome_setor_UNIQUE` (`nome_setor` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`departamento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`departamento` (
  `iddepartamento` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_departamento` VARCHAR(45) NOT NULL,
  `setor` INT(11) NOT NULL,
  PRIMARY KEY (`iddepartamento`, `setor`),
  UNIQUE INDEX `setor_UNIQUE` (`nome_departamento` ASC),
  INDEX `fk_departamento_setor_idx` (`setor` ASC),
  CONSTRAINT `fk_departamento_setor`
    FOREIGN KEY (`setor`)
    REFERENCES `sgis`.`setor` (`idsetor`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`equipe`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`equipe` (
  `idequipe` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_equipe` VARCHAR(45) NOT NULL,
  `departamento` INT(11) NOT NULL,
  PRIMARY KEY (`idequipe`, `departamento`),
  UNIQUE INDEX `nome_equipe_UNIQUE` (`nome_equipe` ASC, `departamento` ASC),
  INDEX `fk_equipe_departamento_idx` (`departamento` ASC),
  CONSTRAINT `fk_equipe_departamento`
    FOREIGN KEY (`departamento`)
    REFERENCES `sgis`.`departamento` (`iddepartamento`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 19
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`usuario` (
  `idusuario` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `sobrenome` VARCHAR(45) NOT NULL,
  `nivel` INT(1) NOT NULL,
  `matricula_gvt` VARCHAR(45) NOT NULL,
  `email_gvt` VARCHAR(45) NOT NULL,
  `matricula_telefonica` VARCHAR(45) NOT NULL,
  `email_telefonica` VARCHAR(45) NOT NULL,
  `cargo` VARCHAR(45) NULL DEFAULT NULL,
  `data_criacao` VARCHAR(45) NOT NULL DEFAULT 'CURRENT_TIMESTAMP',
  `equipe` INT(11) NOT NULL,
  PRIMARY KEY (`idusuario`, `equipe`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC, `sobrenome` ASC, `matricula_gvt` ASC, `email_gvt` ASC, `matricula_telefonica` ASC, `email_telefonica` ASC),
  INDEX `fk_usuario_equipe_idx` (`equipe` ASC),
  CONSTRAINT `fk_usuario_equipe`
    FOREIGN KEY (`equipe`)
    REFERENCES `sgis`.`equipe` (`idequipe`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`pais`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`pais` (
  `idpais` INT(11) NOT NULL AUTO_INCREMENT,
  `pais` VARCHAR(60) NOT NULL,
  `sigla` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`idpais`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sgis`.`estado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`estado` (
  `idestado` INT(11) NOT NULL,
  `estado` VARCHAR(75) NOT NULL,
  `uf` VARCHAR(5) NOT NULL,
  `pais` INT(11) NOT NULL,
  PRIMARY KEY (`idestado`, `pais`),
  INDEX `fk_estado_pais_idx` (`pais` ASC),
  CONSTRAINT `fk_estado_pais`
    FOREIGN KEY (`pais`)
    REFERENCES `sgis`.`pais` (`idpais`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sgis`.`cidade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`cidade` (
  `idcidade` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_cidade` VARCHAR(120) NOT NULL,
  `sigla` VARCHAR(10) NULL DEFAULT NULL,
  `estado` INT(11) NOT NULL,
  PRIMARY KEY (`idcidade`, `estado`),
  INDEX `fk_cidade_estado_idx` (`estado` ASC),
  CONSTRAINT `fk_cidade_estado`
    FOREIGN KEY (`estado`)
    REFERENCES `sgis`.`estado` (`idestado`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 5565
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sgis`.`prioridade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`prioridade` (
  `idprioridade` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_prioridade` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idprioridade`),
  UNIQUE INDEX `nome_prioridade_UNIQUE` (`nome_prioridade` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`status` (
  `idstatus` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_status` VARCHAR(45) NOT NULL,
  `tipo_chamado` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idstatus`),
  UNIQUE INDEX `nome_status_UNIQUE` (`nome_status` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 14
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`tipo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`tipo` (
  `idtipo` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_tipo` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`idtipo`),
  UNIQUE INDEX `nome_tipo_UNIQUE` (`nome_tipo` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`chamado_pai`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`chamado_pai` (
  `idchamado` INT(11) NOT NULL AUTO_INCREMENT,
  `chamado` VARCHAR(45) NOT NULL,
  `autor` VARCHAR(45) NOT NULL,
  `nome_projeto` VARCHAR(100) NOT NULL,
  `descricao` VARCHAR(1000) NOT NULL,
  `vencimento` DATE NOT NULL,
  `historico` VARCHAR(10000) NULL DEFAULT NULL,
  `data_abertura` DATE NULL DEFAULT NULL,
  `data_aprovacao` DATE NULL DEFAULT NULL,
  `data_entrega_c_p` DATE NULL DEFAULT NULL,
  `limitematerial_pcp` DATE NULL DEFAULT NULL,
  `tipo_c_p` INT(11) NOT NULL,
  `matricula_c_p` INT(11) NOT NULL,
  `prioridade_c_p` INT(11) NOT NULL,
  `cidade_c_p` INT(11) NOT NULL,
  `status_c_p` INT(11) NOT NULL,
  `justificativa_c_p` VARCHAR(45) NULL DEFAULT NULL,
  `equipe_c_p` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idchamado`, `tipo_c_p`, `matricula_c_p`, `prioridade_c_p`, `cidade_c_p`, `status_c_p`),
  INDEX `fk_chamado_tipo_idx` (`tipo_c_p` ASC),
  INDEX `fk_chamado_usuario_idx` (`matricula_c_p` ASC),
  INDEX `fk_chamado_prioridade_idx` (`prioridade_c_p` ASC),
  INDEX `fk_chamado_status_idx` (`status_c_p` ASC),
  INDEX `fk_chamado_cidade_idx` (`cidade_c_p` ASC),
  UNIQUE INDEX `chamado_UNIQUE` (`chamado` ASC),
  CONSTRAINT `fk_chamado_cidade`
    FOREIGN KEY (`cidade_c_p`)
    REFERENCES `sgis`.`cidade` (`idcidade`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_chamado_prioridade`
    FOREIGN KEY (`prioridade_c_p`)
    REFERENCES `sgis`.`prioridade` (`idprioridade`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_chamado_status`
    FOREIGN KEY (`status_c_p`)
    REFERENCES `sgis`.`status` (`idstatus`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_chamado_tipo`
    FOREIGN KEY (`tipo_c_p`)
    REFERENCES `sgis`.`tipo` (`idtipo`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_chamado_usuario`
    FOREIGN KEY (`matricula_c_p`)
    REFERENCES `sgis`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 98
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`arquivo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`arquivo` (
  `idarquivo` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_arquivo` VARCHAR(250) NOT NULL,
  `caminho` VARCHAR(250) NOT NULL,
  `tamanho` INT(11) NULL DEFAULT NULL,
  `extensao` VARCHAR(45) NULL DEFAULT NULL,
  `matricula_arquivo` INT(11) NOT NULL,
  `arquivo_chamado` VARCHAR(45) NULL,
  `observacao_arq` VARCHAR(3000) NULL DEFAULT NULL,
  `data_criacao_arq` VARCHAR(45) NOT NULL DEFAULT 'CURRENT_TIMESTAMP',
  `projeto` VARCHAR(5) NULL DEFAULT NULL,
  PRIMARY KEY (`idarquivo`, `matricula_arquivo`),
  INDEX `fk_arquivo_usuario_idx` (`matricula_arquivo` ASC),
  INDEX `fk_arquivo_chamado_pai_idx` (`arquivo_chamado` ASC),
  CONSTRAINT `fk_arquivo_usuario`
    FOREIGN KEY (`matricula_arquivo`)
    REFERENCES `sgis`.`usuario` (`idusuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_arquivo_chamado_pai`
    FOREIGN KEY (`arquivo_chamado`)
    REFERENCES `sgis`.`chamado_pai` (`chamado`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 98
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`biblioteca`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`biblioteca` (
  `idbiblioteca` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(250) NOT NULL,
  `palavra_chave` VARCHAR(200) NOT NULL,
  `descricao` VARCHAR(3000) NOT NULL,
  `nome_arquivo` VARCHAR(250) NOT NULL,
  `caminho` VARCHAR(250) NOT NULL,
  `tamanho` VARCHAR(45) NULL DEFAULT NULL,
  `extensao` VARCHAR(45) NULL DEFAULT NULL,
  `autor` VARCHAR(100) NOT NULL,
  `idautor` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`idbiblioteca`),
  INDEX `fk_biblioteca_usuario1_idx` (`idautor` ASC),
  CONSTRAINT `fk_biblioteca_usuario1`
    FOREIGN KEY (`idautor`)
    REFERENCES `sgis`.`usuario` (`idusuario`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`chamado_filho`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`chamado_filho` (
  `idchamado_filho` INT(11) NOT NULL AUTO_INCREMENT,
  `chamado_pai` INT(11) NOT NULL,
  `resp_abertura` INT(11) NOT NULL,
  `equipe_apoio` INT(11) NOT NULL,
  `observacao_c_f` VARCHAR(3000) NOT NULL,
  `responsavel_c_f` VARCHAR(45) NULL DEFAULT NULL,
  `status_agenda_c_f` INT(11) NULL DEFAULT NULL,
  `cronograma_pcp` DATE NULL DEFAULT NULL,
  `rdm` VARCHAR(45) NULL DEFAULT NULL,
  `rdm_status` VARCHAR(45) NULL DEFAULT NULL,
  `horario_turno` VARCHAR(45) NULL DEFAULT NULL,
  `tempo_execucao` VARCHAR(45) NULL DEFAULT NULL,
  `tecnico_campo` VARCHAR(45) NULL DEFAULT NULL,
  `historico_c_f` VARCHAR(8000) NULL DEFAULT NULL,
  `data_resolucao` DATE NULL DEFAULT NULL,
  `cronograma_pcp_bkp` DATE NULL DEFAULT NULL,
  `rdm_bkp` VARCHAR(45) NULL DEFAULT NULL,
  `rdm_status_bkp` VARCHAR(45) NULL DEFAULT NULL,
  `ordem_agenda` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`idchamado_filho`, `chamado_pai`, `resp_abertura`, `equipe_apoio`),
  INDEX `fk_chamado_filho_chamado_idx` (`chamado_pai` ASC),
  INDEX `fk_chamado_filho_usuario_idx` (`resp_abertura` ASC),
  INDEX `fk_chamado_filho_equipe_idx` (`equipe_apoio` ASC),
  INDEX `fk_chamado_filho_status_idx` (`status_agenda_c_f` ASC),
  CONSTRAINT `fk_chamado_filho_chamado`
    FOREIGN KEY (`chamado_pai`)
    REFERENCES `sgis`.`chamado_pai` (`idchamado`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_chamado_filho_equipe`
    FOREIGN KEY (`equipe_apoio`)
    REFERENCES `sgis`.`equipe` (`idequipe`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_chamado_filho_status`
    FOREIGN KEY (`status_agenda_c_f`)
    REFERENCES `sgis`.`status` (`idstatus`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_chamado_filho_usuario`
    FOREIGN KEY (`resp_abertura`)
    REFERENCES `sgis`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 96
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`chamado_apoio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`chamado_apoio` (
  `idchamado_apoio` INT(11) NOT NULL AUTO_INCREMENT,
  `chamado_filho_apoio` INT(11) NOT NULL,
  `equipe_solicitante` INT(11) NOT NULL,
  `resp_solicitacao` INT(11) NOT NULL,
  `equipe_solicitada` INT(11) NOT NULL,
  `data_lmt_analise` DATE NOT NULL,
  `observacao_apoio` VARCHAR(1000) NOT NULL,
  `responsavel_analise` INT(11) NULL DEFAULT NULL,
  `analise_chamado` VARCHAR(45) NULL DEFAULT NULL,
  `data_analise` DATE NULL DEFAULT NULL,
  `tempo_exe_apoio` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idchamado_apoio`, `chamado_filho_apoio`),
  UNIQUE INDEX `chamado_filho_apoio_UNIQUE` (`chamado_filho_apoio` ASC, `equipe_solicitada` ASC),
  INDEX `fk_chamado_auxiliar_chamado_filho1_idx` (`chamado_filho_apoio` ASC),
  CONSTRAINT `fk_chamado_auxiliar_chamado_filho1`
    FOREIGN KEY (`chamado_filho_apoio`)
    REFERENCES `sgis`.`chamado_filho` (`idchamado_filho`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`checklist`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`checklist` (
  `idchecklist` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(250) NOT NULL,
  `palavra_chave` VARCHAR(200) NOT NULL,
  `descricao` VARCHAR(3000) NOT NULL,
  `nome_arquivo` VARCHAR(250) NOT NULL,
  `caminho` VARCHAR(250) NOT NULL,
  `tamanho` VARCHAR(45) NULL DEFAULT NULL,
  `extensao` VARCHAR(45) NULL DEFAULT NULL,
  `autor` VARCHAR(100) NOT NULL,
  `idautor` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`idchecklist`),
  INDEX `fk_biblioteca_usuario1_idx` (`idautor` ASC),
  CONSTRAINT `fk_biblioteca_usuario10`
    FOREIGN KEY (`idautor`)
    REFERENCES `sgis`.`usuario` (`idusuario`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`controle`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`controle` (
  `idcontrole` INT(11) NOT NULL AUTO_INCREMENT,
  `empresa_controle` VARCHAR(45) NOT NULL,
  `equipe_controle` VARCHAR(45) NOT NULL,
  `tipo_controle` VARCHAR(45) NOT NULL,
  `ultimo_numero` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idcontrole`),
  UNIQUE INDEX `equipe_controle_UNIQUE` (`empresa_controle` ASC, `equipe_controle` ASC, `tipo_controle` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`fluxograma_pai`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`fluxograma_pai` (
  `idfluxograma_pai` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_fluxograma` VARCHAR(45) NOT NULL,
  `equipe_fluxo_pai` INT(11) NOT NULL,
  `tipo_fluxo_pai` INT(11) NOT NULL,
  `equipeapoio_fluxo` INT(11) NOT NULL,
  `observacao_fluxo` VARCHAR(3000) NOT NULL,
  PRIMARY KEY (`idfluxograma_pai`),
  UNIQUE INDEX `fluxograma_equipe_UNIQUE` (`equipe_fluxo_pai` ASC, `tipo_fluxo_pai` ASC, `equipeapoio_fluxo` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`fluxograma_apoio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`fluxograma_apoio` (
  `idfluxograma_apoio` INT(11) NOT NULL,
  `equipe_fluxo_apoio` INT(11) NOT NULL,
  `tipo_fluxo_apoio` INT(11) NOT NULL,
  `status_anterior` INT(11) NOT NULL,
  `status_posterior` INT(11) NOT NULL,
  `novo_status_pai` INT(11) NULL DEFAULT NULL,
  `novofluxograma_pai` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`idfluxograma_apoio`),
  INDEX `fk_fluxograma_apoio_fluxograma_pai1_idx` (`novofluxograma_pai` ASC),
  CONSTRAINT `fk_fluxograma_apoio_fluxograma_pai1`
    FOREIGN KEY (`novofluxograma_pai`)
    REFERENCES `sgis`.`fluxograma_pai` (`idfluxograma_pai`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`justificativa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`justificativa` (
  `idjustificativa` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_justificativa` VARCHAR(200) NOT NULL,
  `status_justificativa` INT(11) NOT NULL,
  PRIMARY KEY (`idjustificativa`, `status_justificativa`),
  INDEX `fk_motivos_status1_idx` (`status_justificativa` ASC),
  CONSTRAINT `fk_motivos_status1`
    FOREIGN KEY (`status_justificativa`)
    REFERENCES `sgis`.`status` (`idstatus`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`meses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`meses` (
  `idmeses` INT(11) NOT NULL AUTO_INCREMENT,
  `nome_mes` VARCHAR(45) NOT NULL,
  `numero_mes` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idmeses`))
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sgis`.`sla`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sgis`.`sla` (
  `idsla` INT(11) NOT NULL AUTO_INCREMENT,
  `sla_vencimento` INT(11) NOT NULL,
  `sla_entrega` INT(11) NOT NULL,
  `idequipe_sla` INT(11) NOT NULL,
  `tipo_idtipo` INT(11) NOT NULL,
  PRIMARY KEY (`idsla`, `tipo_idtipo`),
  UNIQUE INDEX `dias_sla_UNIQUE` (`idequipe_sla` ASC, `tipo_idtipo` ASC),
  INDEX `fk_sla_tipo_idx` (`tipo_idtipo` ASC),
  CONSTRAINT `fk_sla_tipo`
    FOREIGN KEY (`tipo_idtipo`)
    REFERENCES `sgis`.`tipo` (`idtipo`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 17
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
