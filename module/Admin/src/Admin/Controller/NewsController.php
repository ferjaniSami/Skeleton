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
use Admin\Form\News as NewsForm;
use Admin\Entity\News as NewsEntity;
use Admin\Entity\Territory as TerritoryEntity;

use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class NewsController extends AbstractActionController
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function indexAction()
    {
        $territories   = $this->getEntityManager()->getRepository('Admin\Entity\Territory')->findAll();
        $user          = $this->identity();
        $user_acl_data = $this->getEntityManager()->getRepository('Admin\Entity\User')->getTerritoriesAndLocales($user);

        if($territories && is_array($territories) && count($territories)){
            $news = $locales = array();
            foreach($territories as $index => $territory){
                if(in_array($territory->getUrlCode(), $user_acl_data['territories'])){
                    $locales[$territory->getId()] = $this->getEntityManager()->getRepository('Admin\Entity\Territory')->getLocalesByTerritory($territory, true);
                    foreach($locales[$territory->getId()] as $_locale => $_label){
                        if(!in_array($_locale, $user_acl_data['locales'])){
                            unset($locales[$territory->getId()][$_locale]);
                        }
                    }
                    $news[$territory->getId()] = $this->getEntityManager()->getRepository('Admin\Entity\Translation\NewsTranslation')->findEntitiesByLocales(array_keys($locales[$territory->getId()]));
                }else{
                    unset($territories[$index]);
                }
            }
        }else{
            throw new \Zend\Mvc\Exception\InvalidControllerException($this->translate('A territory must be created.'));
        }
 
        return new ViewModel(array(
            'news'        => $news,
            'territories' => $territories,
            'locales'     => $locales
        ));
    }

    public function translationsAction()
    {
        $user          = $this->identity();
        $user_acl_data = $this->getEntityManager()->getRepository('Admin\Entity\User')->getTerritoriesAndLocales($user);

        $id      = $this->params()->fromQuery('id', false);
        $entity  = $this->getEntityManager()->getRepository('Admin\Entity\News')->findOneBy(array('id' => $id));
        if($entity === null){
            throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('No entity found with id #%s'), $id));
        }

        $id_territory = $this->params()->fromQuery('id_territory', false);
        $territory    = $this->getEntityManager()->getRepository('Admin\Entity\Territory')->findOneBy(array('id' => $id_territory));
        if($territory === null){
            throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('No territory found with id #%s'), $id_territory));
        }

        $this->setCurrentBreadcrumb($this->translate(sprintf('Translations #%s', $id)));

        $locales = $this->getEntityManager()->getRepository('Admin\Entity\Territory')->getLocalesByTerritory($territory, true);
        foreach($locales as $_locale => $_label){
            if(!in_array($_locale, $user_acl_data['locales'])){
                unset($locales[$_locale]);
            }
        }
        if(false == ($translations = $this->getEntityManager()->getRepository('Admin\Entity\Translation\NewsTranslation')->findTranslationsByObjectIdAndLocales($id, array_keys($locales)))){
            throw new \Zend\Mvc\Exception\InvalidArgumentException($this->translate('You are not authorized to see this page'));
        }

        $unused_locales  = $locales;
        foreach($translations as $locale => $translation){
            unset($unused_locales[$locale]);
        }

        return new ViewModel(array(
            'translations'   => $translations,
            'entity'         => $entity,
            'locales'        => $locales,
            'unused_locales' => $unused_locales,
            'territory'      => $territory
        ));
    }

    public function addAction()
    {
        return $this->update();
    }

    public function addTranslationAction()
    {
        return $this->update();
    }

    public function editAction()
    {
        return $this->update();
    }

    public function editTranslationAction()
    {
        return $this->update();
    }

    private function update()
    {
        $action              = $this->params('action');
        $builder             = new AnnotationBuilder();
        $form                = $builder->createForm(new NewsForm);
        $entity              = new NewsEntity;
        $territory           = new TerritoryEntity;
        $user                = $this->identity();
        $translatable_fields = $this->getEntityManager()->getRepository('Admin\Entity\Translation\NewsTranslation')->getTranslatableFieldsByClass('Admin\Entity\News');

        $this->setupUpdate($action, $entity, $form, $translatable_fields, $territory, $user);

        $form->setHydrator(new ClassMethodsHydrator);
        $form->bind($entity);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $form->setData($post_data);
            if ($form->isValid()){
                $data = $form->getData();

                switch($action){
                    case 'add':
                        $data
                            ->setAuthor($user->getId())
                            ->setAuthorTranslation($user->getId())
                            ->setStatusTranslation($data->getStatus());
                        break;
                    case 'add-translation': break;
                        $data->setAuthorTranslation($user->getId());
                    case 'edit': break;
                    case 'edit-translation': break;
                }

                $this->getEntityManager()->persist($data);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage($this->translate('Successfully saved entity'));

                if($action == 'edit'){
                    return $this->redirect()->toRoute(
                        'admin/default',
                        array(
                            'action'     => 'index',
                            'controller' => 'news'
                        )
                    );
                }else{
                    return $this->redirect()->toRoute(
                        'admin/default',
                        array(
                            'action'     => 'translations',
                            'controller' => 'news'
                        ),
                        array(
                            'query' => array(
                                'id'           => $data->getId(),
                                'id_territory' => $territory->getId()
                            )
                        )
                    );
                }

            }else{
                foreach($form->getMessages() as $label => $element){
                    foreach($element as $error){
                        $this->flashMessenger()->addErrorMessage($form->get($label)->getLabel() . ' : ' . $error);
                    }
                }
            }
        }

        $viewModel = new ViewModel(array('form' => $form));
        return $viewModel->setTemplate('admin/news/form.phtml');
    }

    private function setupUpdate($action, &$entity, &$form, $translatable_fields, &$territory, $user)
    {
        // add global : $type add, locale (required + acl), id_territory (acl)
        // add translation : $type add-translation, id (required), locale (required + acl), id_territory (acl)
        // edit global : $type edit, id (required), id_territory (acl)
        // edit translation : $type edit-translation, id (required), locale (required + acl), id_territory (acl)

        $_entity = null;

        // check required id
        if(
               $action == 'add-translation'
            || $action == 'edit'
            || $action == 'edit-translation'
        ){
            if(false === ($id = $this->params()->fromQuery('id', false))){
                throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('Required argument \'%s\'  not found'), 'id'));
            }
            if(null === ($_entity = $this->getEntityManager()->getRepository('Admin\Entity\News')->findOneBy(array('id' => $id)))){
                throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('No entity found with id #%s'), $id));
            }
        }

        // check required locale
        if(
               $action == 'add'
            || $action == 'add-translation'
            || $action == 'edit-translation'
        ){
            if(false === ($locale = $this->params()->fromQuery('locale', false))){
                throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('Required argument \'%s\'  not found'), 'locale'));
            }
            $complete_locale = $locale;
            if(strpos($locale, '-') !== false){
                $parts = explode('-', $locale);
                if(count($parts) == 2){
                    $locale = $parts[1];
                }elseif(count($parts) > 2){
                    throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('Invalid locale \'%s\''), $locale));
                }
            }
            $langs = $this->getServiceLocator()->get('Core\Locale')->getLangs();
            if(!isset($langs[$locale])){
                throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('Invalid locale \'%s\''), $locale));
            }
        }

        // check required id_territory
        // @all $action
        if(
               $action == 'add'
            || $action == 'add-translation'
            || $action == 'edit'
            || $action == 'edit-translation'
        ){
            if(false === ($id_territory = $this->params()->fromQuery('id_territory', false))){
                throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('Required argument \'%s\'  not found'), 'id_territory'));
            }
            if(null === ($territory = $this->getEntityManager()->getRepository('Admin\Entity\Territory')->findOneBy(array('id' => $id_territory)))){
                throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('No entity found with id #%s'), $id_territory));
            }
        }

        // check valid locale and territory
        if(isset($complete_locale) && isset($territory)){
            $territoryLocales = $this->getEntityManager()->getRepository('Admin\Entity\Territory')->getLocalesByTerritory($territory, true);
            if(!isset($territoryLocales[$complete_locale])){
                throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('Territory \'%s\' has no locale \'%s\''), $territory->getName(), $complete_locale));
            }
        }

        // add-translation locale already exists + setup entity
        if($action == 'add-translation'){
            $_result = $this->getEntityManager()->getRepository('Admin\Entity\Translation\NewsTranslation')->findTranslationsByObjectIdAndLocales($id, array($complete_locale));
            if(!empty($_result)){
                throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('Can not add this translation. The locale \'%s\' already exists for entity #%s'), $complete_locale, $id));
            }
            $_entity->setTranslatableLocale($complete_locale);
            $this->getEntityManager()->refresh($_entity);
        }

        // edit-translation locale no exists + setup entity
        if($action == 'edit-translation'){
            $_result = $this->getEntityManager()->getRepository('Admin\Entity\Translation\NewsTranslation')->findTranslationsByObjectIdAndLocales($id, array($complete_locale));
            if(empty($_result)){
                throw new \Zend\Mvc\Exception\InvalidArgumentException(sprintf($this->translate('No entity found with id #%s and locale \'%s\''), $id, $complete_locale));
            }
            $_entity->setTranslatableLocale($complete_locale);
            $this->getEntityManager()->refresh($_entity);
        }

        // add setup entity
        if($_entity === null){
            $_entity = new NewsEntity;
            $_entity->setTranslatableLocale($complete_locale);
        }

        // fill form & breadcrumb according to action
        $elementsToValidate = array();
        switch($action){
            case 'add':
                $this->setCurrentBreadcrumb($this->translate('Add'));
                // All fields excludes %_translation
                foreach($form->getElements() as $elementName => $element){
                    if(strpos($elementName, '_translation') !== false){
                        $form->remove($elementName);
                    }else{
                        $elementsToValidate[] = $elementName;
                    }
                }
                break;
            case 'add-translation':
                $this->setCurrentBreadcrumb($this->translate('Add translation'));
                // All fields excludes !isset in $translatable_fields
                foreach($form->getElements() as $elementName => $element){
                    if(!in_array($elementName, $translatable_fields) && $elementName != 'submit'){
                        $form->remove($elementName);
                    }else{
                        $elementsToValidate[] = $elementName;
                    }
                }
                break;
            case 'edit':
                $this->setCurrentBreadcrumb($this->translate('Edit'));
                // All fields excludes %_translation && !isset in $translatable_fields
                foreach($form->getElements() as $elementName => $element){
                    if((in_array($elementName, $translatable_fields) || strpos($elementName, '_translation') !== false) && $elementName != 'submit'){
                        $form->remove($elementName);
                    }else{
                        $elementsToValidate[] = $elementName;
                    }
                }
                break;
            case 'edit-translation':
                $this->setCurrentBreadcrumb($this->translate('Edit translation'));
                // All fields excludes !isset in $translatable_fields
                foreach($form->getElements() as $elementName => $element){
                    if(!in_array($elementName, $translatable_fields) && $elementName != 'submit'){
                        $form->remove($elementName);
                    }else{
                        $elementsToValidate[] = $elementName;
                    }
                }
                break;
        }
        $form->setValidationGroup($elementsToValidate);

        // update $entity;
        $entity = $_entity;

        // check acl locale and id_territory
        $user_acl_data = $this->getEntityManager()->getRepository('Admin\Entity\User')->getTerritoriesAndLocales($user);
        if(isset($complete_locale)){ // @Todo: case of edit wihout locale
            if(!in_array($complete_locale, $user_acl_data['locales'])){
                throw new \Zend\Mvc\Exception\InvalidArgumentException($this->translate('You are not authorized to see this page'));
            }
        }

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
