<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justfaucet</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"
        integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">




</head>

<body class="pt-5">
    <?= $this->include("layout/navbar") ?>

    <div class="container mt-5">
        <div class="row min-vh-100 align-items-center py-5">
            <div class="col-lg-6">
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-gradient rounded-circle p-3 me-3">
                            <i class="fas fa-droplet text-white fs-3"></i>
                        </div>
                        <span class="badge bg-primary fs-6">Beta Version</span>
                    </div>
                    <h1 class="display-3 fw-bold mb-3 text-primary">Welcome to JustFaucet</h1>
                    <p class="lead mb-4 text-muted">The most advanced faucet platform for crypto enthusiasts. Earn points every 5 minutes with our secure and reliable system.</p>
                </div>

                <?php if (auth()->loggedIn()): ?>
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="/dashboard" class="btn btn-success btn-lg px-5 py-3 fw-semibold">
                            <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                        </a>
                    </div>
                <?php else: ?>
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="<?= base_url("register") .
                            (isset($referred_by)
                                ? "?ref={$referred_by}"
                                : "") ?>"
                            class="btn btn-primary btn-lg px-5 py-3 me-md-2 fw-semibold">
                            <i class="fas fa-rocket me-2"></i>Get Started
                        </a>
                        <a href="/login" class="btn btn-outline-success btn-lg px-5 py-3 fw-semibold">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-6 text-center">
                <div class="position-relative">
                    <div class="bg-primary bg-gradient rounded-circle p-5 d-inline-flex mb-4" style="width: 200px; height: 200px;">
                        <i class="fas fa-coins text-white" style="font-size: 5rem;"></i>
                    </div>
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                        <div class="bg-success bg-gradient rounded-circle p-2 position-absolute" style="top: 20%; right: 20%; animation: pulse 2s infinite;">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                    </div>
                </div>
                <h3 class="fw-bold text-primary mb-2">Start Earning Now!</h3>
                <p class="text-muted">Join thousands of users already earning crypto rewards</p>
            </div>
        </div>

        <div class="py-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-primary mb-2">Why Choose JustFaucet</h2>
                <p class="lead text-muted">Discover what makes us the best crypto faucet platform</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center border-0 shadow-sm bg-transparent hover-card">
                        <div class="card-body p-4">
                            <div class="bg-danger bg-gradient rounded-circle p-3 d-inline-flex mb-3">
                                <i class="fa-solid fa-fire text-white fs-2"></i>
                            </div>
                            <h3 class="h5 fw-semibold text-primary mb-2">Daily Challenges</h3>
                            <p class="text-muted mb-0">Complete daily challenges and earn bonus points to boost your earnings.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center border-0 shadow-sm bg-transparent hover-card">
                        <div class="card-body p-4">
                            <div class="bg-info bg-gradient rounded-circle p-3 d-inline-flex mb-3">
                                <i class="fas fa-trophy text-white fs-2"></i>
                            </div>
                            <h3 class="h5 fw-semibold text-primary mb-2">Level System</h3>
                            <p class="text-muted mb-0">Earn EXP for every claim you make and level up to unlock rewards.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center border-0 shadow-sm bg-transparent hover-card">
                        <div class="card-body p-4">
                            <div class="bg-success bg-gradient rounded-circle p-3 d-inline-flex mb-3">
                                <i class="fas fa-users text-white fs-2"></i>
                            </div>
                            <h3 class="h5 fw-semibold text-primary mb-2">Referral Program</h3>
                            <p class="text-muted mb-0">Earn 10% commission by inviting your friends and building your network.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center border-0 shadow-sm bg-transparent hover-card">
                        <div class="card-body p-4">
                            <div class="bg-warning bg-gradient rounded-circle p-3 d-inline-flex mb-3">
                                <i class="fas fa-money-bill text-white fs-2"></i>
                            </div>
                            <h3 class="h5 fw-semibold text-primary mb-2">Instant Withdrawal</h3>
                            <p class="text-muted mb-0">Withdraw instantly to your FaucetPay account with minimum fees.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $this->include("layout/footer") ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"
        integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <!-- THEME TOGGLE -->
    <?php echo minifier("main.min.js"); ?>

    <style>
        .hover-card {
            transition: all 0.3s ease;
        }
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</body>

</html>
