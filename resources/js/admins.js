$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let saveBtn = $("#btn-save");
    let addBtn = $('#addAdmin');

    addBtn.on('click', function () {
        showModal();
    })
    $('#categoryModal').on('hidden.bs.modal', function () {
        var $alertas = $('form#form-add-category');
        $alertas.validate().resetForm();
        $alertas.find('.error').removeClass('error');
    });

    let showModal = function (first = null, last = null, email = null, region = null, id = null) {
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
    }

    let hideModal = function () {
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


    }


    let table = $('#datatable-category').DataTable({
        pageLength: 10,
        serverSide: true,
        responsive: true,
        processing: true,
        columns: [
            {"data": 'first_name'},
            {"data": 'last_name'},
            {"data": 'email'},
            {"data": 'region'},
            {"data": 'role'},
            {"data": null, name: 'action', orderable: false, searchable: false}
        ],
        ColumnDefs: [],
        createdRow: function (row, data) {
            actions = '<button style="margin-right: 5px" type="button" class="mb-1 btn btn-warning btn-pill edit-admin"><i class=" mdi mdi-pencil mr-1"></i></button>';
            actions += '<button style="margin-left: 5px" type="button" class="mb-1 btn btn-danger btn-pill delete-admin"><i class=" mdi mdi-trash-can mr-1"></i></button>'

            $('td', row).eq(0).empty().text(data['first_name']).css('vertical-align', 'middle')
            $('td', row).eq(1).empty().text(data['last_name']).css('vertical-align', 'middle')
            $('td', row).eq(2).empty().text(data['email']).css('vertical-align', 'middle')
            $('td', row).eq(3).empty().text(data.region ? data.region.name + ' (' + data.region.postal_code + ')' : '').css('vertical-align', 'middle')
            $('td', row).eq(4).empty().text(data.role.name === 'super_admin' ? 'Superadmin' : 'Admin').css('vertical-align', 'middle')

            $('td', row).eq(5).empty().append(data.role.name === 'super_admin' ? '' : actions).css('text-align', 'center')
        },
        ajax: {
            method: 'get',
            url: '/admin/admin',
            dataType: 'json',
        }
    })

    $('body').on('click', '.edit-admin', function () {
        var data = table.row($(this).parents('tr')).data();
        console.log(data);
        showModal(data.first_name, data.last_name, data.email, data.region_id, data.id);


    });

    $('body').on('click', '#editAdmin', function () {
        if ($('form#form-add-category').valid()) {
            let firstName = $("#firstName").val();
            let lastName = $("#lastName").val();
            let email = $("#email").val();
            let region = $("#selectRegion").val();
            let id = $("#id").val();
            console.log(id);
            $('#editAdmin').html('Please Wait...');
            $('#editAdmin').attr("disabled", true);
            let formData = new FormData()
            formData.append('first_name', firstName)
            formData.append('last_name', lastName)
            formData.append('email', email)
            formData.append('region_id', region)
            $.ajax({
                method: "POST",
                url: '/admin/admin/' + id,
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function (res) {
                    hideModal();
                    $('#datatable-category').DataTable().ajax.reload(null, false);
                    $('#editAdmin').html('Save');
                    $('#editAdmin').attr("disabled", false);
                    Swal.fire(
                        'Update admin ',
                        'Admin updated successfully !',
                        'success'
                    )

                },
                error: function (res) {
                    $('#editAdmin').html('Save');
                    $('#editAdmin').attr("disabled", false);
                    let msgs = res.responseJSON.errors.email ? res.responseJSON.errors.email : '';
                    Swal.fire(
                        'Update admin ',
                        'Failed to update admin !',
                        'error'
                    )
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
            denyButtonText: `Cancel`,
        }).then((result) => {

            if (result.isConfirmed) {
                $.ajax({
                    method: "DELETE",

                    url: "/admin/admin/" + id,
                    success: function (res) {

                        $('#datatable-category').DataTable().ajax.reload(null, false);
                        Swal.fire(
                            'Delete admin',
                            'Admin deleted successfully !',
                            'success'
                        )

                    },
                    error: function (error) {


                        Swal.fire(
                            'Delete admin',
                            'Failed to delete admin  !',
                            'error',
                        )


                    }

                });
            }
        })


    });

    $('body').on('click', '#btn-save', function (event) {
        if ($('form#form-add-category').valid()) {
            let firstName = $("#firstName").val();
            let lastName = $("#lastName").val();
            let email = $("#email").val();

            let region = $('#selectRegion').val();


            saveBtn.html('Please Wait...');
            saveBtn.attr("disabled", true);


            let formData = new FormData()

            formData.append('first_name', firstName)
            formData.append('last_name', lastName)
            formData.append('email', email)
            formData.append('region_id', region)
            $.ajax({
                method: "POST",
                url: '/admin/admin',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function (res) {
                    hideModal();
                    $('#datatable-category').DataTable().ajax.reload(null, false);

                    saveBtn.html('Save');

                    saveBtn.attr("disabled", false);
                    Swal.fire(
                        'Create admin',
                        'Admin created successfully !',
                        'success'
                    )
                },
                error: function (res) {
                    let msgs = res.responseJSON.errors.email ? res.responseJSON.errors.email : '';

                    saveBtn.html('Save')
                    saveBtn.attr("disabled", false);
                    Swal.fire(
                        'Create admin',
                        'Failed to create Admin !',
                        'error'
                    )


                }
            });
        }


    });

    $('form#form-add-category').validate({
        rules: {
            firstName: {
                required: true,
                maxlength: 20,

            },
            lastName: {
                required: true,
                maxlength: 20,

            },
            email: {
                required: true,
                email: true,
                remote:{
                    url:"/admin/admin/validation/unique",
                    type:"post",
                    data:{
                        email:function (){
                            return $("#email").val();
                        },
                    }
                },
            },
            region: {
                required: true,

            },

        }
    })

})
