<?php include 'generate-visitor-qr-code.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Receipt</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styles for printing */
        @media print {
            @page {
                size: 3.5in 6in;
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .container {
                width: 100%;
                height: 100%;
                margin: 0 auto;
                padding: 0.5rem;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
            }

            .card {
                width: 100%;
                height: auto;
                border: none;
                padding: 1rem;
            }

            .card-header,
            .card-footer {
                text-align: center;
            }

            .card-body {
                font-size: 14px;
                text-align: center;
            }

            img {
                max-width: 3in;
                max-height: 3in;
            }

            h6 {
                margin-bottom: 0.5rem;
            }

            h3 {
                color: black;
                font-weight: 700;
            }

            h5 {
                color: green;
            }
            .fullname{
                font-size: 16px;
            }
        }
    </style>
</head>

<body class="bg-dark mt-2">
    <div class="container">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h3 class="mb-0 fullname">
                    <?php echo strtoupper($visitor['first_name'] . ' ' . $visitor['middle_name'] . ' ' . $visitor['last_name']); ?>
                </h3>
            </div>
            <div class="card-body">
                <div>
                    <p class="mb-1">Code: <strong><?php echo $visitor['code']; ?></strong></p>
                </div>
                <div>
                    <p class="mb-1">Visit Date: <?php echo date('d F Y', strtotime($visitor['time_in'])); ?></p>
                </div>
                <div>
                    <p class="mb-1">Time IN: <?php echo date('h:i A', strtotime($visitor['time_in'])); ?></p>
                </div>
                <div>
                    <p>QR Code:</p>
                    <img src="<?php echo $qrFilePath; ?>" width="270px" height="270px" alt="QR Code">
                </div>
            </div>
            <div class="card-footer text-muted p-0">
            <p>City Youth Development Office &copy; <?php echo date('Y'); ?></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
