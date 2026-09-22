@extends('providermanagement::layouts.master')

@section('title', translate('Add Car Hire Service'))

@push('css_or_js')
    <style>
        :root {
            --primary: var(--c1, #E2B67A);
            --secondary: #6c757d;
            --success: #28a745;
            --border-light: #f1f1f1;
        }

        .step-container {
            display: none;
            animation: fadeIn 0.4s ease-in-out;
        }

        .step-container.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Wizard Header Style */
        .wizard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            position: relative;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .wizard-header::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: #eee;
            z-index: 1;
        }

        .step-indicator {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .step-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #999;
            transition: 0.3s;
        }

        .step-indicator.active .step-circle {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(226, 182, 122, 0.2);
        }

        .step-indicator.done .step-circle {
            border-color: var(--success);
            background: var(--success);
            color: #fff;
        }

        .step-label {
            font-size: 12px;
            font-weight: 600;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .step-indicator.active .step-label {
            color: var(--primary);
        }

        /* Form Card Styling */
        .form-card {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border-radius: 15px;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title span {
            color: var(--primary);
        }

        /* Image Upload Redesign */
        .upload-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .image-upload-wrapper {
            position: relative;
        }

        .image-upload-box {
            border: 2px dashed #e5e5e5;
            border-radius: 12px;
            background: #fbfbfb;
            height: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            transition: 0.3s;
        }

        .image-upload-box:hover {
            border-color: var(--primary);
            background: #fff;
        }

        .image-upload-box .placeholder {
            text-align: center;
            color: #888;
        }

        .image-upload-box .placeholder i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: block;
        }

        .image-upload-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .upload-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-top: 8px;
            text-align: center;
        }

        .remove-img-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            color: #ff4d4d;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 5;
        }

        @media (max-width: 576px) {
            .upload-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title-wrap mb-4">
                <h2 class="page-title text-center">{{ translate('Register Your Car Hire Service') }}</h2>
                <p class="text-center text-muted">
                    {{ translate('Fill in the details below to list your vehicle for rental.') }}</p>
            </div>

            <!-- Wizard Header -->
            <div class="wizard-header">
                <div class="step-indicator active" id="ind-1">
                    <div class="step-circle">1</div>
                    <div class="step-label">{{ translate('Vehicle') }}</div>
                </div>
                <div class="step-indicator" id="ind-2">
                    <div class="step-circle">2</div>
                    <div class="step-label">{{ translate('Pricing') }}</div>
                </div>
                <div class="step-indicator" id="ind-3">
                    <div class="step-circle">3</div>
                    <div class="step-label">{{ translate('Terms') }}</div>
                </div>
            </div>

            <div class="card form-card max-w-800 mx-auto">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('provider.car.store') }}" method="POST" enctype="multipart/form-data"
                        id="car-hire-form">
                        @csrf
                        <input type="hidden" name="service_category" value="car_hire">
                        <input type="hidden" name="category_id"
                            value="{{ $categories->where('name', 'Car Hire')->first()->id ?? ($categories->first()->id ?? '') }}">
                        <input type="hidden" name="pricing_type" value="hourly">

                        <!-- Step 1: Vehicle Details -->
                        <div class="step-container active" id="step-1">
                            <h4 class="section-title"><span class="material-icons">info</span>
                                {{ translate('Vehicle Information') }}</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ translate('Car Brand/Model') }} *</label>
                                    <input type="text" name="brand" class="form-control h-45"
                                        placeholder="{{ translate('Eg "Audi A4 2023"') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ translate('Registration Number') }} *</label>
                                    <input type="text" name="registration_number" class="form-control h-45"
                                        placeholder="{{ translate('Eg "XYZ 1234"') }}" required>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="form-label">{{ translate('Manufacture Year') }}</label>
                                    <input type="number" name="manufacture_year" class="form-control h-45"
                                        placeholder="2022">
                                </div>
                                <div class="col-6 col-sm-4">
                                    <label class="form-label">{{ translate('Seating Capacity') }}</label>
                                    <input type="number" name="seating_capacity" class="form-control h-45" placeholder="5">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">{{ translate('Transmission') }}</label>
                                    <select name="transmission_type" class="form-control h-45">
                                        <option value="Automatic">{{ translate('Automatic') }}</option>
                                        <option value="Manual">{{ translate('Manual') }}</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ translate('Vehicle Type') }} *</label>
                                    <select name="car_type_id" class="form-control h-45" required>
                                        <option value="" disabled selected>{{ translate('Select Type') }}</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 mb-3">
                                <h4 class="section-title"><span class="material-icons">camera_alt</span>
                                    {{ translate('Vehicle Photos') }}</h4>
                                <p class="small text-muted mb-3">
                                    {{ translate('High-quality photos increase your chances of getting bookings.') }}</p>

                                <div class="upload-grid">
                                    @foreach (['front_view', 'rear_view', 'interior', 'dashboard'] as $view)
                                        <div class="image-upload-wrapper">
                                            <button type="button" class="remove-img-btn"
                                                onclick="clearImg('{{ $view }}')"><span
                                                    class="material-icons">close</span></button>
                                            <div class="image-upload-box" id="box_{{ $view }}"
                                                onclick="document.getElementById('input_{{ $view }}').click()">
                                                <div class="placeholder" id="placeholder_{{ $view }}">
                                                    <i class="material-icons">add_a_photo</i>
                                                    <span>{{ translate('Upload') }}</span>
                                                </div>
                                                <img src="" id="preview_{{ $view }}">
                                            </div>
                                            <span class="upload-label">{{ translate(str_replace('_', ' ', $view)) }}</span>
                                            <input type="file" name="car_images[{{ $view }}]"
                                                id="input_{{ $view }}" class="d-none" accept="image/*"
                                                onchange="previewFile(this, '{{ $view }}')">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4 pt-3">
                                <button type="button" class="btn btn--primary px-5 h-45"
                                    onclick="goToStep(2)">{{ translate('Continue') }}</button>
                            </div>
                        </div>

                        <!-- Step 2: Pricing & Location -->
                        <div class="step-container" id="step-2">
                            <h4 class="section-title"><span class="material-icons">payments</span>
                                {{ translate('Pricing & Availability') }}</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ translate('Hourly Rate') }} ({{ currency_symbol() }})
                                        *</label>
                                    <div class="input-group">
                                        <input type="number" name="hourly_rate" class="form-control h-45"
                                            placeholder="0.00" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ translate('Security Deposit') }}
                                        ({{ currency_symbol() }})</label>
                                    <input type="number" name="security_deposit" class="form-control h-45"
                                        placeholder="0.00" step="0.01">
                                </div>

                                <div class="col-12 mt-4">
                                    <h4 class="section-title"><span class="material-icons">location_on</span>
                                        {{ translate('Location Details') }}</h4>
                                </div>

                                <div class="col-md-6 text-dark font-semibold">
                                    <label class="form-label ">{{ translate('Enter Postcode') }}</label>
                                    <input type="text" name="postcode" class="form-control h-45"
                                        placeholder="{{ translate('Eg "TW6 1AP"') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ translate('Available For') }}</label>
                                    <select name="available_for" class="form-control h-45">
                                        <option value="Pickup">{{ translate('Pickup Only') }}</option>
                                        <option value="Delivery">{{ translate('Delivery Only') }}</option>
                                        <option value="Both" selected>{{ translate('Both (Pickup & Delivery)') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ translate('Collection Address') }}</label>
                                    <textarea name="address" class="form-control" rows="3"
                                        placeholder="{{ translate('Complete address for vehicle collection...') }}"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ translate('Available Hours Start') }}</label>
                                    <input type="time" name="available_hours_start" class="form-control h-45"
                                        value="09:00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ translate('Available Hours End') }}</label>
                                    <input type="time" name="available_hours_end" class="form-control h-45"
                                        value="18:00">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-3">
                                <button type="button" class="btn btn-outline-secondary px-4 h-45"
                                    onclick="goToStep(1)">{{ translate('Back') }}</button>
                                <button type="button" class="btn btn--primary px-5 h-45"
                                    onclick="goToStep(3)">{{ translate('Next Step') }}</button>
                            </div>
                        </div>

                        <!-- Step 3: Terms & Documents -->
                        <div class="step-container" id="step-3">
                            <h4 class="section-title"><span class="material-icons">gavel</span>
                                {{ translate('Policies & Documentation') }}</h4>

                            <div class="form-group mb-4">
                                <label class="form-label">{{ translate('Rental Terms & Conditions') }}</label>
                                <textarea name="terms_conditions" class="form-control" rows="6"
                                    placeholder="{{ translate('State your rules regarding mileage limits, fuel, smoking, late returns, etc.') }}"></textarea>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label
                                        class="form-label font-bold text-dark">{{ translate('Driving License') }}</label>
                                    <div class="custom-file">
                                        <input type="file" name="driving_license" class="form-control h-45"
                                            accept="image/*,application/pdf">
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('ID proof for the vehicle owner/operator.') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label
                                        class="form-label font-bold text-dark">{{ translate('Vehicle Registration (V5C)') }}</label>
                                    <div class="custom-file">
                                        <input type="file" name="vehicle_registration" class="form-control h-45"
                                            accept="image/*,application/pdf">
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('Proof of ownership or operation rights.') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label
                                        class="form-label font-bold text-dark">{{ translate('Insurance Documents') }}</label>
                                    <div class="custom-file">
                                        <input type="file" name="insurance_documents" class="form-control h-45"
                                            accept="image/*,application/pdf">
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('Valid commercial vehicle insurance.') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label
                                        class="form-label font-bold text-dark">{{ translate('Address Proof Documents') }}</label>
                                    <div class="custom-file">
                                        <input type="file" name="mot_certificate" class="form-control h-45"
                                            accept="image/*,application/pdf">
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('Utility bill or other address proof document.') }}</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-3">
                                <button type="button" class="btn btn-outline-secondary px-4 h-45"
                                    onclick="goToStep(2)">{{ translate('Back') }}</button>
                                <button type="submit"
                                    class="btn btn--primary px-5 h-45">{{ translate('Finish & Submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function goToStep(step) {
            // Update containers
            document.querySelectorAll('.step-container').forEach(c => c.classList.remove('active'));
            document.getElementById('step-' + step).classList.add('active');

            // Update indicators
            document.querySelectorAll('.step-indicator').forEach((item, index) => {
                const i = index + 1;
                item.classList.remove('active', 'done');
                if (i < step) item.classList.add('done');
                if (i === step) item.classList.add('active');
            });

            window.scrollTo(0, 0);
        }

        function previewFile(input, view) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('preview_' + view);
                    const placeholder = document.getElementById('placeholder_' + view);
                    const removeBtn = input.parentElement.querySelector('.remove-img-btn');

                    img.src = e.target.result;
                    img.style.display = 'block';
                    placeholder.style.display = 'none';
                    removeBtn.style.display = 'flex';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearImg(view) {
            const input = document.getElementById('input_' + view);
            const img = document.getElementById('preview_' + view);
            const placeholder = document.getElementById('placeholder_' + view);
            const removeBtn = input.parentElement.querySelector('.remove-img-btn');

            input.value = '';
            img.src = '';
            img.style.display = 'none';
            placeholder.style.display = 'block';
            removeBtn.style.display = 'none';
        }
    </script>
@endpush
