<?php

namespace Ices\Tool\Helper;

class ValidateHelper
{
    public function createValidate($columns)
    {
        $rules = new \stdClass();
        foreach ($columns as $col) {
            if (!isset($col['fillable'])) {
                continue;
            }

            $this->makeRequireRules($rules, $col);
            $this->makeMinMaxRules($rules, $col);
            $this->makeLengthRules($rules, $col);
            $this->makeTypeRules($rules, $col);
        }

        return json_encode($rules);
    }

    public function makeRequireRules(&$rules, $col)
    {
        if (!empty($col['validation']['required'])) {
            $rule           = new \stdClass();
            $rule->required = true;
//            $rule->message  = 'Please input ' . $col['display'];
            $rule->trigger  = ['change', 'blur'];

            $rules->{$col['name']}[] = $rule;
        }
    }

    public function makeMinMaxRules(&$rules, $col)
    {
        if (!empty($col['validation']['min']) || !empty($col['validation']['max'])) {
            $rule = new \stdClass();

            if (!empty($col['validation']['min'])) {
                $rule->min = (int)$col['validation']['min'];
            }

            if (empty($col['validation']['min'])) {
                $rule->max = (int)$col['validation']['max'];
            }

            $rule->trigger           = ['change', 'blur'];
            $rules->{$col['name']}[] = $rule;
        }
    }

    public function makeLengthRules(&$rules, $col)
    {
        if (!empty($col['validation']['len'])) {
            $rule          = new \stdClass();
            $rule->len     = (int)$col['validation']['len'];
            $rule->trigger = ['change', 'blur'];

            $rules->{$col['name']}[] = $rule;
        }
    }

    public function makeTypeRules(&$rules, $col)
    {
        if (!empty($col['validation']['type'])) {
            if(in_array($col['validation']['type'], ['boolean', 'date'])) {
                return;
            }
            $rule          = new \stdClass();
            $rule->type    = $col['validation']['type'];
            $rule->trigger = ['change', 'blur'];

            $rules->{$col['name']}[] = $rule;
        }
    }
}
