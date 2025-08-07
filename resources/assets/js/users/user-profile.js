document.addEventListener("DOMContentLoaded", loadLanguageData);

function loadLanguageData() {
    if ($("#selectLanguage").length) {
        $("#selectLanguage").select2({
            width: "100%",
            dropdownParent: $("#changeLanguageModal"),
        });
    }
}

listenClick("#changePassword", function () {
    $(".pass-check-meter div.flex-grow-1").removeClass("active");
    $("#changePasswordModal").modal("show").appendTo("body");
});

listenClick("#passwordChangeBtn", function () {
    $.ajax({
        url: changePasswordUrl,
        type: "PUT",
        data: $("#changePasswordForm").serialize(),
        success: function (result) {
            $("#changePasswordModal").modal("hide");
            displaySuccessMessage(result.message);
        },
        error: function error(result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
});

listenHiddenBsModal(
    ["#changeLanguageModal", "#changePasswordModal"],
    function () {
        let checkUrlContain = window.location.href.indexOf("/invoice/");

        if (checkUrlContain == -1) {
            $("#changeLanguageForm")[0].reset();
            $("#changePasswordForm")[0].reset();

            $("select.select2Selector").each(function (index, element) {
                var drpSelector = "#" + $(this).attr("id");
                $(drpSelector).val(getLoggedInUserLang);
                $(drpSelector).trigger("change");
            });
        }
    }
);

listenClick("#languageChangeBtn", function () {
    $.ajax({
        url: route("change-language"),
        type: "POST",
        data: $("#changeLanguageForm").serialize(),
        success: function (result) {
            $("#changeLanguageModal").modal("hide");
            displaySuccessMessage(Lang.get("js.language_updated"));
            setTimeout(function () {
                location.reload(true);
                window.location.reload();
            }, 2000);
        },
        error: function error(result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
});

listenClick("#changeLanguage", function () {
    let getLanguagerUrl = route("get.all.language");
    $.ajax({
        url: getLanguagerUrl,
        type: "GET",
        success: function (result) {
            if (result.success) {
                Livewire.dispatch("refreshDatatable");
                Livewire.dispatch("resetPageTable");
                $("#selectLanguage").empty();
                let options = [];
                $.each(result.data.getAllLanguage, function (key, value) {
                    options +=
                        '<option value="' + key + '">' + value + "</option>";
                });
                $("#selectLanguage").html(options);
                $("#selectLanguage")
                    .val(result.data.currentLanguage)
                    .trigger("change");

                $("#changeLanguageModal").modal("show");
            }
        },
        error: function (result) {
            displayErrorMessage(result.message);
        },
    });
});

window.printErrorMessage = function (selector, errorResult) {
    $(selector).show().html("");
    $(selector).text(errorResult.responseJSON.message);
};

listenHiddenBsModal("#changePasswordModal", function () {
    resetModalForm("#changePasswordForm", "#validationErrorsBox");
});
