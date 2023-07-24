<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\ProductAttribute;
use Yajra\DataTables\DataTables;
use App\Models\Scopes\ActiveScope;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\CategoryRequest;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('product-view')) {
            abort(500);
        }
        if ($request->ajax()) {
            $data = Product::withoutGlobalScope(ActiveScope::class)->orderBy('id' , 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                 ->addColumn('master_image', function ($row) {
                    $master_image = '<img src="' . $row->master_image . '" alt="image" width="50" height="50">';
                    return $master_image;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'ACTIVE') {
                        $status = '<button class="modal-effect btn btn-sm btn-success" style="margin: 5px" id="status" data-id="' . $row->id . '" ><i class=" icon-check"></i></button>';
                    } else {
                        $status = '<button class="modal-effect btn btn-sm btn-danger" style="margin: 5px" id="status" data-id="' . $row->id . '" ><i class=" icon-check"></i></button>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="modal-effect btn btn-sm btn-info"  style="margin: 5px" id="showModalEditProduct" data-id="' . $row->id . '"><i class="las la-pen"></i></button>';
                    $btn = $btn . '<button class="modal-effect btn btn-sm btn-danger" style="margin: 5px" id="showModalDeleteProduct" data-name="' . $row->title_en . '" data-id="' . $row->id . '"><i class="las la-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['master_image' => 'master_image','status' => 'status', 'action' => 'action'])->make(true);
        }
        return view('dashboard.views-dash.product.index');
    }

    public function create()
    {
        $categories = Category::where('parent_id' , NULL)->where('status','ACTIVE')->withoutGlobalScope(ActiveScope::class)->orderBy('id' , 'desc')->get();


        $attributes = Attribute::withoutGlobalScope(ActiveScope::class)->orderBy('id' , 'desc')->get();

        return view('dashboard.views-dash.product.create',compact('categories','attributes'));
    }

    public function store(ProductRequest $productRequest)
    {

        $data=$productRequest->productData();
        $Product=  Product::create($data);

        foreach ($data['attributes'] as $key => $value) {

            $options = Option::where('attribute_id',$value)->get();

            foreach ($options as $option) {
             ProductAttribute::create([
                'product_id'=>$Product->id,
                'attribute_id'=>$value,
                'option_id'=>$option->id
            ]);
        }
        }
       return redirect()->route('product.index');
    }



    public function edit($id)
    {
        $product = Product::withoutGlobalScope(ActiveScope::class)->find($id);
        if ($product) {
            return ControllersService::responseSuccess([
                'message' => __('Found Data'),
                'status' => 200,
                'data' => $product
            ]);
        }
        return ControllersService::responseErorr([
            'message' => __('Not Found Data'),
            'status' => 400,
        ]);
    }

    public function update(ProductRequest $productRequest, $id)
    {
        Product::withoutGlobalScope(ActiveScope::class)->find($id)->update($productRequest->productData());
        return ControllersService::responseSuccess(['message' => __('updated successfully'),'status' => 200]);
    }

    public function destroy($id)
    {
        $product = Product::withoutGlobalScope(ActiveScope::class)->find($id);
        if ($product) {
            $product->delete();
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
        $product = Product::withoutGlobalScope(ActiveScope::class)->find($id);
        if ($product) {
            $product->changeStatus();
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
