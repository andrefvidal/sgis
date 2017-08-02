<?php
class dao{
	
	//Função para realizar uma Query
	public function query($sql){
		$mysqli = new mysqli("localhost", "******", "******", "sgis");
		
		//Verificando a conexão
		if(mysqli_connect_errno()){
			$mysqli->close();
			return "Falha na conexão: ". mysqli_connect_error();
		}
		
		// Verificando a declaração SQL
		if(!($statement = $mysqli->prepare($sql))){
			$mysqli->close();
			return "Erro ao preparar a declaração MySQL: (".$mysqli->errno.")".$mysqli->error;
		}else{
			//Executanto a Query
			if (!$statement->execute()){
				if(strripos($statement->error, "Duplicate entry") !== false){
					$mysqli->close();
					return "Você está tentando inserir um valor duplicado no Banco de Dados.";
				}else{
					$mysqli->close();
					return "Erro ao executar a MySQL query: ".$statement->error;
				}
			}
			//Retornando os resultados
			$resultado = $statement->get_result();
			
			//Verificando o tipo da query SQL
			$palavras = explode(" ", $sql);

			if($palavras[0] == "SELECT"){//Se for uma Busca entra. Se não deixa passar.
				//Cria uma matriz vazia
				$emparray = array();
				//Enchendo a matriz com os resultados retornados
				while($row = $resultado->fetch_assoc()){
					$emparray[] = $row;
				}
				$mysqli->close();
				return $emparray;
			}else{ 
				$mysqli->close();
				return "Successfully";
			}
		}
	}
	
	//Funçõo PDO
	public function pdo()
    {
		global $conn;
		if( $conn )
            return $conn;
        // Informações para conexão
		$dsn = 'mysql:host=localhost;port=3306;dbname=sgis';
		try 
		{
			// Conectando
			$conn = new PDO($dsn, 'administrador', 'newmatrix');
			return $conn;
		} 
		catch (PDOException $e) 
		{
			// Se ocorrer algum erro na conexão
			die($e->getMessage());
			return $e;
		}
    }
	function valida_ldap($servidor, $usuario, $senha)//função para validar usuário no AD
	{
		$porta = '389';
		// Tenta se conectar com o servidor
		$conexao = ldap_connect($servidor, $porta) or die('Não pode se conectar ao servidor.');
		// Tenta autenticar no servidor
		if (!($bind = ldap_bind($conexao, $usuario, $senha))) {
			// se nao validar retorna false
			ldap_close($conexao);
			return FALSE;
		} else {
			ldap_close($conexao);
			// se validar retorna true
			return TRUE;
		}

	}
	function catch_usu_ldap($servidor, $usuario_dominio, $matricula, $senha)//Busca informações do usuário no AD
	{
		$porta = '389';
		// Tenta se conectar com o servidor
		$conexao = ldap_connect($servidor, $porta) or die('Não pode se conectar ao servidor.');
		ldap_set_option($conexao, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($conexao, LDAP_OPT_REFERRALS, false);
		// Tenta autenticar no servidor
		$bind = ldap_bind($conexao, $usuario_dominio, $senha);//Bind GVT
		//Informações necessárias para a pesquisa
		$base = "DC=gvt,DC=net,DC=br";
		$criteria = '(&(objectClass=user)(samaccountname='.$matricula.'))';
		$attributes = array("givenname","sn","title","department","userprincipalname","samaccountname","mail","employeeid","company","memberof","displayName");
		$search = ldap_search($conexao, $base, $criteria, $attributes);
		//Pesquisa, conexão e atributos da pesquisa
		$entries = ldap_get_entries($conexao, $search);
		//Encerra conexão ldap
		ldap_close($conexao);
		//Retorna o resultado da pesquisa
		return $entries;
	}
	public function creatSgis(){
		$create = 	"CREATE SCHEMA IF NOT EXISTS `sgis` DEFAULT CHARACTER SET utf8 ;";
		return $create;
	}
	public function creatChecklist(){
		$create = "CREATE TABLE IF NOT EXISTS `sgis`.`checklist` (
					`idchecklist` INT NOT NULL AUTO_INCREMENT,
					`titulo` VARCHAR(250) NOT NULL,
					`palavra_chave` VARCHAR(200) NOT NULL,
					`descricao` VARCHAR(1000) NOT NULL,
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
					ENGINE = InnoDB;";
		return $create;
	}
	public function creatMeses(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`meses` (
					`idmeses` INT NOT NULL AUTO_INCREMENT,
					`nome_mes` VARCHAR(45) NOT NULL,
					PRIMARY KEY (`idmeses`))
					ENGINE = InnoDB;";
		return $create;
	}
	public function creatBiblioteca(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`biblioteca` (
					`idbiblioteca` INT NOT NULL AUTO_INCREMENT,
					`titulo` VARCHAR(250) NOT NULL,
					`palavra_chave` VARCHAR(200) NOT NULL,
					`descricao` VARCHAR(1000) NOT NULL,
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
					ENGINE = InnoDB;";
		return $create;
	}
	public function creatAgendaFilhoDiaria(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`agenda_apoio_diaria` (
					`idagenda` INT NOT NULL AUTO_INCREMENT,
					`chamadopai_id` INT(11) NOT NULL,
					`chamadofilho_id` INT(11) NOT NULL,
					`data_agenda` VARCHAR(45) NOT NULL,
					`motivos_apoio` INT NULL DEFAULT NULL,
					`justificativa_apoio` VARCHAR(45) NULL DEFAULT NULL,
					PRIMARY KEY (`idagenda`),
					INDEX `fk_agenda_apoio_diaria_motivos1_idx` (`motivos_apoio` ASC),
					CONSTRAINT `fk_agenda_apoio_diaria_motivos1`
						FOREIGN KEY (`motivos_apoio`)
						REFERENCES `sgis`.`justificativa` (`idmotivos`)
						ON DELETE NO ACTION
						ON UPDATE NO ACTION)
					ENGINE = InnoDB;";
		return $create;
	}
	public function creatAgendaDiaria(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`agenda_diaria` (
					`idagenda_diaria` INT NOT NULL AUTO_INCREMENT,
					`chamado_id` VARCHAR(45) NOT NULL,
					`data_agenda` VARCHAR(45) NOT NULL,
					`rdm_agenda` VARCHAR(45) NOT NULL,
					`status_rdm_agenda` VARCHAR(45) NOT NULL,
					`tecnico_campo_agenda` VARCHAR(45) NOT NULL,
					`tecnico_executor_agenda` VARCHAR(45) NOT NULL,
					`status_agenda` VARCHAR(45) NOT NULL,
					`justificativa_agenda` INT(11) NULL DEFAULT NULL,
					`observacao_agenda` VARCHAR(45) NULL DEFAULT NULL,
					PRIMARY KEY (`idagenda_diaria`),
					INDEX `fk_agenda_diaria_motivos1_idx` (`justificativa_agenda` ASC),
					CONSTRAINT `fk_agenda_diaria_motivos1`
						FOREIGN KEY (`justificativa_agenda`)
						REFERENCES `sgis`.`justificativa` (`idjustificativa`)
						ON DELETE NO ACTION
						ON UPDATE NO ACTION)
					ENGINE = InnoDB;";
		return $create;
	}
	public function creatUsuario(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`usuario` (
					`idusuario` INT(11) NOT NULL AUTO_INCREMENT,
					`nome` VARCHAR(45) NOT NULL,
					`sobrenome` VARCHAR(45) NOT NULL,
					`nivel` INT(1) NOT NULL,
					`matricula_gvt` VARCHAR(45) NOT NULL,
					`email_gvt` VARCHAR(45) NOT NULL,
					`matricula_telefonica` VARCHAR(45) NULL,
					`email_telefonica` VARCHAR(45) NULL,
					`cargo` VARCHAR(45) NULL DEFAULT NULL,
					`data_criacao` VARCHAR(45) NOT NULL DEFAULT 'CURRENT_TIMESTAMP',
					`equipe` INT(11) NOT NULL,
					PRIMARY KEY (`idusuario`, `equipe`),
					INDEX `fk_usuario_equipe_idx` (`equipe` ASC),
					UNIQUE INDEX `matricula_gvt_UNIQUE` (`matricula_gvt` ASC),
					UNIQUE INDEX `matricula_telefonica_UNIQUE` (`matricula_telefonica` ASC),
					UNIQUE INDEX `email_gvt_UNIQUE` (`email_gvt` ASC),
					UNIQUE INDEX `email_telefonica_UNIQUE` (`email_telefonica` ASC),
					CONSTRAINT `fk_usuario_equipe`
						FOREIGN KEY (`equipe`)
						REFERENCES `sgis`.`equipe` (`idequipe`)
						ON DELETE CASCADE
						ON UPDATE CASCADE)
					ENGINE = InnoDB";
		return $create;
	}
	public function creatSetor(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`setor` (
					`idsetor` INT(11) NOT NULL AUTO_INCREMENT,
					`nome_setor` VARCHAR(45) NOT NULL,
					PRIMARY KEY (`idsetor`),
					UNIQUE INDEX `nome_setor_UNIQUE` (`nome_setor` ASC))
					ENGINE = InnoDB
					DEFAULT CHARACTER SET = utf8;";
		return $create;
	}
	public function creatDepartamento(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`departamento` (
					`iddepartamento` INT(11) NOT NULL AUTO_INCREMENT,
					`nome_departamento` VARCHAR(45) NOT NULL,
					`setor` INT(11) NOT NULL,
					PRIMARY KEY (`iddepartamento`, `setor`),
					UNIQUE INDEX `setor_UNIQUE` (`nome_departamento` ASC),
					INDEX `fk_departamento_setor_idx` (`setor` ASC),
					CONSTRAINT `fk_departamento_setor`
						FOREIGN KEY (`setor`)
						REFERENCES `sgis`.`setor` (`idsetor`)
						ON DELETE CASCADE
						ON UPDATE CASCADE)
					ENGINE = InnoDB";
		return $create;
	}
	public function creatEquipe(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`equipe` (
					`idequipe` INT(11) NOT NULL AUTO_INCREMENT,
					`nome_equipe` VARCHAR(45) NOT NULL,
					`departamento` INT(11) NOT NULL,
					PRIMARY KEY (`idequipe`, `departamento`),
					INDEX `fk_equipe_departamento_idx` (`departamento` ASC),
					UNIQUE INDEX `nome_equipe_UNIQUE` (`nome_equipe` ASC),
					CONSTRAINT `fk_equipe_departamento`
						FOREIGN KEY (`departamento`)
						REFERENCES `sgis`.`departamento` (`iddepartamento`)
						ON DELETE CASCADE
						ON UPDATE CASCADE)
					ENGINE = InnoDB";
		return $create;
	}
	public function creatTipo(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`tipo` (
					`idtipo` INT(11) NOT NULL AUTO_INCREMENT,
					`nome_tipo` VARCHAR(40) NOT NULL,
					PRIMARY KEY (`idtipo`))
					ENGINE = InnoDB";
		return $create;
	}
	public function creatPrioridade(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`prioridade` (
					`idprioridade` INT(11) NOT NULL AUTO_INCREMENT,
					`nome_prioridade` VARCHAR(45) NOT NULL,
					`sla` VARCHAR(45) NULL DEFAULT NULL,
					PRIMARY KEY (`idprioridade`))
					ENGINE = InnoDB";
		return $create;
	}
	public function creatStatus(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`status` (
					`idstatus` INT(11) NOT NULL AUTO_INCREMENT,
					`nome_status` VARCHAR(45) NOT NULL,
					PRIMARY KEY (`idstatus`))
					ENGINE = InnoDB;";
		return $create;
	}
	public function creatJustificativa(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`justificativa` (
					`idjustificativa` INT NOT NULL AUTO_INCREMENT,
					`nome_justificativa` VARCHAR(200) NOT NULL,
					`status_justificativa` INT(11) NOT NULL,
					PRIMARY KEY (`idmotivos`, `status_justificativa`),
					INDEX `fk_motivos_status1_idx` (`status_justificativa` ASC),
					CONSTRAINT `fk_motivos_status1`
						FOREIGN KEY (`status_justificativa`)
						REFERENCES `sgis`.`status` (`idstatus`)
						ON DELETE NO ACTION
						ON UPDATE NO ACTION)
					ENGINE = InnoDB;";
		return $create;
	}
	public function creatArquivo(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`arquivo` (
					`idarquivo` INT(11) NOT NULL AUTO_INCREMENT,
					`nome_arquivo` VARCHAR(250) NOT NULL,
					`caminho` VARCHAR(250) NOT NULL,
					`tamanho` INT(11) NULL DEFAULT NULL,
					`extensao` VARCHAR(45) NULL DEFAULT NULL,
					`matricula_arquivo` INT(11) NOT NULL,
					`arquivo_chamado` VARCHAR(45) NULL DEFAULT NULL,
					`arquivo_chamadoapoio` VARCHAR(45) NULL DEFAULT NULL,
					`observacao_arq` VARCHAR(45) NULL DEFAULT NULL,
					`data_criacao_arq` VARCHAR(45) NOT NULL DEFAULT 'CURRENT_TIMESTAMP',
					PRIMARY KEY (`idarquivo`, `matricula_arquivo`),
					INDEX `fk_arquivo_usuario_idx` (`matricula_arquivo` ASC),
					CONSTRAINT `fk_arquivo_usuario`
						FOREIGN KEY (`matricula_arquivo`)
						REFERENCES `sgis`.`usuario` (`idusuario`)
						ON DELETE CASCADE
						ON UPDATE CASCADE)
					ENGINE = InnoDB";
		return $create;
	}
	public function creatPais(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`pais` (
					`idpais` INT(11) NOT NULL AUTO_INCREMENT,
					`pais` VARCHAR(60) NOT NULL,
					`sigla` VARCHAR(10) NOT NULL,
					PRIMARY KEY (`idpais`))
					ENGINE = InnoDB
					AUTO_INCREMENT = 2
					DEFAULT CHARACTER SET = latin1;";
		return $create;
	}
	public function creatEstado(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`estado` (
					`idestado` INT(11) NOT NULL,
					`estado` VARCHAR(75) NOT NULL,
					`uf` VARCHAR(5) NOT NULL,
					`pais` INT(11) NOT NULL,
					PRIMARY KEY (`idestado`, `pais`),
					INDEX `fk_estado_pais_idx` (`pais` ASC),
					CONSTRAINT `fk_estado_pais`
						FOREIGN KEY (`pais`)
						REFERENCES `sgis`.`pais` (`idpais`)
						ON DELETE CASCADE
						ON UPDATE CASCADE)
					ENGINE = InnoDB
					AUTO_INCREMENT = 28
					DEFAULT CHARACTER SET = latin1;";
		return $create;
	}
	public function creatCidade(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`cidade` (
					`idcidade` INT(11) NOT NULL AUTO_INCREMENT,
					`nome_cidade` VARCHAR(120) NOT NULL,
					`sigla` VARCHAR(10) NULL DEFAULT NULL,
					`estado` INT(11) NOT NULL,
					PRIMARY KEY (`idcidade`, `estado`),
					INDEX `fk_cidade_estado_idx` (`estado` ASC),
					CONSTRAINT `fk_cidade_estado`
						FOREIGN KEY (`estado`)
						REFERENCES `sgis`.`estado` (`idestado`)
						ON DELETE CASCADE
						ON UPDATE CASCADE)
					ENGINE = InnoDB
					AUTO_INCREMENT = 5565
					DEFAULT CHARACTER SET = latin1;";
		return $create;
	}
	public function creatChamado(){
		$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`chamado_pai` (
					`idchamado` INT(11) NOT NULL AUTO_INCREMENT,
					`chamado` VARCHAR(45) NOT NULL,
					`autor` VARCHAR(45) NOT NULL,
					`data_aprovacao` VARCHAR(45) NOT NULL,
					`nome_projeto` VARCHAR(100) NOT NULL,
					`descricao` VARCHAR(1000) NOT NULL,
					`vencimento` VARCHAR(45) NOT NULL,
					`observacao` VARCHAR(1000) NULL DEFAULT NULL,
					`historico` VARCHAR(9000) NULL DEFAULT NULL,
					`data_resolucao` VARCHAR(45) NULL DEFAULT NULL,
					`limite_pcp` VARCHAR(45) NULL DEFAULT NULL,
					`limitematerial_pcp` VARCHAR(45) NULL DEFAULT NULL,
					`cronograma_pcp` VARCHAR(45) NULL DEFAULT NULL,
					`rdm` VARCHAR(45) NULL DEFAULT NULL,
					`rdm_status` VARCHAR(45) NULL DEFAULT NULL,
					`horario_turno` VARCHAR(45) NOT NULL,
					`tempo_execucao` VARCHAR(45) NOT NULL,
					`tecnico_campo` VARCHAR(45) NULL DEFAULT NULL,
					`tecnico_executor` VARCHAR(45) NULL DEFAULT NULL,
					`tipo` INT(11) NOT NULL,
					`matricula` INT(11) NOT NULL,
					`prioridade` INT(11) NOT NULL,
					`status` INT(11) NOT NULL,
					`cidade` INT(11) NOT NULL,
					`justificativa_chamado` INT NULL DEFAULT NULL,
					PRIMARY KEY (`idchamado`, `tipo`, `matricula`, `prioridade`, `status`, `cidade`),
					INDEX `fk_chamado_tipo_idx` (`tipo` ASC),
					INDEX `fk_chamado_usuario_idx` (`matricula` ASC),
					INDEX `fk_chamado_prioridade_idx` (`prioridade` ASC),
					INDEX `fk_chamado_status_idx` (`status` ASC),
					INDEX `fk_chamado_cidade_idx` (`cidade` ASC),
					UNIQUE INDEX `chamado_UNIQUE` (`chamado` ASC),
					INDEX `fk_chamado_motivos1_idx` (`justificativa_chamado` ASC),
					CONSTRAINT `fk_chamado_cidade`
						FOREIGN KEY (`cidade`)
						REFERENCES `sgis`.`cidade` (`idcidade`)
						ON DELETE CASCADE
						ON UPDATE CASCADE,
					CONSTRAINT `fk_chamado_prioridade`
						FOREIGN KEY (`prioridade`)
						REFERENCES `sgis`.`prioridade` (`idprioridade`)
						ON DELETE CASCADE
						ON UPDATE CASCADE,
					CONSTRAINT `fk_chamado_status`
						FOREIGN KEY (`status`)
						REFERENCES `sgis`.`status` (`idstatus`)
						ON DELETE CASCADE
						ON UPDATE CASCADE,
					CONSTRAINT `fk_chamado_tipo`
						FOREIGN KEY (`tipo`)
						REFERENCES `sgis`.`tipo` (`idtipo`)
						ON DELETE CASCADE
						ON UPDATE CASCADE,
					CONSTRAINT `fk_chamado_usuario`
						FOREIGN KEY (`matricula`)
						REFERENCES `sgis`.`usuario` (`idusuario`)
						ON DELETE CASCADE
						ON UPDATE CASCADE,
					CONSTRAINT `fk_chamado_motivos1`
						FOREIGN KEY (`justificativa_chamado`)
						REFERENCES `sgis`.`justificativa` (`idmotivos`)
						ON DELETE NO ACTION
						ON UPDATE NO ACTION)
					ENGINE = InnoDB";
		return $create;
	}
	public function creatCApoio(){
					$create = 	"CREATE TABLE IF NOT EXISTS `sgis`.`chamado_filho` (
					`idchamado_filho` INT(11) NOT NULL AUTO_INCREMENT,
					`chamado_pai` INT(11) NOT NULL,
					`resp_abertura` INT(11) NOT NULL,
					`equipe_apoio` INT(11) NOT NULL,
					`tempo_c_f` VARCHAR(45) NULL DEFAULT NULL,
					`executor_c_f` VARCHAR(45) NULL DEFAULT NULL,
					`observacao_c_f` VARCHAR(45) NULL DEFAULT NULL,
					`analise_chamado` VARCHAR(45) NULL DEFAULT NULL,
					PRIMARY KEY (`idchamado_filho`, `chamado_pai`, `resp_abertura`, `equipe_apoio`),
					INDEX `fk_chamado_apoio_chamado_idx` (`chamado_pai` ASC),
					INDEX `fk_chamado_apoio_usuario_idx` (`resp_abertura` ASC),
					INDEX `fk_chamado_apoio_equipe_idx` (`equipe_apoio` ASC),
					CONSTRAINT `fk_chamado_apoio_chamado`
						FOREIGN KEY (`chamado_pai`)
						REFERENCES `sgis`.`chamado_pai` (`idchamado`)
						ON DELETE CASCADE
						ON UPDATE CASCADE,
					CONSTRAINT `fk_chamado_apoio_usuario`
						FOREIGN KEY (`resp_abertura`)
						REFERENCES `sgis`.`usuario` (`idusuario`)
						ON DELETE CASCADE
						ON UPDATE CASCADE,
					CONSTRAINT `fk_chamado_apoio_equipe`
						FOREIGN KEY (`equipe_apoio`)
						REFERENCES `sgis`.`equipe` (`idequipe`)
						ON DELETE CASCADE
						ON UPDATE CASCADE)
					ENGINE = InnoDB;";
		return $create;
	}
	public function retorno($mensagem, $sucesso = false)
	{
		// Criando vetor com a propriedades
		$retorno = array();
		$retorno['sucesso'] = $sucesso;
		$retorno['mensagem'] = $mensagem;
	
		// Convertendo para JSON e retornando
		return json_encode($retorno);
	}
	function nl2br2($string)
	{ 
		$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string); 
		return $string; 
	}
	public function br_to_sql($data){
		if(count(explode("-",$data)) > 1){return implode("-",array_reverse(explode("/",$data)));}
	}
	public function sql_to_br($data){
		return date("d/m/Y", strtotime(strtr($data, '/', '-')));
	}
	public function sql_to_br_horas($data){
		return date("d/m/Y H:i", strtotime(strtr($data, '/', '-')));
	}
	public function inverteData($data){
		if(count(explode("/",$data)) > 1){
			return implode("-",array_reverse(explode("/",$data)));
		}elseif(count(explode("-",$data)) > 1){
			return implode("/",array_reverse(explode("-",$data)));
		}
	}
	public function cvtMinutos_Horas($time, $format = '%02d:%02d') {
		if ($time < 1) {
			return;
		}
		$hours = floor($time / 60);
		$minutes = ($time % 60);
		return sprintf($format, $hours, $minutes);
	}
	
