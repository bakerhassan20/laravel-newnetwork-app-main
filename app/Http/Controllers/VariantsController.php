<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Scopes\ActiveScope;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\CategoryRequest;

class VariantsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('category-view')) {
            abort(500);
        }
        if ($request->ajax()) {
            $data = Variant::withoutGlobalScope(ActiveScope::class)->orderBy('id' , 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('product_name', function ($row) {
                    $product =Product::find($row->product_id);
                    return $product->title_en;
                })
                ->addColumn('image', function ($row) {
                    $image = '<img src="' . $row->image . '" alt="image" width="50" height="50">';
                    return $image;
                })

                ->addColumn('action', function ($row) {
                    $btn = '<button class="modal-effect btn btn-sm btn-info"  style="margin: 5px" id="showModalEditCategory" data-id="' . $row->id . '"><i class="las la-pen"></i></button>';
                    $btn = $btn . '<button class="modal-effect btn btn-sm btn-danger" style="margin: 5px" id="showModalDeleteCategory" data-name="' . $row->title_en . '" data-id="' . $row->id . '"><i class="las la-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['image' => 'image', 'status' => 'status', 'action' => 'action'])->make(true);
        }
        return view('dashboard.views-dash.category.index');
    }

    public function store(CategoryRequest $categoryRequest)
    {
        Category::create($categoryRequest->categoryData());
        return ControllersService::responseSuccess(['message' => __('Added successfully') , 'status' => 200]);
    }

    public function edit($id)
    {
        $category = Category::withoutGlobalScope(ActiveScope::class)->find($id);
        if ($category) {
            return ControllersService::responseSuccess([
                'message' => __('Found Data'),
                'status' => 200,
                'data' => $category
            ]);
        }
        return ControllersService::responseErorr([
            'message' => __('Not Found Data'),
            'status' => 400,
        ]);
    }

    public function update(CategoryRequest $categoryRequest, $id)
    {
        Category::withoutGlobalScope(ActiveScope::class)->find($id)->update($categoryRequest->categoryData());
        return ControllersService::responseSuccess(['message' => __('updated successfully'),'status' => 200]);
    }

    public function destroy($id)
    {
        $category = Category::withoutGlobalScope(ActiveScope::class)->find($id);
        if ($category) {
            $category->delete();
            return ControllersService::responseSuccess([
                'message' => __('Deleted successfully'),
                'status' => 200,
            ]);
        }
        return ControllersService::responseErorr([
            'message' => __('Not Found Data'),
            'status' => false,
        ]);
    }

    public function status($id)
    {
        $category = Category::withoutGlobalScope(ActiveScope::class)->find($id);
        if ($category) {
            $category->changeStatus();
            return ControllersService::responseSuccess([
                'message' => __('Updated successfully'),
                'status' => 200,
            ]);
        } else {
            return ControllersService::responseErorr([
                'message' => __('Not Found Data'),
                'status' => 400,
            ]);
        }
    }
}
