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
include("classes/db_conparametro_classe.php");
include("dbforms/db_funcoes.php");
db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);
$clconparametro = new cl_conparametro;
$db_opcao = 22;
$db_botao = false;
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
   db_inicio_transacao();
   $result = $clconparametro->sql_record($clconparametro->sql_query());
   if($result==false || $clconparametro->numrows==0){
     $clconparametro->incluir();
   }else{
     $clconparametro->alterar($oid);
   }
   db_fim_transacao();
}
$db_opcao = 2;
$result = $clconparametro->sql_record($clconparametro->sql_query());
if($result!=false && $clconparametro->numrows>0){
  db_fieldsmemory($result,0);
}
$db_botao = true;
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
<table align="center" style="padding-top: 25px;">
  <tr> 
    <td> 
	    <center>
				<?
				  include("forms/db_frmconparametro.php");
				?>
	    </center>
	  </td>
  </tr>
</table>
<?
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?

	if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
	  if($clconparametro->erro_status=="0"){
	    $clconparametro->erro(true,false);
	    $db_botao=true;
	    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
	    if($clconparametro->erro_campo!=""){
	      echo "<script> document.form1.".$clconparametro->erro_campo.".style.backgroundColor='#99A9AE';</script>";
	      echo "<script> document.form1.".$clconparametro->erro_campo.".focus();</script>";
	    };
	  }else{
	    $clconparametro->erro(true,true);
	  };
	};

	if($db_opcao==22){
	  echo "<script>document.form1.pesquisar.click();</script>";
	}
?>