@extends('layouts.admin')

@section('content')
    <style>
        .error {
            color: red !important;
            border-color: red !important;
        }

        table {
            width: 100% !important;
        }

        th {
            width: 20%;
        }
    </style>
    <div class="row">
        <h2>Admin</h2>
    </div>

    <div style="margin-bottom: 15px" class="d-flex justify-content-end">
        <button type="button" id="addAdmin" class="btn btn-primary">
            Create new admin
        </button>
    </div>

    <div>
        <table class="table table-bordered" id="datatable-category">
            <thead>
            <tr>
                <th>First name</th>
                <th>Last name</th>
                <th>Email</th>
                <th>Region</th>
                <th>Role</th>
                <th>Actions</th>


            </tr>
            </thead>
        </table>
    </div>


    {{--  Modal  --}}
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog"
         aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Add Admin</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-add-category">
                        <div class="form-group">

                            <label for="type" class="col-form-label">First name</label>
                            <input id="firstName" name="firstName" type="text" class="form-control">
                            <br>
                            <label for="type" class="col-form-label">Last name</label>
                            <input id="lastName" name="lastName" type="text" class="form-control">
                            <br>
                            <label for="type" class="col-form-label">email</label>
                            <input id="email" name="email" type="text" class="form-control">
                            <br>
                            <label for="region" class="col-form-label">Region</label>
                            <select name="region" class="form-control" id="selectRegion" style="height: 40px;">
                                <option value="0" selected disabled>Choose a region</option>
                                @foreach($regions as $region)
                                    <option name="region"
                                            value="{{$region->id}}">{{$region->name . ' (' . $region->postal_code . ')'}}</option>

                                @endforeach
                            </select>
                            <input id="id" type="hidden" class="form-control">

                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" id="closeModal" class="btn btn-secondary " data-bs-dismiss="modal">Close
                    </button>
                    <button id="btn-save" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script >
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var saveBtn = $("#btn-save");
            var addBtn = $('#addAdmin');
            addBtn.on('click', function () {
                showModal();
            });
            $('#categoryModal').on('hidden.bs.modal', function () {
                var $alertas = $('form#form-add-category');
                $alertas.validate().resetForm();
                $alertas.find('.error').removeClass('error');
            });

            var showModal = function showModal() {
                var first = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
                var last = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
                var email = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
                var region = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
                var id = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;
                $("#categoryModal").modal('show');

                if (first) {
                    $('.error').text('');
                    $('#categoryModalLabel').text('Edit admin');
                    $('#firstName').val(first);
                    $('#lastName').val(last);
                    $('#email').val(email);
                    $('#selectRegion').val(region);
                    $('#id').val(id);
                    $('#btn-save').attr('id', 'editAdmin');
                    $('#editCategory').text('Save');
                    $('#modal-title').text('Edit admin');
                } else {
                    $('.error').text('');
                    $('#categoryModalLabel').text('Add admin');
                    $('#firstName').val(first);
                    $('#lastName').val(last);
                    $('#email').val(email);
                    $('#selectRegion').val(0);
                    $('#id').val(id);
                    $('#editAdmin').attr('id', 'btn-save');
                    $('#btn-save').text('Save');
                    $('#modal-title').text('Add admin');
                }
            };

            var hideModal = function hideModal() {
                $("#categoryModal").modal('hide');
                $('#firstName').val('');
                $('#lastName').val('');
                $('#email').val('');
                $('#id').val('');
                $('#selectRegion-error').text('');
                $('#firstName-error').text('');
                $('#lastName-error').text('');
                $('#email-error').text('');
                $('#selectRegion').val(0);
            };

            var table = $('#datatable-category').DataTable({
                pageLength: 10,
                serverSide: true,
                responsive: true,
                processing: true,
                columns: [{
                    "data": 'first_name'
                }, {
                    "data": 'last_name'
                }, {
                    "data": 'email'
                }, {
                    "data": 'region'
                }, {
                    "data": 'role'
                }, {
                    "data": null,
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                ColumnDefs: [],
                createdRow: function createdRow(row, data) {
                    actions = '<button style="margin-right: 5px" type="button" class="mb-1 btn btn-warning btn-pill edit-admin"><i class=" mdi mdi-pencil mr-1"></i></button>';
                    actions += '<button style="margin-left: 5px" type="button" class="mb-1 btn btn-danger btn-pill delete-admin"><i class=" mdi mdi-trash-can mr-1"></i></button>';
                    $('td', row).eq(0).empty().text(data['first_name']).css('vertical-align', 'middle');
                    $('td', row).eq(1).empty().text(data['last_name']).css('vertical-align', 'middle');
                    $('td', row).eq(2).empty().text(data['email']).css('vertical-align', 'middle');
                    $('td', row).eq(3).empty().text(data.region ? data.region.name + ' (' + data.region.postal_code + ')' : '').css('vertical-align', 'middle');
                    $('td', row).eq(4).empty().text(data.role.name === 'super_admin' ? 'Superadmin' : 'Admin').css('vertical-align', 'middle');
                    $('td', row).eq(5).empty().append(data.role.name === 'super_admin' ? '' : actions).css('text-align', 'center');
                },
                ajax: {
                    method: 'get',
                    url: '/admin/admin',
                    dataType: 'json'
                }
            });
            $('body').on('click', '.edit-admin', function () {
                var data = table.row($(this).parents('tr')).data();
                console.log(data);
                showModal(data.first_name, data.last_name, data.email, data.region_id, data.id);
            });
            $('body').on('click', '#editAdmin', function () {
                if ($('form#form-add-category').valid()) {
                    var firstName = $("#firstName").val();
                    var lastName = $("#lastName").val();
                    var email = $("#email").val();
                    var region = $("#selectRegion").val();
                    var id = $("#id").val();
                    console.log(id);
                    $('#editAdmin').html('Please Wait...');
                    $('#editAdmin').attr("disabled", true);
                    var formData = new FormData();
                    formData.append('first_name', firstName);
                    formData.append('last_name', lastName);
                    formData.append('email', email);
                    formData.append('region_id', region);
                    $.ajax({
                        method: "POST",
                        url: '/admin/admin/' + id,
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function success(res) {
                            hideModal();
                            $('#datatable-category').DataTable().ajax.reload(null, false);
                            $('#editAdmin').html('Save');
                            $('#editAdmin').attr("disabled", false);
                            Swal.fire('Update admin ', 'Admin updated successfully !', 'success');
                        },
                        error: function error(res) {
                            $('#editAdmin').html('Save');
                            $('#editAdmin').attr("disabled", false);
                            var msgs = res.responseJSON.errors.email ? res.responseJSON.errors.email : '';
                            Swal.fire('Update admin ', 'Failed to update admin !', 'error');
                        }
                    });
                }
            });
            $('body').on('click', '.delete-admin', function () {
                var data = table.row($(this).parents('tr')).data();
                var id = data.id;
                Swal.fire({
                    title: 'Do you want to delete this admin?',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    denyButtonText: "Cancel"
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "DELETE",
                            url: "/admin/admin/" + id,
                            success: function success(res) {
                                $('#datatable-category').DataTable().ajax.reload(null, false);
                                Swal.fire('Delete admin', 'Admin deleted successfully !', 'success');
                            },
                            error: function error(_error) {
                                Swal.fire('Delete admin', 'Failed to delete admin  !', 'error');
                            }
                        });
                    }
                });
            });
            $('body').on('click', '#btn-save', function (event) {
                if ($('form#form-add-category').valid()) {
                    var firstName = $("#firstName").val();
                    var lastName = $("#lastName").val();
                    var email = $("#email").val();
                    var region = $('#selectRegion').val();
                    saveBtn.html('Please Wait...');
                    saveBtn.attr("disabled", true);
                    var formData = new FormData();
                    formData.append('first_name', firstName);
                    formData.append('last_name', lastName);
                    formData.append('email', email);
                    formData.append('region_id', region);
                    $.ajax({
                        method: "POST",
                        url: '/admin/admin',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function success(res) {
                            hideModal();
                            $('#datatable-category').DataTable().ajax.reload(null, false);
                            saveBtn.html('Save');
                            saveBtn.attr("disabled", false);
                            Swal.fire('Create admin', 'Admin created successfully !', 'success');
                        },
                        error: function error(res) {
                            var msgs = res.responseJSON.errors.email ? res.responseJSON.errors.email : '';
                            saveBtn.html('Save');
                            saveBtn.attr("disabled", false);
                            Swal.fire('Create admin', 'Failed to create Admin !', 'error');
                        }
                    });
                }
            });
            $('form#form-add-category').validate({
                rules: {
                    firstName: {
                        required: true,
                        maxlength: 20
                    },
                    lastName: {
                        required: true,
                        maxlength: 20
                    },
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            url: "/admin/admin/validation/unique",
                            type: "post",
                            data: {
                                email: function () {
                                    return $("#email").val();
                                }, id: function () {
                                    return $("#id").val();
                                }
                            }
                        }
                    },
                    region: {
                        required: true
                    }
                }
            });
        });
    </script>
@endsection()
