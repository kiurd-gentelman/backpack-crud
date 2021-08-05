<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\SubCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use function PHPUnit\Framework\isNan;

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

    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings('product', 'products');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([   // Wysiwyg
            'name'  => 'images',
            'label' => 'Image',
            'type'  => 'closure',
            'function' =>
                static function($entry) {
//                    dump($entry->product_images);
                    $html = '';
                    $html .= '<div class="row">';
                    foreach ($entry->product_images as $image){

                        $html .=  '         <div class="col-md-2 col-4">';
                        $html .=  '            <div class="thumbnail">';
                        $html .=  '               <a href="'.route('image-delete',$image->id).'">';
                        $html .=  '                    <img src="'.asset($image->image_path).'" alt="Lights" style="width:30%">';
                        $html .=  '                   <div class="caption">';
                        $html .=  '                       <p class="text-danger">Delete</p>';
                        $html .=  '                  </div>';
                        $html .=  '              </a>';
                        $html .=  '          </div>';
                        $html .=  '     </div>';
                    }
                    $html .= '</div>';
                    return $html;
            }
        ]);
        $this->crud->addFilter([
            'name'  => 'status',
            'type'  => 'select2_multiple',
            'label' => 'Status'
        ], function() {
            return [
                1 => 'In stock',
                2 => 'In provider stock',
                3 => 'Available upon ordering',
                4 => 'Not available',
            ];
        }, function($values ) {
            // if the filter is active
            // $this->crud->addClause('whereIn', 'status', json_decode($values));
        });
        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'category_id',
            'label' => 'Category Filter',
            'ajax' => true,
        ],function() {
                return Category:: whereIn('id' , Product::select('category_id')->distinct()->get()->pluck('category_id')->toArray())->get()->pluck('name' , 'id')->toArray();
            },
            function($value) {
//            dd($value);
                $this->crud->addClause('where', 'category_id', $value);
        });

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'brand_id',
            'label' => 'Brand Filter',
            'ajax' => true,
        ],function() {
            return Brand:: whereIn('id' , Product::select('brand_id')->distinct()->get()->pluck('brand_id')->toArray())->get()->pluck('name' , 'id')->toArray();
        },
            function($value) {
//            dd($value);
                $this->crud->addClause('where', 'brand_id', $value);
            });

        CRUD::addColumns(['name', 'description']);
        CRUD::addColumn([
            'name'           => 'price',
            'type'           => 'number',
            'label'          => 'Price',
            'visibleInTable' => false,
            'visibleInModal' => true,
        ]);
        CRUD::addColumn([
            'label'          => 'Category',
            'type'           => 'relationship',
            'name'           => 'category_id',
            'entity'         => 'category',
            'attribute'      => 'name',
            'visibleInTable' => true,
            'visibleInModal' => true,
        ]);
        CRUD::addColumn([
            'label'          => 'Brand',
            'type'           => 'relationship',
            'name'           => 'brand_id',
            'entity'         => 'brand',
            'attribute'      => 'name',
            'visibleInTable' => true,
            'visibleInModal' => true,
        ]);
        CRUD::addColumn([
            'label'          => 'Sub-category',
            'type'           => 'relationship',
            'name'           => 'sub_category_id',
            'entity'         => 'subCategory',
            'attribute'      => 'name',
            'visibleInTable' => true,
            'visibleInModal' => true,
        ]);
        CRUD::enableDetailsRow();
        CRUD::setDetailsRowView('vendor.backpack.base.test' , ['entry']);
        CRUD::enableExportButtons();
//        CRUD::setFromDb(); // columns

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProductRequest::class);

        CRUD::addField([   // Wysiwyg
            'name'  => 'name',
            'label' => 'Name',
            'type'  => 'text',
            'tab'   => 'Primary Info',
        ]);

        CRUD::addField([   // Browse multiple
            'name'          => 'images',
            'label'         => 'Images',
            'type'          => 'browse_multiple',
            'tab'   => 'Primary Info',
            'upload' => true,
            'multiple'   => true, // enable/disable the multiple selection functionality
            'sortable'   => false, // enable/disable the reordering with drag&drop
            // 'mime_types' => null, // visible mime prefixes; ex. ['image'] or ['application/pdf']

        ]);

        $this->crud->addField([    // SELECT2
            'label'         => 'Category',
            'type'          => 'select',
            'name'          => 'category_id',
            'entity'        => 'category',
            'attribute'     => 'name',
            'tab'   => 'Primary Info'
        ]);
        $this->crud->addField([ // select2_from_ajax: 1-n relationship
            'label'                => "Sub Category", // Table column heading
            'type'                 => 'select2_from_ajax',
            'name'                 => 'sub_category_id', // the column that contains the ID of that connected entity;
            'entity'               => 'subCategory', // the method that defines the relationship in your Model
            'attribute'            => 'name', // foreign key attribute that is shown to user
            'data_source'          => url('api/article'), // url to controller search function (with /{id} should return model)
            'placeholder'          => 'Select an article', // placeholder for the select
            'minimum_input_length' => 0, // minimum characters to type before querying results
            'dependencies'         => ['category'], // when a dependency changes, this select2 is reset to null
            'include_all_form_fields' => true,
            'method'                    => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
            'tab'   => 'Primary Info'
        ]);


