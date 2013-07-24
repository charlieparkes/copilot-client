<?php

//Copyright 2013 Technical Solutions, LLC.
//Confidential & Proprietary Information.

namespace pilot ;

class tss_main extends DB
{
      function get_technician_list($hide_inactive = true, $nativeCompanyID = "15")
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/users/technician', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('technicians') ;
            return $data;
      }
      
      
      function get_user_list($hide_inactive = true)
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/users', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('users') ;
            return $data;
      }
      
      /**
      needs review
      */
      function get_project_list($show_inactive = true)
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/projects', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('projects') ;
            return $data;
      }
      
      
      function get_customer_list($show_inactive = true)
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/customers', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('customers') ;
            return $data;
      }
      
      function get_distance_list($show_inactive = true)
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/definition/distance', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('distance') ;
            return $data;
      }
      
      function get_priority_list($show_inactive = true)
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/definition/priority', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('priority') ;
            return $data;
      }

      function get_substatus_list($current_status, $show_inactive = true)
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/definition/substatus/?&('.urlencode('status='.$current_status).')', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('substatus') ;
            return $data;
      }
      
      function get_role_list($show_inactive = true)
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/definition/role', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('role') ;
            return $data;
      }
      
      function get_service_type_list($show_inactive = true)
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/definition/type', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('type') ;
            return $data;
      }
      
      function get_status_list($show_inactive = true)
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/definition/status', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('status') ;
            return $data;
      }
      
      function get_state_list()
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/definition/state', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('state') ;
            return $data;
      }
      
      function get_event_tabs()
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/event/tabs', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('event_tabs') ;
            return $data;
      }
      
      function get_yes_no_list()
      {
            $data[0]['I'] = "1";
            $data[0]['D'] = 'Yes';
            
            $data[1]['I'] = "0";
            $data[1]['D'] = 'No';
            
            return $data;
      }
      
      function generate_password ($length = 8)
      {
            // start with a blank password
            $password = "";

            // define possible characters
            $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 

            // set up a counter
            $i = 0; 

            // add random characters to $password until $length is reached
            while ($i < $length) { 
                    // pick a random character from the possible ones
                    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
                        
                    // we don't want this character if it's already in the password
                    if (!strstr($password, $char)) { 
                      $password .= $char;
                      $i++;
                    }
            }

            return $password;
        }
        
        
      function print_errors($err_msg)
      {
            if(count($err_msg) > 0) {
                    $pcb_header = '<img src="/images/caution.png" align="absmiddle" /> <span style="color:#cc0000;">Error</span>';
                    $pcb_text = '<ul>';
                    
                    for($i=0; $i<count($err_msg); $i++) {
                            $pcb_text .= '<li style="color:#C00;">'.$err_msg[$i].'</li>';
                    }
                    
                    $pcb_text .= '</ul>';
                    
                    require_once($_SERVER['DOCUMENT_ROOT'].'/includes/class.message.box.php');
                    $post_confirm_box = new message_box();
                    $post_confirm_box->print_text($pcb_header, $pcb_text);
                    
            }
      }
        
      function append_to_log($id, $desc, $userid, $type = 1)
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/event/log/?&('.urlencode('id='.$id.',desc='.$desc.'userid='.$userid).')', 'PUT') ;
            $req->execute() ;
            $data = $req->getBlock('event_log') ;
            return $data;
      }
        
      function get_user_fullname($id)
      {
            $query = "SELECT CONCAT(w.first_name, ' ', w.last_name) AS D 
                                     FROM tss_user w
                                     WHERE w.id = ".$id;
            $rs1 = $this->query($query);
            
            
            if(count($rs1) > 0) {
                      return ($rs1[0]['D']);
            }
            else {
                      return 'ERROR';
            }
      }

      function get_user_email($id)
      {
            $query = "SELECT w.email_address AS D 
                                     FROM tss_user w
                                     WHERE w.id = ".$id;
            $rs1 = $this->query($query);
            
            
            if(count($rs1) > 0) {
                      return ($rs1[0]['D']);
            }
            else {
                      return 'ERROR';
            }
      }
       
      function get_user_timezone($id)
      {
            $query = "SELECT z.timezone AS D 
                         FROM tss_user w
                         INNER JOIN tss_timezone z
                            ON z.id = w.tss_timezone_id
                         WHERE w.id = ".$id;
            $rs1 = $this->query($query);
            
            
            if(count($rs1) > 0) {
                      return ($rs1[0]['D']);
            }
            else {
                      return 'ERROR';
            }
      }
       
       
      function get_popup_method()
      {
            if($_SESSION['device_type'] == 'computer') {
                      return 'rel="shadowbox;width='.DEFAULT_SHADOWBOX_WIDTH.';height='.DEFAULT_SHADOWBOX_HEIGHT.'"';
            }
            else {
                      return 'target="_blank"';
            }
      }
       
       
      function get_close_window_method()
      {
            if($_SESSION['device_type'] == 'computer') {
                      return 'parent.location.reload();';
            }
            else {
                      return 'opener.location.reload();self.close();';
            }
      }
       
      function get_timezone_list()
      {
            $data = array();
            $req = new \CP\Client\request('http://localhost/copilot/v1/definition/timezone', 'GET') ;
            $req->execute() ;
            $data = $req->getBlock('timezone') ;
            return $data;
      }
      
      
