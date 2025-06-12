<footer class="py-4 mx-auto mb-0 border-top text-center">
    <div class="container-fluid">
        <div class="d-flex flex-column align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center">
                <i class="fa-solid fa-sun fa-sm me-2" style="color: #FFD43B;"></i>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                </div>
                <i class="fa-solid fa-moon fa-sm ms-2" style="color: #d7e4f9;"></i>
            </div>

            <p class="text-body-secondary mb-0">
                &copy; <?= date("Y") ?> JustFaucet. All rights reserved.
                <span class="d-block d-sm-inline mt-1 mt-sm-0 ms-sm-2"><?= date(
                    "H:i"
                ) ?></span>
            </p>


        </div>
</footer>