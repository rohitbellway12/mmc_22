<?php

namespace Modules\ServiceManagement\Http\Controllers\Web\Provider;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;
use Modules\ServiceManagement\Entities\Tyre;
use Modules\CategoryManagement\Entities\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ProviderTyreController extends Controller
{
    private $tyre;

    public function __construct(Tyre $tyre)
    {
        $this->tyre = $tyre;
    }

    public function index(Request $request)
    {
        $search = $request->has('search') ? $request['search'] : '';
        $provider = $request->user()->provider;
        if (!$provider) {
            \Brian2694\Toastr\Facades\Toastr::error(translate('provider_not_found'), translate('Error'));
            return redirect()->back();
        }
        $providerId = $provider->id;


        $tyres = $this->tyre
            ->where('provider_id', $providerId)
            ->when($request->has('search'), function ($query) use ($search) {
                $query->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(pagination_limit());

        return view('servicemanagement::provider.tyre.index', compact('tyres', 'search'));
    }

    public function create()
    {
        $categories = Category::ofStatus(1)->ofType('main')->get();
        return view('servicemanagement::provider.tyre.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required|string',
            'model' => 'nullable|string',
            'size' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images' => 'required|array',
            'images.*' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tyreInstance = $this->tyre;
        $provider = $request->user()->provider;
        if (!$provider) {
            \Brian2694\Toastr\Facades\Toastr::error(translate('provider_not_found'), translate('Error'));
            return redirect()->back();
        }
        $tyreInstance->fill([
            'category_id' => $request->category_id,
            'brand' => $request->brand,
            'model' => $request->model,
            'size' => $request->size,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);
        $tyreInstance->provider_id = $provider->id;
        $tyreInstance->status = 1;

        if ($request->has('images')) {
            $images = [];
            foreach ($request->images as $image) {
                $images[] = file_uploader('tyre/', 'png', $image);
            }
            $tyreInstance->images = $images;
        }

        $tyreInstance->save();
        \Brian2694\Toastr\Facades\Toastr::success(translate('Tyre added successfully'), translate('Success'));
        return redirect()->route('provider.tyre.index');
    }

    public function edit($id)
    {
        $provider = auth()->user()->provider;
        if (!$provider) {
            \Brian2694\Toastr\Facades\Toastr::error(translate('provider_not_found'), translate('Error'));
            return redirect()->back();
        }
        $providerId = $provider->id;
        $tyre = $this->tyre->where('id', $id)->where('provider_id', $providerId)->firstOrFail();
        $categories = Category::ofStatus(1)->ofType('main')->get();

        return view('servicemanagement::provider.tyre.edit', compact('tyre', 'categories'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $providerId = $request->user()->provider->id;
        $tyre = $this->tyre->where('id', $id)->where('provider_id', $providerId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'brand' => 'required|string',
            'model' => 'nullable|string',
            'size' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tyre->category_id = $request->category_id;
        $tyre->brand = $request->brand;
        $tyre->model = $request->input('model');
        $tyre->size = $request->size;
        $tyre->price = $request->price;
        $tyre->stock = $request->stock;

        if ($request->has('images')) {
            $images = $tyre->images ?? [];
            foreach ($request->images as $image) {
                $images[] = file_uploader('tyre/', 'png', $image);
            }
            $tyre->images = $images;
        }

        $tyre->save();

        Toastr::success(translate('Tyre updated successfully'), translate('Success'));
        return redirect()->route('provider.tyre.index');
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        $providerId = $request->user()->provider->id;
        $tyre = $this->tyre->where('id', $id)->where('provider_id', $providerId)->firstOrFail();
        $tyre->delete();

        Toastr::success(translate('Tyre deleted successfully'), translate('Success'));
        return back();
    }

    public function statusUpdate(Request $request, $id): JsonResponse
    {
        $providerId = $request->user()->provider->id;
        $tyre = $this->tyre->where('id', $id)->where('provider_id', $providerId)->firstOrFail();
        $tyre->status = !$tyre->status;
        $tyre->save();

        return response()->json(response_formatter(DEFAULT_200), 200);
    }
}
