Livewire.hook("element.init", ({ component }) => {
    if (component.name == "quote-table") {
        if (!$("#quoteStatusID")) {
            return false;
        }
        $("#quoteStatusID").select2();
    }
});

listenClick(".quote-delete-btn", function (event) {
    event.preventDefault();
    let id = $(event.currentTarget).attr("data-id");
    deleteItem(route("quotes.destroy", id), Lang.get("js.quote"));
});

listenClick(".convert-to-invoice", function (e) {
    e.preventDefault();
    let quoteId = $(this).data("id");
    $.ajax({
        url: route("quotes.convert-to-invoice"),
        type: "GET",
        data: { quoteId: quoteId },
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                Livewire.dispatch("refreshDatatable");
                Livewire.dispatch("resetPageTable");
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
});

listenChange("#quoteStatusID", function () {
    let status = $(this).val();
    Livewire.dispatch("filterByStatus", { status: status });
});

listenClick("#quoteResetFilters", function () {
    $("#quoteStatusID").val(2).trigger("change");
});
