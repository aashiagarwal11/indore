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
                                <a href="{{ route('directoryList') }}" class="btn btn-danger m-1">View
                                    List</a>
                            </div>

                            <!-- /.card-header -->
                            <div class="row">
                                <div class="col-md-3"></div>
                                <div class="col-md-6">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Edit Directory <small>Form</small></h3>
                                        </div>
                                        <!-- form start -->
                                        <form id="quickForm" class="was-validated" action="{{ route('updatedirectory') }}"
                                            method="POST">
                                            @method('PUT')
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $bdata->id }}">
                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">Business Name</label>
                                                            <input type="text" name="biz_name" class="form-control"
                                                                id="exampleInputEmail1" placeholder="Enter Business Name"
                                                                required value="{{ $bdata->biz_name }}">
                                                            <div class="valid-feedback">Valid.</div>
                                                            <div class="invalid-feedback">Please Enter Business Name</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>City</label>
                                                            <select name="city_id" class="form-control" required>
                                                                <option value="">Select</option>
                                                                @foreach ($cityData as $cdata)
                                                                    <option value="{{ $cdata->id }}"
                                                                        {{ $cdata->id == $bdata->city_id ? 'selected' : '' }}>
                                                                        {{ $cdata->city_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div class="valid-feedback">Valid.</div>
                                                            <div class="invalid-feedback">Please Enter City</div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Contact Person1</label>
                                                            <input type="text" name="contact_per1" class="form-control"
                                                                id="exampleInputPassword1" placeholder="Enter contact Per1"
                                                                value="{{ $bdata->contact_per1 }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Number1</label>
                                                            <input type="text" name="number1" class="form-control"
                                                                id="exampleInputPassword1" placeholder="Enter Number1"
                                                                value="{{ $bdata->number1 }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Category</label>
                                                            <input type="text" name="category" class="form-control"
                                                                id="exampleInputPassword1" placeholder="Enter category"
                                                                value="{{ $bdata->category }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">City</label>
                                                            <input type="text" name="city" class="form-control"
                                                                id="exampleInputPassword1" placeholder="Enter city"
                                                                value="{{ $bdata->city }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">State</label>
                                                            <input type="text" name="state" class="form-control"
                                                                id="exampleInputPassword1" placeholder="Enter state"
                                                                value="{{ $bdata->state }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Contact Person2</label>
                                                            <input type="text" name="contact_per2"
                                                                class="form-control" id="exampleInputPassword1"
                                                                placeholder="Enter Contact Person2"
                                                                value="{{ $bdata->contact_per2 }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Contact Person3</label>
                                                            <input type="text" name="contact_per3"
                                                                class="form-control" id="exampleInputPassword1"
                                                                placeholder="Enter Contact Person3"
                                                                value="{{ $bdata->contact_per3 }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Number2</label>
                                                            <input type="text" name="number2" class="form-control"
                                                                id="exampleInputPassword1" placeholder="Enter Number2"
                                                                value="{{ $bdata->number2 }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Number3</label>
                                                            <input type="text" name="number3" class="form-control"
                                                                id="exampleInputPassword1" placeholder="Enter Number3"
                                                                value="{{ $bdata->number3 }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Address</label>
                                                            <input type="text" name="address" class="form-control"
                                                                id="exampleInputPassword1" placeholder="Enter Address"
                                                                value="{{ $bdata->address }}">
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">Detail</label>
                                                            <input type="text" name="detail" class="form-control"
                                                                id="exampleInputPassword1" placeholder="Enter Detail"
                                                                value="{{ $bdata->detail }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <button type="submit" class="btn btn-primary">Update</button>
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
