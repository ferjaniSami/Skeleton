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
use Admin\Form\Login as LoginForm;
use Admin\Form\User as UserForm;

use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class UserController extends AbstractActionController
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function loginAction()
    {
        $this->layout('admin/disconnect');

        if ($user = $this->identity()) {
            return $this->redirect()->toRoute('admin');
        }

        $builder = new AnnotationBuilder();
        $form    = $builder->createForm(new LoginForm);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $form->setData($post_data);
            if ($form->isValid()){
                $data        = $form->getData();
                $authService = $this->getServiceLocator()->get('Auth');
                $adapter     = $authService->getAdapter();

                if ($user = $this->getEntityManager()->getRepository('Admin\Entity\User')->findOneBy(array('mail' => $data['login']))) {
                    $data['login'] = $user->getLogin();
                }

                $adapter->setIdentityValue($data['login']);
                $adapter->setCredentialValue($data['password']);

                $authResult = $authService->authenticate();
                if ($authResult->isValid()) {
                    $identity = $authResult->getIdentity();
                    $authService->getStorage()->write($identity);

                    if ($data['rememberme']) {
                        $time = 1209600; // 14 days (1209600/3600 = 336 hours => 336/24 = 14 days)
                        $sessionManager = new \Zend\Session\SessionManager();
                        $sessionManager->rememberMe($time);
                    }

                    return $this->redirect()->toRoute('admin');
                }else{
                    foreach($authResult->getMessages() as $error){
                        $this->flashMessenger()->addErrorMessage($error);
                    }
                }
            }else{
                foreach($form->getMessages() as $label => $element){
                    foreach($element as $error){
                        $this->flashMessenger()->addErrorMessage($form->get($label)->getLabel() . ' : ' . $error);
                    }
                }
            }
        }

        return new ViewModel(array('form' => $form));
    }

    public function logoutAction()
    {
        $auth = $this->getServiceLocator()->get('Auth');

        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
        }
        $auth->clearIdentity();
        $sessionManager = new \Zend\Session\SessionManager();
        $sessionManager->forgetMe();

        return $this->redirect()->toRoute(
            'admin/default',
            array(
                'action'     => 'login',
                'controller' => 'user'
            )
        );

    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function editAction()
    {
        $builder = new AnnotationBuilder();
        $form    = $builder->createForm(new UserForm);
        $user    = $this->identity();
        $entity  = $this->getEntityManager()->getRepository('Admin\Entity\User')->findOneBy(array('id' => $user->getId()));

        $form->setHydrator(new ClassMethodsHydrator);
        $form->bind($entity);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $form->setData($post_data);
            if($request->getPost('login') != $user->getLogin()){
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
            if ($form->isValid()){
                $data = $form->getData();

                $data->setAuthor($user->getId());

                // $this->getServiceLocator()->get('EntityHydrator')->hydrate($entity, $data);

                $this->getEntityManager()->persist($data);
                $this->getEntityManager()->flush();

                $this->getServiceLocator()->get('Auth')->getStorage()->write($data);

                $this->flashMessenger()->addSuccessMessage($this->translate('Successfully saved entity'));

                return $this->redirect()->toRoute(
                    'admin/default',
                    array(
                        'action'     => 'index',
                        'controller' => 'user'
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

        return new ViewModel(array('form' => $form));
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
}
