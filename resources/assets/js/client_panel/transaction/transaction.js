Livewire.hook("element.init", ({ component }) => {
    if (component.name == "client-transaction-table") {
        initializeSelect2CPTransaction();
    }
});

function initializeSelect2CPTransaction() {
    if (!$("#paymentModeID").length) {
        return false;
    }
    $("#paymentModeID").select2();
}

listenClick("#resetFilter", function () {
    $("#paymentModeFilter").select2({
        placeholder: "Select Payment Method",
        allowClear: false,
    });
    $("#paymentModeFilter").val(0).trigger("change");
});
