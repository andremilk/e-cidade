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

require("libs/db_stdlibwebseller.php");
require("libs/db_stdlib.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("classes/db_aluno_classe.php");
include("classes/db_alunocurso_classe.php");
include("dbforms/db_funcoes.php");
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$claluno = new cl_aluno;
$clalunocurso = new cl_alunocurso;
$db_opcao = 2;
$db_opcao1 = 3;
$db_botao = true;
if(isset($alterar)){
 $db_opcao = 2;
 $db_opcao1 = 3;
 $db_botao = true;
 $ed47_c_foto = @trim($GLOBALS["HTTP_POST_VARS"]["ed47_o_oid"]);
 $ed47_o_oid = "tmp/".@trim($GLOBALS["HTTP_POST_VARS"]["ed47_o_oid"]);
 db_inicio_transacao();
 if($ed47_c_foto!=""){
  $oid_imagem = pg_loimport($conn,$ed47_o_oid) or die("Erro(15) importando imagem");
  $ed47_o_oid = $oid_imagem;
 }else{
  $oid_imagem = "0";
 }
 $claluno->ed47_c_foto = $ed47_c_foto;
 $claluno->ed47_o_oid = $oid_imagem;
 $claluno->alterar($ed47_i_codigo);
 db_fim_transacao();
}
if(isset($excluirfoto)){
 $sql = "UPDATE aluno SET
          ed47_c_foto = '',
          ed47_o_oid = 0
         WHERE ed47_i_codigo = $chavepesquisa
        ";
 $result = pg_query($sql);
}
if(isset($chavepesquisa)){
 $db_opcao = 2;
 $db_opcao1 = 3;
 $result = $claluno->sql_record($claluno->sql_query($chavepesquisa));
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
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
 <tr>
  <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
   <br>
   <center>
   <fieldset style="width:95%"><legend><b>Outros Dados</b></legend>
    <?include("forms/db_frmaluno.php");?>
   </fieldset>
   </center>
  </td>
 </tr>
</table>
</body>
</html>
<?
if(isset($alterar)){
 if($claluno->erro_status=="0"){
  $claluno->erro(true,false);
  $db_botao=true;
  echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
  if($claluno->erro_campo!=""){
   echo "<script> document.form1.".$claluno->erro_campo.".style.backgroundColor='#99A9AE';</script>";
   echo "<script> document.form1.".$claluno->erro_campo.".focus();</script>";
  };
 }else{
  $claluno->erro(true,false);
  db_redireciona("edu1_aluno002.php?chavepesquisa=$chavepesquisa");
 };
};
if(isset($excluirfoto)){
 db_redireciona("edu1_aluno002.php?chavepesquisa=$chavepesquisa");
}
?>