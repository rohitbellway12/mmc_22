@extends('providermanagement::layouts.master')

@section('title', translate('Edit Chauffeur Service'))

@push('css_or_js')
    <style>
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .upload-box {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            background: #fafafa;
            transition: 0.3s;
        }

        .upload-box:hover {
            border-color: var(--c1);
            background: #fff;
        }

        .upload-icon {
            font-size: 2rem;
            color: #999;
            margin-bottom: 10px;
        }

        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .image-preview-item {
            width: 100px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
            position: relative;
        }

        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Document Preview Styling */
        .doc-preview-item {
            width: 120px;
            height: 90px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #eee;
            margin-top: 10px;
            background: #fafafa;
            transition: 0.3s;
        }

        .doc-preview-item:hover {
            border-color: var(--c1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .doc-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title-wrap mb-3">
                <h2 class="page-title">{{ translate('Update Your Chauffeur Services') }}</h2>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('provider.car.update', [$car->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="service_category" value="chauffeur">
                        <input type="hidden" name="category_id" value="{{ $categories->firstWhere('name', 'Chauffeur')?->id ?? ($car->category_id ?? 'fd6a04cf-3803-4a9b-a830-6cfa9c3c48d4') }}">
                        <input type="hidden" name="pricing_type" value="{{ $car->pricing_type }}" id="pricing_type_hidden">

                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="form-section-title">{{ translate('Basic Information') }}</h4>

                                <div class="form-group mb-3">
                                    <label class="form-label mb-2">{{ translate('Car Type') }} <span class="text-danger">*</span></label>
                                    <select name="car_type_id" class="form-control" required>
                                        <option value="" disabled>{{ translate('Select Car Type') }}</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}" {{ (old('car_type_id', $car->car_type_id) == $type->id) ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label mb-2">{{ translate('Car Brand/Model') }}</label>
                                    <input type="text" name="brand" class="form-control" value="{{ $car->brand }}"
                                        placeholder="{{ translate('Eg "Audi A4"') }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label mb-2">{{ translate('Registration Number') }}</label>
                                    <input type="text" name="registration_number" class="form-control"
                                        value="{{ $car->registration_number }}"
                                        placeholder="{{ translate('Eg "ABC XYZ"') }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label mb-2">{{ translate('Air Conditioning') }}</label>
                                    <select name="air_conditioning" class="form-control">
                                        <option value="1" {{ $car->air_conditioning ? 'selected' : '' }}>
                                            {{ translate('Yes') }}</option>
                                        <option value="0" {{ !$car->air_conditioning ? 'selected' : '' }}>
                                            {{ translate('No') }}</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label mb-2">{{ translate('Service Type') }}</label>
                                    <select name="service_type" class="form-control" id="chauffeur_service_type">
                                        <option value="full_day" {{ $car->pricing_type == 'daily' ? 'selected' : '' }}>
                                            {{ translate('Full Day') }}</option>
                                        <option value="hourly" {{ $car->pricing_type == 'hourly' ? 'selected' : '' }}>
                                            {{ translate('Hourly') }}</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label mb-2">{{ translate('Rate') }}
                                        ({{ currency_symbol() }})</label>
                                    <input type="number" name="hourly_rate" class="form-control" id="chauffeur_rate_input"
                                        value="{{ $car->hourly_rate > 0 ? $car->hourly_rate : $car->daily_rate }}"
                                        placeholder="{{ translate('Enter Rate') }}" step="0.01" required>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-2">{{ translate('Available Hours Start') }}</label>
                                            <input type="time" name="available_hours_start" class="form-control"
                                                value="{{ $car->available_hours_start }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-2">{{ translate('Available Hours End') }}</label>
                                            <input type="time" name="available_hours_end" class="form-control"
                                                value="{{ $car->available_hours_end }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label mb-2">{{ translate('Preferred Areas') }}</label>
                                    <textarea name="preferred_areas" class="form-control" rows="2"
                                        placeholder="{{ translate('Enter City Or Region') }}">{{ $car->preferred_areas }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h4 class="form-section-title">{{ translate('Gallery & Documents') }}</h4>

                                <div class="form-group mb-3">
                                    <label class="form-label mb-2">{{ translate('Car Images') }}</label>
                                    <div class="image-preview-container mb-2">
                                        @if ($car->images)
                                            @foreach ($car->images as $img)
                                                <div class="image-preview-item">
                                                    <img src="{{ asset('storage/app/public/car/' . $img) }}">
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="upload-box" onclick="document.getElementById('images').click()">
                                        <div class="upload-icon"><span class="material-icons">add_photo_alternate</span>
                                        </div>
                                        <p class="upload-text">{{ translate('Click to upload new images') }}</p>
                                    </div>
                                    <input type="file" name="images[]" id="images" multiple class="d-none"
                                        accept="image/*">
                                    <div id="images-preview" class="image-preview-container"></div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-2">{{ translate('Driving License') }}</label>
                                            <input type="file" name="driving_license" class="form-control">
                                            @if ($car->driving_license)
                                                <div class="doc-preview-item">
                                                    <a href="{{ asset('storage/app/public/car/documents/' . $car->driving_license) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('storage/app/public/car/documents/' . $car->driving_license) }}"
                                                            onerror="this.src='{{ asset('public/assets/admin-module/img/media/placeholder.png') }}'">
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label
                                                class="form-label mb-2">{{ translate('Vehicle Registration (V5C)') }}</label>
                                            <input type="file" name="vehicle_registration" class="form-control">
                                            @if ($car->vehicle_registration)
                                                <div class="doc-preview-item">
                                                    <a href="{{ asset('storage/app/public/car/documents/' . $car->vehicle_registration) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('storage/app/public/car/documents/' . $car->vehicle_registration) }}"
                                                            onerror="this.src='{{ asset('public/assets/admin-module/img/media/placeholder.png') }}'">
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-2">{{ translate('Insurance Documents') }}</label>
                                            <input type="file" name="insurance_documents" class="form-control">
                                            @if ($car->insurance_documents)
                                                <div class="doc-preview-item">
                                                    <a href="{{ asset('storage/app/public/car/documents/' . $car->insurance_documents) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('storage/app/public/car/documents/' . $car->insurance_documents) }}"
                                                            onerror="this.src='{{ asset('public/assets/admin-module/img/media/placeholder.png') }}'">
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-2">{{ translate('MOT Certificate') }}</label>
                                            <input type="file" name="mot_certificate" class="form-control">
                                            @if ($car->mot_certificate)
                                                <div class="doc-preview-item">
                                                    <a href="{{ asset('storage/app/public/car/documents/' . $car->mot_certificate) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('storage/app/public/car/documents/' . $car->mot_certificate) }}"
                                                            onerror="this.src='{{ asset('public/assets/admin-module/img/media/placeholder.png') }}'">
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <button type="submit" class="btn btn--primary">{{ translate('update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.getElementById('images').addEventListener('change', function(e) {
            const preview = document.getElementById('images-preview');
            preview.innerHTML = '';
            const files = e.target.files;
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const item = document.createElement('div');
                    item.className = 'image-preview-item';
                    item.innerHTML = `<img src="${e.target.result}">`;
                    preview.appendChild(item);
                }
                reader.readAsDataURL(files[i]);
            }
        });

        document.getElementById('chauffeur_service_type').addEventListener('change', function() {
            const isHourly = this.value === 'hourly';
            document.getElementById('pricing_type_hidden').value = isHourly ? 'hourly' : 'daily';
            document.getElementById('chauffeur_rate_input').name = isHourly ? 'hourly_rate' : 'daily_rate';
        });
    </script>
@endpush
