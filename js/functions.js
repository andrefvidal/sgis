//Junta as tabelas;
(function($) {
	margetable = function() {
	$('#tabelaCheck > tbody:last').append($('#tabelaPre> tbody').html());
	$('#tabelaCheck > tbody:last').append($('#tabelaAtividade> tbody').html());
	$('#tabelaCheck > tbody:last').append($('#tabelaPos> tbody').html());

	};
})(jQuery);

//Download da tabela
(function($) {
	downloadtable = function() {	


	var chamado = document.getElementById('chamado').value;
	var projeto = document.getElementById('projeto').value;
	var data = moment(document.getElementById('data').value, "YYYY-MM-DD").format("DD/MM/YYYY");
	var chamadoprojeto = chamado + "-" + projeto;

	document.getElementById('campochamado').innerHTML = chamado;
	document.getElementById('campoprojeto').innerHTML = projeto; 
	document.getElementById('chamadoprojeto').innerHTML = chamadoprojeto; 
	document.getElementById('campodata').innerHTML = data;

	if ( chamado == "" ){
		alert("Insira o número do chamado!")
	}else if ( projeto == "" ) {
		alert("Insira o nome do projeto!")
	}else if( data == "Invalid date"){
		alert("Insira a data de execução!")
	}else{

		//Remove as células com o Botão REMOVER		
				$(document).ready(function() {
					$('#tabelaCheck .linha td.colunaBtn').each(function() {						
    				$(this).remove();
   					});
				});


		//Download do arquivo com o numero do chamado e nome do projeto
		$('<a />').attr({download: 'CheckList-'+document.getElementById('chamado').value+'-'+document.getElementById('projeto').value +'.html',href: "data:text/html," + $('#tabela').html() })[0].click()

		//Limpa a tabela
				var div = document.getElementById('tbodyTabela');
				div.innerHTML = "";    
	} 
};




})(jQuery);
(function($) { 
	downloadCheck = function() {
		//Número de linhas da tabela
		var count = $('#tabelaCheck tr.linha').length; 
		//Altera as células com as colunas STATUS para valor selecionado	
					
		for(var i = 0; i<300; i++){		

				var campo = "status"+i; 

				//Verifica se o campo/linha existe
				if (document.getElementById(campo)!=null){

					var valor = document.getElementById(campo).value;
					var campoTexto = "texto"+i;
					var valorTexto = document.getElementById(campoTexto).value;

					//Define o valor da célula celStatus conforme seleção OK ou NOK
					$(document).ready(function() {
						$("#tabelaCheck .linha td#celStatus"+i+"").each(function() {
    					$(this).html(valor);
   						});
   					//Alterar a cor de fundo da célula conforme status selecionado
   						if(valor == "OK"){
   						 document.getElementById("celStatus"+i+"").style.backgroundColor = 'lightseagreen';
   						}else if (valor == "NOK"){
   						document.getElementById("celStatus"+i+"").style.backgroundColor = 'tomato';
   						}
					});
				//Define o valor da célula celTexto conforme texto escrito = Coluna "Observação"
					$(document).ready(function() {
							$("#tabelaCheck .linha td#celTexto"+i+"").each(function() {
    						$(this).html("<pre style='width:100%;white-space: pre-line;' >"+valorTexto+"</pre>");
   							});
						});
				}
		}
			//Download do arquivo com o numero do chamado e nome do projeto
			$('<a />').attr({download: 'Retorno-CheckList_'+document.getElementById('chamadoprojeto').innerHTML+'.html',href: "data:text/html," + $('#conteudo').html() })[0].click()  
	};

})(jQuery);
// Adicionar / Remover linhas da tabela
var status = 0;
var celStatus = 0;
var texto = 0;
var celTexto = 0;

(function($) {	

//Adiciona linha	
AddTableRow = function(it, tabela) { 
	var newRow = $("<tr class='linha'>"); 
	var cols = "";
	//Célula na coluna ITEM conforme texto escrito no input
	cols += '<td style="font-size: 16px;" class="col-md-6">'+it+'</td>';
	//Célula na coluna STATUS incluindo as opções OK e NOK
	cols += '<td align="center"class="col-md-1" id="celStatus'+(celStatus++)+'"><select class="form-control" id="status'+(status++)+'"><option selected value=""></option><option value="OK">OK</option><option value="NOK">NOK</option></select></td>'; 
	//Célula na coluna OBSERVAÇÃO com campo de texto
	cols += '<td id="celTexto'+(celTexto++)+'"><textarea class="form-control" rows="2" id="texto'+(texto++)+'"></textarea></td>';	
	//Célula com o botão REMOVER
	cols += '<td class="colunaBtn" id="celBtn" style="font-size: 16px ;"><button type="button"class="btn btn-danger"onclick="remove(this)"  >Remover</button</td></tr>'; 

	newRow.append(cols);
	$(tabela).append(newRow);
	return false;
};
})(jQuery);
//Remove linha
(function($) {
remove = function(item) {
	var tr = $(item).closest('tr');
	tr.fadeOut(100, function() {
		tr.remove();  

	});
	return false;
}
})(jQuery);
//Fim função para a tabela



