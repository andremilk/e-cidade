<?php
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

require_once("libs/db_stdlib.php");
require_once("libs/db_conecta.php");
require_once("libs/db_sessoes.php");
require_once("libs/db_usuariosonline.php");
require_once("classes/db_issbaseparalisacao_classe.php");
require_once("classes/db_issmotivoparalisacao_classe.php");
require_once("dbforms/db_funcoes.php");

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$clissbaseparalisacao   = new cl_issbaseparalisacao(); 
$clissmotivoparalisacao = new cl_issmotivoparalisacao();
$db_botao = false;
$db_opcao = 33;

if (isset($excluir)) {
  db_inicio_transacao();
  $db_opcao = 3;
  $sSqlValida = $clissbaseparalisacao->sql_query_file(null, "*", null, "q140_issmotivoparalisacao = {$q141_sequencial}");
  $rsValida   = $clissbaseparalisacao->sql_record($sSqlValida);
  
  if ($clissbaseparalisacao->numrows > 0) {
  	$clissmotivoparalisacao->erro_msg  = "Motivo da paralisa��o n�o exclu�do.\\n";
  	$clissmotivoparalisacao->erro_msg .= "Registro vinculado a paralisa��es cadastradas.";
  	$clissmotivoparalisacao->erro_status = '0';
  } else {
  	$clissmotivoparalisacao->excluir($q141_sequencial);
	}
  db_fim_transacao();
} else if (isset($chavepesquisa)) {
   $db_opcao = 3;
   $result = $clissmotivoparalisacao->sql_record($clissmotivoparalisacao->sql_query($chavepesquisa)); 
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

  <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >

    <center>
    	<?
    	 include("forms/db_frmissmotivoparalisacao.php");
    	?>
    </center>

    <?php
      db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
    ?>

  </body>

</html>

<?php

  if (isset($excluir)) {
    if ($clissmotivoparalisacao->erro_status=="0") {
      $clissmotivoparalisacao->erro(true,false);
    } else {
      $clissmotivoparalisacao->erro(true,true);
    }
  }

  if ($db_opcao==33) {
    echo "<script>document.form1.pesquisar.click();</script>";
  }

?>

<script>
  js_tabulacaoforms("form1","excluir",true,1,"excluir",true);
</script>