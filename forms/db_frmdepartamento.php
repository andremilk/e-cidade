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
?>
<p>&nbsp;</p>
<center>
  <form name="form1" method="post" onSubmit="return js_verificaFormulario()">
    <table width="80%" border="1" cellspacing="0" cellpadding="0">
      <tr> 
        <td colspan="2" width="8%" nowrap bgcolor="#CDCDFF"  style="font-size:13px" align="center"><div align="center"><strong>Cadastro 
            de Departamentos:</strong></div></td>
      </tr>
      <tr>
        <td nowrap style="font-size:13px" align="left"><strong>Descri&ccedil;&atilde;o 
          : </strong></td>
        <td nowrap><strong>
          <input name="descrdepto" type="text" id="descrdepto" size="40" maxlength="40">
          </strong></td>
      </tr>
      <tr> 
        <td nowrap style="font-size:13px" align="left"><strong>Nome do respons&aacute;vel 
          :</strong> </td>
        <td nowrap><input name="nomeresponsavel" type="text" id="nomeresponsavel" size="40" maxlength="40"></td>
      </tr>
      <tr> 
        <td width="42%" nowrap style="font-size:13px" align="left"><strong>Email 
            do respons&aacute;vel :</strong></td>
        <td width="58%" nowrap><strong>
          <input name="emailresponsavel" type="text" id="emailresponsavel" size="50" maxlength="50">
          </strong></td>
      </tr>
      <tr> 
        <td colspan="2" nowrap><div align="left"> 
            <input name="coddepto" type="hidden" id="coddepto">
          </div></td>
      </tr>
      <tr> 
        <td colspan="2" nowrap> <div align="center"> 
            <input name="incluir" type="submit" id="incluir"  value="Incluir">
            <input name="cancelar" type="reset" id="cancelar2" value="Cancelar">
          </div></td>
      </tr>
    </table>
  </form>
</center>