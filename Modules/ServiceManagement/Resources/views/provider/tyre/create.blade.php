@extends('providermanagement::layouts.master')

@section('title', translate('Add New Tyre'))

@push('css_or_js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .image-preview-box {
            position: relative;
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .image-preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-image {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            width: 20px;
            height: 20px;
            padding: 0;
            line-height: 1;
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h2 class="page-title">{{ translate('Add New Tyre') }}</h2>
                <p class="page-subtitle">{{ translate('List your tyre stock for customers to browse and book') }}</p>
            </div>

            <form action="{{ route('provider.tyre.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-info-circle me-2"></i> {{ translate('Tyre Information') }}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="brand" class="form-label">{{ translate('Brand') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="brand" name="brand"
                                            placeholder="{{ translate('Eg: Michelin, Pirelli') }}" required
                                            value="{{ old('brand') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="model" class="form-label">{{ translate('Model') }}</label>
                                        <input type="text" class="form-control" id="model" name="model"
                                            placeholder="{{ translate('Eg: Pilot Sport 4') }}" value="{{ old('model') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="size" class="form-label">{{ translate('Size') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="size" name="size"
                                            placeholder="{{ translate('Eg: 205/55 R16') }}" required
                                            value="{{ old('size') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="category_id" class="form-label">{{ translate('Category') }}</label>
                                        <select class="form-select" id="category_id" name="category_id">
                                            <option value="">{{ translate('Select Category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-tags me-2"></i> {{ translate('Pricing & Stock') }}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label">{{ translate('Price per Tyre') }} <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ currency_symbol() }}</span>
                                            <input type="number" class="form-control" id="price" name="price"
                                                required step="0.01" value="{{ old('price') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="stock" class="form-label">{{ translate('Stock Quantity') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="stock" name="stock" required
                                            min="0" value="{{ old('stock', 1) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-images me-2"></i> {{ translate('Tyre Images') }}
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">{{ translate('Images') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="images[]" id="tyre_images" class="form-control" multiple
                                        accept="image/*" required>
                                    <div class="image-preview-container mt-3" id="image_preview_container"></div>
                                </div>
                                <button type="submit" class="btn btn--primary w-100">
                                    <i class="fas fa-save me-2"></i> {{ translate('Save Tyre') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.getElementById('tyre_images').addEventListener('change', function(e) {
            const container = document.getElementById('image_preview_container');
            container.innerHTML = '';

            if (this.files) {
                Array.from(this.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'image-preview-box';
                        div.innerHTML = `<img src="${e.target.result}">`;
                        container.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        });
    </script>
@endpush
