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
 
require_once("libs/db_stdlibwebseller.php");
require_once("fpdf151/scpdf.php");
require_once("classes/db_calendario_classe.php");
require_once("classes/db_periodocalendario_classe.php");
require_once("classes/db_escoladiretor_classe.php");
require_once("classes/db_escola_classe.php");
require_once("classes/db_edu_parametros_classe.php");
require_once("classes/db_matricula_classe.php");  
require_once("classes/db_edu_relatmodel_classe.php"); 
require_once("classes/db_telefoneescola_classe.php");
require_once("libs/db_utils.php");   
$iEscola            = db_getsession("DB_coddepto");
$sNomeEscola        = db_getsession("DB_nomedepto");
$clMatricula        = new cl_matricula();
$clEduRelatmodel    = new cl_edu_relatmodel();
$clTelefoneEscola   = new cl_telefoneescola();
$clEduParametros    = new cl_edu_parametros();
$clEscola           = new cl_escola();
$sSecret = $sSecretario;
$sPresid = $sPresidente;

function maiusculo(&$string) {
	
  $string = strtoupper($string);
  $string = str_replace("á","�",$string);
  $string = str_replace("é","�",$string);
  $string = str_replace("í","�",$string);
  $string = str_replace("ó","�",$string);
  $string = str_replace("ú","�",$string);
  $string = str_replace("â","�",$string);
  $string = str_replace("ê","�",$string);
  $string = str_replace("ô","�",$string);
  $string = str_replace("î","�",$string);
  $string = str_replace("û","�",$string);
  $string = str_replace("ã","�",$string);
  $string = str_replace("õ","�",$string);
  $string = str_replace("ç","�",$string);
  $string = str_replace("à","�",$string);
  $string = str_replace("è","�",$string);
  return $string;
  
}


$sDataVotacao       = substr($iData,6,4)."-".substr($iData,3,2)."-".substr($iData,0,2);

$sSqlDadosTelEscola = $clTelefoneEscola->sql_query("",
                                                   "ed26_i_ddd,ed26_i_numero,ed26_i_ramal",
                                                   "",
                                                   "ed26_i_escola = $iEscola LIMIT 1"
                                                  );

                                     
if ($clTelefoneEscola->numrows == 0) {    
  $sTelEscola = "";  
} 
  $rsDadosTelEscola   = db_query($sSqlDadosTelEscola);
  $oDadosTelEscola    = db_utils::fieldsMemory($rsDadosTelEscola,0);
  $iDdd               = $oDadosTelEscola->ed26_i_ddd;
  $iNumero            = $oDadosTelEscola->ed26_i_numero;
  $sTelEscola         =  $iDdd." ".$iNumero ;


$sCampos         = " ed18_c_nome as nome_escola,j14_nome as rua_escola,ed18_c_cep as cep_escola, ";
$sCampos        .= " j13_descr as bairro_escola, ed18_i_numero as num_escola,";
$sCampos        .= " ed261_c_nome as mun_escola,ed260_c_sigla as uf_escola,ed18_c_email as email_escola ";
$sSqlDadosEscola = $clEscola->sql_query("",$sCampos,"","ed18_i_codigo = $iEscola");
$rsDadosEscola   = db_query($sSqlDadosEscola);
$oDadosEscola    = db_utils::fieldsMemory($rsDadosEscola,0);
$nome_escola     = $oDadosEscola->nome_escola;
$rua_escola      = $oDadosEscola->rua_escola;
$cep_escola      = $oDadosEscola->cep_escola;
$num_escola      = $oDadosEscola->num_escola;
$mun_escola      = $oDadosEscola->mun_escola;
$uf_escola       = $oDadosEscola->uf_escola;
$bairro_escola   = $oDadosEscola->bairro_escola;
$email_escola    = $oDadosEscola->email_escola;

