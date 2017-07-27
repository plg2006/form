<?php

namespace Plg\Form;

class FormFront
{
    private static $instance = null;

    private $form = null;

    public static function getInstance()
    {
        if ( null == self::$instance ) {
            self::$instance = new FormFront();
        }

        return self::$instance;
    }

    private function __construct() { }

    public function form( $form = null )
    {
        if ( null === $form ) {
            return $this->form;
        }

        $this->form = Form::get( $form );
        return this;
    }

    public function token()
    {
        if ( ! $this->form ) {
            return;
        }

        return '<input type="hidden" name="token" value="' . $this->form->getCrsfToken() . '">';
    }

    public function field( $name )
    {
        if ( ! $this->form ) {
            return;
        }

        return $this->form->getField( $name );
    }

    public function __call( $method, $args )
    {
        if ( ! $this->form || ! count( $args ) ) {
            return;
        }

        $field = $this->form->getField( $args[0] );

        if ( ! $field ) {
            return;
        }

        return call_user_func( [ $field, 'get' . ucfirst( $method ) ] );
    }
}