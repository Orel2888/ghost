<?php

namespace App\Ghost\Apanel\Repository;

use Carbon\Carbon;

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

    /**
     * Eloquent build query for filter
     *
     * @param \Eloquent $builder
     * @param array $input
     */
    public function eloquentFilter(\Eloquent $builder, array $input): \Eloquent
    {
        $exceptFiledNameForValue = ['', 'f_compare_', 'f_sorting', -1];

        /**
         * [
         *      field_name,
         *      compare <>=,
         *      value
         * ]
         */
        $fieldValue = [];

        // Generate a fields for query
        foreach ($input as $filedName => $fieldValue) {
            if ($fieldValue != '' && $fieldValue != -1 && !preg_match('/'. implode('|', $exceptFiledNameForValue) .'/')) {

                $compareFieldName = 'f_compare_'. str_replace('f_', '', $filedName);

                $fieldValue[] = [
                    $filedName,
                    isset($input[$compareFieldName]) ? $input[$compareFieldName] : '=',
                    $fieldValue
                ];
            }
        }

        // Build query
        foreach ($fieldValue as $value) {

            switch ($value) {
                case 'like':
                    $value[2] = '%'. $value[2]. '%';
                break;
            }

            $builder->where(str_replace('f_', '', $value[0]), $value[1], $value[2]);
        }

        // Sorting
        if (isset($input['f_sorting_column'])) {
            $builder->orderBy($input['f_sorting_column'], $input['f_sorting_by']);
        }
    }

    public function formFilter($scheme, $inputs)
    {
        $tplData = [
            'scheme'    => $scheme,
            'inputs'    => $inputs
        ];

        if (isset($scheme['period_date'])) {
            $tplData['date_current']  = Carbon::now()->hour(23)->minute(59)->second(59)->format('d-m-Y H:i:s');
            $tplData['date_from'] = Carbon::now()->subDay(7)->hour(0)->minute(0)->second(0)->format('d-m-Y H:i:s');
        }

        return view('apanel.elements.form_filter', $tplData)->render();
    }

    public function confirmAction($message, $urlUp, $urlCancel, $attributes = [])
    {
        return view('apanel.elements.confirm', compact('message', 'urlUp', 'urlCancel', 'attributes'));
    }
}
