<?php

namespace Modules\CarHire\Http\Controllers\Web\Provider;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Modules\CarHire\Entities\Car;
use Modules\CarHire\Entities\CarBrands;
use Modules\CarHire\Entities\CarModels;
use Modules\CarHire\Entities\CarType;
use Modules\CarHire\Entities\CarYears;
use Modules\CarHire\Entities\Features;
use Modules\CarHire\Entities\FuelTypes;
use Modules\CarHire\Entities\Transmissions;
use Modules\CategoryManagement\Entities\Category;

class ProviderCarController extends Controller
{
    private $car;

    public function __construct(Car $car)
    {
        $this->car = $car;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        Log::info('ProviderCarController@index started');
        $search = $request->has('search') ? $request['search'] : '';
        $category = $request->has('category') ? $request['category'] : 'all';
        $providerId = $request->user()->provider->id;
        Log::info('Provider ID: ' . $providerId);

        try {
            Log::info('Querying cars started');
            $cars = $this->car
                ->where('provider_id', $providerId)
                ->with(['category', 'type'])
                ->when($request->has('search'), function ($query) use ($search) {
                    $query->where('brand', 'like', "%{$search}%");
                })
                ->when($category != 'all', function ($query) use ($category) {
                    $query->where('service_category', $category);
                })
                ->latest()
                ->paginate(pagination_limit())->withQueryString();
            Log::info('Querying cars finished');

            return view('carhire::provider.index', compact('cars', 'search', 'category'));
        } catch (\Exception $e) {
            Log::error('Error in ProviderCarController@index: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            Toastr::error(translate('Something went wrong!'));
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $categories = Category::ofStatus(1)->ofType('main')->get();
        $types = CarType::all();
        return view('carhire::provider.create', compact('categories', 'types'));
    }

    public function createCarHire()
    {
        $categories = Category::ofStatus(1)->ofType('main')->get();
        $types = CarType::all();
        $brands = CarBrands::all();
        return view('carhire::provider.car-hire-create', compact('categories', 'types', 'brands'));
    }

    public function createChauffeur()
    {
        $categories = Category::ofStatus(1)->ofType('main')->get();
        $types = CarType::all();
        $brands = CarBrands::all();
        return view('carhire::provider.chauffeur-create', compact('categories', 'types', 'brands'));
    }

    public function store(Request $request)
    {
        $rules = [
            'service_category' => 'required|in:car_hire,chauffeur',
            'category_id' => 'required',
            'brand' => 'required|string',
            'pricing_type' => 'required|in:both,hourly,daily',
            'registration_number' => 'nullable|string',
            'manufacture_year' => 'nullable|string',
            'seating_capacity' => 'nullable|string',
            'transmission_type' => 'nullable|string',
            'security_deposit' => 'nullable|numeric|min:0',
            'postcode' => 'nullable|string',
            'address' => 'nullable|string',
            'available_for' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
        ];

        if ($request->service_category == 'car_hire') {
            $rules['car_images.front_view'] = 'required|image';
            $rules['hourly_rate'] = 'required|numeric|min:0';
        } else {
            $rules['images'] = 'required|array';
            if ($request->pricing_type == 'daily') {
                $rules['daily_rate'] = 'required|numeric|min:0';
            } else {
                $rules['hourly_rate'] = 'required|numeric|min:0';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $providerId = $request->user()->provider->id;

        $car = $this->car;
        $car->provider_id = $providerId;
        $car->category_id = $request->category_id;
        $car->brand = $request->brand;
        $car->registration_number = $request->registration_number;
        $car->air_conditioning = $request->air_conditioning ? 1 : 0;
        $car->service_category = $request->service_category;
        $car->pricing_type = $request->pricing_type;
        $car->hourly_rate = $request->pricing_type == 'daily' ? 0 : $request->hourly_rate;
        $car->daily_rate = $request->pricing_type == 'hourly' ? 0 : $request->daily_rate;
        $car->available_hours_start = $request->available_hours_start;
        $car->available_hours_end = $request->available_hours_end;
        $car->preferred_areas = $request->preferred_areas;
        $car->car_type_id = $request->car_type_id;
        $car->status = 1;

        // New fields
        $car->manufacture_year = $request->manufacture_year;
        $car->seating_capacity = $request->seating_capacity;
        $car->transmission_type = $request->transmission_type;
        $car->security_deposit = $request->security_deposit;
        $car->postcode = $request->postcode;
        $car->address = $request->address;
        $car->available_for = $request->available_for;
        $car->terms_conditions = $request->terms_conditions;

        // Handle Features
        if ($request->has('features')) {
            $features = is_array($request->features) ? $request->features : json_decode($request->features, true);
            $car->features = $features;
        }

        // Handle Car Hire View-Specific Images
        if ($request->has('car_images')) {
            $images = [];
            $views = ['front_view', 'rear_view', 'interior', 'dashboard'];
            foreach ($views as $view) {
                if ($request->hasFile("car_images.$view")) {
                    $images[] = file_uploader('car/', 'png', $request->file("car_images.$view"));
                } else {
                    $images[] = null; // Keep placeholder or handle as needed
                }
            }
            $car->images = $images;
        } elseif ($request->has('images')) {
            // Fallback for Chauffeur or other generic uploads
            $images = [];
            foreach ($request->images as $image) {
                $images[] = file_uploader('car/', 'png', $image);
            }
            $car->images = $images;
        }

        // Handle Document Uploads
        if ($request->hasFile('driving_license')) {
            $car->driving_license = file_uploader('car/documents/', 'png', $request->file('driving_license'));
        }
        if ($request->hasFile('vehicle_registration')) {
            $car->vehicle_registration = file_uploader('car/documents/', 'png', $request->file('vehicle_registration'));
        }
        if ($request->hasFile('insurance_documents')) {
            $car->insurance_documents = file_uploader('car/documents/', 'png', $request->file('insurance_documents'));
        }
        if ($request->hasFile('mot_certificate')) {
            $car->mot_certificate = file_uploader('car/documents/', 'png', $request->file('mot_certificate'));
        }

        $car->save();

        Toastr::success(translate('Car added successfully'), translate('Success'));
        return redirect()->route('provider.car.index');
    }


    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('carhire::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $providerId = auth()->user()->provider->id;
        $car = $this->car->where('id', $id)->where('provider_id', $providerId)->firstOrFail();

        $categories = Category::ofStatus(1)->ofType('main')->get();
        $types = CarType::all();
        $brands = CarBrands::all();

        if ($car->service_category == 'car_hire') {
            return view('carhire::provider.edit-car-hire', compact('car', 'categories', 'types', 'brands'));
        }
        return view('carhire::provider.edit-chauffeur', compact('car', 'categories', 'types', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $providerId = $request->user()->provider->id;
        $car = $this->car->where('id', $id)->where('provider_id', $providerId)->firstOrFail();

        $rules = [
            'service_category' => 'required|in:car_hire,chauffeur',
            'category_id' => 'required',
            'brand' => 'required|string',
            'pricing_type' => 'required|in:both,hourly,daily',
            'registration_number' => 'nullable|string',
            'manufacture_year' => 'nullable|string',
            'seating_capacity' => 'nullable|string',
            'transmission_type' => 'nullable|string',
            'security_deposit' => 'nullable|numeric|min:0',
            'postcode' => 'nullable|string',
            'address' => 'nullable|string',
            'available_for' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
        ];

        if ($request->service_category == 'car_hire') {
            $rules['hourly_rate'] = 'required|numeric|min:0';
        } else {
            if ($request->pricing_type == 'daily') {
                $rules['daily_rate'] = 'required|numeric|min:0';
            } else {
                $rules['hourly_rate'] = 'required|numeric|min:0';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $car->category_id = $request->category_id;
        $car->car_type_id = $request->car_type_id;
        $car->brand = $request->brand;
        $car->registration_number = $request->registration_number;
        $car->air_conditioning = $request->air_conditioning ? 1 : 0;
        $car->service_category = $request->service_category;
        $car->pricing_type = $request->pricing_type;
        $car->hourly_rate = $request->pricing_type == 'daily' ? 0 : $request->hourly_rate;
        $car->daily_rate = $request->pricing_type == 'hourly' ? 0 : $request->daily_rate;
        $car->available_hours_start = $request->available_hours_start;
        $car->available_hours_end = $request->available_hours_end;
        $car->preferred_areas = $request->preferred_areas;

        // New fields
        $car->manufacture_year = $request->manufacture_year;
        $car->seating_capacity = $request->seating_capacity;
        $car->transmission_type = $request->transmission_type;
        $car->security_deposit = $request->security_deposit;
        $car->postcode = $request->postcode;
        $car->address = $request->address;
        $car->available_for = $request->available_for;
        $car->terms_conditions = $request->terms_conditions;

        // Handle Features
        if ($request->has('features')) {
            $features = is_array($request->features) ? $request->features : json_decode($request->features, true);
            $car->features = $features;
        }

        // Handle Car Hire View-Specific Images
        if ($request->has('car_images')) {
            $images = $car->images ?? [null, null, null, null];
            $views = ['front_view', 'rear_view', 'interior', 'dashboard'];
            foreach ($views as $index => $view) {
                if ($request->hasFile("car_images.$view")) {
                    $images[$index] = file_uploader('car/', 'png', $request->file("car_images.$view"));
                }
            }
            $car->images = $images;
        } elseif ($request->has('images')) {
            // Fallback for Chauffeur or other generic uploads
            $images = $car->images ?? [];
            foreach ($request->images as $image) {
                $images[] = file_uploader('car/', 'png', $image);
            }
            $car->images = $images;
        }

        // Handle Document Uploads
        if ($request->hasFile('driving_license')) {
            $car->driving_license = file_uploader('car/documents/', 'png', $request->file('driving_license'), $car->driving_license);
        }
        if ($request->hasFile('vehicle_registration')) {
            $car->vehicle_registration = file_uploader('car/documents/', 'png', $request->file('vehicle_registration'), $car->vehicle_registration);
        }
        if ($request->hasFile('insurance_documents')) {
            $car->insurance_documents = file_uploader('car/documents/', 'png', $request->file('insurance_documents'), $car->insurance_documents);
        }
        if ($request->hasFile('mot_certificate')) {
            $car->mot_certificate = file_uploader('car/documents/', 'png', $request->file('mot_certificate'), $car->mot_certificate);
        }

        $car->save();

        Toastr::success(translate('Car updated successfully'), translate('Success'));
        return redirect()->route('provider.car.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id)
    {
        $providerId = $request->user()->provider->id;
        $car = $this->car->where('id', $id)->where('provider_id', $providerId)->firstOrFail();
        $car->delete();

        Toastr::success(translate('Car deleted successfully'));
        return back();
    }
}
