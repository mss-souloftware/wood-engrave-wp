<?php

/**
 * 
 * @package Crea Tu Frase
 * @subpackage M. Sufyan Shaikh
 * 
 */


require_once ('confirmPayment/paymentfinish.php');
require_once ('confirmPayment/closeprocess.php');
require_once ('report/reportProblem.php');


// Check if the 'chocol_cookie' exists in the $_COOKIE array
if (isset($_COOKIE['chocol_cookie'])) {
    // Retrieve the option using the value from the cookie
    $getCookieOUI = get_option($_COOKIE['chocol_cookie']);
    $getCookieOUILast = explode("_", $getCookieOUI);
    $lastCookieVal = end($getCookieOUILast);
} else {
    $getCookieOUI = null;
    $getCookieOUILast = [];
    $lastCookieVal = null;
}

// Check if the payment parameter is set in the URL and equals true
if (isset($_GET['payment']) && $_GET['payment'] == true) {
    if (isset($_GET['payerID'])) {
        $payerID = $_GET['payerID'];
        global $wpdb;
        $tablename = $wpdb->prefix . 'chocoletras_plugin';
        $query = $wpdb->prepare("SELECT * FROM $tablename WHERE uoi = %s", $payerID);
        $result = $wpdb->get_row($query);

        if ($result) {
            $update_query = $wpdb->prepare("UPDATE $tablename SET pagoRealizado = 1 WHERE uoi = %s", $payerID);
            $wpdb->query($update_query);
            // echo "Row updated successfully.";
        }
    }
    ?>
    <script>
        document.cookie = `chocol_cookie=; Secure; Max-Age=-35120; path=/`;
        console.log("Payment True");
    </script>
    <?php
    require_once ('finishprocess/finishProcessStripe.php');
    $finishProcessStripeResult = finishProcessTripe();
    if (
        $finishProcessStripeResult->payment_status == "paid" &&
        paymentfinish($finishProcessStripeResult->customer) === 1
    ) { ?>
        <script>
            document.cookie = `chocol_cookie=; Secure; Max-Age=-35120; path=/`;
            location.reload();
        </script>
        <?php
    }
}



// PayPal Configuration
define('PAYPAL_EMAIL', 'chocoletra2020@gmail.com');
define('RETURN_URL', 'https://chocoletra.com/gracias-chocoletra/?payerID=' . $lastCookieVal);
define('CANCEL_URL', 'https://chocoletra.com/crea-tu-frase/');
define('NOTIFY_URL', 'https://chocoletra.com/gracias-chocoletra/?payerID=' . $lastCookieVal);
define('PAYPAL_CURRENCY', 'EUR');
define('SANDBOX', FALSE); // TRUE or FALSE 
define('LOCAL_CERTIFICATE', FALSE); // TRUE or FALSE

if (SANDBOX === TRUE) {
    $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
    $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}
// PayPal IPN Data Validate URL
define('PAYPAL_URL', $paypal_url);



if (isset($_GET['PayerID']) && $_GET['PayerID'] != '') {

    if (isset($_GET['payerID'])) {
        $payerID = $_GET['payerID'];
        global $wpdb;
        $tablename = $wpdb->prefix . 'chocoletras_plugin';
        $query = $wpdb->prepare("SELECT * FROM $tablename WHERE uoi = %s", $payerID);
        $result = $wpdb->get_row($query);

        if ($result) {
            $update_query = $wpdb->prepare("UPDATE $tablename SET pagoRealizado = 1 WHERE uoi = %s", $payerID);
            $wpdb->query($update_query);
            // echo "Row updated successfully.";
        }
    }
    //$checkout_session = 'Enhorabuena! Gracias por confiar en Chocoletras!';
    //  require_once('finishprocess/finishProcessPaypal.php');  
    // if(finishProcessTripe()->payment_status == "paid" && 
    //  paymentfinish(finishProcessTripe()->customer) === 1){

    ?>
    <script>
        document.cookie = `chocol_cookie=?; Secure; Max-Age=-35120; path=/`;
        // location.reload() 

        $(document).ready(function () {
            setTimeout(function () {
                location.reload(true);
            }, 300);
        });
    </script>
    <?php

    // } 

}







