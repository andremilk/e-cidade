<?
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2013  DBselller Servicos de Informatica             
 *                            www.dbseller.com.br                     
 *                         e-cidade@dbseller.com.br                   
 *                                                                    
 *  Este programa e software livre; voce pode redistribui-lo e/ou     
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a versao 2 da      
 *  Licenca como (a seu criterio) qualquer versao mais nova.          
 *                                                                    
 *  Este programa e distribuido na expectativa de ser util, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU     
 *  junto com este programa; se nao, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licenca no diretorio licenca/licenca_en.txt 
 *                                licenca/licenca_pt.txt 
 */

require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("dbforms/db_classesgenericas.php");

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$aux								= new cl_arquivo_auxiliar;
$cliframe_seleciona = new cl_iframe_seleciona;

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC>
<form class="container" name="form1" method="post" action="jur2_inicialvara002.php" >
	<fieldset>
	  <legend>Relat�rios - Iniciais - Vara</legend>
		<table class="form-container">
		  <tr> 
			  <td>
          Op��es
        </td>
        <td>
					<?
						$aCondicao = array("com"=>"Com os Varas selecionados","sem"=>"Sem os Varas selecionadas");
						db_select("ver",$aCondicao,true,1,"");
					?>
			  </td>
		  </tr>
      <tr> 
        <td>
          Ordem:
        </td>
        <td>
	     	  <?
	     	 	  $aOrdem = array("i"=>"Inicial","f"=>"Foro","a"=>"Advogado","s"=>"Situa��o");
	     	 	  db_select("selOrdem",$aOrdem,true,1,"");
	     	  ?>
        </td>
      </tr>
      <tr>	
        <td>
          Per�odo:
        </td>
        <td>
          <? 
            $dia="01";
            $mes="01";
            $ano= db_getsession("DB_anousu");
            $dia2="31";
            $mes2="12";
            $ano2= db_getsession("DB_anousu");
            db_inputdata('data1',@$dia,@$mes,@$ano,true,'text',1,"");   		          
            echo "<b> at� </b>";
            db_inputdata('data2',@$dia2,@$mes2,@$ano2,true,'text',1,"");
          ?>
        </td>
      </tr>
  	  <tr>
        <td colspan="2">
          <?
            $aux->cabecalho = "<strong>Varas</strong>";
            $aux->codigo = "v53_codvara"; //chave de retorno da func
            $aux->descr  = "v53_descr";   //chave de retorno
            $aux->nomeobjeto = 'lista';
            $aux->funcao_js = 'js_mostra';
            $aux->funcao_js_hide = 'js_mostra1';
            $aux->sql_exec  = "";
            $aux->func_arquivo = "func_vara.php";  //func a executar
            $aux->nomeiframe = "db_iframe_vara";
            $aux->localjan = "";
            $aux->onclick = "";
            $aux->db_opcao = 2;
            $aux->tipo = 2;
            $aux->top = 0;
            $aux->linhas = 7;
            $aux->vwidth = 350;
            $aux->funcao_gera_formulario();
          ?>    
        </td>
      </tr>
    </table>
	</fieldset>
  <input type="button" value="relatorio" onClick="js_seleciona()"/>
</form>
<!---  menu --->
<? db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
<!--- --->
<script>
variavel = 1;
function js_seleciona(){
  for(i=0;i<document.form1.length;i++){
    if(document.form1.elements[i].name == "lista[]"){
      for(x=0;x< document.form1.elements[i].length;x++){
        document.form1.elements[i].options[x].selected = true;
      }
    }
  }
  //--
   dt1 = new Date(document.form1.data1_ano.value,document.form1.data1_mes.value,document.form1.data1_dia.value,0,0,0);
   dt2 = new Date(document.form1.data2_ano.value,document.form1.data2_mes.value,document.form1.data2_dia.value,0,0,0);
   if (dt1 > dt2 ){
      alert(_M('tributario.juridico.jur2_inicialvara001.data_inicial_maior_data_final'));
      return false;
   }
	
	
	//--
  jan = window.open('','safo' + variavel,'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
  document.form1.target = 'safo' + variavel++;
  setTimeout("document.form1.submit()",1000);
  return true;
  
}
</script>

  </body>
</html>

<script>

$("fieldset_lista").addClassName("separator");
$("v53_codvara").addClassName("field-size2");
$("v53_descr").addClassName("field-size7");
$("data1").addClassName("field-size2");
$("data2").addClassName("field-size2");
$("lista").style.width = "100%";
$("selOrdem").setAttribute("rel","ignore-css");
$("selOrdem").addClassName("field-size6");

</script>