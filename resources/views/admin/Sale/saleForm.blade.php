@extends('admin.layout.dashboard')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Info boxes -->
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-12">

                        <div class="card">

                            <div class="card-header">
                                <a href="{{ route('saleList') }}" class="btn btn-primary m-1">View
                                    List</a>
                            </div>

                            <!-- /.card-header -->
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-10">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Sale <small>Form</small></h3>
                                        </div>
                                        <!-- form start -->
                                        <form id="addSaleForm" class="was-validated" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf

                                            <input type="hidden" name="role_id" class="form-control"
                                                id="exampleInputEmail1" placeholder="Enter title"
                                                value="{{ auth()->user()->role_id }}">

                                            <div class="form-group">
                                                <label>Category</label>
                                                <select name="sale_id" class="form-control" required
                                                    onchange="categoryfun(this)">
                                                    <option value="" selected>Select</option>
                                                    @foreach ($category as $cat)
                                                        <option value="{{ $cat['id'] }}">
                                                            {{ $cat['type'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="valid-feedback">Valid.</div>
                                                <div class="invalid-feedback">Please Enter Category</div>


                                                <div id="errormsg"></div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group d-none" id="subcategory">
                                                        <label>Sub Category</label>
                                                        <select name="sub_cat_id" class="form-control" id="inputGroupVendor"
                                                            required>
                                                            <option value="" selected>Select</option>
                                                        </select>
                                                        <div class="valid-feedback">Valid.</div>
                                                        <div class="invalid-feedback">Please Enter City</div>
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group d-none" id="citymsg">
                                                        <label>City</label>
                                                        <select name="city_id" class="form-control" id="inputcity" required>
                                                            <option value="" selected>Select</option>
                                                        </select>
                                                        <div class="valid-feedback">Valid.</div>
                                                        <div class="invalid-feedback">Please Enter City</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="maindiv"></div>


                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <button onclick="addsalefun();" class="btn btn-primary">Add</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <div class="col-md-1"></div>
                            </div>
                            <!-- /.card-body -->
                        </div>


                        <!-- /.info-box -->


                    </div>
                    <!-- /.col -->


                </div>
                <!-- /.row -->
            </div>
            <!--/. container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection

<script>
    function categoryfun(catid) {
        // console.log(catid.value);
        event.preventDefault();
        $.ajax({
            url: "{{ url('getsaleFormajax') }}",
            method: "post",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                'catid': catid.value
            },
            success: function(data) {
                console.log(data);
                if (data.data != null) {
                    if (data.data != 0) {
                        $('#subcategory').removeClass('d-none');
                        $('#citymsg').removeClass('d-none');



                        $('#inputGroupVendor').html("");
                        $.each(data.data, function(key, val) {
                            $('#inputGroupVendor').append("<option value ='" + val.id + "'>" +
                                val
                                .sub_type + "</option>");
                        });

                        $('#inputcity').html("");
                        $.each(data.cityData, function(key, val) {
                            $('#inputcity').append("<option value ='" + val.id + "'>" + val
                                .city_name + "</option>");
                        });


                        if (data.data[0].sale_id == 1) {
                            $('#errormsg').html('');
                            $('#maindiv').html("");
                            $('#maindiv').removeClass('d-none');

                            $('#maindiv').append(` <div class="form-group">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Vendor Name</label>
                                        <input type="text" name="vendor_name" class="form-control"
                                                            id="" placeholder="Enter Vendor Name">
                                        <div id="vendor_name" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Posted By</label>
                                        <select name="owner_or_broker" class="form-control">
                                            <option value="" selected>Select</option>
                                            <option value="owner">Owner</option>
                                            <option value="broker">Broker</option>
                                        </select>
                                        <div id="owner_or_broker" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Property Location</label>
                                        <input type="text" name="property_location" class="form-control"
                                                            id="" placeholder="Enter Property Location">
                                        <div id="property_location" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Price</label>
                                        <input type="text" name="price" class="form-control"
                                                            id="" placeholder="Enter Price">
                                        <div id="price" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Whatsapp Number</label>
                                        <input type="text" name="whatsapp_no" class="form-control"
                                                            id="" placeholder="Enter Whatsapp Number">
                                        <div id="whatsapp_no" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Call Number</label>
                                        <input type="text" name="call_no" class="form-control"
                                                            id="" placeholder="Enter Call Number">
                                        <div id="call_no" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Vehicle Sighting</label>
                                        <input type="text" name="vehicle_sighting" class="form-control"
                                                            id="" placeholder="Enter Vehicle Sighting">
                                        <div id="vehicle_sighting" class="text-danger"></div>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Brand</label>
                                        <input type="text" name="brand" class="form-control"
                                                            id="" placeholder="Enter Brand">
                                        <div id="brand" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Model Name</label>
                                        <input type="text" name="model_name" class="form-control"
                                                            id="" placeholder="Enter Model Name">
                                        <div id="model_name" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Model Year</label>
                                        <input type="text" name="model_year" class="form-control"
                                                            id="" placeholder="Enter Model Year">
                                        <div id="model_year" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fuel Type</label>
                                        <select name="fuel_type" class="form-control">
                                            <option value="" selected>Select</option>
                                            <option value="petrol">Petrol</option>
                                            <option value="diesel">Diesel</option>
                                            <option value="cng">CNG</option>
                                            <option value="ev">Electric Vehicle</option>
                                        </select>
                                        <div id="fuel_type" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Seater</label>
                                        <input type="text" name="seater" class="form-control"
                                                            id="" placeholder="Enter Seat">
                                        <div id="seater" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Kilometer Running</label>
                                        <input type="text" name="kilometer_running" class="form-control"
                                                            id="" placeholder="Enter Kilometer Running">
                                        <div id="kilometer_running" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Insurance Period</label>
                                        <input type="text" name="insurance_period" class="form-control"
                                                            id="" placeholder="Enter Insurance Period">
                                        <div id="insurance_period" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Color</label>
                                        <input type="text" name="color" class="form-control"
                                                            id="" placeholder="Enter Color">
                                        <div id="color" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Image</label>
                                        <input type="file" name="image[]" class="form-control @error('image') is-invalid @enderror" id="exampleInputPassword1" placeholder="Enter image" multiple>
                                        @error('image')
                                            <div class="text-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            </div>`);
                        } else if (data.data[0].sale_id == 2) {
                            $('#errormsg').html('');
                            $('#maindiv').html("");
                            $('#maindiv').removeClass('d-none');
                            $('#maindiv').append(` <div class="form-group">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Vendor Name</label>
                                        <input type="text" name="vendor_name" class="form-control"
                                                            id="" placeholder="Enter Vendor Name">
                                        <div id="vendor_name" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Posted By</label>
                                        <select name="owner_or_broker" class="form-control">
                                            <option value="" selected>Select</option>
                                            <option value="owner">Owner</option>
                                            <option value="broker">Broker</option>
                                        </select>
                                        <div id="owner_or_broker" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Property Location</label>
                                        <input type="text" name="property_location" class="form-control"
                                                            id="" placeholder="Enter Property Location">
                                        <div id="property_location" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Price</label>
                                        <input type="text" name="price" class="form-control"
                                                            id="" placeholder="Enter Price">
                                        <div id="price" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Whatsapp Number</label>
                                        <input type="text" name="whatsapp_no" class="form-control"
                                                            id="" placeholder="Enter Whatsapp Number">
                                        <div id="whatsapp_no" class="text-danger"></div>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Call Number</label>
                                        <input type="text" name="call_no" class="form-control"
                                                            id="" placeholder="Enter Call Number">
                                        <div id="call_no" class="text-danger"></div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Size Length Width</label>
                                        <input type="text" name="size_length_width" class="form-control"
                                                            id="" placeholder="Enter Size Length Width">
                                        <div id="size_length_width" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Room Qty</label>
                                        <input type="text" name="room_qty" class="form-control"
                                                            id="" placeholder="Enter Room Qty">
                                        <div id="room_qty" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Kitchen</label>
                                        <input type="text" name="kitchen" class="form-control"
                                                            id="" placeholder="Enter Kitchen">
                                        <div id="kitchen" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Hall</label>
                                        <input type="text" name="hall" class="form-control"
                                                            id="" placeholder="Enter Hall">
                                        <div id="hall" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Lat Bath</label>
                                        <select name="lat_bath" class="form-control">
                                            <option value="" selected>Select</option>
                                            <option value="attached">Attached</option>
                                            <option value="non_attached">Non Attached</option>
                                        </select>
                                        <div id="lat_bath" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Image</label>
                                        <input type="file" name="image[]" class="form-control @error('image') is-invalid @enderror" id="exampleInputPassword1" placeholder="Enter image" multiple>
                                        @error('image')
                                            <div class="text-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            </div>`);
                        } else if (data.data[0].sale_id == 3) {
                            $('#errormsg').html('');
                            $('#maindiv').html("");
                            $('#maindiv').removeClass('d-none');
                            $('#maindiv').append(` <div class="form-group">
                                <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Vendor Name</label>
                                        <input type="text" name="vendor_name" class="form-control"
                                                            id="" placeholder="Enter Vendor Name">
                                        <div id="vendor_name" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Posted By</label>
                                        <select name="owner_or_broker" class="form-control">
                                            <option value="" selected>Select</option>
                                            <option value="owner">Owner</option>
                                            <option value="broker">Broker</option>
                                        </select>
                                        <div id="owner_or_broker" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Price</label>
                                        <input type="text" name="price" class="form-control"
                                                            id="" placeholder="Enter Price">
                                        <div id="price" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Whatsapp Number</label>
                                        <input type="text" name="whatsapp_no" class="form-control"
                                                            id="" placeholder="Enter Whatsapp Number">
                                        <div id="whatsapp_no" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Call Number</label>
                                        <input type="text" name="call_no" class="form-control"
                                                            id="" placeholder="Enter Call Number">
                                        <div id="call_no" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>


                            </div>`);

                        }
                    }
                } else {
                    $('#errormsg').html("");
                    $('#subcategory').addClass('d-none');
                    $('#citymsg').addClass('d-none');
                    $('#maindiv').addClass('d-none');
                    $('#errormsg').append(
                        "<div class='text-danger pl-5'>There is no sub category for this category... Please add subcategory First</div>"
                    );
                }
            },
            // error: function(data) {
            //     const obj = JSON.parse(data.responseText);
            //     $('#errorimg').html("");
            //     $('#errorimg').append(obj.message);
            // }
        });

    }

    function addsalefun() {
        event.preventDefault();
        var form = document.getElementById('addSaleForm');
        var formData = new FormData(form);
        $.ajax({
            url: "{{ url('addsale/') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);
                if (response != null) {
                    window.location.href = '{{ url('saleList') }}';
                }
            },
            error: function(data) {
                const obj = JSON.parse(data.responseText);
                $.each(obj.errors, function(key, value) {
                    $('#' + key).html('');
                    $('#' + key).append(value[0]);
                    const myTimeout = setTimeout(myGreeting, 5000);

                    function myGreeting() {
                        $('#' + key).html('');
                    }
                });
            }
        });
    }
</script>
