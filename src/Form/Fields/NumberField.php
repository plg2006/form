<?php

namespace Plg\Form\Fields;

require_once dirname( __DIR__ ) . '/Field.php';

use Plg\Form\Field as Field;

class NumberField extends Field
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
        return 'number';
    }

    function render()
    {
        return '<input type="number" id="' . $this->getId() . '" name="' . $this->getName() . '" value="' . $this->getValue() . '">';
    }

    function check( $value )
    {
        if ( ! filter_var( $value, FILTER_VALIDATE_FLOAT ) ) {
            $this->setError( 'Value is not a number' );
            return false;
        }
        
        $value = filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT );
        $this->setValue( $value );
        return true;
    }

}