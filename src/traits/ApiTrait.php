<?php

namespace hcolab\cms\traits;

trait ApiTrait
{

    public function responseError($code , $title = "" ,$message = "" , $status_code = 400){
        return response([
            'code' => $code,
            'type' => 'NOTIFICATION',
            'error' => [
                'title' => $title,
                'message' => $message
            ]
            ] , $status_code);
    }

    public function responseValidationError($code, $data ,$status_code = 400){

       if(request()->has('validations_response') && request()->input('validations_response') == 'array'){
            $errors = [];

            foreach($data->toArray() as $key => $value){
                $errors [] = [
                    'field' => $key,
                    'validations' => $value 
                ];
            }

            return response([
                'code' => $code,
                'type' => 'VALIDATION',
                'error' => $errors
            ] , $status_code);
        }

        return response([
            'code' => $code,
            'type' => 'VALIDATION',
            'error' => collect($data)->map(function($item){
                return $item[0];
            })->toArray()
        ] , $status_code);
    }

    public function responseSuccess($code , $title = "" ,$message = "" , $status_code = 200){

        return response([
            'code' => $code,
            'type' => 'NOTIFICATION',
            'success' => [
                'title' => $title,
                'message' => $message
            ]
        ] , $status_code);
    }

    public function responseData($code , $data , $status_code = 200){

        return response([
            'code' => $code,
            'data' => $data,
        ] , $status_code);

    }

    /**
     * Execute SQL query and populate field options for select/checkbox fields
     * 
     * @param array $field Field configuration array with 'type', 'query', etc.
     * @return array Array of options with 'value' and 'display' keys, or empty array on error
     */
    public function populateFieldOptionsFromQuery($field)
    {
        // Only process select/checkbox fields with a query
        if (!in_array($field['type'] ?? '', ['select', 'checkbox']) || empty($field['query'] ?? '')) {
            return [];
        }

        try {
            $queryResults = \Illuminate\Support\Facades\DB::select($field['query']);
            $options = [];
            
            foreach ($queryResults as $row) {
                // Convert object to array
                $rowArray = (array) $row;
                // Get first two columns: value (first) and display (second)
                $values = array_values($rowArray);
                
                $options[] = [
                    "display" => $values[1] ?? 'Error', // Second column is display
                    "value" => (string) $values[0] // First column is value
                ];
            }
            
            return $options;
        } catch (\Throwable $th) {
            // If query fails, return empty array
            return [];
        }
    }

}