<?
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2012  DBselller Servicos de Informatica             
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

require_once 'libs/db_stdlib.php';
require_once 'libs/db_conecta.php';
require_once 'libs/db_sessoes.php';
require_once 'libs/db_usuariosonline.php';
require_once 'libs/db_utils.php';
require_once 'libs/db_app.utils.php';

require_once 'classes/db_issconfiguracaogruposervico_classe.php';
require_once 'dbforms/db_funcoes.php';

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

db_postmemory($HTTP_POST_VARS);

$oPost = db_utils::postMemory($_POST);
$oGet  = db_utils::postMemory($_GET);

$oDaoConfiguracaoGrupo = new cl_issconfiguracaogruposervico();

$db_botao = false;
$db_opcao = 33;

if ( isset($oPost->excluir) ) {

  db_inicio_transacao();
  $db_opcao = 3;
  $oDaoConfiguracaoGrupo->excluir($q136_sequencial);
  db_fim_transacao();

} elseif ( isset($oGet->chavepesquisa) ) {

   $db_opcao = 3;
	 $result = $oDaoConfiguracaoGrupo->sql_record($oDaoConfiguracaoGrupo->sql_query($oGet->chavepesquisa)); 
   db_fieldsmemory($result, 0);
   $db_botao = true;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php db_app::load("estilos.css, grid.style.css, scripts.js, strings.js, prototype.js"); ?>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >

	<center>
		<?php include("forms/db_frmissconfiguracaogruposervico.php"); ?>
	</center>

	<?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit")); ?>

</body>
</html>
<?php
if (isset($excluir)) {

  if ($oDaoConfiguracaoGrupo->erro_status=="0") {
    $oDaoConfiguracaoGrupo->erro(true, false);
  }else{
    $oDaoConfiguracaoGrupo->erro(true, true);
  }
}

if ($db_opcao==33) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
js_tabulacaoforms("form1", "excluir", true, 1, "excluir", true);
</script>