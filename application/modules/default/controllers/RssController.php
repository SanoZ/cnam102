<?php
/**
 * RssController 
 * 
 */
require_once('ApplicationController.php');
class RssController extends ApplicationController
{
    public function preDispatch()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    /**
     * Main blog rss
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function indexAction()
    {
        $limit = 10;

        $url = $this->_helper->url;
        $serverUrl = $this->_request->getScheme()  . '://' . $this->_request->getHttpHost();
		$feed = new Zend_Feed_Writer_Feed;
        $feed->setTitle('Nouveauté - Rss Feed');
        $feed->setLink($serverUrl .".com");
        $feed->setFeedLink('http://www.ventes.com/rss', 'atom');
		$feed->addAuthor(
            array(
                'name'  => 'e-Ventes',
                'email' => 'admin@eventes.com',
                'uri'   => $serverUrl,
            )
        );
		$feed->setDateModified(time());
		$feed->addHub('http://pubsubhubbub.appspot.com/');

		/**
		* Add one or more entries. Note that entries must
		* be manually added once created.
		*/
		$tableaux = new Model_TableauMapper();
        $liste_tableaux = $tableaux->fetchAll();
		foreach ($liste_tableaux as $i => $row) {
				$entry = $feed->createEntry();
				$entry->setTitle($row->titre);
				$entry->setLink($serverUrl . ".com/test".$row->article_id); 
				$entry->setDateModified(time());
				$entry->setDateCreated(strtotime($row->date_publication));
				$entry->setDescription($row->description);
				
				$feed->addEntry($entry);
		}
	 
		echo $feed->export('atom');
    }

    
}