	public function salvarArquivo($chamado,$observacao,$projeto){
		$upfile = $_FILES["arquivo"];
		//Cria uma nova instancia das classes SQL e DAO
		$go = new sql();$xo = new dao();
		// Verifica se o arquivo foi selecionado
		if (!empty($upfile["name"])) {
			$error = array();
			// Tamanho máximo do arquivo em bytes
			$tamanho = 10000000;
			// Verifica se o arquivo é uma texto ou planilha
			if(!preg_match("/\.(jpg|jpeg|gif|png|doc|docx|pdf|xls|xlsm|xlsx|msg|zip|7zip|html|txt){1}$/i", $upfile["name"])){
				$error[0] = "<script>alert('Certifique-se que esse é um arquivo de texto ou planilha.');</script>";}
			// Verifica se o tamanho do arquivo é maior que o tamanho permitido
			if($upfile["size"] > $tamanho) {
				$error[1] = "<script>alert('O arquivo deve ter no máximo ".$tamanho." bytes');</script>";}
			// Se não houver nenhum erro
			if (count($error) == 0) {
				// Pega extensão do arquivo
				preg_match("/\.(jpg|jpeg|gif|png|doc|docx|pdf|xls|xlsm|xlsx|msg|zip|7zip|html|txt){1}$/i", $upfile["name"], $ext);
				// Gera a extensao do arquivo
				$ext_arquivo = $ext[1];
				// Gera o nome do arquivo
				$nome_arquivo = preg_replace("[']", "", $upfile["name"]);
				// Caminho de onde ficará o arquivo
				$caminho_arquivo = "arquivos/" . preg_replace("[']", "", $upfile["name"]);
				// Insere os dados no banco
				$try = $go->inserArquivo($nome_arquivo,$caminho_arquivo,$upfile["size"],$ext_arquivo,$_SESSION['matr_usuario'],$chamado,$observacao,$projeto);
				$to = $xo->query($try);
				// Se os dados forem inseridos com sucesso
				if($to === "Successfully"){
					// Faz o upload do arquivo para seu respectivo caminho
					move_uploaded_file($upfile["tmp_name"], $caminho_arquivo);
					return "<script>alert(\"Arquivo salvo com sucesso.\");</script>";
				}else{
					return "<script>alert(\"".$to."\");</script>";
				}
			}elseif (count($error) != 0) {// Se houver mensagens de erro, exibe-as
				foreach ($error as $erro) {
					return $erro;
				}
			}
		}
	}
	
}

