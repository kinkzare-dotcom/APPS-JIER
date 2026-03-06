<?php
// Function to redirect with a message
function redirect($url, $msg = '', $type = 'success')
{
    if ($msg) {
        $_SESSION['flash'] = [
            'message' => $msg,
            'type' => $type
        ];
    }
    // Ensure root-relative or absolute URL
    $redirect_url = (strpos($url, '/') === 0 || strpos($url, 'http') === 0) ? $url : '/' . $url;
    header("Location: $redirect_url");
    exit;
}

// Function to display flash messages
function display_flash()
{
    if (isset($_SESSION['flash'])) {
        $msg = $_SESSION['flash']['message'];
        $type = $_SESSION['flash']['type'];
        unset($_SESSION['flash']);

        $color = ($type == 'error') ? 'bg-red-100 text-red-700 border-red-400' : 'bg-green-100 text-green-700 border-green-400';

        echo "<div class='p-4 mb-4 text-sm rounded-lg border $color' role='alert'>
                <span class='font-medium'>" . ucfirst($type) . "!</span> $msg
              </div>";
    }
}

// Function to check if user is logged in
function check_login()
{
    if (!isset($_SESSION['user_id'])) {
        redirect('/login.php', 'Silahkan login terlebih dahulu.', 'error');
    }
}

// Function to check specific role
function check_role($role)
{
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != $role) {
        redirect('/index.php', 'Akses ditolak.', 'error');
    }
}

// Sanitize user input
function sanitize($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}
// Function to display SweetAlert2 flash messages
function display_swal()
{
    if (isset($_SESSION['flash'])) {
        $msg = $_SESSION['flash']['message'];
        $type = $_SESSION['flash']['type'];
        $title = $type === 'success' ? 'Berhasil!' : 'Oops...';

        echo "<script>
            Swal.fire({
                title: '$title',
                text: '$msg',
                icon: '$type',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>";
        unset($_SESSION['flash']);
    }
}
?>
