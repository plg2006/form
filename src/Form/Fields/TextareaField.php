<?php

namespace Plg\Form\Fields;

require_once dirname( __DIR__ ) . '/Field.php';

use Plg\Form\Field as Field;

class TextareaField extends Field
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
        return 'textarea';
    }

    function render()
    {
        return '<textarea id="' . $this->getId() . '" name="' . $this->getName() . '">' . $this->getValue() . '</textarea>';
    }

    function check( $value )
    {
        $value = filter_var( $value, FILTER_SANITIZE_STRING );
        $this->setValue( $value );
        return true;
    }

}