class sql{
	//Funções de Exclusão
	public function deleTable($table){
		$drop = "DROP TABLE IF EXISTS '".$table."'";
		return $drop;
	}
	public function deleUsuario($usuario){
		$sql = "DELETE FROM `sgis`.`usuario` WHERE `idusuario`='".$usuario."';";
		return $sql;
	}
	public function delePais($pais){
		$sql = "DELETE FROM `sgis`.`pais` WHERE `pais`='".$pais."';";
		return $sql;
	}
	public function delePais_id($idpais){
		$sql = "DELETE FROM `sgis`.`pais` WHERE `idpais`='".$idpais."';";
		return $sql;
	}
	public function deleEstado($estado){
		$sql = "DELETE FROM `sgis`.`estado` WHERE `estado`='".$estado."';";
		return $sql;
	}
	public function deleEstado_id($idestado){
		$sql = "DELETE FROM `sgis`.`estado` WHERE `idestado`='".$idestado."';";
		return $sql;
	}
	public function deleCidade($cidade){
		$sql = "DELETE FROM `sgis`.`cidade` WHERE `idcidade`='".$cidade."';";
		return $sql;
	}
	public function deleSetor($setor){
		$sql = "DELETE FROM `sgis`.`setor` WHERE `setor`='".$setor."';";
		return $sql;
	}
	public function deleSetor_id($idsetor){
		$sql = "DELETE FROM `sgis`.`setor` WHERE `idsetor`='".$idsetor."';";
		return $sql;
	}
	public function deleDepartamento($departamento){
		$sql = "DELETE FROM `sgis`.`departamento` WHERE `departamento`='".$departamento."';";
		return $sql;
	}
	public function deleDepartamento_id($iddepartamento){
		$sql = "DELETE FROM `sgis`.`departamento` WHERE `iddepartamento`='".$iddepartamento."';";
		return $sql;
	}
	public function deleEquipe($equipe){
		$sql = "DELETE FROM `sgis`.`equipe` WHERE `equipe`='".$equipe."';";
		return $sql;
	}
	public function deleEquipe_id($idequipe){
		$sql = "DELETE FROM `sgis`.`equipe` WHERE `idequipe`='".$idequipe."';";
		return $sql;
	}
	public function deleTipo($tipo){
		$sql = "DELETE FROM `sgis`.`tipo` WHERE `idtipo`='".$tipo."';";
		return $sql;
	}
	public function deleSla($sla){
		$sql = "DELETE FROM `sgis`.`sla` WHERE `idsla`='".$sla."';";
		return $sql;
	}
	public function delePrioridade($pdde){
		$sql = "DELETE FROM `sgis`.`prioridade` WHERE `idprioridade`='".$pdde."';";
		return $sql;
	}
	public function deleStatus($status){
		$sql = "DELETE FROM `sgis`.`status` WHERE `idstatus`='".$status."';";
		return $sql;
	}
	public function deleFluxoPai_id($fluxopai){
		$sql = "DELETE FROM `sgis`.`fluxograma_pai` WHERE `idfluxograma_pai`='".$fluxopai."';";
		return $sql;
	}
	public function deleJustificativa_id($justificativa){
		$sql = "DELETE FROM `sgis`.`justificativa` WHERE `idjustificativa`='".$justificativa."';";
		return $sql;
	}
	public function deleArquivoBiblioteca($biblioteca){
		$sql = "DELETE FROM `sgis`.`biblioteca` WHERE `idbiblioteca`='".$biblioteca."';";
		return $sql;
	}
	public function deleArquivoChecklist($checklist){
		$sql = "DELETE FROM `sgis`.`checklist` WHERE `idchecklist`='".$checklist."';";
		return $sql;
	}
	public function deleArquivo($arquivo){
		$sql = "DELETE FROM `sgis`.`arquivo` WHERE `idarquivo`='".$arquivo."';";
		return $sql;
	}
	public function deleChamadoPai($chmd){
		$sql = "DELETE FROM `sgis`.`chamado_pai` WHERE `idchamado`='".$chmd."';";
		return $sql;
	}
	public function deleChamadoFilho($chmdfilho){
		$sql = "DELETE FROM `sgis`.`chamado_filho` WHERE `idchamado_filho`='".$chmdfilho."';";
		return $sql;
	}
	public function deleChamadoApoio($chmdapoio){
		$sql = "DELETE FROM `sgis`.`chamado_apoio` WHERE `idchamado_apoio`='".$chmdapoio."';";
		return $sql;
	}
	public function deleteRetornoAgenda_manual(){
		$sql = "DELETE FROM agenda_diaria WHERE data_agenda=DATE_FORMAT(CURDATE()+INTERVAL 1 DAY,'%Y-%m-%d');";
		return $sql;
	}
	public function deleteRetornoAgendaApoio_manual(){
		$sql = "DELETE FROM agenda_apoio_diaria WHERE data_agenda=DATE_FORMAT(CURDATE()+INTERVAL 1 DAY,'%Y-%m-%d');";
		return $sql;
	}
	
	
	
	//Funções de Inserção
	public function inserUsuario($nome,$sbrm,$nivel,$m_gvt,$e_gvt,$m_tel,$e_tel,$cgo,$equipe){
		$sql = "INSERT INTO `sgis`.`usuario` VALUES (NULL, '".$nome."', '".$sbrm."', '".$nivel."', '".$m_gvt."', '".$e_gvt."', '".$m_tel."', '".$e_tel."', '".$cgo."', NOW( ), '".$equipe."');";
		return $sql;
	}
	public function inserControle($empresa,$equipe,$tipo,$controle){
		$sql = "INSERT INTO `sgis`.`controle` VALUES (NULL, '".$empresa."', '".$equipe."', '".$tipo."', '".$controle."');";
		return $sql;
	}
	public function inserPais($nome,$sigla){
		$sql = "INSERT INTO `sgis`.`pais` VALUES (NULL, '".$nome."', '".$sigla."');";
		return $sql;
	}
	public function inserEstado($nome, $uf, $pais){
		$sql = "INSERT INTO `sgis`.`estado` VALUES (NULL, '".$nome."', '".$uf."', (select idpais from `sgis`.`pais` where `pais` = '".$pais."'));";
		return $sql;
	}
	public function inserCidade($sigla, $nome, $estado){
		$sql = "INSERT INTO `sgis`.`cidade` VALUES (NULL, '".$sigla."', '".$nome."', (select idestado from `sgis`.`estado` where `estado` = '".$estado."'));";
		return $sql;
	}
	public function inserSetor($setor){
		$sql = "INSERT INTO `sgis`.`setor` VALUES (NULL, '".$setor."');";
		return $sql;
	}
	public function inserDepartamento($departamento,$setor){
		$sql = "INSERT INTO `sgis`.`departamento` VALUES (NULL, '".$departamento."', (select idsetor from `sgis`.`setor` where `nome_setor` = '".$setor."'));";
		return $sql;
	}
	public function inserEquipe($equipe,$departamento){
		$sql = "INSERT INTO `sgis`.`equipe` VALUES (NULL, '".$equipe."', (select iddepartamento from `sgis`.`departamento` where `nome_departamento` = '".$departamento."'));";
		return $sql;
	}
	public function inserTipo($tipo){
		$sql = "INSERT INTO `sgis`.`tipo` VALUES (NULL, '".$tipo."');";
		return $sql;
	}
	public function inserSla($sla_vencimento,$sla_entrega,$idequipe_sla,$idtipo){
		$sql = "INSERT INTO `sgis`.`sla` VALUES (NULL, '".$sla_vencimento."', '".$sla_entrega."', '".$idequipe_sla."', '".$idtipo."');";
		return $sql;
	}
	public function inserPrioridade($pdde){
		$sql = "INSERT INTO `sgis`.`prioridade` VALUES (NULL, '".$pdde."');";
		return $sql;
	}
	public function inserStatus($status,$tipo_chamado){
		$sql = "INSERT INTO `sgis`.`status` VALUES (NULL, '".$status."', '".$tipo_chamado."');";
		return $sql;
	}
	public function inserJustificativa($justificativa,$idstatus){
		$sql = "INSERT INTO `sgis`.`justificativa` VALUES (NULL, '".$justificativa."', '".$idstatus."');";
		return $sql;
	}
	public function inserArquivoChecklist($titulo, $p_chave, $descricao, $nome_arquivo,$caminho,$tamanho,$extensao,$autor,$idautor){
		$sql = "INSERT INTO `sgis`.`checklist` VALUES (NULL, '".$titulo."', '".$p_chave."', '".$descricao."', '".$nome_arquivo."', '".$caminho."', '".$tamanho."', '".$extensao."', '".$autor."', '".$idautor."');";
		return $sql;
	}
	public function inserArquivoBiblioteca($titulo, $p_chave, $descricao, $nome_arquivo,$caminho,$tamanho,$extensao,$autor,$idautor){
		$sql = "INSERT INTO `sgis`.`biblioteca` VALUES (NULL, '".$titulo."', '".$p_chave."', '".$descricao."', '".$nome_arquivo."', '".$caminho."', '".$tamanho."', '".$extensao."', '".$autor."', '".$idautor."');";
		return $sql;
	}
	public function inserArquivo($nome, $cmn, $tmn, $extensao,$matri,$chamadopai,$observacao_arq,$projeto){
		$sql = "INSERT INTO `sgis`.`arquivo` VALUES (NULL, '".$nome."', '".$cmn."', '".$tmn."', '".$extensao."', (select idusuario from `sgis`.`usuario` where `matricula_gvt` = '".$matri."' or `matricula_telefonica` = '".$matri."'), '".$chamadopai."', '".$observacao_arq."', NOW( ), '".$projeto."');";
		return $sql;
	}
	public function inserChamadoPai($chmd,$autor,$projt,$desc,$vecm,$data_abertura,$data_entrega_c_p,$l_m_pcp,$tipo,$idusuario,$prior,$cidd,$estado,$status,$idequipe){
		if(!empty($l_m_pcp)){$material = "'".$l_m_pcp."'";}else{$material = "NULL";};
		$sql = "INSERT INTO `sgis`.`chamado_pai` VALUES 
				(NULL, '".$chmd."', '".$autor."', '".$projt."', '".$desc."', '".$vecm."', NULL, '".$data_abertura."', NULL, '".$data_entrega_c_p."', ".$material.", '".$tipo."', '".$idusuario."', '".$prior."', (SELECT idcidade FROM `sgis`.`cidade` WHERE nome_cidade='".$cidd."' AND estado='".$estado."'), '".$status."', NULL, '".$idequipe."');";
		return $sql;
	}
	public function inserChamadoFilho($chamado,$resp_abertura,$equipe_apoio,$obs_c_f){
		$sql = "INSERT INTO `sgis`.`chamado_filho` VALUES (NULL, (SELECT idchamado FROM chamado_pai WHERE chamado='".$chamado."'), '".$resp_abertura."', '".$equipe_apoio."', '".$obs_c_f."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		return $sql;
	}
	public function inserChamadoFilho_FluxoPai($c_pai,$resp_abertura,$equipe_apoio,$obs_c_f){
		$sql = "INSERT INTO `sgis`.`chamado_filho` VALUES (NULL, (SELECT `idchamado` FROM `sgis`.`chamado_pai` WHERE chamado='".$c_pai."'), '".$resp_abertura."', '".$equipe_apoio."', '".$obs_c_f."', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		return $sql;
	}
	public function inserFluxoPai($nome_fluxo,$equipe_fluxo_pai,$tipo_fluxo_pai,$equipeapoio_fluxo,$observacao_fluxo){
		$sql = "INSERT INTO `sgis`.`fluxograma_pai` VALUES (NULL, '".$nome_fluxo."', '".$equipe_fluxo_pai."', '".$tipo_fluxo_pai."', '".$equipeapoio_fluxo."', '".$observacao_fluxo."');";
		return $sql;
	}
	public function inserChamadoApoio($c_filho,$e_solicitante,$resp_solic,$e_solicitada,$d_lmt_analise,$obs_apoio){
		$sql = "INSERT INTO `sgis`.`chamado_apoio` VALUES (NULL, '".$c_filho."', '".$e_solicitante."', '".$resp_solic."', '".$e_solicitada."', '".$d_lmt_analise."', '".$obs_apoio."', NULL, NULL, NULL, NULL);";
		return $sql;
	}
	
