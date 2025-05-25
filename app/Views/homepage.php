<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to FancyApp</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('/css/index.css') ?>">

    <style>
        /* Landing page specific styles */
        .hero-section {
            padding: 120px 0 80px;
            margin-top: var(--navbar-height);
            min-height: calc(100vh - var(--navbar-height));
            display: flex;
            align-items: center;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .features-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 4rem 0;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, #fff, #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .hero-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.25rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-section {
                padding: 80px 0 40px;
            }
        }
    </style>
</head>

<body>
    <?= $this->include('layout/navbar') ?>

    <main class="fade-in-up">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="hero-title">Welcome to FancyApp</h1>
                        <p class="hero-text">The most advanced faucet platform for crypto enthusiasts.</p>
                        <!-- If User Is Logged in, Redirect to Dashboard if they click Login or register -->


                        <?php if (auth()->loggedIn()): ?>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <a href="/dashboard" class="btn btn-light btn-lg px-4 me-md-2">Get Started</a>
                                <a href="/dashboard" class="btn btn-outline-light btn-lg px-4">Login</a>
                            </div>
                        <?php else: ?>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <a href="<?= url_to('register') . (isset($referred_by) ? "?ref={$referred_by}" : '') ?>"
                                    class="btn btn-light btn-lg px-4 me-md-2">Get Started</a>
                                <a href="/login" class="btn btn-outline-light btn-lg px-4">Login</a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('/js/script.js') ?>"></script>
</body>

</html>