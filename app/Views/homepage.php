<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Justfaucet</title>

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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('/assets/main/css/index.css') ?>">


</head>

<body>
    <?= $this->include('layout/navbar') ?>

    <main class="fade-in-up">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="hero-title">Welcome to JustFaucet</h1>
                        <p class="hero-text">The most advanced faucet platform for crypto enthusiasts.</p>
                        <!-- If User Is Logged in, Redirect to Dashboard if they click Login or register -->


                        <?php if (auth()->loggedIn()): ?>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">

                                <a href="/dashboard" class="btn btn-success btn-lg px-4">Dashboard</a>
                            </div>
                        <?php else: ?>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <a href="<?= base_url('register') . (isset($referred_by) ? "?ref={$referred_by}" : '') ?>"
                                    class="btn btn-primary btn-lg px-4 me-md-2">Get Started</a>
                                <a href="/login" class="btn btn-success btn-lg px-4">Login</a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <h2 class="text-center mb-5 welcome-title">Why Choose Us</h2>
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-card text-center">
                            <i class="fas fa-coins feature-icon"></i>
                            <h3>Instant Rewards</h3>
                            <p>Claim your crypto rewards instantly with our automated system.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card text-center">
                            <i class="fas fa-shield-alt feature-icon"></i>
                            <h3>Secure Platform</h3>
                            <p>Your security is our top priority with advanced protection measures.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card text-center">
                            <i class="fas fa-users feature-icon"></i>
                            <h3>Referral Program</h3>
                            <p>Earn extra rewards by inviting your friends to join our platform.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?= $this->include('layout/footer') ?>


    <!-- Bootstrap JS Bundle -->
    <script src="<?= base_url("assets/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>


    <!-- THEME TOGGLE -->
    <script src="<?= base_url('assets/main/js/theme.js') ?>"></script>
</body>

</html>