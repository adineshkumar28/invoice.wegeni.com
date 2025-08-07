
Livewire.hook("element.init", () => {
    if ($("#frequencyId").length) {
        $("#frequencyId").select2();
    }
});

listenClick("#resetFilter", function () {
    $("#planTypeFilter").val("").trigger("change");
    Livewire.dispatch("refreshDatatable");
});

listenClick(".plan-delete-btn", function (event) {
    let subscriptionId = $(event.currentTarget).attr("data-id");
    let deleteSubscriptionUrl =
        $("#subscriptionPlanUrl").val() + "/" + subscriptionId;
    deleteItem(deleteSubscriptionUrl, Lang.get("js.subscription_plan"));
});

listenChange(".is_default", function (event) {
    let subscriptionPlanId = $(event.currentTarget).data("id");
    Livewire.dispatch("refreshDatatable");
    Livewire.dispatch("resetPageTable");
    updateStatusToDefault(subscriptionPlanId);
});

function updateStatusToDefault(subscriptionPlanId) {
    $.ajax({
        url: route("make.plan.default", subscriptionPlanId),
        method: "post",
        cache: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
            }
        },
    });
}

listenChange("#frequencyId", function (event) {
    let frequency = $(this).val();
    Livewire.dispatch("changeFrequency", { frequency: frequency });
});

listenClick("#subscriptionPlanResetFilters", function () {
    $("#frequencyId").val(0).trigger("change");
});

