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
        <h2>Demands   </h2>
    </div>






    <div class="">
        <table class="table table-bordered" id="datatable-category">
            <thead>
            <tr>
                <th style=" width: 20%;">Name</th>
                <th style=" width: 15%;">Demand date</th>
                <th style=" width: 30%;">Requested by</th>

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
                    <h5 class="modal-title" id="exampleModalLabel">Demand management </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-add-article">
                        <div class="form-group">
                <div>
                    <div style="display: flex;">
                        <label style="width: 20%"  for="name" class="col-form-label">Motive:</label>
                        <input style="width: 80%"  id="motive" type="text" class="form-control"  disabled>
                    </div>
                    <br>
                    <div style="display: flex;">
                        <label  style="width: 20%" for="description" class="col-form-label">Article:</label>
                        <input style="width: 80%" id="article-name" type="text" class="form-control" disabled>
                    </div>
                    <br>
                    <div style="display: flex;">
                        <label style="width: 20%" for="state" class="col-form-label">Requested-by:</label>
                        <input style="width: 80%" id="requested" type="text" class="form-control" disabled >
                    </div>
                    <br>
                    <div style="display: flex;">
                        <label style="width: 20%"for="donated-by" class="col-form-label">Demand date:</label>
                        <input style="width: 80%" id="demand-date" type="text"class="form-control" disabled>
                    </div>

                    <br>
                    <div style="display: flex;">
                        <label style="width: 20%"for="type" class="col-form-label">Status:</label>
                        <input style="width: 80%" id="status" type="text" class="form-control"  disabled>
                    </div>
                    <br>
                </div>

                    <input id="id" type="hidden" class="form-control">

                    <div class="text-center">
                        <button id="btn-accept" type="button" class="btn btn-success">Accept</button>
                        <button id="btn-refuse" type="button" class="btn btn-danger" data-bs-dismiss="modal">Refuse</button>
                    </div>
                <br>
                </div>
                </form>


            </div>
        </div>
    </div>




    <!-- end bootstrap model -->
    <script src="{{ asset('js/demands.js') }}" defer></script>











@endsection()
