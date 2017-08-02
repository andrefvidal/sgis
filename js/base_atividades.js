
window.onload = function(){
document.getElementById('atividade').onchange = function(){

function tabelas(){
	//Identifica as DIVs com as tabelas
	var divPre = document.getElementById('tablePre');
    var divAtiv = document.getElementById('tableAtividade'); 
	var divPos = document.getElementById('tablePos');

	//A cada nova opção gera uma nova tabela nas divs "tablePre", "tableAtividade" e "tablePos"
	var tabelaPre =  "<table id='tabelaPre' class='table table-bordered table-hover table-sortable'><tbody><tr><td style='font-size: 15px;background-color:royalblue; color: white' colspan='4'><strong>Pré Atividade</strong></td></tr></tbody></table>";  

	var tabelaAtividade = "<table id='tabelaAtividade' class='table table-bordered table-hover table-sortable'><tbody><tr><td style='font-size: 15px; background-color:royalblue; color: white'  colspan='4'><strong>Atividade:</strong></td></tr></tbody></table>";

	var tabelaPos = "<table id='tabelaPos' class='table table-bordered table-hover table-sortable'><tbody><tr><td style='font-size: 15px;background-color:royalblue; color: white' colspan='4'><strong>Pós Atividade:</strong></td></tr></tbody></table>"; 

	//Insere as tabelas dentro das DIVs
	divPre.innerHTML = "";
	divAtiv.innerHTML = ""; 
	divPos.innerHTML = "";
	divPre.innerHTML = tabelaPre;
	divAtiv.innerHTML = tabelaAtividade; 
	divPos.innerHTML = tabelaPos;
}

function tb(tabela){
	//Identifica a tabela onde será inserido o ITEM
	if (tabela == "tabelaPre"){
		return document.getElementById('tabelaPre');
	}else if(tabela == "tabelaAtividade"){
		return document.getElementById('tabelaAtividade');
	}else if(tabela == "tabelaPos"){
		return document.getElementById('tabelaPos');
	}
}	

//Opção caso seja necessário especificar o equipamento
var equipdados = "<input name='equipamento' type='radio'id='cisco' value='cisco'><label for='cisco'>Cisco</label><br><input name='equipamento'type='radio' id='juniper' value='juniper'><label for='juniper'>Juniper</label><br><input name='equipamento' type='radio' id='extreme' value='extreme'><label for='extreme'>Extreme</label><br>";
	
var atividade = document.getElementById('atividade').value; 
var equips = document.getElementById('equips'); 		
	 

//Verifica a atividade selecionada e insere as linhas do checklist
if (atividade == 'intplacas' ){	
	//Insere insere os radiobutton  para equipamentos na div "equips"
		var equips = document.getElementById('equips');
		equips.innerHTML = equipdados;
			//Seleciona os ITENS conforme tipo de equipamento
			$('#equips input').click( function() {				
				if ($(this).val() == 'cisco'){
				tabelas();
				
				//Inserindo na tabela Pré atividade	
				AddTableRow("Coletar configuração atual do equipamento ( show running-config ).", tb("tabelaPre"));
				AddTableRow("Verificar status das placas atuais do equipamento ( show platform / show inventory ).", tb("tabelaPre"));	
				AddTableRow("Verificar status da interfaces ( show interfaces description ).", tb("tabelaPre"));
				AddTableRow("Verificar versão do equipamento ( show version ).", tb("tabelaPre"));
				AddTableRow("Habilitar o monitoramento do equipamento ( terminal monitor ).", tb("tabelaPre"));
				AddTableRow("Solicitar ao colaborador local que verifique se a placa está sem avarias e apta para ser inserida no equipamento.", tb("tabelaPre"));

				//Inserindo na tabela Atividade	
				AddTableRow("Inserir a placa no slot indicado.", tb("tabelaAtividade"));
				AddTableRow("Aguardar a inicialização da placa até que esteja operacional.",tb("tabelaAtividade"));
				AddTableRow("Inserir transceivers nas posições indicadas.",tb("tabelaAtividade"));


				//Inserindo na tabela Pós Atividade
				AddTableRow("Verificar status das placas do equipamento.", tb("tabelaPos"));
				AddTableRow("Verificar status da interfaces.", tb("tabelaPos"));
				AddTableRow("Verificar se transceivers (SR/LR) foram inseridos nas posições corretas ( show inventory ).", tb("tabelaPos"));
				AddTableRow("Salvar logs da atividade e enviar no retorno da agenda DADOS.", tb("tabelaPos"));

				}else if($(this).val() == 'juniper'){
				tabelas();


				//Inserindo na tabela Pré atividade	
				AddTableRow("Coletar configuração atual do equipamento ( show configuration ).", tb("tabelaPre"));
				AddTableRow("Verificar status das placas atuais do equipamento ( show chassis fpc pic-status ).", tb("tabelaPre"));
				AddTableRow("Verificar status da interfaces ( show interfaces descriptions ).", tb("tabelaPre"));
				AddTableRow("Verificar versão do equipamento ( show version ).", tb("tabelaPre"));
				AddTableRow("Habilitar o monitoramento do equipamento ( monitor start messages ).", tb("tabelaPre"));
				AddTableRow("Solicitar ao colaborador local que verifique se a placa está sem avarias e apta para ser inserida no equipamento.", tb("tabelaPre"));


				//Inserindo na tabela Atividade	
				AddTableRow("Inserir a placa no slot indicado.", tb("tabelaAtividade"));
				AddTableRow("Inicializar placa no slot instalado (request chassis fpc slot X online).", tb("tabelaAtividade"));
				AddTableRow("Aguardar a inicialização da placa até que esteja operacional.", tb("tabelaAtividade"));
				AddTableRow("Inserir transceivers nas posições indicadas.", tb("tabelaAtividade"));


				//Inserindo na tabela Pós Atividade
				AddTableRow("Verificar status das placas do equipamento.", tb("tabelaPos"));
				AddTableRow("Verificar status da interfaces.Verificar se transceivers (SR/LR) foram inseridos nas posições corretas ( show chassis hardware ).", tb("tabelaPos"));
				AddTableRow("Salvar logs da atividade e enviar no retorno da agenda DADOS.", tb("tabelaPos"));			
			

				}else if($(this).val() == 'extreme'){
				tabelas();

				//Inserindo na tabela Pré atividade	
				AddTableRow("Coletar configuração atual do equipamento ( show configuration ).", tb("tabelaPre"));
				AddTableRow("Verificar status das placas atuais do equipamento ( show slot ).", tb("tabelaPre"));
				AddTableRow("Verificar status da interfaces ( show ports no-refresh ).", tb("tabelaPre"));
				AddTableRow("Verificar versão do equipamento ( show switch ).", tb("tabelaPre"));
				AddTableRow("Habilitar o monitoramento do equipamento ( enable log display ).", tb("tabelaPre"));
				AddTableRow("Solicitar ao colaborador local que verifique se a placa está sem avarias e apta para ser inserida no equipamento.", tb("tabelaPre"));

				//Inserindo na tabela Atividade	
				AddTableRow("Inserir a placa no slot indicado.", tb("tabelaAtividade"));
				AddTableRow("Aguardar a inicialização da placa até que esteja operacional.", tb("tabelaAtividade"));
				AddTableRow("Inserir transceivers nas posições indicadas.", tb("tabelaAtividade"));

				//Inserindo na tabela Pós Atividade
				AddTableRow("Verificar status das placas do equipamento.", tb("tabelaPos"));
				AddTableRow("Verificar status da interfaces.", tb("tabelaPos"));
				AddTableRow("Verificar se transceivers (SR/LR) foram inseridos nas posições corretas ( show ports configuration no-refresh  ).", tb("tabelaPos"));
				AddTableRow("Salvar logs da atividade e enviar no retorno da agenda DADOS.	", tb("tabelaPos"));		
								
				}	


			});	

		
			
}else if (atividade == 'miggoogle' ){

			//Identifica a tabela onde os itens serão adicionados
			tabelas();
			equips.innerHTML = "";

			//Inserindo na tabela Pré atividade	
			AddTableRow("Executar teste de conectividade ( PING ) nos servidores GOOGLE.", tb("tabelaPre"));
			AddTableRow("Coletar informações do CRICKET das interfaces que serão migradas.", tb("tabelaPre"));			

			//Inserindo na tabela Atividade	
			AddTableRow("Remover configurações de BE do RDIST.", tb("tabelaAtividade"));
			AddTableRow("Aplicar configurações de BAGG(SW-CDNCACHE) e SHARING(SW-GOOGLE).",tb("tabelaAtividade"));
			AddTableRow("Aplicar endereço IP na VLAN do SW-CDNCACHE conforme endereço IP que estava no RDIST.", tb("tabelaAtividade"));			
			AddTableRow("Configurar a VLAN utilizada pelos servidores GOOGLE como trunk/tagged na conexão SW-CDNCACHE<>SW-GOOGLE", tb("tabelaAtividade"));
			AddTableRow("Realizar migração das interfaces.", tb("tabelaAtividade"));	

			//Inserindo na tabela Pós Atividade
			AddTableRow("Executar teste de conectividade ( PING ) nos servidores GOOGLE.", tb("tabelaPos"));
			AddTableRow("Verificar tráfego no CRICKET", tb("tabelaPos"));
			AddTableRow("Em caso de falha consultar este <a href='file://10.200.17.5/Dados/Procedimentos/atividade_miggoogle.pdf'>ARQUIVO</a>.", tb("tabelaPos"));

}else if (atividade == 'mignetflix' ){

			//Identifica a tabela onde os itens serão adicionados
			tabelas();
			equips.innerHTML = "";

			//Inserindo na tabela Pré atividade	
			AddTableRow("PRE", tb("tabelaPre"));
						

			//Inserindo na tabela Atividade	
			AddTableRow("ATIV", tb("tabelaAtividade"));
			

			//Inserindo na tabela Pós Atividade
			AddTableRow("POS", tb("tabelaPos"));
			
}else if (atividade == 'amplt' ){

			//Identifica a tabela onde os itens serão adicionados
			tabelas();
			equips.innerHTML = "";

			//Inserindo na tabela Pré atividade	
			AddTableRow("PRE", tb("tabelaPre"));
						

			//Inserindo na tabela Atividade	
			AddTableRow("ATIV", tb("tabelaAtividade"));
			

			//Inserindo na tabela Pós Atividade
			AddTableRow("POS", tb("tabelaPos"));
}






/*
########### Nova atividade #############

else if (atividade == 'xxxx' ){

			//Identifica a tabela onde os itens serão adicionados
			tabelas();
			equips.innerHTML = "";

			//Inserindo na tabela Pré atividade	
			AddTableRow("PRE", tb("tabelaPre"));
						

			//Inserindo na tabela Atividade	
			AddTableRow("ATIV", tb("tabelaAtividade"));
			

			//Inserindo na tabela Pós Atividade
			AddTableRow("POS", tb("tabelaPos"));
}
#########################################


FIM*/
}

};