<?php

namespace Plg\Form;

abstract class Field
{
    use \Plg\Util\VariableObject;

    abstract function getType();
    abstract function render();
    abstract function check( $value );

    public function renderLabel()
    {
        if ( ! $this->issetId() || ! $this->issetLabel() ) {
            return '';
        }

        return '<label for="' . $this->getId() . '">' . $this->getLabel() . '</label>';
    }

    public function __toString()
    {
        return $this->render();
    }
}