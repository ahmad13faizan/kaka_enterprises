<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('razorpay::app.redirect-title')</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: #f7f8fa;
            color: #3a3a3a;
        }
        .spinner {
            width: 44px;
            height: 44px;
            border: 4px solid #dfe3e8;
            border-top-color: #3395ff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .retry-btn {
            margin-top: 16px;
            padding: 10px 20px;
            background: #3395ff;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="spinner"></div>
    <p>@lang('razorpay::app.redirect-message')</p>

    <button type="button" class="retry-btn" id="rzp-retry" style="display:none;">
        @lang('razorpay::app.pay-now')
    </button>

    {{-- Form used to post the verified payment result back to the server --}}
    <form id="razorpay-callback-form" action="{{ $callback_url }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
    </form>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        var options = {
            "key": "{{ $key_id }}",
            "amount": "{{ $amount }}",
            "currency": "{{ $currency }}",
            "name": @json($name),
            "order_id": "{{ $razorpay_order }}",
            "prefill": {
                "name": @json($customer_name),
                "email": @json($customer_email),
                "contact": @json($customer_phone)
            },
            "theme": {
                "color": "#3395ff"
            },
            "handler": function (response) {
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                document.getElementById('razorpay-callback-form').submit();
            },
            "modal": {
                "ondismiss": function () {
                    window.location.href = "{{ $cancel_url }}";
                }
            }
        };

        var rzp = new Razorpay(options);

        rzp.on('payment.failed', function (response) {
            window.location.href = "{{ $cancel_url }}";
        });

        function openCheckout() {
            rzp.open();
        }

        // Auto-open the checkout as soon as the page loads.
        window.onload = function () {
            openCheckout();
            var retry = document.getElementById('rzp-retry');
            retry.style.display = 'inline-block';
            retry.onclick = openCheckout;
        };
    </script>
</body>
</html>
