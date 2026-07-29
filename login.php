<?php
require_once('includes/init.php');

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// LOGIKA 1: AKSES LANGSUNG PENGUNJUNG / GUEST (TANPA LOGIN)
if (isset($_GET['action']) && $_GET['action'] === 'guest') {
    $_SESSION["user_id"] = 0;
    $_SESSION["username"] = "Pengunjung";
    $_SESSION["role"] = "user"; // Role non-admin
    
    redirect_to("dashboard.php");
    exit;
}

// Inisialisasi variabel
$errors = array();
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// LOGIKA 2: LOGIN KHUSUS ADMIN / USER TERDAFTAR
if (isset($_POST['submit'])) {

    // Validasi input
    if (empty($username)) {
        $errors[] = 'Username tidak boleh kosong';
    }
    if (empty($password)) {
        $errors[] = 'Password tidak boleh kosong';
    }

    // Jika tidak ada error, lanjut ke proses login
    if (empty($errors)) {
        // Gunakan prepared statement untuk keamanan
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM user WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $hashed_password = sha1($password);
            if ($row['password'] === $hashed_password) {
                // Set session admin/user
                $_SESSION["user_id"] = $row["id_user"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["role"] = $row["role"];
                
                // Redirect ke dashboard
                redirect_to("dashboard.php");
                exit;
            } else {
                $errors[] = 'Username atau password salah!';
            }
        } else {
            $errors[] = 'Username atau password salah!';
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SPK MOORA - Pamella Supermarket</title>

    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet" />
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon" />

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 50% 10%, #fce4ec 0%, #f8bbd0 50%, #f48fb1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 15px;
            margin: 0;
        }

        /* 3D Floating Card */
        .login-card-3d {
            background: #ffffff;
            border-radius: 28px;
            padding: 2.8rem 2.5rem;
            width: 100%;
            max-width: 520px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 
                0 20px 50px rgba(168, 40, 106, 0.22),
                0 10px 20px rgba(0, 0, 0, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card-3d:hover {
            transform: translateY(-4px);
            box-shadow: 
                0 28px 60px rgba(168, 40, 106, 0.28),
                0 12px 24px rgba(0, 0, 0, 0.06);
        }

        /* Top Header Branding */
        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-wrapper-3d {
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 22px;
            border-radius: 18px;
            box-shadow: 0 8px 20px rgba(216, 27, 96, 0.12), 0 2px 6px rgba(0,0,0,0.04);
            border: 1px solid #f1f5f9;
            margin-bottom: 1.25rem;
        }

        .badge-moora-3d {
            display: inline-block;
            background: linear-gradient(135deg, #d81b60 0%, #a8286a 100%);
            color: #ffffff;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(216, 27, 96, 0.3);
            margin-bottom: 1rem;
        }

        .main-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .sub-title {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.55;
            margin: 0 auto;
            max-width: 400px;
        }

        /* Form Controls */
        .input-group-3d {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .input-group-3d .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #be185d;
            font-size: 1.05rem;
            z-index: 5;
            transition: color 0.3s ease;
        }

        .input-group-3d .form-control {
            height: 54px;
            padding-left: 50px;
            padding-right: 48px;
            border-radius: 16px;
            border: 2px solid #f1f5f9;
            background-color: #f8fafc;
            font-size: 0.95rem;
            font-weight: 500;
            color: #1e293b;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.25s ease;
        }

        .input-group-3d .form-control:focus {
            background-color: #ffffff;
            border-color: #d81b60;
            box-shadow: 0 0 0 4px rgba(216, 27, 96, 0.12), inset 0 2px 4px rgba(0,0,0,0.01);
            outline: none;
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            z-index: 5;
            font-size: 1.05rem;
            padding: 4px;
            transition: color 0.2s ease;
        }

        .toggle-password:hover {
            color: #d81b60;
        }

        /* 3D Interactive Buttons */
        .btn-3d-primary {
            background: linear-gradient(135deg, #e11d48 0%, #be185d 100%);
            color: #ffffff;
            height: 54px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            box-shadow: 0 6px 0 #9f1239, 0 10px 20px rgba(225, 29, 72, 0.35);
            transition: all 0.15s ease;
            position: relative;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            cursor: pointer;
        }

        .btn-3d-primary:hover {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
            color: #ffffff;
            box-shadow: 0 6px 0 #9f1239, 0 12px 24px rgba(225, 29, 72, 0.45);
        }

        .btn-3d-primary:active {
            top: 4px;
            box-shadow: 0 2px 0 #9f1239, 0 4px 10px rgba(225, 29, 72, 0.3);
        }

        .btn-3d-secondary {
            background: #ffffff;
            color: #475569;
            height: 52px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.92rem;
            border: 2px solid #e2e8f0;
            box-shadow: 0 4px 0 #cbd5e1;
            transition: all 0.15s ease;
            position: relative;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            text-decoration: none !important;
        }

        .btn-3d-secondary:hover {
            background: #fff5f8;
            border-color: #f43f5e;
            color: #e11d48;
            box-shadow: 0 4px 0 #fda4af;
        }

        .btn-3d-secondary:active {
            top: 3px;
            box-shadow: 0 1px 0 #fda4af;
        }

        /* Divider */
        .divider-3d {
            display: flex;
            align-items: center;
            text-align: center;
            color: #94a3b8;
            font-size: 0.82rem;
            margin: 1.5rem 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .divider-3d::before,
        .divider-3d::after {
            content: '';
            flex: 1;
            border-bottom: 2px dashed #e2e8f0;
        }

        .divider-3d span {
            padding: 0 14px;
        }

        /* Footer Credits */
        .card-footer-cred {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
            color: #64748b;
            font-size: 0.82rem;
        }

        .card-footer-cred strong {
            color: #be185d;
        }
    </style>
</head>
<body>

    <div class="login-card-3d">
        
        <!-- Header Branding -->
        <div class="brand-header">
            <div>
                <span class="badge-moora-3d">
                    <i class="fas fa-calculator mr-1"></i> Metode MOORA
                </span>
            </div>
            
            <div class="logo-wrapper-3d">
                <img src="assets/img/pamella.jpg" alt="Pamella Supermarket" style="max-height: 46px; object-fit: contain;">
            </div>

            <h1 class="main-title">SPK Rekomendasi Produk</h1>
            <p class="sub-title">Pemilihan Produk Non-Makanan Terbaik<br><strong>Pamella Supermarket Yogyakarta</strong></p>
        </div>

        <!-- Alert Error -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger border-0 rounded-lg small mb-4 shadow-sm" style="background-color: #fff1f2; color: #e11d48; padding: 12px 16px; border-left: 4px solid #e11d48 !important;" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo implode('<br><i class="fas fa-exclamation-circle mr-2"></i>', $errors); ?>
            </div>
        <?php endif; ?>

        <!-- Form Akses Admin -->
        <form action="login.php" method="post">
            
            <!-- Input Username -->
            <div class="input-group-3d">
                <i class="fas fa-user input-icon"></i>
                <input autocomplete="off" type="text" value="<?php echo htmlentities($username); ?>" class="form-control" placeholder="Username Admin" name="username" />
            </div>

            <!-- Input Password -->
            <div class="input-group-3d">
                <i class="fas fa-lock input-icon"></i>
                <input autocomplete="off" type="password" id="passwordInput" class="form-control" name="password" placeholder="Password Admin" />
                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>

            <!-- Tombol Login Admin -->
            <button name="submit" type="submit" class="btn-3d-primary mt-4">
                <i class="fas fa-sign-in-alt mr-2"></i> Masuk Administrator
            </button>

        </form>

        <div class="divider-3d">
            <span>atau akses publik</span>
        </div>

        <!-- Tombol Direct Link Ke Guest Auth (Langsung Masuk Tanpa Login) -->
        <a href="login.php?action=guest" class="btn-3d-secondary">
            <i class="fas fa-eye mr-2"></i> Lihat Rekomendasi (Tanpa Login)
        </a>

        <!-- Footer Info -->
        <div class="card-footer-cred">
            &copy; <?= date('Y'); ?> SPK MOORA — <strong>Asysifa Sandra Devi, S.Kom</strong>
        </div>

    </div>

    <!-- Scripts -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $("#togglePassword").click(function() {
                const passwordInput = $("#passwordInput");
                const type = passwordInput.attr("type") === "password" ? "text" : "password";
                passwordInput.attr("type", type);
                $(this).toggleClass("fa-eye fa-eye-slash");
            });
        });
    </script>
</body>
</html>