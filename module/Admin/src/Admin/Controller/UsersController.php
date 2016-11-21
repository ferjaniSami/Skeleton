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
use Admin\Form\UserManagement as UserManagementForm;
use Admin\Entity\User as UserEntity;

use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class UsersController extends AbstractActionController
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function indexAction()
    {
        $users = $this->getEntityManager()->getRepository('Admin\Entity\User')->findAll();
        return new ViewModel(array('users' => $users));
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
        $form    = $builder->createForm(new UserManagementForm);
        $user    = $this->identity();
        $entity  = $id ? $this->getEntityManager()->getRepository('Admin\Entity\User')->findOneBy(array('id' => $id)) : new UserEntity();

        if($entity === null){
            throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('No entity found with id #%s'), $id));
        }

        if($entity->getRoles() !== null){
            $entity->setRoles(new \ArrayObject(\Zend\Json\Json::decode($entity->getRoles()), \ArrayObject::ARRAY_AS_PROPS));
        }

        if($id){
            $this->setCurrentBreadcrumb($this->translate(sprintf('Edit #%s', $id)));
        }else{
            $this->setCurrentBreadcrumb($this->translate('Add'));
        }

        $roles  = $this->getEntityManager()->getRepository('Admin\Entity\Role')->findAll();
        $_roles = array();
        foreach($roles as $role){
            $_roles[$role->getId()] = $role->getLabel();
        }
        $form->get('roles')->setValueOptions($_roles);

        $form->setHydrator(new ClassMethodsHydrator);
        $form->bind($entity);

        $request = $this->getRequest();
        if ($request->isPost()) {
            if(($id && $request->getPost('login') != $entity->getLogin()) || $entity->getLogin() === null){
                $login_validator = new \DoctrineModule\Validator\NoObjectExists(array(
                    'object_repository' => $this->getEntityManager()->getRepository('Admin\Entity\User'),
                    'fields'            => array('login'),
                ));
                $form->getInputFilter()->get('login')->getValidatorChain()->addValidator($login_validator);
            }
            if ($form->get('password')->getValue() == ''){
                $form->getInputFilter()->remove('password');
                $form->getInputFilter()->remove('password_confirmation');
            } 
            $post_data = $request->getPost();
            $form->setData($post_data);
            if ($form->isValid()){
                $data = $form->getData();

                if(!$id) $data->setAuthor($user->getId());
                $data->setRoles(\Zend\Json\Json::encode(isset($post_data['roles'])?$data->getRoles():array()));

                $this->getEntityManager()->persist($data);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage($this->translate('Successfully saved entity'));

                return $this->redirect()->toRoute(
                    'admin/default',
                    array(
                        'action'     => 'index',
                        'controller' => 'users'
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
        return $viewModel->setTemplate('admin/users/form.phtml');
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