	//Funções de Atualização
	public function updateProduto($id,$prod,$des,$vlr){
		$up = "UPDATE `anjos_paes`.`produto` SET produto='".$prod."', descricao='".$des."', valor='".$vlr."' WHERE `idproduto`='".$id."';";
		return $up;
	}
	public function updateUsuario_dados($id,$nome,$sbrm,$matri,$email){
		$sql = "UPDATE `sgis`.`usuario` SET nome='".$nome."', sobrenome='".$sbrm."', matricula='".$matri."', email='".$email."'
				WHERE `idusuario`='".$id."';";
		return $sql;
	}
	public function updateUsuario_senha($id,$senha){
		$sql = "UPDATE `sgis`.`usuario` SET senha='".$senha."' WHERE `idusuario`='".$id."';";
		return $sql;
	}
	public function updateUsuario_nivel($id,$nivel){
		$sql = "UPDATE `sgis`.`usuario` SET nivel='".$nivel."' WHERE `idusuario`='".$id."';";
		return $sql;
	}
	public function updatePais($id,$nome,$sigla){
		$sql = "UPDATE `sgis`.`pais` SET pais='".$nome."', sigla='".$sigla."' WHERE `idpais`='".$id."';";
		return $sql;
	}
	public function updateEstado($id,$nome, $uf){
		$sql = "UPDATE `sgis`.`estado` SET estado='".$nome."', uf='".$uf."'' WHERE `idestado`='".$id."';";
		return $sql;
	}
	public function updateCidade($id,$sigla, $nome){
		$sql = "UPDATE `sgis`.`cidade` SET sigla='".$sigla."', nome_cidade='".$nome."' WHERE `idcidade`='".$id."';";
		return $sql;
	}
	public function updateSetor($id,$setor){
		$sql = "UPDATE `sgis`.`setor` SET nome_setor='".$setor."' WHERE `idsetor`='".$id."';";
		return $sql;
	}
	public function updateDepartamento($id,$departamento){
		$sql = "UPDATE `sgis`.`departamento` SET nome_departamento='".$departamento."' WHERE `iddepartamento`='".$id."';";
		return $sql;
	}
	public function updateEquipe($id,$equipe){
		$sql = "UPDATE `sgis`.`equipe` SET nome_equipe='".$equipe."' WHERE `idequipe`='".$id."';";
		return $sql;
	}
	public function updateTipo($id,$tipo){
		$sql = "UPDATE `sgis`.`tipo` SET nome_tipo='".$tipo."' WHERE `idtipo`='".$id."';";
		return $sql;
	}
	public function updateSla($id,$sl_venci,$sla_entre){
		$sql = "UPDATE `sgis`.`sla` SET sla_vencimento='".$sl_venci."', sla_entrega='".$sla_entre."' WHERE `idsla`='".$id."';";
		return $sql;
	}
	public function updatePrioridade($id,$pdde){
		$sql = "UPDATE `sgis`.`prioridade` SET nome_prioridade='".$pdde."' WHERE `idprioridade`='".$id."';";
		return $sql;
	}
	public function updateStatus($id,$status){
		$sql = "UPDATE `sgis`.`status` SET  nome_status='".$status."' WHERE `idstatus`='".$id."';";
		return $sql;
	}
	public function updateControle($id,$controle){
		$sql = "UPDATE `sgis`.`controle` SET  ultimo_numero='".$controle."' WHERE `idcontrole`='".$id."';";
		return $sql;
	}
	public function updateJustificativa($id,$justificativa){
		$sql = "UPDATE `sgis`.`justificativa` SET  nome_justificativa='".$justificativa."' WHERE `idjustificativa`='".$id."';";
		return $sql;
	}
	public function updateArquivo($id,$nome, $cmn, $tmn, $extensao){
		$sql = "UPDATE `sgis`.`arquivo` SET nome='".$nome."', caminho='".$cmn."'
				, tamanho='".$tmn."', extensao='".$extensao."' WHERE `idarquivo`='".$id."';";
		return $sql;
	}
	public function updateArquivo_Caminho($id){
		$sql = "UPDATE `sgis`.`arquivo` SET caminho='deletado', observacao_arq='Arquivo removido em: ".date('d/m/Y H:i')."', projeto='NULL' WHERE `idarquivo`='".$id."';";
		return $sql;
	}
	public function updateChamadoPai($id,$autor,$n_projeto,$desc,$vencimento,$data_entrega_c_p,$histo,$l_m_pcp,$tipo,$matri,$prior,$cidd,$estado){
		if(empty($l_m_pcp)){$l_material = "NULL";}else{$l_material = "'".$l_m_pcp."'";};
		if(empty($data_entrega_c_p)){$data_entrega = "NULL";}else{$data_entrega = "'".$data_entrega_c_p."'";};
		$sql = "UPDATE `sgis`.`chamado_pai` SET 
				autor='".$autor."', nome_projeto='".$n_projeto."', descricao='".$desc."', vencimento='".$vencimento."', data_entrega_c_p=".$data_entrega.",historico='".$histo."', limitematerial_pcp=".$l_material.", tipo_c_p='".$tipo."', matricula_c_p='".$matri."', prioridade_c_p='".$prior."', cidade_c_p=(SELECT idcidade FROM `sgis`.`cidade` WHERE nome_cidade='".$cidd."' AND estado='".$estado."') WHERE `idchamado`='".$id."';";
		return $sql;
	}
	public function updateChamadoPai_Sts_Just($id,$histo,$status,$justificativa){
		$sql = "UPDATE `sgis`.`chamado_pai` SET 
				historico='".$histo."', status_c_p='".$status."', justificativa_c_p='".$justificativa."' WHERE `idchamado`='".$id."';";
		return $sql;
	}
	public function updateChamadoPai_DentrodoFilho($idpai,$histo,$d_aprovacao,$status,$justifi){
		if(empty($d_aprovacao)){$d_aprovacao = "NULL";}else{$d_aprovacao = "'".$d_aprovacao."'";};
		if(empty($l_m_pcp)){$l_material = "NULL";}else{$l_material = "'".$l_m_pcp."'";};
		$sql = "UPDATE `sgis`.`chamado_pai` SET 
				historico='".$histo."', data_aprovacao=".$d_aprovacao.", status_c_p='".$status."', justificativa_c_p='".$justifi."' WHERE `idchamado`='".$idpai."';";
		return $sql;
	}
	public function updateChamadoFilho_StsFilho($idfilho,$respon_c_f,$h_turno,$t_execucao,$hist_c_f){
		$sql = "UPDATE `sgis`.`chamado_filho` SET 
				responsavel_c_f='".$respon_c_f."', horario_turno='".$h_turno."', tempo_execucao='".$t_execucao."', historico_c_f='".$hist_c_f."', status_agenda_c_f=(SELECT idstatus FROM `sgis`.`status` WHERE nome_status='Agendar') WHERE `idchamado_filho`='".$idfilho."';";
		return $sql;
	}
	public function updateChamadoFilho_StsFilho2($idfilho,$respon_c_f){
		$sql = "UPDATE `sgis`.`chamado_filho` SET 
				responsavel_c_f='".$respon_c_f."' WHERE `idchamado_filho`='".$idfilho."';";
		return $sql;
	}
	public function updateMinhaFilaStatusFilho($idfilho,$status,$hist_c_f){
		$sql = "UPDATE `sgis`.`chamado_filho` SET historico_c_f='".$hist_c_f."', status_agenda_c_f='".$status."', data_resolucao=NULL WHERE `idchamado_filho`='".$idfilho."';";
		return $sql;
	}
	public function updateMinhaFilaStatusAgenda($idfilho,$status,$hist_c_f,$observacao){
		$sql = "UPDATE `sgis`.`chamado_filho` SET historico_c_f='".$hist_c_f."', status_agenda_c_f='".$status."', data_resolucao=NULL, observacao_c_f='".$observacao."' WHERE `idchamado_filho`='".$idfilho."';";
		return $sql;
	}
	public function updateChamadoFilho($id,$obs_c_f,$resp_c_f,$sts_agenda_c_f,$h_turno,$t_execucao,$histo_c_f,$data_reso){
		if(empty($data_reso))$data_reso = "NULL";else $data_reso = "'".$data_reso."'";
		$sql = "UPDATE `sgis`.`chamado_filho` SET observacao_c_f='".$obs_c_f."', responsavel_c_f='".$resp_c_f."', status_agenda_c_f='".$sts_agenda_c_f."', horario_turno='".$h_turno."', tempo_execucao='".$t_execucao."', historico_c_f='".$histo_c_f."', data_resolucao=".$data_reso." WHERE `idchamado_filho`='".$id."';";
		return $sql;
	}
	public function updateChamadoFilho_Encerrar($id,$sts_agenda_c_f,$histo_c_f,$data_reso){
		if(empty($data_reso))$data_reso = "NULL";else $data_reso = "'".$data_reso."'";
		$sql = "UPDATE `sgis`.`chamado_filho` SET status_agenda_c_f='".$sts_agenda_c_f."', historico_c_f='".$histo_c_f."', data_resolucao=".$data_reso." WHERE `idchamado_filho`='".$id."';";
		return $sql;
	}
	public function updateChamadoFilho_EncerrarAgenda($id,$sts_agenda_c_f,$histo_c_f,$data_reso,$observacao){
		if(empty($data_reso))$data_reso = "NULL";else $data_reso = "'".$data_reso."'";
		$sql = "UPDATE `sgis`.`chamado_filho` SET status_agenda_c_f='".$sts_agenda_c_f."', historico_c_f='".$histo_c_f."', data_resolucao=".$data_reso.", observacao_c_f='".$observacao."' WHERE `idchamado_filho`='".$id."';";
		return $sql;
	}
	public function updateChamadoFilho_Ordem($id,$ordem){
		$sql = "UPDATE `sgis`.`chamado_filho` SET ordem_agenda='".$ordem."' WHERE `idchamado_filho`='".$id."';";
		return $sql;
	}
	public function updateChamadoFilho_Agenda($id,$sts_agenda_c_f,$cronograma_pcp,$rdm,$rdm_status,$t_campo,$histo_c_f,$cronograma_pcp_bkp,$rdm_bkp,$rdm_status_bkp){
		if(empty($cronograma_pcp))$cronograma_pcp = "NULL";else $cronograma_pcp = "'".$cronograma_pcp."'";
		if(empty($cronograma_pcp_bkp))$cronograma_pcp_bkp = "NULL";else $cronograma_pcp_bkp = "'".$cronograma_pcp_bkp."'";
		$sql = "UPDATE `sgis`.`chamado_filho` SET status_agenda_c_f='".$sts_agenda_c_f."', cronograma_pcp=".$cronograma_pcp.", rdm='".$rdm."', rdm_status='".$rdm_status."', tecnico_campo='".$t_campo."', historico_c_f='".$histo_c_f."', cronograma_pcp_bkp=".$cronograma_pcp_bkp.", rdm_bkp='".$rdm_bkp."', rdm_status_bkp='".$rdm_status_bkp."' WHERE `idchamado_filho`='".$id."';";
		return $sql;
	}
	public function updateRetornoAgenda_manual(){
		$sql = "INSERT INTO agenda_diaria(chamado_id, data_agenda, rdm_agenda, status_rdm_agenda, tecnico_campo_agenda, status_agenda)
		SELECT DISTINCT chamado_pai, cronograma_pcp, rdm, rdm_status, tecnico_campo, nome_status FROM `sgis`.`chamado_filho` 
		JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus 
		WHERE cronograma_pcp=DATE_FORMAT(CURDATE()+INTERVAL 1 DAY,'%Y-%m-%d') AND `nome_status`!='Concluído' 
		ON DUPLICATE KEY UPDATE chamado_id=chamado_pai, data_agenda=cronograma_pcp, rdm_agenda=rdm, status_rdm_agenda=rdm_status, tecnico_campo_agenda=tecnico_campo, status_agenda=nome_status;";
		return $sql;
	}
	public function updateRetornoAgendaBkp_manual(){
		$sql = "INSERT INTO agenda_diaria(chamado_id, data_agenda, rdm_agenda, status_rdm_agenda, tecnico_campo_agenda, status_agenda)
		SELECT DISTINCT chamado_pai, cronograma_pcp_bkp, rdm_bkp, rdm_status_bkp, tecnico_campo, nome_status FROM `sgis`.`chamado_filho` 
		JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus 
		WHERE cronograma_pcp_bkp=DATE_FORMAT(CURDATE()+INTERVAL 1 DAY,'%Y-%m-%d') AND `nome_status`!='Concluído' 
		ON DUPLICATE KEY UPDATE chamado_id=chamado_pai, data_agenda=cronograma_pcp_bkp, rdm_agenda=rdm_bkp, status_rdm_agenda=rdm_status_bkp, tecnico_campo_agenda=tecnico_campo, status_agenda=nome_status;";
		return $sql;
	}
	public function updateRetornoAgendaApoio_manual(){
		$sql = "INSERT INTO agenda_apoio_diaria(chamadofilho_id, chamadoapoio_id, data_agenda, rdm_apoio, status_rdm_apoio, tecnico_campo_apoio, status_apoio)
		SELECT idchamado_filho, idchamado_apoio, cronograma_pcp, rdm, rdm_status, tecnico_campo, nome_status FROM `sgis`.`chamado_apoio` 
		LEFT JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho
		LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus 
		WHERE cronograma_pcp=DATE_FORMAT(CURDATE()+INTERVAL 1 DAY,'%Y-%m-%d') AND `nome_status`!='Concluído' 
		ON DUPLICATE KEY UPDATE chamadoapoio_id=idchamado_apoio, data_agenda=cronograma_pcp, rdm_apoio=rdm, status_rdm_apoio=rdm_status, tecnico_campo_apoio=tecnico_campo, status_apoio=nome_status;";
		return $sql;
	}
	public function updateRetornoAgendaBkpApoio_manual(){
		$sql = "INSERT INTO agenda_apoio_diaria(chamadofilho_id, chamadoapoio_id, data_agenda, rdm_apoio, status_rdm_apoio, tecnico_campo_apoio, status_apoio)
		SELECT idchamado_filho, idchamado_apoio, cronograma_pcp_bkp, rdm_bkp, rdm_status_bkp, tecnico_campo, nome_status FROM `sgis`.`chamado_apoio` 
		LEFT JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho
		LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus 
		WHERE cronograma_pcp_bkp=DATE_FORMAT(CURDATE()+INTERVAL 1 DAY,'%Y-%m-%d') AND `nome_status`!='Concluído' 
		ON DUPLICATE KEY UPDATE chamadoapoio_id=idchamado_apoio, data_agenda=cronograma_pcp_bkp, rdm_apoio=rdm_bkp, status_rdm_apoio=rdm_status_bkp, tecnico_campo_apoio=tecnico_campo, status_apoio=nome_status;";
		return $sql;
	}
	public function updateRetornoAgenda($id, $tecnico_c_a, $tecnico_e_a, $status_agenda, $logs_agenda, $justificativa, $observacao){
		$sql = "UPDATE `sgis`.`agenda_diaria` SET tecnico_campo_agenda='".$tecnico_c_a."', tecnico_executor_agenda='".$tecnico_e_a."', status_agenda='".$status_agenda."', logs_agenda='".$logs_agenda."', justificativa_agenda='".$justificativa."', observacao_agenda='".$observacao."' 
		WHERE `idagenda_diaria`='".$id."';";
		return $sql;
	}
	public function updateRetornoAgendaApoio($id, $tecnico_c_apoio, $tecnico_ex_apoio, $status_apoio, $logs_a_apoio, $just_apoio, $obs_ag_apoio){
		$sql = "UPDATE `sgis`.`agenda_apoio_diaria` SET tecnico_campo_apoio='".$tecnico_c_apoio."', tecnico_executor_apoio='".$tecnico_ex_apoio."', status_apoio='".$status_apoio."', logs_agenda_apoio='".$logs_a_apoio."', justificativa_apoio='".$just_apoio."', observacao_ag_apoio='".$obs_ag_apoio."' 
		WHERE `idagenda_apoio`='".$id."';";
		return $sql;
	}
	public function updateChamadoApoio($idapoio, $r_analise, $anls_chamado, $d_analise, $tp_exe_apoio, $obs_apoio){
		$sql = "UPDATE `sgis`.`chamado_apoio` SET responsavel_analise='".$r_analise."', analise_chamado='".$anls_chamado."', data_analise='".$d_analise."', tempo_exe_apoio='".$tp_exe_apoio."', observacao_apoio='".$obs_apoio."' WHERE `idchamado_apoio`='".$idapoio."';";
		return $sql;
	}
	
