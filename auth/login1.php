<?php
include "../config/db.php";
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $_SESSION['message'] = "<div class='text-center'>Username dan kata sandi wajib diisi!</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "login";
    } else {
        $query = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['id_admin'] = $row['id_admin'];
                $_SESSION['message'] = "Login berhasil!";
                $_SESSION['message_type'] = "success";
                $_SESSION['message_section'] = "login";
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['message'] = "<div class='text-center'>Username atau kata sandi salah!</div>";
                $_SESSION['message_type'] = "danger";
                $_SESSION['message_section'] = "login";
            }
        } else {
            $_SESSION['message'] = "<div class='text-center'>Username atau kata sandi salah!</div>";
            $_SESSION['message_type'] = "danger";
            $_SESSION['message_section'] = "login";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
  <main>
    <header id="header" class="header fixed-top d-flex align-items-center">
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </header>

    <aside id="sidebar" class="sidebar">
      <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
          <a class="nav-link collapsed" href="../index.php">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link collapsed" href="../perkara.php">
            <i class="bi bi-journal-text"></i>
            <span>Data Perkara</span>
          </a>
        </li> 
        <li class="nav-heading">__________________________________________________</li>
        <li class="nav-item">
          <a class="nav-link collapsed" href="../jumlah_admin.php">
            <i class="bi bi-person-lines-fill"></i>
            <span>Jumlah Admin</span>
          </a>
        </li>
      </ul>
    </aside>

    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="pt-4 pb-2">
                      <h5 class="card-title text-center pb-0 fs-4">Jadwal Sidang</h5>
                      <p class="text-center">Masukkan username & kata sandi</p>
                    </div>
                    <?php
                    if (isset($_SESSION['message']) && $_SESSION['message_section'] == "login") {
                        $message = $_SESSION['message'];
                        $message_type = $_SESSION['message_type'];
                        echo "<div id='alertMessage' class='alert alert-$message_type' role='alert'>
                                $message
                              </div>";
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        unset($_SESSION['message_section']);
                    }
                    ?>

                    <form action="login.php" method="POST" class="row g-3 needs-validation" novalidate>
                      <div class="col-12">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group has-validation">
                          <input type="text" name="username" class="form-control" id="username" required>
                        </div>
                      </div>
                      <div class="col-12">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                          <input type="password" name="password" class="form-control" id="password" required>
                          <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class='bi bi-eye-fill'></i>
                          </button>
                        </div><br>
                      </div>
                      <div class="col-12">
                        <button class="btn btn-primary w-100" type="submit">Login</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <script src="../assets/js/main.js"></script>
  <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        let passwordField = document.getElementById('password');
        let icon = this.querySelector('i');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('bi-eye-fill');
            icon.classList.add('bi-eye-slash-fill');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('bi-eye-slash-fill');
            icon.classList.add('bi-eye-fill');
        }
    });
    setTimeout(function() {
        let alertBox = document.getElementById("alertMessage");
        if (alertBox) {
            alertBox.style.transition = "opacity 0.5s";
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 300); 
        }
    }, 3000);
  </script>
</body>
</html>