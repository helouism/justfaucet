<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JustFaucet</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('/css/bootstrap/bootstrap.min.css') ?>">
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

    <link rel="stylesheet" href="<?= base_url('/css/index.css') ?>">

</head>

<body>

    <?= $this->include('layout/navbar') ?>



    <!-- Main Content -->
    <div class="main-content">
        <?= $this->include('layout/sidebar') ?>
        <?= $this->renderSection('content') ?>
    </div>
    <?= $this->include('layout/footer') ?>



    <!-- Bootstrap, Sweetalert2, jQuery  -->
    <script src="<?= base_url("/js/bootstrap/bootstrap.bundle.min.js") ?>"></script>


    <!-- THEME TOGGLE-->
    <script src="<?= base_url('js/theme.js') ?>"></script>

    <!-- SIDEBAR TOGGLE -->
    <script src="<?= base_url('/js/script.js') ?>"></script>



</body>

</html>