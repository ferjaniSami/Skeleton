<?php

namespace Admin\View\Helper\Form;

use Zend\Form\Element\Button;
use Zend\Form\Element\MonthSelect;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\LabelAwareInterface;

use Zend\Form\View\Helper\FormRow as ZendFormRow;

class FormRow extends ZendFormRow
{

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  null|ElementInterface $element
     * @param  null|string           $labelPosition
     * @param  bool                  $renderErrors
     * @param  string|null           $partial
     * @return string|FormRow
     */
    public function __invoke(ElementInterface $element = null, $labelPosition = null, $renderErrors = null, $partial = null)
    {
        $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
        $classAttributes = $classAttributes . 'form-control';
        $element->setAttribute('class', $classAttributes);

        if($element instanceof LabelAwareInterface){
            $labelAttributes = $element->getLabelAttributes();
            $labelAttributes['class'] = (isset($labelAttributes['class']) ? $labelAttributes['class'] . ' ' : '');
            $labelAttributes['class'] .= 'control-label';
            $element->setLabelAttributes($labelAttributes);
        }

        $inputErrorClass = $this->getInputErrorClass();
        return '<div class="form-group' . (count($element->getMessages()) > 0 && !empty($inputErrorClass) ? ' has-error' : '') . '">' . parent::__invoke($element, $labelPosition, $renderErrors, $partial) . '</div>';
    }
}
