<?php

namespace App\ExtendsLaravel\HtmlBuilder;

use Collective\Html\FormBuilder as FormBuilderOrig;

class FormBuilder extends FormBuilderOrig
{
    public function control($labelText, $name, $value = null, $informer = null, $helpBlock = null, $options = [], $type = 'text')
    {
        return sprintf('
        <div class="form-group %s">
            <label>%s</label>
            %s
            %s
        </div>
        ',  $informer ? 'has-'. $informer : '',
            $labelText,
            parent::input($type, $name, $value, $options + ['class' => 'form-control']),
            '<div class="help-block">'. $helpBlock .'</div>');
    }

    public function checkboxControl($labelText, $name, $value = null, $checked = false, $options = [])
    {
        return sprintf('
        <div class="checkbox">
            <label>
                %s %s
            </label>
        </div>
        ', parent::checkbox($name, $value, $checked, $options), $labelText);
    }

    public function buttonType($value, $typeButton = 'default', $options = [])
    {
        return sprintf('
        %s',
            parent::button($value, $options + ['class' => 'btn btn-'. $typeButton, 'type' => 'submit']));
    }

    public function __call($method, $arguments)
    {
        if (strpos($method, 'control') !== false) {
            preg_match('control(.*)', $method, $matches);

            switch ($matches[1]){

                case 'password':
                    call_user_func_array([$this, 'control'], [
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        'password'
                    ]);
                break;

                default:
                    call_user_func_array([$this, 'control'], $arguments);
            }
        }
    }
}