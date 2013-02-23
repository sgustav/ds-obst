<?php

    class UserModel extends BaseModel {
    
        public $table = 'xdb_users';
        
        function getUserName($id)
        {
            $result = $this->getById($id);
            if(!$result)
            {
                return '<i>Benutzer gelöscht</i>';
            }
            
            return $result['name'];
        }
        
    }

?>