$sCampoMatricula  = " ed29_i_codigo,fc_idade(ed47_d_nasc,'$sDataVotacao'::date) as idadealuno,ed47_v_nome,ed11_c_descr,ed10_c_abrev,";
$sCampoMatricula .= " ed47_v_mae, ed47_d_nasc, turma.ed57_i_codigo,aluno.ed47_i_codigo,turma.ed57_c_descr,ed29_c_descr";
$sOrderMatricula  = " turma.ed57_i_codigo,ed29_c_descr,ed11_c_descr,turma.ed57_c_descr,to_ascii(aluno.ed47_v_nome)";
$sWhereMatricula  = " ed60_c_ativa='S' and ed60_c_situacao='MATRICULADO' and ed220_i_codigo in ($turmas) "; 
$sResult          = $clMatricula->sql_record($clMatricula->sql_query("",
                                                                     $sCampoMatricula,
                                                                     $sOrderMatricula,
                                                                     $sWhereMatricula
                                                                     )
                                            );
if ($clMatricula->numrows == 0) {
	
  db_fieldsmemory($sResult,0);
  ?>
  <table width='100%'>
   <tr>
    <td align='center'>
     <font color='#FF0000' face='arial'>
      <b>Nenhum registro encontrado.<br>
      <input type='button' value='Fechar' onclick='window.close()'></b>
     </font>
    </td>
   </tr>
  </table>
  <?
  exit;
}

$sResultParametros = $clEduParametros->sql_record($clEduParametros->sql_query("",
                                                                                "ed233_i_idadevotacao",
                                                                                "",
                                                                                "ed233_i_escola = $iEscola"
                                                                               )
                                                  );
                                                  
$sCampos             = "ed217_t_cabecalho,ed217_t_rodape,ed217_t_obs";
$sSqlDadosRelatModel = $clEduRelatmodel->sql_query("",$sCampos,"","ed217_i_codigo = 3");
$rsDadosRelatModel   = $clEduRelatmodel->sql_record($sSqlDadosRelatModel);

if ($clEduRelatmodel->numrows > 0) {
    
  $oDadosRelatModel  = db_utils::fieldsMemory($rsDadosRelatModel,0);
  $sCabecalho        = $oDadosRelatModel->ed217_t_cabecalho;
  
}
                                          
$fpdf = new FPDF();
$fpdf->Open();
$fpdf->AliasNbPages();
$fpdf->ln(5);
$fpdf->SetAutoPageBreak(false,1);
$fpdf->Addpage('P');  

$data         = date($sDataVotacao);
$sDataExtenso        = db_dataextenso(db_strtotime($sDataVotacao));
$mes_extenso  = array("01"=>"janeiro",
                      "02"=>"fevereiro",
                      "03"=>"mar�o",
                      "04"=>"abril",
                      "05"=>"maio",
                      "06"=>"junho",
                      "07"=>"julho",
                      "08"=>"agosto",
                      "09"=>"setembro",
                      "10"=>"outubro",
                      "11"=>"novembro",
                      "12"=>"dezembro"
                     );
$cidade = strtolower($mun_escola);     
$cidadeescola =   ucfirst($cidade);   
$sDataFinal = $cidadeescola.", ".$sDataExtenso;
db_fieldsmemory($sResult,0);
$fpdf->setfont('times','',12);
$fpdf->setXY(10,5);
$fpdf->cell(100,5,"Estabelecimento: ".$sNomeEscola,0,0,"J",0);
$fpdf->setXY(50,12);
$fpdf->cell(100,5,"ELEI��O DO DIRETOR ESCOLAR - Data da vota��o: $iData",0,1,"C",0);
$fpdf->setXY(10,20);
$fpdf->setfont('times','',12);
$fpdf->multicell(180,5,"                    ".$sCabecalho,0,"J",0,0);
$fpdf->setXY(70,40);
$fpdf->setfont('times','b',14);
$fpdf->cell(30,5,"Turma  $ed57_c_descr ",0,0,0);
$fpdf->setfont('times','',10);
$fpdf->cell(35,5," $ed11_c_descr    $ed10_c_abrev",0,1,0);
$fpdf->setfillcolor(223);
$fpdf->setY(45);
$fpdf->setfont('times','b',7);
$fpdf->cell(8,5,"C�d",1,0,"C",1);
$fpdf->cell(15,5,"Data Nasc.",1,0,"C",1);
$fpdf->cell(90,5,"Alunos Votantes",1,0,"C",1);
$fpdf->cell(82,5,"Assinaturas",1,1,"C",1);
db_fieldsmemory($sResult,0);
$iCodigo = $ed57_i_codigo;
$iTotal  = 0;
$iCount   = 0;

