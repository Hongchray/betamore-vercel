<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PayWay Checkout</title>
    <link rel="stylesheet" href="https://checkout-sandbox.payway.com.kh/plugins/checkout2-0.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
</head>
<body>
    <form method="POST" target="aba_webservice" action="{{ $apiUrl }}" id="aba_merchant_request">
        <input type="hidden" name="hash" value="{{ $hash }}"/>
        <input type="hidden" name="tran_id" value="{{ $tranId }}"/>
        <input type="hidden" name="amount" value="{{ $amount }}"/>
        <input type="hidden" name="firstname" value="{{ $firstName }}"/>
        <input type="hidden" name="lastname" value="{{ $lastName }}"/>
        <input type="hidden" name="phone" value="{{ $phone }}"/>
        <input type="hidden" name="email" value="{{ $email }}"/>
        <input type="hidden" name="return_params" value="{{ $returnParams }}"/>
        <input type="hidden" name="merchant_id" value="{{ $merchantId }}"/>
        <input type="hidden" name="req_time" value="{{ $reqTime }}"/>
    </form>

    <div style="margin-top: 75px; text-align: center;">
        <h2>TOTAL: ${{ $amount }}</h2>
        <button type="button" id="checkout_button">Checkout Now</button>
    </div>

    <!-- Load sandbox PayWay checkout -->
    <script src="https://checkout-sandbox.payway.com.kh/plugins/checkout2-0.js"></script>

    <script>
        $(document).ready(function () {
            $('#checkout_button').click(function () {
                if (typeof AbaPayway === "undefined") {
                    alert("AbaPayway script failed to load.");
                    return;
                }
                AbaPayway.checkout();
            });
        });
    </script>
</body>
</html>
