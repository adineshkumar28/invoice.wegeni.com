document.addEventListener("DOMContentLoaded", loadDefaultCountryCode);

function loadDefaultCountryCode() {
    if (!$("#defaultCountryPhone").length) {
        return false;
    }

    let input = document.querySelector("#defaultCountryPhone");
    let intl = window.intlTelInput(input, {
        initialCountry: $("#defaultCountryCode").val(),
        separateDialCode: true,
        preferredCountries: false,
        geoIpLookup: function (success, failure) {
            $.get("https://ipinfo.io", function () {}, "jsonp").always(
                function (resp) {
                    var countryCode = resp && resp.country ? resp.country : "";
                    success(countryCode);
                }
            );
        },
        utilsScript: "../../public/assets/js/inttel/js/utils.min.js",
    });

    let getCode =
        intl.selectedCountryData["name"] +
        "+" +
        intl.selectedCountryData["dialCode"];
    $("#defaultCountryPhone").val(getCode);
}

listenClick(".super-admin-delete-btn", function (event) {
    let recordId = $(event.currentTarget).attr("data-id");
    deleteItem(
        route("super-admins.destroy", recordId),
        Lang.get("js.super_admin")
    );
});

listenSubmit("#superAdminCreateForm, #superAdminEditForm", function (e) {
    e.preventDefault();
    if ($("#error-msg").text() !== "") {
        $("#phoneNumber").focus();
        return false;
    }
    $("#superAdminEditForm,#superAdminCreateForm")[0].submit();
});

listenClick(".default-country-code .iti__standard", function () {
    $("#defaultCountryCode").val($(this).attr("data-country-code"));
});
