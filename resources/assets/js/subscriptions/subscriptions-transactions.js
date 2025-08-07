Livewire.hook("element.init",()=>{
    if($('#paymentTypeID').length) {
        $('#paymentTypeID').select2()
    }
})

listenClick("#resetFilter", function () {
    $("#paymentTypeArr").val("").trigger("change");
});

listenChange(".payment-approve", function () {
    let id = $(this).attr("data-id");
    let status = $(this).val();

    $.ajax({
        url: route("change-payment-status", id),
        type: "GET",
        data: { id: id, status: status },
        success: function (result) {
            displaySuccessMessage(result.message);
            Livewire.dispatch("refreshDatatable");
            Livewire.dispatch("resetPageTable");
            // Turbo.visit(route("subscriptions.transactions.index"));
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
});

listenChange("#paymentTypeID", function () {
    let paymentType = $(this).val();
    Livewire.dispatch("filterByPaymentType", { paymentType: paymentType });
});

listenClick("#subscriptionTransactionResetFilters", function () {
    $("#paymentTypeID").val(0).trigger("change");
});
