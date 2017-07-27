<?php

namespace Plg;

class Form {

    use \Plg\Util\VariableObject;

    private static $instances = null;
    private static $activeForm = null;

    private $fields = [];
    
    public function add()
    {
        $args = func_get_args();

        if ( ! count( $args ) ) {
            throw new \Exception( 'error' );
        }

        if ( $args[0] instanceof Field ) {
            $this->fields[] = $field;
            return $field;
        }

        $type = array_shift( $args );

        $field_class = str_replace( ' ', '', ucfirst( str_replace( '_', ' ', $type ) ) ) . 'Field';
        $refClass = new \ReflectionClass( 'Plg\\Form\\Fields\\' . $field_class );
        $field = $refClass->newInstanceArgs( $args );

        $this->fields[] = $field;
        return $field;
    }

    public function getCrsfToken()
    {
        if ( ! isset( $_SESSION['plg_form_crsf_tokens'] ) || ! isset( $_SESSION['plg_form_crsf_tokens'][$this->getAction()] ) ) {
            return false;
        }
        
        return $_SESSION['plg_form_crsf_tokens'][$this->getAction()];
    }

    public function render( $html )
    {
        if ( ! isset( $_SESSION['plg_form_crsf_tokens'] ) ) {
            $_SESSION['plg_form_crsf_tokens'] = [];
        }

        $_SESSION['plg_form_crsf_tokens'][$this->getAction()] = md5(rand());

        $form = '<form method="post" action="">';
        $form .= '<input type="hidden" name="token" value="' . $this->getCrsfToken() . '">';

        $html = str_replace( '{{form}}', $form, $html );
        $html = str_replace( '{{/form}}', '</form>', $html );

        foreach ( $this->fields as $field ) {
            $html = str_replace( '{{label:' . $field->getName() . '}}', $field->renderLabel(), $html );
            $html = str_replace( '{{field:' . $field->getName() . '}}', $field->render(), $html );
            $html = str_replace( '{{error:' . $field->getName() . '}}', $field->getError(), $html );
        }

        return $html;
    }

    public function getField( $name )
    {
        foreach( $this->fields as $field ) {
            if ( $name == $field->getName() ) {
                return $field;
            }
        }

        return null;
    }

    public function check()
    {
        if ( ! isset( $_SESSION['plg_form_crsf_tokens'][$this->getAction()] ) || $_SESSION['plg_form_crsf_tokens'][$this->getAction()] !== $_POST['token'] ) {
            return false;
        }

        if ( ! $this->issetCheck() ) {
            return true;
        }

        $checkCb = $this->getCheck();
        return $checkCb($this);
    }

    public function getValues()
    {
        return array_reduce( $this->fields, function( $carry, $field ) {
            $carry[$field->getName()] = $field->getValue();
            return $carry;
        } );
    }

    public function setValues( $values )
    {
        foreach ( $values as $name => $value ) {
            if ( isset( $this->fields[$name] ) ) {
                $this->fields[$name]->setValue( $value );
            }
        }
    }

    public function checkValues( $values )
    {
        $check = true;

        foreach ( $values as $name => $value ) {
            $field = $this->getField( $name );

            if ( $field ) {
                $check &= $field->check( $value );
            }
        }

        return $check;
    }

    public function submit()
    {
        if ( ! $this->issetSubmit() ) {
            return true;
        }

        $submitCb = $this->getSubmit();
        return $submitCb($this);
    }
    
    public static function create( $formname )
    {
        $form = new Form();
        $form->setAction( $formname );
        self::register( $form );
        return $form;
    }
    
    public static function register( Form $form )
    {
        if ( ! self::$instances ) {
            self::$instances = [];
        }

        if ( ! $form->getAction() ) {
            throw new \Exception( 'error' );
        }

        if ( ! isset( $_SESSION['plg_form_crsf_tokens'] ) ) {
            $_SESSION['plg_form_crsf_tokens'] = [];
        }

        if ( ! isset( $_SESSION['plg_form_crsf_tokens'][$form->getAction()] ) ) {
            $_SESSION['plg_form_crsf_tokens'][$form->getAction()] = md5(rand());
        }

        self::$instances[$form->getAction()] = $form;
    }

    public static function get( $action )
    {
        foreach ( self::$instances as $form ) {
            if ( $action == $form->getAction() ) {
                return $form;
            }
        }
        
        return null;
    }

    public static function checkRequest()
    {
        if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_SESSION['plg_form_crsf_tokens'] ) || ! isset( $_POST['token'] ) ) {
            return false;
        }

        foreach ( self::$instances as $form ) {
            if ( ! isset( $_SESSION['plg_form_crsf_tokens'][$form->getAction()] ) ||
                $_SESSION['plg_form_crsf_tokens'][$form->getAction()] != $_POST['token'] ||
                ! $form->check() ) {
                continue;
            }

            unset( $_SESSION['plg_form_crsf_tokens'][$form->getAction()] );

            if ( ! $form->checkValues( $_POST ) ) {
                continue;
            }

            $form->submit();
            return true;
        }

        return false;
    }
}