<?php

class Frontend_Model_DbTable_Article extends Zend_Db_Table_Abstract
{

    protected $_name = 'articles';

	public function getArticles()
    {		
		
		
        $select = $this->select();
        return $this->fetchAll($select);
    }


}

