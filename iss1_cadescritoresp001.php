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
include("classes/db_cadescritoresp_classe.php");
include("classes/db_cadescrito_classe.php");
include("dbforms/db_funcoes.php");
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clcadescritoresp = new cl_cadescritoresp;
$clcadescrito = new cl_cadescrito;
$db_opcao = 22;
$db_botao = false;

if(isset($alterar) || isset($excluir) || isset($incluir)){
  $sqlerro = false;
  /*
$clcadescritoresp->q84_sequencial = $q84_sequencial;
$clcadescritoresp->q84_cadescrito = $q84_cadescrito;
$clcadescritoresp->q84_numcgm = $q84_numcgm;
  */
}
if(isset($incluir)){
  if($sqlerro==false){
    db_inicio_transacao();
    $clcadescritoresp->incluir($q84_sequencial);
    $erro_msg = $clcadescritoresp->erro_msg;
    if($clcadescritoresp->erro_status==0){
      $sqlerro=true;
    }
    db_fim_transacao($sqlerro);
  }
}else if(isset($alterar)){
  if($sqlerro==false){
    db_inicio_transacao();
    $clcadescritoresp->alterar($q84_sequencial);
    $erro_msg = $clcadescritoresp->erro_msg;
    if($clcadescritoresp->erro_status==0){
      $sqlerro=true;
    }
    db_fim_transacao($sqlerro);
  }
}else if(isset($excluir)){
  if($sqlerro==false){
    db_inicio_transacao();
    $clcadescritoresp->excluir($q84_sequencial);
    $erro_msg = $clcadescritoresp->erro_msg;
    if($clcadescritoresp->erro_status==0){
      $sqlerro=true;
    }
    db_fim_transacao($sqlerro);
  }
}else if(isset($opcao)){
   $result = $clcadescritoresp->sql_record($clcadescritoresp->sql_query_mod(null,'*',null,"q84_cadescrito = $q84_cadescrito"));
   if($result!=false && $clcadescritoresp->numrows>0){
     db_fieldsmemory($result,0);
   }
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
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="center" valign="top" bgcolor="#CCCCCC"> 
    <center>
	<?
	include("forms/db_frmcadescritoresp.php");
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?
if(isset($alterar) || isset($excluir) || isset($incluir)){
    db_msgbox($erro_msg);
    if($clcadescritoresp->erro_campo!=""){
        echo "<script> document.form1.".$clcadescritoresp->erro_campo.".style.backgroundColor='#99A9AE';</script>";
        echo "<script> document.form1.".$clcadescritoresp->erro_campo.".focus();</script>";
    }
}
?>