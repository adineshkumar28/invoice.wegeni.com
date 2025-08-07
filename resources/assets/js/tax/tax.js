listenClick(".addTax", function () {
    $("#addTaxModal").appendTo("body").modal("show");
});

listenSubmit("#addTaxForm", function (e) {
    e.preventDefault();
    if (isDoubleClicked($(this))) return;

    $.ajax({
        url: route("taxes.store"),
        type: "POST",
        data: $(this).serialize(),
        beforeSend: function () {
            startLoader();
        },
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                Livewire.dispatch("refreshDatatable");
                Livewire.dispatch("resetPageTable");
                $("#addTaxModal").modal("hide");
                $("#taxTbl").DataTable().ajax.reload(null, false);
                setTimeout(function () {
                    window.location.reload();
                }, 3000)
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            stopLoader();
        },
    });
});

listenHiddenBsModal("#addTaxModal", function () {
    resetModalForm("#addTaxForm", "#validationErrorsBox");
});

listenClick(".tax-edit-btn", function (event) {
    let taxId = $(event.currentTarget).attr("data-id");
    taxRenderData(taxId);
});

listenSubmit("#editTaxForm", function (event) {
    event.preventDefault();
    const taxId = $("#taxId").val();
    $.ajax({
        url: route("taxes.update", { tax: taxId }),
        type: "put",
        data: $(this).serialize(),
        beforeSend: function () {
            startLoader();
        },
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                Livewire.dispatch("refreshDatatable");
                Livewire.dispatch("resetPageTable");
                $("#editTaxModal").modal("hide");
                $("#taxTbl").DataTable().ajax.reload(null, false);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            stopLoader();
        },
    });
});

listenClick(".tax-delete-btn", function (event) {
    let taxId = $(event.currentTarget).attr("data-id");
    deleteItem(route("taxes.destroy", taxId), Lang.get("js.tax"));
});

listenChange(".tax-status", function (event) {
    let taxId = $(event.currentTarget).attr("data-id");
    updateStatus(taxId, this);
});

function taxRenderData(taxId) {
    $.ajax({
        url: route("taxes.edit", taxId),
        type: "GET",
        beforeSend: function () {
            startLoader();
        },
        success: function (result) {
            if (result.success) {
                $("#editTaxName").val(result.data.name);
                $("#editTaxValue").val(result.data.value);
                if (result.data.is_default === 1) {
                    $("input:radio[value='1'][name='is_default']").prop(
                        "checked",
                        true
                    );
                } else {
                    $("input:radio[value='0'][name='is_default']").prop(
                        "checked",
                        true
                    );
                }
                $("#taxId").val(result.data.id);
                $("#editTaxModal").appendTo("body").modal("show");
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            stopLoader();
        },
    });
}

function updateStatus(taxId, currentObject) {
    $.ajax({
        url: route("taxes.default-status", taxId),
        method: "post",
        cache: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                Livewire.dispatch("refreshDatatable");
                Livewire.dispatch("resetPageTable");

                if ($(currentObject).is(":checked")) {
                    $(".tax-status").prop("checked", false);
                    $(currentObject).prop("checked", true);
                }
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
}
var taxTable = $("#taxTbl").DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('taxes.index') }}",
    columns: [
        { data: "id", name: "id" },
        { data: "name", name: "name" },
        { data: "value", name: "value" },
        {
            data: "is_default", name: "is_default", render: function (data) {
                return `<input type="checkbox" class="tax-status" data-id="${data}" ${data ? 'checked' : ''}>`;
            }
        },
        { data: "action", name: "action", orderable: false, searchable: false }
    ]
});
