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
        <h2>Categories</h2>
    </div>


    <div style="margin-bottom: 15px" class="d-flex justify-content-end">
        <button type="button" id="addCategory" class="btn btn-primary">
            Create new category
        </button>
    </div>



    <div class="">
        <table class="table table-bordered" id="datatable-category">
            <thead>
            <tr>
                <th>Type</th>
                <th>Number of articles</th>

                <th>Actions</th>
            </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog"
         aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">category</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-add-category">
                        <div class="form-group">
                            <label for="type" class="col-form-label">type</label>
                            <input id="type" name="type" type="text" class="form-control">

                            <input id="id" type="hidden" class="form-control">

                        </div>
                    </form>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="editCategory" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var saveBtn = $("#btn-save");
            var addBtn = $('#addCategory');
            addBtn.on('click', function () {
                showModal();
            });

            var showModal = function showModal() {
                var type = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
                var id = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
                $("#categoryModal").modal('show');

                if (type) {
                    $('#categoryModalLabel').text('Edit category');
                    $('#type').val(type);
                    $('#id').val(id);
                    saveBtn.attr('id', 'editCategory');
                    //$('#form-add-category').removeData('validator')
                    //$( "#form-add-category" ).rules( "remove" );
                    $('#editCategory').text('Save');
                    $('#modal-title').text('Edit category');
                } else {
                    $('#categoryModalLabel').text('Add category');
                    $('#type').val("");
                    $('#id').val(" ");
                    $('#editCategory').attr('id', 'btn-save');
                    $('#btn-save').text('Save');
                    //$('#form-edit-category').removeData('validator')
                    //$( "#form-edit-category" ).rules( "remove" );


                    $('#modal-title').text('Add category');
                }
            };

            var hideModal = function hideModal() {
                $("#categoryModal").modal('hide');
                $('#type').val('');
            };

            $('#categoryModal').on('hidden.bs.modal', function () {

                    var $alertas = $('form#form-add-category');






                $alertas.validate().resetForm();
                $alertas.find('.error').removeClass('error');
            });
            var table = $('#datatable-category').DataTable({
                pageLength: 10,
                serverSide: true,
                responsive: true,
                processing: true,
                columns: [{
                    "data": 'type'
                }, {
                    "data": 'articles_count'
                }, {
                    "data": null,
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                //order: [[0, 'desc']],
                createdRow: function createdRow(row, data) {
                    var actions = '<button style="margin-right: 5px" type="button" class="mb-1 btn btn-warning btn-pill edit-category"><i class=" mdi mdi-pencil mr-1"></i></button>';
                    actions += '<button style="margin-left: 5px" type="button" class="mb-1 btn btn-danger btn-pill delete-category"><i class=" mdi mdi-trash-can mr-1"></i></button>';
                    $('td', row).eq(0).empty().text(data['type']);
                    $('td', row).eq(1).empty().text(data['articles_count']);
                    $('td', row).eq(2).empty().append(data['type'] == 'Other' ? '' : actions).css('text-align', 'center');
                },
                ajax: {
                    method: 'get',
                    url: '/admin/category',
                    dataType: 'json'
                }
            });
            $('body').on('click', '.edit-category', function () {
                var data = table.row($(this).parents('tr')).data();

                showModal(data.type, data.id);
            });

            $('body').on('click', '#editCategory', function () {
                if ($('form#form-add-category').valid()) {
                    var type = $("#type").val();
                    var id = $("#id").val();
                    $('#editCategory').html('Please Wait...');
                    $('#editCategory').attr("disabled", true);
                    var formData = new FormData();
                    formData.append('type', type);
                    $.ajax({
                        method: "POST",
                        url: '/admin/category/' + id,
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function success(res) {
                            hideModal();
                            $('#datatable-category').DataTable().ajax.reload(null, false);
                            $('#editCategory').html('Save');
                            $('#editCategory').attr("disabled", false);
                            Swal.fire('Update category', 'Category updated successfully !', 'success');
                        },
                        error: function error(res) {
                            $('#editCategory').html('Save');
                            $('#editCategory').attr("disabled", false);
                            var msgs = res.responseJSON.errors.type ? res.responseJSON.errors.type : '';
                            Swal.fire('Update category', 'Failed to update category !', 'error');
                        }
                    });
                }
            });

            $('body').on('click', '.delete-category', function () {
                var data = table.row($(this).parents('tr')).data();
                var id = data.id;
                console.log(id);
                Swal.fire({
                    title: 'Do you want to delete this category?',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    denyButtonText: "Cancel"
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "DELETE",
                            url: "/admin/category/" + id,
                            success: function success(res) {
                                $('#datatable-category').DataTable().ajax.reload(null, false);
                                Swal.fire('Delete category', 'Category deleted successfully !', 'success');
                            },
                            error: function error(_error) {
                                Swal.fire('Delete category', 'Failed to delete category  !', 'error');
                            }
                        });
                    }
                });
            });

            $('body').on('click', '#btn-save', function (event) {
                if ($('form#form-add-category').valid()) {
                    var type = $("#type").val();
                    $('#btn-save').html('Please Wait...');
                    $('#btn-save').attr("disabled", true);
                    var formData = new FormData();
                    formData.append('type', type);
                    $.ajax({
                        method: "POST",
                        url: '/admin/category',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function success(res) {
                            hideModal();
                            $('#datatable-category').DataTable().ajax.reload(null, false);
                            $('#btn-save').attr("Save");
                            $('#btn-save').attr("disabled", false);
                            Swal.fire('Create category', 'Category created successfully !', 'success');
                        },
                        error: function error(res) {
                            var msgs = res.responseJSON.errors.type ? res.responseJSON.errors.type : '';

                            if (res.status == 422) {
                                $('.invalid-feedback').fadeIn().html(res.responseJSON.errors.type);
                            }

                            $('#btn-save').html('Save');
                            $('#btn-save').attr("disabled", false);
                            Swal.fire('Create category', 'Failed to create Category !', 'error');
                        }
                    });
                }
            });

            $('form#form-edit-category').validate({
                rules: {
                    type: {
                        required: true,
                        digits: false,
                        maxlength: 10,
                        minlength: 4,
                        remote: {
                            url: "/admin/category/validation/unique",
                            type: "post",
                            data: {
                                type: function () {
                                    return $("#type").val();
                                },
                                id: function () {
                                    return $("#id").val();
                                }
                            }
                        }
                    }
                }
            });

            $('form#form-add-category').validate({
                rules: {
                    type: {
                        required: true,
                        digits: false,
                        maxlength: 10,
                        minlength: 4,
                        remote: {
                            url: "/admin/category/validation/unique",
                            type: "post",
                            data: {
                                type: function () {
                                    return $("#type").val();
                                },
                                id: function () {
                                    return $("#id").val();
                                }
                            }
                        }
                    }
                }
            });

        });

    </script>
    <!-- end bootstrap model -->












@endsection()
