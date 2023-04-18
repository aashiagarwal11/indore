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
                                <h3 class="card-title">Requirement Images</h3>

                                <form method="POST" id="imagedata" enctype='multipart/form-data' onchange="myimage()">
                                    @csrf
                                    <input class="d-none" type="file" id="change-profile" name="image[]" multiple>
                                </form>
                                <span style="margin-left:150px;color:red" id="errorimg"></span>

                                <label for="change-profile" class="h-110px w-110px -label"
                                    style="float:right
                                ">
                                    <div class="btn btn-sm btn-primary float-left">Add Photo</div>
                                </label>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    @if (isset($exp))
                                        @foreach ($exp as $expImg)
                                            @if ($expImg != '')
                                                <div class="col-md-2">
                                                    <img src="{{ $expImg }}" alt="" width="100%"
                                                        height="100%">
                                                </div>
                                                {{-- <div>
                                                    <a class="btn btn-danger m-2" href="#">Delete</a>
                                                </div> --}}
                                            @endif
                                        @endforeach
                                    @endif

                                </div>

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
    function myimage() {
        let formData = new FormData($('#imagedata')[0]);
        console.log(formData);
        $.ajax({
            url: "{{ url('addrequirementImage/' . $id) }}",
            method: "post",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                location.reload();
            },
            error: function(data) {
                const obj = JSON.parse(data.responseText);
                $('#errorimg').append(obj.message);
            }
        });
    }
</script>
