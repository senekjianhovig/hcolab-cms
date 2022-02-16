<?php
return [
    'foreign_keys' => [
        // [ 'type' => 'double|single', 'name' => 'field_name' , 'format' => 'table_name:key:label , separator, table_name:key:label' ],
        [ 'type' => 'single', 'name' => 'category_id' , 'format' => 'categories:id:label' ],
        [ 'type' => 'single', 'name' => 'country_id' , 'format' => 'countries:id:name' ]
    ],
    'menu' => [
        [ 'type' => 'group label' , 'label' => 'Main' ],
        ['type' => 'static' , 'link_to' => 'https://example.com' , 'label' => 'Example' ,'target' => '_blanc' ],
        [ 'type' => 'page' , 'link_to' => 'ExamplePage' ]
    ]
];