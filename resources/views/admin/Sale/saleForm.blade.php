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
                                <div class="col-md-3"></div>
                                <div class="col-md-6">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Sale <small>Form</small></h3>
                                        </div>
                                        <!-- form start -->
                                        <form id="quickForm" class="was-validated" action="{{ route('addsale') }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf

                                            <input type="hidden" name="role_id" class="form-control"
                                                id="exampleInputEmail1" placeholder="Enter title"
                                                value="{{ auth()->user()->role_id }}">

                                            <div class="form-group">
                                                <label>Category</label>
                                                <select name="type" class="form-control" required
                                                    onchange="categoryfun(this)">
                                                    <option value="" selected>Select</option>
                                                    @foreach ($category as $cat)
                                                        <option value="{{ $cat['id'] }}">
                                                            {{ $cat['type'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="valid-feedback">Valid.</div>
                                                <div class="invalid-feedback">Please Enter City</div>
                                            </div>


                                            <div class="form-group d-none" id="subcategory">
                                                <label>Sub Category</label>
                                                <select name="type" class="form-control" id="inputGroupVendor" required>
                                                    <option value="" selected>Select</option>
                                                </select>
                                                <div class="valid-feedback">Valid.</div>
                                                <div class="invalid-feedback">Please Enter City</div>
                                            </div>

                                            <div id="maindiv"></div>


                                            {{-- <div class="card-body">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Title</label>
                                                    <input type="title" name="title" class="form-control"
                                                        id="exampleInputEmail1" placeholder="Enter title" required>
                                                    <div class="valid-feedback">Valid.</div>
                                                    <div class="invalid-feedback">Please Enter Title</div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Description</label>
                                                    <input type="description" name="description" class="form-control"
                                                        id="exampleInputPassword1" placeholder="Enter description" required>
                                                    <div class="valid-feedback">Valid.</div>
                                                    <div class="invalid-feedback">Please Enter Description</div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Image</label>
                                                    <input type="file" name="image[]"
                                                        class="form-control @error('image') is-invalid @enderror"
                                                        id="exampleInputPassword1" placeholder="Enter image" multiple>
                                                    @error('image')
                                                        <div class="text-danger mt-1 mb-1">{{ $message }}</div>
                                                    @enderror

                                                </div>
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <select name="city_id" class="form-control" required>
                                                        <option value="">Select</option>
                                                        @foreach ($cityData as $cdata)
                                                            <option value="{{ $cdata->id }}">{{ $cdata->city_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback">Valid.</div>
                                                    <div class="invalid-feedback">Please Enter City</div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Video Url</label>
                                                    <input type="video" name="video" class="form-control"
                                                        id="exampleInputPassword1" placeholder="Enter video link">
                                                </div>
                                            </div> --}}
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <button type="submit" class="btn btn-primary">Add</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <div class="col-md-3"></div>
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
                if (data != 0){
                    $('#subcategory').removeClass('d-none');
                }else{
                    $('#subcategory').addClass('d-none');
                    $('#maindiv').addClass('d-none');
                }

                $('#inputGroupVendor').html("");
                $.each(data, function(key, val) {
                    $('#inputGroupVendor').append("<option value ='" + val.id + "'>" + val
                        .sub_type + "</option>");
                });
                if (data[0].sale_id == 3){
                    $('#maindiv').html("");
                    $('#maindiv').removeClass('d-none');
                    $('#maindiv').append(` <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="name" class="form-control"
                                                id="" placeholder="Enter title"
                                                value="">
                    </div>`);

                } else {
                    
                    $('#maindiv').addClass('d-none');
                }


            },
            // error: function(data) {
            //     const obj = JSON.parse(data.responseText);
            //     $('#errorimg').html("");
            //     $('#errorimg').append(obj.message);
            // }
        });

    }
</script>
