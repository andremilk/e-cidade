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

include("fpdf151/pdf.php");
include("fpdf151/assinatura.php");
include("dbforms/db_funcoes.php");
include("libs/db_libcaixa_ze.php");
include("libs/db_libgertxtfolha.php");
include("classes/db_folha_classe.php");
include("classes/db_pensao_classe.php");
include("classes/db_rharqbanco_classe.php");
include("classes/db_orctiporec_classe.php");
require_once("libs/db_utils.php");
parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));
db_postmemory($HTTP_POST_VARS);

$cllayouts_bb  = new cl_layouts_bb;
$clfolha       = new cl_folha;
$clpensao      = new cl_pensao;
$clrharqbanco  = new cl_rharqbanco;
$clorctiporec  = new cl_orctiporec;
$clrotulo = new rotulocampo;
$clrotulo->label("rh01_regist");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("z01_cgccpf");
$clrotulo->label("r38_liq");
$clrotulo->label("r38_banco");
$clrotulo->label("r38_agenc");
$clrotulo->label("r38_conta");
$clrotulo->label("r70_descr");

$sqlerro = false;

db_sel_instit();

//die($clrharqbanco->sql_query($rh34_codarq));    
$result_arqbanco=$clrharqbanco->sql_record($clrharqbanco->sql_query($rh34_codarq));    
if($clrharqbanco->numrows>0){

  db_fieldsmemory($result_arqbanco,0);


    include("dbforms/db_layouttxt.php");
    $posicao = "A";
    $layoutimprime = 2;
    $idcaixa = "P";
    $idcliente = "P";
    $db90_codban = $rh34_codban;
    $agenciaheader = $rh34_agencia;
    $contaheader = $rh34_conta;
    $contalote   = $rh34_conta;

    $conveniobanco = trim($rh34_convenio); 
    
    $descrarquivo = "FOLHA PAGAMENTO"; // Campo somente do layout 3

    $dvagenciaheader = "0";
    $dvcontaheader   = "0";
    $dvagenciacontaheader = " ";
    if(trim($rh34_dvagencia)!=""){
      $dvagenciaheader = $rh34_dvagencia[0];
    }
    if(trim($rh34_dvconta)!=""){
      $dvcontaheader  = $rh34_dvconta[0];
      $digitos        = strlen($rh34_dvconta);
      if($digitos>1){
        $dvagenciacontaheader = $rh34_dvconta[1];
      }
    }
    $operacaoheader = substr($contaheader,0,3);
    $contaheader2   = str_pad(trim(substr($contaheader,4,20)),8);
    $dvagencialote = $dvagenciaheader;
    $dvcontalote   = $dvcontaheader;
    $dvagenciacontalote = $dvagenciacontaheader;

    $datageracao = $datagera; 
    $horageracao = date("H").date("i").date("s");
 
    if(isset($datageracao) && $datageracao!=""){
      $datag = split('-',$datageracao);
      $datag_dia = $datag[2];
      $datag_mes = $datag[1];
      $datag_ano = $datag[0];
    }
    if(isset($datadeposit) && $datadeposit!=""){
      $datad = split('-',$datadeposit);
      $datad_dia = $datad[2];
      $datad_mes = $datad[1];
      $datad_ano = $datad[0];
    }

    $sequencialarq = $rh34_sequencial;
    $usoprefeitura1 = $rh34_sequencial;

    $adatadegeracao = $datag_ano."-".$datag_mes."-".$datag_dia;
    $datadedeposito = $datad_ano."-".$datad_mes."-".$datad_dia;
 
    $sequenciaarqui = $rh34_sequencial;
    $versaodoarquiv = "030";

    $paramnome = $datag_mes.$datag_ano."_".$horageracao;
    $nomearquivo = "folha_".$db90_codban."_".$paramnome.".txt";
    $db_layouttxt = new db_layouttxt($layoutimprime,"tmp/".$nomearquivo, $posicao);

    $conveniobanco = trim($rh34_convenio); 
    ////// DADOS SOMENTE CNAB240 CEF
    $parametrotransmiss = substr($rh34_convenio,10,2);
    $indicaambcaixa   = "P";
    $indicaambcliente = "P";
    $densidadearquivo = "01600";
    //////


    db_setaPropriedadesLayoutTxt(&$db_layouttxt,1);

 ///// }

}else{
  $sqlerro = true;
  $erro_msg = "Arquivo n�o encontrado";
}

if(!isset($rh34_where) || (isset($rh34_where) && trim($rh34_where) == "")){
  $rh34_wherefolha = "";
  $rh34_wherepensa = ""; 
}else{
  $rh34_wherefolha = $rh34_where." and ";
  $rh34_wherepensa = '' ;
}

