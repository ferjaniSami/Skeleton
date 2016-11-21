<?php

namespace Admin\View\Helper\Form;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\LabelAwareInterface;

use Zend\Form\View\Helper\FormLabel as ZendFormLabel;

class FormLabel extends ZendFormLabel
{

    /**
     * Generate a form label, optionally with content
     *
     * Always generates a "for" statement, as we cannot assume the form input
     * will be provided in the $labelContent.
     *
     * @param  ElementInterface $element
     * @param  null|string      $labelContent
     * @param  string           $position
     * @throws Exception\DomainException
     * @return string|FormLabel
     */
    public function __invoke(ElementInterface $element = null, $labelContent = null, $position = null)
    {
        if (!$element) {
            return $this;
        }

        $openTag = $this->openTag($element);
        $label   = '';
        if ($labelContent === null || $position !== null) {
            $label = $element->getLabel();
            if (empty($label)) {
                throw new Exception\DomainException(sprintf(
                    '%s expects either label content as the second argument, ' .
                        'or that the element provided has a label attribute; neither found',
                    __METHOD__
                ));
            }

            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }

            if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
                $escapeHtmlHelper = $this->getEscapeHtmlHelper();
                $label = $escapeHtmlHelper($label);
            }
        }

        if ($label && $labelContent) {
            switch ($position) {
                case self::APPEND:
                    $labelContent .= $label;
                    break;
                case self::PREPEND:
                default:
                    $labelContent = $label . $labelContent;
                    break;
            }
        }

        if ($label && null === $labelContent) {
            $labelContent = $label;
        }

        // Set $required to a default of true | existing elements required-value
        $required = ($element->hasAttribute('required') ? $element->getAttribute('required') : false);

        if ($required) {
            $labelContent = sprintf(
                '%s <span data-toggle="tooltip" data-placement="top" data-original-title="Required">*</span>',
                $labelContent
            );
        }

        $options = $element->getOptions();
        if(isset($options['description'])){
            $labelContent = sprintf(
                '%s <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" data-original-title="' . $options['description'] . '"></i>',
                $labelContent
            );
        }

        return $openTag . $labelContent . $this->closeTag();
    }
}
