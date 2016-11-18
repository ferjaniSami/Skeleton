<?php

namespace Artist\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Artist\Model\Artist;          // <-- Add this import
 use Artist\Form\ArtistForm; 

class ArtistController extends AbstractActionController
{
	protected $artistTable;
	public function indexAction()
	{
		return new ViewModel(array(
             'artists' => $this->getArtistTable()->fetchAll(),
         ));
	}

	// Add content to this method:
     public function addAction()
     {
         $form = new ArtistForm();
         $form->get('submit')->setValue('Add');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $artist = new Artist();
             $form->setInputFilter($artist->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $artist->exchangeArray($form->getData());
                 $this->getArtistTable()->saveArtist($artist);

                 // Redirect to list of artists
                 return $this->redirect()->toRoute('artist');
             }
         }
         return array('form' => $form);
     }


	public function editAction()
	{
	}

	public function deleteAction()
	{
	}
	
	public function getArtistTable()
	{
		if (!$this->artistTable) {
			$sm = $this->getServiceLocator();
			$this->artistTable = $sm->get('Artist\Model\ArtistTable');
		}
		return $this->artistTable;
	}
}