$rh34_wherefolha.= " r38_banco = '$rh34_codban' ";
$rh34_wherepensa.= " r52_codbco = '$rh34_codban' and r52_anousu = ".db_anofolha()." and r52_mesusu = ".db_mesfolha();

$titrelatorio = "Todos os funcion�rios";
$titarquivo   = "pagtofuncionarios";


$lPensionista = false;

if($sqlerro == false){

  if($tiparq == 0){
    $sql = $clfolha->sql_query_gerarqbag(null,"folha.*,cgm.*, length(trim(r38_agenc)) as qtddigitosagencia,
                                               r70_descr, 
                                               case when trim(translate(r38_conta,'0','')) = '' then '02' 
                                                    when (select rh02_fpagto 
                                                            from rhpessoalmov
                                                           where rh02_regist = r38_regist
                                                             and rh02_anousu = ".db_anofolha()."
                                                            and rh02_mesusu     = ".db_mesfolha()."
                                                            and rh02_instit     = ".db_getsession("DB_instit").") = 4 then '02' 
                                                else '01' end as tipo_pagamento,
                                               length(trim(z01_cgccpf)) as tam,
                                               r38_liq as valorori",
                                              "r38_banco,tipo_pagamento, r38_nome",
                                              "$rh34_wherefolha");
    $result  = $clfolha->sql_record($sql);
    $numrows = $clfolha->numrows;
  }else{

      $titarquivo = "pensaojudicial";
      $titrelatorio = "PENS�O JUDICIAL ";
    if($qfolha == 1){
      $campovalor = " r52_valor+r52_valfer ";
      $rh34_wherepensa .= " and (r52_valor > 0 or r52_valfer > 0 ) ";
    }else if($qfolha == 2){
      $campovalor = " r52_valcom ";
      $rh34_wherepensa .= " and r52_valcom > 0 ";
    }else if($qfolha == 3){
      $campovalor = " r52_val13 ";
      $rh34_wherepensa .= " and r52_val13 > 0 ";
    }else if($qfolha == 4){
      $campovalor = " r52_valres ";
      $rh34_wherepensa .= " and r52_valres > 0 ";
    }

    $sql = $clpensao->sql_query_gerarqbag(null,null,null,null,"$campovalor as r38_liq, length(trim(r52_codage)||trim(r52_dvagencia)) as qtddigitosagencia,
                                               r52_numcgm as r38_regist,
                                               r52_codbco as r38_banco,
                                               trim(r52_conta)||trim(coalesce(r52_dvconta,'')) as r38_conta,
	                                             trim(r52_codage)||trim(coalesce(r52_dvagencia,'')) as r38_agenc,
	                                             cgm.*,func.z01_nome as nomefuncionario,
                                               r70_descr,
                                               length(trim(cgm.z01_cgccpf)) as tam,
                                               $campovalor as valorori",
                                              "r52_codbco,cgm.z01_nome",
                                              "$rh34_wherepensa and $campovalor > 0");

    $lPensionista = true;
    $result  = $clpensao->sql_record($sql);
    $numrows = $clpensao->numrows;
  }

  if ($numrows > 0) {
    
    db_fieldsmemory($result,0);

    $idregistroimpressao = $posicao;
    $nomearquivo_impressao = "/tmp/folha_".$db90_codban."_".$paramnome.".pdf";
    if(!is_writable("/tmp/")){
      $sqlerro= true;
      $erro_msg = 'Sem permiss�o de gravar o arquivo. Contate suporte.';
    }  

    ///// INICIA IMPRESS�O DO RELAT�RIO
    $pdf = new PDF();
    $pdf->Open();
    $pdf->AliasNbPages();
    $pdf->setfillcolor(235);
    $total = 0;
    $alt = 4;

    $head3 = "ARQUIVO PAGAMENTO FOLHA";
    $head5 = "SEQUENCIAL DO ARQUIVO  :  ".$sequenciaarqui;
    $head6 = "GERA��O  :  ".db_formatar($datagera,"d").' AS '.$horageracao.' HS';
    $head7 = "PAGAMENTO:  ".db_formatar($datadedeposito,"d");
    $head8 = 'BANCO : '.$rh34_codban.' - '.$db90_descr;


    $finalidadedoc = "00";
    $codigocompromisso = substr($rh34_convenio,6,4);
    $tipocompromisso = "02";
    $agencialote = $agenciaheader;

    $loteservico = 0;
    $tiposervico = "30";
    if($db90_codban == $r38_banco){
      $formalancamento = "01";
    }else{
      $formalancamento = "03";
    }

    $sequencialnolote = 0;
  
    $troca_header = 0;

    $quantidadefuncionarios = 0;
    $valortotal = 0;
    $valortotallote = 0;
    $quantidaderegistarq = 0;
    for($i=0;$i<$numrows;$i++){

      db_fieldsmemory($result,$i);
      if ($tipo_pagamento == 02) {
        $r38_banco = '';
      }
      //////////////////////////////////////////////
      $formalancamento = $tipo_pagamento;
      $agencia = db_formatar(str_replace('.','',str_replace('-','',$r38_agenc)),'s','0', 5,'e',0);
      $conta   = trim(str_replace(',','',str_replace('.','',str_replace('-','',$r38_conta))));
      $qtddigitosconta = strlen($conta) - 4; /////// -4, pois -1 � do dvconta e -3 do codigooperacao
      $dvconta = substr($conta,-1);
      $codigooperacao = substr($conta,0,3);
      $conta = substr($conta, 3, $qtddigitosconta);
      //////////////////////////////////////////////
      //////////////////////////////////////////////
      $quantidaderegistarq ++;
      if($troca_header != $tipo_pagamento){
        if($troca_header != 0){
          $quantidadetotallote = $sequencialnolote + 2;
          //$valortotallote = $valortotal;
          ///// TRAILLER DE LOTE
          db_setaPropriedadesLayoutTxt(&$db_layouttxt, 4);
          ///// FINAL DO TRAILLER DE LOTE
          $sequencialnolote = 0;
          $valortotallote   = 0;
          $quantidaderegistarq ++;
        }
        ///// HEADER DO LOTE
        $loteservico       += 1;
        $formalancamento = $tipo_pagamento;
        db_setaPropriedadesLayoutTxt(&$db_layouttxt, 2);
        ///// FINAL DO HEADER DO LOTE
        $troca_header = $tipo_pagamento;
        $sequencialnolote == 0;
        $quantidaderegistarq ++;
      }
      //////////////////////////////////////////////
      // CAMPOS LAYOUT CNAB240
      $sequencialnolote ++;
      $compensacao = "700";
      if($r38_banco == $db90_codban){
        $compensacao = str_repeat('0',3);
      }

      $agenciapagarT = db_formatar(str_replace('.','',str_replace('-','',$r38_agenc)),'s','0', 6,'e',0);
      $contasapagarT = db_formatar(str_replace('.','',str_replace('-','',$r38_conta)),'s','0',15,'e',0);

      $agenciapagar = substr($agenciapagarT,0,5);
      $digitoagenci = substr($agenciapagarT,5,1);

      $contasapagar = substr($contasapagarT,0,14)+0;
      $digitocontas = substr($contasapagarT,14,1);
      $contasapagar = db_formatar($contasapagar,'s','0',12,'e',0);

      $bancofavorecido     = $r38_banco;
      $agenciafavorecido   = $agenciapagar;
      $dvagenciafavorecido = $digitoagenci;
      $contafavorecido     = $contasapagar;
      $dvcontafavorecido   = $digitocontas;
      $dvagenciacontafav   = " ";
      $numerocontrolemov   = $r38_regist;
      $sequencialreg       = $i + 1;
			//echo "<br> matricula --> $numerocontrolemov   banco --> $bancofavorecido   agencia --> $agenciafavorecido   dvagencia --> $dvagenciafavorecido   conta --> $contafavorecido  dvconta --> $dvcontafavorecido ";
      //////////////////////////////////////////////
      //////////////////////////////////////////////
      
      $valordebito = $r38_liq;
      $dataprocessamento = $datadedeposito;
//      $sequencialreg = "      ";
      ///// REGISTRO A
      db_setaPropriedadesLayoutTxt(&$db_layouttxt, 3, $posicao);
      ///// FINAL DO REGISTRO A
     /**
      * Grava Registro N
      */
      //db_setaPropriedadesLayoutTxt(&$db_layouttxt, 3, 'B');
      if($tam == 11){
        $tipoinscricaofav = "1";
      }else if($tam == 14){
        $tipoinscricaofav = "2";
      }else{
        $tipoinscricaofav = "3";
      }
      $datavencimento = $datadedeposito;
      $valorvencimento = $r38_liq;
      //$db90_codban
      ///// REGISTRO B
      db_setaPropriedadesLayoutTxt(&$db_layouttxt, 3, "");
      ///// FINAL DO REGISTRO B

      if($i == 0 || $pdf->gety() > $pdf->h - 30){
	$pdf->addpage("L");
        $pdf->cell(15,$alt,$RLrh01_regist,1,0,"C",1);
        if($tiparq < 5){
          $pdf->cell(15,$alt,$RLz01_numcgm,1,0,"C",1);
          $pdf->cell(20,$alt,$RLz01_cgccpf,1,0,"C",1);
          $pdf->cell(65,$alt,$RLz01_nome,1,0,"C",1);
          $pdf->cell(65,$alt,$RLr70_descr,1,0,"C",1);
	}else{
          $pdf->cell(65,$alt,"Pensionista",1,0,"C",1);
          $pdf->cell(65,$alt,"Funcion�rio",1,0,"C",1);
          $pdf->cell(15,$alt,$RLz01_numcgm,1,0,"C",1);
          $pdf->cell(20,$alt,$RLz01_cgccpf,1,0,"C",1);
	}
        $pdf->cell(17,$alt,$RLr38_liq,1,0,"C",1);
        $pdf->cell(13,$alt,$RLr38_agenc,1,0,"C",1);
        $pdf->cell(20,$alt,$RLr38_conta,1,1,"C",1);
        $pdf->ln(3);
      }

      $pdf->setfont('arial','',7);
      $pdf->cell(15,$alt,$r38_regist,1,0,"C",0);
      if($tiparq < 5){
        $pdf->cell(15,$alt,$z01_numcgm,1,0,"C",0);
        $pdf->cell(20,$alt,$z01_cgccpf,1,0,"C",0);
        $pdf->cell(65,$alt,$z01_nome,1,0,"L",0);
        $pdf->cell(65,$alt,$r70_descr,1,0,"L",0);
      }else{
        $pdf->cell(65,$alt,$z01_nome,1,0,"L",0);
        $pdf->cell(65,$alt,$nomefuncionario,1,0,"L",0);
        $pdf->cell(15,$alt,$z01_numcgm,1,0,"C",0);
        $pdf->cell(20,$alt,$z01_cgccpf,1,0,"C",0);
      }
      $pdf->cell(17,$alt,db_formatar($r38_liq,'f'),1,0,"R",0);
      $pdf->cell(13,$alt,$r38_agenc,1,0,"R",0);
      $pdf->cell(20,$alt,$r38_conta,1,1,"R",0);
      $quantidadefuncionarios ++;
      $valortotal += $r38_liq;
      $valortotallote += $r38_liq;
    }

    $pdf->setfont('arial','b',8);
    $pdf->cell(160,$alt,'Total de funcion�rios',1,0,"C",1);
    $pdf->cell(20,$alt,$quantidadefuncionarios,1,0,"R",1);
    $pdf->cell(50,$alt,'',1,1,"C",1);

    $pdf->cell(160,$alt,'Total Geral',1,0,"C",1);
    $pdf->cell(20,$alt,db_formatar($valortotal,'f'),1,0,"R",1);
    $pdf->cell(50,$alt,'',1,1,"C",1);

    $quantidadetotallote = $sequencialnolote + 2;
    $quantidaderegistarq ++;
    //$valortotallote = $valortotal;
    ///// TRAILLER DE LOTE
    db_setaPropriedadesLayoutTxt(&$db_layouttxt, 4);
    ///// FINAL DO TRAILLER DE LOTE



    // VARIAVEIS PARA TRAILLER CEF
    $quanttrailler = $quantidadefuncionarios + 2;
    $valortrailler = $valortotal;
    $sequencialreg += 1;
    //////////////////////////////////
    //////////////////////////////////


    // VARIAVEIS PARA TRAILLER CNAB240
    $quantidadelotesarq = $loteservico;
//    $quantidaderegistarq = $quantidadetotallote + 2;
    //////////////////////////////////
    //////////////////////////////////


    ///// TRAILLER DE ARQUIVO
    $loteservico = '9999';
    $quantidaderegistarq ++;
    db_setaPropriedadesLayoutTxt(&$db_layouttxt, 5);
    ///// FINAL DO TRAILLER DE ARQUIVO
    //////////////////////////////////

    $pdf->Output($nomearquivo_impressao,false,true);
  }else{
    $sqlerro = true;
    $erro_msg = "Nenhum registro encontrado. Contate o suporte.";
  }
}
//exit;
  if($sqlerro == false){
    echo "
    <script>
      parent.js_detectaarquivo('tmp/$nomearquivo','$nomearquivo_impressao');
    </script>
    ";
  }else{
    echo "
    <script>
      parent.js_erro('$erro_msg');
    </script>
    ";
  }
  db_fim_transacao($sql);

?>