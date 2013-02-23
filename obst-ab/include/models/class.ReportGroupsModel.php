<?php

    class ReportGroupsModel extends BaseModel {
    
        public $table = 'xdb_report_groups';
        
        function getGroupName($id)
        {
            if($id == -1)
                return '<i>keine Gruppe</i>';
                
            $result = $this->getById($id);
            if(!$result)
            {
                return '<i>Gruppe gelöscht? (Fehler!)</i>';
            }
            
            return $result['name'];
        }
        
    };
    
?>