"use strict";
$(document).ready(function () {
  $("#datatable").DataTable();
  var a = $("#datatable-buttons").DataTable({
    lengthChange: !1,
    buttons: ["copy", "excel", "pdf"],
  });
  $("#key-table").DataTable({ keys: !0 }),
    $("#responsive-datatable").DataTable(),
    $("#selection-datatable").DataTable({ select: { style: "multi" } }),
    a
      .buttons()
      .container()
      .appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)"),
    $("#datatable_length select[name*='datatable_length']").addClass(
      "form-select form-select-sm"
    ),
    $("#datatable_length select[name*='datatable_length']").removeClass(
      "custom-select custom-select-sm"
    ),
    $(".dataTables_length label").addClass("form-label");
});
