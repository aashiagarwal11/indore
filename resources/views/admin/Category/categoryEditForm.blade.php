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
                                <a href="{{ route('classifiedCategoryList') }}" class="btn btn-danger m-1">View List</a>
                            </div>

                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-4 mt-3" id="message_id">
                                    @if (Session::has('message'))
                                        <p class="text-center text-danger">
                                            <b>{{ Session::get('message') }}</b>
                                        </p>
                                    @endif
                                </div>
                                <div class="col-md-4"></div>
                            </div>

                            <!-- /.card-header -->
                            <div class="row">
                                <div class="col-md-3"></div>
                                <div class="col-md-6">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Edit Classified Category <small>Form</small></h3>
                                        </div>
                                        <!-- form start -->
                                        <form id="quickForm" class="was-validated"
                                            action="{{ route('updateclassifiedCategory') }}" method="POST">
                                            @method('PUT')
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $bdata->id }}">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Category Type</label>
                                                    <input type="text" name="type" class="form-control"
                                                        id="exampleInputEmail1" placeholder="Enter Type" required
                                                        value="{{ $bdata->type }}">
                                                    <div class="valid-feedback">Valid.</div>
                                                    <div class="invalid-feedback">Please Enter Type</div>
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
