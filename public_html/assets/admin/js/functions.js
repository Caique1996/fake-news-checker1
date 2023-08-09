function dangerAlert(title, message) {
    Swal.fire(
        title,
        message,
        'error'
    )
}

function successAlert(title, message) {
    Swal.fire(
        title,
        message,
        'success'
    )
}

function copyInputText(el) {

    let inputId = el.getAttribute('data-input-id');
    let element = $(el);
    let copyText = document.getElementById(inputId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    successAlert(element.attr('data-text-copied-title'), element.attr('data-text-copied'))
}

function getOrderTotal(products, productId, period) {
    let total = 0;
    for (var i in products) {
        let product = products[i];
        if (product['id'] == productId) {
            let prices = product['prices'];

            if (prices['default'] !== undefined) {
                var defaultPrice = product['prices']['default']['price_' + period];
                total = parseFloat(total) + parseFloat(defaultPrice);
            }
            if (prices['san'] !== undefined) {
                let qtySan = parseInt($('input[name="single_sans"]').val());
                total = parseFloat(total) + parseFloat(parseFloat(product['prices']['san']['price_' + period]) * qtySan);
            }
            if (prices['wildcard_san'] !== undefined) {
                let qtySanWild = parseInt($('input[name="wildcard_sans"]').val());
                total = parseFloat(total) + parseFloat(parseFloat(product['prices']['wildcard_san']['price_' + period]) * qtySanWild);
            }

        }
    }
    return total.toLocaleString('pt-br', {style: 'currency', currency: 'BRL'});
}

function updateTotalOrder() {
    let productId = crud.field('product_id').value;
    let period = crud.field('period').value;
    let total = getOrderTotal(prices, productId, period);
    crud.field("total_order").input.value = total;
}

function alertConfirmError() {
    dangerAlert(_t.ops, _t.an_error_occurred);
}

function alertConfirmSuccess() {
    successAlert(_t.success, _t.default_success_message);
}

function confirmActionAjax(button) {
    var route = $(button).attr('data-route');
    var request_type = $(button).attr('request-type');
    var text = $(button).attr('data-text');
    var data = JSON.parse($(button).attr('data-post'));
    Swal.fire({
        title: _t.are_you_sure,
        text: text,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: _t.confirm,
        cancelButtonText: _t.cancel_btn_text,
        closeOnConfirm: true,
        closeOnCancel: true,
        danger: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: route,
                type: request_type,
                data: data,
                success: function (result) {

                    if (result instanceof Object) {
                        if (result.success) {
                            var notifyType = "success";
                        } else {
                            var notifyType = "error";
                        }
                        new Noty({
                            type: notifyType,
                            text: result.message
                        }).show();
                    } else if (result == 1) {
                        alertConfirmSuccess();
                    } else {// Show an error alert
                        alertConfirmError();
                    }
                },
                error: function (result) {
                    alertConfirmError();
                }
            });
        }
    });

}
