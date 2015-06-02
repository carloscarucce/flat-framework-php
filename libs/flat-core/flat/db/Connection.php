<?php

namespace flat\db;

/**
 * PDO Connection that provides ability to use nested transactions via SAVEPOINTs
 * inspired from: http://www.kennynet.co.uk/2008/12/02/php-pdo-nested-transactions/
 */
class Connection extends \PDO{

    /**
     * Database drivers that support SAVEPOINTs.
     * @var string[] 
     */
    protected static $savepointTransactions = ['pgsql', 'mysql', 'mysqli', 'sqlite'];

    /**
     * The current transaction level.
     * @var type 
     */
    protected $transactionLevel = 0;

    /**
     * 
     */
    public function beginTransaction() {
        if(!$this->nestable() || $this->transactionLevel == 0) {
            parent::beginTransaction();
        } else {
            $this->exec("SAVEPOINT LEVEL{$this->transactionLevel}");
        }

        $this->transactionLevel++;
    }

    /**
     * 
     */
    public function commit() {
        $this->transactionLevel--;

        if(!$this->nestable() || $this->transactionLevel == 0) {
            parent::commit();
        } else {
            $this->exec("RELEASE SAVEPOINT LEVEL{$this->transactionLevel}");
        }
    }

    /**
     * 
     */
    public function rollBack() {
        $this->transactionLevel--;

        if(!$this->nestable() || $this->transactionLevel == 0) {
            parent::rollBack();
        } else {
            $this->exec("ROLLBACK TO SAVEPOINT LEVEL{$this->transactionLevel}");
        }
    }
    
    /**
     * Checks if current driver supports savepoints
     * @return boolean
     */
    protected function nestable() {
        return in_array(
            $this->getAttribute(\PDO::ATTR_DRIVER_NAME),
            self::$savepointTransactions
        );
    }
    
    /**
     * 
     * @param string $dsn
     * @param string $username
     * @param string $passwd
     */
    public function __construct($dsn, $username = null, $passwd = null) {
        
        parent::__construct($dsn, $username, $passwd);
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
    }
    
}