function chocoletras_shortCode()
{
    ob_start();




    ?>
    <section id="chocoletrasPlg" class="chocoletrasPlg sufyan">

        <div class="chocoletrasPlg-spiner">
            <img src="<?php echo plugins_url('../img/logospiner.gif', __FILE__); ?>" alt="<?php echo _e('Chocoletras'); ?>">
            <div class="chocoletrasPlg-spiner-ring">
            </div>
        </div>  
        <div class="chocoletrasPlg__wrapperCode">
            <div class="chocoletrasPlg__wrapperCode-firstHead <?php
            if (isset($_COOKIE['chocol_cookie']) && get_option($_COOKIE['chocol_cookie'])) {
                echo ' closedPannel';
            }
            ?>">
                <div class="chocoletrasPlg__wrapperCode-firstHead-wrapper">
                    <ul class="chocoletrasPlg__wrapperCode-firstHead-wrapper-ulWrapperFirst">
                        <li class="chocoletrasPlg__wrapperCode-firstHead-wrapper-ulWrapperFirst-emptyLi">
                            <b id="hideDetails">
                                <?php echo _e('Mostrar/Ocultar Detalles
                         ') ?>
                            </b>
                        </li>
                        <li class="chocoletrasPlg__wrapperCode-firstHead-wrapper-ulWrapperFirst-liTable closedPannel">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <pre><?php echo _e('Prec. por letras:  ') ?></pre>
                                        </td>
                                        <td><b>
                                                <pre><?php echo get_option('precLetra'); ?>€</pre>
                                            </b></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <pre><?php echo _e('Prec. por 🖤:  ') ?></pre>
                                        </td>
                                        <td><b>
                                                <pre>   €</pre>
                                            </b></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <pre><?php echo _e('Gastos de envio:  ') ?></pre>
                                        </td>
                                        <td><b>
                                                <pre><?php echo get_option('precEnvio'); ?>€</pre>
                                            </b></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <pre><?php echo _e('Caracteres Maximo: ') ?></pre>
                                        </td>
                                        <td><b>
                                                <pre><?php echo get_option('maxCaracteres'); ?></pre>
                                            </b></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <pre><?php echo _e('Caracteres Usados: ') ?></pre>
                                        </td>
                                        <td><b>
                                                <pre id="<?php echo _e('actual') ?>">0</pre>
                                            </b></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <pre><?php echo _e('Gasto Minimo: ') ?></pre>
                                        </td>
                                        <td><b>
                                                <pre><?php echo get_option('gastoMinimo'); ?>€</pre>
                                            </b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                    </ul>

                    <ul class="chocoletrasPlg__wrapperCode-firstHead-wrapper-ulWrapperFirst-wrapperFrase">
                        <li class="chocoletrasPlg__wrapperCode-firstHead-wrapper-firstHead-right-total">
                            <span>
                                <?php echo _e('Total: ') ?>
                                <b id="<?php echo _e('counter') ?>">
                                    <?php echo get_option('gastoMinimo') + get_option('precEnvio'); ?>
                                </b>€
                            </span>
                        </li>
                        <li>
                            <form><input id="<?php echo _e('getText') ?>" type="text"
                                    placeholder="<?php echo _e('Escriba su frase aqu&iacute;..'); ?>" required></form>
                        </li>
                        <li><button id="<?php echo _e('continuarBTN') ?>">
                                <?php echo _e('continuar '); ?>
                            </button> </li>
                    </ul>
                </div>
            </div>

            <div class="chocoletrasPlg__wrapperCode-firstHead-dataUser<?php
            if (isset($_COOKIE['chocol_cookie']) && get_option($_COOKIE['chocol_cookie'])) {
                echo ' closedPannel';
            } ?>">
                <h3>
                    <?php echo _e('Inserte datos de env&#237;o del destinatario'); ?>
                </h3>
                <form class="chocoletrasPlg__wrapperCode-dataUser-form" action="test_action">
                    <input type="hidden" name="action" value="test_action" readonly>
                    <input class="chocoletrasPlg__wrapperCode-dataUser-form-input" type="text" name="name" id=""
                        placeholder="Nombre Completo" required>
                    <input class="chocoletrasPlg__wrapperCode-dataUser-form-input" type="email" name="email" id=""
                        placeholder="Email del comprador" required>
                    <div class="chocoletrasPlg__wrapperCode-dataUser-form-bhooth">
                        <input class="chocoletrasPlg__wrapperCode-dataUser-form-bhooth-input" type="tel" name="tel"
                            pattern="[0-9]{9}" id="chocoTel" placeholder="Tel&#233;fono" minlength="9" required>
                        <input class="chocoletrasPlg__wrapperCode-dataUser-form-bhooth-input" type="number" name="cp" id=""
                            placeholder="C&#243;digo postal">
                    </div>
                    <div class="chocoletrasPlg__wrapperCode-dataUser-form-bhooth">
                        <input class="chocoletrasPlg__wrapperCode-dataUser-form-bhooth-input" type="text" name="city" id=""
                            placeholder="Ciudad">
                        <input class="chocoletrasPlg__wrapperCode-dataUser-form-bhooth-input" type="text" name="province"
                            id="" placeholder="Provincia">
                    </div>
                    <input class="chocoletrasPlg__wrapperCode-dataUser-form-input" type="text" name="address" id=""
                        placeholder="Direccion de entrega" required>
                    <div class="chocoletrasPlg__wrapperCode-dataUser-form_envioExpress">
                        <div class="chocoletrasPlg__wrapperCode-dataUser-form_envioExpress-wrapper"
                            style="background-image: url(<?php echo plugin_dir_url(__DIR__) . "img/captura.png"; ?>);">
                            <input type="checkbox" id="ExpressActivatorSwith" />
                            <label for="ExpressActivatorSwith">
                                <?php echo _e('Env&#237;o Express! ( 24h-48h! d&#237;as laborables ) por ') ?>
                                <b>
                                    <?php echo _e('€' . get_option('expressShiping')) ?>
                                </b>
                            </label>
                        </div>
                    </div>
                    <div class="chocoletrasPlg__wrapperCode-dataUser-form_envioNormal">
                        <label for="picDate">
                            <?php echo _e('Fecha deseada de entrega.') ?>
                        </label>
                        <span class="chocoletrasPlg__wrapperCode-dataUser-form-input-disclamer">
                            <?php echo _e('Todos nuestros env&iacute;os se realizan en d&iacute;as laborables e igualmente las entregas se hacen d&iacute;as laborables de 24h a 72h, envio ordinario.'); ?>
                        </span>
                        <label>Fecha de entrega</label>
                        <input type="date" name="date" id="picDate" placeholder="Fecha de entrega">
                        <?php
                        $getCookieOUI = get_option($_COOKIE['chocol_cookie']);
                        $getCookieOUILast = explode("_", $getCookieOUI);
                        $lastCookieVal = end($getCookieOUILast);
                        function uniqueOrderNum(int $lengthURN = 10): string
                        {
                            $uniqueOrderNumber = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $randomOrderNum = '';
                            for ($i = 0; $i < $lengthURN; $i++) {
                                $randomOrderNum .= $uniqueOrderNumber[rand(0, strlen($uniqueOrderNumber) - 1)];
                            }
                            return $randomOrderNum;
                        }

                        $finalUON = uniqueOrderNum();
                        ?>
                        <input type="hidden" name="uoi" id="uniqueOrderID" value="<?php echo $finalUON; ?>"
                            placeholder="Unique Order ID">
                    </div>
                    <textarea name="message" placeholder="<?php echo _e('Agregue su comentario aqu&#237;.'); ?>"></textarea>
                    <div class="chocoletrasPlg__wrapperCode-dataUser-form-termCond">
                        <input type="checkbox" name="term" id="TermAndCond" required>
                        <label for="TermAndCond">
                            <?php echo _e('Para continuar acepte nuestros '); ?>
                            <a href="<?php echo get_option('termCond'); ?>">
                                <?php echo _e('terminos y condiciones.') ?>
                            </a>
                        </label>
                    </div>
                    <div class="chocoletrasPlg__wrapperCode-dataUser-form-action">
                        <button id="backBTN">
                            <?php echo _e('atr&#225;s ') ?>
                        </button> <input type="submit" value="<?php echo _e('Enviar ') ?>">
                    </div>
                    <input class="chocoletrasPlg__wrapperCode-dataUser-form-input" type="hidden" name="chocofrase" readonly>
                    <input class="chocoletrasPlg__wrapperCode-dataUser-form-input-price" type="hidden" name="price"
                        readonly>
                    <input id="ExpressActivator" type="hidden" name="express" value="off" readonly>
                </form>
            </div>

            <div class="chocoletrasPlg__wrapperCode-payment  <?php

            if (isset($_COOKIE['chocol_cookie']) && get_option($_COOKIE['chocol_cookie'])) {
                echo ' openPannel';
            } else {
                echo ' closedPannel';
            }
            ?>">
                <a href="#" id="cancelProcessPaiment" class="<?php
                if (isset($_COOKIE['chocol_cookie']) && get_option($_COOKIE['chocol_cookie'])) {
                    echo 'cancelPrpcess__' . get_option($_COOKIE['chocol_cookie']);
                }
                ?>">Cancelar</a>
                <h3>
                    <?php echo _e('Seleccione el m&#233;todo de pago de su preferencia ') ?>
                </h3>


                <p>
                    <?php echo _e('Frase:  '); ?>
                    <?php echo get_option($_COOKIE['chocol_cookie']) ? explode('_', get_option($_COOKIE['chocol_cookie']))[1] : 'null'; ?>
                </p>
                <span>
                    <?php echo _e('Saldo a pagar:  '); ?> <b>
                        <?php echo get_option($_COOKIE['chocol_cookie']) ? explode('_', get_option($_COOKIE['chocol_cookie']))[0] . '€' : 'null'; ?>
                    </b>
                </span>
                <div class="chocoletrasPlg__wrapperCode-payment-card">
                    <span class="chocoletrasPlg__wrapperCode-payment-card-span">
                        <?php echo _e('Aceptamos:  '); ?>
                    </span>
                    <img class="chocoletrasPlg__wrapperCode-payment-card-img"
                        src="<?php echo plugins_url('../img/lista-de-tarjetas-aceptadas.jpg', __FILE__); ?>"
                        alt="<?php echo _e('Chocoletra cards acepted'); ?>">
                </div>
                <div class="chocoletrasPlg__wrapperCode-payment-buttons">
                    <div class="chocoletrasPlg__wrapperCode-payment-buttons-left">

                        <form action="<?php echo PAYPAL_URL; ?>" method="post">



                            <!-- PayPal business email to collect payments -->
                            <input type='hidden' name='business' value="<?php echo PAYPAL_EMAIL; ?>">

                            <input type="hidden" name="item_name"
                                value="<?php echo get_option($_COOKIE['chocol_cookie']) ? explode('_', get_option($_COOKIE['chocol_cookie']))[1] : 'null'; ?>">
                            <input type="hidden" name="item_number" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="amount"
                                value="<?php echo get_option($_COOKIE['chocol_cookie']) ? explode('_', get_option($_COOKIE['chocol_cookie']))[0] : 'null'; ?>">
                            <input type="hidden" name="currency_code" value="<?php echo PAYPAL_CURRENCY; ?>">
                            <input type='hidden' name='no_shipping' value='1'>
                            <input type="hidden" name="lc" value="" />
                            <input type="hidden" name="no_note" value="1" />
                            <input type="hidden" name="page_style" value="paypal" />
                            <input type="hidden" name="charset" value="utf-8" />

                            <!-- PayPal return, cancel & IPN URLs -->
                            <input type='hidden' name='return' value="<?php echo RETURN_URL; ?>">
                            <input type='hidden' name='cancel_return' value="<?php echo CANCEL_URL; ?>">
                            <input type='hidden' name='notify_url' value="<?php echo NOTIFY_URL; ?>">

                            <!-- Specify a Pay Now button. -->
                            <input type="hidden" name="cmd" value="_xclick">




                            <!-- Display the payment button. -->
                            <?php // echo $lastCookieVal;   ?>
                            <Button type="submit"><span>
                                    <?php echo _e('Pagar con PayPal '); ?>
                                </span><img
                                    src="https://chocoletra.com/wp-content/uploads/2024/03/new-PayPal-Logo-horizontal-full-color-png.png"
                                    alt="<?php echo _e('Chocoletras'); ?>"></Button>

                        </form>




                    </div>

                    <?php /*
               <div class="chocoletrasPlg__wrapperCode-payment-buttons-right">



                   <Button id="payment_strype"><span>
                           <?php echo _e('Pagar con Tarjeta '); ?>
                       </span><img src="<?php echo plugins_url('../img/card_png.png', __FILE__); ?>"
                           alt="<?php echo _e('Chocoletras'); ?>"></Button>

               </div>
*/ ?>
                    <div class="chocoletrasPlg__wrapperCode-payment-buttons-left">
                        <?php

                        function generateRandomOrderNumberRedsys(int $lengthRedsys = 10): string
                        {
                            $charactersRedsys = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $randomStringRedsys = '';
                            for ($i = 0; $i < $lengthRedsys; $i++) {
                                $randomStringRedsys .= $charactersRedsys[rand(0, strlen($charactersRedsys) - 1)];
                            }
                            return $randomStringRedsys;
                        }

                        $orderNumberRedsys = generateRandomOrderNumberRedsys();

                        function generateRandomOrderNumberBizum(int $lengthBizum = 10): string
                        {
                            $charactersBizum = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $randomStringBizum = '';
                            for ($i = 0; $i < $lengthBizum; $i++) {
                                $randomStringBizum .= $charactersBizum[rand(0, strlen($charactersBizum) - 1)];
                            }
                            return $randomStringBizum;
                        }

                        $orderNumberBizum = generateRandomOrderNumberBizum();

                        $redsysAPIwoo = WP_PLUGIN_DIR . '/redsyspur/apiRedsys/apiRedsysFinal.php';

                        require_once ($redsysAPIwoo);
                        // echo $lastCookieVal;
                        $miObj = new RedsysAPI;


                        // $amount = get_option($_COOKIE['chocol_cookie']);
                        // $amount = $amount ? str_replace('.', '', $amount) : 'null';
                    
                        // $amount = $amount ? explode('_', $amount)[0] : 'null';
                    

                        $amount = get_option($_COOKIE['chocol_cookie']);
                        $amount = $amount ? str_replace('.', '', $amount) : 'null';
                        $amount = $amount ? explode('_', $amount)[0] : 'null';

                        // Check the length of the amount
                        if (strlen($amount) == 3) {
                            // Add "0" at the end
                            $amount = $amount . "0";
                        } elseif (strlen($amount) == 2) {
                            // Add "00" at the end
                            $amount = $amount . "00";
                        }

                        $miObj->setParameter("DS_MERCHANT_AMOUNT", $amount);
                        $miObj->setParameter("DS_MERCHANT_ORDER", $orderNumberRedsys);
                        $miObj->setParameter("DS_MERCHANT_MERCHANTCODE", "340873405");
                        $miObj->setParameter("DS_MERCHANT_CURRENCY", "978");
                        $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", "0");
                        $miObj->setParameter("DS_MERCHANT_TERMINAL", "001");
                        $miObj->setParameter("DS_MERCHANT_MERCHANTURL", "https://chocoletra.com/crea-tu-frase/");
                        $miObj->setParameter("DS_MERCHANT_URLOK", "https://chocoletra.com/gracias-chocoletra/?payment=true&payerID=" . $lastCookieVal);
                        $miObj->setParameter("DS_MERCHANT_URLKO", "https://chocoletra.com/crea-tu-frase/");

                        $params = $miObj->createMerchantParameters();
                        $claveSHA256 = 'qdBg81KwXKi+QZpgNXoOMfBzsVhBT+tm';
                        // $claveSHA256 = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
                        $firma = $miObj->createMerchantSignature($claveSHA256); ?>
                        <form action="https://sis.redsys.es/sis/realizarPago" method="POST">
                            <input type="hidden" name="Ds_SignatureVersion" value="HMAC_SHA256_V1" />
                            <input type="hidden" name="Ds_MerchantParameters" value="<?php echo $params; ?>" />
                            <input type="hidden" name="Ds_Signature" value="<?php echo $firma; ?>" />
                            <button type="submit"><span>
                                    <?php echo _e('Pagar con Tarjeta '); ?>
                                </span><img src="https://chocoletra.com/wp-content/uploads/2024/03/redsys-tarjetas.png"
                                    alt="<?php echo _e('Chocoletra'); ?>"></button>
                        </form>
                    </div>

                    <div class="chocoletrasPlg__wrapperCode-payment-buttons-left">
                        <?php
                        // echo $lastCookieVal;
                        $bizumObj = new RedsysAPI;

                        // $bizumObj->setParameter("DS_MERCHANT_AMOUNT", 10);
                        $bizumObj->setParameter("DS_MERCHANT_AMOUNT", $amount);
                        $bizumObj->setParameter("DS_MERCHANT_ORDER", $orderNumberBizum);
                        $bizumObj->setParameter("DS_MERCHANT_MERCHANTCODE", "340873405");
                        $bizumObj->setParameter("DS_MERCHANT_CURRENCY", "978");
                        $bizumObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", "0");
                        $bizumObj->setParameter("DS_MERCHANT_TERMINAL", "001");
                        $bizumObj->setParameter("DS_MERCHANT_PAYMETHODS", "z");
                        $bizumObj->setParameter("DS_MERCHANT_MERCHANTURL", "https://chocoletra.com/crea-tu-frase/");
                        $bizumObj->setParameter("DS_MERCHANT_URLOK", "https://chocoletra.com/gracias-chocoletra/?payment=true&payerID=" . $lastCookieVal);
                        $bizumObj->setParameter("DS_MERCHANT_URLKO", "https://chocoletra.com/crea-tu-frase/");

                        $bizumparams = $bizumObj->createMerchantParameters();
                        // $bizumclaveSHA256 = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
                        $bizumclaveSHA256 = 'qdBg81KwXKi+QZpgNXoOMfBzsVhBT+tm';
                        $bizumfirma = $bizumObj->createMerchantSignature($bizumclaveSHA256); ?>
                        <?php // <form action="https://sis-t.redsys.es:25443/sis/realizarPago" method="POST"> ?>
                        <form action="https://sis.redsys.es/sis/realizarPago" method="POST">
                            <input type="hidden" name="Ds_SignatureVersion" value="HMAC_SHA256_V1" />
                            <input type="hidden" name="Ds_MerchantParameters" value="<?php echo $bizumparams; ?>" />
                            <input type="hidden" name="Ds_Signature" value="<?php echo $bizumfirma; ?>" />
                            <button type="submit"><span>
                                    <?php echo _e('Pagar con Bizum '); ?>
                                </span><img src="https://chocoletra.com/wp-content/uploads/2024/03/Bizum.svg.png"
                                    alt="<?php echo _e('Chocoletra'); ?>"></button>
                        </form>

                    </div>

                </div>
            </div>
            <?php if (isset($_GET['PayerID']) && $_GET['PayerID'] != '' || isset($_GET['payerID']) && $_GET['payerID'] != '') { ?>

                <script>
                    (function ($) {
                        $(document).ready(function () {
                            // Function to get cookie value by name
                            function getCookie(name) {
                                var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                                if (match) return match[2];
                            }

                            // Function to set form field values from cookies
                            function setFormValuesFromCookies() {
                                $('#nf-field-24').val(getCookie('address') || '');
                                $('#nf-field-19').val(getCookie('email') || '');
                                $('#nf-field-18').val(getCookie('fname') || '');
                                $('#nf-field-17').val(getCookie('nf-field-17-textbox') || ''); //price
                                $('#nf-field-22').val(getCookie('nf-field-22-textbox') || ''); //city
                                $('#nf-field-20').val(getCookie('phone') || ''); //phone
                                $('#nf-field-21').val(getCookie('zip') || ''); //zip
                                $('#nf-field-16').val(getCookie('nf-field-16-textbox') || ''); //phrase
                                $('#nf-field-23').val(getCookie('nf-field-23-textbox') || ''); //proven
                                $('#nf-field-26').val(getCookie('nf-field-26-textbox') || ''); //date
                                $('#nf-field-27').val(getCookie('nf-field-27-textbox') || ''); //message
                                $('#nf-field-25').val(getCookie('nf-field-25-textbox') || ''); //express delivery
                                $('#nf-field-28').click();
                            }

                            // Execute the function after 2 seconds
                            setTimeout(setFormValuesFromCookies, 2000);
                        });

                    }(jQuery))
                </script>

                <div class="alert alert-success" role="alert" style="background: #2aad49;color: #fff;padding: 5px;">Enhorabuena!
                    Gracias por confiar en Chocoletra!</div>
            <?php } ?>
            <div id="stripeErrorMessage"></div>
            <?php

            echo reportProblemStructure();
            ?>
    </section>


    <?php
    return ob_get_clean();
}
