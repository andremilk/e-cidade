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

require_once ("interfaces/IRegraLancamentoContabil.interface.php");

/**
 * @author Andrio Costa
 * @package contabilidade
 * @subpackage lancamento
 * @version $Revision: 1.8 $
 */
class RegraBaixaInscricaoPassivoSemSuporteOrcamentario implements IRegraLancamentoContabil {

  public function __construct(){
  }

  /**
   * Este m�todo deve retornar UMA instancia da Conta Cont�bil
   * Para isso devemos validar se a inscri��o � v�lida (n�o estar anulada)
   * @param $oLancamentoAuxiliar
   * @see iRegraLancamentoContabil::getRegraLancamento()
   * @return RegraLancamentoContabil
   */
  public function getRegraLancamento($iCodigoDocumento, $iCodigoLancamento, ILancamentoAuxiliar $oLancamentoAuxiliar) {

    $oInscricaoPassivo = new InscricaoPassivoOrcamento($oLancamentoAuxiliar->getInscricao());

    $iContaPcasp = cl_translan::getVinculoPcasp($oLancamentoAuxiliar->getCodigoElemento());

    if ($iContaPcasp != "") {

      $sSqlCodigoConta = "select c61_reduz ";
      $sSqlCodigoConta .= "  from conplanoreduz ";
      $sSqlCodigoConta .= " where c61_codcon = {$iContaPcasp}";
      $sSqlCodigoConta .= "   and c61_anousu = ".db_getsession("DB_anousu");
      $sSqlCodigoConta .= "   and c61_instit = ".db_getsession("DB_instit");
      $rsConta          = db_query($sSqlCodigoConta);

      if (pg_num_rows($rsConta) > 0) {

        $iContaPcasp = db_utils::fieldsMemory($rsConta, 0)->c61_reduz;
      }
    }

    $oDaoTransacao = db_utils::getDao('contranslr');
    $sWhere        = "     c45_coddoc      = {$iCodigoDocumento}";
    $sWhere       .= " and c45_anousu      = ".db_getsession("DB_anousu");
    $sWhere       .= " and c46_seqtranslan = {$iCodigoLancamento}";
    $sSqlTransacao = $oDaoTransacao->sql_query(null, "*", null, $sWhere);

    $rsTransacao   = $oDaoTransacao->sql_record($sSqlTransacao);

    for ($i = 0; $i < $oDaoTransacao->numrows; $i++) {

      $oDadosTransacao =  db_utils::fieldsMemory($rsTransacao, $i);

      if ($oDadosTransacao->c47_compara == 2 && $oDadosTransacao->c47_credito == $iContaPcasp) {
        return new RegraLancamentoContabil($oDadosTransacao->c47_seqtranslr);
      }
    }

    /**
     * Nao encontrou regra de lancamento para o documento 
     */
    return false;
  }

}