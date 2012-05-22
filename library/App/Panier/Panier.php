<?php

//on ne gère pas de qté donc pas de fonction pour modifier la quantité.
//Une fonction pour ajouter un article s'il n'est pas présent dans le panier
//Une fonction pour supprimer un article du panier

class App_Panier_Panier {

	protected $_lignes = array();
    protected $_total = 0;

    public function ajouterArticle($articleId) {
        if (! isset($this->_lignes[$articleId])) {
            $this->_lignes[$articleId] = new App_Panier_Ligne($articleId);
			$this->_lignes[$articleId]->ajouter();
			$article = $this->_lignes[$articleId]->getArticle();
			$this->_total += $article['prix'];
        }
    }
	
	public function supprimerArticle($articleId) {
		$article = $this->_lignes[$articleId]->getArticle();
        $this->_total -= $article['prix'];
		unset ($this->_lignes[$articleId]);
	}
	
	public function getLignes() {
        return $this->_lignes;
    }

    public function setLignes($lignes) {
        $this->_lignes = $lignes;
    }

    public function getTotal() {
        return $this->_total;
    }

    public function setTotal($total) {
        $this->_total = $total;
    }

}