/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************************!*\
  !*** ./resources/js/regions.js ***!
  \*********************************/
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
    var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    var code = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var id = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    $("#categoryModal").modal('show');

    if (name) {
      $('#categoryModalLabel').text('Edit region');
      $('#name').val(name);
      $('#code').val(code);
      $('#id').val(id);
      $('#btn-save').attr('id', 'editCategory');
      $('#editCategory').text('Save');
      $('#modal-title').text('Edit region');
    } else {
      $('#categoryModalLabel').text('Add region');
      $('#name').val("");
      $('#code').val("");
      $('#id').val("");
      $('#editCategory').attr('id', 'btn-save');
      $('#btn-save').text('Save');
      $('#modal-title').text('Add region');
    }
  };

  var hideModal = function hideModal() {
    $("#categoryModal").modal('hide');
    $('#name').val('');
    $('#code').val('');
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
      "data": 'name'
    }, {
      "data": 'postal_code'
    }, {
      "data": 'users_count'
    }, {
      "data": 'customers_count'
    }, {
      "data": null,
      name: 'action',
      orderable: false,
      searchable: false
    }],
    ColumnDefs: [],
    createdRow: function createdRow(row, data) {
      actions = '<button style="margin-right: 5px" type="button" class="mb-1 btn btn-warning btn-pill edit-category"><i class=" mdi mdi-pencil mr-1"></i></button>';
      actions += '<button style="margin-left: 5px" type="button" class="mb-1 btn btn-danger btn-pill delete-category"><i class=" mdi mdi-trash-can mr-1"></i></button>';
      $('td', row).eq(0).empty().text(data['name']).css('vertical-align', 'middle');
      $('td', row).eq(1).empty().text(data['postal_code']).css('vertical-align', 'middle');
      $('td', row).eq(2).empty().text(data['users_count']).css('vertical-align', 'middle');
      $('td', row).eq(3).empty().text(data['customers_count']).css('vertical-align', 'middle');
      $('td', row).eq(4).empty().append(actions).css('text-align', 'center');
    },
    ajax: {
      method: 'get',
      url: '/admin/region',
      dataType: 'json'
    }
  });
  $('body').on('click', '.edit-category', function () {
    var data = table.row($(this).parents('tr')).data();
    showModal(data.name, data.postal_code, data.id);
  });
  $('body').on('click', '#editCategory', function () {
    if ($('form#form-add-category').valid()) {
      var name = $("#name").val();
      var code = $("#code").val();
      var id = $("#id").val();
      $('#editCategory').html('Please Wait...');
      $('#editCategory').attr("disabled", true);
      var formData = new FormData();
      formData.append('name', name);
      formData.append('postal_code', code);
      $.ajax({
        method: "POST",
        url: '/admin/region/' + id,
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
          Swal.fire('Update region', 'Region updated successfully !', 'success');
        },
        error: function error(res) {
          $('#editCategory').html('Save');
          $('#editCategory').attr("disabled", false);
          var msgs = res.responseJSON.errors.name ? res.responseJSON.errors.name : '';
          Swal.fire('Update region', 'Failed to update region !', 'error');
        }
      });
    }
  });
  $('body').on('click', '.delete-category', function () {
    var data = table.row($(this).parents('tr')).data();
    var id = data.id;
    Swal.fire({
      title: 'Do you want to delete this region?',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      denyButtonText: "Cancel"
    }).then(function (result) {
      if (result.isConfirmed) {
        $.ajax({
          method: "DELETE",
          url: "/admin/region/" + id,
          success: function success(res) {
            $('#datatable-category').DataTable().ajax.reload(null, false);
            Swal.fire('Delete region', 'Region deleted successfully !', 'success');
          },
          error: function error(_error) {
            Swal.fire('Delete region', 'Failed to delete region  !', 'error');
          }
        });
      }
    });
  });
  $('body').on('click', '#btn-save', function (event) {
    if ($('form#form-add-category').valid()) {
      var name = $("#name").val();
      var code = $("#code").val();
      saveBtn.html('Please Wait...');
      saveBtn.attr("disabled", true);
      var formData = new FormData();
      formData.append('name', name);
      formData.append('postal_code', code);
      $.ajax({
        method: "POST",
        url: '/admin/region',
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
          Swal.fire('Create region', 'Region created successfully !', 'success');
        },
        error: function error(res) {
          var msgs = res.responseJSON.errors.name ? res.responseJSON.errors.name : '';
          saveBtn.html('Save');
          saveBtn.attr("disabled", false);
          Swal.fire('Create region', 'Failed to create region !', 'error');
        }
      });
    }
  });
  $('form#form-add-category').validate({
    rules: {
      name: {
        required: true,
        maxlength: 20,
        remote: {
          url: "/admin/region/validation/unique",
          type: "post",
          data: {
            name: function name() {
              return $("#name").val();
            }
          }
        }
      },
      code: {
        required: true
      }
    }
  });
});
/******/ })()
;