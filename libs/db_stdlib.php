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


set_time_limit(0);

/***
 *
 * Funcao para montar uma string com o backtrace do PHP **SEM PARAMETROS***
 * nas chamadas de fun��es e m�todos
 *
 */
function db_debug_backtrace() {
  $aBacktrace = debug_backtrace();
  $iCount     = count($aBacktrace);
  $sBacktrace = "";

  for($i=1; $i<$iCount; $i++) {
    $sBacktrace .=
      sprintf(" * #%s %s(%s) called at [%s:%d]\n",
        $i,
        $aBacktrace[$i]["function"],
        ($aBacktrace[$i]["args"]?"...":""),
        $aBacktrace[$i]["file"],
        $aBacktrace[$i]["line"]);
  }

  if($sBacktrace <> "") {
    $sBacktrace = "\n/***\n * e-cidade backtrace\n{$sBacktrace} */\n";
  }

  return $sBacktrace;
}


/***
 *
 * Funcao wrapper para executar a pg_query (PostgreSQL)
 *
 */
function db_query($param1, $param2=null, $param3="SQL"){

  $lMostraBackTrace = false;
  $sBackTrace = "";
  if ($lMostraBackTrace) {
    $sBackTrace = db_debug_backtrace();
  }

  $lExecutaAccount = true;
  $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);

  if (isset($lSessaoDesativarAccount) && $lSessaoDesativarAccount === true) {
    $lExecutaAccount = false;
  }

  if($param2==null){

    $dbsql    = $sBackTrace . $param1;
    if ( !$lExecutaAccount) {

      $aWordsBlock = array(" db_acount ",
                           " db_acountkey "
                          );

      foreach($aWordsBlock as $sWord) {

        $mAchouString = strpos($dbsql, $sWord);
        if ($mAchouString) {
          return true;
        }
      }
    }
    $dbresult = pg_query($dbsql);

  }else{

    $dbsql    = $sBackTrace . $param2;
    if ($lExecutaAccount) {
      $dbresult = pg_query($param1, $dbsql);
    }
  }

  db_tracelog($param3, $dbsql,(!$dbresult?true:false));

  return $dbresult;
}

// Retorna a quantidade de dias do m�s no ano informadoo
function db_dias_mes($ano,$mes,$ret_data = false){
  $data = getdate(mktime(0,0,0,$mes+1,0,$ano));
  if($ret_data == false){
    return $data["mday"];
  }else{
    return date('Y-m-d',mktime(0,0,0,$mes,$data["mday"],$ano));
  }
}

/*
 * Fun��o para validar PIS
 */
    function checkPIS($pis){
      $pis = str_pad(ereg_replace('[^0-9]', '', $pis), 11, '0', STR_PAD_LEFT);

      if (strlen($pis) != 11 || intval($pis) == 0) {
          return false;
      } else {
          for ($d = 0, $p = 3, $c = 0; $c < 10; $c++) {
              $d += $pis{$c} * $p;
              $p  = ($p < 3) ? 9 : --$p;
          }

          $d = ((10 * $d) % 11) % 10;

          return ($pis{$c} == $d) ? true : false;
      }
    }

function db_diasemana($dia,$opcao="s"){
  // Opcao = S - Sigla
  //         E - Extenso
  $arr_SdiasUS = array(
  "Sun"=>"Dom",
  "Mon"=>"Seg",
  "Tue"=>"Ter",
  "Wed"=>"Qua",
  "Thu"=>"Qui",
  "Fri"=>"Sex",
  "Sat"=>"Sab"
                      );
  $arr_EdiasUS = array(
  "Sun"=>"Domingo",
  "Mon"=>"Segunda",
  "Tue"=>"Terca",
  "Wed"=>"Quarta",
  "Thu"=>"Quinta",
  "Fri"=>"Sexta",
  "Sat"=>"Sabado"
                      );
  if($opcao == "s"){
    return $arr_SdiasUS[date("D",mktime(0,0,0,db_subdata($dia,"m"),db_subdata($dia,"d"),db_subdata($dia,"a")))];
  }else{
    return $arr_EdiasUS[date("D",mktime(0,0,0,db_subdata($dia,"m"),db_subdata($dia,"d"),db_subdata($dia,"a")))];
  }
}
// Se opcao == "d" retorna o dia da data
//          == "m" retorna o m�s da data
//          == "a" retorna o ano da data
// formatar == "b" default - Data no formato do banco YYYY-mm-dd
//          == "f" data formatada dd/mm/YYYY
//          == "t" timestamp
function db_subdata($data,$opcao,$formatar="b"){
  if($formatar == "b"){
    $data = db_formatar($data,"d");
  }else if($formatar == "t"){
    $data = date("d/m/Y",$data);
  }
  $arr_data = split("/",$data);
  if(trim($arr_data[0]) == "" || !isset($arr_data[1]) || !isset($arr_data[2])){
    return 0;
  }
  if($opcao == "d"){
    return db_formatar($arr_data[0],"s","0",2,"e",0);
  }else if($opcao == "m"){
    return db_formatar($arr_data[1],"s","0",2,"e",0);
  }else if($opcao == "a"){
    return $arr_data[2];
  }
}

// classe para download de arquivos
class cl_download {
  var $diretorio = 'tmp/';
  var $arquivo = null;
  var $texto = "Clique Aqui";
  function cl_download() {
    $this->criaarquivo();
  }
  function criaarquivo() {
    $this->arquivo = "rp".rand(1, 10000)."_".time();
  }
  function download() {
    echo "<a href='db_download.php?arquivo=".$this->diretorio.$this->arquivo."'>".$this->texto."</a>";
  }
}

//Classe que verifica se o boletim j� foi liberado
class cl_verificaboletim {
  function cl_verificaboletim($clboletim) {
    global $k11_numbol;
    $data = date("Y-m-d", db_getsession("DB_datausu"));
    $instit = db_getsession("DB_instit");
    $result = $clboletim->sql_record($clboletim->sql_query_file(null, null, "k11_numbol", "", "k11_data='$data' and k11_instit=$instit and k11_libera = 't'"));
    $numrows = $clboletim->numrows;
    if ($numrows > 0) {
      db_fieldsmemory($result, 0);
      db_redireciona("db_erromenu.php?k11_numbol=$k11_numbol");
    }
  }
}

/**
 * Busca o ano atual da folha
 *
 * @return integer
 */
function db_anofolha() {

  global $max;
  require_once 'model/pessoal/std/DBPessoal.model.php';

  try {

    /**
     * Mes atual da folha
     */
    $iMes = str_pad(DBPessoal::getMesFolha(), 2, "0", STR_PAD_LEFT);

    /**
     * Ano e mes da folha concatenados
     */
    $max = DBPessoal::getAnoFolha() . $iMes;

    return DBPessoal::getAnoFolha();

  } catch ( Exception $oErro ) {
    return 0;
  }

}

/**
 * Busca o mes atual da folha
 *
 * @return integer
 */
function db_mesfolha() {

  global $max;
  require_once 'model/pessoal/std/DBPessoal.model.php';

  try {

    /**
     * Mes atual da folha
     */
    $iMes = str_pad(DBPessoal::getMesFolha(), 2, "0", STR_PAD_LEFT);

    /**
     * Ano e mes da folha concatenados
     */
    $max = DBPessoal::getAnoFolha() . $iMes;

    return $iMes;

  } catch ( Exception $oErro ) {
    return 0;
  }

}

