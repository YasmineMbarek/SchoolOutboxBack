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
