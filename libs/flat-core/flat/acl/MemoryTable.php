<?php

namespace flat\acl;

class MemoryTable implements \flat\acl\TableInterface{
    
    private $_roles = [];
    private $_roleChildren = [];
    private $_resources = [];
    private $_permissions = [];

    /**
     * 
     * @param string $role
     * @param string $parentRoleNames
     */
    public function addRole($role, $parentRoleNames = null) {
        
        if(!\in_array($role, $this->_roles)){
            $this->_roles[] = $role;
        }
        
        if(!\is_null($parentRoleNames)){
            
            if(!\is_array($parentRoleNames)){
                $parentRoleNames = [$parentRoleNames];
            }
            
            $this->_roleChildren = \array_merge_recursive($this->_roleChildren, [
                $role => $parentRoleNames
            ]);
            
        }
        
    }

    /**
     * 
     * @param string $role
     * @param string $resource
     * @param string $action
     */
    public function allow($role, $resource, $action) {
        
        if(!\in_array($resource, $this->_resources)){
            $this->_resources[] = $resource;
        }
        
        $this->_permissions = \array_merge_recursive($this->_permissions, [
            $role => [
                $resource => [ $action ]
            ]
        ]);
        
    }

    /**
     * 
     * @param string $role
     * @param string $resource
     * @param string $action
     */
    public function deny($role, $resource, $action) {
        
        $pos = $this->_getPosition($role, $resource, $action);
        if($pos !== false){
            unset($this->_permissions[$role][$resource][$pos]);
        }
        
    }

    /**
     * 
     * @param string $role
     * @param string $resource
     * @param string $action
     * @return boolean
     */
    public function isAllowed($role, $resource, $action) {
        
        //Have permission in current role?
        $permission = $this->_getPosition($role, $resource, $action) !== false;
        
        //Search for permission in children
        if(!$permission && isset($this->_roleChildren[$role])){
           
            foreach($this->_roleChildren[$role] as $childRole){
                $permission = $this->isAllowed($childRole, $resource, $action);
                
                if($permission){
                    break;
                }
            }
            
        }
        
        return $permission;
        
    }
    
    /**
     * Search for the array position of $action
     * @param string $role
     * @param string $resource
     * @return int|false
     */
    private function _getPosition($role, $resource, $action) {
        
        $pos = false;
        
        if(isset($this->_permissions[$role][$resource])){
            
            $pos = \array_search($action, $this->_permissions[$role][$resource]);
            
        }
        
        return $pos;
        
    }

}
