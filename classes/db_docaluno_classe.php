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

//MODULO: educa��o
//CLASSE DA ENTIDADE docaluno
class cl_docaluno { 
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
   var $ed49_i_codigo = 0; 
   var $ed49_i_escola = 0; 
   var $ed49_i_aluno = 0; 
   var $ed49_i_documentacao = 0; 
   var $ed49_t_obs = null; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 ed49_i_codigo = int8 = C�digo 
                 ed49_i_escola = int8 = Escola 
                 ed49_i_aluno = int8 = Aluno 
                 ed49_i_documentacao = int8 = Documento 
                 ed49_t_obs = text = Observa��es 
                 ";
   //funcao construtor da classe 
   function cl_docaluno() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("docaluno"); 
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]."?ed49_i_aluno=".@$GLOBALS["HTTP_POST_VARS"]["ed49_i_aluno"]."&ed47_v_nome=".@$GLOBALS["HTTP_POST_VARS"]["ed47_v_nome"]);
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
       $this->ed49_i_codigo = ($this->ed49_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed49_i_codigo"]:$this->ed49_i_codigo);
       $this->ed49_i_escola = ($this->ed49_i_escola == ""?@$GLOBALS["HTTP_POST_VARS"]["ed49_i_escola"]:$this->ed49_i_escola);
       $this->ed49_i_aluno = ($this->ed49_i_aluno == ""?@$GLOBALS["HTTP_POST_VARS"]["ed49_i_aluno"]:$this->ed49_i_aluno);
       $this->ed49_i_documentacao = ($this->ed49_i_documentacao == ""?@$GLOBALS["HTTP_POST_VARS"]["ed49_i_documentacao"]:$this->ed49_i_documentacao);
       $this->ed49_t_obs = ($this->ed49_t_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["ed49_t_obs"]:$this->ed49_t_obs);
     }else{
       $this->ed49_i_codigo = ($this->ed49_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed49_i_codigo"]:$this->ed49_i_codigo);
     }
   }
   // funcao para inclusao
   function incluir ($ed49_i_codigo){ 
      $this->atualizacampos();
     if($this->ed49_i_escola == null ){ 
       $this->erro_sql = " Campo Escola nao Informado.";
       $this->erro_campo = "ed49_i_escola";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed49_i_aluno == null ){ 
       $this->erro_sql = " Campo Aluno nao Informado.";
       $this->erro_campo = "ed49_i_aluno";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed49_i_documentacao == null ){ 
       $this->erro_sql = " Campo Documento nao Informado.";
       $this->erro_campo = "ed49_i_documentacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ed49_i_codigo == "" || $ed49_i_codigo == null ){
       $result = db_query("select nextval('docaluno_ed49_i_codigo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: docaluno_ed49_i_codigo_seq do campo: ed49_i_codigo"; 
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->ed49_i_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from docaluno_ed49_i_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed49_i_codigo)){
         $this->erro_sql = " Campo ed49_i_codigo maior que �ltimo n�mero da sequencia.";
         $this->erro_banco = "Sequencia menor que este n�mero.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed49_i_codigo = $ed49_i_codigo; 
       }
     }
     if(($this->ed49_i_codigo == null) || ($this->ed49_i_codigo == "") ){ 
       $this->erro_sql = " Campo ed49_i_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into docaluno(
                                       ed49_i_codigo 
                                      ,ed49_i_escola 
                                      ,ed49_i_aluno 
                                      ,ed49_i_documentacao 
                                      ,ed49_t_obs 
                       )
                values (
                                $this->ed49_i_codigo 
                               ,$this->ed49_i_escola 
                               ,$this->ed49_i_aluno 
                               ,$this->ed49_i_documentacao 
                               ,'$this->ed49_t_obs' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Documenta��o Pendente de Alunos ($this->ed49_i_codigo) nao Inclu�do. Inclusao Abortada.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Documenta��o Pendente de Alunos j� Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Documenta��o Pendente de Alunos ($this->ed49_i_codigo) nao Inclu�do. Inclusao Abortada.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->ed49_i_codigo;
     $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->ed49_i_codigo));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,1008308,'$this->ed49_i_codigo','I')");
       $resac = db_query("insert into db_acount values($acount,1010053,1008308,'','".AddSlashes(pg_result($resaco,0,'ed49_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010053,1008653,'','".AddSlashes(pg_result($resaco,0,'ed49_i_escola'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010053,1008309,'','".AddSlashes(pg_result($resaco,0,'ed49_i_aluno'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010053,1008310,'','".AddSlashes(pg_result($resaco,0,'ed49_i_documentacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010053,1008311,'','".AddSlashes(pg_result($resaco,0,'ed49_t_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($ed49_i_codigo=null) { 
      $this->atualizacampos();
     $sql = " update docaluno set ";
     $virgula = "";
     if(trim($this->ed49_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed49_i_codigo"])){ 
       $sql  .= $virgula." ed49_i_codigo = $this->ed49_i_codigo ";
       $virgula = ",";
       if(trim($this->ed49_i_codigo) == null ){ 
         $this->erro_sql = " Campo C�digo nao Informado.";
         $this->erro_campo = "ed49_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed49_i_escola)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed49_i_escola"])){ 
       $sql  .= $virgula." ed49_i_escola = $this->ed49_i_escola ";
       $virgula = ",";
       if(trim($this->ed49_i_escola) == null ){ 
         $this->erro_sql = " Campo Escola nao Informado.";
         $this->erro_campo = "ed49_i_escola";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed49_i_aluno)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed49_i_aluno"])){ 
       $sql  .= $virgula." ed49_i_aluno = $this->ed49_i_aluno ";
       $virgula = ",";
       if(trim($this->ed49_i_aluno) == null ){ 
         $this->erro_sql = " Campo Aluno nao Informado.";
         $this->erro_campo = "ed49_i_aluno";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed49_i_documentacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed49_i_documentacao"])){ 
       $sql  .= $virgula." ed49_i_documentacao = $this->ed49_i_documentacao ";
       $virgula = ",";
       if(trim($this->ed49_i_documentacao) == null ){ 
         $this->erro_sql = " Campo Documento nao Informado.";
         $this->erro_campo = "ed49_i_documentacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed49_t_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed49_t_obs"])){ 
       $sql  .= $virgula." ed49_t_obs = '$this->ed49_t_obs' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($ed49_i_codigo!=null){
       $sql .= " ed49_i_codigo = $this->ed49_i_codigo";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->ed49_i_codigo));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1008308,'$this->ed49_i_codigo','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed49_i_codigo"]))
           $resac = db_query("insert into db_acount values($acount,1010053,1008308,'".AddSlashes(pg_result($resaco,$conresaco,'ed49_i_codigo'))."','$this->ed49_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed49_i_escola"]))
           $resac = db_query("insert into db_acount values($acount,1010053,1008653,'".AddSlashes(pg_result($resaco,$conresaco,'ed49_i_escola'))."','$this->ed49_i_escola',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed49_i_aluno"]))
           $resac = db_query("insert into db_acount values($acount,1010053,1008309,'".AddSlashes(pg_result($resaco,$conresaco,'ed49_i_aluno'))."','$this->ed49_i_aluno',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed49_i_documentacao"]))
           $resac = db_query("insert into db_acount values($acount,1010053,1008310,'".AddSlashes(pg_result($resaco,$conresaco,'ed49_i_documentacao'))."','$this->ed49_i_documentacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed49_t_obs"]))
           $resac = db_query("insert into db_acount values($acount,1010053,1008311,'".AddSlashes(pg_result($resaco,$conresaco,'ed49_t_obs'))."','$this->ed49_t_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Documenta��o Pendente de Alunos nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed49_i_codigo;
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Documenta��o Pendente de Alunos nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed49_i_codigo;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Altera��o efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->ed49_i_codigo;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($ed49_i_codigo=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($ed49_i_codigo));
     }else{ 
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1008308,'$ed49_i_codigo','E')");
         $resac = db_query("insert into db_acount values($acount,1010053,1008308,'','".AddSlashes(pg_result($resaco,$iresaco,'ed49_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010053,1008653,'','".AddSlashes(pg_result($resaco,$iresaco,'ed49_i_escola'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010053,1008309,'','".AddSlashes(pg_result($resaco,$iresaco,'ed49_i_aluno'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010053,1008310,'','".AddSlashes(pg_result($resaco,$iresaco,'ed49_i_documentacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010053,1008311,'','".AddSlashes(pg_result($resaco,$iresaco,'ed49_t_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from docaluno
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($ed49_i_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " ed49_i_codigo = $ed49_i_codigo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Documenta��o Pendente de Alunos nao Exclu�do. Exclus�o Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed49_i_codigo;
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Documenta��o Pendente de Alunos nao Encontrado. Exclus�o n�o Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed49_i_codigo;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclus�o efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$ed49_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:docaluno";
        $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   function sql_query ( $ed49_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from docaluno ";
     $sql .= "      inner join escola  on  escola.ed18_i_codigo = docaluno.ed49_i_escola";
     $sql .= "      inner join aluno  on  aluno.ed47_i_codigo = docaluno.ed49_i_aluno";
     $sql .= "      inner join documentacao  on  documentacao.ed02_i_codigo = docaluno.ed49_i_documentacao";
     $sql .= "      inner join bairro  on  bairro.j13_codi = escola.ed18_i_bairro";
     $sql .= "      inner join ruas  on  ruas.j14_codigo = escola.ed18_i_rua";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = escola.ed18_i_codigo";
     $sql2 = "";
     if($dbwhere==""){
       if($ed49_i_codigo!=null ){
         $sql2 .= " where docaluno.ed49_i_codigo = $ed49_i_codigo "; 
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
   function sql_query_file ( $ed49_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from docaluno ";
     $sql2 = "";
     if($dbwhere==""){
       if($ed49_i_codigo!=null ){
         $sql2 .= " where docaluno.ed49_i_codigo = $ed49_i_codigo "; 
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