<?
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBselller Servicos de Informatica             
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
include("libs/db_libdicionario.php");
include("classes/db_pactoitem_classe.php");
include("classes/db_pactoitempcmater_classe.php");
include("dbforms/db_funcoes.php");
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clpactoitem = new cl_pactoitem;
$clpactoitempcmater = new cl_pactoitempcmater;
$db_opcao = 22;
$db_botao = false;
if(isset($alterar)){
  db_inicio_transacao();
  $db_opcao = 2;
  $clpactoitem->alterar($o109_sequencial);
  
  $clpactoitempcmater->excluir(null," o89_pactoitem = {$clpactoitem->o109_sequencial}");
  if ($clpactoitempcmater->erro_status =='0') {
    $clpactoitem->erro_msg = $clpactoitempcmater->erro_msg;
    $lErro = true;
  }
  
  if (!empty($o89_pcmater)){
    $clpactoitempcmater->o89_pactoitem = $clpactoitem->o109_sequencial;
    $clpactoitempcmater->o89_pcmater   = $o89_pcmater;
    $clpactoitempcmater->incluir(null);
    if ($clpactoitempcmater->erro_status =='0') {
      $clpactoitem->erro_msg = $clpactoitempcmater->erro_msg;
      $lErro = true;
    }
  }
  
  
  db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result = $clpactoitem->sql_record($clpactoitem->sql_query($chavepesquisa)); 
   db_fieldsmemory($result,0);

   $resultpcmater = $clpactoitempcmater->sql_record($clpactoitempcmater->sql_query(null,"*",null,"o89_pactoitem = $chavepesquisa")); 
   db_fieldsmemory($resultpcmater,0);
   
      $db_botao = true;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
    <center>
	<?
	include("forms/db_frmpactoitem.php");
	?>
    </center>
<?
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?
if(isset($alterar)){
  if($clpactoitem->erro_status=="0"){
    $clpactoitem->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clpactoitem->erro_campo!=""){
      echo "<script> document.form1.".$clpactoitem->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clpactoitem->erro_campo.".focus();</script>";
    }
  }else{
    $clpactoitem->erro(true,true);
  }
}
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
js_tabulacaoforms("form1","o109_matunid",true,1,"o109_matunid",true);
</script>