<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProductCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProductCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings('product', 'products');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'category_id',
            'label' => 'Category Filter',
            'ajax' => true,
        ],
            function() {
                return Category:: whereIn('id' , Product::select('category_id')->distinct()->get()->pluck('category_id')->toArray())->get()->pluck('name' , 'id')->toArray();
            },
            function($value) {

//            dd($value);
                $this->crud->addClause('where', 'category_id', $value);
            });

        CRUD::addColumns(['name', 'description']); // add multiple columns, at the end of the stack
        CRUD::addColumn([
            'name'           => 'price',
            'type'           => 'number',
            'label'          => 'Price',
            'visibleInTable' => false,
            'visibleInModal' => true,
        ]);
        CRUD::addColumn([
            'label'          => 'Category', // Table column heading
            'type'           => 'relationship',
            'name'           => 'category_id', // the column that contains the ID of that connected entity;
            'entity'         => 'category', // the method that defines the relationship in your Model
            'attribute'      => 'name', // foreign key attribute that is shown to user
            'visibleInTable' => true,
            'visibleInModal' => true,
        ]);
        CRUD::addColumn([
            'label'          => 'Brand', // Table column heading
            'type'           => 'relationship',
            'name'           => 'brand_id', // the column that contains the ID of that connected entity;
            'entity'         => 'brand', // the method that defines the relationship in your Model
            'attribute'      => 'name', // foreign key attribute that is shown to user
            'visibleInTable' => true,
            'visibleInModal' => true,
        ]);
//        CRUD::setFromDb(); // columns

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProductRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name',
            'label' => 'Name',
            'type'  => 'text',
            'tab'   => 'Primary Info',

            // optional
            //'prefix' => '',
            //'suffix' => '',
            //'default'    => 'some value', // default value
            //'hint'       => 'Some hint text', // helpful text, show up after input
            //'attributes' => [
            //'placeholder' => 'Some text when empty',
            //'class' => 'form-control some-class'
            //], // extra HTML attributes and values your input might need
            //'wrapperAttributes' => [
            //'class' => 'form-group col-md-12'
            //], // extra HTML attributes for the field wrapper - mostly for resizing fields
            //'readonly'=>'readonly',
        ]);
        CRUD::addField([   // Textarea
            'name'  => 'description',
            'label' => 'Description',
            'type'  => 'textarea',
            'tab'   => 'Primary Info',
        ]);

        CRUD::addField([   // Wysiwyg
            'name'  => 'details',
            'label' => 'Details',
            'type'  => 'wysiwyg',
            'tab'   => 'Primary Info',
        ]);
        $this->crud->addField([
            'label' => 'Category',
            'type' => 'relationship',
            'name' => 'category_id',
            'entity' => 'category',
            'attribute' => 'name',
            /*'inline_create' => true,*/
            'ajax' => true,
            'tab'   => 'Primary Info',
        ]);

        $this->crud->addField([
            'label' => 'Brand',
            'type' => 'relationship',
            'name' => 'brand_id',
            'entity' => 'brand',
            'attribute' => 'name',
            /*'inline_create' => true,*/
            'ajax' => true,
            'tab'   => 'Primary Info',
        ]);

        $this->crud->addField([
            'label' => 'Sub Category',
            'type' => 'relationship',
            'name' => 'sub_category_id',
            'entity' => 'subCategory',
            'attribute' => 'name',
            /*'inline_create' => true,*/
            'ajax' => true,
            'tab'   => 'Primary Info',
        ]);

        CRUD::addField([ // Table
            'name'            => 'features',
            'label'           => 'Features',
            'type'            => 'table',
            'entity_singular' => 'feature', // used on the "Add X" button
            'columns'         => [
                'name' => 'Feature',
                'desc' => 'Value',
            ],
            'max' => 25, // maximum rows allowed in the table
            'min' => 0, // minimum rows allowed in the table
            'tab' => 'Primary Info',
        ]);

        CRUD::addField([ // Table
            'name'            => 'extra_features',
            'label'           => 'Extra Features',
            'type'            => 'table',
            'entity_singular' => 'extra feature', // used on the "Add X" button
            'columns'         => [
                'name' => 'Feature',
                'desc' => 'Value',
            ],
            'fake' => true,
            'max'  => 25, // maximum rows allowed in the table
            'min'  => 0, // minimum rows allowed in the table
            'tab'  => 'Primary Info',
        ]);



        CRUD::addField([   // Number
            'name'  => 'price',
            'label' => 'Price',
            'type'  => 'number',
            // optionals
            // 'attributes' => ["step" => "any"], // allow decimals
            'prefix' => '$',
            'suffix' => '.00',
            // 'wrapperAttributes' => [
            //    'class' => 'form-group col-md-6'
            //  ], // extra HTML attributes for the field wrapper - mostly for resizing fields
            'tab' => 'Basic Info',
        ]);


        $this->crud->setOperationSetting('contentClass', 'col-md-12');

//        CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function fetchCategory()
    {
        return $this->fetch(Category::class);
    }
    public function fetchBrand()
    {
        return $this->fetch(Brand::class);
    }
    public function fetchSubCategory()
    {
        return $this->fetch(SubCategory::class);
    }
}
