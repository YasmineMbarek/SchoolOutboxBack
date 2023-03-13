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

    </style>


    <div class="row">
        <h2>Articles {{ Auth::user()->region->name }}  </h2>
    </div>






    <div class="">
        <table class="table table-bordered" id="datatable-category">
            <thead>
            <tr>


                <th style=" width: 20%;">Name</th>
                <th style=" width: 15%;">Deposit date</th>



                <th style=" width: 20%;">State </th>
                <th style=" width: 30%;">Donated by</th>

                <th style=" width: 10%;">Actions</th>


            </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade bd-example-modal-lg" id="articleModal" tabindex="-1" role="dialog"
         aria-labelledby="articleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Article details</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-add-article">
                        <div class="form-group">
                            <div style="display: flex;">
                                <label style="width: 20%"  for="name" class="col-form-label">Name:</label>
                                <input style="width: 80%"  id="name" type="text" class="form-control"  disabled>
                            </div>
                            <br>
                            <div style="display: flex;">
                                <label  style="width: 20%" for="description" class="col-form-label">Description:</label>
                                <textarea style="width: 80%" id="description" type="text" class="form-control" disabled></textarea>
                            </div>
                            <br>
                            <div style="display: flex;">
                                <label style="width: 20%" for="state" class="col-form-label">State:</label>
                                <input style="width: 80%" id="state" type="text" class="form-control" disabled >
                            </div>
                            <br>
                            <div style="display: flex;">
                                <label style="width: 20%"for="donated-by" class="col-form-label">Donated by:</label>
                                <input style="width: 80%" id="donated-by" type="text"class="form-control" disabled>
                            </div>

                            <br>
                            <div style="display: flex;">
                                <label style="width: 20%"for="type" class="col-form-label">Deposit date:</label>
                                <input style="width: 80%" id="deposit" type="text" class="form-control"  disabled>
                            </div>
                            <br>
                            <div id="theDiv" style="display: flex;">
                            </div>


                            <label for="type" class="col-form-label"></label>
                            <input id="id" type="hidden" class="form-control">
                            <div class="text-center">
                                <button id="btn-received" type="button" class="btn btn-success">Received</button>
                                <button id="btn-delete" type="button" class="btn btn-danger" data-bs-dismiss="modal">Delete</button>
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

            let saveBtn = $("#btn-save");


            let showModal = function (name,description,state,email,status,article,firstName,lastName,depositDate,image ,id) {
                $("#articleModal").modal('show');

                $('#articleModalLabel').text('Edit category');
                $("#name").val(name);
                $("#description").val(description);
                $("#state").val(state);
                $('#donated-by').val(  firstName+ '  '+ lastName + ' ' + '('+ email + ')');
                $("#deposit").val(depositDate);
                $('#selectArticle').val(article);

                $("#theDiv").empty()

                let i;
                for( i=0 ; i<image.length ; i++){
                    var source = "{!! asset('" + image[i].path + " ') !!}";
                    $("#theDiv").append('<img id="'+i.id+'" src='+source+' width="320px" height="200px"  class="image" />')}
                console.log(image.length);




                $('#id').val(id);

                $('#modal-title').text('Edit region');
                $('#btn-received').html('Update');

                $('#btn-received').attr("disabled", false);


            }

            let hideModal = function () {
                $("#articleModal").modal('hide');
                $("#name").val('');
                $("#description").val('');
                $("#state").val('');
                $("#email").val('');
                $("#status").val('');
                $("#deposit").val('');
                $("#theDiv").empty()

                $('#id').val('');

                $('#btn-received').html('Update');
                $('#btn-received').attr("disabled", false);
            }


            let table = $('#datatable-category').DataTable({
                pageLength: 10,
                serverSide: true,
                responsive: true,
                processing: true,

                columns: [
                    {"data": 'name'},
                    {"data": 'deposit_date'},
                    {"data": 'state'},
                    {"data": 'customer'},


                    {"data": null, name: 'action', orderable: false, searchable: false}
                ],
                createdRow: function (row, data) {
                    let actions = '<button style="margin-right: 5px" type="button" class="mb-1 btn btn-warning btn-pill edit-article"><i class=" mdi mdi-pencil mr-1"></i></button>';


                    $('td', row).eq(0).empty().text(data['name'])
                    $('td', row).eq(1).empty().text(data['deposit_date'])
                    $('td', row).eq(2).empty().text(data['state'])
                    $('td', row).eq(3).empty().text(data.customer.first_name +' '+data.customer.last_name+ ' (' + data.customer.email + ')')


                    $('td', row).eq(4).empty().append(actions).css('text-align', 'center')


                },

                ajax: {
                    method: 'get',
                    url: '/admin/article',
                    dataType: 'json',
                }
            })

            $('body').on('click', '.edit-article', function () {
                var data = table.row($(this).parents('tr')).data();
                console.log(data.pictures)
                showModal(data.name,data.description,data.state,data.customer.email,data.status,data.status,
                    data.customer.first_name,data.customer.last_name,data.deposit_date,data.pictures,data.id);


            });

            $('body').on('click', '#btn-received', function () {


                let status = 'received';
                //console.log(status);
                let id = $("#id").val();


                $('#btn-received').html('Please Wait...');
                $('#btn-received').attr("disabled", true);
                let formData = new FormData()
                formData.append('status', status)
                Swal.fire({
                    icon: 'warning',
                    title: 'Do you want to update this article status?',
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    denyButtonText: `Cancel`,
                }).then((result) => {

                    if (result.isConfirmed) {
                        $.ajax({
                            method: "POST",
                            url: '/admin/article/status/' + id,
                            data: formData,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            cache: false,
                            success: function (res) {
                                hideModal();
                                $('#datatable-category').DataTable().ajax.reload(null, false);
                                $('#btn-received').html('Update');

                                $('#btn-received').attr("disabled", false);
                                Swal.fire(
                                    'Article accepted',
                                    'This article is now available for public ',
                                    'success'
                                )
                            },
                            error: function (error) {
                                $('#btn-received').html('Update');

                                $('#btn-received').attr("disabled", false);
                                Swal.fire(
                                    'Accept article',
                                    'Failed to accept article  !',
                                    'error'
                                )


                            }
                        });
                    }
                    else {
                        $('#btn-received').html('Update');

                        $('#btn-received').attr("disabled", false);


                    }
                })


            });
            $('body').on('click', '#btn-delete', function () {
                let id = $("#id").val();
                // console.log(id);
                Swal.fire({
                    icon: 'warning',
                    title: 'Do you want to delete this article?',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    denyButtonText: `Cancel`,
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({
                            method: "DELETE",

                            url: "/admin/article/delete/" + id,
                            success: function (res) {

                                $('#datatable-category').DataTable().ajax.reload(null, false);
                                Swal.fire(
                                    'Delete article',
                                    'Article deleted successfully !',
                                    'success'
                                )

                            },
                            error: function (error) {
                                Swal.fire(
                                    'Delete article',
                                    'Failed to delete article  !',
                                    'error'
                                )


                            }

                        });
                    }
                    else {
                        $('#btn-delete').html('Delete');

                        $('#btn-delete').attr("disabled", false);


                    }
                })

            });


            $('form#form-add-category').validate({
                rules: {
                    type: {}
                }
            })

        })

    </script>


    <!-- end bootstrap model -->












@endsection()