//        function udf_convert_date_time($timeString, $timeZone, $format) {
//            if($d = new DateTime($timeString)) {
//                    $d->setTimeZone(new DateTimeZone($timeZone));
//                    return $d->format($format);
//            }
//           
//            return NULL;
//        }
       
      // dont worry about this one
      function is_site_billable($id)
      {
            $query = "SELECT w.is_billable_address AS D 
                         FROM tss_site w
                         WHERE w.id = ".$id;
            $rs1 = $this->query($query);
            
            
            if($rs1[0]['D'] == 1) {
                      return true;
            }
            else {
                      return false;
            }
      }
       
      function get_permitted_file_extensions($attachment_type)
      {
            $data = array();
              
            $query = "SELECT p.extension AS D 
                        FROM tss_mime_extension p 
                        INNER JOIN tss_attachment_type_mime_extension x
                               ON x.tss_mime_extension_id = p.id 
                        WHERE x.tss_attachment_type_id = ".$attachment_type;
            
            $rs1 = $this->query($query);
            
            for($z = 0; $z < count($rs1); $z++) {
                foreach ($rs1[$z] as $k => $v) {
                    $data[$z]{$k} = $v;
                }
            }
            
            return $data;
      }
      
    /**
    Generate a mysql query. Sort this out later with Dave.
    */
    function get_where_clause(
                            $status,
                            $keywords,
                            $cond,
                            $sts_change,
                            $date1,
                            $date2,
                            $service_type,
                            $priority,
                            $po_number,
                            $po_field,
                            $site,
                            $projects,
                            $crew
                            ) {
            
            // STATUS
            if(count($status) > 0) {
                $sql .= " AND (";
                
                foreach($status as $v) {
                   $s = explode('|', $v);
                   
                   $status_stmt_array[] = "(E.tss_status_id = ".$s[0].(!empty($s[1]) ? " AND E.tss_substatus_id = ".$s[1] : "").")";
                }
                
                $sql .= implode(" OR ", $status_stmt_array);
                $sql .= ")";
            }   
            
            
            // DATE RANGES
            switch($cond) {
                    case "on":
                    $sql .= " AND (CONVERT_TZ(E.".$sts_change.", '".DB_TIMEZONE."', '".$this->get_user_timezone($_SESSION['user_id'])."') BETWEEN '".$date1." 00:00:00' AND '".$date1." 23:59:59')";
                    break;
                    
                    case "before":
                    $sql .= " AND (CONVERT_TZ(E.".$sts_change.", '".DB_TIMEZONE."', '".$this->get_user_timezone($_SESSION['user_id'])."') < '".$date1." 23:59:59')";
                    break;
                    
                    case "after":
                    $sql .= " AND (CONVERT_TZ(E.".$sts_change.", '".DB_TIMEZONE."', '".$this->get_user_timezone($_SESSION['user_id'])."') > '".$date1." 00:00:00')";
                    break;
                    
                    case "between":
                    $sql .= " AND (CONVERT_TZ(E.".$sts_change.", '".DB_TIMEZONE."', '".$this->get_user_timezone($_SESSION['user_id'])."') BETWEEN '".$date1." 00:00:00' AND '".$date2." 23:59:59')";
                    break;
            } // END SWITCHCASE
            
            
            
            // SITE ID
            if(trim($site) != "" && is_numeric($site)) {
                    $sql .= " AND (E.tss_site_id = ".$site.")";
            }
            
            
            // PROJECT IDs
            if(count($projects) > 0) {
                    $sql .= " AND (E.tss_project_id IN (".implode(",", $projects)."))";
            }
            
            
            // ASSIGNED CREW
            if(count($crew) > 0) {
                    $sql .= " AND (A.tss_tech_id IN (".implode(",", $crew)."))";
            }
            
            
            // SERVICE TYPE
            if(count($service_type) > 0) {
                $sql .= " AND (";
                
                foreach($service_type as $v) {
                   $s = explode('|', $v);
                   
                   $svc_type_stmt_array[] = "(E.tss_type_id = ".$s[0].(!empty($s[1]) ? " AND E.tss_subtype_id = ".$s[1] : "").")";
                }
                
                $sql .= implode(" OR ", $svc_type_stmt_array);
                $sql .= ")";
            }
            
            
            // PRIORITY
            if(count($priority) > 0) {
                    $sql .= " AND (E.tss_priority_id IN (".implode(",", $priority)."))";
            }
            
            
            if(trim($po_number) != "") {
                    $sql .= " AND (E.".$po_field." = '".$po_number."')";
            }
            
            
            if(trim($keywords) != "") {
                   // PAD SINGLE QUOTES
                   $keywords = str_replace("'", "''", trim($keywords));
                   
                   $words = explode(" ", $keywords);
                   $numrows = count($words);
                   
                   for($i = 0; $i < $numrows; $i++) {
                           $words[$i] = "%".$words[$i]."%";
                   } // END FOR
                   
                   $sql .= " AND (E.title LIKE '".$words[0]."'";
                   if($numrows > 1) {
                           for($i = 1; $i < $numrows; $i++) {
                                   $sql .= " OR E.title LIKE '".$words[$i]."'";
                           } // end for
                   } // end if
                   
                   $sql .= " OR E.description LIKE '".$words[0]."'";
                   if($numrows > 1) {
                           for($i = 1; $i < $numrows; $i++) {
                                   $sql .= " OR E.description LIKE '".$words[$i]."'";
                           } // end for
                   } // end if
                   $sql .= ")";
            }
            
            //echo $sql;
            
            return $sql;
    }
  }
  
?>
