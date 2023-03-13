@extends('layouts.admin')

@section('content')

    <style>
        .error {
            color: red !important;
            border-color: red !important;
        }



    </style>


    <div class="row">
        <h2 style="margin-left: 30%; ">Profile Admin  {{ Auth::user()->region->name }}  </h2>
    </div>






    <div class="card" style="width: 80%; margin-left: 10%; ">
        <div class="modal-body" >
            <form id="form-add-article"  >
                <div class="form-group">
                    <div style="display: flex;">
                        <label style="width: 20%"  for="first" class="col-form-label">First name</label>
                        <input style="width: 80%"  id="firstName"  type="text" class="form-control"  disabled>

                    </div>
                    <br>
                    <div style="display: flex;">
                        <label  style="width: 20%" for="last" class="col-form-label">last name</label>
                        <input style="width: 80%" id="lastName"  type="text" class="form-control" disabled></input>

                    </div>
                    <br>
                    <div style="display: flex;">
                        <label style="width: 20%" for="email" class="col-form-label">email</label>
                        <input style="width: 80%" id="email"  type="text" class="form-control" disabled >

                    </div>
                    <br>

                    <input id="id" type="hidden" class="form-control">





                    <label for="type" class="col-form-label"></label>
                    <div class="text-center">
                        <button  type="button" class="btn btn-warning btn-update">Update</button>
                        <button   type="button" class="btn btn-warning btn-password">Change password</button>

                    </div>


                </div>
            </form>


        </div>
    </div>


    <div class="modal fade bd-example-modal-lg" id="profileModal" tabindex="-1" role="dialog"
         aria-labelledby="articleModalLabel" aria-hidden="true">
        <div class="modal-dialog  " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit profile</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-profile">
                        <div class="form-group">
                            <div style="display: flex;">
                                <label style="width: 20%"  for="first" class="col-form-label">Name:</label>
                                <input style="width: 80%"  id="first" name="first" type="text" class="form-control"  >
                                <br>

                            </div>
                            <br>
                            <div style="display: flex;">
                                <label  style="width: 20%" for="last" class="col-form-label">last</label>
                                <input style="width: 80%" id="last" name="last" type="text" class="form-control" ></input>

                            </div>
                            <br>
                            <div style="display: flex;">
                                <label style="width: 20%" for="email" class="col-form-label">email</label>
                                <input style="width: 80%" id="gmail" name="email" type="text" class="form-control"  >

                            </div>
                            <br>




                            <label for="type" class="col-form-label"></label>
                            <div class="text-center">
                                <button id="btn-update-profile" type="button" class="btn btn-warning">Edit</button>
                            </div>


                        </div>
                    </form>


                </div>

            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="ModalPassword" tabindex="-1" role="dialog"
         aria-labelledby="articleModalLabel" aria-hidden="true">
        <div class="modal-dialog  " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit profile</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="password">
                        <div class="form-group">
                            <div style="display: flex;">
                                <label style="width: 20%"  for="first" class="col-form-label">email</label>
                                <input style="width: 80%"  id="emailPassword" name="emailPassword" type="text" class="form-control"  >
                                <br>

                            </div>
                            <br>
                            <div style="display: flex;">
                                <label  style="width: 20%" for="last" class="col-form-label">Old password</label>
                                <input style="width: 80%" id="oldPassword" name="oldPassword" type="text" class="form-control" ></input>

                            </div>
                            <br>
                            <div style="display: flex;">
                                <label style="width: 20%" for="email" class="col-form-label">New password</label>
                                <input style="width: 80%" id="newPassword" name="newPassword" type="text" class="form-control"  >

                            </div>
                            <br>
                            <div style="display: flex;">
                                <label style="width: 20%" for="email" class="col-form-label">confirmation</label>
                                <input style="width: 80%" id="confirmation" name="confirmation" type="text" class="form-control"  >

                            </div>




                            <label for="type" class="col-form-label"></label>
                            <div class="text-center">
                                <button id="btn-password" type="button" class="btn btn-warning">Edit</button>
                            </div>


                        </div>
                    </form>


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

            $.ajax({
                url: "/admin/profile",
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    console.log(res.data);
                    $("#firstName").val(res.data.first_name);
                    $("#lastName").val(res.data.last_name);
                    $("#email").val(res.data.email);
                    $('#id').val(res.data.id);


                }
            });


            let showModal = function (firstName,lastname,email,id) {
                $("#profileModal").modal('show');
                $("#first").val(firstName);
                $("#last").val(lastname);
                $("#gmail").val(email);
                $('#id').val(id);

            }
            let hideModal = function () {
                $("#profileModal").modal('hide');
                $("#first").val();
                $("#last").val();
                $("#gmail").val();
                $('#id').val();
            }
            $('#profileModal').on('hidden.bs.modal', function () {
                var $alertas = $('form#form-edit-profile');
                $alertas.validate().resetForm();
                $alertas.find('.error').removeClass('error');
            });

            $('body').on('click', '.btn-update', function () {
                let id= $("#id").val();
                let firstName=$("#firstName").val();
                let lastName=$("#lastName").val();
                let email=$("#email").val();
                console.log(id, firstName,lastName,email);
                showModal(firstName,lastName,email,id);

            });
            $('body').on('click', '#btn-update-profile', function () {

                if ($('form#form-edit-profile').valid()) {

                let firstName=$("#first").val();
                let lastName=$("#last").val();
                let email=$("#gmail").val();
                let id = $("#id").val();
                console.log(firstName,lastName,email,id);


                $('#btn-received').html('Please Wait...');
                $('#btn-received').attr("disabled", true);
                let formData = new FormData()
                formData.append('first_name', firstName);
                formData.append('last_name', lastName);
                formData.append('email', email);



                Swal.fire({
                    icon: 'warning',
                    title: 'Do you want to update your profile?',
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    denyButtonText: `Cancel`,
                }).then((result) => {

                    if (result.isConfirmed) {
                        $.ajax({
                            method: "POST",
                            url: '/admin/profile/update/' + id,
                            data: formData,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            cache: false,
                            success: function (res) {
                                hideModal();
                                location.reload();

                                $('#btn-received').attr("disabled", false);
                                Swal.fire(
                                    'Update profile',
                                    'Profile updated successfully !',
                                    'success'
                                )
                            },
                            error: function (error) {

                                $('#btn-received').attr("disabled", false);
                                Swal.fire(
                                    'Update profile',
                                    'Failed to  to update profile  !',
                                    'error'
                                )


                            }
                        });
                    }
                    else {
                        $('#btn-received').html('Update');

                        $('#btn-received').attr("disabled", false);


                    }
                });
                }



            });


            <!--password-->
            $('#ModalPassword').on('hidden.bs.modal', function () {
                var $alertas = $('form#password');
                $alertas.validate().resetForm();
                $alertas.find('.error').removeClass('error');
            });
            let showModalPassword = function (id) {
                $("#ModalPassword").modal('show');
                $("#emailPassword").val('');
                $("#oldPassword").val('');
                $("#newPassword").val('');
                $("#confirmation").val('');
                $('#id').val(id);

            }
            let hideModalpassword = function () {
                $("#ModalPassword").modal('hide');
                $("#emailPassword").val('');
                $("#oldPassword").val('');
                $("#newPassword").val('');
                $("#confirmation").val('');

                $('#id').val(id);
            }
            $('body').on('click', '.btn-password', function () {
                let id= $("#id").val();
                console.log(id)

                showModalPassword(id);

            });
            $('body').on('click', '#btn-password', function () {

                if ($('form#password').valid()) {

                    let email=$("#emailPassword").val();
                    let old=$("#oldPassword").val();
                    let newpassword=$("#newPassword").val();
                    let confirm=$("#confirmation").val();

                    let id = $("#id").val();
                    console.log(email,old,newpassword,confirm,id);


                    $('#btn-received').html('Please Wait...');
                    $('#btn-received').attr("disabled", true);
                    let formData = new FormData()
                    formData.append('email', email);
                    formData.append('old_password', old);
                    formData.append('password', newpassword);
                    formData.append('confirm_password', confirm);




                    Swal.fire({
                        icon: 'warning',
                        title: 'Do you want to update your password?',
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        denyButtonText: `Cancel`,
                    }).then((result) => {

                        if (result.isConfirmed) {
                            $.ajax({
                                method: "POST",
                                url: '/admin/profile/password/' + id,
                                data: formData,
                                dataType: 'json',
                                processData: false,
                                contentType: false,
                                cache: false,
                                success: function (res) {
                                    hideModalpassword();
                                    location.reload();

                                    $('#btn-password').attr("disabled", false);
                                    Swal.fire(
                                        'Update password',
                                        'Password updated successfully !',
                                        'success'
                                    )
                                },
                                error: function (error) {

                                    $('#btn-password').attr("disabled", false);
                                    Swal.fire(
                                        'Update password',
                                        'Failed to  to update password  !',
                                        'error'
                                    )


                                }
                            });
                        }
                        else {
                            $('#btn-password').html('Update');

                            $('#btn-password').attr("disabled", false);


                        }
                    });
                }



            });








            $('form#password').validate({
                rules: {
                    emailPassword: {
                        required: true,
                        email: true,


                    },
                    oldPassword: {
                        required: true,

                    },
                    newPassword: {
                        required: true,
                    },
                    confirmation:{
                        required: true,
                        equalTo:'#newPassword',

                    }
                }
            })



            $('form#form-edit-profile').validate({
                rules: {
                    first: {
                        required: true,
                        maxlength: 20,

                    },
                    last: {
                        required: true,
                        maxlength: 20,

                    },
                    email: {
                        required: true,
                        email: true,
                    },
                }
            })

        })

    </script>


    <!-- end bootstrap model -->












@endsection()
