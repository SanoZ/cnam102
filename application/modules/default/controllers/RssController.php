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
        $feed->setFeedLink('http://'.$this->_siteweb.'/rss', 'atom');
		$feed->addAuthor(
            array(
                'name'  => 'e-Ventes',
                'email' => 'admin@eventes.com',
                'uri'   => $serverUrl,
            )
        );
		$feed->setDateModified(time());

		/**
		* Add one or more entries. Note that entries must
		* be manually added once created.
		*/
		$article_model = new Model_Article(); 
		$articles = $article_model->getFilteredArticles( array(), false);  
		foreach ($articles as $i => $row) { 
				$entry = $feed->createEntry();
				$entry->setTitle($row['titre']);
				$entry->setLink($serverUrl . "/article/".$row['article_id']); 
				$entry->setDateModified(time());
				$entry->setDateCreated(strtotime($row['date_publication']));
				$entry->setDescription($row['description']);
				
				$feed->addEntry($entry);
		}
	 
		echo $feed->export('atom');
    }

    
}