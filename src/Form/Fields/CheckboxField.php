<?php

namespace Plg\Form\Fields;

require_once dirname( __DIR__ ) . '/Field.php';

use Plg\Form\Field as Field;

class CheckboxField extends Field
{
    public function __construct( $name, $label = null, $value = null )
    {
        $this->setName( $name );

        if ( $label ) {
            $this->setLabel( $label );
        }

        if ( $value ) {
            $this->setValue( $value );
        }
    }

    function getType()
    {
        return 'checkbox';
    }

    function render()
    {
        return '<input type="checkbox"
            name="' . $this->getName() . '"' .
            ( $this->issetInputValue() ? 'value="' . $this->getInputValue() . '"' : '' ) .
            ( $this->getValue() ? ' checked' : '' ) .
            '>';
    }

    function check( $value )
    {
        $this->setValue( $value == ( $this->issetInputValue() ? $this->getInputValue() : 'on' ) );
        return true;
    }

}