	//Funções de Busca
	public function valUsuario($matricula){
		$sql = "SELECT `idusuario` FROM `sgis`.`usuario` WHERE `matricula_gvt` = '".$matricula."' OR `matricula_telefonica` = '".$matricula."';";
		return $sql;
	}
	public function buscarUsuarios(){
		$sql = "SELECT * FROM `sgis`.`usuario` ORDER BY `nome` ASC;";
		return $sql;
	}
	public function buscarUsuario_Id_Mat($matricula){
		$sql = "SELECT `idusuario` FROM `sgis`.`usuario` WHERE `matricula_gvt` = '".$matricula."';";
		return $sql;
	}
	public function buscarUsuario_Id_Nome($nome){
		$sql = "SELECT idusuario, n_sobrenome FROM 
				(SELECT `idusuario` FROM `sgis`.`usuario`) as a
				JOIN
				(SELECT `idusuario` as `id`, CONCAT(`usuario`.`nome`, ' ', `usuario`.`sobrenome`) as `n_sobrenome` FROM `sgis`.`usuario`) as b
				ON a.idusuario=b.id
				WHERE `n_sobrenome` = '".$nome."';";
		return $sql;
	}
	public function buscarUsuarioNivel_id($id){
		$sql = "SELECT `nivel` FROM `sgis`.`usuario` WHERE `idusuario` = '".$id."';";
		return $sql;
	}
	public function buscarUsuarioNivel_Id_Sobrenome($sobrenome){
		$sql = "SELECT `id`, `nivel` FROM 
					(SELECT `idusuario`, `nivel` FROM `sgis`.`usuario`) as a
				LEFT JOIN
					(SELECT `idusuario` as `id`, CONCAT(`usuario`.`nome`, ' ', `usuario`.`sobrenome`) as `completo` FROM `sgis`.`usuario`) as b
				ON a.idusuario=b.id 
				WHERE `completo` LIKE '%".$sobrenome."%';";
		return $sql;
	}
	public function buscarUsuario_Nome_id($id){
		$sql = "SELECT `nome`, `sobrenome` FROM `sgis`.`usuario` WHERE `idusuario` = '".$id."';";
		return $sql;
	}
	public function buscarUsuario_Matricula($matricula){
		$sql = "SELECT * FROM `sgis`.`usuario` 
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe
				WHERE `matricula_gvt` = '".$matricula."' OR `matricula_telefonica` = '".$matricula."';";
		return $sql;
	}
	public function buscarUsuario_Equipe($equipe){
		$sql = "SELECT * FROM `sgis`.`usuario` 
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe
				WHERE `nome_equipe` = '".$equipe."'
				ORDER BY `nome` ASC;";
		return $sql;
	}
	public function buscarMeses(){
		$sql = "SELECT * FROM `sgis`.`meses` ORDER BY `idmeses` ASC;";
		return $sql;
	}
	public function buscarPais(){
		$sql = "SELECT * FROM `sgis`.`pais` ORDER BY `pais` ASC;";
		return $sql;
	}
	public function buscarPais_id($id){
		$sql = "SELECT * FROM `sgis`.`pais` WHERE idpais='".$id."';";
		return $sql;
	}
	public function buscarEstado(){
		$sql = "SELECT * FROM `sgis`.`estado` 
				JOIN `sgis`.`pais` ON estado.pais=pais.idpais
				ORDER BY `estado` ASC;";
		return $sql;
	}
	public function buscarEstado_id($id){
		$sql = "SELECT * FROM `sgis`.`estado` 
				JOIN `sgis`.`pais` ON estado.pais=pais.idpais
				WHERE idestado='".$id."';";
		return $sql;
	}
	public function buscarCidade(){
		$sql = "SELECT * FROM `sgis`.`cidade` 
				JOIN `sgis`.`estado` ON cidade.estado=estado.idestado
				ORDER BY `cidade` ASC;";
		return $sql;
	}
	public function buscarCidadeNome($cidade){
		$sql = "SELECT idcidade FROM `sgis`.`cidade` WHERE nome_cidade='".$cidade."';";
		return $sql;
	}
	public function buscarCidade_letra($letra){
		$sql = "SELECT * FROM `sgis`.`cidade` 
				JOIN `sgis`.`estado` ON cidade.estado=estado.idestado
				WHERE `nome_cidade` LIKE '".$letra."%';";
		return $sql;
	}
	public function buscarCidade_id($id){
		$sql = "SELECT * FROM `sgis`.`cidade` 
				JOIN `sgis`.`estado` ON cidade.estado=estado.idestado
				WHERE idestado='".$id."';";
		return $sql;
	}
	public function buscarJustificativa(){
		$sql = "SELECT * FROM `sgis`.`justificativa` ORDER BY `nome_justificativa` ASC;";
		return $sql;
	}
	public function buscarJustificativa_Status(){
		$sql = "SELECT * FROM `sgis`.`justificativa` 
				JOIN `sgis`.`status` ON justificativa.status_justificativa=status.idstatus 
				ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarJustificativa_Status_id($id){
		$sql = "SELECT * FROM `sgis`.`justificativa` 
				JOIN `sgis`.`status` ON justificativa.status_justificativa=status.idstatus
				WHERE `idstatus`='".$id."'
				ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarJustificativa_NomeStatus($nomestatus){
		$sql = "SELECT * FROM `sgis`.`justificativa` 
				JOIN `sgis`.`status` ON justificativa.status_justificativa=status.idstatus 
				Where `status_justificativa`=(SELECT idstatus FROM `sgis`.`status` WHERE `nome_status`='".$nomestatus."') 
				ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarJustificativa_id($id){
		$sql = "SELECT * FROM `sgis`.`justificativa` WHERE idjustificativa='".$id."';";
		return $sql;
	}
	public function buscarSetor(){
		$sql = "SELECT * FROM `sgis`.`setor` ORDER BY `nome_setor` ASC;";
		return $sql;
	}
	public function buscarSetor_id($id){
		$sql = "SELECT * FROM `sgis`.`setor` WHERE idsetor='".$id."';";
		return $sql;
	}
	public function buscarDepartamento(){
		$sql = "SELECT * FROM `sgis`.`departamento` ORDER BY `nome_departamento` ASC;";
		return $sql;
	}
	public function buscarDepartamento_id($id){
		$sql = "SELECT * FROM `sgis`.`departamento` 
				WHERE `iddepartamento`='".$id."' 
				ORDER BY `nome_departamento` ASC;";
		return $sql;
	}
	public function buscarDepartamento_Setor(){
		$sql = "SELECT * FROM `sgis`.`departamento` 
				JOIN `sgis`.`setor` ON departamento.setor=setor.idsetor 
				ORDER BY `nome_departamento` ASC;";
		return $sql;
	}
	public function buscarDepartamento_Setor_Id($idsetor){
		$sql = "SELECT * FROM `sgis`.`departamento` 
				JOIN `sgis`.`setor` ON departamento.setor=setor.idsetor 
				WHERE idsetor='".$idsetor."' 
				ORDER BY `nome_departamento` ASC;";
		return $sql;
	}
	public function buscarEquipe(){
		$sql = "SELECT * FROM `sgis`.`equipe` 
				JOIN `sgis`.`departamento` ON equipe.departamento=departamento.iddepartamento 
				JOIN `sgis`.`setor` ON departamento.setor=setor.idsetor 
				ORDER BY `nome_equipe` ASC;";
		return $sql;
	}
	public function buscarEquipe_id($id){
		$sql = "SELECT * FROM `sgis`.`equipe` 
				JOIN `sgis`.`departamento` ON equipe.departamento=departamento.iddepartamento 
				WHERE equipe.departamento='".$id."' 
				ORDER BY `nome_equipe` ASC;";
		return $sql;
	}
	public function buscarTipo(){
		$sql = "SELECT * FROM `sgis`.`tipo` ORDER BY `nome_tipo` ASC;";
		return $sql;
	}
	public function buscarTipo_id($id){
		$sql = "SELECT * FROM `sgis`.`tipo` WHERE idtipo='".$id."' ORDER BY `nome_tipo` ASC;";
		return $sql;
	}
	public function buscarSla(){
		$sql = "SELECT * FROM `sgis`.`sla` ORDER BY `nome_sla` ASC;";
		return $sql;
	}
	public function buscarSla_id($idsla,$equipe){
		$sql = "SELECT * FROM `sgis`.`sla` 
				WHERE idsla='".$idsla."' AND `idequipe_sla`='".$equipe."' ;";
		return $sql;
	}
	public function buscarSla_Tipo_id($idtipo,$equipe){
		$sql = "SELECT * FROM `sgis`.`sla` 
				JOIN `sgis`.`tipo` ON sla.tipo_idtipo=tipo.idtipo
				WHERE tipo_idtipo='".$idtipo."' AND `idequipe_sla`='".$equipe."';";
		return $sql;
	}
	public function buscarFluxoPai_Tipo_id($idtipo,$equipe){
		$sql = "SELECT * FROM `sgis`.`fluxograma_pai` 
				JOIN `sgis`.`tipo` ON fluxograma_pai.tipo_fluxo_pai=tipo.idtipo
				WHERE `tipo_fluxo_pai`='".$idtipo."' AND `equipe_fluxo_pai`='".$equipe."';";
		return $sql;
	}
	public function buscarControle(){
		$sql = "SELECT * FROM `sgis`.`controle` ORDER BY `empresa_controle` ASC;";
		return $sql;
	}
	public function buscarControle_Tipo($equipe){
		$sql = "SELECT * FROM `sgis`.`controle` 
				JOIN `sgis`.`equipe` ON controle.equipe_controle=equipe.idequipe 
				JOIN `sgis`.`tipo` ON controle.tipo_controle=tipo.idtipo 
				WHERE idequipe='".$equipe."' 
				ORDER BY `nome_tipo` ASC;";
		return $sql;
	}
	public function buscarControle_Tipo_Equipe($equipe,$tipo){
		$sql = "SELECT * FROM `sgis`.`controle` 
				JOIN `sgis`.`equipe` ON controle.equipe_controle=equipe.idequipe 
				JOIN `sgis`.`tipo` ON controle.tipo_controle=tipo.idtipo 
				WHERE idequipe='".$equipe."' AND idtipo='".$tipo."' 
				ORDER BY `nome_tipo` ASC;";
		return $sql;
	}
	public function buscarPrioridade(){
		$sql = "SELECT * FROM `sgis`.`prioridade` ORDER BY `nome_prioridade` ASC;";
		return $sql;
	}
	public function buscarPrioridade_id($id){
		$sql = "SELECT * FROM `sgis`.`prioridade` WHERE idprioridade='".$id." ORDER BY `nome_prioridade` ASC';";
		return $sql;
	}
	public function buscarStatus(){
		$sql = "SELECT * FROM `sgis`.`status` ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarStatus_PCP(){
		$sql = "SELECT * FROM `sgis`.`status` WHERE `nome_status`='Aguardando RDM' OR `nome_status`='Agendado' 
				OR `nome_status`='Agendar' OR `nome_status`='Reagendar' OR `nome_status`='Aguardando Material' 
				ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarStatus_Retorno(){
		$sql = "SELECT * FROM `sgis`.`status` WHERE `nome_status`='Abortada' OR `nome_status`='Agendado' 
				OR `nome_status`='Executada' OR `nome_status`='Reagendar' OR `nome_status`='Executada Parcialmente' 
				ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarStatus_Reagendar(){
		$sql = "SELECT * FROM `sgis`.`status` WHERE `nome_status`='Concluído' OR `nome_status`='Concluído com erro' OR `nome_status`='Reagendar' 
				ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarStatus_Encerrado(){
		$sql = "SELECT * FROM `sgis`.`status` WHERE `nome_status`='Cancelado' OR `nome_status`='Reprovado' 
				ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarStatus_Validacao(){
		$sql = "SELECT * FROM `sgis`.`status` WHERE `nome_status`='Concluído' OR `nome_status`='Reagendar' 
				OR `nome_status`='Agendado' ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarStatus_Aprovar(){
		$sql = "SELECT * FROM `sgis`.`status` WHERE `nome_status`='Aguardando aprovação' OR `nome_status`='Aprovado' 
			OR `nome_status`='Reprovado' ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarStatus_Pai(){
		$sql = "SELECT * FROM `sgis`.`status` WHERE tipo_chamado='pai' ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarStatus_Filho(){
		$sql = "SELECT * FROM `sgis`.`status` WHERE tipo_chamado='filho' ORDER BY `nome_status` ASC;";
		return $sql;
	}
	public function buscarStatus_Cancelado(){
		$sql = "SELECT * FROM `sgis`.`status` WHERE nome_status='Cancelado';";
		return $sql;
	}
	public function buscarStatus_Reabrir(){
		$sql = "SELECT * FROM `sgis`.`status` WHERE nome_status='Aguardando aprovação';";
		return $sql;
	}
	public function buscarStatus_id($id){
		$sql = "SELECT nome_status FROM `sgis`.`status` WHERE idstatus='".$id."';";
		return $sql;
	}
	public function buscarStatus_Nome($nome){
		$sql = "SELECT idstatus FROM `sgis`.`status` WHERE nome_status='".$nome."';";
		return $sql;
	}
	public function buscarMaterial_Filtro($material){
		$sql = "SELECT * FROM `sgis`.`biblioteca`
				WHERE titulo LIKE '%".$material."%' OR palavra_chave LIKE '%".$material."%' OR descricao LIKE '%".$material."%' OR autor LIKE '%".$material."%' 
				ORDER BY `titulo` ASC;";
		return $sql;
	}
	public function buscarChecklist_Filtro($checklist){
		$sql = "SELECT * FROM `sgis`.`checklist`
				WHERE titulo LIKE '%".$checklist."%' OR palavra_chave LIKE '%".$checklist."%' OR descricao LIKE '%".$checklist."%' OR autor LIKE '%".$checklist."%' 
				ORDER BY `titulo` ASC;";
		return $sql;
	}
	public function buscarArquivo(){
		$sql = "SELECT * FROM `sgis`.`arquivo` ORDER BY `nome_arquivo` ASC;";
		return $sql;
	}
	public function buscarArquivo_id($id){
		$sql = "SELECT * FROM `sgis`.`arquivo` WHERE idarquivo='".$id."' ORDER BY `nome_arquivo` ASC;";
		return $sql;
	}
	public function buscarArquivo_matri($matricula){
		$sql = "SELECT * FROM `sgis`.`arquivo` 
				JOIN `sgis`.`usuario` ON arquivo.matricula_arquivo=usuario.idusuario 
				WHERE matricula_gvt='".$matricula."' 
				ORDER BY `nome_arquivo` ASC;";
		return $sql;
	}
	public function buscarArquivo_chamado($chamado){
		$sql = "SELECT * FROM `sgis`.`arquivo` 
				JOIN `sgis`.`chamado_pai` ON arquivo.arquivo_chamado=chamado_pai.chamado
				JOIN `sgis`.`usuario` ON arquivo.matricula_arquivo=usuario.idusuario 
				WHERE arquivo_chamado='".$chamado."' OR idchamado='".$chamado."' 
				ORDER BY `nome_arquivo` ASC;";
		return $sql;
	}
	public function buscarArquivo_ChamadoFilho($chamado){
		$sql = "SELECT * FROM `sgis`.`arquivo` 
				JOIN `sgis`.`chamado_pai` ON arquivo.arquivo_chamado=chamado_pai.chamado
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai
				JOIN `sgis`.`usuario` ON arquivo.matricula_arquivo=usuario.idusuario 
				WHERE chamado_pai=(SELECT idchamado FROM chamado_pai WHERE chamado='".$chamado."') 
				ORDER BY `nome_arquivo` ASC;";
		return $sql;
	}
	public function buscarChamadoPai($chamado){
		$sql = "SELECT * FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo
				JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				WHERE chamado='".$chamado."';";
		return $sql;
	}
	public function buscarChamadoPai_id($chamado){
		$sql = "SELECT * FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo
				JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe
				LEFT JOIN `sgis`.`justificativa` ON chamado_pai.justificativa_c_p=justificativa.idjustificativa
				WHERE idchamado='".$chamado."' OR chamado='".$chamado."' 
				ORDER BY `chamado` ASC;";
		return $sql;
	}
	public function buscarAgenda_Equipe_Data($equipe,$dia){
		$sql = "SELECT Distinct * FROM`sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo 
				JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario 
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade 
                JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe 
				LEFT  JOIN `sgis`.`arquivo` ON chamado_pai.chamado=arquivo.arquivo_chamado	
				WHERE `nome_equipe`='".$equipe."' AND `cronograma_pcp`='".$dia."' AND `nome_status`!='Concluído' AND `projeto`='1' OR `nome_equipe`='".$equipe."' AND `cronograma_pcp_bkp`='".$dia."' AND `nome_status`!='Concluído' AND `projeto`='1' 
				ORDER BY `ordem_agenda` ASC;";
		return $sql;
	}
	public function buscarAgenda_Equipe_Data_Validacao($equipe,$dia){
		$sql = "SELECT Distinct * FROM`sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo 
				JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario 
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade 
                JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				LEFT  JOIN `sgis`.`arquivo` ON chamado_pai.chamado=arquivo.arquivo_chamado				
				WHERE `nome_equipe`='".$equipe."' AND `cronograma_pcp`='".$dia."' AND `projeto`='1' 
				OR `nome_equipe`='".$equipe."' AND `cronograma_pcp_bkp`='".$dia."' AND `nome_status`!='Concluído' AND `projeto`='1' 
				ORDER BY `ordem_agenda` ASC;";
		return $sql;
	}
	public function buscarAgenda_Equipe_Data_Passado($equipe,$dia){
		$sql = "SELECT Distinct * FROM `sgis`.`agenda_diaria`
				JOIN `sgis`.`chamado_pai` ON agenda_diaria.chamado_id=chamado_pai.idchamado
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai
				LEFT JOIN `sgis`.`arquivo` ON chamado_pai.chamado=arquivo.arquivo_chamado
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				WHERE `nome_equipe`='".$equipe."' AND `data_agenda`='".$dia."' AND `projeto`='1' 
				ORDER BY `ordem_agenda` ASC;";
		return $sql;
	}
	public function buscarFluxogramaPai_Equipe($nome_equipe){
		$sql = "SELECT * FROM 
				(SELECT idfluxograma_pai, nome_equipe as equipe_solicitante FROM `sgis`.`fluxograma_pai`
				 JOIN `sgis`.`equipe` ON fluxograma_pai.equipe_fluxo_pai=equipe.idequipe) as a
				 JOIN
				(SELECT idfluxograma_pai as idapoio, nome_equipe as equipe_apoio, nome_fluxograma, nome_tipo, observacao_fluxo FROM `sgis`.`fluxograma_pai`
				 JOIN `sgis`.`equipe` ON fluxograma_pai.equipeapoio_fluxo=equipe.idequipe
				 JOIN `sgis`.`tipo` ON fluxograma_pai.tipo_fluxo_pai=tipo.idtipo) as b
				 ON a.idfluxograma_pai=b.idapoio
				WHERE equipe_solicitante='".$nome_equipe."';";
		return $sql;
	}
	public function buscarChamadoPai_Matri($matricula){
		$sql = "SELECT * FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`tipo` ON chamado_pai.tipo=tipo.idtipo
				JOIN `sgis`.`usuario` ON chamado_pai.matricula=usuario.idusuario
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade=prioridade.idprioridade
				JOIN `sgis`.`status` ON chamado_pai.status=status.idstatus
				JOIN `sgis`.`cidade` ON chamado_pai.cidade=cidade.idcidade
				WHERE `matricula_gvt` = '".$matricula."';";
			return $sql;
	}
	public function buscarChamadoPai_M_Status($matricula,$coluna,$ordem){
		$sql = "SELECT * FROM
				(SELECT * FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai 
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo 
				JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario 
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus 
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade 
				JOIN `sgis`.`arquivo` ON chamado_pai.chamado=arquivo.arquivo_chamado
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe) as a
				JOIN
				(SELECT `chamado_pai` as `idch`, `nome_status` as `status_filho` FROM `sgis`.`chamado_filho` 
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as b
				ON a.idchamado=b.idch
                WHERE `matricula_gvt` = '".$matricula."' AND `status_filho` NOT LIKE '%Concluído%' AND `nome_status`='Aguardando aprovação' AND `projeto`='1' 
				OR `matricula_gvt` = '".$matricula."' AND `status_filho` NOT LIKE '%Concluído%' AND `nome_status`='Aprovado' AND `projeto`='1' 
				OR `matricula_gvt` = '".$matricula."' AND `nome_status`='Aguardando aprovação' AND `projeto`='1' ";
				if(!empty($coluna) AND !empty($ordem)){$sql .= 'ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= "ORDER BY `vencimento` ASC;";}
			return $sql;
	}
	public function buscarChamadoPai_Equipe($equipe,$coluna,$ordem){
		$sql = "SELECT * FROM
				(SELECT * FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai 
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo 
				JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario 
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus 
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade 
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe) as a
				JOIN
				(SELECT `chamado_pai` as `idch`, `nome_status` as `status_filho` FROM `sgis`.`chamado_filho` 
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as b
				ON a.idchamado=b.idch
                WHERE `nome_equipe`='".$equipe."' AND `status_filho`!='Concluído' AND `nome_status`='Aguardando aprovação'  
				OR `nome_equipe`='".$equipe."' AND `status_filho`!='Concluído' AND `nome_status`='Aprovado' 
				OR `nome_equipe`='".$equipe."' AND `nome_status`='Aguardando aprovação'" ;
				if(!empty($coluna) AND !empty($ordem)){$sql .= 'ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= "ORDER BY vencimento ASC;";}
		return $sql;
	}
	public function buscarChamadoFilho_Equipe_PCP($equipe,$coluna,$ordem){
		$sql = "SELECT * FROM 
				(SELECT * FROM`sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo
				JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe) as a
				JOIN
				(SELECT idchamado_filho as idfilho, nome_status as statuspai FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus) as b
				ON a.idchamado_filho=b.idfilho
                WHERE `nome_equipe`='".$equipe."' AND `statuspai`='Aprovado' AND `nome_status`='Aguardando Material' 
				OR `nome_equipe`='".$equipe."' AND `statuspai`='Aprovado' AND `nome_status`='Agendar' 
				OR `nome_equipe`='".$equipe."' AND `statuspai`='Aprovado' AND `nome_status`='Reagendar' 
				OR `nome_equipe`='".$equipe."' AND `statuspai`='Aprovado' AND `nome_status`='Aguardando RDM'" ;
				if(!empty($coluna) AND !empty($ordem)){$sql .= 'ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= "ORDER BY data_entrega_c_p AND nome_status ASC;";}
		return $sql;
	}
	public function buscarChamadoPai_Filtro($vencimento,$status,$tipo,$prio,$cidade,$d_abertura,$lmtmtr_pcp,$autor,$nome_proj,$equipe_apoio){
		$sql = "SELECT * FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo
				JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade ";
		if(!empty($vencimento)){$ct=1; $sql .= 'WHERE `vencimento`=\''.$vencimento.'\' ';};
		if(!empty($status))if($ct!=1){ $ct=1; $sql .= 'WHERE `status_c_p`=\''.$status.'\' ';}else{$sql .= 'AND `status_c_p`=\''.$status.'\' ';}
		if(!empty($tipo))if($ct!=1){ $ct=1; $sql .= 'WHERE `tipo_c_p`=\''.$tipo.'\' ';}else{$sql .= 'AND `tipo_c_p`=\''.$tipo.'\' ';}
		if(!empty($prio))if($ct!=1){ $ct=1; $sql .= 'WHERE `prioridade_c_p`=\''.$prio.'\' ';}else{$sql .= 'AND `prioridade_c_p`=\''.$prio.'\' ';}
		if(!empty($cidade))if($ct!=1){ $ct=1; $sql .= 'WHERE `cidade_c_p`=\''.$cidade.'\' ';}else{$sql .= 'AND `cidade_c_p`=\''.$cidade.'\' ';}
		if(!empty($d_abertura))if($ct!=1){ $ct=1; $sql .= 'WHERE `data_abertura`=\''.$d_abertura.'\' ';}else{$sql .= 'AND `data_abertura`=\''.$d_abertura.'\' ';}
		if(!empty($lmtmtr_pcp))if($ct!=1){ $ct=1; $sql .= 'WHERE `limitematerial_pcp`=\''.$lmtmtr_pcp.'\' ';}else{$sql .= 'AND `limitematerial_pcp`=\''.$lmtmtr_pcp.'\' ';}
		if(!empty($autor))if($ct!=1){ $ct=1; $sql .= 'WHERE `autor` LIKE \'%'.$autor.'%\' ';}else{$sql .= 'AND `autor`=\''.$autor.'\' ';}
		if(!empty($nome_proj))if($ct!=1){ $ct=1; $sql .= 'WHERE `nome_projeto` LIKE \'%'.$nome_proj.'%\' ';}else{$sql .= 'AND `nome_projeto` LIKE \'%'.$nome_proj.'%\' ';}
		if(!empty($equipe_apoio))if($ct!=1){ $ct=1; $sql .= 'WHERE `nome_equipe`=\''.$equipe_apoio.'\' ';}else{$sql .= 'AND `nome_equipe`=\''.$equipe_apoio.'\' ';}
		$sql .=	'ORDER BY `chamado` ASC;';
		if($ct!=1){return '0';}else{return $sql;}
	}
	public function buscarChamadoPai_Tempo_Execu($id){
		$sql = "SELECT tempo_execucao FROM sgis.chamad WHERE matricula='".$id."';";
		return $sql;
	}
	public function buscarChamadoFilho(){
		$sql = "SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				JOIN `sgis`.`usuario` ON chamado_filho.matricula_c_f=usuario.idusuario
				ORDER BY `chamado` ASC;";
		return $sql;
	}
	public function buscarChamadoFilho_id($id){
		$sql = "SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				WHERE idchamado_filho='".$id."';";
		return $sql;
	}
	public function buscarChamadoFilho_CPai($chamado_pai){
		$sql = "SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				LEFT JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo
				JOIN `sgis`.`departamento` ON equipe.departamento=departamento.iddepartamento
				WHERE chamado_pai=(SELECT idchamado FROM chamado_pai WHERE chamado='".$chamado_pai."');";
		return $sql;
	}
	public function buscarChamadoFilho_CPai_idPai($chamado){
		$sql = "SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				LEFT JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo
				JOIN `sgis`.`departamento` ON equipe.departamento=departamento.iddepartamento
				WHERE chamado_pai=(SELECT idchamado FROM chamado_pai WHERE chamado='".$chamado."');";
		return $sql;
	}
	public function buscarChamadoFilho_M_Status($idusuario,$coluna,$ordem){
		$sql = "SELECT * FROM 
				(SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo
				JOIN `sgis`.`arquivo` ON chamado_pai.chamado=arquivo.arquivo_chamado
				JOIN `sgis`.`departamento` ON equipe.departamento=departamento.iddepartamento) as a
				JOIN
				(SELECT idchamado_filho as idfilho, nome_status as statuspai, nome_equipe as requisitante  FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus
				JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe) as b
				ON a.idchamado_filho=b.idfilho
				WHERE `responsavel_c_f` = '".$idusuario."' AND `nome_status` NOT LIKE '%Concluído%' AND `statuspai`!='Cancelado' AND `statuspai`!='Reprovado' AND `projeto`='1' ";
				if(!empty($coluna) AND !empty($ordem)){$sql .= 'ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= "ORDER BY `data_entrega_c_p` ASC;";}
			return $sql;
	}
	public function buscarChamadoFilho_Equipe($equipe,$coluna,$ordem){
		$sql = "SELECT DISTINCT * FROM
				(SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado 
				LEFT JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as a
				JOIN
				(SELECT idchamado,nome_prioridade, nome_equipe as requisitante, nome_cidade, nome_status as status_pai FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
                JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe) as b
				ON a.chamado_pai=b.idchamado
				WHERE nome_equipe='".$equipe."' AND nome_status!='Concluído' AND status_pai!='Cancelado' AND status_pai!='Reprovado' OR nome_equipe='".$equipe."' AND nome_status IS NULL AND status_pai!='Cancelado' AND status_pai!='Reprovado'";
				if(!empty($coluna) AND !empty($ordem)){$sql .= ' ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= ' ORDER BY `status_pai` ASC;';}
		return $sql;
	}
	public function buscarChamadoFilho_Equipe_Aguardando($equipe,$coluna,$ordem){
		$sql = "SELECT DISTINCT * FROM
				(SELECT * FROM `sgis`.`chamado_filho` 
				LEFT JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado 
				LEFT JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as a
				JOIN
				(SELECT idchamado,nome_prioridade, caminho, projeto, nome_equipe as requisitante, nome_cidade, nome_status as status_pai FROM `sgis`.`chamado_pai` 
				LEFT JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
                JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario 
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade 
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus 
				JOIN `sgis`.`arquivo` ON chamado_pai.chamado=arquivo.arquivo_chamado 
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe) as b
				ON a.chamado_pai=b.idchamado
				WHERE nome_equipe='".$equipe."' AND status_pai='Aguardando aprovação' AND `projeto`='1' ";
				if(!empty($coluna) AND !empty($ordem)){$sql .= ' ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= ' ORDER BY `vencimento` ASC;';}
		return $sql;
	}
	public function buscarChamadoFilho_Equipe_Aprovado($equipe,$coluna,$ordem){
		$sql = "SELECT DISTINCT * FROM
				(SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado 
				LEFT JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as a
				JOIN
				(SELECT idchamado,nome_prioridade, caminho, projeto, nome_equipe as requisitante, nome_cidade, nome_status as status_pai FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai 
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
                JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario 
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus 
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade 
				LEFT  JOIN `sgis`.`arquivo` ON chamado_pai.chamado=arquivo.arquivo_chamado 
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe) as b
				ON a.chamado_pai=b.idchamado
				WHERE nome_equipe='".$equipe."' AND status_pai='Aprovado' AND nome_status NOT LIKE '%Concluído%' AND `projeto`='1' ";
				if(!empty($coluna) AND !empty($ordem)){$sql .= ' ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= ' ORDER BY `data_entrega_c_p` ASC;';}
		return $sql;
	}
	public function buscarChamadoFilho_Equipe_Encerrado($equipe,$coluna,$ordem){
		$sql = "SELECT DISTINCT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado 
				JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe 
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus 
				LEFT  JOIN `sgis`.`arquivo` ON chamado_pai.chamado=arquivo.arquivo_chamado 
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade 
				WHERE nome_equipe='".$equipe."' AND nome_status LIKE '%Concluído%' AND `projeto`='1' ";
				if(!empty($coluna) AND !empty($ordem)){$sql .= ' ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= ' ORDER BY `data_resolucao` DESC;';}
		return $sql;
	}
	public function buscarChamadoPai_Equipe_Encerrado($equipe,$coluna,$ordem){
		$sql = "SELECT DISTINCT * FROM `sgis`.`chamado_pai` 
				LEFT JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai 
                JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario 
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus 
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade 
				LEFT  JOIN `sgis`.`arquivo` ON chamado_pai.chamado=arquivo.arquivo_chamado 
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe 
				WHERE nome_equipe='".$equipe."' AND nome_status='Cancelado' 
				OR nome_equipe='".$equipe."' AND nome_status='Reprovado' ";
				if(!empty($coluna) AND !empty($ordem)){$sql .= ' ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= ' ORDER BY `data_resolucao` DESC;';}
		return $sql;
	}
	public function buscarChamadoFilho_Equipe_CancelRepro($equipe,$coluna,$ordem){
		$sql = "SELECT DISTINCT * FROM
				(SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado 
				LEFT JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as a
				JOIN
				(SELECT idchamado,nome_prioridade, nome_equipe as requisitante, nome_cidade, idstatus as idstatus_pai, nome_status as status_pai FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai 
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
                JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario 
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus 
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade 
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe) as b
				ON a.chamado_pai=b.idchamado 
				WHERE nome_equipe='".$equipe."' AND status_pai='Cancelado' 
				OR nome_equipe='".$equipe."' AND status_pai='Reprovado' ";
				if(!empty($coluna) AND !empty($ordem)){$sql .= ' ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= ' ORDER BY `data_resolucao` DESC;';}
		return $sql;
	}
	public function buscarAgendaApoio_Equipe_Data($equipe,$dia){
		$sql = "SELECT DISTINCT * FROM
				(SELECT `idchamado_apoio`, `idchamado`, `chamado`, `autor`, `nome_projeto`, `nome_cidade`, `rdm`,  `rdm_status`,  `horario_turno`, `tecnico_campo`, `nome_tipo`, `cronograma_pcp`, `nome_status`, `nome_prioridade`, `idchamado_filho`, `data_lmt_analise`, `observacao_apoio`, `responsavel_analise`, `analise_chamado`, `data_analise`, `tempo_exe_apoio`, `idequipe` as `id_solicitada`, `nome_equipe` as `e_solicitada`, CONCAT(`usuario`.`nome`, ' ', `usuario`.`sobrenome`) as `r_analise`, `caminho`, `projeto` FROM `sgis`.`chamado_apoio` 
				JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`arquivo` ON chamado_pai.chamado=arquivo.arquivo_chamado
				LEFT JOIN `sgis`.`usuario` ON chamado_apoio.responsavel_analise=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_apoio.equipe_solicitada=equipe.idequipe) as a
				JOIN
				(SELECT `idchamado_apoio` as `idapoio`, `tempo_exe_apoio`, `chamado_filho_apoio`, `idusuario`, CONCAT(`usuario`.`nome`, ' ', `usuario`.`sobrenome`) as `solicitante`, `nome_equipe` as `e_rqtante` FROM `sgis`.`chamado_apoio` 
				JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho
				JOIN `sgis`.`usuario` ON chamado_apoio.resp_solicitacao=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_apoio.equipe_solicitante=equipe.idequipe) as b
				ON a.idchamado_apoio=b.idapoio
				WHERE `e_solicitada`='".$equipe."' AND cronograma_pcp='".$dia."' AND `projeto`='1' 
				ORDER BY `nome_tipo` ASC;";
		return $sql;
	}
	public function buscarAgendaApoio_Equipe_Data_Passado($equipe,$dia){
		$sql = "SELECT * FROM
				(SELECT * FROM `sgis`.`agenda_apoio_diaria` 
				JOIN `sgis`.`chamado_filho` ON agenda_apoio_diaria.chamadofilho_id=chamado_filho.idchamado_filho 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado 
				JOIN `sgis`.`chamado_apoio` ON agenda_apoio_diaria.chamadoapoio_id=chamado_apoio.idchamado_apoio 
				JOIN `sgis`.`usuario` ON chamado_apoio.resp_solicitacao=usuario.idusuario 
				JOIN `sgis`.`equipe` ON chamado_apoio.equipe_solicitante=equipe.idequipe 
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade 
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo 
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus 
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade) as a 
				JOIN
				(SELECT DISTINCT idchamado_apoio as idapoio, nome_equipe as e_requisitada FROM `sgis`.`chamado_apoio`  
				JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho 
				LEFT JOIN `sgis`.`usuario` ON chamado_apoio.responsavel_analise=usuario.idusuario 
				JOIN `sgis`.`equipe` ON chamado_apoio.equipe_solicitada=equipe.idequipe) as b 
				ON a.idchamado_apoio=b.idapoio 
				WHERE `e_requisitada`='".$equipe."' AND `data_agenda`='".$dia."' 
				ORDER BY `ordem_agenda` ASC;";
		return $sql;
	}
	public function buscarChamadoFilho_Filtro($d_entrega,$status,$tipo,$prio,$cidade,$d_resol,$analista,$nome_proj,$equipe_apoio){
		$sql = "SELECT idchamado_filho, chamado, nome_projeto, nome_equipe, requisitante, nome_tipo, nome_status, r_analise FROM
				(SELECT chamado_pai, idchamado_filho, nome_status, data_resolucao, nome_equipe, CONCAT(`nome`, ' ',`sobrenome`) as r_analise FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado 
				JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as a
				JOIN
				(SELECT DISTINCT idchamado, chamado, nome_projeto, nome_tipo, prioridade_c_p, data_entrega_c_p, nome_cidade, nome_equipe as requisitante FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai
				JOIN `sgis`.`usuario` ON chamado_pai.matricula_c_p=usuario.idusuario
				JOIN `sgis`.`equipe` ON usuario.equipe=equipe.idequipe
				JOIN `sgis`.`tipo` ON chamado_pai.tipo_c_p=tipo.idtipo
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade) as b
				ON a.chamado_pai=b.idchamado ";
		if(!empty($d_entrega)){$ct=1; $sql .= 'WHERE `data_entrega_c_p`=\''.$d_entrega.'\' ';};
		if(!empty($status))if($ct!=1){ $ct=1; $sql .= 'WHERE `nome_status`=\''.$status.'\' ';}else{$sql .= 'AND `nome_status`=\''.$status.'\' ';}
		if(!empty($tipo))if($ct!=1){ $ct=1; $sql .= 'WHERE `nome_tipo`=\''.$tipo.'\' ';}else{$sql .= 'AND `nome_tipo`=\''.$tipo.'\' ';}
		if(!empty($prio))if($ct!=1){ $ct=1; $sql .= 'WHERE `prioridade_c_p`=\''.$prio.'\' ';}else{$sql .= 'AND `prioridade_c_p`=\''.$prio.'\' ';}
		if(!empty($cidade))if($ct!=1){ $ct=1; $sql .= 'WHERE `nome_cidade` LIKE \'%'.$cidade.'%\' ';}else{$sql .= 'AND `nome_cidade` LIKE \'%'.$cidade.'%\' ';}
		if(!empty($d_resol))if($ct!=1){ $ct=1; $sql .= 'WHERE `data_resolucao`=\''.$d_resol.'\' ';}else{$sql .= 'AND `data_resolucao`=\''.$d_resol.'\' ';}
		if(!empty($analista))if($ct!=1){ $ct=1; $sql .= 'WHERE `r_analise` LIKE \'%'.$analista.'%\' ';}else{$sql .= 'AND `r_analise` LIKE \'%'.$analista.'%\' ';}
		if(!empty($nome_proj))if($ct!=1){ $ct=1; $sql .= 'WHERE `nome_projeto` LIKE \'%'.$nome_proj.'%\' ';}else{$sql .= 'AND `nome_projeto` LIKE \'%'.$nome_proj.'%\' ';}
		if(!empty($equipe_apoio))if($ct!=1){ $ct=1; $sql .= 'WHERE `nome_equipe`=\''.$equipe_apoio.'\' ';}else{$sql .= 'AND `nome_equipe`=\''.$equipe_apoio.'\' ';}
		$sql .=	'ORDER BY `chamado` ASC;';
		if($ct!=1){return '0';}else{return $sql;}
	}
	public function buscarChamadoApoio_CFilho_idFilho($chamado){
		$sql = "SELECT DISTINCT * FROM 
				(SELECT `idchamado_apoio`, `idchamado`, `chamado`, `idchamado_filho`, `data_lmt_analise`, `observacao_apoio`, `responsavel_analise`, `analise_chamado`, `data_analise`, `tempo_exe_apoio`, `idequipe` as `id_solicitada`, `nome_equipe` as `e_solicitada`, CONCAT(`usuario`.`nome`, ' ', `usuario`.`sobrenome`) as `r_analise` FROM `sgis`.`chamado_apoio` 
				JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				LEFT JOIN `sgis`.`usuario` ON chamado_apoio.responsavel_analise=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_apoio.equipe_solicitada=equipe.idequipe) as a
				JOIN
				(SELECT `idchamado_apoio` as `idapoio`, `chamado_filho_apoio`, `idusuario`, CONCAT(`usuario`.`nome`, ' ', `usuario`.`sobrenome`) as `solicitante`, `nome_equipe` as `e_rqtante` FROM `sgis`.`chamado_apoio` 
				JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho
				JOIN `sgis`.`usuario` ON chamado_apoio.resp_solicitacao=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_apoio.equipe_solicitante=equipe.idequipe) as b
				ON a.idchamado_apoio=b.idapoio
				WHERE idchamado =(SELECT idchamado FROM chamado_pai WHERE chamado='".$chamado."');";
		return $sql;
	}
	public function buscarChamadoApoio_CFilho_idApoio($chamadoapoio){
		$sql = "SELECT DISTINCT * FROM 
				(SELECT `idchamado_apoio`, `idchamado`, `chamado`, `idchamado_filho`, `data_lmt_analise`, `observacao_apoio`, `responsavel_analise`, `analise_chamado`, `data_analise`, `tempo_exe_apoio`, `idequipe` as `id_solicitada`, `nome_equipe` as `e_solicitada`, CONCAT(`usuario`.`nome`, ' ', `usuario`.`sobrenome`) as `r_analise` FROM `sgis`.`chamado_apoio` 
				JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				LEFT JOIN `sgis`.`usuario` ON chamado_apoio.responsavel_analise=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_apoio.equipe_solicitada=equipe.idequipe) as a
				JOIN
				(SELECT `idchamado_apoio` as `idapoio`, `chamado_filho_apoio`, `idusuario`, CONCAT(`usuario`.`nome`, ' ', `usuario`.`sobrenome`) as `solicitante`, `nome_equipe` as `e_rqtante` FROM `sgis`.`chamado_apoio` 
				JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho
				JOIN `sgis`.`usuario` ON chamado_apoio.resp_solicitacao=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_apoio.equipe_solicitante=equipe.idequipe) as b
				ON a.idchamado_apoio=b.idapoio
				WHERE idchamado_apoio='".$chamadoapoio."';";
		return $sql;
	}
	public function buscarChamadoApoio_id($id){
		$sql = "SELECT * FROM `sgis`.`chamado_apoio` 
				JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho
				WHERE idchamado_apoio='".$id."';";
		return $sql;
	}
	public function buscarChamadoApoio_Equipe($equipe,$coluna,$ordem){
		$sql = "SELECT DISTINCT * FROM
				(SELECT `idchamado_apoio`, `idchamado`, `chamado`, `autor`, `cronograma_pcp`, `nome_status`, `nome_prioridade`, `idchamado_filho`, `data_lmt_analise`, `observacao_apoio`, `responsavel_analise`, `analise_chamado`, `data_analise`, `tempo_exe_apoio`, `idequipe` as `id_solicitada`, `nome_equipe` as `e_solicitada`, CONCAT(`usuario`.`nome`, ' ', `usuario`.`sobrenome`) as `r_analise` FROM `sgis`.`chamado_apoio` 
				JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus
				JOIN `sgis`.`prioridade` ON chamado_pai.prioridade_c_p=prioridade.idprioridade
				LEFT JOIN `sgis`.`usuario` ON chamado_apoio.responsavel_analise=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_apoio.equipe_solicitada=equipe.idequipe) as a
				JOIN
				(SELECT `idchamado_apoio` as `idapoio`, `chamado_filho_apoio`, `idusuario`, CONCAT(`usuario`.`nome`, ' ', `usuario`.`sobrenome`) as `solicitante`, `nome_equipe` as `e_rqtante`, `nome_status` as `status_filho` FROM `sgis`.`chamado_apoio` 
				JOIN `sgis`.`chamado_filho` ON chamado_apoio.chamado_filho_apoio=chamado_filho.idchamado_filho
				JOIN `sgis`.`usuario` ON chamado_apoio.resp_solicitacao=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_apoio.equipe_solicitante=equipe.idequipe
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as b
				ON a.idchamado_apoio=b.idapoio
				WHERE e_solicitada='".$equipe."' AND status_filho!='Concluído' AND nome_status!='Cancelado' AND nome_status!='Reprovado'";
				if(!empty($coluna) AND !empty($ordem)){$sql .= 'ORDER BY '.$coluna.' '.$ordem.';';}
				else{$sql .= ' ORDER BY `data_lmt_analise` ASC;';}
		return $sql;
	}
	public function buscarChamado_SLA_Aprovado($equipe){
		$sql = "SELECT DISTINCT * FROM
				(SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado 
				LEFT JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as a
				JOIN
				(SELECT idchamado_filho as idfilho, nome_cidade, nome_status as status_pai FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus) as b
				ON a.idchamado_filho=b.idfilho
				WHERE nome_equipe='".$equipe."' AND nome_status NOT LIKE '%Concluído%' AND status_pai='Aprovado' OR nome_equipe='".$equipe."' AND nome_status IS NULL AND status_pai='Aprovado'
				ORDER BY `data_aprovacao`";
		return $sql;
	}
	public function buscarChamado_SLA_Aprovado_Usuario($usuario){
		$sql = "SELECT DISTINCT * FROM
				(SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado 
				LEFT JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as a
				JOIN
				(SELECT idchamado_filho as idfilho, nome_cidade, nome_status as status_pai FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus) as b
				ON a.idchamado_filho=b.idfilho
				WHERE responsavel_c_f='".$usuario."' AND nome_status NOT LIKE '%Concluído%' AND status_pai='Aprovado' OR responsavel_c_f='".$usuario."' AND nome_status IS NULL AND status_pai='Aprovado'
				ORDER BY `data_aprovacao`";
		return $sql;
	}
	public function buscarChamado_SLA_Aguardando($equipe){
		$sql = "SELECT DISTINCT * FROM
				(SELECT * FROM `sgis`.`chamado_filho` 
				JOIN `sgis`.`chamado_pai` ON chamado_filho.chamado_pai=chamado_pai.idchamado 
				LEFT JOIN `sgis`.`usuario` ON chamado_filho.responsavel_c_f=usuario.idusuario
				JOIN `sgis`.`equipe` ON chamado_filho.equipe_apoio=equipe.idequipe
				LEFT JOIN `sgis`.`status` ON chamado_filho.status_agenda_c_f=status.idstatus) as a
				JOIN
				(SELECT idchamado_filho as idfilho, nome_cidade, nome_status as status_pai FROM `sgis`.`chamado_pai` 
				JOIN `sgis`.`chamado_filho` ON chamado_pai.idchamado=chamado_filho.chamado_pai
				JOIN `sgis`.`cidade` ON chamado_pai.cidade_c_p=cidade.idcidade
				JOIN `sgis`.`status` ON chamado_pai.status_c_p=status.idstatus) as b
				ON a.idchamado_filho=b.idfilho
				WHERE nome_equipe='".$equipe."' AND nome_status NOT LIKE '%Concluído%' AND status_pai='Aguardando aprovação' OR nome_equipe='".$equipe."' AND nome_status IS NULL AND status_pai='Aguardando aprovação'
				ORDER BY `data_aprovacao`";
		return $sql;
	}
	
}

?>
