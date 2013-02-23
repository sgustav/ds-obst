<?php

    class ReportsModel extends BaseModel {
    
        public $table = 'xdb_reports';
        
        /**
          * Examines whether the user specified by $userid is the
          * owner of the report specified by $reportid.
          */
        public function checkOwnership($userid, $reportid)
        {
            $report = $this->getById($reportid);
            
            if($report === false)
                return false;
                
            if($report['user_id'] == $userid)
                return true;
            
            return false;
        }
        
    };
    
?>