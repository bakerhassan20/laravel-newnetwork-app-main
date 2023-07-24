@extends('dashboard.layouts.master')
@section('title', __('Product'))
@section('css')
<link href="{{URL::asset('dashboard/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-color-markers/dist/css/leaflet-color-markers.css" />
    <script src="https://unpkg.com/leaflet-color-markers/dist/leaflet-color-markers.js"></script>

    <style>
        #map {
            height: 600px;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="row row-xs wd-xl-80p">
                        <div class="col-sm-6 col-md-3 mg-t-10">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="needs-validation" action="{{ route('product.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row row-sm">
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Title in English') }} :</label>
                            <input type="text" class="form-control" name="title_en" value="{{ old('title_en') }}">
                            @error('title_en')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Title in Arabic') }} :</label>
                            <input type="text" class="form-control" name="title_ar" value="{{ old('title_ar') }}"
                                required>
                            @error('title_ar')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Price') }} :</label>
                            <input type="number" class="form-control" name="price" value="{{ old('price') }}" required>
                            @error('price')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Discount') }} :</label>
                            <input type="number" class="form-control" name="discount" value="{{ old('discount') }}">
                            @error('discount')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Image') }} :</label>
                            <input type="file" class="form-control" name="master_image" required>
                            @error('file')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>


                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Category Name') }} :</label>
                            <select name="category_id" class="form-control">
                                <option value=""></option>
                                @foreach ($categories as $value)
                                    <option value="{{ $value->id }}" @if ($value->id == old('category_id')) selected @endif>
                                        {{ app()->getLocale() == 'ar' ? $value->title_ar : $value->title_en }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>


                        <div class="form-group col-md-6">

                        <label class="control-label">{{ __('Attributes') }}  </label>
                                    <select id="select-state2" name="attributes[]" multiple class="form-control form-control-lg select2">
                                        <option value=""> اختر من القائمة.... </option>
                                        @foreach($attributes as $attribute)
                                            <option {{old("user_update")==$attribute->id?"selected":""}} value="{{$attribute->id}}"> {{$attribute->title_en}} </option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger">{{$errors->first('user_update')}}</div>

                            </div><br>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Type') }} :</label>
                            <select name="type" class="form-control">
                                <option value=""></option>
                                <option value="ALL" @if ('ALL' == old('type')) selected @endif>
                                    {{ __('ALL') }}</option>
                                <option value="NEW" @if ('NEW' == old('type')) selected @endif>
                                    {{ __('NEW') }}</option>

                                <option value="MOSTBOUGHT" @if ('MOSTBOUGHT' == old('type')) selected @endif>
                                    {{ __('MOSTBOUGHT') }}</option>
                                <option value="MOSTWATCHED" @if ('MOSTWATCHED' == old('type')) selected @endif>
                                    {{ __('MOSTWATCHED') }}</option>

                            </select>
                            @error('type')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>



                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Status') }} :</label>
                            <select name="status" class="form-control">
                                <option value="ACTIVE" @if ('ACTIVE' == old('status')) selected @endif>
                                    {{ __('ACTIVE') }}</option>
                                <option value="INACTIVE" @if ('INACTIVE' == old('status')) selected @endif>
                                    {{ __('NACTIVE') }}</option>
                            </select>
                            @error('status')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>


                               <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('General Info in Arabic') }} :</label>
                            <textarea name="general_info_ar" class="form-control" rows="10"></textarea>
                            @error('general_info_ar')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('General info in English') }} :</label>
                            <textarea name="general_info_en" class="form-control" rows="10"></textarea>
                            @error('general_info_en')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>



                               <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Specefications in Arabic') }} :</label>
                            <textarea name="specefications_ar" class="form-control" rows="10"></textarea>
                            @error('specefications_ar')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Specefications in English') }} :</label>
                            <textarea name="specefications_en" class="form-control" rows="10"></textarea>
                            @error('specefications_en')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Description in Arabic') }} :</label>
                            <textarea name="description_ar" class="form-control" rows="10"></textarea>
                            @error('description_ar')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">{{ __('Description in English') }} :</label>
                            <textarea name="description_en" class="form-control" rows="10"></textarea>
                            @error('description_en')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                    </div>
                        <button type="submit"
                            class="btn btn-main-primary pd-x-30 mg-r-5 mg-t-5">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

<script src="{{URL::asset('dashboard/js/form-validation.js')}}"></script>
<script src="{{URL::asset('dashboard/plugins/select2/js/select2.min.js')}}"></script>

@stop