for ($x = 0; $x < $clMatricula->numrows; $x++) {

  db_fieldsmemory($sResult,$x);
  db_fieldsmemory($sResultParametros,0);

  if ($iCodigo != $ed57_i_codigo) {             
    if ($x != 0) {
    	
  	  $fpdf->setfont('times','',11);
  	  $final =$fpdf->getY();
      $fpdf->setY($final+2);
      $fpdf->cell(137,5,"Total : " .$iTotal,0,0,"L",0);
      $fpdf->cell(222,5,$sDataFinal ,0,0,"L",0);
      $fpdf->setfont('times','',10);
      $fim = $fpdf->getY();
      $fpdf->setY($fim+10);    
      $fpdf->setX(10);
      $fpdf->cell(45, 5, "____________________________________________________", 0, 0, "L", 0);
      $fpdf->setX(130);
      $fpdf->cell(65, 5, "_________________________________________", 0, 1, "L", 0);  
      $secretario = $fpdf->getY();
      $fpdf->setX(10);
      $fpdf->setfont('times','',10);
      $fpdf->multicell(75,4,$sSecret,0,"L",0,0);
      $fpdf->setY($secretario);
      $fpdf->setX(130);
      $fpdf->multicell(75,4,$sPresid,0,"L",0,0);
      $fpdf->setX(10);
      $fpdf->cell(65, 5, "Secret�rio(a) da Comiss�o proc. escolha Diretor Escolar", 0, 0,"L", 0);
      $fpdf->setX(130);
      $fpdf->cell(65, 5, "Presidente da Comiss�o proc. escolha Diretor Escolar", 0, 1, "L", 0); 
      $fpdf->setY(289);  
      $fpdf->line(10, $fpdf->getY(), 200, $fpdf->getY());
      $fpdf->setX(85);
      $fpdf->setfont('times','b',7);
      $fpdf->cell(40, 5, "RUA $rua_escola, $num_escola - $bairro_escola - $mun_escola / $uf_escola - $cep_escola 
                    Fone/Fax : $sTelEscola - e-mail: $email_escola", 0, 1, "C", 0
                 ); 
 	  $fpdf->ln(5);
      $fpdf->addpage('P');        
      	
    }  
               
    $fpdf->setfont('times','',12);
    $fpdf->setXY(10,5);
    $fpdf->cell(100,5,"Estabelecimento: ".$sNomeEscola,0,0,"J",0);
    $fpdf->setXY(50,12);
    $fpdf->cell(100,5,"ELEI��O DO DIRETOR ESCOLAR - Data da vota��o: $iData",0,1,"C",0);
    $fpdf->setXY(10,20);
    $fpdf->setfont('times','',12);
    $fpdf->multicell(180,5,"                    ".$sCabecalho,0,"J",0,0);
    $fpdf->setXY(70,40);
    $fpdf->setfont('times','b',14);
    $fpdf->cell(30,5,"Turma  $ed57_c_descr ",0,0,0);
    $fpdf->setfont('times','',10);
    $fpdf->cell(35,5," $ed11_c_descr    $ed10_c_abrev",0,1,0);
    $fpdf->setfillcolor(223);
    $fpdf->setY(45);
    $fpdf->setfont('times','b',7);
    $fpdf->cell(8,5,"C�d",1,0,"C",1);
    $fpdf->cell(15,5,"Data Nasc.",1,0,"C",1);
    $fpdf->cell(90,5,"Alunos Votantes",1,0,"C",1);
    $fpdf->cell(82,5,"Assinaturas",1,1,"C",1);
    $iCodigo = $ed57_i_codigo;    
    $iTotal = 0;
  }
   
  
  if ($idadealuno >= $ed233_i_idadevotacao) {
   	    	   	
   	$fpdf->setfont('times','',7);
    $fpdf->cell(8,6,$ed47_i_codigo,1,0,"R",0);
 	$fpdf->cell(15,6,db_formatar($ed47_d_nasc,'d'),1,0,"R",0);
 	$fpdf->cell(90,6,$ed47_v_nome,1,0,"L",0);
    $fpdf->cell(82,6,"",1,1,"L",0);
     
    if ($fpdf->getY() >= $fpdf->h - 30 ) {
   	
   	  $final =$fpdf->getY();
      $fpdf->setY($final+16);
      $fpdf->line(10, $fpdf->getY(), 285, $fpdf->getY());
      $fpdf->setX(110);
      $fpdf->setfont('times','b',7);
      $fpdf->cell(20, 5, "RUA $rua_escola, $num_escola - $bairro_escola - $mun_escola / $uf_escola - $cep_escola 
                        Fone/Fax : $sTelEscola - e-mail: $email_escola", 0, 1, "C", 0
                  );   
      $fpdf->Addpage('P');
      $fpdf->setfont('times','',12);
      $fpdf->setXY(10,5);
      $fpdf->cell(100,5,"Estabelecimento: ".$sNomeEscola,0,0,"J",0);
      $fpdf->setXY(50,12);
      $fpdf->cell(100,5,"ELEI��O DO DIRETOR ESCOLAR - Data da vota��o: $iData",0,1,"C",0);
      $fpdf->setXY(10,20);
      $fpdf->setfont('times','',12);
      $fpdf->multicell(180,5,"                    ".$sCabecalho,0,"J",0,0);
      $fpdf->setXY(70,40);
      $fpdf->setfont('times','b',14);
      $fpdf->cell(30,5,"Turma  $ed57_c_descr ",0,0,0);
      $fpdf->setfont('times','',10);
      $fpdf->cell(35,5," $ed11_c_descr    $ed10_c_abrev",0,1,0);
      $fpdf->setfillcolor(223);
      $fpdf->setY(45);
      $fpdf->setfont('times','b',7);
      $fpdf->cell(8,5,"C�d",1,0,"C",1);
      $fpdf->cell(15,5,"Data Nasc.",1,0,"C",1);
      $fpdf->cell(90,5,"Alunos Votantes",1,0,"C",1);
      $fpdf->cell(82,5,"Assinaturas",1,1,"C",1);
     }
     $iTotal++;
   }   
}
$fpdf->setfont('times','',11);
$final =$fpdf->getY();
$fpdf->setY($final+10);
$fpdf->cell(137,5,"Total : " .$iTotal,0,0,"L",0);
$fpdf->cell(222,5,$sDataFinal ,0,0,"L",0);
$fpdf->setfont('times','',10);
$fim = $fpdf->getY();
$fpdf->setY($fim+13);
$fpdf->setX(10);
$fpdf->cell(45, 5, "_________________________________________", 0, 0, "L", 0);
$fpdf->setX(130);
$fpdf->cell(65, 5, "_________________________________________", 0, 1, "L", 0);    
$secretario = $fpdf->getY();
$fpdf->setX(10);
$fpdf->setfont('times','',10);
$fpdf->multicell(75,4,$sSecret,0,"L",0,0);
$fpdf->setY($secretario);
$fpdf->setX(130);
$fpdf->multicell(75,4,$sPresid,0,"L",0,0);
$fpdf->setX(10);
$fpdf->cell(65, 5, "Secret�rio(a) da Comiss�o proc. escolha Diretor Escolar", 0, 0,"L", 0);
$fpdf->setX(130);
$fpdf->cell(65, 5, "Presidente da Comiss�o proc. escolha Diretor Escolar", 0, 1, "L", 0); 
$fpdf->setY(289);  
$fpdf->line(10, $fpdf->getY(), 200, $fpdf->getY());
$fpdf->setX(85);
$fpdf->setfont('times','b',7);
$fpdf->cell(40, 5, "RUA $rua_escola, $num_escola - $bairro_escola - $mun_escola / $uf_escola - $cep_escola 
                    Fone/Fax : $sTelEscola - e-mail: $email_escola", 0, 1, "C", 0
           ); 
           
$fpdf->Output();
?>