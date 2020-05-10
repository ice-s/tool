<?php

namespace Ices\Tool\Helper;

class FormHelper
{
    public function createForm($columns, $isEdit = false)
    {
        $formHtml = '';
        foreach ($columns as $col) {
            if(!isset($col['fillable']) && !isset($col['primary'])) {
                continue;
            }

            if($isEdit == false && isset($col['primary'])) {
                continue;
            }
            $disable = false;
            if($isEdit == true && isset($col['primary'])) {
                $disable = true;
            }

            switch ($col['type']) {
                case 'bigint':
                    $formHtml .= $this->createVueInput($col['display'], $col['name'], $disable);
                    break;
                case 'integer':
                    $formHtml .= $this->createVueInput($col['display'], $col['name'], $disable);
                    break;
                case 'boolean':
                    $formHtml .= $this->createVueBoolean($col['display'], $col['name'], $disable);
                    break;
                case 'string':
                    $formHtml .= $this->createVueInput($col['display'], $col['name'], $disable);
                    break;
                case 'float':
                    $formHtml .= $this->createVueInput($col['display'], $col['name'], $disable);
                    break;
                case 'date':
                    $formHtml .= $this->createVueDatePicker($col['display'], $col['name'], $disable);
                    break;
                case 'datetime':
                    $formHtml .= $this->createVueDatetimePicker($col['display'], $col['name'], $disable);
                    break;
                case 'text':
                    $formHtml .= $this->createVueTextArea($col['display'], $col['name'], $disable);
                    break;
            }
        }

        return $formHtml;
    }

    public function createVueInput($label, $name, $disable = false)
    {
        $disable ? $disabled = 'disabled' : $disabled = '';
        return <<<EOF
                            <a-form-model-item label="$label" prop="$name" ref="$name">
                                <a-input v-model="form.$name" $disabled/>
                            </a-form-model-item>

EOF;
    }

    public function createVueTextArea($label, $name, $disable = false)
    {
        $disable ? $disabled = 'disabled' : $disabled = '';
        return <<<EOF
                            <a-form-model-item label="$label" prop="$name">
                                <a-input v-model="form.$name" type="textarea" $disabled/>
                            </a-form-model-item>

EOF;

    }

    public function createVueCheckbox($label, $name, $data = [])
    {
        return <<<EOF
                            <a-form-model-item label="$label" prop="$name">
                                <a-checkbox-group v-model="form.$name">
                                    <a-checkbox value="1" name="$name">Online</a-checkbox>
                                    <a-checkbox value="2" name="$name">Promotion</a-checkbox>
                                    <a-checkbox value="3" name="$name">Offline</a-checkbox>
                                </a-checkbox-group>
                            </a-form-model-item>

EOF;

    }

    public function createVueSelect($label, $name, $data = [])
    {
        return <<<EOF
                            <a-form-model-item label="$label" prop="$name">
                                <a-select v-model="form.$name" placeholder="please select your zone">
                                    <a-select-option value="shanghai">Zone one</a-select-option>
                                    <a-select-option value="beijing">Zone two</a-select-option>
                                </a-select>
                            </a-form-model-item>

EOF;
    }

    public function createVueDatePicker($label, $name, $disable = false)
    {
        $disable ? $disabled = 'disabled' : $disabled = '';
        return <<<EOF
                            <a-form-model-item label="$label" prop="$name">
                                <a-date-picker
                                    $disabled
                                    v-model="form.$name"
                                    type="date"
                                    placeholder="Pick a date"
                                    style="width: 100%;"
                                />
                            </a-form-model-item>

EOF;
    }

    public function createVueDatetimePicker($label, $name, $disable = false)
    {
        $disable ? $disabled = 'disabled' : $disabled = '';
        return <<<EOF
                            <a-form-model-item label="$label" prop="$name">
                                <a-date-picker
                                    $disabled
                                    v-model="form.$name"
                                    show-time
                                    type="date"
                                    placeholder="Pick a datetime"
                                    style="width: 100%;"
                                />
                            </a-form-model-item>

EOF;
    }

    public function createVueBoolean($label, $name, $disable = false)
    {
        $disable ? $disabled = 'disabled' : $disabled = '';
        return <<<EOF
                            <a-form-model-item label="$label" prop="$name">
                                <a-radio-group v-model="form.$name" $disabled>
                                    <a-radio :value="1">Yes</a-radio>
                                    <a-radio :value="0">No</a-radio>
                                </a-radio-group>
                            </a-form-model-item>

EOF;
    }


    public function convertMoment($columns)
    {
        $js = '';
        foreach ($columns as $col) {
            if (!isset($col['fillable'])) {
                continue;
            }
            $colName = $col['name'];
            if ($col['type'] == 'date' || $col['type'] == 'datetime') {
                $js .= "                    app.form.$colName = app.form.$colName? moment(app.form.$colName) : null;\n";
            }
        }
        return $js;
    }

    public function convertDataSubmit($columns, $isEdit = false)
    {
        $js = '';
        foreach ($columns as $col) {
            if (!isset($col['fillable'])) {
                continue;
            }

            $colName = $col['name'];

            if ($col['type'] == 'date') {
                $js .= "data.$colName = data.$colName? data.$colName.format('YYYY-MM-DD') : null;\n";
            }

            if ($col['type'] == 'datetime') {
                $js .= "                        data.$colName = data.$colName? data.$colName.format('YYYY-MM-DD HH:mm:ss') : null;\n";
            }
        }

        foreach ($columns as $col) {
            if($isEdit && isset($col['primary'])) {
                $js .= '                        delete data.'.$col['name'].';';
            }
        }


        return $js;
    }
}
