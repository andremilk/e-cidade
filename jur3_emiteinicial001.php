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
include("dbforms/db_funcoes.php");
include("classes/db_issbase_classe.php");
include("classes/db_iptubase_classe.php");
include("classes/db_cgm_classe.php");
include("classes/db_certid_classe.php");
db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);
$db_botao=1;
$db_opcao=1;

$oRotulo = new rotulocampo;
$oRotulo->label("j01_matric");
$oRotulo->label("q02_inscr");
$oRotulo->label("z01_numcgm");
$oRotulo->label("v13_certid");
$oRotulo->label("z01_nome");
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_processar() {

  iMatricula = document.form1.j01_matric.value;
  iInscricao = document.form1.q02_inscr.value;
  iCgm       = document.form1.z01_numcgm.value;
  iCertidao  = document.form1.v13_certid.value;

  sQueryString  = "certidao="+iCertidao;
  sQueryString += "&matricula="+iMatricula;
  sQueryString += "&inscricao="+iInscricao;
  sQueryString += "&cgm="+iCgm;
  
  js_OpenJanelaIframe('top.corpo','db_iframe_cda', 'jur3_emiteinicial011.php?'+sQueryString, 'Pesquisa', true);
  
}

function js_fechaIframe() {

  db_iframe_cda.hide();
}

function js_testacamp(){

  var matri = document.form1.j01_matric.value;
  var inscr = document.form1.q02_inscr.value;
  var numcgm = document.form1.z01_numcgm.value;
  var certid = document.form1.v13_certid.value;
  if(matri=="" && inscr=="" && numcgm=="" && certid==""){
    alert(_M('tributario.juridico.jur3_emiteinicial001.informe_campo'));   
    return false;  
    
  }
  
return true;  
}   
</script>
</head>
<body bgcolor=#CCCCCC >
<form class="container" name="form1" id="form1" >
<fieldset>
<legend>Consultas - Certid�o</legend>
  <table class="form-container">
    <tr>   
      <td>
      <?
        db_ancora($Lv13_certid,' js_certid(true); ',1);
      ?>
      </td>
      <td> 
      <?
        db_input('v13_certid',10,$Iv13_certid,true,'text',1);
      ?>
      </td>
    </tr>
    <tr>   
      <td>
      <?
       db_ancora($Lj01_matric,' js_matri(true); ',1);
      ?>
      </td>
      <td> 
      <?
        db_input('j01_matric',10,$Ij01_matric,true,'text',1,"onchange='js_matri(false)'");
        db_input('z01_nome',40,$Iz01_nome,true,'text',3,"","z01_nomematri");
      ?>
      </td>
    </tr>
    <tr>   
      <td>
      <?
        db_ancora($Lq02_inscr,' js_inscr(true); ',1);
      ?>
      </td>
      <td> 
      <?
        db_input('q02_inscr',10,$Iq02_inscr,true,'text',1,"onchange='js_inscr(false)'");
        db_input('z01_nome',40,0,true,'text',3,"","z01_nomeinscr");
      ?>
      </td>
    </tr>
    <tr>   
      <td>
      <?
        db_ancora($Lz01_numcgm,' js_cgm(true); ',1);
      ?>
      </td>
      <td> 
      <?
        db_input('z01_numcgm',10,$Iz01_numcgm,true,'text',1,"onchange='js_cgm(false)'");
        db_input('z01_nome',40,0,true,'text',3,"","z01_nomecgm");
      ?>
      </td>
    </tr>
  </table> 	 
</fieldset>
<input id="botao" type="button" name="pesquisar" value="Pesquisar" onclick="return js_processar()" >
</form>
<?
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
function js_matri(mostra){
  var matri=document.form1.j01_matric.value;
  if(mostra==true){
    db_iframe.jan.location.href = 'func_iptubase.php?funcao_js=parent.js_mostramatri|0|2';
    db_iframe.mostraMsg();
    db_iframe.show();
    db_iframe.focus();
  }else{
    db_iframe.jan.location.href = 'func_iptubase.php?pesquisa_chave='+matri+'&funcao_js=parent.js_mostramatri1';
  }
}
function js_mostramatri(chave1,chave2){

  document.form1.v13_certid.value    = '';
  document.form1.q02_inscr.value     = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.z01_numcgm.value    = '';
  document.form1.z01_nomecgm.value   = '';

  document.form1.j01_matric.value = chave1;
  document.form1.z01_nomematri.value = chave2;
  db_iframe.hide();
}
function js_mostramatri1(chave,erro){

  document.form1.v13_certid.value    = '';
  document.form1.q02_inscr.value     = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.z01_numcgm.value    = '';
  document.form1.z01_nomecgm.value   = '';
  
  document.form1.z01_nomematri.value = chave; 
  if(erro==true){ 
    document.form1.j01_matric.focus(); 
    document.form1.j01_matric.value = ''; 
  }
}


