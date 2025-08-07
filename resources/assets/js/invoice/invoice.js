let start_date;
let end_date;
let datePicker;
let isPickerApply = false;

Livewire.hook("element.init", ({ component }) => {
    if (
        component.name == "invoice-table" &&
        component.canonical.dateRange.length == 0
    ) {
        initializeSelect2Invoice();

        let uri = window.location.toString();
        if (uri.indexOf("?") > 0) {
            let clean_uri = uri.substring(0, uri.indexOf("?"));
            window.history.replaceState({}, document.title, clean_uri);
        }

        invoiceDateWiseFilter();
    }
});

function invoiceDateWiseFilter() {
    datePicker = $("#dateRange");

    if (!datePicker.length) {
        return;
    }

    start_date = moment().subtract(100, "years");
    end_date = moment();

    setDatepickerValue(start_date, end_date);
    const last_month = moment().startOf("month").subtract(1, "days");

    datePicker.daterangepicker(
        {
            startDate: start_date,
            endDate: end_date,
            opens: "left",
            showDropdowns: true,
            autoUpdateInput: false,
            locale: {
                customRangeLabel: Lang.get("js.custom"),
                applyLabel: Lang.get("js.apply"),
                cancelLabel: Lang.get("js.cancel"),
                fromLabel: Lang.get("js.from"),
                toLabel: Lang.get("js.to"),
                monthNames: [
                    Lang.get("js.jan"),
                    Lang.get("js.feb"),
                    Lang.get("js.mar"),
                    Lang.get("js.apr"),
                    Lang.get("js.may"),
                    Lang.get("js.jun"),
                    Lang.get("js.jul"),
                    Lang.get("js.aug"),
                    Lang.get("js.sep"),
                    Lang.get("js.oct"),
                    Lang.get("js.nov"),
                    Lang.get("js.dec"),
                ],
                daysOfWeek: [
                    Lang.get("js.sun"),
                    Lang.get("js.mon"),
                    Lang.get("js.tue"),
                    Lang.get("js.wed"),
                    Lang.get("js.thu"),
                    Lang.get("js.fri"),
                    Lang.get("js.sat"),
                ],
            },
            ranges: {
                [Lang.get("js.all")]: [
                    moment().subtract(100, "years"),
                    moment(),
                ],
                [Lang.get("js.today")]: [moment(), moment()],
                [Lang.get("js.this_week")]: [
                    moment().startOf("week"),
                    moment().endOf("week"),
                ],
                [Lang.get("js.last_week")]: [
                    moment().startOf("week").subtract(7, "days"),
                    moment().startOf("week").subtract(1, "days"),
                ],
                [Lang.get("js.last_30")]: [
                    moment().subtract(29, "days"),
                    moment(),
                ],
                [Lang.get("js.this_month")]: [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                [Lang.get("js.last_month")]: [
                    last_month.clone().startOf("month"),
                    last_month.clone().endOf("month"),
                ],
            },
        },
        setDatepickerValue
    );

    datePicker.on("apply.daterangepicker", function (ev, picker) {
        isPickerApply = true;
        start_date = picker.startDate.format("YYYY-MM-D");
        end_date = picker.endDate.format("YYYY-MM-D");
        Livewire.dispatch("changeDateFilter", [start_date, end_date]);
    });

    function setDatepickerValue(start, end) {
        datePicker.val(
            start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY")
        );
    }

    listenChange("#invoiceStatusId", function () {
        let status = $(this).val();
        let recurringStatus = $("#invoiceRecurringId").val();
        let startDate = start_date;
        let endDate = end_date;
        Livewire.dispatch("filterStatus", {
            status: status,
            recurringStatus: recurringStatus,
            startDate: startDate,
            endDate: endDate,
        });
    });

    listenChange("#invoiceRecurringId", function () {
        let recurringStatus = $(this).val();
        let status = $("#invoiceStatusId").val();
        let startDate = start_date;
        let endDate = end_date;
        Livewire.dispatch("filterRecurringStatus", {
            recurringStatus: recurringStatus,
            status: status,
            startDate: startDate,
            endDate: endDate,
        });
    });
}

function initializeSelect2Invoice() {
    if (!$("#invoiceStatusId").length) {
        return false;
    }
    $("#invoiceStatusId").select2();
    if (!$("#invoiceRecurringId").length) {
        return false;
    }
    $("#invoiceRecurringId").select2();
}

listenClick(".invoice-delete-btn", function (event) {
    event.preventDefault();
    let id = $(event.currentTarget).attr("data-id");
    deleteItem(route("invoices.destroy", id), Lang.get("js.invoice"));
});

listenClick("#invoiceResetFilters", function () {
    $("#invoiceStatusId").val("").trigger("change");
    $("#invoiceRecurringId").val("").trigger("change");

    let startDate = moment().subtract(100, "years");
    let endDate = moment();
    Livewire.dispatch("changeDateFilter", [startDate, endDate]);
    invoiceDateWiseFilter();
    hideDropdownManually($("#invoiceFilters"), $(".dropdown-menu"));
});

listenClick(".reminder-btn", function () {
    let invoiceId = $(this).data("id");
    $.ajax({
        type: "POST",
        url: route("invoice.payment-reminder", invoiceId),
        beforeSend: function () {
            screenLock();
            startLoader();
        },
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            stopLoader();
            screenUnLock();
        },
    });
});

listenClick(".update-recurring", function (e) {
    e.preventDefault();
    let invoiceId = $(this).data("id");
    $.ajax({
        type: "POST",
        url: route("update-recurring-status", invoiceId),
        beforeSend: function () {
            screenLock();
            startLoader();
        },
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
        complete: function () {
            stopLoader();
            screenUnLock();
        },
    });
});
