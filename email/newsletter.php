<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hvala na prijavi!</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #DEAAAA;
            padding: 20px;
            text-align: center;
            color: #ffffff;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            color: #333333;
        }
        .coupon {
            background-color: #DEAAAA;
            color: #ffffff;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-radius: 8px;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #777777;
        }
        @media (prefers-color-scheme: dark) {
            body, .email-container {
                background-color: #1a1a1a !important;
                color: #f9f9f9 !important;
            }
            .header, .coupon {
                background-color: #DEAAAA !important;
                color: #1a1a1a !important;
            }
        }
    </style>
</head>
<body>
<table>
    <tr>
        <td>
            <div class="email-container">
                <div class="header">Uspješna prijava na <?php bloginfo('name'); ?> newsletter!</div>
                <div class="content">
                    <p>Pozdrav,</p>
                    <p>Hvala vam što ste se prijavili na naš newsletter. Ovo je Vaš kod za <?php echo $discount_amount; ?>% popusta:</p>
                    <div class="coupon"><?php echo $coupon_code; ?></div>
                    <p>Iskoristite ovaj kod pri sljedećoj kupnji i uživajte u popustu!</p>
                    <p>Srdačno,<br>Vaš Renaissance tim</p>
                </div>
                <div class="footer">
                    © <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Sva prava pridržana.
                </div>
            </div>
        </td>
    </tr>
</table>
</body>
</html>
