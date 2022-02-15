<?php
return [
'foreign_keys' => [
    // [ 'type' => 'double|single', 'name' => 'field_name' , 'format' => 'table_name:key:label , separator, table_name:key:label' ],
    [ 'type' => 'single', 'name' => 'category_id' , 'format' => 'categories:id:label' ],
    [ 'type' => 'single', 'name' => 'country_id' , 'format' => 'countries:id:name' ]
],

'menu' => [
    'group' => ['type' => "group label", 'label' => "Product Structure"],
    'categories' => ['repo' =>  "CategoryPage", 'label' => "Categories", 'key' => "categories", 'type' => "link", 'icon' => ""],
    'subcategories' => ['repo' =>  "SubcategoryPage", 'label' => "Subcategories", 'key' => "subcategories", 'type' => "link", 'icon' => ""],
    'products' => ['repo' =>  "ProductPage", 'label' => "Products", 'key' => "products", 'type' => "link", 'icon' => ""],
    'countries' => ['repo' =>  "CountryPage", 'label' => "Countries", 'key' => "countries", 'type' => "link", 'icon' => ""],
    'users' => ['repo' =>  "UserPage", 'label' => "Users", 'key' => "users", 'type' => "link", 'icon' => ""],
    'designers' => ['repo' =>  "DesignerPage", 'label' => "Designers", 'key' => "designers", 'type' => "link", 'icon' => ""],
    'experience' => ['repo' =>  "ExperiencePage", 'label' => "Experience", 'key' => "experience", 'type' => "link", 'icon' => ""],
]
];