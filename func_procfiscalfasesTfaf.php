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
include("classes/db_procfiscalfases_classe.php");
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clprocfiscalfases = new cl_procfiscalfases;
$clprocfiscalfases->rotulo->label("y108_sequencial");
$clprocfiscalfases->rotulo->label("y108_procfiscal");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr> 
    <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
	     <form name="form2" method="post" action="" >
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Ty108_sequencial?>">
              <?=$Ly108_sequencial?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?
		       db_input("y108_sequencial",10,$Iy108_sequencial,true,"text",4,"","chave_y108_sequencial");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Ty108_procfiscal?>">
              <?=$Ly108_procfiscal?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?
		       db_input("y108_procfiscal",10,$Iy108_procfiscal,true,"text",4,"","chave_y108_procfiscal");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_procfiscalfases.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <?
			$where = "";			
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_procfiscalfases.php")==true){
             include("funcoes/db_func_procfiscalfases.php");
           }else{
           $campos = "procfiscalfases.*,cgm.z01_nome";
           }
        }
        if(isset($chave_y108_sequencial) && (trim($chave_y108_sequencial)!="") ){
        	$where = " and y108_sequencial = $chave_y108_sequencial";
	        // $sql = $clprocfiscalfases->sql_query($chave_y108_sequencial,$campos,"y108_sequencial");
        }else if(isset($chave_y108_procfiscal) && (trim($chave_y108_procfiscal)!="") ){
        	$where = " and y108_procfiscal like '$chave_y108_procfiscal%' ";
	        // $sql = $clprocfiscalfases->sql_query("",$campos,"y108_procfiscal"," y108_procfiscal like '$chave_y108_procfiscal%' ");
        }
				$sql = "				
								select y100_sequencial,
								       y100_dtinicial,
								       y33_descricao,
								       descrdepto,
								       z01_nome
								  from procfiscal
								       inner join db_depart on db_depart.coddepto = procfiscal.y100_coddepto
								       inner join procfiscalcgm on y101_procfiscal = y100_sequencial
								       inner join cgm on cgm.z01_numcgm = y101_numcgm
								       inner join procfiscalcadtipo on y100_procfiscalcadtipo = y33_sequencial
								       inner join procfiscalfases on y108_procfiscal = y100_sequencial
								 where y100_coddepto= ".db_getsession("DB_coddepto")." 
								   and y100_instit = ".db_getsession("DB_instit")."
								   and y108_tipo = 2
								 order by y100_sequencial
												";
				
        $repassa = array();
        if(isset($chave_y108_procfiscal)){
          $repassa = array("chave_y108_sequencial"=>$chave_y108_sequencial,"chave_y108_procfiscal"=>$chave_y108_procfiscal);
        }
				
				
        db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
				
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clprocfiscalfases->sql_record($clprocfiscalfases->sql_query($pesquisa_chave));
          if($clprocfiscalfases->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$y108_procfiscal',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") n�o Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }
      ?>
     </td>
   </tr>
</table>
</body>
</html>
<?
if(!isset($pesquisa_chave)){
  ?>
  <script>
  </script>
  <?
}
?>
<script>
js_tabulacaoforms("form2","chave_y108_procfiscal",true,1,"chave_y108_procfiscal",true);
</script>