<?php

namespace Plg\Form\Fields;

require_once dirname( __DIR__ ) . '/Field.php';

use Plg\Form\Field as Field;

class SelectField extends Field
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
        return 'select';
    }

    function render()
    {
        $value = $this->getValue();
        $options = implode( '', array_map( function( $option ) use ( $value ) {
            return '<option
                value="' . $option['value']. '"' .
                ( isset( $option['hidden'] ) && $option['hidden'] ? ' hidden' : '' ) .
                ( isset( $option['disabled'] ) && $option['disabled'] ? ' disabled' : '' ) .
                ( $value == $option['value'] ? ' selected' : '' ) .
                '>' . $option['name']. '</option>';
        }, (array) $this->getOptions() ) );

        return '<select
            id="' . $this->getId() . '"
            name="' . $this->getName() . '"
            >' . $options . '</select>';
    }

    function check( $value )
    {
        $this->setValue( $value );
        return true;
    }

}