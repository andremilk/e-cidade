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

//MODULO: saude
$clsau_grupohabilitacao->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("sd75_c_nome");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tsd76_i_codigo?>">
       <?=@$Lsd76_i_codigo?>
    </td>
    <td>
<?
db_input('sd76_i_codigo',5,$Isd76_i_codigo,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tsd76_c_grupohabilitacao?>">
       <?=@$Lsd76_c_grupohabilitacao?>
    </td>
    <td>
<?
db_input('sd76_c_grupohabilitacao',20,$Isd76_c_grupohabilitacao,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tsd76_i_habilitacao?>">
       <?
       db_ancora(@$Lsd76_i_habilitacao,"js_pesquisasd76_i_habilitacao(true);",$db_opcao);
       ?>
    </td>
    <td>
<?
db_input('sd76_i_habilitacao',5,$Isd76_i_habilitacao,true,'text',$db_opcao," onchange='js_pesquisasd76_i_habilitacao(false);'")
?>
       <?
db_input('sd75_c_nome',50,$Isd75_c_nome,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tsd76_c_descricao?>">
       <?=@$Lsd76_c_descricao?>
    </td>
    <td>
<?
db_input('sd76_c_descricao',62,$Isd76_c_descricao,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisasd76_i_habilitacao(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('top.corpo','db_iframe_sau_habilitacao','func_sau_habilitacao.php?funcao_js=parent.js_mostrasau_habilitacao1|sd75_i_codigo|sd75_c_nome','Pesquisa',true);
  }else{
     if(document.form1.sd76_i_habilitacao.value != ''){
        js_OpenJanelaIframe('top.corpo','db_iframe_sau_habilitacao','func_sau_habilitacao.php?pesquisa_chave='+document.form1.sd76_i_habilitacao.value+'&funcao_js=parent.js_mostrasau_habilitacao','Pesquisa',false);
     }else{
       document.form1.sd75_c_nome.value = '';
     }
  }
}
function js_mostrasau_habilitacao(chave,erro){
  document.form1.sd75_c_nome.value = chave;
  if(erro==true){
    document.form1.sd76_i_habilitacao.focus();
    document.form1.sd76_i_habilitacao.value = '';
  }
}
function js_mostrasau_habilitacao1(chave1,chave2){
  document.form1.sd76_i_habilitacao.value = chave1;
  document.form1.sd75_c_nome.value = chave2;
  db_iframe_sau_habilitacao.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('top.corpo','db_iframe_sau_grupohabilitacao','func_sau_grupohabilitacao.php?funcao_js=parent.js_preenchepesquisa|sd76_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_sau_grupohabilitacao.hide();
  <?
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>