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

//MODULO: cadastro
//CLASSE DA ENTIDADE loteloteam
class cl_loteloteam { 
   // cria variaveis de erro 
   var $rotulo     = null; 
   var $query_sql  = null; 
   var $numrows    = 0; 
   var $numrows_incluir = 0; 
   var $numrows_alterar = 0; 
   var $numrows_excluir = 0; 
   var $erro_status= null; 
   var $erro_sql   = null; 
   var $erro_banco = null;  
   var $erro_msg   = null;  
   var $erro_campo = null;  
   var $pagina_retorno = null; 
   // cria variaveis do arquivo 
   var $j34_idbql = 0; 
   var $j34_loteam = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 j34_idbql = int4 = C�d. Lote 
                 j34_loteam = int4 = C�d. Loteamento 
                 ";
   //funcao construtor da classe 
   function cl_loteloteam() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("loteloteam"); 
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }
   //funcao erro 
   function erro($mostra,$retorna) { 
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->j34_idbql = ($this->j34_idbql == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_idbql"]:$this->j34_idbql);
       $this->j34_loteam = ($this->j34_loteam == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_loteam"]:$this->j34_loteam);
     }else{
       $this->j34_idbql = ($this->j34_idbql == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_idbql"]:$this->j34_idbql);
       $this->j34_loteam = ($this->j34_loteam == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_loteam"]:$this->j34_loteam);
     }
   }
   // funcao para inclusao
   function incluir ($j34_idbql,$j34_loteam){ 
      $this->atualizacampos();
       $this->j34_idbql = $j34_idbql; 
       $this->j34_loteam = $j34_loteam; 
     if(($this->j34_idbql == null) || ($this->j34_idbql == "") ){ 
       $this->erro_sql = " Campo j34_idbql nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->j34_loteam == null) || ($this->j34_loteam == "") ){ 
       $this->erro_sql = " Campo j34_loteam nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into loteloteam(
                                       j34_idbql 
                                      ,j34_loteam 
                       )
                values (
                                $this->j34_idbql 
                               ,$this->j34_loteam 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = " ($this->j34_idbql."-".$this->j34_loteam) nao Inclu�do. Inclusao Abortada.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = " j� Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = " ($this->j34_idbql."-".$this->j34_loteam) nao Inclu�do. Inclusao Abortada.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j34_idbql."-".$this->j34_loteam;
     $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->j34_idbql,$this->j34_loteam));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,79,'$this->j34_idbql','I')");
       $resac = db_query("insert into db_acountkey values($acount,86,'$this->j34_loteam','I')");
       $resac = db_query("insert into db_acount values($acount,21,79,'','".AddSlashes(pg_result($resaco,0,'j34_idbql'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,21,86,'','".AddSlashes(pg_result($resaco,0,'j34_loteam'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($j34_idbql=null,$j34_loteam=null) { 
      $this->atualizacampos();
     $sql = " update loteloteam set ";
     $virgula = "";
     if(trim($this->j34_idbql)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_idbql"])){ 
       $sql  .= $virgula." j34_idbql = $this->j34_idbql ";
       $virgula = ",";
       if(trim($this->j34_idbql) == null ){ 
         $this->erro_sql = " Campo C�d. Lote nao Informado.";
         $this->erro_campo = "j34_idbql";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j34_loteam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_loteam"])){ 
        if(trim($this->j34_loteam)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j34_loteam"])){ 
           $this->j34_loteam = "0" ; 
        } 
       $sql  .= $virgula." j34_loteam = $this->j34_loteam ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($j34_idbql!=null){
       $sql .= " j34_idbql = $this->j34_idbql";
     }
     if($j34_loteam!=null){
       $sql .= " and  j34_loteam = $this->j34_loteam";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->j34_idbql,$this->j34_loteam));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,79,'$this->j34_idbql','A')");
         $resac = db_query("insert into db_acountkey values($acount,86,'$this->j34_loteam','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_idbql"]))
           $resac = db_query("insert into db_acount values($acount,21,79,'".AddSlashes(pg_result($resaco,$conresaco,'j34_idbql'))."','$this->j34_idbql',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_loteam"]))
           $resac = db_query("insert into db_acount values($acount,21,86,'".AddSlashes(pg_result($resaco,$conresaco,'j34_loteam'))."','$this->j34_loteam',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->j34_idbql."-".$this->j34_loteam;
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = " nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->j34_idbql."-".$this->j34_loteam;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Altera��o efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j34_idbql."-".$this->j34_loteam;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($j34_idbql=null,$j34_loteam=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($j34_idbql,$j34_loteam));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,79,'$j34_idbql','E')");
         $resac = db_query("insert into db_acountkey values($acount,86,'$j34_loteam','E')");
         $resac = db_query("insert into db_acount values($acount,21,79,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_idbql'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,21,86,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_loteam'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from loteloteam
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($j34_idbql != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " j34_idbql = $j34_idbql ";
        }
        if($j34_loteam != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " j34_loteam = $j34_loteam ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " nao Exclu�do. Exclus�o Abortada.\\n";
       $this->erro_sql .= "Valores : ".$j34_idbql."-".$j34_loteam;
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = " nao Encontrado. Exclus�o n�o Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$j34_idbql."-".$j34_loteam;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclus�o efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$j34_idbql."-".$j34_loteam;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao do recordset 
   function sql_record($sql) { 
     $result = db_query($sql);
     if($result==false){
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_numrows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:loteloteam";
        $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   function sql_query ( $j34_idbql=null,$j34_loteam=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from loteloteam ";
     $sql .= "      inner join lote  on  lote.j34_idbql = loteloteam.j34_idbql";
     $sql .= "      inner join loteam  on  loteam.j34_loteam = loteloteam.j34_loteam";
     $sql .= "      inner join bairro  on  bairro.j13_codi = lote.j34_bairro";
     $sql .= "      inner join setor  on  setor.j30_codi = lote.j34_setor";
     $sql2 = "";
     if($dbwhere==""){
       if($j34_idbql!=null ){
         $sql2 .= " where loteloteam.j34_idbql = $j34_idbql "; 
       } 
       if($j34_loteam!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " loteloteam.j34_loteam = $j34_loteam "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_file ( $j34_idbql=null,$j34_loteam=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from loteloteam ";
     $sql2 = "";
     if($dbwhere==""){
       if($j34_idbql!=null ){
         $sql2 .= " where loteloteam.j34_idbql = $j34_idbql "; 
       } 
       if($j34_loteam!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         } 
         $sql2 .= " loteloteam.j34_loteam = $j34_loteam "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
}
?>