function db_hora($id_timestamp = 0, $formato = "H:i") {
                //#00#//db_hora
                //#10#// retorna a hora do servidor em no formato HH:MM
                //#15#//$hora = db_hora($timestamp,$formato);
                //#20#//$id_timestamp =        Data e hora no formato timestamp
                //#20#//$formato      = Formato do retorno da hora ou data
                //#20#//                Padrao: H:i - Hora e minuto com :.
                //#99#//Os tipos de formato de retorno s�o:
                //#99#//a        Meridiano da Hora no formato am ou pm
                //#99#//A        Meridiano da Hora no formato AM or PM
                //#99#//B        Hora na internet de 000 a 999
                //#99#//c        Formato ISO 8601 da data (PHP 5) 2004-02-12T15:19:21+00:00
                //#99#//d        Dia do Mes com dois digitos 01 to 31
                //#99#//D        3 primeiras letras do dia
                //#99#//F        Nome do mes
                //#99#//g        Hora no formato de 1 a 12
                //#99#//G        Hora no formato 24 horas ( 0 a 23 )
                //#99#//h        Hora no formato 12 com zeros 01 through 12
                //#99#//H        Hora no formato 24 horas 00 through 23
                //#99#//i        Minutos com zero a esquerda 00 to 59
                //#99#//j        Dia do mes sem zero a esquerda 1 to 31
                //#99#//l       Nome Dia da semana
                //#99#//m        Mes numericpo com dois digitos  01 a 12
                //#99#//M        3 primeiras letras do nome do mes Jan through Dec
                //#99#//n        Mes numerico sem zero a esquerda 1 a 12
                //#99#//O        Diferen�a para hora Greenwich (GMT) em horas        Example: +0200
                //#99#//r        Data no formato RFC 2822 Exemplo: Thu, 21 Dec 2000 16:01:07 +0200
                //#99#//s        Segundos com zeros a esquerda 00 through 59
                //#99#//S        Ordinal sufixo em Ingles do mes, 2 caracteres st, nd, rd or th.
                //#99#//t        Numero de dias do mes 28 a 31
                //#99#//T        Zona da hora setada na m�quina        Exemplo: EST, MDT ...
                //#99#//U        Segundos em rela��o a 1/1/1970  timestamp.
                //#99#//w        Nnumero do dia da semana 0 a 6
                //#99#//W        Numero da semana do ano conforme ISO-8601
                //#99#//Y        Ano com 4 digitos Exemplo: 1999 or 2003
                //#99#//y        Ano em 2 digitos Exemplo: 99 or 03
                //#99#//z        Dia do ano da data 0 a 365
                //#99#//Z        Hora em segundos do timezona. -43200 a 43200
                if ($id_timestamp != 0) {
                  return date($formato, $id_timestamp);
                } else {
                  return date($formato);
                }
}
function db_verifica_ip_banco() {
  //#00#//db_verifica_ip_anco
  //#10#//Verifica se o IP que esta acessando poder� abrir o dbportal, pesquisando o arquivo db_acessa
  //#10#//e verificando as permiss�es
  //#15#//db_verifica_ip();
  //#40#//"1" para acesso permitido e "0" para n�o permitido
  //#99#//O arquido db_acessa possue a matriz com os IPs que poder�o efetuar o acesso
  //#99#//Nome da matriz: db_acessa
  //#99#//db_acessa[1][1] = M�scara do IP que pode acessar, quando colocado um astesrisco(*) no final, o sistema
  //#99#//                  testa o tamanho do IP at� o asterisco e desconsidera a partir dele.
  //#99#//db_acessa[1][2] = Campo L�gico, quando verdadeiro, poder� acessar, o IP ou m�scara do IP e quando
  //#99#//                  falso nao poder� acessar o db_portal
  global $SERVER, $HTTP_SERVER_VARS, $db48_ip, $db47_id_usuario;
  if (isset ($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $db_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
  } else {
    $db_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
  }

  $usuario_liberado = '0';
  // verifica pelo codigo do usu�rio
  $sql = "select db47_id_usuario, db48_ip
          from db_sysregrasacesso
               inner join db_sysregrasacessousu  on db46_idacesso = db47_idacesso
               inner join db_sysregrasacessoip   on db46_idacesso = db48_idacesso
               left join db_sysregrasacessocanc on db46_idacesso = db49_idacesso
          where db49_idacesso is null
            and db47_id_usuario = ".db_getsession("DB_id_usuario")."
            and (( db46_dtinicio  < '".date('Y-m-d')."' and db46_datafinal > '".date('Y-m-d')."' )
            or  ( db46_dtinicio = '".date('Y-m-d')."' and db46_horaini   <= '".date("G:i")."' )
            or  ( db46_datafinal = '".date('Y-m-d')."' and db46_horafinal >= '".date("G:i")."' ))
            ";
  $result = db_query($sql);
  $numrows = pg_numrows($result);
  for ($i = 0; $i < $numrows; $i ++) {
    db_fieldsmemory($result,$i);
    if( $db48_ip == "" ){
      $usuario_liberado = '1';
    }else{
      if ( $db48_ip == $db_ip) {

        $usuario_liberado = '1';

      }else{

        $aster = strpos("#".$db48_ip, "*");
        if ($aster != 0) {
          $quantos = substr($db48_ip, 0, $aster -1);
          if (substr($db48_ip, 0, strlen($quantos)) == substr($db_ip, 0, strlen($quantos))) {
            $usuario_liberado = '1';
          }
        }
      }
    }
  }

  // verifica somente ips liberados sem usuarios
  $sql = "select  db48_ip
          from db_sysregrasacesso
               inner join db_sysregrasacessoip   on db46_idacesso = db48_idacesso
               left join db_sysregrasacessousu  on db46_idacesso = db47_idacesso
               left join db_sysregrasacessocanc on db46_idacesso = db49_idacesso
          where db49_idacesso is null
            and db47_id_usuario is null
            and (( db46_dtinicio  < '".date('Y-m-d')."' and db46_datafinal > '".date('Y-m-d')."' )
            or  ( db46_dtinicio = '".date('Y-m-d')."' and db46_horaini   <= '".date("H:i")."' )
            or  ( db46_datafinal = '".date('Y-m-d')."' and db46_horafinal >= '".date("H:i")."' ))"
            ;
  $result = db_query($sql);
  $numrows = pg_numrows($result);
  for ($i = 0; $i < $numrows; $i ++) {
    db_fieldsmemory($result,$i);
    if ( $db48_ip == $db_ip) {

      $usuario_liberado = '1';

    }else{

      $aster = strpos("#".$db48_ip, "*");
      if ($aster != 0) {
        $quantos = substr($db48_ip, 0, $aster -1);
        if (substr($db48_ip, 0, strlen($quantos)) == substr($db_ip, 0, strlen($quantos))) {
          $usuario_liberado = '1';
        }
      }
    }
  }
  return $usuario_liberado;

}

function db_verifica_ip() {
  //#00#//db_verifica_ip
  //#10#//Verifica se o IP que esta acessando poder� abrir o dbportal, pesquisando o arquivo db_acessa
  //#10#//e verificando as permiss�es
  //#15#//db_verifica_ip();
  //#40#//"1" para acesso permitido e "0" para n�o permitido
  //#99#//O arquido db_acessa possue a matriz com os IPs que poder�o efetuar o acesso
  //#99#//Nome da matriz: db_acessa
  //#99#//db_acessa[1][1] = M�scara do IP que pode acessar, quando colocado um astesrisco(*) no final, o sistema
  //#99#//                  testa o tamanho do IP at� o asterisco e desconsidera a partir dele.
  //#99#//db_acessa[1][2] = Campo L�gico, quando verdadeiro, poder� acessar, o IP ou m�scara do IP e quando
  //#99#//                  falso nao poder� acessar o db_portal
  global $SERVER, $HTTP_SERVER_VARS;
  if (isset ($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $db_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
  } else {
    $db_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
  }

  include ("db_acessa.php");

  for ($i = 1; $i -1 < sizeof($db_acessa); $i ++) {
    if ($db_acessa[$i][1] == $db_ip) {
      if ($db_acessa[$i][2] == false) {
        return '0';
      }
    }
  }
  $pode_acessar = "0";
  for ($i = 1; $i -1 < sizeof($db_acessa); $i ++) {
    $aster = strpos("#".$db_acessa[$i][1], "*");
    if ($aster != 0) {
      $quantos = substr($db_acessa[$i][1], 0, $aster -1);
      if (substr($db_acessa[$i][1], 0, strlen($quantos)) == substr($db_ip, 0, strlen($quantos))) {
        if ($db_acessa[$i][2] == false) {
          return '0';
        }
        $pode_acessar = "1";
      }
    }
  }
  return $pode_acessar;

}

class cl_abre_arquivo {
  //|00|//cl_abre_arquivo
  //|10|//Abre um determinado arquivo no diret�rio DOCUMENT_ROOT do servidor onde estiver rodando o PHP
  //|15|//$clabre_arquivo = new cl_abre_arquivo($nomearq="");
  //|20|//Nome do Arquivo : Nome do arquivo para abrir e colocar na propriedade arquivo
  var $nomearq = null;
  //|30|//nomearq : Nome do arquivo com o caminho completo
  var $arquivo = null;
  //|30|//arquivo : FD do arquivo - retorno da fun��o fopen()
  function cl_abre_arquivo($nomearq = "") {
    //#00#//cl_abre_arquivo
    //#10#//M�todo para abrir um arquivo
    //#15#//cl_abre_arquivo($nomearq="");
    //#20#//Nome do Arquivo : Nome do arquivo a ser gerado, quando em branco, o sistema gera um arquivo aleat�rio
    //#20#//                  com a fun��o tempnam()
    //#40#//true se o arquivo foi gerado ou false se nao foi gerado
    global $HTTP_SERVER_VARS;
    $Dirroot = "";
    if ($nomearq == "") {
      $Dirroot = substr($HTTP_SERVER_VARS['DOCUMENT_ROOT'], 0, strrpos($HTTP_SERVER_VARS['DOCUMENT_ROOT'], "/"))."/";
      $nomearq = tempnam("tmp", "");
    }
    $this->arquivo = fopen($Dirroot.$nomearq, "w");
    $this->nomearq = $Dirroot.$nomearq;
    if ($this->arquivo == false) {
      return false;
    } else {
      return true;
    }
  }
  //|XX|//
}


function db_mes($xmes,$tipo=0) {
  //#00#//db_mes
  //#10#//Retorna o nome do mes por extenso
  //#15#//db_mes($mes);
  //#20#//mes : N�mero do mes 01,02,03,04,05,06,07,08,09,10,11,12 como string
  //#20#//      N�mero do mes 1,2,3,4,5,6,7,8,9,10,11,12 como numero
  //#20#//Tipo : 0 - minusculo  1 - MAIUSCULO  2 - Primeira Maiusculo
  //#40#//Nome do mes por extenso
  $Mes = "";
  if ($xmes == '01' || $xmes == 1) {
    $Mes = 'janeiro';
  } else
  if ($xmes == '02' || $xmes == 2) {
    $Mes = 'fevereiro';
  } else
  if ($xmes == '03 '|| $xmes == 3 ) {
    $Mes = 'mar�o';
  } else
  if ($xmes == '04' ||$xmes == 4) {
    $Mes = 'abril';
  } else
  if ($xmes == '05' || $xmes == 5) {
    $Mes = 'maio';
  } else
  if ($xmes == '06' || $xmes == 6) {
    $Mes = 'junho';
  } else
  if ($xmes == '07' || $xmes == 7) {
    $Mes = 'julho';
  } else
  if ($xmes == '08' || $xmes == 8) {
    $Mes = 'agosto';
                } else
                if ($xmes == '09' || $xmes == 9) {
                  $Mes = 'setembro';
                } else
                if ($xmes == '10' || $xmes == 10) {
                  $Mes = 'outubro';
                } else
                if ($xmes == '11' || $xmes == 11) {
                  $Mes = 'novembro';
                } else
                if ($xmes == '12' || $xmes == 12) {
                  $Mes = 'dezembro';
                }
                if($tipo==0){
                  return $Mes;
                }elseif ($tipo==1){
                  return strtoupper(str_replace("�","�",$Mes));
                }else{
                  return ucfirst($Mes);
                }
}




function db_geratexto($texto) {
  $texto .= "#";
  $txt = split("#", $texto);
  $texto1 = '';
  for ($x = 0; $x < sizeof($txt); $x ++) {
    if (substr($txt[$x], 0, 1) == "$") {
      $txt1 = substr($txt[$x], 1);
      global $$txt1;
      $texto1 .= $$txt1;
    } else
    if ((substr($txt[$x], 0, 2) == '\n')or(substr($txt[$x], 0, 4) == '<br>')) {
      $texto1 .= "\n";
    } else
                                if (substr($txt[$x], 0, 2) == '\t') {
                                  $texto1 .= "\t";
                                } else {
                                  $texto1 .= $txt[$x];
                                }
  }
  return $texto1;
}

class janela {
  //|00|//janela
  //|10|//Abre um determinado arquivo no diret�rio DOCUMENT_ROOT do servidor onde estiver rodando o PHP
  //|15|//[variavel] = new janela($nome,$arquivo);
  //|20|//nome    : Nome da janela a ser criada como objeto
  //|20|//arquivo : Arquivo a ser executado no iframe
  //|99|//(esta funcao n�o esta mais em uso, verifica a funcao java script |js_OpenJanelaIframe|)
  //|99|//Exemplo:
  //|99|//$func_iframe = new janela('db_iframe',''); // abre a classe janela
  //|99|//$func_iframe->posX=1;                      // seta a posicao que dever� abrir em rela��o a esquerda
  //|99|//$func_iframe->posY=20;                     // seta a posi��o que dever� abrir em rela��o ao topo
  //|99|//$func_iframe->largura=780;                 // seta a largura do formul�rio
  //|99|//$func_iframe->altura=430;                  // seta a altura do formul�rio
  //|99|//$func_iframe->titulo='Pesquisa';           // seta o titulo da janela
  //|99|//$func_iframe->iniciarVisivel = false;      // seta se mostrar� ou n�o a janela (neste caso n�o)
  //|99|//$func_iframe->mostrar();                   // escreve o objeto iframe na tela

  var $nome;
  var $arquivo;
  var $iniciarVisivel = true;
  var $largura = "780";
  var $altura = "430";
  var $posX = "1";
  var $posY = "20";
  var $scrollbar = "auto"; // pode ser tb, 0 ou 1
  var $corFundoTitulo = "#2C7AFE";
  var $corTitulo = "white";
  var $fonteTitulo = "Arial, Helvetica, sans-serif";
  var $tamTitulo = "11";
  var $titulo = "DBSeller Inform�tica Ltda";
  var $janBotoes = "111";

  function janela($nome, $arquivo) {
    $this->nome = $nome;
    $this->arquivo = $arquivo;
  }

  function mostrar() {
    $this->largura = "100%";
    $this->altura = "100%";

    if ($this->iniciarVisivel == true)
    $this->iniciarVisivel = "visible";
    else
    $this->iniciarVisivel = "hidden";
    ?>
        <div id="Jan<? echo $this->nome ?>" style=" background-color: #c0c0c0;border: 0px outset #666666;position:absolute; left:<? echo $this->posX ?>px; top:<? echo $this->posY ?>px; width:<? echo $this->largura ?>; height:<? echo $this->altura ?>; z-index:1; visibility: <? echo $this->iniciarVisivel ?>;"><table width="100%" height="100%" style="border-color: #f0f0f0 #606060 #404040 #d0d0d0;border-style: solid;  border-width: 2px;"  border="0" cellspacing="0" cellpadding="2"><tr><td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr id="CF<? echo $this->nome ?>" style="white-space: nowrap;background-color:<? echo $this->corFundoTitulo ?>"><td nowrap onmousedown="js_engage(document.getElementById('Jan<? echo $this->nome ?>'),event)" onmouseup="js_release(document.getElementById('Jan<? echo $this->nome ?>'),event)" onmousemove="js_dragIt(document.getElementById('Jan<? echo $this->nome ?>'),event)" onmouseout="js_release(document.getElementById('Jan<? echo $this->nome ?>'),event)" width="80%" style="cursor:hand;font-weight: bold;color: <? echo $this->corTitulo ?>;font-family: <? echo $this->fonteTitulo ?>;font-size: <? echo $this->tamTitulo ?>px">&nbsp;<? echo $this->titulo ?></td><td width="20%" align="right" valign="middle" nowrap><?$kp=0x4;$m = $kp & $this->janBotoes;$kp >>= 1;?><img <? echo $m?'style="cursor:hand"':"" ?> src=<? echo $m?"imagens/jan_mini_on.gif":"imagens/jan_mini_off.gif" ?> title="Minimizar" border="0" onClick="js_MinimizarJan(this,'<? echo $this->nome ?>')"><?$m = $kp & $this->janBotoes;$kp >>= 1;?><img <? echo $m?'style="cursor:hand"':"" ?> src=<? echo $m?"imagens/jan_max_on.gif":"imagens/jan_max_off.gif" ?> title="Maximizar" border="0" onClick="js_MaximizarJan(this,'<? echo $this->nome ?>')"><?$m = $kp & $this->janBotoes;$kp >>= 1;?><img <? echo $m?'style="cursor:hand"':"" ?> src=<? echo $m?"imagens/jan_fechar_on.gif":"imagens/jan_fechar_off.gif" ?> title="Fechar" border="0" onClick="js_FecharJan(this,'<? echo $this->nome ?>')"></td></tr></table></td></tr><tr><td width="100%" height="100%"><iframe frameborder="1" style="border-color:#C0C0F0" height="100%" width="100%" id="IF<? echo $this->nome ?>" name="IF<? echo $this->nome ?>" scrolling="<? echo $this->scrollbar ?>" src="<? echo $this->arquivo ?>"></iframe></td></tr></table></div><script><? echo $this->nome ?> = new janela(document.getElementById('Jan<? echo $this->nome ?>'),document.getElementById('CF<? echo $this->nome ?>'),IF<? echo $this->nome ?>);</script>
                                        <?


}
//|XX|//
}

//////////// CLASSE ROTULO  ///////////
/// ESTA CLASSE CRIA AS VARIAVEIS DE LABEL E TITLE DAS P�GINAS ///
class rotulo {
  //|00|//rotulo
  //|10|//Esta classe gera as vari�veis de controle do sistema de uma determinada tabela
  //|15|//[variavel] = new rotulo($tabela);
  //|20|//tabela  : Nome da tabela a ser pesquisada
  //|40|//Gera todas as vari�veis de controle dos campos
  //|99|//
  var $tabela;
  function rotulo($tabela) {
    $this->tabela = $tabela;
  }
  function rlabel($nome = "") {
    //#00#//rlabel
    //#10#//Este m�todo gera o label do campo ou campos para relat�rio
    //#15#//rlabel($nome);
    //#20#//nome  : Nome do campo a ser gerado o label para relat�rio
    //#20#//        Se n�o for informado campo, ser� gerado de todos os campos
    //#40#//Gera a vari�vel label do relatorio do campo rotulorel
    //#99#//A vari�vel ser� o "RL" mais o nome do campo
    //#99#//Exemplo : campo z01_nome ficar� RLz01_nome
    $sCampoTrim = trim($nome);
    $result = db_query("select c.rotulorel
                         from db_syscampo c
                              inner join db_sysarqcamp s on s.codcam = c.codcam
                              inner join db_sysarquivo a on a.codarq = s.codarq
                        where a.nomearq = '".$this->tabela."'
                        ". ($sCampoTrim != "" ? "and c.nomecam = '${sCampoTrim}'" : ""));
    $numrows = pg_numrows($result);
    for ($i = 0; $i < $numrows; $i ++) {
      /// variavel para colocar como label de campo
      $variavel = "RL".trim(pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = ucfirst(trim(pg_result($result, $i, "rotulorel")));
    }
  }
  function label($nome = "") {
    //#00#//label
    //#10#//Este m�todo gera o label do arquivo ou de um campo para os formul�rios
    //#15#//label($nome);
    //#20#//nome  : Nome do campo a ser gerado as vari�veis de controle
    //#20#//        Se n�o informado o campo, ser� gerado de todos os campos
    //#99#//Nome das vari�veis geradas:
    //#99#//"I" + nome do campo -> Tipo de consistencia javascript a ser gerada no formul�rio (|aceitatipo|)
    //#99#//"A" + nome do campo -> Variavel para determinar o autocomplete no objeto (!autocompl|)
    //#99#//"U" + nome do campo -> Variavel para preenchimento obrigatorio do campo (|nulo|)
    //#99#//"G" + nome do campo -> Variavel para colocar se letras do objeto devem ser maiusculo ou n�o (|maiusculo|)
    //#99#//"S" + nome do campo -> Variavel para colocar mensagem de erro do javascript de preenchimento de campo (|rotulo|)
    //#99#//"L" + nome do campo -> Variavel para colocar como label de campo (|rotulo|)
    //#99#//                       Coloca o campo com a primeira letra maiuscula e entre tags strong (negrito) (|rotulo|)
    //#99#//"T" + nome do campo -> Variavel para colocat na tag title dos campos (|descricao|)
    //#99#//"M" + nome do campo -> Variavel para incluir o tamanho da propriedade maxlength dos campos (|tamanho|)
    //#99#//"N" + nome do campo -> Variavel para controle da cor de fundo quando o  campo aceitar nulo (|nulo|)
    //#99#//                       style="background-color:#E6E4F1";
    //#99#//"RL"+ nome do campo -> Variavel para colocar como label de campo nos relatorios
    //#99#//"TC"+ nome do campo -> Variavel com o tipo de campo do banco de dados

    //        $result = db_query("select c.descricao,c.rotulo,c.nomecam,c.tamanho,c.nulo,c.maiusculo,c.autocompl,c.conteudo,c.aceitatipo,c.rotulorel
    //                                   from db_syscampo c
    //                                                   inner join db_sysarqcamp s
    //                                                   on s.codcam = c.codcam
    //                                                   inner join db_sysarquivo a
    //                                                   on a.codarq = s.codarq
    //                                                   where a.nomearq = '".$this->tabela."'
    //                                                   ". ($nome != "" ? "and trim(c.nomecam) = trim('$nome')" : ""));
    $sCampoTrim = trim($nome);
    $result = db_query("select c.descricao,c.rotulo,c.nomecam,c.tamanho,c.nulo,c.maiusculo,c.autocompl,c.conteudo,c.aceitatipo,c.rotulorel
                         from db_sysarquivo a
                              inner join db_sysarqcamp s on s.codarq = a.codarq
                              inner join db_syscampo c on c.codcam = s.codcam
                        where a.nomearq = '".$this->tabela."'
                        ". ($sCampoTrim != "" ? "and c.nomecam = '${sCampoTrim}'" : ""));
    $numrows = pg_numrows($result);
    for ($i = 0; $i < $numrows; $i ++) {
      /// variavel com o tipo de campo
      $variavel = trim("I".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "aceitatipo");
      /// variavel para determinar o autocomplete
      $variavel = trim("A".pg_result($result, $i, "nomecam"));
      global $$variavel;
      if (pg_result($result, $i, "autocompl") == 'f') {
        $$variavel = "off";
      } else {
        $$variavel = "on";
      }
      /// variavel para preenchimento obrigatorio
      $variavel = trim("U".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "nulo");
      /// variavel para colocar maiusculo
      $variavel = trim("G".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "maiusculo");
      /// variavel para colocar no erro do javascript de preenchimento de campo
      $variavel = trim("S".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "rotulo");
      /// variavel para colocar como label de campo
      $variavel = trim("L".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = "<strong>".ucfirst(pg_result($result, $i, "rotulo")).":</strong>";

      /// variavel para colocar como label de campo
      $variavel = trim("LS".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = ucfirst(pg_result($result, $i, "rotulo"));

      /// vaariavel para colocat na tag title dos campos
      $variavel = trim("T".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = ucfirst(pg_result($result, $i, "descricao"))."\n\nCampo:".pg_result($result, $i, "nomecam");
      /// variavel para incluir o tamanhoda tag maxlength dos campos
      $variavel = trim("M".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "tamanho");
      /// variavel para controle de campos nulos
      $variavel = trim("N".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "nulo");
      if ($$variavel == "t")
      $$variavel = "style=\"background-color:#E6E4F1\"";
      else
      $$variavel = "";
      /// variavel para colocar como label de campo nos relatorios
      $variavel = trim("RL".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = ucfirst(pg_result($result, $i, "rotulorel"));
      /// variavel para colocar o tipo de campo
      $variavel = "TC".trim(pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "conteudo");

    }
  }
  function tlabel($nome = "") {
    //#00#//tlabel
    //#10#//Este m�todo gera o label do arquivo
    //#15#//tlabel($nome);
    //#20#//nome  : Nome do arquivo para ser gerado o label
    //#40#//Gera a vari�vel label do arquivo "L" + nome do arquivo
    //#99#//Vari�veis geradas:
    //#99#//"L" + nome do arquivo -> Label do arquivo
    //#99#//"T" + nome do arquivo -> Texto para a tag title

    $result = db_query("select c.nomearq,c.descricao,c.nomearq,c.rotulo
                         from db_sysarquivo c
                        where c.nomearq = '".$this->tabela."'");
    $numrows = pg_numrows($result);
    if ($numrows > 0) {
      $variavel = trim("L".pg_result($result, 0, "nomearq"));
      global $$variavel;
      $$variavel = "<strong>".pg_result($result, 0, "rotulo").":</strong>";
      $variavel = trim("T".pg_result($result, 0, "nomearq"));
      global $$variavel;
      $$variavel = pg_result($result, 0, "descricao");
    }
  }
  //|XX|//
}

class rotulocampo {
  //|00|//rotulocampo
  //|10|//Esta classe gera as vari�veis de controle do sistema de uma determinada tabela
  //|15|//[variavel] = new rotulocampo($campo);
  //|20|//campo  : Nome do campo a ser pesquisada
  //|40|//Gera todas as vari�veis de controle do campo
  //|99|//Exemplo:
  //|99|//[variavel] = new rotulocampo("z01_nome");
  //|99|//ou
  //|99|//[variavel] = new rotulocampo();
  //|99|//[variavel]->label("z01_nome");
  function label($campo = "") {
    //#00#//label
    //#10#//Este m�todo gera o label do campo
    //#15#//label($campo);
    //#20#//nome  : Nome do campo a ser gerado as vari�veis de controle
    //#99#//Nome das vari�veis geradas:
    //#99#//"I" + nome do campo -> Tipo de consistencia javascript a ser gerada no formul�rio (|aceitatipo|)
    //#99#//"A" + nome do campo -> Variavel para determinar o autocomplete no objeto (!autocompl|)
    //#99#//"U" + nome do campo -> Variavel para preenchimento obrigatorio do campo (|nulo|)
    //#99#//"G" + nome do campo -> Variavel para colocar se letras do objeto devem ser maiusculo ou n�o (|maiusculo|)
    //#99#//"S" + nome do campo -> Variavel para colocar mensagem de erro do javascript de preenchimento de campo (|rotulo|)
    //#99#//"L" + nome do campo -> Variavel para colocar como label de campo (|rotulo|)
    //#99#//                       Coloca o campo com a primeira letra maiuscula e entre tags strong (negrito) (|rotulo|)
    //#99#//"T" + nome do campo -> Variavel para colocat na tag title dos campos (|descricao|)
    //#99#//"M" + nome do campo -> Variavel para incluir o tamanho da propriedade maxlength dos campos (|tamanho|)
    //#99#//"N" + nome do campo -> Variavel para controle da cor de fundo quando o  campo aceitar nulo (|nulo|)
    //#99#//                       style="background-color:#E6E4F1";
    //#99#//"RL"+ nome do campo -> Variavel para colocar como label de campo nos relatorios
    //#99#//"TC"+ nome do campo -> Variavel com o tipo de campo do banco de dados
    //#99#//"LS"+ nome do campo -> Variavel para colocar como label de campo sem as tags STRONG

    $sCampoTrim = trim($campo);
    $result = db_query("select c.descricao,c.rotulo,c.nomecam,c.tamanho,c.nulo,c.maiusculo,c.autocompl,c.conteudo,c.aceitatipo,c.valorinicial,c.rotulorel
                         from db_syscampo c
                        where c.nomecam = '${sCampoTrim}'");
    $numrows = pg_numrows($result);
    for ($i = 0; $i < $numrows; $i ++) {

      /// variavel com o tipo de campo
      $variavel = trim("I".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "aceitatipo");
      /// variavel para determinar o autocomplete
      $variavel = trim("A".pg_result($result, $i, "nomecam"));
      global $$variavel;
      if (pg_result($result, $i, "autocompl") == 'f') {
        $$variavel = "off";
      } else {
        $$variavel = "on";
      }
      /// variavel para preenchimento obrigatorio
      $variavel = trim("U".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "nulo");
      /// variavel para colocar maiusculo
      $variavel = trim("G".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "maiusculo");
      /// variavel para colocar no erro do javascript de preenchimento de campo
      $variavel = trim("S".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "rotulo");
      /// variavel para colocar como label de campo
      $variavel = trim("L".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = "<strong>".ucfirst(pg_result($result, $i, "rotulo")).":</strong>";

      /// variavel para colocar como label de campo
      $variavel = trim("LS".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = ucfirst(pg_result($result, $i, "rotulo"));

      /// vaariavel para colocat na tag title dos campos
      $variavel = trim("T".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = ucfirst(pg_result($result, $i, "descricao"))."\n\nCampo:".pg_result($result, $i, "nomecam");
      /// variavel para incluir o tamanhoda tag maxlength dos campos
      $variavel = trim("M".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "tamanho");
      /// variavel para controle de campos nulos
      $variavel = trim("N".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "nulo");
      if ($$variavel == "t")
      $$variavel = "style=\"background-color:#E6E4F1\"";
      else
      $$variavel = "";
      if ('DBtxt' == substr(trim(pg_result($result, $i, "nomecam")), 0, 5)) {
        $variavel = trim(pg_result($result, $i, "nomecam"));
        global $$variavel;
        $$variavel = pg_result($result, $i, "valorinicial");
      }
      /// variavel para colocar como label de campo nos relatorios
      $variavel = trim("RL".pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = ucfirst(pg_result($result, $i, "rotulorel"));
      /// variavel para colocar o tipo de campo
      $variavel = "TC".trim(pg_result($result, $i, "nomecam"));
      global $$variavel;
      $$variavel = pg_result($result, $i, "conteudo");

    }
  }
  //|XX|//
}
class rotulolov {
  //|00|//rotullov
  //|10|//Esta classe gera as vari�veis para titulo, descricao e tamanho para rotina |db_lovrot|
  //|15|//[variavel] = new rotulolov;
  //|30|//titulo    : Propriedade que recebe o conteudo do campo |rotulo| da documenta��o
  //|30|//descricao : Propriedade que recebe o conteudo do campo |descricao| da documenta��o
  //|30|//tamanho   : Propriedade que recebe o conteudo do campo |tamanho| da documenta��o
  //|99|//Exemplo:
  //|99|//[variavel] = new rotulolov;
  //|99|//[variavel]->label("z01_nome");
  //|99|//[variavel]->titulo    // ja com o valor atulizado pelo m�todo label
  //|99|//[variavel]->descricao // ja com o valor atulizado pelo m�todo label
  //|99|//[variavel]->tamanho   // ja com o valor atulizado pelo m�todo label
  var $titulo = null;
  var $title = null;
  var $tamanho = null;
  function label($nome = "") {
    //#00#//label
    //#10#//Este m�todo gera o label do campo para a funcao |db_lovrot|
    //#15#//label($campo);
    //#20#//nome  : Nome do campo a ser gerado as vari�veis de controle para fun��o
    //#99#//Caso os campos comecem com dl_ n�o ser� pesquisada o label da documenta��o e sim o pr�prio
    //#99#//nome da vari�vel sem o "dl_"
    if (substr($nome, 0, 3) == "dl_") {
      $this->titulo = substr($nome, 3);
      $this->title = substr($nome, 3);
      $this->tamanho = 0;
    } else {
      $sCampoTrim = trim($nome);
      $result = db_query("select c.descricao,c.rotulo,c.tamanho
                           from db_syscampo c
                          where c.nomecam = '${sCampoTrim}'");
      $numrows = pg_numrows($result);
      if ($numrows != 0) {
        $this->titulo = ucfirst(pg_result($result, 0, "rotulo"));
        $this->title = ucfirst(pg_result($result, 0, "descricao"));
        $this->tamanho = pg_result($result, 0, "tamanho");
      } else {
        $this->titulo = "";
        $this->title = "";
        $this->tamanho = "";
      }
    }
  }
  //|XX|//
}

//header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');

//Variavel com a URL absoluta, menos o arquivo
$DB_URL_ABS = "http://".$HTTP_SERVER_VARS['HTTP_HOST'].substr($HTTP_SERVER_VARS['PHP_SELF'], 0, strrpos($HTTP_SERVER_VARS['PHP_SELF'], "/") + 1);
//Variavel com a URL absoluta da pagina que abriu a atual, menos o arquivo
if (isset ($HTTP_SERVER_VARS["HTTP_REFERER"]))
$DB_URL_REF = substr($HTTP_SERVER_VARS["HTTP_REFERER"], 0, strrpos($HTTP_SERVER_VARS["HTTP_REFERER"], "/") + 1);

//troca os caracteres especiais em tags html

//if(basename($HTTP_SERVER_VARS['PHP_SELF']) != "/~dbjoao/dbportal2/pre4_mensagens001.php") {
if (basename($HTTP_SERVER_VARS['PHP_SELF']) != "pre4_mensagens001.php") {
  $tam_vetor = sizeof($HTTP_POST_VARS);
  reset($HTTP_POST_VARS);
  for ($i = 0; $i < $tam_vetor; $i ++) {
    if (gettype($HTTP_POST_VARS[key($HTTP_POST_VARS)]) != "array")
    $HTTP_POST_VARS[key($HTTP_POST_VARS)] = ($HTTP_POST_VARS[key($HTTP_POST_VARS)]);
    next($HTTP_POST_VARS);
  }
  $tam_vetor = sizeof($HTTP_GET_VARS);
  reset($HTTP_GET_VARS);
  for ($i = 0; $i < $tam_vetor; $i ++) {
    if (gettype($HTTP_GET_VARS[key($HTTP_GET_VARS)]) != "array")
    $HTTP_GET_VARS[key($HTTP_GET_VARS)] = ($HTTP_GET_VARS[key($HTTP_GET_VARS)]);
    next($HTTP_GET_VARS);
  }
}

// Verifica se esta sendo passado algum comando SQL
function db_verfPostGet($post) {

  //db_postmemory($GLOBALS["HTTP_POST_VARS"],2);

  $tam_vetor = sizeof($post);
  reset($post);
  for ($i = 0; $i < $tam_vetor; $i ++) {

    if (key($post) != 'triggerfuncao' && key($post) != 'corpofuncao' && key($post) != 'eventotrigger' && key($post) != 'codigoclass' && key($post) != 'db33_obs' && key($post) != 'db33_obscpd' && key($post) != 'descricao' && key($post) != 'descr') {
      $dbarraypost = (gettype($post[key($post)]) != "array" ? $post[key($post)] : "");
    } else {
      $dbarraypost = "";
    }
    if (db_indexOf(strtoupper($dbarraypost), "INSERT ") > 0 || db_indexOf(strtoupper($dbarraypost), "UPDATE ") > 0 || db_indexOf(strtoupper($dbarraypost), "DELETE ") > 0 || db_indexOf(strtoupper($dbarraypost), "EXEC(") > 0 || db_indexOf(strtoupper($dbarraypost), "SYSTEM(") > 0 || db_indexOf(strtoupper($dbarraypost), "<SCRIPT>") > 0 || db_indexOf(strtoupper($dbarraypost), "PASSTHRU(") > 0) {
      if(defined("TAREFA")==false) {
        echo "<script>alert('Voce est� passando parametros inv�lidos e sera redirecionado. Verifique INSERT/UPDATE e ... nos campos enviados.');top.corpo.location.href='instit.php'</script>\n";
        exit;
      }
    }
    //$post[key($post)] = htmlspecialchars(gettype($post[key($post)])!="array"?$post[key($post)]:"");
    next($post);
  }
}
db_verfPostGet($HTTP_POST_VARS);
db_verfPostGet($HTTP_GET_VARS);

function db_criatabela($result, $columns=array()) {
  //#00#//db_criatabela
  //#10#//Esta funcao mostra os dados de um record set na tela, em uma tabela
  //#15#//db_criatabela($result);
  //#20#//result  : Record set gerado
  //#20#//columns : Array com nomes das colunas a exibir, se nao passar nada mostra todas colunas do recordset

  $numrows = pg_numrows($result);
  if(count($columns)==0) {
    $numcols  = pg_numfields($result);
    $bycolumn = false;
  } else {
    $numcols  = count($columns);
    $bycolumn = true;
  }
  echo "<br><br><table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<tr bgcolor=\"#00CCFF\">\n";

  for ($j = 0; $j < $numcols; $j ++) {
    if(!$bycolumn) {
      echo "<td>".pg_fieldname($result, $j)."</td>\n";
    } else {
      echo "<td>".$columns[$j]."</td>\n";
    }
  }
  $cor = "#07F89D";
  echo "</tr>\n";
  for ($i = 0; $i < $numrows; $i ++) {
    echo "<tr bgcolor=\"". ($cor = ($cor == "#07F89D" ? "#51F50A" : "#07F89D"))."\">\n";
    for ($j = 0; $j < $numcols; $j ++) {
      if(!$bycolumn) {
        echo "<td nowrap>".pg_result($result, $i, $j)."</td>\n";
      } else {
        echo "<td nowrap>".pg_result($result, $i, $columns[$j])."</td>\n";
      }
    }
    echo "</tr>\n";
  }

  echo "</table><br><br>";


}

//retorna o tamanho do maior registro
function db_getMaxSizeField($recordset, $campo = 0) {
                //#00#//db_getMaxSizeField
                //#10#//Esta funcao retorna o maior valor do size de um determinado campo de um record set
                //#15#//db_getMaxSizeField($recordset,$campo = 0);
                //#20#//recordset : Record que ser� pesquisado
                //#20#//campo     : N�mero do campo do record set que ser� pesquisado

                $numrows = pg_numrows($recordset);
                $val = strlen(trim(pg_result($recordset, 0, $campo)));
                for ($i = 1; $i < $numrows; $i ++) {
                  $field = strlen(trim(pg_result($recordset, $i, $campo)));
                  if ($val < $field)
                  $val = $field;
                }
                return (int) $val;
}
//Pega um vetor e cria variaveis globais pelo indice do vetor
//atualiza a classe dos arquivos
function db_postmemory($vetor, $verNomeIndices = 0) {
                //#00#//db_postmemory
                //#10#//Esta funcao cria as veri�veis que s�o passadas por POST no array $HTTP_POST_VARS do apache
                //#15#//db_postmemory($vetor,$verNomeIndices = 0);
                //#20#//vetor         : Array que ser� pesquisado
                //#20#//verNomeIndice : 1 - para gerar as vari�veis
                //#20#//                2 - para gerar as vari�veis e mostrar no formul�rio com o nome e conte�do
                if (!is_array($vetor)) {
                  echo "Erro na fun��o postmemory: Parametro n�o � um array v�lido.<Br>\n";
                  return false;
                }
                $tam_vetor = sizeof($vetor);
                reset($vetor);
                if ($verNomeIndices > 0)
                echo "<br><br>\n";
                for ($i = 0; $i < $tam_vetor; $i ++) {
                  $matriz[$i] = key($vetor);
                  global $$matriz[$i];
                  $$matriz[$i] = $vetor[$matriz[$i]];
                  if ($verNomeIndices == 1)
                  echo "$".$matriz[$i]."<br>\n";
                  else
                  if ($verNomeIndices == 2)
                                echo "$".$matriz[$i]." = '".$$matriz[$i]."';<br>\n";
                                next($vetor);
                }
                if ($verNomeIndices > 0)
                echo "<br><br>\n";
}
function db_numpre_sp($qn, $qnp = "x", $qnt = "x", $qnd = "x") {
                //#00#//db_numpre_sp
                //#10#//Esta funcao coloca a mascara no numpre SEM os pontos entre os n�mero
                //#15#//db_numpre_sp($qn,$qnp="x",$qnt="x",$qnd="x");
                //#20#//qn  : N�mero do numpre, normalmento k00_numpre
                //#20#//qnp : N�mero da parcela do numpre
                //#20#//qnt : N�mero da quantidade de parcelas do numpre
                //#20#//qnd : D�gito verificador do numpre
                //#40#//C�digo de arrecada��o formatado SEM os pontos
                //#99#//Exemplo:
                //#99#//db_numpre_sp(123456,1,12,0); // numpre 123456 - parcela 1 - total de parcelas 12 - digito 0
                //#99#//Retorno ser� : 001234560010120
                //#99#//
                //#99#//Para formatar os n�meros o sistema utiliza a fun��o |db_formatar|
                $retorno = db_formatar($qn, 's', "0", 8, "e");
                if ($qnp != "x") {
                  // $retorno .= ".000";
                  $retorno .= db_formatar($qnp, 's', "0", 3, "e");
                }
                if ($qnt != "x") {
                  $retorno .= db_formatar($qnt, 's', "0", 3, "e");
                }
                if ($qnd != "x") {
                  $retorno .= db_formatar($qnd, 's', "0", 1, "e");
                }
                return $retorno;
}

function db_numpre($qn, $qnp = "x", $qnt = "x", $qnd = "x") {
                //#00#//db_numpre
                //#10#//Esta funcao coloca a mascara no numpre COM os pontos entre os n�mero
                //#15#//db_numpre_sp($qn,$qnp="x",$qnt="x",$qnd="x");
                //#20#//qn  : N�mero do numpre, normalmento k00_numpre
                //#20#//qnp : N�mero da parcela do numpre
                //#20#//qnt : N�mero da quantidade de parcelas do numpre
                //#20#//qnd : D�gito verificador do numpre
                //#40#//C�digo de arrecada��o formatado COM os pontos
                //#99#//Exemplo:
                //#99#//db_numpre_sp(123456,1,12,0); // numpre 123456 - parcela 1 - total de parcelas 12 - digito 0
                //#99#//Retorno ser� : 00123456.001.012.0
                //#99#//
                //#99#//Para formatar os n�meros o sistema utiliza a fun��o |db_formatar|
                $retorno = db_formatar($qn, 's', "0", 8, "e");
                if ($qnp != "x") {
                  // $retorno .= ".000";
                  $retorno .= ".".db_formatar($qnp, 's', "0", 3, "e");
                }
                if ($qnt != "x") {
                  $retorno .= ".".db_formatar($qnt, 's', "0", 3, "e");
                }
                if ($qnd != "x") {
                  $retorno .= ".".db_formatar($qnd, 's', "0", 1, "e");
                }
                return $retorno;
}
function db_translate($db_transforma = null,$expresAdicional = "",$stringAdicional = ""){

  // Array com express�es regulares
  $arr_regexp = Array(
  "/�/",
  "/�/",
  "/�/",
  "/�|�|�|�|�/",
  "/�|�|�|�|�/",
  "/�|�|�|�/",
  "/�|�|�|�|&/",
  "/�|�|�|�/",
  "/�|�|�|�/",
  "/�|�|�|�|�/",
  "/�|�|�|�|�/",
  "/�|�|�|�/",
  "/�|�|�|�/",
  "/'|;|:/",
  "/$expresAdicional/"
  );
  // Array com substitutos
  $arr_replac = Array("o","c","C","a","A","e","E","i","I","o","O","u","U"," ","$stringAdicional");

  // $arr_regexp[0] substitu�do por $arr_replac[0], ou seja, � por c
  // $arr_regexp[1] substitu�do por $arr_replac[1], ou seja, � por C
  // $arr_regexp[2] substitu�do por $arr_replac[2], ou seja, � ou � ou � ou � ou � por a
  // $arr_regexp[3] substitu�do por $arr_replac[3], ou seja, � ou � ou � ou � ou � por A
  // $arr_regexp[n] substitu�do por $arr_replac[n]
  // ...
  $db_transforma = preg_replace($arr_regexp,$arr_replac,$db_transforma);

  return $db_transforma;

}

// retorna uma string formatada, retorna false se alguma op��o estiver errada
// $tipo pode ser:
// "b" formata boolean s / n
// "f" formata a string pra float
// "d" formata a string pra data
// "v" tira a formata��o
// "cpf" formata cpf
// "cnpj" formata cnpj
// "s"  Preenche uma string para um certo tamanho com outra string
// se for "s":
//   $caracter             caracter ou espa�o pra acrecentar a esquerda, direita ou meio
//   $quantidade           tamanho que ficar� a string com os espa�os ou caracteres
//   $TipoDePreenchimento  informa se vai aplicar a string a:
//                         esquerda       "e"
//                         direita        "d"
//                         ambos os lados "a"

function db_formatar($str, $tipo, $caracter = " ", $quantidade = 0, $TipoDePreenchimento = "e", $casasdecimais = 2) {
                //#00#//db_formatar
                //#10#//Esta funcao coloca a mascara no numpre SEM os pontos entre os n�mero
                //#15#//db_formatar($str,$tipo,$caracter=" ",$quantidade=0,$TipoDePreenchimento="e",$casasdecimais=2) {
                //#20#//Str                   : String que ser� formatada
                //#20#//Tipo                  : Tipo de formata��o que ser� executada
                //#20#//                        cpf  =  Formata para CPF
                //#20#//                        cnpj =  Formata para CNPJ
                //#20#//                        b    =  Formata falso ou verdadeiro (S = Verdadeiro N = Falso )
                //#20#//                        p    =  Formata ponto flutuante, com PONTO na casa decimal Ex: 1000.55
                //#20#//                        f    =  Formata ponto flutuante, com VIRGULA na casa decimal Ex: 1000,55
                //#20#//                        d    =  Formata data
                //#20#//                        s    =  Formata uma string alinhando conforme Tipo de Preenchimento
                //#20#//                        v    =  Variavel, ou seja, imprime quantas casas decimais o valor tiver, combustivel por exemplo, valor de 1,359
                //#20#//Caracter              : Caracter que ser� colocado para formatar
                //#20#//Quantidade            : Tamanho da string que ser� gerada
                //#20#//Tipo de Preenchimento : Se preenche a esquerda, direito ou centro
                //#20#//                        e = Esquerda   d = Direita  a = Centro
                //#20#//Casas Decimais        : N�mero de casas decimais, para valores flutuantes, que ser� gerada
                //#40#//String formatada conforme os par�metros
                //#99#//Exemplo:
                //#99#//db_formatar(100.55,'f','0',15,'e',2)
                //#99#//Retorno ser� : 000000000100,55
                //#99#//db_formatar(100.55,'f') // formata��o padr�o de n�meros
                //#99#//Retorno ser� : "         100,55"
                //#99#//
                //#99#//db_formatar(100.55,'p','0',15,'e',2)
                //#99#//Retorno ser� : 000000000100.55

                switch ($tipo) {
                  case "sistema" :
                    return substr($str, 0, 1).".".substr($str, 1, 1).".".substr($str, 2, 1).".".substr($str, 3, 1).".".substr($str, 4, 1).".".substr($str, 5, 2).".".substr($str, 7, 2).".".substr($str, 9, 2).".".substr($str, 11, 2);
                  case "receita" :
                    return substr($str, 0, 1).".".substr($str, 1, 1).".".substr($str, 2, 1).".".substr($str, 3, 1).".".substr($str, 4, 1).".".substr($str, 5, 2).".".substr($str, 7, 2).".".substr($str, 9, 2).".".substr($str, 11, 2).".".substr($str, 13, 2);
                  case "receita_int" :
                    return substr($str, 0, 1).".".substr($str, 1, 1).".".substr($str, 2, 1).".".substr($str, 3, 1).".".substr($str, 4, 1).".".substr($str, 5, 2).".".substr($str, 7, 2).".".substr($str, 9, 2).".".substr($str, 11, 2).".".substr($str, 13, 2);
                  case "orgao" :
                    return str_pad($str, 2, "0", STR_PAD_LEFT);
                  case "unidade" :
                    return str_pad($str, 2, "0", STR_PAD_LEFT);
                  case "funcao" :
                    return str_pad($str, 2, "0", STR_PAD_LEFT);
                  case "subfuncao" :
                    return str_pad($str, 3, "0", STR_PAD_LEFT);
                  case "programa" :
                    return str_pad($str, 4, "0", STR_PAD_LEFT);
                  case "projativ" :
                    return str_pad($str, 4, "0", STR_PAD_LEFT);
                  case "elemento_int" :
                    return substr($str, 0, 1).".".substr($str, 1, 1).".".substr($str, 2, 1).".".substr($str, 3, 1).".".substr($str, 4, 1).".".substr($str, 5, 2).".".substr($str, 7, 2).".".substr($str, 9, 2).".".substr($str, 11, 2);
                  case "elemento" :
                    return substr($str, 1, 1).".".substr($str, 2, 1).".".substr($str, 3, 1).".".substr($str, 4, 1).".".substr($str, 5, 2).".".substr($str, 7, 2).".".substr($str, 9, 2).".".substr($str, 11, 2);
                  case "recurso" :
                    return str_pad($str, 4, "0", STR_PAD_LEFT);
                  case "atividade" :
                    return str_pad($str, 4, "0", STR_PAD_LEFT);
                  case "cpf" :
                    return substr($str, 0, 3).".".substr($str, 3, 3).".".substr($str, 6, 3)."/".substr($str, 9, 2);
                  case "cep" :
                    return substr($str, 0, 2).".".substr($str, 2, 3)."-".substr($str, 5, 3);
                  case "cnpj" :
                    return substr($str, 0, 2).".".substr($str, 2, 3).".".substr($str, 5, 3)."/".substr($str, 8, 4)."-".substr($str, 12, 2);
                    //90.832.619/0001-55
                  case "b" :
                    // boolean
                    if ($str == false) {
                      return 'N';
                    } else {
                      return 'S';
                    }
                  case "p" :
                    // ponto decimal com "."
                    /*
                    if (strpos($str,".") != 0) {
                    if (strpos($str,",") == 0) {
                    $casasdecimais = strlen($str) - strpos($str,".") - 1;
                    if ($casasdecimais < 2) {
                    $casasdecimais = 2;
                    }
                    }
                    }
                    */

                  	$str = $str == null ? 0 :$str;
                    if ($quantidade == 0) {
                      return str_pad(number_format($str, $casasdecimais, ".", ""), 15, "$caracter", STR_PAD_LEFT);
                    } else {
                      return str_pad(number_format($str, $casasdecimais, ".", ""), $quantidade, "$caracter", STR_PAD_LEFT);
                    }
                  case "v" :
                    // ponto decimal com virgula
                    if (strpos($str, ".") != 0) {
                      if (strpos($str, ",") == 0) {
                        $casasdecimais = strlen($str) - strpos($str, ".") - 1;
                        if ($casasdecimais < 2) {
                          $casasdecimais = 2;
                        }
                      }
                    }
                    if ($quantidade == 0)
                    if ($str == 0)
                    return "   ".str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
                    else
                    return str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
                    else {
                      //        return str_pad(number_format($str,$casasdecimais,",","."),$quantidade,"$caracter",STR_PAD_LEFT);
                      $vlrreturn = str_pad(number_format($str, $casasdecimais, ",", "."), $quantidade +1, "$caracter", STR_PAD_LEFT);
                      $posponto = strpos($vlrreturn, ",");
                      return substr($vlrreturn, 0, $posponto + $quantidade +1);
                    }
                  case "vdec" :
                    // ponto decimal sem virgula
                    if (strpos($str, ".") != 0) {
                      if (strpos($str, ",") == 0) {
                        $casasdecimais = strlen($str) - strpos($str, ".") - 1;
                        if ($casasdecimais < 2) {
                          $casasdecimais = 2;
                        }
                      }
                    }
                    if ($quantidade == 0)
                    if ($str == 0)
                    return "   ".str_pad(number_format($str, $casasdecimais, ".", ""), 15, "$caracter", STR_PAD_LEFT);
                    else
                    return str_pad(number_format($str, $casasdecimais, ".", ""), 15, "$caracter", STR_PAD_LEFT);
                    else {
                      $vlrreturn = str_pad(number_format($str, $casasdecimais, ".", ""), $quantidade +1, "$caracter", STR_PAD_LEFT);
                      $posponto = strpos($vlrreturn, ".");
                      return substr($vlrreturn, 0, $posponto + $quantidade +1);
                    }
                  case "valsemform" :

                    if ($quantidade == 0)
                    if ($str == 0)
                    $valretornar = "   ".str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
                    else
                    $valretornar = str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
                    else
                    $valretornar = str_pad(number_format($str, $casasdecimais, ",", "."), $quantidade, "$caracter", STR_PAD_LEFT);

                    $valretornar = str_replace(",","",$valretornar);
                    $valretornar = str_replace(".","",$valretornar);
                    return str_pad($valretornar,$quantidade," ",STR_PAD_LEFT);

                  case "f" :
                    // ponto decimal com virgula
                    /*
                    if (strpos($str,".") != 0) {
                    if (strpos($str,",") == 0) {
                    $casasdecimais = strlen($str) - strpos($str,".") - 1;
                    if ($casasdecimais < 2) {
                    $casasdecimais = 2;
                    }
                    }
                    }
                    */
                  	$str = $str == null ? 0 :$str;
                    if ($quantidade == 0)
                    if ($str == 0)
                    return "   ".str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
                    else
                    return str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
                    else
                    return str_pad(number_format($str, $casasdecimais, ",", "."), $quantidade, "$caracter", STR_PAD_LEFT);
                  case "fff" :
                    // ponto decimal com virgula

                    if ($quantidade == 0)
                    if ($str == 0)
                    return "   ".str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
                    else
                    return str_pad(number_format($str, $casasdecimais, ",", "."), 15, "$caracter", STR_PAD_LEFT);
                    else
                    return str_pad(number_format($str, $casasdecimais, ",", "."), $quantidade, "$caracter", STR_PAD_LEFT);
                  case "d" :

                    if ($str != "") {
                      $data = split("-", str_replace("/","-",$str));
                      return $data[2]."/".$data[1]."/".$data[0];
                    } else {
                      return $str;
                    }
                  case "s" :
                    if ($TipoDePreenchimento == "e") {
                      return str_pad($str, $quantidade, $caracter, STR_PAD_LEFT);
                    } else
                    if ($TipoDePreenchimento == "d") {
                      return str_pad($str, $quantidade, $caracter, STR_PAD_RIGHT);
                    } else
                    if ($TipoDePreenchimento == "a") {
                      return str_pad($str, $quantidade, $caracter, STR_PAD_BOTH);
                    }
                  case "xxxv" : // antigo "v"
                    if (strpos($str, ",") != "") {
                      $str = str_replace(".", "", $str);
                      $str = str_replace(",", ".", $str);
                      return $str;
                    } else
                    if (strpos($str, "-") != "") {
                      $str = split("-", $str);
                      return $str[2]."-".$str[1]."-".$str[0];
                    } else
                    if (strpos($str, "/") != "") {
                      $str = split("/", $str);
                      return $str[2]."-".$str[1]."-".$str[0];
                    }
                    break;
                }
                return false;
}


//Cria variaveis globais para a institui��o passada
//Se institui��o n�o for passada, buscar� dados da institui��o do db_getsession
//Retorna false se tiver problemas na execu��o do sql e numrows caso sql esteja correto (0 se n�o encontrar institui��o e 1 caso encontre)
function db_sel_instit($instit=null,$campos=" * "){
  if($instit == null || trim($instit) == ""){
    $instit = db_getsession("DB_instit");
  }
  if(trim($campos) == ""){
    $campos = " * ";
  }
  $record_config = db_query("select ".$campos."
                            from db_config
                                 left join db_tipoinstit on db21_codtipo = db21_tipoinstit
                            where codigo = ".$instit);
  if($record_config == false){
    return false;
  }else{
    $num_rows = pg_numrows($record_config);
    if($num_rows > 0){
      $num_cols = pg_numfields($record_config);
      for($index=0; $index<$num_cols; $index++){
        $nam_campo = pg_fieldname($record_config, $index);
        global $$nam_campo;
        $$nam_campo = pg_result($record_config, 0, $nam_campo);
      }
    }
  }
  return $num_rows;
}

//Cria variaveis globais para usu�rio passado
//Se o usu�rio n�o for passado, buscar� dados do usu�rio do db_getsession
//Retorna false se tiver problemas na execu��o do sql e numrows caso sql esteja correto (0 se n�o encontrar o usu�rio e 1 caso encontre)
function db_sel_usuario($usuario=null,$campos=" * "){
  if($usuario == null || trim($usuario) == ""){
    $usuario = db_getsession("DB_id_usuario");
  }
  if(trim($campos) == ""){
    $campos = " * ";
  }
  $record_usuarios = db_query("select ".$campos."
                            from db_usuarios
                            where id_usuario = ".$usuario);
  if($record_usuarios == false){
    return false;
  }else{
    $num_rows = pg_numrows($record_usuarios);
    if($num_rows > 0){
      $num_cols = pg_numfields($record_usuarios);
      for($index=0; $index<$num_cols; $index++){
        $nam_campo = pg_fieldname($record_usuarios, $index);
        global $$nam_campo;
        $$nam_campo = pg_result($record_usuarios, 0, $nam_campo);
      }
    }
  }
  return $num_rows;
}

//Cria veriaveis globais de todos os campos do recordset no indice $indice
function db_fieldsmemory($recordset, $indice, $formatar = "", $mostravar = false) {
                //#00#//db_fieldsmemory
                //#10#//Esta funcao cria as vari�veis de uma determinada linha de um record set, sendo o nome da vari�vel
                //#10#//o nome do campo no record set e seu conte�do o conte�do da vari�vel
                //#15#//db_fieldsmemory($recordset,$indice,$formatar="",$mostravar=false);
                //#20#//Record Set        : Record set que ser� pesquisado
                //#20#//Indice            : N�mero da linha (�ndice) que ser� caregada as fun��es
                //#20#//Formatar          : Se formata as vari�veis conforme o tipo no banco de dados
                //#20#//                    true = Formatar      false = N�o Formatar (Padr�o = false)
                //#20#//Mostrar Vari�veis : Mostrar na tela as vari�veis que est�o sendo geradas
                //#99#//Esta fun��o � bastante utilizada quando se faz um for para percorrer um record set.
                //#99#//Exemplo:
                //#99#//db_fieldsmemory($result,0);
                //#99#//Cria todas as vari�veis com o conte�do de cada uma sendo o valor do campo
                $fm_numfields = pg_numfields($recordset);
                $fm_numrows = pg_numrows($recordset);
                //if(pg_numrows($recordset)==0){
                // echo "RecordSet Vazio: <br>";
                // for ($i = 0;$i < $fm_numfields;$i++){
                //    echo pg_fieldname($recordset,$i)."<br>";
                // }
                // exit;
                // }
                for ($i = 0; $i < $fm_numfields; $i ++) {
                  $matriz[$i] = pg_fieldname($recordset, $i);
                  //if($fm_numrows==0){
                  //  $aux = trim(pg_result($recordset,$indice,$matriz[$i]));
                  //  echo "Record set vazio->".$aux;
                  //  continue;
                  //}

                  global $$matriz[$i];
                  $aux = trim(pg_result($recordset, $indice, $matriz[$i]));
                  if (($formatar != '')) {
                    switch (pg_fieldtype($recordset, $i)) {
                      case "float8" :
                      case "float4" :
                      case "float" :

												if (empty($aux) ){
													$aux = 0;
												}
												$$matriz[$i] = number_format($aux, 2, ".", "");
                        if ($mostravar == true)
                        echo $matriz[$i]."->".$$matriz[$i]."<br>";
                        break;
                      case "date" :
                        if ($aux != "") {
                          $data = split("-", $aux);
                          $$matriz[$i] = $data[2]."/".$data[1]."/".$data[0];
                        } else {
                          $$matriz[$i] = "";
                        }
                        if ($mostravar == true)
                        echo $matriz[$i]."->".$$matriz[$i]."<br>";
                        break;
                      default :
                        $$matriz[$i] = stripslashes($aux);
                        if ($mostravar == true)
                        echo $matriz[$i]."->".$$matriz[$i]."<br>";
                        break;
                    }
                  } else
                  switch (pg_fieldtype($recordset, $i)) {
                    case "date" :
                      $datav = split("-", $aux);
                      $split_data = $matriz[$i]."_dia";
                      global $$split_data;
                      $$split_data = @ $datav[2];
                      if ($mostravar == true)
                      echo $split_data."->".$$split_data."<br";
                      $split_data = $matriz[$i]."_mes";
                      global $$split_data;
                      $$split_data = @ $datav[1];
                      if ($mostravar == true)
                      echo $split_data."->".$$split_data."<br>";
                      $split_data = $matriz[$i]."_ano";
                      global $$split_data;
                      $$split_data = @ $datav[0];
                      if ($mostravar == true)
                      echo $split_data."->".$$split_data."<br>";
                      $$matriz[$i] = $aux;
                      if ($mostravar == true)
                      echo $matriz[$i]."->".$$matriz[$i]."<br>";
                      break;
                    default :
                      $$matriz[$i] = stripslashes($aux);
                      if ($mostravar == true)
                      echo $matriz[$i]."->".$$matriz[$i]."<br>";
                      break;
                  }

                  //          echo $matriz[$i] . " - " . pg_fieldtype($recordset,$i) . " - " . $aux . " - " . gettype($$matriz[$i]) . "<br>";

                }

}

///////  Calcula Digito Verificador
///////  sCampo - Valor  Ipeso - Qual peso 10 11

function db_CalculaDV($sCampo, $iPeso = 11) {
  $mult = 2;
  $i = 0;
  $iDigito = 0;
  $iSoma1 = 0;
  $iDV1 = 0;
  $iTamCampo = strlen($sCampo);
  for ($i = $iTamCampo -1; $i > -1; $i --) {
    $iDigito = $sCampo[$i];
    $iSoma1 = intval($iSoma1, 10) + intval(($iDigito * $mult), 10);
    $mult ++;
    if ($mult > 9)
    $mult = 2;
  }
  $iDV1 = ($iSoma1 % 11);
  if ($iDV1 < 2)
                $iDV1 = 0;
                else
                $iDV1 = 11 - $iDV1;
                return $iDV1;
}

//funcao para a db_CalculaDV
function db_Calcular_Peso($iPosicao, $iPeso) {
  return ($iPosicao % ($iPeso -1)) + 2;
}

//formata uma string pra cgc ou cpf
function db_cgccpf($str) {
  if (strlen($str) == 14)
                return substr($str, 0, 2).".".substr($str, 2, 3).".".substr($str, 5, 3)."/".substr($str, 8, 4)."-".substr($str, 12, 2);
                else
                if (strlen($str) == 11)
                return substr($str, 0, 3).".".substr($str, 3, 3).".".substr($str, 6, 3)."-".substr($str, 9, 2);
                else
                return $str;
}

function verifica_data($dia, $mes, $ano) {
  //#00#//db_verifica_data
  //#10#//Esta funcao verifica se uma data � v�lida ou n�o
  //#15#//db_verifica_data($dia,$mes,$ano);
  //#20#//Dia : Dia que ser� testado na data
  //#20#//Mes : M�s que ser� testado na data
  //#20#//Ano : Ano que ser� testado na data
  //#40#//Data correta
  //#99#//Caso n�o exista a data que foi enviada como par�metro o sistema soma o dia, mes e ano at� encontrar uma data

  while ((checkdate($mes, $dia, $ano) == false) or ((date("w", mktime(0, 0, 0, $mes, $dia, $ano)) == "0") or (date("w", mktime(0, 0, 0, $mes, $dia, $ano)) == "6"))) {
    if ($dia > 31) {
      $dia = 1;
      $mes ++;
      if ($mes > 12) {
        $mes = 1;
        $ano ++;
      }
    } else {
      $dia ++;
    }
  }
  return $ano."-".$mes."-".$dia;
}

function db_vencimento($dt = "") {
                //#00#//db_vencimento
                //#10#//Esta funcao coloca a data no formato ano-mes-dia para o postgres
                //#15#//db_vencimento($dt);
                //#20#//Dt : Data a ser convertida para o formato
                //#20#//     Caso a data em branco ou n�o informada, o sistema carrega a data da fun��o |db_getsession|
                //#40#//Data formatada para postgres

                if (empty ($dt))
                $dt = db_getsession("DB_datausu");
                $data = date("Y-m-d", $dt);
                /*
          if ( (date("H",$dt) >= "16" ) ) {
          $data = verifica_data(date("d",$dt)+1,date("m",$dt),date("Y",$dt));
          } else {
          if ( ( date("w",mktime(0,0,0,date("m",$dt),date("d",$dt),date("Y",$dt))) == "0" ) or ( date("w",mktime(0,0,0,date("m",$dt),date("d",$dt),date("Y",$dt))) == "6" )  ) {
          $data = verifica_data(date("d",$dt)+1,date("m",$dt),date("Y",$dt));
          }
          }
          */
                return $data;
}

/**
 * mostra uma mensagem na tela
 *
 * @param string $sMensagem - Mensagem a ser colocada no alert
 * @access public
 * @return void
 */
function db_msgbox($sMensagem) {

  $sMensagem = str_replace("\n", '\n', $sMensagem);

  echo "<script>alert('".$sMensagem."');</script>\n";
}

//redireciona para uma url
function db_redireciona($url = "0") {
                //#00#//db_redireciona
                //#10#//Esta funcao executa um redrecionamento de p�gina utilizando o javascript
                //#15#//db_redireciona($url="0")
                //#20#//Url : Nome completo da p�gina a ser acessada pelo redirecionamento
                //#99#//Exemplo:
                //#99#//db_redireciona("index.php");
                //#99#//Ir� abrir a p�gina index.php
                if ($url == "0")
                $url = $GLOBALS["PHP_SELF"];
                echo "<script>location.href='$url'</script>\n";
                exit;
}

//retorna uma vari�vel de sess�o
/*
 function db_getsession($var) {
 global $HTTP_SESSION_VARS;
 if(!class_exists("crypt_hcemd5"))
 include("db_calcula.php");
 $rand = 195728462;
 $key = "alapuchatche";
 $md = new Crypt_HCEMD5($key, $rand);
 return $md->decrypt($HTTP_SESSION_VARS[$var]);
 }
 */

//retorna uma vari�vel de sess�o
function db_getsession($var = "0", $alertarExistencia = true) {
                //#00#//db_getsession
                //#10#//Esta funcao recupera da sess�o do php uma determinada vari�vel, ou todas as vari�veis l� registradas
                //#15#//db_getsession($var="0", $alertarExistencia = true)
                //#20#//Var : Nome da vari�vel que ser� recuperada
                //#20#//alertarExistencia : Se deseja alertar que v�riavel de sess�o n�o existe (conforme o caso).
                //#99#//Variaveis dispon�veis na sess�o
                //#99#//DB_acessado     Utilizado para Log
                //#99#//DB_login        Login do usu�rio
                //#99#//DB_id_usuario   N�medo do id do usu�rio na taela |db_usuarios|
                //#99#//DB_ip           N�mero do IP que esta acessando
                //#99#//DB_uol_hora     Hora de acesso do usu�rio
                //#99#//DB_SELLER       Variavel de controle
                //#99#//DB_NBASE        Nome da base de dados que esta sendo acessada
                //#99#//DB_modulo       N�mero do m�dulo que esta acessado
                //#99#//DB_nome_modulo  Nome do m�dulo que esta acessado
                //#99#//DB_anousu       Exerc�cio que esta sendo acessado
                //#99#//DB_datausu      Data do servidor
                //#99#//DB_coddepto     C�digo do departamento do usu�rio
                //#99#//DB_instit       C�digo da institui��o
                //#99#//
                //#99#//Exemplo:
                //#99#//db_getsession("DB_datausu");
                //#99#//Ir� abrir a p�gina index.php
                global $HTTP_SESSION_VARS;
                /*
                 if(!class_exists("crypt_hcemd5"))
          include("db_calcula.php");
          $rand = 195728462;
          $key = "alapuchatche";
          $md = new Crypt_HCEMD5($key, $rand);
          if($var=="0"){
          reset($HTTP_SESSION_VARS);
          $str = "";
          $caract = "";
          for($x=0;$x<sizeof($HTTP_SESSION_VARS);$x++){
          $str .= $caract.key($HTTP_SESSION_VARS)."=".$md->decrypt($HTTP_SESSION_VARS[key($HTTP_SESSION_VARS)]);
          next($HTTP_SESSION_VARS);
          $caract = "&";
          }
          return $str;
          } else {
          return $md->decrypt($HTTP_SESSION_VARS[$var]);
          }
          */
                if ($var == "0") {
                  reset($HTTP_SESSION_VARS);
                  $str = "";
                  $caract = "";
                  for ($x = 0; $x < sizeof($HTTP_SESSION_VARS); $x ++) {
                    $str .= $caract.key($HTTP_SESSION_VARS)."=".$HTTP_SESSION_VARS[key($HTTP_SESSION_VARS)];
                    next($HTTP_SESSION_VARS);
                    $caract = "&";
                  }
                  return $str;
                } else {
                  if (isset ($HTTP_SESSION_VARS[$var])) {
                    return $HTTP_SESSION_VARS[$var];
                  } else {
      if($alertarExistencia == true){
               db_msgbox('Variavel de sess�o nao encontrada: '.$var);
      }
      return null;
                  }
                }
}

//atualiza uma vari�vel de sessao
function db_putsession($var, $valor) {
  //#00#//db_putsession
  //#10#//Esta funcao inclui na sess�o do php uma determinada vari�vel
  //#15#//db_putsession($var,$valor)
  //#20#//Var   : Nome da vari�vel que ser� incluida na sess�o
  //#20#//valor : Valor da vari�vel inclu�da
  global $HTTP_SESSION_VARS;
  /*
   if(!class_exists("crypt_hcemd5"))
          include("db_calcula.php");
          $rand = 195728462;
          $key = "alapuchatche";
          $md = new Crypt_HCEMD5($key, $rand);
          $HTTP_SESSION_VARS[$var] = $md->encrypt($valor);
          */
  $HTTP_SESSION_VARS[$var] = $valor;
}

function db_destroysession($var){
  //#00#//db_destroysession
  //#10#//Esta funcao desregistra uma vari�vel de sess�o do php
  //#15#//db_destroysession($var)
  //#20#//Var   : Nome da vari�vel que ser� desregistrada na sess�o
  unset($_SESSION[$var]);
}

//coloca no tamanho e acrecenta caracteres '$qual' a esquerda
function db_sqlformatar($campo, $quant, $qual) {
  $aux = "";
  for ($i = strlen($campo); $i < $quant; $i ++)
                $aux .= $qual;
                return $aux.$campo;

}

//retorna uma string do inicio de $str, at� primeiro caractere da ocorrencia em $pos
function db_strpos($str, $pos) {
  return substr($str, 0, (strpos($str, $pos) == "" ? strlen($str) : strpos($str, $pos)));
}

//imprime uma mensagem de erro, com um link pra voltar pra p�gina anterior
function db_erro($msg, $voltar = 1) {
  $uri = $GLOBALS["PHP_SELF"];
  echo "$msg<br>\n";
  if ($voltar == 1)
                echo "<a href=\"$uri\">Voltar</a>\n";
                exit;
}

//Tipo a parseInt do javascript
function db_parse_int($str) {
  $num = array ("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
  $tam = strlen($str);
  $aux = "";
  for ($i = 0; $i < $tam; $i ++) {
    if (in_array($str[$i], $num))
    $aux .= $str[$i];
  }
  return $aux;
}

// Tipo o indexOf do javascript
function db_indexOf($str, $proc) {
  // 0 nao encontrou
  // > 0 encontrou
  return strlen(strstr($str, $proc));
}

//Executa um SELECT e pagina na tela com os labels do sistema
function db_lovrot($query, $numlinhas, $arquivo = "", $filtro = "%", $aonde = "_self", $campos_layer = "", $NomeForm = "NoMe", $variaveis_repassa = array (), $automatico = true, $totalizacao = array()) {

  //Observa��o : Quando utilizar o parametro automatico, coloque no parametro NomeForm o seguinte "NoMe" e em variaveis_repassa array().

  //#00#//db_lovrot
  //#10#//Esta funcao � utilizada para mostrar registros na tela, podendo p�ginar os dados
  //#15#//db_lovrot($query,$numlinhas,$arquivo="",$filtro="%",$aonde="_self",$campos_layer="",$NomeForm="NoMe",$variaveis_repassa=array());
  //#20#//$query               Select que ser� executado
  //#20#//$numlinhas           N�mero de linhas a serem mostradas
  //#20#//$arquivo             Arquivo que ser� executado quando der um click em uma linha
  //#20#//                     Na vers�o com iframe dever� ser colocado "()"
  //#20#//$filtro              Filtro que ser� gerado, normamente ""
  //#20#//$aonde               Nome da fun��o que ser� executada quando der um click
  //#20#//$campos_layer        Campos que ser�o colocados na layer quando passar o mouse ( n�o esta implementado )
  //#20#//$NomeForm            Nome do formul�rio para colocar vari�veis complementares Padr�o = "NoMe"
  //#20#//$variaveis_repassa   Array com as vari�veis a serem reoassadas para o programa
  //#99#//Exemplo:
  //#99#//$js_funcao = "";
  //#99#//db_lovrot("select z01_nome from cgm limit 1","()","",$js_funcao);
  //#99#//
  //#99#//O cabe�alho da tabela o sistema pega pelo nome do campo e busca na documenta��o, colcando o label
  //#99#//Quando n�o desejar colocar o label da documentacao, o nome do campo dever� ser iniciado com dl_ e o sistema retirar�
  //#99#//estes caracteres e colocar� o primeiro caracter em maiusculo
  //#99#//Criado parametro novo, $totalizacao = array() que devere fornecer os campos que desejar fazer somatorio, conforme
  //#99#//exemplo abaixo:
  //#99#//
  //#99#//$totalizacao["e60_vlremp"] = "e60_vlremp"; totaliza o campo
  //#99#//$totalizacao["e60_vlranu"] = "e60_vlranu"; totaliza o campo
  //#99#//$totalizacao["e60_vlrpag"] = "e60_vlrpag"; totaliza o campo
  //#99#//$totalizacao["e60_vlrliq"] = "e60_vlrliq"; totaliza o campo
  //#99#//$totalizacao["dl_saldo"] = "dl_saldo";     totaliza o campo ( neste caso, o campo � um alias no sql)
  //#99#//$totalizacao["totalgeral"] = "z01_nome";   indica qual o campo sera colocado o total


  global $BrowSe;
  //cor do cabecalho
  global $db_corcabec;
  $db_corcabec = $db_corcabec == "" ? "#CDCDFF" : $db_corcabec;
  //cor de fundo de cada registro
  global $cor1;
  global $cor2;
  $mensagem = "Clique Aqui";
  $cor1 = $cor1 == "" ? "#97B5E6" : $cor1;
  $cor2 = $cor2 == "" ? "#E796A4" : $cor2;
  global $HTTP_POST_VARS;
  $tot_registros = "tot_registros".$NomeForm;
  $offset = "offset".$NomeForm;
  //recebe os valores do campo hidden

  if (isset ($HTTP_POST_VARS["totreg".$NomeForm])) {
    $$tot_registros = $HTTP_POST_VARS["totreg".$NomeForm];
  } else {
    $$tot_registros = 0;
  }
  if (isset ($HTTP_POST_VARS["offset".$NomeForm])) {
    $$offset = $HTTP_POST_VARS["offset".$NomeForm];
  } else {
    $$offset = 0;
  }
  if(isset($HTTP_POST_VARS["recomecar"])){
    $recomecar = $HTTP_POST_VARS["recomecar"];
  }
  // se for a primeira vez que � rodado, pega o total de registros e guarda no campo hidden
  if ( ( empty ($$tot_registros) && !empty ($query) ) || isset($recomecar)) {
    if(isset($recomecar)){
      $query = db_getsession("dblov_query_inicial");
    }
    $Dd1 = "disabled";
    if(count($totalizacao)>0 || isset($totalizacao_rep)){
      $total_campos = "";
      $sep_total_campos = "";
      reset($totalizacao);
      for ($j = 0; $j < count($totalizacao); $j ++) {
        if( key($totalizacao) == $totalizacao[key($totalizacao)] ){
          $total_campos .= $sep_total_campos."sum(".$totalizacao[key($totalizacao)].") as ".$totalizacao[key($totalizacao)];
          $sep_total_campos = ",";
        }
        next($totalizacao);
      }
      reset($totalizacao);
      $tot = db_query("select count(*),$total_campos
                        from ($query) as temp");
    }else{

      $tot = db_query("select count(*) from ($query) as temp");
    }
    //$tot = 0;

    db_putsession("dblov_query_inicial",$query);

    $$tot_registros = pg_result($tot, 0, 0);
    if ($$tot_registros == 0)
    $Dd2 = "disabled";
  }


  if(isset($HTTP_POST_VARS["nova_quantidade_linhas"]) && $HTTP_POST_VARS["nova_quantidade_linhas"]!=''){
    $HTTP_POST_VARS["nova_quantidade_linhas"] = $HTTP_POST_VARS["nova_quantidade_linhas"] + 0;
    $numlinhas = $HTTP_POST_VARS["nova_quantidade_linhas"];
  }

  // testa qual botao foi pressionado
  if (isset ($HTTP_POST_VARS["pri".$NomeForm])) {
    $$offset = 0;
    $Dd1 = "disabled";
    $query = str_replace("\\", "", $HTTP_POST_VARS["filtroquery"]);
  } else
                if (isset ($HTTP_POST_VARS["ant".$NomeForm])) {
                  // if(isset("filtroquery"]);
                  $query = str_replace("\\", "", @ $HTTP_POST_VARS["filtroquery"]);
                  if ($$offset <= $numlinhas) {
                    $$offset = 0;
                    $Dd1 = "disabled";
                  } else
                                $$offset = $$offset - $numlinhas;
                } else
                if (isset ($HTTP_POST_VARS["prox".$NomeForm])) {
                                $query = str_replace("\\", "", $HTTP_POST_VARS["filtroquery"]);
                                //    if($numlinhas >= ($$tot_registros - $$offset - $numlinhas)) {

                                if (($$offset + ($numlinhas * 2)) >= $$tot_registros) {
                                  $Dd2 = "disabled";
                                }

                                if ($numlinhas >= ($$tot_registros - $$offset)) {
                                  //$$offset = $$tot_registros - $numlinhas;
                                  if ($$tot_registros - $$offset - $numlinhas >= $numlinhas)
                                                $$offset = $numlinhas;
                                                else
                                                $$offset = $$offset + $numlinhas;

                                                if ($$offset > $$tot_registros)
                                                $$offset = 0;
                                                $Dd2 = "disabled";
                                } else
                                $$offset = $$offset + $numlinhas;
                } else
                if (isset ($HTTP_POST_VARS["ult".$NomeForm])) {
                  $query = str_replace("\\", "", $HTTP_POST_VARS["filtroquery"]);
                  $$offset = $$tot_registros - $numlinhas;
                  if ($$offset < 0) {
                                                $$offset = 0;
                  }
                  $Dd2 = "disabled";
                } else {
                  reset($HTTP_POST_VARS);
                  for ($i = 0; $i < sizeof($HTTP_POST_VARS); $i ++) {
                                                $ordem_lov = substr(key($HTTP_POST_VARS), 0, 11);
                                                if ($ordem_lov == 'ordem_dblov') {
                                                  $query = str_replace("\\", "", $HTTP_POST_VARS["filtroquery"]);
                                                  $campo = substr(key($HTTP_POST_VARS), 11);

                                                  $ordem_ordenacao = '';

                                                  if(isset($HTTP_POST_VARS['ordem_lov_anterior'])){
                                                    if($HTTP_POST_VARS['ordem_lov_anterior'] == $HTTP_POST_VARS[key($HTTP_POST_VARS)] ){
                                                      $ordem_ordenacao = 'desc';
                                                    }
                                                  }

                                                  if ($HTTP_POST_VARS["codigo_pesquisa"] != '') {
                                                    $query_anterior = $query;
                                                    $query_novo_filtro = "select * from (".$query.") as x where ".$campo."::text ILIKE '".$HTTP_POST_VARS["codigo_pesquisa"]."%' order by ".$campo.' '.$ordem_ordenacao;
                                                    $query = $query_novo_filtro;
                                                  } else {
                                                    if ($HTTP_POST_VARS["distinct_pesquisa"] == '1') {
                                                      $query_anterior = $query;
                                                      $query = "select distinct on (".$campo.") * from (".$query.") as x order by ".$campo." ".$ordem_ordenacao;
                                                      $query_novo_filtro = $query;
                                                    }else{
                                                      $query = "select * from (".$query.") as x order by ".$campo." ".$ordem_ordenacao;
                                                    }
                                                  }
                                                  $$offset = 0;
                                                  break;
                                                }
                                                next($HTTP_POST_VARS);
                  }
                }

                $filtroquery = $query;
                // executa a query e cria a tabela
                if ($query == "") {
                  exit;
                }


                $query .= " limit $numlinhas offset ".$$offset;
                $result = db_query($query);
                $NumRows = pg_numrows($result);

                if ($NumRows == 0) {
                  if (isset ($query_anterior)) {
                    echo "<script>alert('N�o existem dados para este filtro');</script>";


                    if(count($totalizacao)>0 || isset($totalizacao_rep)){
                      $total_campos = "";
                      $sep_total_campos = "";
                      reset($totalizacao);
                      for ($j = 0; $j < count($totalizacao); $j ++) {
                        if( key($totalizacao) == $totalizacao[key($totalizacao)] ){
                          $total_campos .= $sep_total_campos."sum(".$totalizacao[key($totalizacao)].") as ".$totalizacao[key($totalizacao)];
                          $sep_total_campos = ",";
                        }
                        next($totalizacao);
                      }
                      reset($totalizacao);
                      $tot = db_query("select count(*),$total_campos
                            from ($query_anterior) as temp");
                    }else{
                      $tot = db_query("select count(*) from ($query_anterior) as temp");
                    }
                    //$tot = 0;
                    $$tot_registros = pg_result($tot, 0, 0);


                    $$tot_registros = pg_result($tot, 0, 0);

                    $query = $query_anterior." limit $numlinhas offset ".$$offset;
                    $result = db_query($query);
                    $NumRows = pg_numrows($result);
                    $filtroquery = $query_anterior;
                  }
                } else {
                  if (isset ($query_anterior)) {
                    $Dd1 = "disabled";

                    if(count($totalizacao)>0 || isset($totalizacao_rep)){
                      $total_campos = "";
                      $sep_total_campos = "";
                      reset($totalizacao);
                      for ($j = 0; $j < count($totalizacao); $j ++) {
                        if( key($totalizacao) == $totalizacao[key($totalizacao)] ){
                          $total_campos .= $sep_total_campos."sum(".$totalizacao[key($totalizacao)].") as ".$totalizacao[key($totalizacao)];
                          $sep_total_campos = ",";
                        }
                        next($totalizacao);
                      }
                      reset($totalizacao);
                      $tot = db_query("select count(*),$total_campos
                            from ($query_novo_filtro) as temp");
                    }else{
                      $tot = db_query("select count(*) from ($query_novo_filtro) as temp");
                    }
                    //$tot = 0;
                    $$tot_registros = pg_result($tot, 0, 0);
                    if ($$tot_registros == 0)
                    $Dd2 = "disabled";
                  }

                }

                // echo "<script>alert('$NumRows')</script>";

                $NumFields = pg_numfields($result);
                if (($NumRows < $numlinhas) && ($numlinhas < ($$tot_registros - $$offset - $numlinhas))) {
                  $Dd1 = @ $Dd2 = "disabled";
                }

                echo "<script>
                function js_mostra_text(liga,nomediv,evt){

                  evt = (evt)?evt:(window.event)?window.event:'';
                  if(liga==true){

                    document.getElementById(nomediv).style.top = 0; //evt.clientY;
                    document.getElementById(nomediv).style.left = 0; //(evt.clientX+20);
                    document.getElementById(nomediv).style.visibility = 'visible';
                  }else
                    document.getElementById(nomediv).style.visibility = 'hidden';
                }
                function js_troca_ordem(nomeform,campo,valor){
                  obj=document.createElement('input');
                  obj.setAttribute('name',campo);
                  obj.setAttribute('type','submit');
                  obj.setAttribute('value',valor);
              obj.setAttribute('style','color:#FCA;background-color:transparent;border-style:none');
                  eval('document.'+nomeform+'.appendChild(obj)');
                  eval('document.'+nomeform+'.'+campo+'.click()');
                }
            function js_lanca_codigo_pesquisa(valor_recebido){
              document.navega_lov".$NomeForm.".codigo_pesquisa.value = valor_recebido;
            }
            function js_lanca_distinct_pesquisa(){
              document.navega_lov".$NomeForm.".distinct_pesquisa.value = 1;
            }
            function js_nova_quantidade_linhas(valor_recebido){
          valor_recebe = Number(valor_recebido);
          if(!valor_recebe){
            alert('Valor Inv�lido!');
                document.navega_lov".$NomeForm.".nova_quantidade_linhas.value = '';
                document.getElementById('quant_lista').value = '';
          }else{
            if(valor_recebe > 100){
                  document.navega_lov".$NomeForm.".nova_quantidade_linhas.value = '100';
                  document.getElementById('quant_lista').value = 100;
            }else{
                  document.navega_lov".$NomeForm.".nova_quantidade_linhas.value = valor_recebido;
            }
          }
            }
                </script>";

                echo "<table id=\"TabDbLov\" border=\"1\" cellspacing=\"1\" cellpadding=\"0\">\n";
                /**** botoes de navegacao ********/
                echo "<tr><td colspan=\"". ($NumFields +1)."\" nowrap> <form name=\"navega_lov".$NomeForm."\" method=\"post\">
            <input type=\"submit\" name=\"pri".$NomeForm."\" value=\"In�cio\" ".@ $Dd1.">
            <input type=\"submit\" name=\"ant".$NomeForm."\" value=\"Anterior\" ".@ $Dd1.">
            <input type=\"submit\" name=\"prox".$NomeForm."\" value=\"Pr�ximo\" ".@ $Dd2.">
            <input type=\"submit\" name=\"ult".$NomeForm."\" value=\"�ltimo\" ".@ $Dd2.">
                <input type=\"hidden\" name=\"offset".$NomeForm."\" value=\"".@ $$offset."\">
                <input type=\"hidden\" name=\"totreg".$NomeForm."\" value=\"".@ $$tot_registros."\">
                <input type=\"hidden\" name=\"codigo_pesquisa\" value=\"\">\n
                <input type=\"hidden\" name=\"distinct_pesquisa\" value=\"\">\n
                <input type=\"hidden\" name=\"filtro\" value=\"$filtro\">\n";
                reset($variaveis_repassa);
                if (sizeof($variaveis_repassa) > 0) {
                  for ($varrep = 0; $varrep < sizeof($variaveis_repassa); $varrep ++) {
                    echo "<input type=\"hidden\" name=\"".key($variaveis_repassa)."\" value=\"".$variaveis_repassa[key($variaveis_repassa)]."\">\n";
                    next($variaveis_repassa);
                  }
                }

                if (isset($ordem_lov) && (isset($ordem_ordenacao) && $ordem_ordenacao == '' ) ) {
                  echo "<input type=\"hidden\" name=\"ordem_lov_anterior\" value=\"".$HTTP_POST_VARS[key($HTTP_POST_VARS)]."\">\n";
                }
                if(isset($HTTP_POST_VARS['nova_quantidade_linhas']) && $HTTP_POST_VARS['nova_quantidade_linhas'] == ''){
                  $numlinhas = $HTTP_POST_VARS['nova_quantidade_linhas'];
                }

                echo "<input type=\"hidden\" name=\"nova_quantidade_linhas\" value=\"$numlinhas\" >\n";

                if(isset($totalizacao) && isset($tot)){
                  if(count($totalizacao)>0 ){
                    $totNumfields = pg_numfields($tot);
                    for($totrep=1;$totrep<$totNumfields;$totrep++){
               echo "<input type=\"hidden\" name=\"totrep_".pg_fieldname($tot,$totrep)."\" value=\"".db_formatar(pg_result($tot,0,$totrep),'f')."\">";
                    }
                    reset($totalizacao);
                    $totrepreg = "";
                    $totregsep = "";
                    for($totrep=0;$totrep<count($totalizacao);$totrep++){
                      $totrepreg .= $totregsep.key($totalizacao)."=".$totalizacao[key($totalizacao)];
                      $totregsep = "|";
                      next($totalizacao);
                    }
                    reset($totalizacao);
                    echo "<input type=\"hidden\" name=\"totalizacao_repas\" value=\"".$totrepreg."\">";
                  }
                }else if(isset( $HTTP_POST_VARS["totalizacao_repas"]) ){
                  $totalizacao_split = split("\|",$HTTP_POST_VARS["totalizacao_repas"]);
                  for($totrep=0;$totrep<count($totalizacao_split);$totrep++){
                    $totalizacao_sep = split("\=",$totalizacao_split[$totrep]);
             $totalizacao[$totalizacao_sep[0]] = $totalizacao_sep[1];
             if(isset($HTTP_POST_VARS["totrep_".$totalizacao_sep[0]])){
               echo "<input type=\"hidden\" name=\"totrep_".$totalizacao_sep[0]."\" value=\"".$HTTP_POST_VARS["totrep_".$totalizacao_sep[0]]."\">";
             }
                  }
                  echo "<input type=\"hidden\" name=\"totalizacao_repas\" value=\"".$HTTP_POST_VARS["totalizacao_repas"]."\">";
                }

                echo "<input type=\"hidden\" name=\"filtroquery\" value=\"".str_replace("\n", "", @ $filtroquery)."\">
          ". ($NumRows > 0 ? "
          Foram retornados <font color=\"red\"><strong>".$$tot_registros."</strong></font> registros.
          Mostrando de <font color=\"red\"><strong>". (@ $$offset +1)."</strong></font> at�
          <font color=\"red\"><strong>". ($$tot_registros < (@ $$offset + $numlinhas) ? ($NumRows <= $numlinhas ? $$tot_registros : $NumRows) : ($$offset + $numlinhas))."</strong></font>." : "Nenhum Registro
          Retornado")."</form>
          </td></tr>\n";

                /*********************************/
                /***** Escreve o cabecalho *******/
                if ($NumRows > 0) {
                  echo "<tr>\n";
                  // implamentacao de informacoes complementares
                  //    echo "<td title='Outras Informa��es'>OI</td>\n";
                  //se foi passado funcao
                  if ($campos_layer != "") {
                    $campo_layerexe = split("\|", $campos_layer);
                    echo "<td nowrap bgcolor=\"$db_corcabec\" title=\"Executa Procedimento Espec�fico.\" align=\"center\">Clique</td>\n";
                  }

                  $clrotulocab = new rotulolov();
                  for ($i = 0; $i < $NumFields; $i ++) {
                    if (strlen(strstr(pg_fieldname($result, $i), "db_")) == 0) {
                      $clrotulocab->label(pg_fieldname($result, $i));
                      //echo "<td nowrap bgcolor=\"$db_corcabec\" title=\"".$clrotulocab->title."\" align=\"center\"><b><u>".$clrotulocab->titulo."</u></b></td>\n";
                      echo "<td nowrap bgcolor=\"$db_corcabec\" title=\"".$clrotulocab->title."\" align=\"center\"><input name=\"".pg_fieldname($result, $i)."\" value=\"".ucfirst($clrotulocab->titulo)."\" type=\"button\" onclick=\"js_troca_ordem('navega_lov".$NomeForm."','ordem_dblov".pg_fieldname($result, $i)."','".pg_fieldname($result, $i)."');\" style=\"text-decoration:underline;background-color:transparent;border-style:none\"> </td>\n";

                    } else {
                      if (strlen(strstr(pg_fieldname($result, $i), "db_m_")) != 0) {
                        echo "<td nowrap bgcolor=\"$db_corcabec\" title=\"".substr(pg_fieldname($result, $i), 5)."\" align=\"center\"><b><u>".substr(pg_fieldname($result, $i), 5)."</u></b></td>\n";
                      }
                    }
                  }
                  echo "</tr>\n";
                }
                //cria nome da funcao com parametros
                if ($arquivo == "()") {
                  $arrayFuncao = split("\|", $aonde);
                  $quantidadeItemsArrayFuncao = sizeof($arrayFuncao);

                }

                /********************************/
                /****** escreve o corpo *******/

                for ($i = 0; $i < $NumRows; $i ++) {
                  echo '<tr >'."\n";
                  // implamentacao de informacoes complementares
                  //          echo '<td onMouseOver="document.getElementById(\'div'.$i.'\').style.visibility=\'visible\';" onMouseOut="document.getElementById(\'div'.$i.'\').style.visibility=\'hidden\';" >-></td>'."\n";
                  if ($arquivo == "()") {
                    $loop = "";
                    $caracter = "";
                    if ($quantidadeItemsArrayFuncao > 1) {
                      for ($cont = 1; $cont < $quantidadeItemsArrayFuncao; $cont ++) {
                        if (strlen($arrayFuncao[$cont]) > 3) {
                          for ($luup = 0; $luup < pg_NumFields($result); $luup ++) {
                            if (pg_FieldName($result, $luup) == "db_".$arrayFuncao[$cont]) {
                              $arrayFuncao[$cont] = "db_".$arrayFuncao[$cont];
                            }
                          }
                        }
                        $loop .= $caracter."'".addslashes(str_replace('"','',@ pg_result($result, $i, (strlen($arrayFuncao[$cont]) < 4 ? (int) $arrayFuncao[$cont] : $arrayFuncao[$cont]))))."'";
                        //$loop .= $caracter."'".pg_result($result,$i,(int)$arrayFuncao[$cont])."'";
                        $caracter = ",";
                      }
                      $resultadoRetorno = $arrayFuncao[0]."(".$loop.")";
                    } else {
                      $resultadoRetorno = $arrayFuncao[0]."()";
                    }
                  }

                  /*
                   if($NumRows==1){
                   if($arquivo!=""){
                   echo "<td>$resultadoRetorno<td>";
                   exit;
                          }else{
                          echo "<script>JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=".($BrowSe==1?0:trim(pg_result($result,0,0))))."','$aonde','width=800,height=600');</script>";
                          exit;
                          }
                          }
                          */

                  if (isset ($cor)) {
                    $cor = $cor == $cor1 ? $cor2 : $cor1;
                  } else {
                    $cor = $cor1;
                  }
                  // implamentacao de informacoes complementares
                  //    $mostradiv="";
                  if ($campos_layer != "") {
                    $campo_layerexe = split("\|", $campos_layer);
                    echo "<td id=\"funcao_aux".$i."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a href=\"\" onclick=\"".$campo_layerexe[1]."($loop);return false\" ><strong>".$campo_layerexe[0]."&nbsp;</strong></a></td>\n";
                  }
                  for ($j = 0; $j < $NumFields; $j ++) {
                    if (strlen(strstr(pg_fieldname($result, $j), "db_")) == 0 || strlen(strstr(pg_fieldname($result, $j), "db_m_")) != 0) {
                      if (pg_fieldtype($result, $j) == "date") {
                        if (pg_result($result, $i, $j) != "") {
                          $matriz_data = split("-", pg_result($result, $i, $j));
                          $var_data = $matriz_data[2]."/".$matriz_data[1]."/".$matriz_data[0];
                        } else {
                          $var_data = "//";
                        }
                        echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim($var_data)."</a>" : (trim($var_data)))."&nbsp;</td>\n";
                      } else {
                        if (pg_fieldtype($result, $j) == "float8" || pg_fieldtype($result, $j) == "float4") {
                          $var_data = db_formatar(pg_result($result, $i, $j), 'f', ' ');
                          echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000\" bgcolor=\"$cor\" align=right nowrap>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim($var_data)."</a>" : (trim($var_data)))."&nbsp;</td>\n";
                        } else {
                          if (pg_fieldtype($result, $j) == "bool") {
                            $var_data = (pg_result($result, $i, $j) == 'f' || pg_result($result, $i, $j) == '' ? 'N�o' : 'Sim');
                            echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;align:right\" bgcolor=\"$cor\" nowrap>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim($var_data)."</a>" : (trim($var_data)))."&nbsp;</td>\n";
                          } else {
                            if (pg_fieldtype($result, $j) == "text") {
                              $var_data = substr(pg_result($result, $i, $j), 0, 10)."...";
                              echo "<td onMouseOver=\"js_mostra_text(true,'div_text_".$i."_".$j."',event);\" onMouseOut=\"js_mostra_text(false,'div_text_".$i."_".$j."',event)\" id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;align:right\" bgcolor=\"$cor\" nowrap>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim($var_data)."</a>" : (trim($var_data)))."&nbsp;</td>\n";

                            } else {

                              if (pg_fieldname($result, $j) == 'j01_matric')
                              echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Im�vel' onclick=\"js_JanelaAutomatica('iptubase','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                              else
                              if (pg_fieldname($result, $j) == 'm80_codigo')
                              echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Lan�amento' onclick=\"js_JanelaAutomatica('matestoqueini','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                              else
                              if (pg_fieldname($result, $j) == 'm40_codigo')
                              echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Requisi��o' onclick=\"js_JanelaAutomatica('matrequi','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                              else
                              if (pg_fieldname($result, $j) == 'm42_codigo')
                              echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Atendimento' onclick=\"js_JanelaAutomatica('atendrequi','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                              else
                              if (pg_fieldname($result, $j) == 'm45_codigo')
                              echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Devolu��o' onclick=\"js_JanelaAutomatica('matestoquedev','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                              else
                              if (pg_fieldname($result, $j) == 't52_bem')
                              echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Bem' onclick=\"js_JanelaAutomatica('bem','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                              else
                              if (pg_fieldname($result, $j) == 'q02_inscr')
                              echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Issqn' onclick=\"js_JanelaAutomatica('issbase','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                              else
                              if (pg_fieldname($result, $j) == 'z01_numcgm')
                                                                                                                                echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Contribuinte/Empresa' onclick=\"js_JanelaAutomatica('cgm','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                                                                                                                                //else if(pg_fieldname($result,$j)=='o58_coddot' )
                                                                                                                                //  echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Dota��o Or�ament�ria' onclick=\"js_JanelaAutomatica('orcdotacao','".(trim(pg_result($result,$i,$j)))."','".(trim(pg_result($result,$i,"o58_anousu")))."');return false;\">&nbsp;Inf->&nbsp;</a>".($arquivo!=""?"<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ".($arquivo=="()"?"OnClick=\"".$resultadoRetorno.";return false\">":"onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=".($BrowSe==1?$i:trim(pg_result($result,$i,0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result,$i,$j))."</a>":(trim(pg_result($result,$i,$j))))."&nbsp;</td>\n";
                                                                                                                                //else if(pg_fieldname($result,$j)=='o59_coddot' )
                                                                                                                                //  echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Dota��o Or�ament�ria' onclick=\"js_JanelaAutomatica('orcdotacao','".(trim(pg_result($result,$i,$j)))."','".(trim(pg_result($result,$i,"o59_anousu")))."');return false;\">&nbsp;Inf->&nbsp;</a>".($arquivo!=""?"<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ".($arquivo=="()"?"OnClick=\"".$resultadoRetorno.";return false\">":"onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=".($BrowSe==1?$i:trim(pg_result($result,$i,0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result,$i,$j))."</a>":(trim(pg_result($result,$i,$j))))."&nbsp;</td>\n";
                                                                                                                                //else if(pg_fieldname($result,$j)=='o61_coddot' )
                                                                                                                                //  echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Dota��o Or�ament�ria' onclick=\"js_JanelaAutomatica('orcdotacao','".(trim(pg_result($result,$i,$j)))."','".(trim(pg_result($result,$i,"o61_anousu")))."');return false;\">&nbsp;Inf->&nbsp;</a>".($arquivo!=""?"<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ".($arquivo=="()"?"OnClick=\"".$resultadoRetorno.";return false\">":"onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=".($BrowSe==1?$i:trim(pg_result($result,$i,0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result,$i,$j))."</a>":(trim(pg_result($result,$i,$j))))."&nbsp;</td>\n";
                                                                                                                                //else if(pg_fieldname($result,$j)=='o70_codrec' )
                                                                                                                                //  echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Receita Or�ament�ria' onclick=\"js_JanelaAutomatica('orcreceita','".(trim(pg_result($result,$i,$j)))."','".(trim(pg_result($result,$i,"o70_anousu")))."');return false;\">&nbsp;Inf->&nbsp;</a>".($arquivo!=""?"<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ".($arquivo=="()"?"OnClick=\"".$resultadoRetorno.";return false\">":"onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=".($BrowSe==1?$i:trim(pg_result($result,$i,0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result,$i,$j))."</a>":(trim(pg_result($result,$i,$j))))."&nbsp;</td>\n";
                                                                                                                                //else if(pg_fieldname($result,$j)=='o71_codrec' )
                                                                                                                                //  echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Receita Or�ament�ria' onclick=\"js_JanelaAutomatica('orcreceita','".(trim(pg_result($result,$i,$j)))."','".(trim(pg_result($result,$i,"o71_anousu")))."');return false;\">&nbsp;Inf->&nbsp;</a>".($arquivo!=""?"<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ".($arquivo=="()"?"OnClick=\"".$resultadoRetorno.";return false\">":"onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=".($BrowSe==1?$i:trim(pg_result($result,$i,0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result,$i,$j))."</a>":(trim(pg_result($result,$i,$j))))."&nbsp;</td>\n";
                                                                                                                                //else if(pg_fieldname($result,$j)=='o74_codrec' )
                                                                                                                                //  echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es Receita Or�ament�ria' onclick=\"js_JanelaAutomatica('orcreceita','".(trim(pg_result($result,$i,$j)))."','".(trim(pg_result($result,$i,"o74_anousu")))."');return false;\">&nbsp;Inf->&nbsp;</a>".($arquivo!=""?"<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ".($arquivo=="()"?"OnClick=\"".$resultadoRetorno.";return false\">":"onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=".($BrowSe==1?$i:trim(pg_result($result,$i,0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result,$i,$j))."</a>":(trim(pg_result($result,$i,$j))))."&nbsp;</td>\n";
                                                                                                                                else
                                                                                                                                if (pg_fieldname($result, $j) == 'e60_numemp' || pg_fieldname($result, $j) == 'e61_numemp' || pg_fieldname($result, $j) == 'e62_numemp')
                                                                                                                                echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es do Empenho' onclick=\"js_JanelaAutomatica('empempenho','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                                                                                                                                else
                                                                                                                                if (pg_fieldname($result, $j) == 'e54_autori' || pg_fieldname($result, $j) == 'e55_autori' || pg_fieldname($result, $j) == 'e56_autori')
                                                                                                                                echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es da Autoriza��o de Empenho' onclick=\"js_JanelaAutomatica('empautoriza','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                                                                                                                                else
                                                                                                                                if (pg_fieldname($result, $j) == "pc10_numero")
                                                                                                                                echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap><a title='Informa��es da Solicita��o' onclick=\"js_JanelaAutomatica('empsolicita','". (trim(pg_result($result, $i, $j)))."');return false;\">&nbsp;Inf->&nbsp;</a>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                                                                                                                                else
                                                                                                                                echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;\" bgcolor=\"$cor\" nowrap>". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"".$resultadoRetorno.";return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">").trim(pg_result($result, $i, $j))."</a>" : (trim(pg_result($result, $i, $j))))."&nbsp;</td>\n";
                            }
                          }
                        }
                      }
                    }
                  }
                  echo "</tr>\n";

                  // implamentacao de informacoes complementares
                  //    $divmostra .= "</table>";
                  //    $divmostra .= '<div id="div'.$i.'" name="div'.$i.'" style="position:absolute; left:30px; top:40px; z-index:1; visibility: hidden; border: 1px none #000000; background-color: #CCCCCC; layer-background-color: #CCCCCC;">';
                  //    $divmostra .= '<table  border=\"1\"  align=\"center\" cellspacing=\"1\">';
                  //    $divmostra .= '<tr>';
                  //    $divmostra .= '<td> '.$mostradiv;
                  //    $divmostra .= '</td> ';
                  //    $divmostra .= '</tr> ';
                  //    $divmostra .= '</table>';
                  //    $divmostra .= '</div>';
                }
                //  echo $divmostra;
                /******************************/

                if(count($totalizacao) > 0 ){

                  echo  "<tr>";
                  for ($j = 0; $j < $NumFields; $j ++) {

                    $key_elemento = array_search( pg_fieldname($result, $j), $totalizacao ) ;

                    if( $key_elemento == true && pg_fieldname($result, $j) == $key_elemento && strlen(strstr(pg_fieldname($result, $j), "db_")) == 0 ){

                      @$vertotrep = $HTTP_POST_VARS['totrep_'.$key_elemento];

                      if(@$vertotrep!="" && !isset($tot)){
                        echo "<td style=\"text-decoration:none;color:#000000\" bgcolor=\"white\" align=right nowrap> ".$vertotrep."&nbsp;</td>\n";
                      }else if(isset($tot)){
                        echo "<td style=\"text-decoration:none;color:#000000\" bgcolor=\"white\" align=right nowrap> ".db_formatar(pg_result($tot,0,$key_elemento),'f')."&nbsp;</td>\n";
                      }else{
                        echo "<td style=\"text-decoration:none;color:#000000\" bgcolor=\"white\" align=right nowrap> &nbsp;</td>\n";
                      }
                    }else{
                      if ($key_elemento == 'totalgeral')
                        echo  "<td align='right'><strong>Total Geral : </strong></td>";
                else if (strlen(strstr(pg_fieldname($result, $j), "db_")) == 0)
                      echo  "<td></td>";
                    }
                  }
                  echo "</tr>";

                }

                if ($NumRows > 0) {
                  echo "<tr><td colspan=$NumFields >
              <input name='recomecar' type='button' value='Recome�ar' onclick=\"js_troca_ordem('navega_lov".$NomeForm."','recomecar','0');\">
                          <strong>Indique o Conte�do:</strong><input title='Digite o valor a pesquisar e clique sobre o campo (cabe�alho) a pesquisar' name=indica_codigo type=text onchange='js_lanca_codigo_pesquisa(this.value)' style='background-color:#E6E4F1'>
                  <strong>Quantidade a Listar:</strong><input id=quant_lista name=quant_lista type=text onchange='js_nova_quantidade_linhas(this.value)' style='background-color:#E6E4F1' value='$numlinhas' size='5'>
                  <strong>Mostra Diferentes:</strong><input title='Mostra os valores diferentes clicando no cabe�alho a pesquisar' name=mostra_diferentes type=checkbox onchange='js_lanca_distinct_pesquisa()' style='background-color:#E6E4F1'>
              </td>";
                  echo "</tr>\n";
                }

                echo "</table>";

                for ($i = 0; $i < $NumRows; $i ++) {
                  for ($j = 0; $j < $NumFields; $j ++) {
                    if (pg_fieldtype($result, $j) == "text") {
                      $clrotulocab->label(pg_fieldname($result, $j));
                      echo "<div id='div_text_".$i."_".$j."' style='position:absolute;left:10px; top:10px; visibility:hidden ; background-color:#6699CC ; border:2px outset #cccccc; align:left'>
                                               <table>
                                               <tr>
                                               <td align='left'>
                                                <font color='black' face='arial' size='2'><strong>".$clrotulocab->titulo."</strong>:</font><br>
                                                <font color='black' face='arial' size='1'>".str_replace("\n", "<br>", pg_result($result, $i, $j))."</font>
                                                </td>
                                                </tr>
                                                </table>
                                          </div>";
                    }
                  }

                }
                if ($automatico == true) {
                  if (pg_numrows($result) == 1 && $$offset == 0) {
                    echo "<script>".@ $resultadoRetorno."</script>";
                  }
                }

                return $result;
}

function db_lov($query, $numlinhas, $arquivo = "", $filtro = "%", $aonde = "_self", $mensagem = "Clique Aqui", $NomeForm = "NoMe") {
  global $BrowSe;
  //cor do cabecalho
  global $db_corcabec;
  $db_corcabec = $db_corcabec == "" ? "#CDCDFF" : $db_corcabec;
  //cor de fundo de cada registro
  global $cor1;
  global $cor2;
  $cor1 = $cor1 == "" ? "#97B5E6" : $cor1;
  $cor2 = $cor2 == "" ? "#E796A4" : $cor2;
  global $HTTP_POST_VARS;
  $tot_registros = "tot_registros".$NomeForm;
  $offset = "offset".$NomeForm;
  //recebe os valores do campo hidden
  $$tot_registros = @ $HTTP_POST_VARS["totreg".$NomeForm];
  $$offset = @ $HTTP_POST_VARS["offset".$NomeForm];
  // se for a primeira vez que � rodado, pega o total de registros e guarda no campo hidden
  if (empty ($$tot_registros)) {
    $Dd1 = "disabled";
    $tot = db_query("select count(*) from ($query) as temp");
    $$tot_registros = pg_result($tot, 0, 0);
  }
  // testa qual botao foi pressionado
  if (isset ($HTTP_POST_VARS["pri".$NomeForm])) {
    $$offset = 0;
    $Dd1 = "disabled";
  } else
                if (isset ($HTTP_POST_VARS["ant".$NomeForm])) {
                  if ($$offset <= $numlinhas) {
                    $$offset = 0;
                    $Dd1 = "disabled";
                  } else
                                $$offset = $$offset - $numlinhas;
                } else
                if (isset ($HTTP_POST_VARS["prox".$NomeForm])) {
                                if ($numlinhas >= ($$tot_registros - $$offset - $numlinhas)) {
                                  $$offset = $$tot_registros - $numlinhas;
                                  $Dd2 = "disabled";
                                } else
                                $$offset = $$offset + $numlinhas;
                } else
                if (isset ($HTTP_POST_VARS["ult".$NomeForm])) {
                  $$offset = $$tot_registros - $numlinhas;
                  $Dd2 = "disabled";
                } else {
                  $$offset = @ $HTTP_POST_VARS["offset".$NomeForm] == "" ? 0 : @ $HTTP_POST_VARS["offset".$NomeForm];
                }
                // executa a query e cria a tabela
                $query .= " limit $numlinhas offset ".$$offset;
                $result = db_query($query);
                $NumRows = pg_numrows($result);
                $NumFields = pg_numfields($result);
                if ($NumRows < $numlinhas)
                $Dd1 = $Dd2 = "disabled";
                echo "<table id=\"TabDbLov\" border=\"1\" cellspacing=\"1\" cellpadding=\"0\">\n";
                /**** botoes de navegacao ********/
                echo "<tr><td colspan=\"$NumFields\" nowrap>
          <form name=\"navega_lov".$NomeForm."\" method=\"post\">
            <input type=\"submit\" name=\"pri".$NomeForm."\" value=\"<<\" ".@ $Dd1.">
            <input type=\"submit\" name=\"ant".$NomeForm."\" value=\"<\" ".@ $Dd1.">
            <input type=\"submit\" name=\"prox".$NomeForm."\" value=\">\" ".@ $Dd2.">
            <input type=\"submit\" name=\"ult".$NomeForm."\" value=\">>\" ".@ $Dd2.">
                <input type=\"hidden\" name=\"offset".$NomeForm."\" value=\"".$$offset."\">
                <input type=\"hidden\" name=\"totreg".$NomeForm."\" value=\"".$$tot_registros."\">
                <input type=\"hidden\" name=\"filtro\" value=\"$filtro\">
          </form>". ($NumRows > 0 ? "
          Foram retornados <font color=\"red\"><strong>".$$tot_registros."</strong></font> registros.
          Mostrando de <font color=\"red\"><strong>". ($$offset +1)."</strong></font> at�
          <font color=\"red\"><strong>". ($$tot_registros < ($$offset + $numlinhas) ? $NumRows : ($$offset + $numlinhas))."</strong></font>." : "Nenhum Registro
          Retornado")."
          </td></tr>\n";
                /*********************************/
                /***** Escreve o cabecalho *******/
                if ($NumRows > 0) {
                  echo "<tr>\n";
                  for ($i = 0; $i < $NumFields; $i ++) {
                    if (strlen(strstr(pg_fieldname($result, $i), "db")) == 0)
                    echo "<td nowrap bgcolor=\"$db_corcabec\"  style=\"font-size:13px\" align=\"center\"><b><u>".ucfirst(pg_fieldname($result, $i))."</u></b></td>\n";
                  }
                  echo "</tr>\n";
                }
                /********************************/
                /****** escreve o corpo *******/
                for ($i = 0; $i < $NumRows; $i ++) {
                  echo "<tr>\n";
                  $cor = @ $cor == $cor1 ? $cor2 : $cor1;
                  for ($j = 0; $j < $NumFields; $j ++) {
                    if (strlen(strstr(pg_fieldname($result, $j), "db")) == 0)
                    echo "<td id=\"I".$i.$j."\" style=\"text-decoration:none;color:#000000;font-size:13px\" bgcolor=\"$cor\" nowrap>
                   ". ($arquivo != "" ? "<a title=\"$mensagem\" style=\"text-decoration:none;color:#000000;font-size:13px\" href=\"\" ". ($arquivo == "()" ? "OnClick=\"js_retornaValor('I".$i.$j."');return false\">" : "onclick=\"JanBrowse = window.open('".$arquivo."?".base64_encode("retorno=". ($BrowSe == 1 ? $i : trim(pg_result($result, $i, 0))))."','$aonde','width=800,height=600');return false\">"). (trim(pg_result($result, $i, $j)) == "" ? "&nbsp;" : trim(pg_result($result, $i, $j)))."</a>" : (trim(pg_result($result, $i, $j)) == "" ? "&nbsp;" : trim(pg_result($result, $i, $j))))."</td>\n";
                  }
                  echo "</tr>\n";
                }
                /******************************/
                echo "</table>";

                return $result;
}

//Insere um registro de log
function db_logs($string = '', $codcam = 0, $chave = 0) {
$wheremod="";
if(session_is_registered("DB_modulo")){
  $wheremod =  "and modulo = ".db_getsession("DB_modulo");
}
  $sql = "select db_itensmenu.id_item
                  from db_itensmenu
            inner join db_menu  on  db_menu.id_item_filho = db_itensmenu.id_item
                  where trim(funcao) = '".trim(addslashes( basename( $_SERVER["REQUEST_URI"] ) ))."'
            $wheremod limit 1 ";

  //echo $sql;
  //die();
  $result = db_query($sql);
  if ($result != false && pg_numrows($result) > 0) {
    db_query("BEGIN");
    $item = pg_result($result, 0, 0);

    $sql = "select nextval('db_logsacessa_codsequen_seq')";
    $result = db_query($sql);
    $codsequen = pg_result($result, 0, 0);

    // grava codigo na sessao
    db_putsession("DB_itemmenu_acessado",$item);
    db_putsession("DB_acessado",$codsequen);


    $sql = "INSERT INTO db_logsacessa VALUES ($codsequen,
                                              '".db_getsession("DB_ip")."',
                                              '".date("Y-m-d")."',
                                              '".date("H:i:s")."',
                                              '".$_SERVER["REQUEST_URI"]."',
                                              '$string',
                                              ".db_getsession("DB_id_usuario").",
                                              ".db_getsession("DB_modulo").",
                                              ".$item.",
                                              ".db_getsession("DB_coddepto").",
                                              ".db_getsession("DB_instit").")";
    $result = db_query($sql);
    if (pg_cmdtuples($result) > 0) {
      db_query("COMMIT");
    } else {
      db_query("ROLLBACK");
    }
  }
}

function db_logsmanual($string = '', $modulo = 0, $item = 0, $codcam = 0, $chave = 0) {
  db_query("BEGIN");

  $sql = "select nextval('db_logsacessa_codsequen_seq')";
  $result = db_query($sql);
  $codsequen = pg_result($result, 0, 0);
  $sql = "INSERT INTO db_logsacessa VALUES ($codsequen,'".db_getsession("DB_ip")."','".date("Y-m-d")."','".date("H:i:s")."','".$_SERVER["REQUEST_URI"]."','$string',".db_getsession("DB_id_usuario").",".$modulo.",".$item.",".db_getsession("DB_coddepto").",".db_getsession("DB_instit").")";
  $result = db_query($sql);
  if (pg_cmdtuples($result) > 0) {
    db_query("COMMIT");
  } else {
    db_query("ROLLBACK");
  }

}

function db_logsmanual_demais($string = '', $id_usuario=0, $modulo = 0, $item = 0, $coddepto = 0, $instit = 0) {
  db_query("BEGIN");

  global $SERVER, $HTTP_SERVER_VARS;
  if (isset ($_SERVER["HTTP_X_FORWARDED_FOR"])) {
     $db_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
  } else {
     $db_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
  }

  $sql = "select nextval('db_logsacessa_codsequen_seq')";
  $result = db_query($sql);
  $codsequen = pg_result($result, 0, 0);
  $sql = "INSERT INTO db_logsacessa VALUES ($codsequen,'$db_ip','".date("Y-m-d")."','".date("H:i:s")."','".$_SERVER["REQUEST_URI"]."','$string',$id_usuario,$modulo,$item,$coddepto,$instit)";
  $result = db_query($sql);
  if (pg_cmdtuples($result) > 0) {
    db_query("COMMIT");
  } else {
    db_query("ROLLBACK");
  }

}


function db_menu($usuario, $modulo, $anousu, $instit) {
  //#00#//db_menu
  //#10#//Esta funcao cria o menu nos programas
  //#15#//db_menu($usuario,$modulo,$anousu,$instit);
  //#20#//Usuario  : Id do usu�rio do arquivo |db_usuarios|
  //#20#//Modulo   : C�digo do M�dulo
  //#20#//Anousu   : Exerc�cio de Acesso
  //#20#//Instit   : N�mero da institui��o

  global $HTTP_SERVER_VARS, $HTTP_SESSION_VARS;
  global $conn, $DB_SELLER;

  /**
   * Busca a prefer�nia do usu�rio
   */
  $oPreferencias = unserialize(base64_decode(db_getsession('DB_preferencias_usuario')));

  switch ($oPreferencias->getOrdenacao()) {
    case 'sequencial':
      $sOrdenacao = 'menusequencia';
    break;
    case 'alfabetico':
      $sOrdenacao = 'descricao';
    break;
    default:
      $sOrdenacao = 'menusequencia';
    break;
  }

  if ($usuario == 1 || db_getsession("DB_administrador") == 1) {

    $sql  = "select m.id_item,m.id_item_filho,m.menusequencia,i.descricao,i.help,i.funcao,i.desctec ";
    $sql .= "  from db_menu m                                                                       ";
    $sql .= "       inner join db_itensmenu i on i.id_item = m.id_item_filho                        ";
    $sql .= "where m.modulo = $modulo and                                                           ";
    $sql .= "      i.itemativo = '1' and libcliente is true                                         ";
    $sql .= "order by $sOrdenacao                                                                   ";
  } else {

    $sql  = "  select m.id_item,m.id_item_filho,m.menusequencia,i.descricao,i.help,i.funcao,i.desctec  ";
    $sql .= "    from db_menu m                                                                        ";
    $sql .= "         inner join db_permissao p on p.id_item = m.id_item_filho                         ";
    $sql .= "         inner join db_itensmenu i on i.id_item = m.id_item_filho                         ";
    $sql .= "                                  and p.permissaoativa = '1'                              ";
    $sql .= "                                  and p.anousu = $anousu                                  ";
    $sql .= "                                  and p.id_instit = $instit                               ";
    $sql .= "                                  and p.id_modulo = $modulo                               ";
    $sql .= "   where p.id_usuario = $usuario                                                          ";
    $sql .= "      and m.modulo    = $modulo                                                           ";
    $sql .= "      and i.itemativo = '1'                                                               ";
    if (isset($DB_SELLER)) {
      $sql .= "    and i.libcliente = true ";
    }
    $sql .= " union                                                                                     ";
    $sql .= "  select m.id_item,m.id_item_filho,m.menusequencia,i.descricao,i.help,i.funcao,i.desctec   ";
    $sql .= "    from db_menu m                                                                         ";
    $sql .= "         inner join db_permherda h on h.id_usuario     = $usuario                          ";
    $sql .= "         inner join db_usuarios  u on u.id_usuario     = h.id_perfil                       ";
    $sql .= "                                  and u.usuarioativo   = '1'                               ";
    $sql .= "         inner join db_permissao p on p.id_item        = m.id_item_filho                   ";
    $sql .= "         inner join db_itensmenu i on i.id_item        = m.id_item_filho                   ";
    $sql .= "                                  and p.permissaoativa = '1'                               ";
    $sql .= "                                  and p.anousu         = $anousu                           ";
    $sql .= "                                  and p.id_instit      = $instit                           ";
    $sql .= "                                  and p.id_modulo      = $modulo                           ";
    $sql .= "  where p.id_usuario = h.id_perfil                                                         ";
    $sql .= "    and m.modulo = $modulo                                                                 ";
    $sql .= "    and i.itemativo = '1'                                                                  ";
    if (isset ($db_seller)) {
      $sql .= "  and i.libcliente = true ";
    }
    $sql .= "  order by $sOrdenacao                                     															  ";
  }
  $menu = db_query($sql);
  $NumMenu = pg_numrows($menu);
  $help_descricao = "";
  $rotinadb = "";
  if ($NumMenu != 0) {
    echo "<div class=\"menuBar\" id='dbmenu' style=\"width:100%;position:absolute;left:0px;top:0px\">\n";
    $gera_helps = "";
    for ($i = 0; $i < $NumMenu; $i ++) {
      //$URI = pg_result($menu,$i,5) == ""?"":"http://".$HTTP_SERVER_VARS["HTTP_HOST"].substr($HTTP_SERVER_VARS["PHP_SELF"],0,strrpos($HTTP_SERVER_VARS["PHP_SELF"],"/"))."/".pg_result($menu,$i,5);
      if (pg_result($menu, $i, 0) == $modulo) {
        echo "<a class=\"menuButton\" href=\"\" onclick=\"return buttonClick(event, 'Ijoao".pg_result($menu, $i, 1)."');\" onmouseover=\"buttonMouseover(event, 'Ijoao".pg_result($menu, $i, "id_item_filho")."');\">".pg_result($menu, $i, "descricao")."</a>\n";
      }
      if (strtolower(basename($HTTP_SERVER_VARS["PHP_SELF"])) == strtolower(pg_result($menu, $i, 5))) {
        $rotinadb = pg_result($menu, $i, 4);
      }
      $db_funcao = trim(pg_result($menu, $i, 'funcao'));
      if ($gera_helps == "" && $db_funcao == basename($HTTP_SERVER_VARS["PHP_SELF"])) {
        $gera_helps = pg_result($menu, $i, 'id_item_filho');
        $help_descricao = pg_result($menu, $i, 'desctec');
      }
    }
    //   echo "<a class=\"menuButton\" id=\"menuSomeTela\" href=\"\" onclick=\"someFrame(event,1); return false\">Tela</a>\n";
    echo "<a class=\"menuButton\" id=\"menuModulosTela\" href=\"\" onclick=\"return buttonClick(event,'IListaAreas');return false\">M�dulos</a>\n";
    echo "<a class=\"menuButton\" id=\"menuMostraHelp\" href=\"\" onclick=\"return buttonClick(event,'IMostraHelpMenu');return false\">Help</a>\n";
    //echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a class=\"menuItem\" onMouseover=\"js_cria_objeto_div('mensagem_menu','Mensagem')\" onMouseout=\"js_remove_objeto_div('mensagem_menu')\" id=\"menuhelp\" href=\"\" onclick=\"buttonMensagemMenu();return false\">**** Voc� recebeu Mensagens ****</a>\n";
    

    $oPreferencias = unserialize(base64_decode(db_getsession('DB_preferencias_usuario')));
    if ($oPreferencias->getExibeBusca() == '1') {
      echo "<div style=\"margin-right:20px; float:right;\" id=\"autoComplete\">";
      echo "<label>Busca r�pida de menus: </label>";
      echo "<input type=\"text\" id=\"autoCompleteMenus\" name=\"autoCompleteMenus\" autoComplete=\"off\" style=\"width: 300px; height: 15px;\" />";
      echo "</div>";
    }

    echo "</div>\n";
    for ($i = 0; $i < $NumMenu; $i ++) {
      for ($j = 0; $j < $NumMenu; $j ++) {
        if (pg_result($menu, $j, "id_item") == pg_result($menu, $i, "id_item_filho")) {
          echo "<div id=\"Ijoao".pg_result($menu, $i, "id_item_filho")."\" class=\"menu\" onmouseover=\"menuMouseover(event)\" style=\"z-index:800;\">\n ";
          for ($a = 0; $a < $NumMenu; $a ++) {
            if (pg_result($menu, $j, "id_item") == pg_result($menu, $a, "id_item")) {
              $verifica = 1;
              for ($b = 0; $b < $NumMenu; $b ++) {
                if (pg_result($menu, $a, "id_item_filho") == pg_result($menu, $b, "id_item")) {
                  echo "<a class=\"menuItem\" href=\"\"  onclick=\"return false;\"  onmouseover=\"menuItemMouseover(event, 'Ijoao".pg_result($menu, $a, "id_item_filho")."');\">\n";
                  echo "<span class=\"menuItemText\">".pg_result($menu, $a, "descricao")."</span>\n";
                  echo "<span class=\"menuItemArrow\"><strong>>>></strong></span></a>\n";
                  $verifica = 0;
                  break;
                }
              }
              if ($verifica == 1)
              echo "<a class=\"menuItem\" id=\"DBmenu_".pg_result($menu, $a, "id_item_filho")."\" href=\"".pg_result($menu, $a, "funcao")."\"  onclick='return js_db_menu_confirma();'>".ucfirst(pg_result($menu, $a, "descricao"))."</a>\n";
            }
          }
          echo "</div>\n";
          break;
        }
      }
    }

    if ($usuario == 1 || db_getsession("DB_administrador") == 1) {
      $sql = "select distinct         atendcadarea.at25_descr,
                                              db_itensmenu.id_item,
                                                  db_modulos.nome_modulo,
                                                extract (year from current_date) as anousu
                                          from db_itensmenu
                                              inner join db_menu on db_itensmenu.id_item         = db_menu.id_item
                                              inner join db_modulos on db_modulos.id_item         = db_itensmenu.id_item
                                              inner join atendcadareamod on db_itensmenu.id_item = atendcadareamod.at26_id_item
                                              inner join atendcadarea on atendcadarea.at26_sequencial =  atendcadareamod.at26_codarea

                                          where libcliente is true
                                          order by atendcadarea.at25_descr,nome_modulo ";
      $resultmodulo = db_query($sql);
    } else {
      $sqlmodulo = "select atendcadarea.at25_descr,x.id_item, nome_modulo, max(x.anousu) as anousu from (
                                  select distinct i.id_modulo as id_item,m.nome_modulo,
                                                 case when u.anousu is null then ".db_getsession("DB_anousu")." else u.anousu end
                                          from
                                              (
                                               select distinct i.itemativo,p.id_modulo,p.id_usuario,p.id_instit
                                               from db_permissao p
                                                    inner join db_itensmenu i on p.id_item = i.id_item
                                               where i.itemativo = '1'
                                                    and p.id_usuario = ".db_getsession("DB_id_usuario")."
                                                    and p.id_instit = ".db_getsession("DB_instit")."
						                                        and p.anousu = ".(isset($HTTP_SESSION_VARS["DB_datausu"])?date("Y",db_getsession("DB_datausu")):date("Y"))."
                                                    AND libcliente is true
                                              ) as i
                                              inner join db_modulos m  on m.id_item = i.id_modulo
                                              inner join db_itensmenu it on it.id_item = i.id_modulo
                                              left outer join db_usumod u  on u.id_item = i.id_modulo and u.id_usuario = i.id_usuario
                                          where i.id_usuario = ".db_getsession("DB_id_usuario")."
                                                and i.id_instit = ".db_getsession("DB_instit")."

                                          union
                                          select distinct i.id_modulo as id_item,m.nome_modulo,
                                                 case when u.anousu is null then ".db_getsession("DB_anousu")." else u.anousu end
                                          from
                                              (
                                               select distinct i.itemativo,p.id_modulo,p.id_usuario,p.id_instit
                                               from db_permissao p
        					                                  inner join db_permherda h on h.id_perfil = p.id_usuario
                                                    inner join db_itensmenu i on p.id_item = i.id_item
                                               where i.itemativo = '1'
                                                    and p.id_usuario in (select id_perfil from db_permherda where id_usuario = ".db_getsession("DB_id_usuario").")
                                                    and p.id_instit = ".db_getsession("DB_instit")."
						                                        and p.anousu = ".(isset($HTTP_SESSION_VARS["DB_datausu"])?date("Y",db_getsession("DB_datausu")):date("Y"))."
                                                    and libcliente is true
                                              ) as i
                                              inner join db_modulos m  on m.id_item = i.id_modulo
                                              inner join db_itensmenu it on it.id_item = i.id_modulo
                                              left outer join db_usumod u  on u.id_item = i.id_modulo and u.id_usuario = i.id_usuario
                                          where i.id_instit = ".db_getsession("DB_instit")."
                                          order by nome_modulo) as x
                                              inner join atendcadareamod on id_item = atendcadareamod.at26_id_item
                                              inner join atendcadarea on atendcadarea.at26_sequencial =  atendcadareamod.at26_codarea
                                              inner join db_usumod       on db_usumod.id_item = atendcadareamod.at26_id_item
                                                                     and db_usumod.id_usuario = ".db_getsession("DB_id_usuario")."
                                          group by at25_descr, x.id_item, nome_modulo
                                          order by at25_descr,nome_modulo";

      $resultmodulo = db_query($sqlmodulo) or die($sqlmodulo);
    }
    $NumModulos = pg_numrows($resultmodulo);

    $lista_areas = true;
    if ( $NumModulos > 15 ) {
      $lista_areas = true;
    }

    if (  $usuario == 1 || db_getsession("DB_administrador") == 1 || $lista_areas == true ){
      // for para listar areas
      $area = '';
      echo "<div id=\"IListaAreas\" class=\"menu\" onmouseover=\"menuMouseover(event)\">\n";
      for ( $i_area=0 ; $i_area < $NumModulos ; $i_area ++ ) {
        if($area == '' || $area != pg_result($resultmodulo, $i_area, "at25_descr") ){
          $area = pg_result($resultmodulo, $i_area, "at25_descr");
          echo "<a class=\"menuItem\" href=\"\"  onclick=\"return false;\"  onmouseover=\"menuItemMouseover(event, 'IListaArea_$area');\">\n";
          echo "<span class=\"menuItemText\">$area </span>";
          echo "<span class=\"menuItemArrow\"><strong> >>> </strong></span></a>\n";
        }
      }
      echo "</div>";
    }else{
      echo "<div id=\"IListaAreas\" class=\"menu\" onmouseover=\"menuMouseover(event)\">\n";
    }

    // for para listar modulos
    $lista_outros = 0;
    $area = '';
    for ($i = 0; $i < $NumModulos; $i ++) {
      if ( $lista_areas == true ){
        if($area == '' || $area != pg_result($resultmodulo, $i, "at25_descr") ){
          if($area != '' ){
            echo "</div>";
            $lista_outros = 0;
          }
          $area = pg_result($resultmodulo, $i, "at25_descr");
          echo "<div id=\"IListaArea_$area\" class=\"menu\" onmouseover=\"menuMouseover(event)\">\n";
        }
      }
      if ( $lista_outros < 21) {
        $lista_outros ++;
        echo "<a class=\"menuItem\" href=\"modulos.php?".base64_encode("anousu=".pg_result($resultmodulo, $i, "anousu")."&modulo=".pg_result($resultmodulo, $i, "id_item")."&nomemod=".pg_result($resultmodulo, $i, "nome_modulo"))."\"  onclick='return js_db_menu_confirma();'>".pg_result($resultmodulo, $i, 'nome_modulo')."</a>\n";
      } else {
        echo "<a class=\"menuItem\" href=\"\"  onclick=\"return false;\"  onmouseover=\"menuItemMouseover(event, 'IListaModulosOutros');\">\n";
        echo "<span class=\"menuItemText\">Outros ...</span>\n";
        echo "<span class=\"menuItemArrow\"><strong>>>></strong></span></a>\n";
        echo "</div>";
        echo "<div id=\"IListaModulosOutros\" class=\"menu\" onmouseover=\"menuMouseover(event)\">\n";
        echo "<a class=\"menuItem\" href=\"modulos.php?".base64_encode("anousu=".pg_result($resultmodulo, $i, "anousu")."&modulo=".pg_result($resultmodulo, $i, "id_item")."&nomemod=".pg_result($resultmodulo, $i, "nome_modulo"))."\"  onclick='return js_db_menu_confirma();'>".pg_result($resultmodulo, $i, 'nome_modulo')."</a>\n";
        $lista_outros = 0;
      }
    }
    echo "</div>\n";

    // Menu do Help

    echo "<div id=\"IMostraHelpMenu\" class=\"menu\" onmouseover=\"menuMouseover(event)\">\n";
    echo "<a class=\"menuItem\" onMouseover=\"js_cria_objeto_div('help','$help_descricao')\" onMouseout=\"js_remove_objeto_div('help')\" id=\"menuhelp\" href=\"\" onclick=\"buttonHelp('".basename($HTTP_SERVER_VARS["PHP_SELF"])."','".$gera_helps."','".$modulo."',true);return false\">Help</a>\n";
    echo "<a class=\"menuItem\" onMouseover=\"js_cria_objeto_div('versao','$help_descricao')\" onMouseout=\"js_remove_objeto_div('versao')\" id=\"menuMostraVersao\" href=\"\" onclick=\"buttonHelp('".basename($HTTP_SERVER_VARS["PHP_SELF"])."','".$gera_helps."','".$modulo."',false);return false\">Vers�es</a>\n";
    echo "</div>\n";

    //$msg = ucfirst(db_getsession("DB_nome_modulo"))." -> ".ucfirst($rotinadb)." -> ".basename($HTTP_SERVER_VARS["PHP_SELF"]);

    if (isset ($HTTP_SESSION_VARS["DB_coddepto"])) {
      $result = @ db_query("select descrdepto from db_depart where coddepto = ".db_getsession("DB_coddepto"));
      if ($result != false && pg_numrows($result)>0) {
        $descrdep = "[<strong>".db_getsession("DB_coddepto")."-".substr(pg_result($result, 0, 'descrdepto'), 0, 40)."</strong>]";
      } else {
        $descrdep = "";
      }
    } else {
      $descrdep = "";
    }
    $msg = ucfirst(db_getsession("DB_nome_modulo")).$descrdep."->".ucfirst($rotinadb);

    //if(db_getsession("DB_anousu")!=date("Y")){
    //  echo "<script>alert('Voc� esta acessando um exerc�cio diferente. Verifique!')</script>";
    //}

    echo "<script>
            parent.bstatus.document.getElementById('st').innerHTML = '&nbsp;&nbsp;$msg' ;
            parent.bstatus.document.getElementById('dtatual').innerHTML = '".date("d/m/Y", db_getsession("DB_datausu"))."' ;
            parent.bstatus.document.getElementById('dtanousu').innerHTML = '".db_getsession("DB_anousu")."' ;
            //
            //colocar esta funcao em todos os itens dos menus
            //onclick='return js_db_menu_confirma();'
            function js_db_menu_confirma () {
              //if( js_db_menu_retorno != null )
              if(typeof(js_db_menu_retorno) != 'undefined')
                var retorno  = js_db_menu_retorno();
              else
                var retorno = true;
              return retorno;
            }

            if(document.getElementById('autoCompleteMenus')){
               autoCompleteMenu();
            }

          </script>";

  } else {
    echo "Sem permissao de menu!";
  }

}

// acessa menu
function db_acessamenu($item_menu, $descr, $acao) {
  //#00#//db_acessamenu
  //#10#//Esta funcao acessa o menu de permiss�es do usu�rio
  //#15#//db_acessamenu($item_menu,$acao);
  //#20#//Item menu : Item de menu que ser� acessado
  //#20#//A��o      : A��o a executar quando clicado no menu
  //#20#//            1 - Abrir Janela de Iframe
  //#20#//            2 - Redirecionar para o �tem
  $sql = "select m.descricao
                  from db_permissao p
                       inner join db_itensmenu m on m.id_item = p .id_item
                  where p.anousu = ".db_getsession("DB_anousu")." and p.id_item = $item_menu and id_usuario = ".db_getsession("DB_id_usuario");
  $res = db_query($sql);
  if (pg_numrows($res) > 0) {
    $descri = pg_result($res, 0, 'descricao');
    echo " <input name='db_acessa_menu_".$item_menu."' value='".$descr."' type='button' onclick=\"document.getElementById('DBmenu_".$item_menu."').click();\" title='$descri'>  ";
  }
}

// emite o valor por extenso ( em moeda )
function db_extenso($valor = 0, $maiusculas = false) {
                //#00#//db_extenso
                //#10#//Esta funcao retorna um valor por extenso em maiusculo ou n�o
                //#15#//db_extenso($valor,$maiusculo);
                //#20#//Valor    : Valor a ser gerado
                //#20#//Maiusculo: Se retorna a string gerada em maiusculo ou n�o

                $rt = '';
                $singular = array ("centavo", "real", "mil", "milh�o", "bilh�o", "trilh�o", "quatrilh�o");
                $plural = array ("centavos", "reais", "mil", "milh�es", "bilh�es", "trilh�es", "quatrilh�es");

                $c = array ("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
                $d = array ("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
                $d10 = array ("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
                $u = array ("", "um", "dois", "tr�s", "quatro", "cinco", "seis", "sete", "oito", "nove");

                $z = 0;

                $valor = number_format($valor, 2, ".", ".");
                $inteiro = explode(".", $valor);
                for ($i = 0; $i < count($inteiro); $i ++)
                for ($ii = strlen($inteiro[$i]); $ii < 3; $ii ++)
                $inteiro[$i] = "0".$inteiro[$i];

                $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
                for ($i = 0; $i < count($inteiro); $i ++) {
                  $valor = $inteiro[$i];
                  $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
                  $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
                  $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

                  $r = $rc. (($rc && ($rd || $ru)) ? " e " : "").$rd. (($rd && $ru) ? " e " : "").$ru;
                  $t = count($inteiro) - 1 - $i;
                  $r .= $r ? " ". ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
                  if ($valor == "000")
                  $z ++;
                  elseif ($z > 0) $z --;
                  if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
                  $r .= (($z > 1) ? " de " : "").$plural[$t];
                  //        $rt = '';
                  if ($r)
                  $rt = $rt. ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ").$r;
                }

                if (!$maiusculas) {
                  return ($rt ? $rt : "zero");
                } else { /*
                  Trocando o " E " por " e ", fica muito + apresent�vel!
                  Rodrigo Cerqueira, rodrigobc@fte.com.br
                  */
                  if ($rt)
                  $rt = ereg_replace(" E ", " e ", ucwords($rt));
                  return (($rt) ? ($rt) : "Zero");
                }

}
function db_permissaomenu($ano, $modulo, $item) {
  //#00#//db_permissaomenu
  //#10#//Esta funcao verifica se o usuario tem permissao de menu do item no ano e modulo especificado
  //#15#//db_permissaomenu($ano,$modulo,$item);
  //#20#//Ano: ano que deseja utilizar para verificar o acesso
  //#20#//Modulo: codigo do modulo que deseja utilizar para verificar o acesso
  //#20#//Item: codigo do item que deseja utilizar para verificar o acesso
  //#20#//Sempre ser� utilizado o usuario atual para a verificacao
  if (db_getsession("DB_id_usuario") == 1 || db_getsession("DB_administrador") == 1) {
    return "true";
  } else {
    $sql = "select id_item
                                                                from db_permissao
                                where anousu = $ano and id_modulo = $modulo and id_item = $item and id_usuario = ".db_getsession("DB_id_usuario") . " and db_permissao.id_instit = " . db_getsession("DB_instit") . " union
                                select id_item
                                from db_permissao
                                inner join db_permherda on db_permherda.id_perfil = db_permissao.id_usuario
                                where db_permissao.anousu = $ano and db_permissao.id_modulo = $modulo and db_permissao.id_item = $item and db_permherda.id_usuario = ".db_getsession("DB_id_usuario") . " and db_permissao.id_instit = " . db_getsession("DB_instit");
    //echo $sql;exit;
    $res = db_query($sql);
    if (pg_numrows($res) == 0) {
      return "false";
    } else {
      return "true";
    }
  }
}

// ---------------------
function debug($classe, $func = false) {
  print_vars($classe);
  if ($func == true)
                print_methods($classe);

}
function print_vars($obj) {
  $arr = get_object_vars($obj);
  echo "<table border=0 bgcolor=#AAAAFF style='border:1px solid'>";
  echo "<tr><td colspan=3>Debug da classe ".get_class($obj)." </td></tr>";
  while (list ($prop, $val) = each($arr))
                echo "<tr><td>&nbsp; </td><td align=left>var $$prop </td><td> $val </td>";
}
function print_methods($obj) {
  echo "<tr><td colspan=3>Metodos encontrados </td></tr>";
  $arr = get_class_methods(get_class($obj));
  foreach ($arr as $method)
                echo "<tr><td>&nbsp; </td><td colspan=2>function $method() </td>";
                echo "<table>";
}

function db_base_ativa() {
  //#00#//db_base_ativa
  //#10#//Esta funcao verifica se a variavel DB_NBASE esta setada para quando troca de base pelo info
  //#15#//db_base_ativa();
  //#20#//dbnbase=Nome da Base de Dados
  if (isset ($GLOBALS["DB_NBASE"])) {
    return $GLOBALS["DB_NBASE"];
  } else {
    return $GLOBALS["DB_BASE"];
  }
}

function db_criatermometro($dbnametermo='termometro',$dbtexto='Conclu�do',$dbcor='blue',$dbborda=1,$dbacao='Aguarde Processando...'){

  //#00#//db_criatermometro
  //#10#//Cria uma barra de progresso no ponto do programa que for chamado
  //#15#//db_criatermometro('termometro','Conclu�do','blue',1);
  //#20#//dbnametermo = Nome do termometro e da funcao js que atualiza o termometro
  //#20#//dbtexto     = Texto mostrado no lado da porcentagem concluida
  //#20#//dbcor       = Cor do termometro
  //#20#//dbborda     = Borda, 1 com borda ou 2 sem borda
  //#20#//dbacao      = Texto para acao executada ex: Aguarde Processando...
  //#99#//Essa fun��o apenas cria o termometro, para atualizar o valor do termometro deve usar a funcao db_atutermometro

  if($dbborda !=1 && $dbborda !=0){
    $dbborda=1;
  }
  /*
   bkp
   <table border=".$dbborda." cellspacing=0 cellpadding=0>
   */
  echo "<table align='center' marginwidth='0' width='790' border='0' cellspacing='0' cellpadding='0'>";
  echo "<tr>
                  <td align='center'>
                   <b> $dbacao </b>
                  </td>
                </tr>";
  echo "<tr>
                    <td align='center'>";
  echo "   </td>
                 </tr>";
  echo "<tr>
                    <td align='center'>
                      <table style='border-collapse: collapse; border:1px solid #525252;' cellspacing=0 cellpadding=0>
                      <tr><td>";
  echo "        <table border=0 cellspacing=0 cellpadding=0>";
  echo "           <tr>
                               <td>";
  echo "                   <input name='".$dbnametermo."' style='background: transparent;text-align:center' id='dbtermometro".$dbnametermo."' type='text' value='' size=100 readonly>
                               </td>
                           </tr>";
  echo "           <tr>
                               <td>";
  echo "                   <input name='barra".$dbnametermo."' style='background: ".$dbcor.";text-align:center;visibility:hidden' id='dbbarra".$dbnametermo."' type='text' value='' size=0 readonly> ";
  echo "              </td>
                            </tr>";
  echo "        </table>
                     </td></tr>
                     </table>";
  echo "   </td>
                 </tr>";
  echo "</table>";
  echo "<script>
                    function js_termo_".$dbnametermo."(atual,texto){
                            atual = new Number(atual);
                            dbtexto = (texto==null)?'{$dbtexto}':texto;
                            document.getElementById('dbtermometro".$dbnametermo."').value = ' '+atual.toFixed(0)+'%'+' '+dbtexto;
                            document.getElementById('dbbarra".$dbnametermo."').size = atual;
                            document.getElementById('dbbarra".$dbnametermo."').style.visibility = 'visible';
                      }
                </script>";
}

function db_atutermometro($dblinha,$dbrows,$dbnametermo,$dbquantperc=1,$dbtexto=null){

  //#00#//db_atutermometro
  //#10#//Atualiza o valor do termometro
  //#15#//db_atutermometro($i,$numrows,'termometro',1);
  //#20#//dblinha       = linha que esta atualmente
  //#20#//dbrows        = total de registros
  //#20#//dbnametermo   = nome do termometro q foi criado com o db_criatermometro
  //#20#//dbquantperc   = percentual que a barra sera atualizada

  $percatual = (($dblinha+1) * 100) / $dbrows;
  if($percatual % $dbquantperc == 0){
    if(is_null($dbtexto)) {
      echo "<script>js_termo_".$dbnametermo."($percatual);</script>";
    } else {
      echo "<script>js_termo_".$dbnametermo."($percatual,'$dbtexto');</script>";
    }
    flush();
  }
}
function db_criacarne($arretipo,$ip,$datahj,$instit,$tipomod){

  global $k47_sequencial;
  global $k47_descr;
  global $k47_obs;
  global $k47_altura;
  global $k47_largura;
  global $k47_orientacao;

  global $k47_tipoconvenio; // 1 se for arrecadacao ou 2 se for cobranca
  global $k22_cadban;       // codigo do banco no caso de ser cobranca

  $intnumexe   = 0;
  $intnumtipo  = 0;
  $intnumgeral = 0;
  $achou       = 0;

  //  die($arretipo." -- ".$ip." -- ".$datahj." -- ".$instit." -- ".$tipomod);
  $sqlexe = "  select * from cadmodcarne
                       inner join modcarnepadrao         on cadmodcarne.k47_sequencial                = modcarnepadrao.k48_cadmodcarne
                       left  join modcarnepadraocobranca on modcarnepadraocobranca.k22_modcarnepadrao = modcarnepadrao.k48_sequencial
                       inner join modcarnepadraotipo     on modcarnepadrao.k48_sequencial             = modcarnepadraotipo.k49_modcarnepadrao
                       inner join modcarneexcessao       on modcarneexcessao.k36_modcarnepadraotipo   = modcarnepadraotipo.k49_sequencial
                 where k36_ip         = '".$ip."'
                   and k49_tipo       = $arretipo
                   and k48_dataini    <='".$datahj."'
                   and k48_datafim    >='".$datahj."'
                   and k48_instit     = $instit
                   and k48_cadtipomod = $tipomod  ";
  //  die($sqlexe);
  $rsModexe   = db_query($sqlexe);
  $intnumexe  = pg_numrows($rsModexe);
  if(isset($intnumexe) && $intnumexe > 0 ){
    db_fieldsmemory($rsModexe,0);
    //    db_msgbox("achou excessao");
    $achou = 1;
  }
  if($achou == 0){
    $sqltipo = " select * from cadmodcarne
                        inner join modcarnepadrao         on cadmodcarne.k47_sequencial                = modcarnepadrao.k48_cadmodcarne
                        left  join modcarnepadraocobranca on modcarnepadraocobranca.k22_modcarnepadrao = modcarnepadrao.k48_sequencial
                        inner join modcarnepadraotipo     on modcarnepadrao.k48_sequencial             = modcarnepadraotipo.k49_modcarnepadrao
                        left  join modcarneexcessao       on modcarneexcessao.k36_modcarnepadraotipo   = modcarnepadraotipo.k49_sequencial
           where k49_tipo = $arretipo
             and k48_dataini    <='".$datahj."'
             and k48_datafim    >='".$datahj."'
             and k48_instit     = $instit
             and k48_cadtipomod = $tipomod
             and modcarneexcessao.k36_modcarnepadraotipo is null
             ";
    //     die($sqltipo);
    $rsModtipo  = db_query($sqltipo);
    $intnumtipo = pg_numrows($rsModtipo);
    if(isset($intnumtipo) && $intnumtipo > 0){
      //        db_msgbox("achou tipo");
      db_fieldsmemory($rsModtipo,0);
      $achou = 1;
    }
  }
  if($achou == 0){
    $sqlgeral = " select * from cadmodcarne
                     inner join modcarnepadrao         on cadmodcarne.k47_sequencial                = modcarnepadrao.k48_cadmodcarne
                     left  join modcarnepadraocobranca on modcarnepadraocobranca.k22_modcarnepadrao = modcarnepadrao.k48_sequencial
                     left  join modcarnepadraotipo     on modcarnepadrao.k48_sequencial             = modcarnepadraotipo.k49_modcarnepadrao
                     left  join modcarneexcessao       on modcarneexcessao.k36_modcarnepadraotipo   = modcarnepadraotipo.k49_sequencial
            where k48_dataini    <= '".$datahj."'
              and k48_datafim    >= '".$datahj."'
              and k48_instit     = $instit
              and k48_cadtipomod = $tipomod
              and modcarnepadraotipo.k49_modcarnepadrao   is null
              and modcarneexcessao.k36_modcarnepadraotipo is null
            ";
    //    die($sqlgeral);
    $rsModgeral  = db_query($sqlgeral);
    $intnumgeral = pg_numrows($rsModgeral);
    if($intnumgeral > 0){
      //        db_msgbox("achou padrao");
      db_fieldsmemory($rsModgeral,0);
      $achou = 1;
    }else{
      db_redireciona('db_erros.php?fechar=true&db_erro=Modelo de carne n�o encontrado, contate o suporte !');
    }
  }

  //die($k47_sequencial."--".$k47_altura."--".$k47_largura."-".$k47_orientacao."-".$k22_cadban);

  unset($spdf);
  unset($pdf);

  if (isset($k47_altura) && $k47_altura != 0 && isset($k47_largura) && $k47_largura != 0 && isset($k47_orientacao) && $k47_orientacao != ""){
    $medidas = array ($k47_altura,$k47_largura);
    $spdf    = new scpdf($k47_orientacao, "mm",$medidas);
  }else{
    $spdf    = new scpdf();
  }
  $spdf->Open();
  $pdf    = new db_impcarne($spdf,$k47_sequencial);
  return $pdf;
}


function db_tracelog($descricao, $sql, $lErro){

  if(db_getsession("DB_traceLog", false) != null || db_getsession("DB_traceLogAcount", false) != null){
    $data = date("[d/m/Y  H:i:s]");
    $sql = str_replace("\n","",$sql);
    if ($lErro) {
      $sMensagem = "{$data} ERRO : ".addslashes($sql)." \n ";
    } else {
      $sMensagem = "{$data} INFO : ".addslashes($sql)." \n ";
    }


     $lBloquearMensagem = false;
     if (db_getsession("DB_traceLogAcount", false)) {

       $aWordsBlock = array("db_acount",
                            "db_syscampo",
                            "db_sysarquivo",
                            "db_sysarqcamp",
                            "db_usuariosonline",
                            "db_itensmenu",
                            "db_logsacessa");

       foreach($aWordsBlock as $sWord) {

         $mAchouString = strpos($sMensagem, $sWord);
         if ($mAchouString) {
           $lBloquearMensagem = true;
         }
       }
     }

     if (!$lBloquearMensagem) {
       db_tracelogfile($sMensagem);
     }
  }
}

function db_tracelogfile($sStr){

  $sFilename = "/tmp/TRACELOG_FILE_".db_getsession('DB_login')."_".date('Ymd',db_getsession('DB_datausu')).".log";
  $rsFile    = fopen($sFilename,'a');
  fputs($rsFile,$sStr);
  fclose($rsFile);

}

function db_tracelogsaida($tipo, $descricao, $sql) {
  switch($tipo) {
    case "file": break;
    case "db": break;
    default: break;
  }
}

function db_preparageratxt($lista, $k00_tipo =null) {

  global $k03_numpre, $fc_numbco, $aNumpres;
                $result = db_query("select nextval('numpref_k03_numpre_seq') as k03_numpre");
                db_fieldsmemory($result,0);

                $aNumpres = array();

                global $k00_codbco, $k00_codage, $k00_descr, $k00_hist1, $k00_hist2, $k00_hist3, $k00_hist4, $k00_hist5, $k00_hist6, $k00_hist7, $k00_hist8, $k03_tipo, $k00_tipoagrup;

                if ($k00_tipo == null) {

                  $resultnumbco = db_query("select distinct k00_codbco,k00_codage,k00_descr,k00_hist1,k00_hist2,k00_hist3,k00_hist4,k00_hist5,k00_hist6,k00_hist7,k00_hist8,k03_tipo,k00_tipoagrup from arretipo
                        inner join listatipos on k62_tipodeb = k00_tipo where k62_lista = $lista");

                  if(pg_numrows($resultnumbco)==0){
                    echo "O c�digo do banco n�o esta cadastrado no arquivo arretipo para este tipo!";
                    exit;
                  }
                } else {

                  $sqlnumbco = "select distinct k00_codbco,k00_codage,k00_descr,k00_hist1,k00_hist2,k00_hist3,k00_hist4,k00_hist5,k00_hist6,k00_hist7,k00_hist8,k03_tipo,k00_tipoagrup
                                                                                from arretipo
                                                                                where k00_tipo = $k00_tipo";
                  $resultnumbco = db_query($sqlnumbco) or die($sqlnumbco);

                  if(pg_numrows($resultnumbco)==0){
                    echo "O c�digo do banco n�o esta cadastrado no arquivo arretipo para este tipo!";
                    exit;
                  }
                }
                db_fieldsmemory($resultnumbco, 0);

                $resultfc = db_query("select fc_numbco($k00_codbco,'$k00_codage')") or die("erro ao executar fc_numbco");
                db_fieldsmemory($resultfc, 0);

}

function db_separainstrucao($texto, $comeca=0, $layout, $linha, $separador, $maximo=0, $quantidadegeral) {
  global $quantidadegeral;

  $texto = db_geratexto($texto);

  $textos = split("\|", $texto);

  //        for ($xxx=0; $xxx < sizeof($textos); $xxx++) {
  //                echo "$xxx: " . $textos[$xxx] . "<br>";
  //        }

  if ($maximo == 0) {
    $maximo = sizeof($textos);
  }
  if (trim($texto) != "") {
    $totalprocessado=0;
    if ($separador == "05") {
      $mostrar=0;
    } else {
      $mostrar=0;
    }

    ////                echo "$texto<br>size: " . sizeof($textos) . "<br>";
    ////                flush();

    for ($contasepara=0; $contasepara < sizeof($textos); $quantsepara) {
      global $instrucao1, $instrucao2, $instrucao3, $instrucao4;
      $instrucao1="";
      $instrucao2="";
      $instrucao3="";
      $instrucao4="";

      if ($totalprocessado+$maximo > sizeof($textos)) {
        if ($mostrar == "1") {
          echo "111<br>";
        }
        $processar=sizeof($textos)-$totalprocessado;
      } else {
        if ($mostrar == "1") {
          echo "222<br>";
        }
        $processar = $maximo;
      }
      if ($mostrar == "1") {
        echo("contasepara: $contasepara / separador: $separador / processar: $processar - tot: $totalprocessado / maximo: $maximo - size: " . sizeof($textos) . " <br>");
      }

      for ($quantsepara=0+$totalprocessado; $quantsepara < $processar+$totalprocessado; $quantsepara++) {
        $nomevar = "instrucao".($quantsepara+$comeca+1-$totalprocessado);
        global $$nomevar;
        $$nomevar = trim($textos[$quantsepara]);
        $contasepara++;
        if ($mostrar == "1") {
          echo "quantsepara: $quantsepara / $nomevar: " . $$nomevar . "<br>";
        }
        if ($separador == "04" and 1==2) {
          echo "maximo: $maximo - contasepara: $contasepara - quantsepara: $quantsepara - processar: $processar - total: $totalprocessado<br>";
          flush();
        }
      }
      $totalprocessado+=$processar;
      $quantidadegeral++;
      db_setaPropriedadesLayoutTxt(&$layout, $linha, $separador);
      if ($separador == "04" and 1==2) {
        echo "pula<br>";
        flush();
      }
      if ($maximo >= sizeof($textos)) {
        break;
      }
    }

    if ($mostrar == "1" and 1==2) {
      exit;
    }
  }
}
function db_formatatexto($linhas, $largura, $texto, $tipo="t") {
  //$linhas = numero de linhas (altura)
  //$largura= largura do texto
  //$texto  = texto a ser formatado
  //$tipo   = tipo ... t para texto e h para html

  if ($tipo=="t"){
    $quebra = "\n";
  }else{
        $quebra = "<br>";
  }

  $linha       = split("\n",$texto);
  $numlinhas   = count($linha);
  $obs         ="";

        if($numlinhas>0){
          $linhatotal=0;
          for($i = 0; $i < $numlinhas; $i++){
            $linhatotal = $linhatotal + 1;
            $linhastam = strlen($linha[$i]);
            if($linhastam>$largura){
              $nlinhas = ($linhastam / $largura);
              $nlinhas1 = (int)$nlinhas;
              $linhatotal = $linhatotal + $nlinhas1;
             // $obs .= $linha[$i]."\n";

            }

            if($linhatotal>=$linhas){
              if($linhastam>$largura){
                $obs .= substr($linha[$i], 1, $largura);
                $obs .= "...";
              }else{
                $obs .= $linha[$i]."...";
              }
              break;
            }else{
              $obs .= $linha[$i]."$quebra";
            }
          }

        }
        return $obs;
}


// Faz os devidos "Escapes" na string a ser utilizada em javascript
function db_jsspecialchars($s) {
  //$s = string a ser tratada
  return preg_replace('/([^ !#$%@()*+,-.\x30-\x5b\x5d-\x7e])/e',
              "'\\x'.(ord('\\1')<16? '0': '').dechex(ord('\\1'))",$s);
}


function monta_menu($item_modulo,$id_modulo,$espacos,$lista, $iUsuario = false){

  global $matriz_item , $matriz_item_seleciona ;

  $iAnoUsu      = db_getsession('DB_anousu', false);
  $iInstituicao = db_getsession('DB_instit', false);

  $sql  = "select id_item_filho,descricao ,funcao                         ";
  $sql .= "  from db_menu m                                               ";
  $sql .= "       inner join db_itensmenu i on i.id_item = id_item_filho  ";
  $sql .= "  where m.id_item = $item_modulo                               ";
  $sql .= "    and m.modulo  = $id_modulo                                 ";
  $sql .= "  order by menusequencia                                       ";
  
  if ($iUsuario and ($iUsuario != 1 || db_getsession("DB_administrador") != 1)) {

    $sql  = "  select id_item_filho,descricao ,funcao                                                  ";
    $sql .= "    from db_menu m                                                                        ";
    $sql .= "         inner join db_permissao p on p.id_item = m.id_item_filho                         ";
    $sql .= "         inner join db_itensmenu i on i.id_item = m.id_item_filho                         ";
    $sql .= "                                  and p.permissaoativa = '1'                              ";
    $sql .= "                                  and p.anousu = $iAnoUsu                                 ";
    $sql .= "                                  and p.id_instit =$iInstituicao                          ";
    $sql .= "                                  and p.id_modulo = $id_modulo                            ";
    $sql .= "   where p.id_usuario = $iUsuario                                                         ";
    $sql .= "      and m.id_item   = $item_modulo                                                      ";
    $sql .= "      and m.modulo    = $id_modulo                                                        ";
    $sql .= "      and i.itemativo = '1'                                                               ";
    $sql .= " union                                                                                    ";
    $sql .= "  select id_item_filho,descricao ,funcao                                                  ";
    $sql .= "    from db_menu m                                                                        ";
    $sql .= "         inner join db_permherda h on h.id_usuario     = $iUsuario                        ";
    $sql .= "         inner join db_usuarios  u on u.id_usuario     = h.id_perfil                      ";
    $sql .= "                                  and u.usuarioativo   = '1'                              ";
    $sql .= "         inner join db_permissao p on p.id_item        = m.id_item_filho                  ";
    $sql .= "         inner join db_itensmenu i on i.id_item        = m.id_item_filho                  ";
    $sql .= "                                  and p.permissaoativa = '1'                              ";
    $sql .= "                                  and p.anousu         = $iAnoUsu                         ";
    $sql .= "                                  and p.id_instit      = $iInstituicao                    ";
    $sql .= "                                  and p.id_modulo      = $id_modulo                       ";
    $sql .= "  where p.id_usuario = h.id_perfil                                                        ";
    $sql .= "    and m.id_item    = $item_modulo                                                       ";
    $sql .= "    and m.modulo     = $id_modulo                                                         ";
    $sql .= "    and i.itemativo = '1'                                                                 ";
  }

  $res = db_query($sql);
  if(pg_numrows($res)>0){

    for($i=0;$i<pg_numrows($res);$i++){
      $item_filho= pg_result($res,$i,0);
      $descricao = pg_result($res,$i,1);
      $funcao    = trim(pg_result($res,$i,2));
      if( empty($lista) || isset( $lista[$item_filho]) ){
        $matriz_item_seleciona[count($matriz_item_seleciona)] = $espacos."-".$item_filho;
      }
      $matriz_item[count($matriz_item)] = $espacos;
      if($funcao == ""){
        monta_menu($item_filho,$id_modulo,$espacos."-".$item_filho,$lista, $iUsuario);
      }
    }

  }
}

function db_strtotime($strData){

  if(empty($strData)) {
    return $strData;
  }

  if (substr(phpversion(),0,1) == 4) {
    return strtotime($strData,date('h:i'));
  } else if (substr(phpversion(),0,1) >= 5) {
    return(strtotime($strData));
  }
}


function db_getnomelogo(){

        $rsLogo = db_query("select logo
                            from db_config
                                 left join db_tipoinstit on db21_codtipo = db21_tipoinstit
                                                         where codigo = ".db_getsession("DB_instit"));
  if($rsLogo == false || pg_num_rows($rsLogo) == 0 ){
    return false;
  }else{
    return pg_result($rsLogo,0,"logo");
        }

}


function db_dataextenso( $timestamp=null, $sMunic=null ){

  $aMeses = array("01" => "Janeiro",
                  "02" => "Fevereiro",
                  "03" => "Mar�o",
                  "04" => "Abril",
                  "05" => "Maio",
                  "06" => "Junho",
                  "07" => "Julho",
                  "08" => "Agosto",
                  "09" => "Setembro",
                  "10" => "Outubro",
                  "11" => "Novembro",
                  "12" => "Dezembro" );

  if ( $timestamp == null ) {
    $timestamp = db_getsession('DB_datausu');
  }

  if ( $sMunic == null and  $sMunic <> "" ) {
    $sSqlMunic = "select munic from db_config where codigo = ".db_getsession('DB_instit')." limit 1";
    $sMunic    = pg_result( db_query( $sSqlMunic ),0,'munic' );
  }

  $sData = ($sMunic == ""?"":ucfirst(strtolower( $sMunic ) ).", ").date('d',$timestamp)." de ".$aMeses[date('m',$timestamp)]." de ".date('Y',$timestamp).".";
  return $sData;

}

function db_geraArquivoOid ($arquivo,$arquivoAlt=null,$opcao=1,$conn){
  /*
   * $arquivo    => o arquivo do type "file", o arquivo a ser gravado
   * $arquivoAlt => o arquivo ja existente no banco,
   *                a op��o alterar a fun��o altera do $arquivoAlt para o $arquivo
   *                na op��o excluir a fun��o exclui este arquivo
   * $opcao      => 1 = incluir, 2= alterar, 3 = excluir
   * $conn       => conex�o com banco
   */

  if($opcao==2){
    pg_lo_unlink($conn, $arquivoAlt);
  }
  if($opcao==3){
    pg_lo_unlink($conn, $arquivoAlt);
  return "null";
  }

  $nomeArquivo        = $_FILES["$arquivo"]["name"];
  $localRecebeArquivo = $_FILES["$arquivo"]["tmp_name"];

  if ( trim($localRecebeArquivo) != "") {
      $arquivoGrava = fopen($localRecebeArquivo, "rb");
      if ($arquivoGrava == false) {
        throw new Exception("Erro arquivo a gravar ");
      	//echo "erro aruivograva";
        //exit;
      }
      $dados = fread($arquivoGrava, filesize($localRecebeArquivo));
      if ($dados == false) {
      	throw new Exception("Erro fread ");
        //echo "erro fread";
        //exit;
      }
      fclose($arquivoGrava);
      $oidgrava = pg_lo_create();
      if ($oidgrava == false) {
      	throw new Exception("Erro pg_lo_create ");
        //echo "erro pg_lo_create";
        //exit;
      }


      $objeto = pg_lo_open($conn, $oidgrava, "w");
      if ($objeto != false) {
        $erro = pg_lo_write($objeto, $dados);
        if ($erro == false) {
        	throw new Exception("Erro pg_lo_write ");
          //echo "erro pg_lo_write";
          //exit;
        }
        pg_lo_close($objeto);
      } else {
      	throw new Exception("Opera��o Cancelada! ");
        //$erro_msg ("Opera��o Cancelada!!");
        //$sqlerro = true;
      }

      return $oidgrava;
    }

}

function db_buscaImagemBanco($cadban,$conn){
/*
 * $cadban = codigo k15_codigo da cadban
 * $conn   =  conex�o
 */

  $sqlcodban = "select k15_codbco from cadban where k15_codigo = $cadban";
  $resultcadban = db_query($sqlcodban);
  $linhascadban = pg_num_rows($resultcadban);
  if($linhascadban >0){
    //db_fieldsmemory($resultcadban,0);
  $k15_codbco = pg_result($resultcadban,0,"k15_codbco");
  $banco = str_pad($k15_codbco, 3, "0", STR_PAD_LEFT);
  // busca os dados do banco..logo etc
  $sqlBanco = "select  * from db_bancos where db90_codban = '".$banco."'";
  $resultBanco = db_query($sqlBanco);
  $linhasBanco = pg_num_rows($resultBanco);
  if($linhasBanco > 0 ){
    //db_fieldsmemory($resultBanco,0);
    $db90_digban = pg_result($resultBanco,0,"db90_digban");
    $db90_abrev  = pg_result($resultBanco,0,"db90_abrev");
    $db90_logo   = pg_result($resultBanco,0,"db90_logo");
    // se n�o tiver os dados do banco na db_bancos n�o deve emitir o recibo.
    if($db90_digban=="" || $db90_abrev=="" || $db90_logo==""){
      return false;
//      db_redireciona('db_erros.php?fechar=true&db_erro=Configure os dados(Digito verificador, Nome abreviado do banco e o Arquivo do logo) do Banco: '.$banco.'-'.$db90_descr.', no Cadastro de Bancos');
    }
    // seta os dados para o boleto passando as informa��es do logo
    db_query ($conn, "begin");
    $caminho = "tmp/".$banco.".jpg";
      pg_lo_export  ( "$db90_logo",$caminho ,$conn);
    db_query ($conn, "commit");

    $arr = array("numbanco"  =>$banco."-".$db90_digban,
                 "banco"     =>$db90_abrev ,
           "imagemlogo"=>$caminho);

    return $arr;

  }else{
    // se n�o tiver o banco na db_bancos
    db_redireciona('db_erros.php?fechar=true&db_erro=N�o existe Banco cadastrado para o c�digo'.$banco.' no Cadastro de Bancos'.$sqlBanco);
  }
  }
}

function verifica_bissexto($ano){

  if ($ano%4 == 0) {
    if ($ano%100 != 0) {
      return true;
    } else {
      if ($ano%400 == 0) {
        return true;
      } else {
        return false;
      }
    }
  }
  return false;
}


function verifica_ultimo_dia_mes($data){

  $iDia = substr($data,0,2);
  $iMes = substr($data,3,2);
  $iAno = substr($data,6,4);

  $lBisexto = verifica_bissexto($data);

  if ($lBisexto) {
   $iFev = 29;
  } else {
   $iFev = 28;
  }

  $aUltimoDia = array("01"=>"31",
                      "02"=>$iFev,
                      "03"=>"31",
                      "04"=>"30",
                      "05"=>"31",
                      "06"=>"30",
                      "07"=>"31",
                      "08"=>"31",
                      "09"=>"30",
                      "10"=>"31",
                      "11"=>"30",
                      "12"=>"31");
  if ($aUltimoDia[$iMes] == $iDia) {
    return true;
  } else {
    return false;
  }

}
/**
 * retorna o numero de meses entre o intervalo dado
 *
 * @param string $dataini
 * @param string  $datafim
 * @return integer
 */
function conta_meses($dataini, $datafim) {

  $res_meses = db_query("select fc_conta_meses('$dataini','$datafim') as totalmeses");
  $oMeses    = db_utils::fieldsMemory($res_meses, 0);
  return $oMeses->totalmeses;

}
/**
 * Funcao para compactar arquivos apartir de um array com os nomes dos arquivos
 *
 * @param array()      $aArquivosCompactar   array com o nome dos arquivos a serem compactados
 * @param string       $sNomeAbsoluto        nome do arquivo a ser gerado
 * @param string       $sTipoCompactacao     string identificando o tipo de compacta��o ('zip') implementado apenas zip
 */
function compactaArquivos($aArquivosCompactar=array(),$sNomeAbsoluto="",$sTipoCompactacao="zip") {

  switch ($sTipoCompactacao) {
    case 'zip':
      $sArquivos = "";
      foreach($aArquivosCompactar as $sArquivo) {
        $sArquivos .= " $sArquivo";
      }
      system("rm -f tmp/{$sNomeAbsoluto}.zip");
      system("cd tmp/; ../bin/zip -q {$sNomeAbsoluto}.zip $sArquivos 2> /tmp/erro.txt ; cd ..");
      break;
    case 'tar':
      return false;
      break;
  }

}

// Funcao ROUND personalizada
function db_round($valor, $decimais=0) {

  if((int)$valor == $valor) {
    return $valor;
  }

  return round($valor, $decimais);
}

function db_buscaImagemInstituicao($instit,$tipo){
  /**
   * $instit c�digo da institui��o {db_getsession("DB_instit")}
   * $tipo 1 - para logo 2 - para figura
   * $conn conexao com o banco
   */
	global $conn;
	$sSqlConfigArquivos  = "select db38_arquivo ";
	$sSqlConfigArquivos .= "  from db_configarquivos ";
	$sSqlConfigArquivos .= " where db38_instit = $instit ";
	$sSqlConfigArquivos .= "   and db38_tipo   = $tipo ";
	$rsSqlConfigArquivos = db_query($sSqlConfigArquivos);
	$iNumRows            = pg_numrows($rsSqlConfigArquivos);
	if ($iNumRows > 0) {

		$arquivo = pg_result($rsSqlConfigArquivos,0,"db38_arquivo");
	  $caminho = "tmp/".$arquivo.".jpg";
		db_query($conn,"begin");
		pg_lo_export($conn,$arquivo,$caminho);
		db_query($conn,"commit");
		return $caminho;
	}else{
		return null;
	}
}

/**
 * Remove��o acentu��o da string informada como par�metro .
 *
 * @param string $sRemover
 * @return string
 */

function db_removeAcentuacao($sRemover){

    $var = $sRemover;

    $var = ereg_replace("[����]","A",$var);

    $var = ereg_replace("[����]","a",$var);

    $var = ereg_replace("[���]","E",$var);

    $var = ereg_replace("[���]","e",$var);

    $var = ereg_replace("[����]","O",$var);

    $var = ereg_replace("[�����]","o",$var);

    $var = ereg_replace("[���]","U",$var);

    $var = ereg_replace("[���]","u",$var);

    $var = str_replace("�","C",$var);

    $var = str_replace("�","c",$var);

    return $var;

  }

/**
 * @name fun��o db_tempodecorrido
 * @desc retorna o tempo gasto formatado entre duas datas
 * @param timestamp do primeiro tempo
 * @param timestamp do segundo tempo
 */
function db_formatatempodecorrido($timestampAntes, $timestampDepois){

  //string de retorno
  $sRetorno = '';

  //diferen�a entre as datas em segundos
  $iRestaSegundos = $timestampDepois-$timestampAntes;

  //quantidade de anos
  $anos = $iRestaSegundos/31536000;

  //se houver anos
  $iAnos = floor($anos);
  if ($iAnos >= 1) {

    //mostra quantos anos passaram
    $sRetorno .= ($iAnos == 1) ? $iAnos.' ano, ' : $iAnos.' anos, ';

    //retira do total, o tempo em segundos dos anos passados
    $iRestaSegundos = $iRestaSegundos-($iAnos*31536000);
  }

  //quantidade de meses (anos/12 e n�o dias*30)
  $ises = $iRestaSegundos/2628000;

  //se houver meses
  $iMeses = floor($ises);
  if ($iMeses >= 1) {

    //mostra quantos meses passaram
    $sRetorno .= ($iMeses == 1) ? $iMeses.' m�s, ' : $iMeses.' meses, ';

    //retira do total, o tempo em segundos dos meses passados
    $iRestaSegundos = $iRestaSegundos-($iMeses*2628000);
  }

  //quantidade de semanas
  $semanas = $iRestaSegundos/604800;

  //se houver semanas
  $iSemanas = floor($semanas);
  if ($iSemanas >= 1) {

    //mostra quantas semanas passaram
    $sRetorno .= ($iSemanas == 1) ? $iSemanas.' semana, ' : $iSemanas.' semanas, ';

    //retira do total, o tempo em segundos das semanas passados
    $iRestaSegundos = $iRestaSegundos-($iSemanas*604800);
  }

  //quantidade de dias
  $dias = $iRestaSegundos/86400;

  //se houver dias
  $iDias = floor($dias);
  if ($iDias >= 1) {

    //mostra quantos dias passaram
    $sRetorno .= ($iDias == 1) ? $iDias.' dia, ' : $iDias.' dias, ';

    //retira do total, o tempo em segundos dos dias passados
    $iRestaSegundos = $iRestaSegundos-($iDias*86400);
  }

  //quantidade de horas
  $horas = $iRestaSegundos/3600;

  //se houver horas
  $iHoras = floor($horas);
  if ($iHoras >= 1) {

    //mostra quantas horas passaram
    $sRetorno .= ($iHoras == 1) ? $iHoras.' hora, ' : $iHoras.' horas, ';

    //retira do total, o tempo em segundos das horas passados
    $iRestaSegundos = $iRestaSegundos-($iHoras*3600);
  }

  //quantidade de minutos
  $minutos = $iRestaSegundos/60;

  //se houver minutos
  $iMinutos = floor($minutos);
  if ($iMinutos >= 1) {

    //mostra quantos minutos passaram
    $sRetorno .= ($iMinutos == 1) ? $iMinutos.' minuto, ' : $iMinutos.' minutos, ';

    //retira do total, o tempo em segundos dos minutos passados
    $iRestaSegundos = $iRestaSegundos-($iMinutos*60);
  }

  //mostra quantos minutos passaram
  if($iRestaSegundos >= 0) {
    $sRetorno .= (ceil($iRestaSegundos) == 1) ? ceil($iRestaSegundos).' segundo, ' : ceil($iRestaSegundos).' segundos, ';
  }

  //retira a ultima virgula
  $sRetorno = rtrim($sRetorno, ', ');

  //coloca "e" no lugar da ultima virgula
  $arrExplode = explode(',', $sRetorno);
  $sRetornoFinal = '';
  $nPedacos = count($arrExplode);
  for ($i=0; $i<$nPedacos; $i++) {
    if ($i == ($nPedacos-1)) {
      $sRetornoFinal .= ($i <> 0) ? ' e '.$arrExplode[$i] : $arrExplode[$i];

    } else if ($i == ($nPedacos-2)) {
      $sRetornoFinal .= $arrExplode[$i];
    } else {
      $sRetornoFinal .= $arrExplode[$i].',';
    }
  }

  //retorna o tempo decorrido formatado
  return $sRetornoFinal;
}

/*
 * retorna o mes abreviado conforme numero do mes recebido por parametro no formato ex: '01' = JAN ...
 */
 function db_mesAbreviado ($iMes){
   $aMesAbreviado = array( "01" => "Jan",
                           "02" => "Fev",
                           "03" => "Mar",
                           "04" => "Abr",
                           "05" => "Mai",
                           "06" => "Jun",
                           "07" => "Jul",
                           "08" => "Ago",
                           "09" => "Set",
                           "10" => "Out",
                           "11" => "Nov",
                           "12" => "Dez");
   return $aMesAbreviado[$iMes];
 }

 /**
  * Fun��o que analisa se o plano de contas PCASP esta ativado e ent�o altera o nome das tabelas envolvido
  * @param string $sQuery
  * @throws Exception
  */
 function analiseQueryPlanoOrcamento($sQuery, $iAnoUsu = null)  {

 	if (empty($iAnoUsu)) {
 		$iAnoUsu = db_getsession("DB_anousu");
 	}

  $aTablesFrom = array(" conplano ",
                       "conplano.",
                       "conplanoreduz",
                       "conplanoconta",
                       "conplanocontabancaria",
                       "orccenarioeconomicoconplano ",
  		                 "fc_conplano_grupo"
                      );

  $aTablesTo  = array(" conplanoorcamento ",
                      "conplanoorcamento.",
                      "conplanoorcamentoanalitica",
                      "conplanoorcamentoconta",
                      "conplanoorcamentocontabancaria",
                      "orccenarioeconomicoconplanoorcamento ",
  		                "fc_conplanoorcamento_grupo"
                     );
  /**
   * incluido um or no if da constante use pecasp, para verificar tambem a variavel de se��o
   * para fazer o parse das tabelas migradas
   * ajuste provisorio para que seja possivel a visualiza��o dos relatorios e quadros de
   * estimativas de receitas para o ano de 2012.
   * ativamos para 't' no RPC que chama o metodo getQuadroEstimativa
   * @todo remover || $_SESSION["DB_use_pcasp"] == 't'  do if abaixo
   */
  if ( $_SESSION["DB_use_pcasp"] == "t" || (USE_PCASP && $iAnoUsu > 2012) ) {
    $sQuery = str_replace($aTablesFrom, $aTablesTo, $sQuery);
  }

  return $sQuery;
}

/**
 *  funcao para corrigir o erro do php 2, relacionado a arredondamento
 *  mais informa��es sobre o bug: https://bugs.php.net/bug.php?id=44223
 */
function dbround_php_52($nValor, $iCasas = 0) {

  return round($nValor, $iCasas);

  // return sprintf("%.{$iCasas}f", round($nValor, $iCasas));
}
/**
 * Fun��o que valida o uso do PCASP e direciona o usu�rio caso precise
 * @param  integer $iCodigoMenu
 * @return boolean
 */
function db_validarMenuPCASP($iCodigoMenu = null) {

  if (USE_PCASP && !empty($iCodigoMenu)) {

    $aCodigosMenusBloqueados = array(3973,  // Caixa > Procedimentos > Planilha de Lan�amento > Inclus�o
                                     3974,  // Caixa > Procedimentos > Planilha de Lan�amento > Altera��o,
                                     3975,  // Caixa > Procedimentos > Planilha de Lan�amento > Exclus�o
                                     3985,  // Caixa > Procedimentos > Planilha de Lan�amento > Autentica Planilha
                                     4541,  // Caixa > Procedimentos > Planilha de Lan�amento > Estorna Planilha
                                     94,    // Caixa > Procedimentos > Slip > Inclus�o de Transfer�ncia
                                     95,    // Caixa > Procedimentos > Slip > Altera��o de Transfer�ncia
                                     110,   // Caixa > Procedimentos > Slip > Autentica
                                     7652,  // Caixa > Procedimentos > Slip > Anula
                                     3364,  // Contabilidade > Cadastros > Plano de Contas > Inclus�o
                                     3365,  // Contabilidade > Cadastros > Plano de Contas > Altera��o
                                     3366,  // Contabilidade > Cadastros > Plano de Contas > Exclus�o
                                     331366,// Contabilidade > Cadastros > Plano de Contas > Alterar Estrutural
                                     3743,  // Contabilidade > Cadastros > Cadastro de Transa��es > Inclus�o
                                     3744,  // Contabilidade > Cadastros > Cadastro de Transa��es > Altera��o
                                     3745); // Contabilidade > Cadastros > Cadastro de Transa��es > Exclus�o

    if ( in_array($iCodigoMenu, $aCodigosMenusBloqueados) ) {

      $sMensagem  = "Esta rotina encontra-se desabilitada para institui��es que utilizam o PCASP.\\n\\n";
      $sMensagem .= "Para mais informa��es, contate o suporte.";
      db_msgbox($sMensagem);
      db_redireciona("corpo.php");
      return false;
    }
  }
  return true;
}


/**
 * Retorna a mensagem solicitada
 * @param string sCaminhoMensagem caminho de mensagem
 * @param stdclass oVariaveis objeto literal com as variaveis que devem ser substituidas
 * @example _M('configuracao.mensagem.con4_mensagem001.mensagem_nao_informada');
 *          Aonde: DBPortal. <-area
 *                 configuracao <- modulo
 *                 con4_mensagem001<- Programa
 *                 mensagem_nao_informada <- mensagem que deve ser exibida
 * @returns {string} texto da mensagem
 */
function _M($sCaminhoMensagem, $oOpcoes = null) {
  require_once 'model/configuracao/mensagem/DBMensagem.model.php';
  return DBMensagem::getMensagem($sCaminhoMensagem, $oOpcoes);
}
?>