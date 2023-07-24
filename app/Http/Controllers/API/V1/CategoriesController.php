<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Messages;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{

    public function __invoke(Request $request)
    {
        $categories = Category::when($request->parent_id, function($q) use ($request) {
            $q->where('parent_id', $request->parent_id);
        })->when($request->name, function($q) use ($request) {
            $q->where('title_ar', 'like', '%' . $request->name . '%')
            ->orWhere('title_en' , 'like' , '%'. $request->name . '%');
        })->where('parent_id' , null)->get();

        return CategoryResource::collection($categories)
        ->additional(['code' => 200 , 'status' => true ,'message' =>  Messages::getMessage('operation accomplished successfully')]);
    }
}
