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
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("classes/db_edu_relatmodel_classe.php");
include("dbforms/db_funcoes.php");
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$cledu_relatmodel = new cl_edu_relatmodel;
$db_opcao         = 22;
$db_botao         = false;
if (isset($alterar)) {
	
  if (!empty($ed217_t_cabecalho)) {
    $cledu_relatmodel->ed217_t_cabecalho = null;
  }
  
  if ($ed217_i_relatorio == 3 && $ed217_i_tipomodelo == 2) {
    $cledu_relatmodel->ed217_t_cabecalho = preg_replace(array('/\r/', '/\n/'), ' ', $ed217_t_cabecalho);
  }

  $cledu_relatmodel->ed217_exibeturma        = $ed217_exibeturma;
  $cledu_relatmodel->ed217_exibecargahoraria = $ed217_exibecargahoraria;
  $db_opcao = 2;
  db_inicio_transacao();
  $cledu_relatmodel->alterar($ed217_i_codigo);
  db_fim_transacao();
  
} else if(isset($chavepesquisa)) {
	
  $db_opcao = 2;
  $result = $cledu_relatmodel->sql_record($cledu_relatmodel->sql_query($chavepesquisa));
  db_fieldsmemory($result,0);
  $db_botao = true;
  
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
 <tr>
  <td width="360" height="18">&nbsp;</td>
  <td width="263">&nbsp;</td>
  <td width="25">&nbsp;</td>
  <td width="140">&nbsp;</td>
 </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
 <tr>
  <td  align="left" valign="top" bgcolor="#CCCCCC">
   <br>
   <center>
   <fieldset style="width:95%"><legend><b>Altera��o Modelo de Relat�rio</b></legend>
    <?include("forms/db_frmedu_relatmodel.php");?>
   </fieldset>
   </center>
  </td>
 </tr>
</table>
<?db_menu(db_getsession("DB_id_usuario"),
          db_getsession("DB_modulo"),
          db_getsession("DB_anousu"),
          db_getsession("DB_instit")
         );
?>
</body>
</html>
<?
if (isset($alterar)) {
	
  if ($cledu_relatmodel->erro_status == "0") {
  	
    $cledu_relatmodel->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    
    if ($cledu_relatmodel->erro_campo != "") {
    	
      echo "<script> document.form1.".$cledu_relatmodel->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cledu_relatmodel->erro_campo.".focus();</script>";
      
  }
  
 } else {
   $cledu_relatmodel->erro(true,true);
 }
 
}
if ($db_opcao == 22) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
js_tabulacaoforms("form1","ed217_i_relatorio",true,1,"ed217_i_relatorio",true);
</script>