//        CRUD::addField([ // Text
//          // two interconnected entities
//            'label'             => 'Category',
//            'field_unique_name' => 'category_sub_category',
//            'type'              => 'checklist_dependency',
//            'name'              => ['category', 'subCategory'], // the methods that define the relationship in your Models
//            'subfields'         => [
//                'primary' => [
//                    'label'            => 'Category',
//                    'name'             => 'category', // the method that defines the relationship in your Model
//                    'entity'           => 'category', // the method that defines the relationship in your Model
//                    'entity_secondary' => 'subCategory', // the method that defines the relationship in your Model
//                    'attribute'        => 'name', // foreign key attribute that is shown to user
//                    'model'            => Category::class, // foreign key model
//                    'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
//                    'number_columns'   => 3, //can be 1,2,3,4,6
//                ],
//                'secondary' => [
//                    'label'          => 'Sub Category',
//                    'name'           => 'subCategory', // the method that defines the relationship in your Model
//                    'entity'         => 'subCategory', // the method that defines the relationship in your Model
//                    'entity_primary' => 'category', // the method that defines the relationship in your Model
//                    'attribute'      => 'name', // foreign key attribute that is shown to user
//                    'model'          => SubCategory::class, // foreign key model
//                    'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
//                    'number_columns' => 3, //can be 1,2,3,4,6
//                ],
//            ],
//
//        ]);
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
//        $this->crud->addField([
//            'label' => 'Category',
//            'type' => 'relationship',
//            'name' => 'category_id',
//            'entity' => 'category',
//            'attribute' => 'name',
//            /*'inline_create' => true,*/
//            'ajax' => true,
//            'tab'   => 'Primary Info',
//        ]);

        $this->crud->addField([
            'label'          => 'Brand',
            'type'           => 'relationship',
            'name'           => 'brand_id',
            'entity'         => 'brand',
            'attribute'      => 'name',
            'visibleInTable' => true,
            'visibleInModal' => true,
            /*'inline_create' => true,*/
            'ajax'          => true,
        ]);

//        $this->crud->addField([
//            'label' => 'Sub Category',
//            'type' => 'relationship',
//            'name' => 'sub_category_id',
//            'entity' => 'subCategory',
//            'attribute' => 'name',
//            /*'inline_create' => true,*/
//            'ajax' => true,
//            'tab'   => 'Primary Info',
//        ]);

        CRUD::addField([ // Table
            'name'            => 'features',
            'label'           => 'Features',
            'type'            => 'table',
            'entity_singular' => 'feature', // used on the "Add X" button
            'columns'         => [
                'name' => 'Feature' ,
                'desc' => 'Value',
            ],
            'max' => 50,
            'min' => 0,
            'tab' => 'Primary Info',
        ]);

