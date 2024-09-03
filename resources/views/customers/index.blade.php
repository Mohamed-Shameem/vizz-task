@extends('layouts')

@section('content')
    <style>
        .error {
            color: red;
            margin-top: 5px;
        }

        .card-header {
            background-color: #000;
            color: white;
        }

        .btn-primary {
            background-color: #000;
            border: none;
        }

        .btn-primary:hover {
            background-color: #000;
        }

        .btn-primary:focus {
            background-color: #000 !important;
        }
    </style>
    <div class="card">
        <div class="card-header">
            <h3>Upload Customer Data</h3>
        </div>
        <div class="card-body">
            <form action="" id="fileUpload" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="form-label">Choose CSV File</label>
                    <input class="form-control" type="file" name="file" id="file">
                </div>
                <button type="submit" id="saveFile" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3>Customer Data</h3>
        </div>
        <div class="card-body">
            <table id="customers-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>DOB</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Profession</th>
                        <th>Salary</th>
                        <th>GST</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#saveFile').on('click', function() {
                var uploadFile = $('#fileUpload').submit(function(e) {
                    e.preventDefault();
                }).validate({
                    rules: {
                        file: "required",
                    },
                    messages: {
                        file: "Please select a file to upload",
                    },
                    submitHandler: function(form) {
                        var form = $('#fileUpload')[0];
                        var data = new FormData(form);
                        $.ajax({
                            url: '{{ route('customers.upload') }}',
                            type: 'POST',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: data,
                            success: function(response) {
                                $('#fileUpload')[0].reset();
                                uploadFile.resetForm();
                                if (response.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: response.message,
                                        timer: 2000
                                    });

                                }
                            },
                            error: function(e) {
                                $('#fileUpload')[0].reset();
                                uploadFile.resetForm();
                                if (e.responseJSON.success == false) {
                                    Swal.fire({
                                        icon: "error",
                                        title: e.responseJSON.errors.file,
                                        timer: 2500
                                    });

                                }
                            }
                        });
                    }
                })
            })

            /*  $('#file').on('change', function() {
                 $(this).valid();
             }); */

            let customerTable = $('#customers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('customers.getCustomers') }}',
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'dob',
                        name: 'dob'
                    },
                    {
                        data: 'age',
                        name: 'age'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'profession',
                        name: 'profession'
                    },
                    {
                        data: 'salary',
                        name: 'salary'
                    },
                    {
                        data: 'gst',
                        name: 'gst_salary'
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return '<button class="btn btn-sm btn-success calculate-gst" data-id="' +
                                data + '">Calculate GST</button>';
                        },
                        orderable: false,
                        searchable: false,
                    }
                ],
                paging: true,
                searching: true,
                order: [
                    [5, 'desc']
                ],
                lengthChange: false,
                pageLength: 10
            });

            $(document).on('click', '.calculate-gst', function() {
                var id = $(this).attr('data-id');
                $.ajax({
                    url: 'customers/getCalculation/' + id,
                    type: 'get',
                    dataType: 'JSON',
                    success: function(response) {
                        customerTable.ajax.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr[0]);
                    }

                })
            })
        });
    </script>
@endpush
