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

//MODULO: ambulatorial
//CLASSE DA ENTIDADE sau_cadsus
class cl_sau_cadsus { 
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
   var $s136_i_codigo = 0; 
   var $s136_d_data_dia = null; 
   var $s136_d_data_mes = null; 
   var $s136_d_data_ano = null; 
   var $s136_d_data = null; 
   var $s136_c_hora = null; 
   var $s136_i_user = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 s136_i_codigo = int4 = c�digo 
                 s136_d_data = date = Data 
                 s136_c_hora = char(5) = Hora 
                 s136_i_user = int4 = Usuario 
                 ";
   //funcao construtor da classe 
   function cl_sau_cadsus() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("sau_cadsus"); 
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
       $this->s136_i_codigo = ($this->s136_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["s136_i_codigo"]:$this->s136_i_codigo);
       if($this->s136_d_data == ""){
         $this->s136_d_data_dia = ($this->s136_d_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["s136_d_data_dia"]:$this->s136_d_data_dia);
         $this->s136_d_data_mes = ($this->s136_d_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["s136_d_data_mes"]:$this->s136_d_data_mes);
         $this->s136_d_data_ano = ($this->s136_d_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["s136_d_data_ano"]:$this->s136_d_data_ano);
         if($this->s136_d_data_dia != ""){
            $this->s136_d_data = $this->s136_d_data_ano."-".$this->s136_d_data_mes."-".$this->s136_d_data_dia;
         }
       }
       $this->s136_c_hora = ($this->s136_c_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["s136_c_hora"]:$this->s136_c_hora);
       $this->s136_i_user = ($this->s136_i_user == ""?@$GLOBALS["HTTP_POST_VARS"]["s136_i_user"]:$this->s136_i_user);
     }else{
       $this->s136_i_codigo = ($this->s136_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["s136_i_codigo"]:$this->s136_i_codigo);
     }
   }
   // funcao para inclusao
   function incluir ($s136_i_codigo){ 
      $this->atualizacampos();
     if($this->s136_d_data == null ){ 
       $this->erro_sql = " Campo Data nao Informado.";
       $this->erro_campo = "s136_d_data_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->s136_c_hora == null ){ 
       $this->erro_sql = " Campo Hora nao Informado.";
       $this->erro_campo = "s136_c_hora";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->s136_i_user == null ){ 
       $this->erro_sql = " Campo Usuario nao Informado.";
       $this->erro_campo = "s136_i_user";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($s136_i_codigo == "" || $s136_i_codigo == null ){
       $result = db_query("select nextval('sau_cadsus_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: sau_cadsus_seq do campo: s136_i_codigo"; 
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->s136_i_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from sau_cadsus_seq");
       if(($result != false) && (pg_result($result,0,0) < $s136_i_codigo)){
         $this->erro_sql = " Campo s136_i_codigo maior que �ltimo n�mero da sequencia.";
         $this->erro_banco = "Sequencia menor que este n�mero.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->s136_i_codigo = $s136_i_codigo; 
       }
     }
     if(($this->s136_i_codigo == null) || ($this->s136_i_codigo == "") ){ 
       $this->erro_sql = " Campo s136_i_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into sau_cadsus(
                                       s136_i_codigo 
                                      ,s136_d_data 
                                      ,s136_c_hora 
                                      ,s136_i_user 
                       )
                values (
                                $this->s136_i_codigo 
                               ,".($this->s136_d_data == "null" || $this->s136_d_data == ""?"null":"'".$this->s136_d_data."'")." 
                               ,'$this->s136_c_hora' 
                               ,$this->s136_i_user 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "sau_cadsus ($this->s136_i_codigo) nao Inclu�do. Inclusao Abortada.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "sau_cadsus j� Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "sau_cadsus ($this->s136_i_codigo) nao Inclu�do. Inclusao Abortada.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->s136_i_codigo;
     $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->s136_i_codigo));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,14694,'$this->s136_i_codigo','I')");
       $resac = db_query("insert into db_acount values($acount,2582,14694,'','".AddSlashes(pg_result($resaco,0,'s136_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2582,14695,'','".AddSlashes(pg_result($resaco,0,'s136_d_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2582,14696,'','".AddSlashes(pg_result($resaco,0,'s136_c_hora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2582,14697,'','".AddSlashes(pg_result($resaco,0,'s136_i_user'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($s136_i_codigo=null) { 
      $this->atualizacampos();
     $sql = " update sau_cadsus set ";
     $virgula = "";
     if(trim($this->s136_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s136_i_codigo"])){ 
       $sql  .= $virgula." s136_i_codigo = $this->s136_i_codigo ";
       $virgula = ",";
       if(trim($this->s136_i_codigo) == null ){ 
         $this->erro_sql = " Campo c�digo nao Informado.";
         $this->erro_campo = "s136_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->s136_d_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s136_d_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["s136_d_data_dia"] !="") ){ 
       $sql  .= $virgula." s136_d_data = '$this->s136_d_data' ";
       $virgula = ",";
       if(trim($this->s136_d_data) == null ){ 
         $this->erro_sql = " Campo Data nao Informado.";
         $this->erro_campo = "s136_d_data_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{ 
       if(isset($GLOBALS["HTTP_POST_VARS"]["s136_d_data_dia"])){ 
         $sql  .= $virgula." s136_d_data = null ";
         $virgula = ",";
         if(trim($this->s136_d_data) == null ){ 
           $this->erro_sql = " Campo Data nao Informado.";
           $this->erro_campo = "s136_d_data_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->s136_c_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s136_c_hora"])){ 
       $sql  .= $virgula." s136_c_hora = '$this->s136_c_hora' ";
       $virgula = ",";
       if(trim($this->s136_c_hora) == null ){ 
         $this->erro_sql = " Campo Hora nao Informado.";
         $this->erro_campo = "s136_c_hora";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->s136_i_user)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s136_i_user"])){ 
       $sql  .= $virgula." s136_i_user = $this->s136_i_user ";
       $virgula = ",";
       if(trim($this->s136_i_user) == null ){ 
         $this->erro_sql = " Campo Usuario nao Informado.";
         $this->erro_campo = "s136_i_user";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($s136_i_codigo!=null){
       $sql .= " s136_i_codigo = $this->s136_i_codigo";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->s136_i_codigo));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14694,'$this->s136_i_codigo','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s136_i_codigo"]) || $this->s136_i_codigo != "")
           $resac = db_query("insert into db_acount values($acount,2582,14694,'".AddSlashes(pg_result($resaco,$conresaco,'s136_i_codigo'))."','$this->s136_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s136_d_data"]) || $this->s136_d_data != "")
           $resac = db_query("insert into db_acount values($acount,2582,14695,'".AddSlashes(pg_result($resaco,$conresaco,'s136_d_data'))."','$this->s136_d_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s136_c_hora"]) || $this->s136_c_hora != "")
           $resac = db_query("insert into db_acount values($acount,2582,14696,'".AddSlashes(pg_result($resaco,$conresaco,'s136_c_hora'))."','$this->s136_c_hora',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s136_i_user"]) || $this->s136_i_user != "")
           $resac = db_query("insert into db_acount values($acount,2582,14697,'".AddSlashes(pg_result($resaco,$conresaco,'s136_i_user'))."','$this->s136_i_user',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "sau_cadsus nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->s136_i_codigo;
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "sau_cadsus nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->s136_i_codigo;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Altera��o efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->s136_i_codigo;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($s136_i_codigo=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($s136_i_codigo));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14694,'$s136_i_codigo','E')");
         $resac = db_query("insert into db_acount values($acount,2582,14694,'','".AddSlashes(pg_result($resaco,$iresaco,'s136_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2582,14695,'','".AddSlashes(pg_result($resaco,$iresaco,'s136_d_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2582,14696,'','".AddSlashes(pg_result($resaco,$iresaco,'s136_c_hora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2582,14697,'','".AddSlashes(pg_result($resaco,$iresaco,'s136_i_user'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from sau_cadsus
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($s136_i_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " s136_i_codigo = $s136_i_codigo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "sau_cadsus nao Exclu�do. Exclus�o Abortada.\\n";
       $this->erro_sql .= "Valores : ".$s136_i_codigo;
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "sau_cadsus nao Encontrado. Exclus�o n�o Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$s136_i_codigo;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclus�o efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$s136_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:sau_cadsus";
        $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   function sql_query ( $s136_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from sau_cadsus ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = sau_cadsus.s136_i_user";
     $sql2 = "";
     if($dbwhere==""){
       if($s136_i_codigo!=null ){
         $sql2 .= " where sau_cadsus.s136_i_codigo = $s136_i_codigo "; 
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
   // funcao do sql 
   function sql_query_file ( $s136_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from sau_cadsus ";
     $sql2 = "";
     if($dbwhere==""){
       if($s136_i_codigo!=null ){
         $sql2 .= " where sau_cadsus.s136_i_codigo = $s136_i_codigo "; 
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
   function sql_query_coferenciaimportacaocartaosus($iCodigo = null, $sCampos = '*', $sOrdem = null, $sDbWhere = '') { 

    $sSql = 'select ';
    if ($sCampos != '*') {

      $sCamposSql = split('#', $sCampos);
      $sVirgula   = '';
      for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

        $sSql    .= $sVirgula.$sCamposSql[$iCont];
        $virgula  = ",";

      }

    } else {
      $sSql .= $sCampos;
    }
    $sSql  .= ' from sau_cadsus';
    $sSql  .= '  inner join sau_cadsusreg  on s137_i_import    = s136_i_codigo ';
    $sSql  .= '  inner join sau_cadsustipo on s138_i_codigo    = s137_i_situacao '; 
    $sSql  .= '  inner join cgs_cartaosus  on s115_c_cartaosus = s137_i_cadsus ';
    $sSql  .= '  inner join cgs_und        on z01_i_cgsund     = s115_i_cgs '; 
    $sSql2  = '';
    if ($sDbWhere == '') {

      if ($iCodigo != null ){
        $sSql2 .= " where s136_i_codigo = $iCodigo "; 
      }

    } elseif ($sDbWhere != '') {
      $sSql2 = " where $sDbWhere";
    }
    $sSql .= $sSql2;

    if ($sOrdem != null) {

      $sSql      .= ' order by ';
      $sCamposSql = split('#', $sOrdem);
      $sVirgula   = '';
      for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

        $sSql    .= $sVirgula.$sCamposSql[$iCont];
        $sVirgula = ',';

      }

    }

    return $sSql;

  }
}
?>