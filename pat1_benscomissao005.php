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
include("dbforms/db_funcoes.php");
include("classes/db_benscomissao_classe.php");
$clbenscomissao = new cl_benscomissao;
db_postmemory($HTTP_POST_VARS);
$db_opcao = 22;
$db_botao = false;

if(isset($chavepesquisa) || isset($alterar)){
  $db_opcao = 2;
  $db_botao = true;
  if(isset($alterar)){
    $sqlerro=false;
    if($sqlerro==false){
      db_inicio_transacao();
      $clbenscomissao->alterar($t60_codcom);
      if($clbenscomissao->erro_status==0){
	$sqlerro=true;
      } 
      $erro_msg = $clbenscomissao->erro_msg;
      db_fim_transacao($sqlerro);
    }
  }
  if(isset($chavepesquisa)){
    $t60_codcom = $chavepesquisa;
  }
  $result = $clbenscomissao->sql_record($clbenscomissao->sql_query($t60_codcom));
  db_fieldsmemory($result,0);
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
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
	<?
	include("forms/db_frmbenscomissao.php");
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?
if(isset($alterar)){
  db_msgbox($erro_msg);
  if($sqlerro==true){
    if($clbenscomissao->erro_campo!=""){
      echo "<script> document.form1.".$clbenscomissao->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clbenscomissao->erro_campo.".focus();</script>";
    };
  };
};
if(isset($chavepesquisa)){
 echo "
  <script>
      function js_db_libera(){
         parent.document.formaba.benscomissaousu.disabled=false;
         top.corpo.iframe_benscomissaousu.location.href='pat1_benscomissaousu001.php?t61_codcom=".@$t60_codcom."';
     ";
         if(isset($liberaaba)){
           echo "  parent.mo_camada('benscomissaousu');";
         }
 echo"}\n
    js_db_libera();
  </script>\n
 ";
}
 if($db_opcao==22||$db_opcao==33){
    echo "<script>document.form1.pesquisar.click();</script>";
 }
?>