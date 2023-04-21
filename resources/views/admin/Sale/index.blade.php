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
                                <h3 class="card-title">Sale List</h3>
                            </div>

                            <div class="card-header">
                                <a href="{{ route('getsaleForm') }}" class="btn btn-primary m-1">Add
                                    New</a>
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4" id="message_id">
                                        @if (Session::has('message'))
                                            <h5
                                                class="text-center alert {{ Session::get('alert-class', 'alert-primary') }}">
                                                {{ Session::get('message') }}
                                            </h5>
                                        @endif
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Sale Category</th>
                                            <th>Sale Sub Category</th>
                                            <th>Vendor Name</th>
                                            <th>Owner or Broker</th>
                                            <th>Vehicle Sighting</th>
                                            <th>Property Location</th>
                                            <th>Price</th>
                                            <th>Brand</th>
                                            <th>Model Name</th>
                                            <th>Model Year</th>
                                            <th>View</th>
                                            <th>Edit</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($birthdayData as $bday)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $bday['sale_id'] }}</td>
                                                <td>{{ $bday['sub_cat_id'] }}</td>
                                                <td>
                                                    <a href="{{ url('saleImage/' . $bday['id']) }}"
                                                        class="btn btn-danger m-1">View</a>
                                                </td>
                                                <td>
                                                    <a href="{{ url('getsaleEditForm/' . $bday['id']) }}"
                                                        class="btn btn-primary m-1">Edit</a>
                                                </td>
                                                <td>
                                                    @if ($bday['status'] == 1)
                                                        <button class="btn btn-secondary m-1">Accepted</button>
                                                    @elseif ($bday['status'] == 2)
                                                        <button class="btn btn-secondary m-1">Denied</button>
                                                    @else
                                                        <a href="{{ url('acceptsale/' . $bday['id']) }}"
                                                            class="btn btn-success m-1">
                                                            Accept
                                                        </a>

                                                        <a href="{{ url('denysale/' . $bday['id']) }}"
                                                            class="btn btn-danger m-1">
                                                            Deny
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>id</th>
                                            <th>Sale Category</th>
                                            <th>Sale Sub Category</th>
                                            <th>Vendor Name</th>
                                            <th>Owner or Broker</th>
                                            <th>Vehicle Sighting</th>
                                            <th>Property Location</th>
                                            <th>Price</th>
                                            <th>Brand</th>
                                            <th>Model Name</th>
                                            <th>Model Year</th>
                                            <th>View</th>
                                            <th>Edit</th>
                                            <th>Status</th>
                                        </tr>
                                    </tfoot>
                                </table>
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
