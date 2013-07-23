<?php

//Copyright 2013 Technical Solutions, LLC.
//Confidential & Proprietary Information.

class tss_material extends tss_main {
        function load($id) {
            if(is_numeric($id)) {
                    $query = "SELECT *,
                        	   	  CONVERT_TZ(p.created, '".DB_TIMEZONE."', '".$this->get_user_timezone($_SESSION['user_id'])."') AS created_locale
                        	   	  FROM ".get_class($this)." p 
                        	   	  WHERE p.id = ".$id;
                    $rs1 = $this->query($query);
                    
                    if(count($rs1) == 1) {
                            // BIND DATABASE FIELD NAMES TO CLASS PROPERTY NAMES...
                            foreach ($rs1[0] as $k => $v) {
                                $this->{$k} = $v;
                            }
                    }
            }
        }
        
        
        
        function get_creator_name() {
                $query = "SELECT CONCAT(w.first_name, ' ', w.last_name) AS D 
                                    FROM tss_user w
                                    WHERE w.id = ".$this->tss_user_id;
                $rs1 = $this->query($query);
                
                
                if(count($rs1) > 0) {
                        return ($rs1[0]['D']);
                }
                else {
                        return 'ERROR';
                }
        }
        
        
        
        function del() {
                $query = "DELETE FROM tss_material WHERE tss_material.id = ".$this->id;
                $this->query($query);
                
			 $histText = "Material item # ".$this->id." deleted by ".$_SESSION['first_name']." ".$_SESSION['last_name'].".";
			 $this->append_to_log($this->tss_event_id, $histText);
        }
}
?>
