Livewire.hook("element.init", ({ component }) => {
    if (component.name == "client-quote-table") {
        initializeSelect2Quote();
    }
});

function initializeSelect2Quote() {
    if (!$("#clientQuoteStatusID").length) {
        return false;
    }
    $("#clientQuoteStatusID").select2();
}

listenClick(".client-quote-delete-btn", function (event) {
    event.preventDefault();
    let id = $(event.currentTarget).attr("data-id");
    deleteItem(route("client.quotes.destroy", id), Lang.get("js.quote"));
});

listenClick("#resetFilter", function () {
    $("#status_filter").val(5).trigger("change");
    $("#status_filter").select2({
        placeholder: "All",
    });
});

listenChange("#clientQuoteStatusID", function () {
    let status = $(this).val();
    Livewire.dispatch("filterByStatus", { status: status });
});

listenClick("#clientQuoteResetFilters", function () {
    $("#clientQuoteStatusID").val(2).trigger("change");
});
