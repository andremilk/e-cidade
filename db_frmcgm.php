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

  $clrotulo = new rotulocampo;
  $clrotulo->label("DBtxt29");
  $clrotulo->label("DBtxt30");  
  $clrotulo->label("DBtxt31");  
?>
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>
function FormataCPF(Campo, teclapres){
  var tecla = teclapres.keyCode;
  var vr = new String(Campo.value);
  vr = vr.replace(".", "");
  vr = vr.replace(".", "");
  vr = vr.replace("-", "");
  tam = vr.length + 1;
  if (tecla != 9 && tecla != 8){
    if (tam > 3 && tam < 7)
	    Campo.value = vr.substr(0, 3) + '.' + vr.substr(3, tam);
    if (tam >= 7 && tam <10)
	    Campo.value = vr.substr(0,3) + '.' + vr.substr(3,3) + '.' + vr.substr(6,tam-6);
    if (tam >= 10 && tam < 12)
	    Campo.value = vr.substr(0,3) + '.' + vr.substr(3,3) + '.' + vr.substr(6,3) + '-' + vr.substr(9,tam-9);
  }
}
function FormataCNPJ(Campo, teclapres){
  var tecla = teclapres.keyCode;
  var vr = new String(Campo.value);
  vr = vr.replace(".", "");
  vr = vr.replace(".", "");
  vr = vr.replace("/", "");
  vr = vr.replace("-", "");
  tam = vr.length + 1 ;
  if (tecla != 9 && tecla != 8){
    if (tam > 2 && tam < 6)
	    Campo.value = vr.substr(0, 2) + '.' + vr.substr(2, tam);
    if (tam >= 6 && tam < 9)
	    Campo.value = vr.substr(0,2) + '.' + vr.substr(2,3) + '.' + vr.substr(5,tam-5);
    if (tam >= 9 && tam < 13)
	    Campo.value = vr.substr(0,2) + '.' + vr.substr(2,3) + '.' + vr.substr(5,3) + '/' + vr.substr(8,tam-8);
    if (tam >= 13 && tam < 15)
	    Campo.value = vr.substr(0,2) + '.' + vr.substr(2,3) + '.' + vr.substr(5,3) + '/' + vr.substr(8,4)+ '-' + vr.substr(12,tam-12);
  }
}
function js_verifica(){
  if(document.form1.cnpj.value == "" && document.form1.cpf.value == ""){
    alert('preencha um dos campos \nCNPJ\nCPF ');
    return false;
  }else{
    return true;
  }
return false;
}

function js_confirma(){
  if(document.form1.cpf.value!=""){
    if(js_verificaCGCCPF(document.form1.cpf)==true){
       document.form1.submit();
     }
  }else if(document.form1.cnpj.value!=""){
    if(js_verificaCGCCPF(document.form1.cnpj)==true){
      document.form1.submit();
    }
  }else{
    alert("Usu�rio: \n\nInforme o CFP ou CNPJ.\n\nAdministrador:");
   }
}

</script>
<?
if($db_opcao == 2 || $db_opcao == 3){
  include("prot1_cadastrocgm.php");
  exit;
}
if(isset($cnpj) || isset($cpf)){
  if(isset($cnpj) && $cnpj != ""){
    $cgccpf = $cnpj;
  }
  if(isset($cpf) && $cpf != ""){
    $cgccpf = $cpf;
  }
  $cgccpf = str_replace(".","",$cgccpf);
  $cgccpf = str_replace("/","",$cgccpf);
  $cgccpf = str_replace("-","",$cgccpf);  
  //$result = $clcgm->sql_record($clcgm->sql_query("","cgm.z01_cgccpf,cgm.z01_numcgm",""," z01_cgccpf = '$cgccpf' and z01_cgccpf <> '' and z01_numcgm <> $z01_numcgm "));
    if(db_permissaomenu(db_getsession("DB_anousu"),604,3775) == "false"){
      if(trim($cgccpf) == '00000000000'){
       echo "
       <script>
	 alert('Voc� n�o tem permiss�o para incluir CPF zerado, contate o administrador para obter esta permiss�o!');
       ";
	   echo"
       </script>";
        exit;
      }
    }
  $result = $clcgm->sql_record($clcgm->sql_query("","z01_cgccpf,cgm.z01_numcgm","","z01_cgccpf = '$cgccpf' and z01_cgccpf <> '00000000000' and z01_cgccpf <> '00000000000000'")); 
  if(!isset($testanome)){
    if($clcgm->numrows > 0){
       db_fieldsmemory($result,0);
       echo "
	 <script>
	   var x = confirm('CNPJ/CPF j� existe no cadastro do CGM \\n CGM n�mero: $z01_numcgm \\n Deseja visu�liza-lo?');
	   if(x)
	     document.location.href = 'prot1_cadcgm001.php?mostradadoscgm=sim&z01_numcgm=$z01_numcgm';
	   else\n
	     document.location.href = 'prot1_cadcgm001.php';";
	 echo "</script>";
	 exit;
    }
  }
include("prot1_cadastrocgm.php");
exit;
}
?>
<form name="form1" method="post" action="" onSubmit="return js_verifica()">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr align="left" valign="top"> 
      <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
	    <td colspan="2" height="30" align="center"><br><br>
	      <strong>Cadastro Geral do Munic�pio</strong><br><br><br><br>
	    </td>
          </tr>
          <tr align="center"> 
	    <td width="40%" align="right" title="<?=$TDBtxt30?>">
	      <strong><?=$LDBtxt30?></strong>
	    </td>
	    <td align="left">
	      <input style="text-align:right" type="text" value="" name="cpf" size="18" maxlength="11" onBlur="js_verificaCGCCPF(this)" onKeyDown="return js_controla_tecla_enter(this,event);" >
	    </td>
	  </tr>
	  <tr align="center" title="<?=$TDBtxt31?>">
	    <td align="right">
	      <strong><?=$LDBtxt31?></strong>
	    </td>
	    <td align="left">
	      <input style="text-align:right" type="text" value="" name="cnpj" size="18" maxlength="14" onBlur="js_verificaCGCCPF(this)" onKeyDown="return js_controla_tecla_enter(this,event);" >
	      
	    </td>
          </tr>
	  <tr align="center">
	    <td align="right" title="<?=$TDBtxt29?>">
	      <strong><?=$LDBtxt29?></strong>
	    </td>
	    <td align="left">
              <?
		$x = array("t"=>"SIM","f"=>"N�O");
		db_select('municipio',$x,true,$db_opcao);
   	      ?>
	    </td>
          </tr>
	  <tr>
	    <td align="center" colspan="2"><br>
	      <input type="button" name="enviar" value="Confirma" onClick="js_confirma()">
	    </td>
	  </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
<script>
onLoad = document.form1.cpf.focus();
</script>
<?
if(isset($mostradadoscgm)){
echo "<script>js_OpenJanelaIframe('top.corpo','db_janela_Cgm','prot3_conscgm002.php?fechar=top.corpo.db_janela_Cgm&numcgm=$z01_numcgm','Consulta CGM - $z01_numcgm',true);</script>";
}
?>