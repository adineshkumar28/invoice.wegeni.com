Livewire.hook("element.init", () => {
    if ($("#filterStatusID").length) {
        $("#filterStatusID").select2();
    }
});

listenClick('.enquiry-delete-btn', function (e) {
    let superAdminEnquiryId = $(e.currentTarget).attr('data-id')
    deleteItem($('#enquiryUrl').val() + '/' + superAdminEnquiryId,
        Lang.get('js.enquiry'));
})

listenChange("#filterStatusID", function () {
    let status = $(this).val();
    Livewire.dispatch("filterByStatus", { status: status });
});

listenClick("#enquiryResetFilters", function () {
    $("#filterStatusID").val(2).trigger("change");
});
