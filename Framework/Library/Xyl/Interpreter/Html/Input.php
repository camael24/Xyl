<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

from('Hoa')

/**
 * \Hoa\Xyl\Interpreter\Html\Concrete
 */
-> import('Xyl.Interpreter.Html.Concrete')

/**
 * \Hoa\Xyl\Element\Executable
 */
-> import('Xyl.Element.Executable')

/**
 * \Hoa\Test\Praspel\Compiler
 */
-> import('Test.Praspel.Compiler');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Input.
 *
 * The <input /> component.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2011 Ivan Enderlin.
 * @license    New BSD License
 */

class Input extends Concrete implements \Hoa\Xyl\Element\Executable {

    /**
     * Extra attributes.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Concrete array
     */
    protected $iAttributes = array(
        'type'      => 'text',
        'name'      => null,
        'value'     => null,
        'autofocus' => null,
        'checked'   => null
    );

    /**
     * Extra attributes mapping.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Concrete array
     */
    protected $attributesMapping = array(
        'type'      => 'type',
        'name'      => 'name',
        'value'     => 'value',
        'autofocus' => 'autofocus',
        'checked'   => 'checked'
    );

    /**
     * Type of input: button, checkbox, color, date, datetime, datetime-local,
     * email, file, hidden, image, month, number, password, radio, range, reset,
     * search, submit, tel, text, time, url and week.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input string
     */
    protected $_type            = null;

    /**
     * Praspel compiler, to interprete the validate attribute.
     *
     * @var \Hoa\Test\Praspel\Compiler object
     */
    protected static $_compiler = null;

    /**
     * Whether the input is valid or not.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input bool
     */
    protected $_validity        = true;



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $out->writeAll(
            '<input' . $this->readAttributesAsString() . ' />'
        );

        return;
    }

    /**
     * Pre-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function preExecute ( ) {

        return;
    }

    /**
     * Post-execute an element.
     *
     * @access  public
     * @return  void
     */
    public function postExecute ( ) {

        $type = strtolower($this->abstract->readAttribute('type'));

        switch($type) {

            case 'button':
            case 'checkbox':
            case 'color':
            case 'date':
            case 'datetime':
            case 'datetime-local':
            case 'email':
            case 'file':
            case 'hidden':
            case 'image':
            case 'month':
            case 'number':
            case 'password':
            case 'radio':
            case 'range':
            case 'reset':
            case 'search':
            case 'submit':
            case 'tel':
            case 'text':
            case 'time':
            case 'url':
            case 'week':
                $this->_type = $type;
              break;

            default:
                $this->_type = 'text';
        }

        return;
    }

    /**
     * Set (or restore) the input value.
     *
     * @access  public
     * @param   string  $value    Value.
     * @return  string
     */
    public function setValue ( $value ) {

        $old = $this->getValue();

        switch($this->getType()) {

            case 'checkbox':
                $this->writeAttribute('checked', 'checked');
              break;

            case 'radio':
                if($value == $this->readAttribute('value'))
                    $this->writeAttribute('checked', 'checked');
                else
                    $this->removeAttribute('checked');
              break;

            default:
                $this->writeAttribute('value', $value);
        }

        return $old;
    }

    /**
     * Get the input value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        $value = $this->readAttribute('value');

        if(ctype_digit($value))
            $value = (int) $value;
        elseif(is_numeric($value))
            $value = (float) $value;

        return $value;
    }

    /**
     * Unset the input value.
     *
     * @access  public
     * @return  void
     */
    public function unsetValue ( ) {

        switch($this->getType()) {

            case 'checkbox':
            case 'radio':
                $this->removeAttribute('checked');
              break;
        }

        return;
    }

    /**
     * Check the input validity.
     *
     * @access  public
     * @param   mixed  $value    Value (if null, will find the value).
     * @return  bool
     */
    public function checkValidity ( $value = null ) {

        $type = $this->getType();

        if('submit' === $type || 'reset' === $type) {

            $this->_validity = false;

            if(   null   === $value
               || $value ==  $this->getValue())
                $this->_validity = true;

            return $this->_validity;
        }

        $validates = array();

        if(true === $this->abstract->attributeExists('validate'))
            $validates['@'] = $this->abstract->readAttribute('validate');

        $validates = array_merge(
            $validates,
            $this->abstract->readCustomAttributes('validate')
        );

        if(empty($validates))
            return true;

        $onerrors = array();

        if(true === $this->abstract->attributeExists('onerror'))
            $onerrors['@'] = $this->abstract->readAttributeAsList('onerror');

        $onerrors = array_merge(
            $onerrors,
            $this->abstract->readCustomAttributesAsList('onerror')
        );

        if(null === $value)
            $value = $this->getValue();
        else
            if(ctype_digit($value))
                $value = (int) $value;
            elseif(is_numeric($value))
                $value = (float) $value;

        if(null === self::$_compiler)
            self::$_compiler = new \Hoa\Test\Praspel\Compiler();

        $this->_validity = true;

        foreach($validates as $name => $realdom) {

            self::$_compiler->compile('@requires i: ' . $realdom . ';');
            $praspel  = self::$_compiler->getRoot();
            $variable = $praspel->getClause('requires')->getVariable('i');
            $decision = false;

            foreach($variable->getDomains() as $domain)
                $decision = $decision || $domain->predicate($value);

            $this->_validity = $this->_validity && $decision;

            if(true === $decision)
                continue;

            if(!isset($onerrors[$name]))
                continue;

            $errors = $this->xpath(
                '//__current_ns:error[@id="' .
                implode('" or @id="', $onerrors[$name]) .
                '"]'
            );

            foreach($errors as $error)
                $this->getConcreteElement($error)->setVisibility(true);
        }

        return $this->_validity;
    }

    /**
     * Whether the input is valid or not.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        return $this->_validity;
    }

    /**
     * Get the input type.
     *
     * @access  public
     * @return  string
     */
    public function getType ( ) {

        return $this->_type;
    }
}

}
