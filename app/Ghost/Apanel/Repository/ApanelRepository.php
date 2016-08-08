<?php

namespace App\Ghost\Apanel\Repository;

class ApanelRepository
{
    public function dbQueryBuilder($builder, array $input = [])
    {
        $fieldsAndValues = [];

        foreach ($input as $filedName => $filedValue) {
            if ($filedValue != '' && strpos($filedName, 'f_compare_') === false && strpos($filedName, 'f_sorting') === false && $filedValue != -1) {

                $compareFieldName = 'f_compare_'. str_replace('f_', '', $filedName);

                $fieldsAndValues[] = [
                    $filedName,
                    isset($input[$compareFieldName]) ? $input[$compareFieldName] : '=',
                    $filedValue
                ];
            }
        }

        foreach ($fieldsAndValues as $itemData) {
            if ($itemData[1] == 'like') {
                $itemData[2] = '%'. $itemData[2] .'%';
            }
            $builder->where(str_replace('f_', '', $itemData[0]), $itemData[1], $itemData[2]);
        }

        if (isset($input['f_sorting_column'])) {
            $builder->orderBy($input['f_sorting_column'], $input['f_sorting_by']);
        }
        
        return $builder;
    }

    public function formFilter($scheme, $inputs)
    {
        $tplData = [
            'scheme'    => $scheme,
            'inputs'    => $inputs
        ];

        return view('apanel.elements.form_filter', $tplData)->render();
    }
}
