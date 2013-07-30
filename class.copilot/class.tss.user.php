<?php

//Copyright 2013 Technical Solutions, LLC.
//Confidential & Proprietary Information.

  class tss_user extends tss_main {
        function load($id) {
            if(is_numeric($id)) {
                $query = "SELECT * FROM tss_user p WHERE p.id = ".$id;
                $rs1 = $this->query($query);
                
                if(count($rs1) == 1) {
                        // BIND DATABASE FIELD NAMES TO CLASS PROPERTY NAMES...
                        foreach ($rs1[0] as $k => $v) {
                            $this->{$k} = $v;
                        }
                }

                return $rs1[0] ;
            }
            else
            {
                return "ID was malformed." ;
            }
        }
        
        
        function add($email, $phone, $first_name, $last_name, $notes, $hourly_rate, $timezone, $roles, $projects, $company = "15") {
            $this->start_trans();
            
            // GET RANDOM PASSWORD
            $pwd = $this->generate_password(8);
            
            
            // INSERT USER DATA
            $query = "INSERT INTO tss_user (
                        user_name, 
                        email_address, 
                        phone_number, 
                        first_name, 
                        last_name, 
                        password, 
                        notes, 
                        hourly_rate, 
                        tss_timezone_id, 
                        tss_customer_id,
                        created)
                        VALUES (
                        '".$email."', 
                        '".$email."', 
                        '".$phone."', 
                        '".$first_name."', 
                        '".$last_name."', 
                        '".sha1($pwd)."', 
                        '".$notes."', 
                        ".$hourly_rate.", 
                        ".$timezone.", 
                        ".$company.", 
                        NOW()
                        )";
            $this->query($query, 1);
            
            
            // GET NEW ID
            $query = "SELECT LAST_INSERT_ID() AS ID";
            $rs1 = $this->query($query, 1);
            $last_insert_id = $rs1[0]['ID'];
            
            
            // INSERT ROLES
            foreach($roles as $v) {
                $query = "INSERT INTO tss_user_group (
                            tss_group_id,
                            tss_user_id
                            ) VALUES (
                            ".$v.", 
                            ".$last_insert_id." 
                            )";
                $this->query($query, 1);
            }
            
            
            // INSERT PERMITTED PROJECTS
            foreach($projects as $v) {
                $query = "INSERT INTO tss_user_project (
                            tss_project_id,
                            tss_user_id
                            ) VALUES (
                            ".$v.", 
                            ".$last_insert_id." 
                            )";
                $this->query($query, 1);
            }
            
            $this->commit();
            
            
            
            // SEND ACCT INFO TO USER...
            require_once($_SERVER['DOCUMENT_ROOT'].'/includes/class.phpmailer.php');
            $mail = new PHPMail();
            
            if(DEV) {
               $mail->AddAddress(EMAIL_DEV.'@'.FQDN);
            }
            else {
                $mail->AddAddress($email);
            }
            
            $mail->Subject = 'Your New Account';
            $mail->Body = "A new account has been created for you.\n\nPlease use the link and login information provided to access the application:\n\nhttp://".FQDN."\nUsername/Email: ".$email."\nPassword: ".$pwd."\n\nOnce you login, you may change your password by clicking the 'Change Password' link in the upper-right portion of the screen.\n\nIf you have any questions, please direct them to ".EMAIL_ADMIN."@".FQDN;
            
            $mail->Send();
        }
        
        
        function update($id, $email, $phone, $first_name, $last_name, $notes, $hourly_rate, $timezone, $roles, $projects, $company = "15") {
            $this->start_trans();
            
            // UPDATE USER DATA
            $query = "UPDATE tss_user t SET 
                        t.user_name = '".$email."', 
                        t.email_address = '".$email."', 
                        t.phone_number = '".$phone."', 
                        t.notes = '".$notes."', 
                        t.hourly_rate = '".$hourly_rate."', 
                        t.first_name = '".$first_name."', 
                        t.last_name = '".$last_name."',
                        t.tss_timezone_id = ".$timezone.",
                        t.tss_customer_id = ".$company." 
                        WHERE t.id = ".$id;
            $this->query($query, 1);
            
            
            // UPDATE ROLES
            $query = "DELETE FROM tss_user_group WHERE tss_user_id = ".$id;
            $this->query($query, 1);
            
            
            foreach($roles as $v) {
                $query = "INSERT INTO tss_user_group (
                            tss_group_id,
                            tss_user_id
                            ) VALUES (
                            ".$v.", 
                            ".$id." 
                            )";
                $this->query($query, 1);
            }
            
            
            // UPDATE PROJECTS
            $query = "DELETE FROM tss_user_project WHERE tss_user_id = ".$id;
            $this->query($query, 1);
            
            
            foreach($projects as $v) {
                $query = "INSERT INTO tss_user_project (
                            tss_project_id,
                            tss_user_id
                            ) VALUES (
                            ".$v.", 
                            ".$id." 
                            )";
                $this->query($query, 1);
            }
            
            $this->commit();
        }
        
        
        function del($id) {
            $this->start_trans();
            
            $query = "DELETE FROM tss_user WHERE id = ".$id;
            $this->query($query, 1);
            
            $this->commit();
        }
        
        
        function change_password($pwd = NULL) {
            $mail_body = "Your password has been changed.\n\nPlease use the link below to access the application:\n\nhttp://".FQDN."\n\nIf you did not change your password, or have any questions or concerns, please direct them to ".EMAIL_ADMIN."@".FQDN;
            
            if(empty($pwd)) {
                $pwd = $this->generate_password(8);
                $mail_body = "Your password has been reset by an administrator.\n\nPlease use the link and new password below to access the application:\n\nhttp://".FQDN."\nPassword: ".$pwd."\n\nOnce you login, you may change your password by clicking the 'Change Password' link in the upper right portion of the screen.";
            }
            
            $query = "UPDATE tss_user u SET u.password = '".sha1($pwd)."' WHERE u.id = ".$this->id;
            $this->query($query);
            
            
            // SEND ACCT INFO TO USER...
            require_once($_SERVER['DOCUMENT_ROOT'].'/includes/class.phpmailer.php');
            $mail = new PHPMail();
            
            if(DEV) {
               $mail->AddAddress(EMAIL_DEV.'@'.FQDN);
            }
            else {
                $mail->AddAddress($this->email_address);
            }
            
            $mail->Subject = 'Your Login Information';
            $mail->Body = $mail_body;
            
            $mail->Send();
        }
        
        
        function get_associated_role_ids() {
            $data = array();
            
            $query = "SELECT c.tss_group_id AS D FROM tss_user_group c 
                        WHERE c.tss_user_id = ".$this->id;
            $rs1 = $this->query($query);
            
            for($z = 0; $z < count($rs1); $z++) {
                $data[] = $rs1[$z]['D'];
            }
            
            return $data;
        }
        
        
        function get_associated_role_name() {
            $data = 'Error';
            
            $query = "SELECT g.display_name AS D 
                        FROM tss_user_group c 
                        INNER JOIN tss_group g 
                               ON g.id = c.tss_group_id 
                        WHERE c.tss_user_id = ".$this->id;
            $rs1 = $this->query($query);
            
            if(count($rs1) > 0) {
                $data = $rs1[0]['D'];
            }
            
            return $data;
        }
        
        
        function get_associated_project_ids() {
            $data = array();
            
            $query = "SELECT c.tss_project_id AS D FROM tss_user_project c WHERE c.tss_user_id = ".$this->id;
            $rs1 = $this->query($query);
            
            for($z = 0; $z < count($rs1); $z++) {
                $data[] = $rs1[$z]['D'];
            }
            
            return $data;
        }
        
        
        function get_associated_ticket_ids() {
            $data = array();
            
            $query = "SELECT t.tss_event_id AS D FROM tss_schedule t WHERE t.tss_user_id = ".$this->id;
            $rs1 = $this->query($query);
            
            for($z = 0; $z < count($rs1); $z++) {
                $data[] = $rs1[$z]['D'];
            }
            
            return $data;
        }
        
        
        function set_permissions() {
                     $_SESSION['perms'] = array();
                     
                     $query = "SELECT DISTINCT p.permission_name AS P
                                    FROM tss_user u
                                         INNER JOIN tss_user_group ug 
                                              ON u.id = ug.tss_user_id
                                         INNER JOIN tss_group g 
                                              ON g.id = ug.tss_group_id 
                                         INNER JOIN tss_group_permission gp
                                              ON g.id = gp.tss_group_id
                                         INNER JOIN tss_permission p
                                              ON p.id = gp.tss_permission_id
                                    WHERE u.id = ".$this->id;
                     $rs1 = $this->query($query);
                     
                     for($z = 0; $z < count($rs1); $z++) {
                               $_SESSION['perms'][] = $rs1[$z]['P'];
                     }
                     
                     $_SESSION['company_id'] = NATIVE_COMPANY_ID;
                     $_SESSION['company_name'] = COMPANY_NAME;
                     
                     $_SESSION['auth_projects'] = $this->get_associated_project_ids();
                     $_SESSION['auth_projects'][] = '0'; // ADD PROJECT ID 0 TO PREVENT SQL ERRORS
                     
                     $_SESSION['auth_tickets'] = $this->get_associated_ticket_ids();
                     $_SESSION['auth_tickets'][] = '0'; // ADD TICKET ID 0 TO PREVENT SQL ERRORS
        }


        function record_login_data() {
                     $query = "INSERT INTO tss_user_sessions (ip_address, session_id, login, tss_user_id) 
                                              VALUES ('".$_SERVER['REMOTE_ADDR']."', '".session_id()."', NOW(), ".$this->id.")";
                     $this->query($query);
                     
                     $query = "SELECT LAST_INSERT_ID() AS I";
                     $rs1 = $this->query($query);
                     
                     $_SESSION['tss_session_id'] = $rs1[0]['I'];
        }


        function record_logout_data() {
                     if(isset($_SESSION['tss_session_id'])) {
                               $query = "UPDATE tss_user_sessions t SET t.logout = NOW() WHERE t.id = ".$_SESSION['tss_session_id'];
                               $this->query($query);
                     }
        }


        function get_last_login() {
                     $query = "SELECT IFNULL(MAX(t.login), 'Never') AS LL FROM tss_user_sessions t WHERE t.tss_user_id = ".$this->id;
                     $rs1 = $this->query($query);
                     
                     $data = $rs1[0]['LL'];
                     
                     if($data != 'Never') {
                    $data = date(DATE_FORMAT, strtotime($data));
                     }
                     
                     return $data;
        }
        
        
        function get_labor_entries($d1, $d2) {
            $data = array();
            
            $query = "SELECT 
                        L.id 
                        FROM tss_labor L INNER JOIN tss_user U ON L.tss_tech_id = U.id
                        WHERE ((L.drive_to BETWEEN '".$d1." 00:00:00' AND '".$d2." 00:00:00') 
                           OR (L.drive_from BETWEEN '".$d1." 00:00:00' AND '".$d2." 00:00:00'))
                           AND L.tss_tech_id = ".$this->id."
                        ORDER BY L.drive_to"; 
            $rs1 = $this->query($query);
            
            
            for($z = 0; $z < count($rs1); $z++) {
                    $data[] = $rs1[$z]['id'];
            }
            
            return $data;
        }
        
        
        function get_schedule_entries($d1, $d2) {
            $data = array();
            
            $query = "SELECT 
                        L.id  
                        FROM tss_schedule L 
                        INNER JOIN tss_event e 
                        ON e.id = L.tss_event_id 
                        WHERE (L.start_job >= '".$d1."' AND L.start_job < '".$d2."') 
                           AND L.tss_user_id = ".$this->id." 
                           AND e.tss_status_id < 3 
                        ORDER BY L.start_job"; 
            $rs1 = $this->query($query);
            
            
            for($z = 0; $z < count($rs1); $z++) {
                    $data[] = $rs1[$z]['id'];
            }
            
            return $data;
        }
  }
?>
