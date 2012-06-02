<?php
class Model_ThemeMapper
{
    protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data theme provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Model_DbTable_Theme');
        }
        return $this->_dbTable;
    }
 
    public function save(Model_Theme $theme)
    {
        $data = array(
            'theme'   => $theme->getTheme(),
            'date_creation' => date('Y-m-d H:i:s'),
            'active' =>  $theme->getActive(),
        );

        if (null === ($id = $theme->getTheme_id())) {
            unset($data['theme_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('theme_id = ?' => $id));
        }
    }
 
    public function find($id, Model_Theme $theme)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $theme->setTheme_id($row->theme_id)
                  ->setTheme($row->theme) 
		          ->setActive($row->active)
                  ->setDateCreation($row->date_creation);
		return $theme;
    }
 
	// public function findById($id){
	// 	if(is_int($id)){
	// 		
	// 		$res_array = array();
	// 		$select = Zend_Db_Table::getDefaultAdapter()->select();
	// 		$select->from('theme');
	// 		$select->where("theme_id = ?" ,$id); 
	// 		$res = $select->query();  
	// 		foreach ($res as $val => $theme){ 
	// 			$res_array [$val] = $theme;
	// 		}
	// 		return $res_array;	
	// 	}
	// 	return false;
	// }
	
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Model_Theme();
            $entry->setTheme_id($row->theme_id)
                  ->setTheme($row->theme)
                  ->setActive($row->active)
                  ->setDateCreation($row->date_creation);
            $entries[] = $entry;
        }
        return $entries;
    }
	public function fetchAllActive()
    {
        $resultSet = $this->getDbTable()->fetchAll("active=1");
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Model_Theme();
            $entry->setTheme_id($row->theme_id)
                  ->setTheme($row->theme)
                  ->setActive($row->active)
                  ->setDateCreation($row->date_creation);
            $entries[] = $entry;
        }
        return $entries;
    }
}