//        CRUD::addField([ // Table
//            'name'            => 'extra_features',
//            'label'           => 'Extra Features',
//            'type'            => 'table',
//            'entity_singular' => 'extra feature',
//            'columns'         => [
//                'name' => 'Feature',
//                'desc' => 'Value',
//            ],
//            'fake' => true,
//            'max'  => 25, // maximum rows allowed in the table
//            'min'  => 0, // minimum rows allowed in the table
//            'tab'  => 'Primary Info',
//        ]);



        CRUD::addField([
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
        CRUD::addField([ // Table
            'name'            => 'variant',
            'label'           => 'Variant',
            'type'            => 'table',
            'entity_singular' => 'variant',
            'columns'         => [
                'name' => 'Variant',
                'desc' => 'Value',
                'price' => 'Price',

            ],
            'max'  => 25,
            'min'  => 0,
            'tab'  => 'Basic Info',
        ]);


        $this->crud->setOperationSetting('contentClass', 'col-md-12');

//        CRUD::setFromDb(); // fields
    }

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

    public function show($id)
    {
        $this->crud->hasAccessOrFail('show');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $setFromDb = $this->crud->get('show.setFromDb');

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.preview').' '.$this->crud->entity_name;

        // set columns from db

        if ($setFromDb) {
            $this->crud->addColumn([
                'label' => 'Category',
                'type' => 'relationship',
                'name' => 'category_id',
                'entity' => 'category',
                'attribute' => 'name',
                /*'inline_create' => true,*/
            ]);
            $this->crud->addColumn([
                'label' => 'Brand',
                'type' => 'relationship',
                'name' => 'brand_id',
                'entity' => 'brand',
                'attribute' => 'name',
                /*'inline_create' => true,*/
            ]);
            $this->crud->addColumn([
                'label' => 'Sub Category',
                'type' => 'relationship',
                'name' => 'sub_category_id',
                'entity' => 'subCategory',
                'attribute' => 'name',
                /*'inline_create' => true,*/
            ]);
            CRUD::addColumn([   // Wysiwyg
                'name'  => 'features',
                'label' => 'Feature',
                'type'  => 'closure',
                'function' => function($entry) {
                $html = '';
                    foreach (json_decode($entry->features , true) as $item){
                        $html .= '<p> Name: '.$item['name'].'</p>';
                        $html .= '<p> Description: '.$item['desc'].'</p>';
                    }
                return $html;
                }
            ]);

            CRUD::addColumn([   // Wysiwyg
                'name'  => 'variant',
                'label' => 'Variant',
                'type'  => 'closure',
                'function' => function($entry) {
                $html = '';
                    foreach (json_decode($entry->variant , true) as $item){
                        $html .= '<p> Name: '.$item['name'].'</p>';
                        $html .= '<p> Description: '.$item['desc'].'</p>';
                        $html .= '<p> Price: '.$item['price'].'</p>';
                        $html .= '<hr>';
                    }
                return $html;
                }
            ]);


            $this->crud->setFromDb();
            $this->crud->removeColumn('extras');
        }

        // cycle through columns
        foreach ($this->crud->columns() as $key => $column) {

            // remove any autoset relationship columns
            if (array_key_exists('model', $column) && array_key_exists('autoset', $column) && $column['autoset']) {
                $this->crud->removeColumn($column['key']);
            }

            // remove any autoset table columns
            if ($column['type'] == 'table' && array_key_exists('autoset', $column) && $column['autoset']) {
                $this->crud->removeColumn($column['key']);
            }

            // remove the row_number column, since it doesn't make sense in this context
            if ($column['type'] == 'row_number') {
                $this->crud->removeColumn($column['key']);
            }

            // remove columns that have visibleInShow set as false
            if (isset($column['visibleInShow']) && $column['visibleInShow'] == false) {
                $this->crud->removeColumn($column['key']);
            }

            // remove the character limit on columns that take it into account
            if (in_array($column['type'], ['text', 'email', 'model_function', 'model_function_attribute', 'phone', 'row_number', 'select'])) {
                $this->crud->modifyColumn($column['key'], ['limit' => ($column['limit'] ?? 999)]);
            }
        }

        // remove preview button from stack:line
        $this->crud->removeButton('show');

        // remove bulk actions colums
        $this->crud->removeColumns(['blank_first_column', 'bulk_actions']);

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getShowView(), $this->data);
    }

    public function update()
    {

        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db
//        dump($this->crud->getStrippedSaveRequest()['images']);
        $res = str_replace( '\"', '"', $this->crud->getStrippedSaveRequest()['images']);

        $res2 = str_replace( '"[', '[', $res);
        $res3 = str_replace( ']"', ']', $res2);
        $res4 = str_replace( '\\\\', '\\', $res3);
        $this->crud->getStrippedSaveRequest()['images'] = $res4;

        $v = json_decode($this->crud->getStrippedSaveRequest()['images'] , true);


        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();
//        dd(empty($v));
        // save the redirect choice for next time
        $this->crud->setSaveAction();

        $item = $this->crud->update($request->get($this->crud->model->getKeyName()), $this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;
//        dd($v);

        if (!empty($v)){
            foreach ( $v as $image){
                $product_image = new ProductImages();
                $product_image->product_id = $item->getKey();
                $product_image->image_path = $image;
                $product_image->save();
            }
        }

        return $this->crud->performSaveAction($item->getKey());
    }
}
