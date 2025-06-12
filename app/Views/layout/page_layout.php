<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JustFaucet</title>

    <!-- Prevent theme flickering -->
    <script>
        (function () {
            // Get theme from localStorage or default to dark
            const storedTheme = localStorage.getItem('bsTheme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', storedTheme);
        })();
    </script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="<?= base_url(
        "/assets/sweetalert2/css/sweetalert2.min.css"
    ) ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"
        integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- CUSTOM CSS -->
    <?php echo minifier("main.min.css"); ?>



    <?php if (
        $title === "Referral" ||
        $title === "Manage Users" ||
        $title === "Manage Withdrawals"
    ): ?>

        <!-- DataTables CSS -->
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap.min.css"
            integrity="sha512-BMbq2It2D3J17/C7aRklzOODG1IQ3+MHw3ifzBHMBwGO/0yUqYmsStgBjI0z5EYlaDEFnvYV7gNYdD3vFLRKsA=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="<?= base_url(
            "assets/datatables/css/datatables.css"
        ) ?>">
    <?php endif; ?>


    <style>
        /* Ensure proper z-index hierarchy */
        .navbar {
            z-index: 1030 !important;
        }

        .offcanvas {
            z-index: 1045 !important;
        }

        .offcanvas-backdrop {
            z-index: 1040 !important;
        }

        /* Improved sidebar styling */
        .offcanvas-start {
            width: 280px !important;
        }

        /* Better list group item styling */
        .list-group-item-action {
            transition: all 0.2s ease-in-out;
            border: none;
            border-radius: 0;
        }

        .list-group-item-action:hover {
            background-color: var(--bs-primary-bg-subtle);
            padding-left: 1.5rem;
            transform: translateX(5px);
        }

        .list-group-item-action.active {
            background-color: var(--bs-primary);
            color: white;
            border-left: 4px solid var(--bs-primary-text-emphasis);
        }

        .list-group-item-action.active i {
            color: white !important;
        }

        /* Section headers styling */
        .list-group-item.bg-primary,
        .list-group-item.bg-info {
            font-size: 0.875rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Icon spacing and colors */
        .list-group-item i {
            width: 20px;
            text-align: center;
        }

        /* Mobile optimizations */
        @media (max-width: 991.98px) {
            .offcanvas-body {
                padding: 0 !important;
            }

            .list-group-item-action {
                padding: 0.875rem 1rem;
            }

            .list-group-item-action:hover {
                transform: none;
                padding-left: 1rem;
            }
        }

        /* Main content area adjustments for desktop */
        @media (min-width: 992px) {
            body {
                padding-top: 60px;

            }

            /* When logged in, adjust main content to account for sidebar */
            .main-content {
                transform: translateY(-16rem);

                margin-left: 280px;
                padding-top: 0;
                /* Same as sidebar width */
            }

            /* When not logged in, no sidebar offset needed */
            .main-content.no-sidebar {
                margin-left: 0;
            }
        }

        /* Dark mode enhancements */
        [data-bs-theme="dark"] .list-group-item-action:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        [data-bs-theme="dark"] .offcanvas-header {
            background-color: var(--bs-dark);
            border-color: var(--bs-border-color);
        }

        /* Smooth animations */
        .list-group-item-action i {
            transition: transform 0.2s ease;
        }

        .list-group-item-action:hover i {
            transform: scale(1.1);
        }

        /* Better spacing for navbar on mobile */
        @media (max-width: 575.98px) {
            .navbar-brand {
                font-size: 1.1rem;
            }

            .form-check-label span {
                display: none !important;
            }
        }

        /* Ensure content doesn't overlap with fixed navbar */
        body {
            padding-top: 60px;
            /* Reduced from 76px */
        }


        .main-content .container-fluid {
            padding-top: 1rem;

        }
    </style>
</head>


<body>
    <?php if (auth()->loggedIn()): ?>
        <!-- Include sidebar -->
        <?= $this->include("layout/sidebar") ?>
    <?php endif; ?>

    <!-- Include navbar -->
    <?= $this->include("layout/navbar") ?>



    <!-- Main content area -->
    <div class="main-content  <?= auth()->loggedIn() ? "" : "no-sidebar" ?>">
        <div class="container-fluid py-4 mx-auto">
            <?= $this->renderSection("content") ?>
        </div>
    </div>

    <?= $this->include("layout/footer") ?>



    <!-- Bootstrap, jQuery  -->
    <!-- DataTables & SweetAlert, jquery Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>


    <script src="<?= base_url(
        "assets/sweetalert2/js/sweetalert2.all.min.js"
    ) ?>"></script>



    <?= $this->renderSection("scripts") ?>

    <?php echo minifier("main.min.js"); ?>

</body>

</html>