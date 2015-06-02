<?php


namespace flat\acl;

interface TableInterface {
    
    public function addRole($role, $parentRole = null);
    
    public function allow($role, $resource, $action);
    
    public function deny($role, $resource, $action);
    
    public function isAllowed($role, $resource, $action);
    
}