function js_inscr(mostra){
  var inscr=document.form1.q02_inscr.value;
  if(mostra==true){
    db_iframe.jan.location.href = 'func_issbase.php?funcao_js=parent.js_mostrainscr|q02_inscr|z01_nome';
    db_iframe.mostraMsg();
    db_iframe.show();
    db_iframe.focus();
  }else{
    db_iframe.jan.location.href = 'func_issbase.php?pesquisa_chave='+inscr+'&funcao_js=parent.js_mostrainscr1';
  }
}
function js_mostrainscr(chave1,chave2){

  document.form1.v13_certid.value    = '';
  document.form1.z01_numcgm.value    = '';
  document.form1.z01_nomecgm.value   = '';
  document.form1.j01_matric.value    = '';
  document.form1.z01_nomematri.value = '';
  
  document.form1.q02_inscr.value = chave1;
  document.form1.z01_nomeinscr.value = chave2;
  db_iframe.hide();
}

function js_mostrainscr1(chave,erro){
  
  document.form1.v13_certid.value    = '';
  document.form1.z01_numcgm.value    = '';
  document.form1.z01_nomecgm.value   = '';
  document.form1.j01_matric.value    = '';
  document.form1.z01_nomematri.value = '';
  
  document.form1.z01_nomeinscr.value = chave; 
  if(erro==true){ 
    document.form1.q02_inscr.focus(); 
    document.form1.q02_inscr.value = ''; 
  }
}


function js_cgm(mostra){
  var cgm=document.form1.z01_numcgm.value;
  if(mostra==true){
    db_iframe.jan.location.href = 'func_nome.php?funcao_js=parent.js_mostracgm|0|1';
    db_iframe.mostraMsg();
    db_iframe.show();
    db_iframe.focus();
  }else{
    db_iframe.jan.location.href = 'func_nome.php?pesquisa_chave='+cgm+'&funcao_js=parent.js_mostracgm1';
  }
}
function js_mostracgm(chave1,chave2){

  document.form1.v13_certid.value    = '';
  document.form1.q02_inscr.value     = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.j01_matric.value    = '';
  document.form1.z01_nomematri.value = '';
  
  document.form1.z01_numcgm.value = chave1;
  document.form1.z01_nomecgm.value = chave2;
  db_iframe.hide();
}
function js_mostracgm1(erro,chave){

  document.form1.v13_certid.value    = '';
  document.form1.q02_inscr.value     = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.j01_matric.value    = '';
  document.form1.z01_nomematri.value = '';
  
  document.form1.z01_nomecgm.value = chave; 
  if(erro==true){ 
    document.form1.z01_numcgm.focus(); 
    document.form1.z01_numcgm.value = ''; 
  }
}

function js_certid(mostra){
  var certid=document.form1.v13_certid.value;
  if(mostra==true){
    db_iframe.jan.location.href = 'func_certid.php?consulta=true&funcao_js=parent.js_mostracertid|0';
    db_iframe.mostraMsg();
    db_iframe.show();
    db_iframe.focus();
  }else{
    db_iframe.jan.location.href = 'func_certid.php?consulta=true&pesquisa_chave='+certid+'&funcao_js=parent.js_mostracertid1';
  }
}
function js_mostracertid(chave1){

  document.form1.z01_numcgm.value    = '';
  document.form1.z01_nomecgm.value   = '';
  document.form1.q02_inscr.value     = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.j01_matric.value    = '';
  document.form1.z01_nomematri.value = '';
  
  document.form1.v13_certid.value = chave1;
  db_iframe.hide();
}
function js_mostracertid1(chave,erro){

  document.form1.z01_numcgm.value    = '';
  document.form1.z01_nomecgm.value   = '';
  document.form1.q02_inscr.value     = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.j01_matric.value    = '';
  document.form1.z01_nomematri.value = '';
  
  if(erro==true){ 
    alert(_M('tributario.juridico.jur3_emiteinicial001.numero_certidao_invalida'));
    document.form1.v13_certid.focus(); 
  }
}

</script>
<?
$func_iframe = new janela('db_iframe','');
$func_iframe->posX=1;
$func_iframe->posY=20;
$func_iframe->largura=780;
$func_iframe->altura=430;
$func_iframe->titulo='Pesquisa';
$func_iframe->iniciarVisivel = false;
$func_iframe->mostrar();
if(isset($invalido)){
    echo "<script>alert(_M('tributario.juridico.jur3_emiteinicial001.nenhum_certidao_encontrada'))</script>";
}
?>

<script>

$("v13_certid").addClassName("field-size2");
$("j01_matric").addClassName("field-size2");
$("z01_nomematri").addClassName("field-size7");
$("q02_inscr").addClassName("field-size2");
$("z01_nomeinscr").addClassName("field-size7");
$("z01_numcgm").addClassName("field-size2");
$("z01_nomecgm").addClassName("field-size7");

</script>