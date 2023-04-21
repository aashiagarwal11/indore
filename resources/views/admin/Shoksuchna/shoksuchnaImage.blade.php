@extends('admin.layout.dashboard')
@section('content')
    <style>
        .hoverEffect {
            position: relative;
        }

        .hoverEffect i {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            color: #000000
        }

        .hoverEffect:hover i {
            display: block;
        }

        .imgicon i {
            width: 100%;
            height: 100%;
            opacity: 0.5;
            background-color: #5e5b5b;
            position: absolute;
            padding-top: 60px;
            padding-left: 60px;
        }
    </style>
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
                                <h3 class="card-title">Shok Suchna Images</h3>

                                <form method="POST" id="imageshoksuchna" enctype='multipart/form-data'
                                    onchange="myimage()">
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
                                        @foreach ($exp as $key => $expImg)
                                            @if ($expImg != '')
                                                <div class="col-md-2 hoverEffect">
                                                    <img src="{{ $expImg }}" alt="" width="100%"
                                                        height="100%">
                                                    <div class="imgicon"
                                                        onclick="imgdelete({{ $key }},{{ $id }})">
                                                        <i class="fas fa-trash right "></i>
                                                    </div>
                                                </div>
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
        let formData = new FormData($('#imageshoksuchna')[0]);
        console.log(formData);
        $.ajax({
            url: "{{ url('addshoksuchnaImage/' . $id) }}",
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

<script>
    function imgdelete(key, id) {
        event.preventDefault();
        $.ajax({
            url: "{{ url('deleteshoksuchnaImage/') }}",
            method: "get",
            data: {
                'key': key,
                'id': id,
            },
            success: function(data) {
                $('#errorimg').html("");
                $('#errorimg').append(data.message);
                location.reload();
            },
            error: function(data) {
                const obj = JSON.parse(data.responseText);
                $('#errorimg').html("");
                $('#errorimg').append(obj.message);
            }
        });
    }
</script>
