/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************!*\
  !*** ./resources/js/articles.js ***!
  \**********************************/
$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  var saveBtn = $("#btn-save");

  var showModal = function showModal(name, description, state, email, status, article, firstName, lastName, depositDate, image, id) {
    $("#articleModal").modal('show');
    $('#articleModalLabel').text('Edit category');
    $("#name").val(name);
    $("#description").val(description);
    $("#state").val(state);
    $('#donated-by').val(firstName + '  ' + lastName + ' ' + '(' + email + ')');
    $("#deposit").val(depositDate);
    $('#selectArticle').val(article);
    $("#theDiv").empty();
    var i;

    for (i = 0; i < image.length; i++) {
      var source = "{!! asset('" + image[i].path + "') !!}";
      $("#theDiv").append('<img id="' + i.id + '" src=' + source + ' width="320px" height="200px"  class="image" />');
    }

    console.log(image.length);
    $('#id').val(id);
    $('#modal-title').text('Edit region');
    $('#btn-received').html('Update');
    $('#btn-received').attr("disabled", false);
  };

  var hideModal = function hideModal() {
    $("#articleModal").modal('hide');
    $("#name").val('');
    $("#description").val('');
    $("#state").val('');
    $("#email").val('');
    $("#status").val('');
    $("#deposit").val('');
    $("#theDiv").empty();
    $('#id').val('');
    $('#btn-received').html('Update');
    $('#btn-received').attr("disabled", false);
  };

  var table = $('#datatable-category').DataTable({
    pageLength: 10,
    serverSide: true,
    responsive: true,
    processing: true,
    columns: [{
      "data": 'name'
    }, {
      "data": 'deposit_date'
    }, {
      "data": 'state'
    }, {
      "data": 'name'
    }, {
      "data": null,
      name: 'action',
      orderable: false,
      searchable: false
    }],
    createdRow: function createdRow(row, data) {
      var actions = '<button style="margin-right: 5px" type="button" class="mb-1 btn btn-warning btn-pill edit-article"><i class=" mdi mdi-pencil mr-1"></i></button>';
      $('td', row).eq(0).empty().text(data['name']);
      $('td', row).eq(1).empty().text(data['deposit_date']);
      $('td', row).eq(2).empty().text(data['state']);
      $('td', row).eq(3).empty().text(data.customer.first_name + ' ' + data.customer.last_name + ' (' + data.customer.email + ')');
      $('td', row).eq(4).empty().append(actions).css('text-align', 'center');
    },
    ajax: {
      method: 'get',
      url: '/admin/article',
      dataType: 'json'
    }
  });
  $('body').on('click', '.edit-article', function () {
    var data = table.row($(this).parents('tr')).data();
    console.log(data.pictures);
    showModal(data.name, data.description, data.state, data.customer.email, data.status, data.status, data.customer.first_name, data.customer.last_name, data.deposit_date, data.pictures, data.id);
  });
  $('body').on('click', '#btn-received', function () {
    var status = 'received'; //console.log(status);

    var id = $("#id").val();
    $('#btn-received').html('Please Wait...');
    $('#btn-received').attr("disabled", true);
    var formData = new FormData();
    formData.append('status', status);
    Swal.fire({
      icon: 'warning',
      title: 'Do you want to update this article status?',
      showCancelButton: true,
      confirmButtonText: 'Update',
      denyButtonText: "Cancel"
    }).then(function (result) {
      if (result.isConfirmed) {
        $.ajax({
          method: "POST",
          url: '/admin/article/status/' + id,
          data: formData,
          dataType: 'json',
          processData: false,
          contentType: false,
          cache: false,
          success: function success(res) {
            hideModal();
            $('#datatable-category').DataTable().ajax.reload(null, false);
            $('#btn-received').html('Update');
            $('#btn-received').attr("disabled", false);
            Swal.fire('Article accepted', 'This article is now available for public ', 'success');
          },
          error: function error(_error) {
            $('#btn-received').html('Update');
            $('#btn-received').attr("disabled", false);
            Swal.fire('Accept article', 'Failed to accept article  !', 'error');
          }
        });
      } else {
        $('#btn-received').html('Update');
        $('#btn-received').attr("disabled", false);
      }
    });
  });
  $('body').on('click', '#btn-delete', function () {
    var id = $("#id").val(); // console.log(id);

    Swal.fire({
      icon: 'warning',
      title: 'Do you want to delete this article?',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      denyButtonText: "Cancel"
    }).then(function (result) {
      if (result.isConfirmed) {
        $.ajax({
          method: "DELETE",
          url: "/admin/article/delete/" + id,
          success: function success(res) {
            $('#datatable-category').DataTable().ajax.reload(null, false);
            Swal.fire('Delete article', 'Article deleted successfully !', 'success');
          },
          error: function error(_error2) {
            Swal.fire('Delete article', 'Failed to delete article  !', 'error');
          }
        });
      } else {
        $('#btn-delete').html('Delete');
        $('#btn-delete').attr("disabled", false);
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