//Função para atualizar as informações conforme combobox "atividade"
/*window.onload = function(){

document.getElementById('atividade').onchange = function(){


	var divPre = document.getElementById('tablePre');
	var divAtiv = document.getElementById('tableAtividade');
	var divPos = document.getElementById('tablePos');
	var atividade = document.getElementById('atividade').value; 
	var equips = document.getElementById('equips');
 		
	//Opção caso seja necessário especificar o equipamento
	var equipdados = "<input name='equipamento' type='radio'id='cisco' value='cisco'><label for='cisco'>Cisco</label><br><input name='equipamento'type='radio' id='juniper' value='juniper'><label for='juniper'>Juniper</label><br><input name='equipamento' type='radio' id='extreme' value='extreme'><label for='extreme'>Extreme</label><br>";

	//A cada nova opção gera uma nova tabela nas divs "tablePre", "tableAtividade" e "tablePos"
	var tabelaPre =  "<table id='tabelaPre' class='table table-bordered table-hover table-sortable'><tbody><tr><td style='font-size: 15px;background-color:royalblue; color: white' colspan='4'><strong>Pré Atividade</strong></td></tr></tbody></table>";  

	var tabelaAtividade = "<table id='tabelaAtividade' class='table table-bordered table-hover table-sortable'><tbody><tr><td style='font-size: 15px; background-color:royalblue; color: white'  colspan='4'><strong>Atividade:</strong></td></tr></tbody></table>";

	var tabelaPos = "<table id='tabelaPos' class='table table-bordered table-hover table-sortable'><tbody><tr><td style='font-size: 15px;background-color:royalblue; color: white' colspan='4'><strong>Pós Atividade:</strong></td></tr></tbody></table>"; 		 

	//Verifica a atividade selecionada e insere as linhas do checklist
	if (atividade == 'intplacas' ){		

		//Insere insere os radiobutton  para equipamentos na div "equips"
		var equips = document.getElementById('equips');
		equips.innerHTML = equipdados;		

		$('#equips input').click( function() {
			divPre.innerHTML = tabelaPre;
			divAtiv.innerHTML = tabelaAtividade;
			divPos.innerHTML = tabelaPos;

			if ($(this).val() == 'cisco'){
				//Seleciona a tabelaPre onde serão adicionados os itens;
				var tabelaPre = document.getElementById('tabelaPre');

				AddTableRow('Verificar status das placas atuais ( show platform )' , tabelaPre);
				AddTableRow('Verificar status das interfaces ( show interfaces description)', tabelaPre);
				AddTableRow('Habilitar o monitoramento do equipamento (terminal monitor)', tabelaPre);
				AddTableRow('Solicitar ao colaborador da cidade que verifique se a placa está apta para ser inserida no equipamento', tabelaPre);
				AddTableRow('Inserir a placa no slot indicado do projeto', tabelaPre);

			}else if($(this).val() == 'juniper'){
				var tabelaPre = document.getElementById('tabelaPre');
				AddTableRow('<strong>PRÉ-ATIVIDADE</strong>' , tabelaPre);
				AddTableRow('Verificar status das placas atuais ( show chassis hardware )', tabelaPre);
				AddTableRow('Verificar status das interfaces ( show interfaces description)', tabelaPre);
				AddTableRow('Habilitar o monitoramento do equipamento (monitor start messages )', tabelaPre);
				AddTableRow('Solicitar ao colaborador da cidade que verifique se a placa está apta para ser inserida no equipamento.', tabelaPre);
				AddTableRow('Inserir a placa no slot indicado do projeto.', tabelaPre);
			}else if($(this).val() == 'extreme'){
				AddTableRow('EXTREME', tabelaPre); 
			}				
			
			
		});


		//Verifica a atividade selecionada e insere as linhas do checklist
		}else if 
		(atividade == 'miggoogle' ){

			//Identifica a tabela onde os itens serão adicionados
			divPre.innerHTML = tabelaPre;
			divAtiv.innerHTML = tabelaAtividade;
			divPos.innerHTML = tabelaPos;
			equips.innerHTML = "";
			var tabelaPre = document.getElementById('tabelaPre');
			var tabelaAtividade = document.getElementById('tabelaAtividade');
			var tabelaPos = document.getElementById('tabelaPos');

			//Inserindo na tabela Pré atividade	
			AddTableRow("Executar teste de conectividade ( PING ) nos servidores GOOGLE.", tabelaPre);
			AddTableRow("Coletar informações do CRICKET das interfaces que serão migradas.", tabelaPre);			

			//Inserindo na tabela Atividade	
			AddTableRow("Remover configurações de BE do RDIST.", tabelaAtividade);
			AddTableRow("Aplicar configurações de BAGG(SW-CDNCACHE) e SHARING(SW-GOOGLE).",tabelaAtividade);
			AddTableRow("Aplicar endereço IP na VLAN do SW-CDNCACHE conforme endereço IP que estava no RDIST.", tabelaAtividade);			
			AddTableRow("Configurar a VLAN utilizada pelos servidores GOOGLE como trunk/tagged na conexão SW-CDNCACHE<>SW-GOOGLE", tabelaAtividade);
			AddTableRow("Realizar migração das interfaces.", tabelaAtividade);	

			//Inserindo na tabela Pós Atividade
			AddTableRow("Executar teste de conectividade ( PING ) nos servidores GOOGLE.", tabelaPos);
			AddTableRow("Verificar tráfego no CRICKET", tabelaPos);
			AddTableRow("Em caso de falha consultar este <a href='file://10.200.17.5/Dados/Procedimentos/atividade_miggoogle.pdf'>ARQUIVO</a>.", tabelaPos);
		}
		//Verifica a atividade selecionada e insere as linhas do checklist
		else if 
			(atividade == 'mignetflix' ){
				
			//Identifica a tabela onde os itens serão adicionados
			divPre.innerHTML = tabelaPre;
			divAtiv.innerHTML = tabelaAtividade;
			divPos.innerHTML = tabelaPos;
			equips.innerHTML = "";
			var tabelaPre = document.getElementById('tabelaPre');
			var tabelaAtividade = document.getElementById('tabelaAtividade');
			var tabelaPos = document.getElementById('tabelaPos');

			//Inserindo na tabela Pré atividade	
			AddTableRow("Executar teste de conectividade ( PING ) nos servidores GOOGLE.", tabelaPre);
			AddTableRow("Coletar informações do CRICKET das interfaces que serão migradas.", tabelaPre);
			
			//Inserindo na tabela Atividade	
			AddTableRow("Remover configurações de BE do RDIST.", tabelaAtividade);
			AddTableRow("Aplicar configurações de BAGG(SW-CDNCACHE) e SHARING(SW-GOOGLE).",tabelaAtividade);
			AddTableRow("Aplicar endereço IP na VLAN do SW-CDNCACHE conforme endereço IP que estava no RDIST.", tabelaAtividade);			
			AddTableRow("Configurar a VLAN utilizada pelos servidores GOOGLE como trunk/tagged na conexão SW-CDNCACHE<>SW-GOOGLE", tabelaAtividade);
			AddTableRow("Realizar migração das interfaces.", tabelaAtividade);	

			//Inserindo na tabela Pós Atividade
			AddTableRow("Executar teste de conectividade ( PING ) nos servidores GOOGLE.", tabelaPos);
			AddTableRow("Verificar tráfego no CRICKET", tabelaPos);
			}

			//Verifica a atividade selecionada e insere as linhas do checklist
			else if 
			(atividade == 'amplt' ){
				
			//Identifica a tabela onde os itens serão adicionados
			divPre.innerHTML = tabelaPre;
			divAtiv.innerHTML = tabelaAtividade;
			divPos.innerHTML = tabelaPos;
			equips.innerHTML = "";
			var tabelaPre = document.getElementById('tabelaPre');
			var tabelaAtividade = document.getElementById('tabelaAtividade');
			var tabelaPos = document.getElementById('tabelaPos');

			//Inserindo na tabela Pré atividade	
			
			
			
			//Inserindo na tabela Atividade	
			
			

			//Inserindo na tabela Pós Atividade
			
			
			}
		}
		
			
		


};*/







