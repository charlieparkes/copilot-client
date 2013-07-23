<?php

//Copyright 2013 Technical Solutions, LLC.
//Confidential & Proprietary Information.

namespace pilot ;

    class DB {
            var $created;
            
            function __construct($db_hst = DB_SERVER, $db_id = DB_NAME, $db_usr = DB_USER, $db_pwd = DB_PW) {
                    $this->db_hst = $db_hst;
                    $this->db_id = $db_id;
                    $this->db_usr = $db_usr;
                    $this->db_pwd = $db_pwd;

                    $this->conn = mysql_connect($this->db_hst, $this->db_usr, $this->db_pwd, TRUE);
                    mysql_select_db($this->db_id);
                    
                    $this->exec_func = "\$result = mysql_query(\$sql, \$this->conn);";
                    $this->fetch_func = "return (\$row = mysql_fetch_array(\$result)) ? TRUE : FALSE;";
                    
                    if(!$this->conn)
                    {
                            DB::throwError(mysql_error());
                            $this->created = FALSE;
                            exit;
                    }
                    else
                    {
                            $this->created = TRUE;
                    }

                    require_once("/client/copilot.client.php") ;
            } 

            
            function throwError($errMsg) {
                    PRINT $errMsg;
            }
            
            function start_trans() {
                    $this->query("SET AUTOCOMMIT=0");
                    $this->query("START TRANSACTION");
            }
            
            function commit() {
                    $this->query("COMMIT");
                    $this->query("SET AUTOCOMMIT=1");
            }
            
            function rollback() {
                    $this->query("ROLLBACK");
                    $this->query("SET AUTOCOMMIT=1");
            }
            
            function query($sql, $rb=0) {
                    //echo $sql;
                    
                    eval($this->exec_func);
                    
                    
                    if($result === "DISABLED") { //if($result === FALSE) {
                        	   // ON ERROR, NOTIFY ADMINS...
                            DB::throwError(mysql_error());
                		   
                		   require_once($_SERVER['DOCUMENT_ROOT'].'/includes/class.phpmailer.php');
                		   $mail = new PHPMail();
                		   
                		   $mail->AddAddress(EMAIL_DEV."@".FQDN, 'Development Team');
                		   $mail->Subject = "Query Failure";
                		   $mail->Body = "Script: ".$_SERVER['SCRIPT_FILENAME']."\nError: ".mysql_error()."\nQuery: ".$sql;
                		   $mail->Body .= "\n\n".$_SERVER['HTTP_USER_AGENT']." | ".$_SESSION['display_name'];
                		   
                            if($rb == 1) { 
                                    $this->rollback();
                                    $mail->Subject = "Transaction Failure";
                            }

                            $mail->Send();
                            
                            exit;
                    } 
                    
                    
                    if(preg_match("/^SELECT/i", $sql)) {
                            for($i = 0; eval($this->fetch_func); $i++) {
                                    foreach($row as $k => $v) {
                                            $rslt_arr[$i][$k] = $v;
                                    } 
                            } 
                            
                            return $rslt_arr;
                    }
                    
                    
                    return TRUE;
            }
    }
?>
