</main>
    </div> <!-- End .dashboard-container -->
    <?php if (function_exists('display_swal'))
    display_swal(); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            window.showToast = function(icon, title) {
                Toast.fire({
                    icon: icon,
                    title: title
                });
            };

            <?php if (isset($_SESSION['flash'])): ?>
                showToast("<?php echo $_SESSION['flash']['type']; ?>", "<?php echo $_SESSION['flash']['message']; ?>");
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
