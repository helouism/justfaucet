<!-- Footer -->
<?php if (auth()->loggedIn()): ?>
    <footer class="footer">
        <div class="container-fluid">
            <p class="mb-0">© 2024 FancyApp. All rights reserved.</p>
        </div>
    </footer>
<?php else: ?>
    <footer class="footer mx-auto">
        <div class="container-fluid">
            <p class="mb-0">© 2024 FancyApp. All rights reserved.</p>
        </div>
    </footer>
<?php endif; ?>