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
                                            <th>Posted By</th>
                                            <th>WhatsApp Number</th>
                                            <th>Call Number</th>
                                            <th>View</th>
                                            <th>Edit</th>
                                            <th>Status</th>
                                            <th>Property Location</th>
                                            <th>Price</th>
                                            <th>Brand</th>
                                            <th>Model Name</th>
                                            <th>Model Year</th>
                                            <th>Vehicle Sighting</th>
                                            <th>Fuel Type</th>
                                            <th>Seater</th>
                                            <th>Kilometer Running</th>
                                            <th>Insurance Period</th>
                                            <th>Other Information</th>
                                            <th>Size Length Width</th>
                                            <th>Room Quantity</th>
                                            <th>Kitchen</th>
                                            <th>Hall</th>
                                            <th>Lat Bath</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($birthdayData as $bday)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $bday['type'] }}</td>
                                                <td>{{ $bday['sub_type'] }}</td>
                                                <td>{{ $bday['vendor_name'] }}</td>
                                                <td>{{ $bday['owner_or_broker'] }}</td>
                                                <td>{{ $bday['whatsapp_no'] }}</td>
                                                <td>{{ $bday['call_no'] }}</td>
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

                                                <td>{{ $bday['property_location'] }}</td>
                                                <td>{{ $bday['price'] }}</td>
                                                <td>{{ $bday['brand'] }}</td>
                                                <td>{{ $bday['model_name'] }}</td>
                                                <td>{{ $bday['model_year'] }}</td>
                                                <td>{{ $bday['vehicle_sighting'] }}</td>
                                                <td>{{ $bday['fuel_type'] }}</td>
                                                <td>{{ $bday['seater'] }}</td>
                                                <td>{{ $bday['kilometer_running'] }}</td>
                                                <td>{{ $bday['insurance_period'] }}</td>
                                                <td>{{ $bday['other_information'] }}</td>
                                                <td>{{ $bday['size_length_width'] }}</td>
                                                <td>{{ $bday['room_qty'] }}</td>
                                                <td>{{ $bday['kitchen'] }}</td>
                                                <td>{{ $bday['hall'] }}</td>
                                                <td>{{ $bday['lat_bath'] }}</td>
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
                                            <th>WhatsApp Number</th>
                                            <th>Call Number</th>
                                            <th>View</th>
                                            <th>Edit</th>
                                            <th>Status</th>
                                            <th>Property Location</th>
                                            <th>Price</th>
                                            <th>Brand</th>
                                            <th>Model Name</th>
                                            <th>Model Year</th>
                                            <th>Vehicle Sighting</th>
                                            <th>Fuel Type</th>
                                            <th>Seater</th>
                                            <th>Kilometer Running</th>
                                            <th>Insurance Period</th>
                                            <th>Other Information</th>
                                            <th>Size Length Width</th>
                                            <th>Room Quantity</th>
                                            <th>Kitchen</th>
                                            <th>Hall</th>
                                            <th>Lat Bath</th>
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
