<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Form\Annotation\AnnotationBuilder;
use Admin\Form\Territory as TerritoryForm;
use Admin\Entity\Territory as TerritoryEntity;

use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class TerritoriesController extends AbstractActionController
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function indexAction()
    {
        $territories = $this->getEntityManager()->getRepository('Admin\Entity\Territory')->findAll();
        return new ViewModel(array('territories' => $territories, 'langs' => $this->getServiceLocator()->get('Core\Locale')->getLangs()));
    }

    public function addAction()
    {
        return $this->update();
    }

    public function editAction()
    {
        return $this->update();
    }

    private function update()
    {
        $id      = $this->params()->fromQuery('id', false);
        $builder = new AnnotationBuilder();
        $form    = $builder->createForm(new TerritoryForm);
        $user    = $this->identity();
        $entity  = $id ? $this->getEntityManager()->getRepository('Admin\Entity\Territory')->findOneBy(array('id' => $id)) : new TerritoryEntity();
        $langs   = $this->getServiceLocator()->get('Core\Locale')->getLangs();

        if($entity === null){
            throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('No entity found with id #%s'), $id));
        }

        if($entity->getLangs() !== null){
            $entity->setLangs(new \ArrayObject(\Zend\Json\Json::decode($entity->getLangs()), \ArrayObject::ARRAY_AS_PROPS));
            $_langs = array();
            foreach($entity->getLangs() as $_lang){
            	if(isset($langs[$_lang])){
            		$_langs[$_lang] = $langs[$_lang];
            	}
            }
            $form->get('default_lang')->setValueOptions($_langs);
        }

        if($id){
            $form->get('url_code')->setAttribute('readonly', 'readonly');
            $this->setCurrentBreadcrumb($this->translate(sprintf('Edit #%s', $id)));
        }else{
            $this->setCurrentBreadcrumb($this->translate('Add'));
        }

        $form->get('langs')->setValueOptions($langs);

        $form->setHydrator(new ClassMethodsHydrator);
        $form->bind($entity);

        $request = $this->getRequest();
        if ($request->isPost()) {
            if(($id && $request->getPost('url_code') != $entity->getUrlCode()) || $entity->getUrlCode() === null){
                $url_code_validator = new \DoctrineModule\Validator\NoObjectExists(array(
                    'object_repository' => $this->getEntityManager()->getRepository('Admin\Entity\Territory'),
                    'fields'            => array('url_code'),
                ));
                $form->getInputFilter()->get('url_code')->getValidatorChain()->addValidator($url_code_validator);
            }
            $post_data = $request->getPost();
            $form->setData($post_data);
            if ($form->isValid()){
                $data = $form->getData();

                if(!$id) $data->setAuthor($user->getId());
                $data->setLangs(\Zend\Json\Json::encode($data->getLangs()));

                $this->getEntityManager()->persist($data);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage($this->translate('Successfully saved entity'));

                return $this->redirect()->toRoute(
                    'admin/default',
                    array(
                        'action'     => 'index',
                        'controller' => 'territories'
                    )
                );

            }else{
                foreach($form->getMessages() as $label => $element){
                    foreach($element as $error){
                        $this->flashMessenger()->addErrorMessage($form->get($label)->getLabel() . ' : ' . $error);
                    }
                }
            }
        }

        $viewModel = new ViewModel(array('form' => $form));
        return $viewModel->setTemplate('admin/territories/form.phtml');
    }

    /**
     * get entityManager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Db');
        }

        return $this->em;
    }

    private function setCurrentBreadcrumb($label)
    {
        $navigation = $this->getServiceLocator()->get('Admin\Navigation');
        $page = $navigation->findBy('resource', $this->getEvent()->getRouteMatch()->getParam('controller', 'index'));
        $page->addPage(array(
            'uri'    => $this->getRequest()->getRequestUri(),
            'label'  => $label,
            'active' => true,
        ));
    }
}
