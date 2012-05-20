<?php

class Frontend_Model_DbTable_Theme extends Zend_Db_Table_Abstract
{

    protected $_name = 'themes';
	
	static public function getTheme()
    {	
        $select = $this->select();
        return $this->fetchAll($select);
    }


}

