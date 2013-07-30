<?php

//Copyright 2013 Technical Solutions, LLC.
//Confidential & Proprietary Information.

namespace pilot ;

class tss_material extends tss_main {
        function load($id)
        {
            $data = array();
            $req = new \CP\Client\request('GET', 'http://localhost/copilot/v1/material/'.$id) ;
            $req->execute() ;
            $data = $req->getBlock('material') ;
            return $data;

            foreach ($data as $k => $v)
            {
                $this->{$k} = $v;
            }
        }
        
        
        
        function get_creator_name() {
            $data = array();
            $req = new \CP\Client\request('GET', 'http://localhost/copilot/v1/user/'.$this->tss_user_id.'?@(fullname)') ;
            $req->execute() ;
            $data = $req->getBlock('material_creator') ;
            return $data;
        }
        
        
        
        function del() {
                $query = "DELETE FROM tss_material WHERE tss_material.id = ".$this->id;
                $this->query($query);
                
			 $histText = "Material item # ".$this->id." deleted by ".$_SESSION['first_name']." ".$_SESSION['last_name'].".";
			 $this->append_to_log($this->tss_event_id, $histText);
        }
}
?>
