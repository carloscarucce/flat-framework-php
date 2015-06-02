<?php

namespace flat;

class Acl{
    
    /**
     *
     * @var \flat\acl\TableInterface
     */
    private $_table;
    
    /**
     * 
     * @param \flat\acl\TableInterface $table
     */
    public function setTable(\flat\acl\TableInterface $table) {
        $this->_table = $table;
    }

    /**
     * 
     * @param \flat\flat\acl\Resource $resource
     */
    public function addResource(\flat\acl\Resource $resource) {
        $this->_table->addResource($resource);
    }

    /**
     * 
     * @param $role
     * @param string $parentRoleName
     */
    public function addRole($role, $parentRoleName = null) {
        $this->_table->addRole($role, $parentRoleName);
    }

    /**
     * 
     * @param string $roleName
     * @param string $resouceName
     * @param string $action
     */
    public function allow($roleName, $resouceName, $action) {
        $this->_table->allow($roleName, $resouceName, $action);
    }

    /**
     * 
     * @param string $roleName
     * @param string $resouceName
     * @param string $action
     */
    public function deny($roleName, $resouceName, $action) {
        $this->_table->deny($roleName, $resouceName, $action);
    }

    /**
     * 
     * @param string $roleName
     * @param string $resouceName
     * @param string $action
     * @return boolean
     */
    public function isAllowed($roleName, $resouceName, $action) {
        return $this->_table->isAllowed($roleName, $resouceName, $action);
    }
    
    /**
     * 
     * @param \flat\acl\table\TableInterface $table
     */
    public function __construct(\flat\acl\TableInterface $table = null) {
        if(\is_null($table)){
            $this->setTable(new \flat\acl\MemoryTable());
        }
    }

}
