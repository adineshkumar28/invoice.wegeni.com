listenClick(".subscriber-delete-btn", function () {
    let subscriberId = $(this).attr("data-id");
    deleteItem(
        route("super.admin.subscribe.destroy", subscriberId),
        Lang.get("js.subscriber")
    );
});
