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
                                <h3 class="card-title">Directory List</h3>
                            </div>

                            <div class="card-header">
                                <a href="{{ route('getdirectoryForm') }}" class="btn btn-primary m-1">Add
                                    new</a>
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
                                            <th>biz_name</th>
                                            <th>contact_per1</th>
                                            <th>number1</th>
                                            <th>city</th>
                                            <th>state</th>
                                            <th>View</th>
                                            <th>Edit</th>
                                            <th>Status</th>
                                            <th>category</th>
                                            <th>contact_per2</th>
                                            <th>contact_per3</th>
                                            <th>number2</th>
                                            <th>number3</th>
                                            <th>address</th>
                                            <th>detail</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($birthdayData as $bday)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $bday['biz_name'] }}</td>
                                                <td>{{ $bday['contact_per1'] }}</td>
                                                <td>{{ $bday['number1'] }}</td>
                                                <td>{{ $bday['city'] }}</td>
                                                <td>{{ $bday['state'] }}</td>
                                                <td>
                                                    <a href="{{ url('directoryImage/' . $bday['id']) }}"
                                                        class="btn btn-danger m-1">View</a>
                                                </td>
                                                <td>
                                                    <a href="{{ url('getdirectoryEditForm/' . $bday['id']) }}"
                                                        class="btn btn-primary m-1">Edit</a>
                                                </td>
                                                <td>
                                                    @if ($bday['status'] == 1)
                                                        <button class="btn btn-secondary m-1">Accepted</button>
                                                    @elseif ($bday['status'] == 2)
                                                        <button class="btn btn-secondary m-1">Denied</button>
                                                    @else
                                                        <a href="{{ url('acceptdirectory/' . $bday['id']) }}"
                                                            class="btn btn-success m-1">
                                                            Accept
                                                        </a>

                                                        <a href="{{ url('denydirectory/' . $bday['id']) }}"
                                                            class="btn btn-danger m-1">
                                                            Deny
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ $bday['category'] }}</td>
                                                <td>{{ $bday['contact_per2'] }}</td>
                                                <td>{{ $bday['contact_per3'] }}</td>
                                                <td>{{ $bday['number2'] }}</td>
                                                <td>{{ $bday['number3'] }}</td>
                                                <td>{{ $bday['address'] }}</td>
                                                <td>{{ $bday['detail'] }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>id</th>
                                            <th>biz_name</th>
                                            <th>contact_per1</th>
                                            <th>number1</th>
                                            <th>city</th>
                                            <th>state</th>
                                            <th>View</th>
                                            <th>Edit</th>
                                            <th>Status</th>
                                            <th>category</th>
                                            <th>contact_per2</th>
                                            <th>contact_per3</th>
                                            <th>number2</th>
                                            <th>number3</th>
                                            <th>address</th>
                                            <th>detail</th>

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
