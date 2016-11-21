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
use Admin\Form\Role as RoleForm;
use Admin\Entity\Role as RoleEntity;

use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class RolesController extends AbstractActionController
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function indexAction()
    {
        $roles = $this->getEntityManager()->getRepository('Admin\Entity\Role')->findAll();
        return new ViewModel(array('roles' => $roles));
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
        $id          = $this->params()->fromQuery('id', false);
        $builder     = new AnnotationBuilder();
        $form        = $builder->createForm(new RoleForm);
        $user        = $this->identity();
        $entity      = $id ? $this->getEntityManager()->getRepository('Admin\Entity\Role')->findOneBy(array('id' => $id)) : new RoleEntity();
        $actions     = $this->getServiceLocator()->get('Admin\Service\Acl')->getResourcesRules();
        $lang_labels = $this->getServiceLocator()->get('Core\Locale')->getLangs();
        $territories = $this->getEntityManager()->getRepository('Admin\Entity\Territory')->findAll();
        $langs       = array();
        foreach($territories as $territory){
            $_langs = \Zend\Json\Json::decode($territory->getLangs());
            $langs  = array_unique(array_merge($langs, $_langs));
        }

        if($entity === null){
            throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('No entity found with id #%s'), $id));
        }

        if($entity->getAcl() !== null){
            $entity->setAcl(new \ArrayObject(\Zend\Json\Json::decode($entity->getAcl()), \ArrayObject::ARRAY_AS_PROPS));
        }
        if($entity->getTerritories() !== null){
            $entity->setTerritories(new \ArrayObject(\Zend\Json\Json::decode($entity->getTerritories()), \ArrayObject::ARRAY_AS_PROPS));
        }
        if($entity->getLangs() !== null){
            $entity->setLangs(new \ArrayObject(\Zend\Json\Json::decode($entity->getLangs()), \ArrayObject::ARRAY_AS_PROPS));
        }

        if($id){
            $this->setCurrentBreadcrumb($this->translate(sprintf('Edit #%s', $id)));
        }else{
            $this->setCurrentBreadcrumb($this->translate('Add'));
        }

        $form->setHydrator(new ClassMethodsHydrator);
        $form->bind($entity);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $form->setData($post_data);
            if ($form->isValid()){
                $data = $form->getData();

                if(!$id) $data->setAuthor($user->getId());
                $data->setAcl(\Zend\Json\Json::encode(isset($post_data['acl'])?$data->getAcl():array()));
                $data->setTerritories(\Zend\Json\Json::encode(isset($post_data['territories'])?$data->getTerritories():array()));
                $data->setLangs(\Zend\Json\Json::encode(isset($post_data['langs'])?$data->getLangs():array()));

                $this->getEntityManager()->persist($data);
                $this->getEntityManager()->flush();
 
                $this->flashMessenger()->addSuccessMessage($this->translate('Successfully saved entity'));

                return $this->redirect()->toRoute(
                    'admin/default',
                    array(
                        'action'     => 'index',
                        'controller' => 'roles'
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

        $viewModel = new ViewModel(array(
            'form'        => $form,
            'actions'     => $actions,
            'territories' => $territories,
            'langs'       => $langs,
            'lang_labels' => $lang_labels
        ));
        return $viewModel->setTemplate('admin/roles/form.phtml');
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
