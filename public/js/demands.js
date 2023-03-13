/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************************!*\
  !*** ./resources/js/demands.js ***!
  \*********************************/
$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  var saveBtn = $("#btn-save");

  var showModal = function showModal(motive, articleName, email, firstName, lastName, date, status, id) {
    $("#articleModal").modal('show');
    $('#articleModalLabel').text('Accept demand');
    $('#motive').val(motive);
    $('#article-name').val(articleName);
    $('#requested').val(email + ' ' + '(' + firstName + '  ' + lastName + ')');
    $('#demand-date').val(date);
    $('#status').val(status);
    $('#id').val(id);
    $('#btn-accept').html('Accept');
    $('#modal-title').text('Demand management ');
  };

  var hideModal = function hideModal() {
    $("#articleModal").modal('hide');
    $('#motive').val('');
    $('#article-name').val('');
    $('#requested').val('');
    $('#demand-date').val('');
    $('#status').val('');
    $('#id').val('');
    $('#btn-accept').html('Accept');
    $('#btn-accept').attr("disabled", false);
  };

  var table = $('#datatable-category').DataTable({
    pageLength: 10,
    serverSide: true,
    responsive: true,
    processing: true,
    columns: [{
      "data": 'article'
    }, {
      "data": 'demand_date'
    }, {
      "data": 'customer'
    }, {
      "data": null,
      name: 'action',
      orderable: false,
      searchable: false
    }],
    createdRow: function createdRow(row, data) {
      var actions = '<button style="margin-right: 5px" type="button" class="mb-1 btn btn-warning btn-pill edit-demand"><i class=" mdi mdi-pencil mr-1"></i></button>';
      $('td', row).eq(0).empty().text(data.article.name);
      $('td', row).eq(1).empty().text(data['demand_date']);
      $('td', row).eq(2).empty().text(data.customer.first_name + ' ' + data.customer.last_name + ' (' + data.customer.email + ')');
      $('td', row).eq(3).empty().append(actions).css('text-align', 'center');
    },
    ajax: {
      method: 'get',
      url: '/admin/demand',
      dataType: 'json'
    }
  });
  $('body').on('click', '.edit-demand', function () {
    var data = table.row($(this).parents('tr')).data();
    console.log(data.motive, data.article.name, data.customer.email, data.customer.first_name, data.customer.last_name, data.status, data.id);
    showModal(data.motive, data.article.name, data.customer.email, data.customer.first_name, data.customer.last_name, data.demand_date, data.status, data.id);
  });
  $('body').on('click', '#btn-accept', function () {
    var id = $("#id").val();
    console.log(id);
    $('#btn-accept').html('Please Wait...');
    $('#btn-accept').attr("disabled", true);
    Swal.fire({
      icon: 'warning',
      title: 'Do you want to accept this demand?',
      showCancelButton: true,
      confirmButtonText: 'Accept',
      denyButtonText: "Cancel"
    }).then(function (result) {
      if (result.isConfirmed) {
        $.ajax({
          method: "GET",
          url: '/admin/demand/accept/' + id,
          dataType: 'json',
          processData: false,
          contentType: false,
          cache: false,
          success: function success(res) {
            hideModal();
            $('#datatable-category').DataTable().ajax.reload(null, false);
            $('#btn-accept').html('Accept');
            $('#btn-accept').attr("disabled", false);
            Swal.fire('Demand accepted', 'Demand  accepted successfully !', 'success');
          },
          error: function error(_error) {
            $('#btn-accept').html('Accept');
            $('#btn-accept').attr("disabled", false);
            Swal.fire('Accept demand', 'Failed to accept demand  !', 'error');
          }
        });
      } else {
        $('#btn-accept').html('Accept');
        $('#btn-accept').attr("disabled", false);
      }
    });
  });
  $('body').on('click', '#btn-refuse', function () {
    var id = $("#id").val();
    console.log(id);
    $('#btn-refuse').html('Please Wait...');
    $('#btn-refuse').attr("disabled", true);
    Swal.fire({
      icon: 'warning',
      title: 'Do you want to refuse this demand?',
      showCancelButton: true,
      confirmButtonText: 'Accept',
      denyButtonText: "Cancel"
    }).then(function (result) {
      if (result.isConfirmed) {
        $.ajax({
          method: "GET",
          url: '/admin/demand/refuse/' + id,
          dataType: 'json',
          processData: false,
          contentType: false,
          cache: false,
          success: function success(res) {
            hideModal();
            $('#datatable-category').DataTable().ajax.reload(null, false);
            $('#btn-refuse').html('Refuse');
            $('#btn-refuse').attr("disabled", true);
            Swal.fire('Refuse demand', 'Demand refused successfully !', 'success');
          },
          error: function error(_error2) {
            $('#btn-refuse').html('Refuse');
            $('#btn-refuse').attr("disabled", false);
            Swal.fire('Refuse demand', 'Failed to refuse demand  !', 'error');
          }
        });
      } else {
        $('#btn-refuse').html('Delete');
        $('#btn-refuse').attr("disabled", false);
      }
    });
  });
  $('form#form-add-category').validate({
    rules: {
      type: {}
    }
  });
});
/******/ })()
;