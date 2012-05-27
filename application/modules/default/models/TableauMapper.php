<?php
class Model_TableauMapper
{
    protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data tableau provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Model_DbTable_Tableau');
        }
        return $this->_dbTable;
    }
 
    public function save(Model_Tableau $tableau)
    { 
        $data = array(
            'titre'   => $tableau->getTitre(),
            'description' => $tableau->getDescription(),
            'theme_id' => $tableau->getTheme_id(),
            'date_publication' => date('Y-m-d H:i:s'),
            'date_modification' => date('Y-m-d H:i:s'),
            'stock' => 1,
            'active' =>  $tableau->getActive(),
            'image' =>  $tableau->getImage(),
        ); 
        if (null === ($id = $tableau->getArticle_id())) {
            unset($data['article_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('article_id = ?' => $id));
        }
    }
	

    public function find($id, Model_Tableau $tableau)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        return $tableau->setArticle_id($row->article_id)
                  ->setDescription($row->description)
                  ->setTitre($row->titre)
                  ->setImage($row->image)
                  ->setActive($row->active)
                  ->setDatepublication($row->date_publication); 
    }
	// 
	// public function fetchRow($value)
	//     {
	//         $result = $this->getDbTable()->find(value);
	// 	$select->where($value) ;
	// 	$row = $table->fetchRow($select);
	// 	if($row){
	// 		return $row->toArray();
	// 	}else
	// 		return null;
	// 
	//     }
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Model_Tableau();
            $entry->setArticle_id($row->article_id)
                  ->setDescription($row->description)
                  ->setTitre($row->titre)
                  ->setImage($row->image)
                  ->setActive($row->active)
                  ->setDatepublication($row->date_publication);
            $entries[] = $entry;
        }
        return $entries;
    }
}