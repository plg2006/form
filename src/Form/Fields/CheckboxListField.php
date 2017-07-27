<?php

namespace Plg\Form\Fields;

require_once dirname( __DIR__ ) . '/Field.php';

use Plg\Form\Field as Field;

class CheckboxListField extends Field
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
        return 'checkbox_list';
    }

    function render()
    {
        $name = $this->getName();
        $checked = $this->getValue();

        return implode( '', array_map( function( $input ) use ( $name, $checked ) {
            $html = '';
            $id = false;

            if ( isset( $input['label'] ) ) {
                $id = 'checkbox_' . $name . '_' . $input['value'];
                $html .= '<label for="' . $id . '">';
            }

            $html .= '<input type="checkbox"' .
                ( $id ? 'id="' . $id . '"' : '') .'
                name="' . $name . '[]"
                value="' . $input['value'] . '"' .
                ( $input['value'] == $checked ? ' checked' : '' ) .
                '>';
            
            if ( isset( $input['label'] ) ) {
                $html .= $input['label'] . '</label>';
            }

            return $html;
        }, (array) $this->getInputs() ) );
        
    }

    function check( $value )
    {
        $values = array_map( function( $input ) {
            return $input['value'];
        }, $this->getInputs() );

        if ( in_array( $value, $values ) ) {
            $this->setValue( $value );
        }

        return true;
    }

}