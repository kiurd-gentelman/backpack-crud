<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search_term = $request->input('q');

//        dd($request->input('q'));
//        dd($request->input('q'));

        // NOTE: this is a Backpack helper that parses your form input into an usable array.
        // you still have the original request as `request('form')`
        $form = backpack_form_input();
//        dd($form['category_id']);
        $options = SubCategory::query();

        // if no category has been selected, show no options
        if (! $form['category_id']) {
            return [];
        }

        // if a category has been selected, only show articles in that category
        if ($form['category_id']) {
            $options = $options->where('category_id', $form['category_id']);
        }

        if ($search_term) {
            $results = $options->where('name', 'LIKE', '%'.$search_term.'%')->paginate(10);
        } else {
            $results = $options->paginate(10);
        }

        return $results;
    }

    public function show($id)
    {

        return Category::find($id);
    }

    public function image_delete($id){
        dd($id);
    }

}
