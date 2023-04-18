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
                                <a href="{{ route('resumeList') }}" class="btn btn-danger m-1">View
                                    List</a>
                            </div>

                            <!-- /.card-header -->
                            <div class="row">
                                <div class="col-md-3"></div>
                                <div class="col-md-6">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Edit Resume <small>Form</small></h3>
                                        </div>
                                        <!-- form start -->
                                        <form id="quickForm" class="was-validated" action="{{ route('updateresume') }}"
                                            method="POST">
                                            @method('PUT')
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $bdata->id }}">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        id="exampleInputEmail1" placeholder="Enter name" required
                                                        value="{{ $bdata->name }}">
                                                    <div class="valid-feedback">Valid.</div>
                                                    <div class="invalid-feedback">Please Enter Name</div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Education</label>
                                                    <input type="text" name="education" class="form-control"
                                                        id="exampleInputEmail1" placeholder="Enter education" required
                                                        value="{{ $bdata->education }}">
                                                    <div class="valid-feedback">Valid.</div>
                                                    <div class="invalid-feedback">Please Enter Title</div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Job Experience</label>
                                                    <input type="text" name="job_experience" class="form-control"
                                                        id="exampleInputEmail1" placeholder="Enter job Experiance" required
                                                        value="{{ $bdata->job_experience }}">
                                                    <div class="valid-feedback">Valid.</div>
                                                    <div class="invalid-feedback">Please Enter job experience</div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Expectation</label>
                                                    <input type="text" name="expectation" class="form-control"
                                                        id="exampleInputEmail1" placeholder="Enter expectation"
                                                        value="{{ $bdata->expectation }}">
                                                </div>

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
