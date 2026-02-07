<?php
session_start();

// 🔐 ইউজারলিস্ট
$users = ['sk' => 'cyber'];

// 🔓 লগআউট
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// 🔐 লগইন পেইজ
if (!isset($_SESSION['user'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
        $u = $_POST['username'];
        $p = $_POST['password'];
        if (isset($users[$u]) && $users[$u] === $p) {
            $_SESSION['user'] = $u;
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "❌ Invalid username or password!";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>SK Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Goldman&display=swap');
    body { background:#000; font-family:"Goldman",sans-serif; color:#0ff; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
    .login-box { background:#111; padding:30px; border:2px solid #0ff; border-radius:10px; width:300px; }
    h2 { text-align:center; }
    input[type=text], input[type=password], input[type=submit] {
        width:100%; padding:10px; margin-top:10px; background:#222; color:#0ff; border:1px solid #0ff; border-radius:5px;
    }
    input[type=submit]:hover { background:#0ff; color:#000; cursor:pointer; }
    .error { color:#faa; background:#500; padding:10px; margin-top:10px; border-radius:5px; text-align:center; }
    </style>
    </head>
    <body>
    <div class="login-box">
        <h2>🌐 SK Hosting Panel Login</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// ✅ ইউজার ইন
$user = $_SESSION['user'];
$userDir = "uploads/$user/";
if (!is_dir($userDir)) mkdir($userDir, 0755, true);

$message = "";
$lastLink = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileUp'])) {
    $f = $_FILES['fileUp'];
    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($f['name']));
    $path = $userDir . $safeName;
    if (move_uploaded_file($f['tmp_name'], $path)) {
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/$path";
        $message = "<div class='msg success'>✅ Upload successful!</div>";
        $lastLink = "<div class='copy-box'>
                        <input type='text' value='$url' id='fileLink' readonly>
                        <button onclick='copyLink()'>📋 Copy Link</button>
                    </div>";
    } else {
        $message = "<div class='msg error'>❌ Upload failed!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SK Hosting Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
@import url('https://fonts.googleapis.com/css2?family=Goldman&display=swap');
body { margin:0; padding:0; font-family:"Goldman",sans-serif; background:#000; color:#0ff; }
header { text-align:center; padding:30px; background:linear-gradient(90deg,#000,#00ffff1a); border-bottom:2px solid #0ff;}
header h1 { margin:0; font-size:2.5rem; }
.container { max-width:600px; margin:40px auto; background:#111; padding:20px; border:2px solid #0ff; border-radius:10px; }
form label, input[type=file], input[type=submit] { display:block; width:100%; margin-top:15px; font-size:1rem; }
input[type=file], input[type=submit] { padding:10px; border:2px solid #0ff; background:#222; color:#fff; }
input[type=submit] { cursor:pointer; transition:.3s; }
input[type=submit]:hover { background:#0ff; color:#000; }
.msg { margin:15px 0; padding:10px; border-radius:5px; }
.success { background:#055; color:#afa; }
.error { background:#500; color:#faa; }
footer { text-align:center; padding:20px; border-top:1px solid #0ff; font-size:0.9rem; color:#aaa; }
.logout { text-align:center; margin-top:20px; }
.logout a { color:#0ff; text-decoration:none; background:#111; padding:5px 15px; border:1px solid #0ff; border-radius:5px; }
.logout a:hover { background:#0ff; color:#000; }
.copy-box { display:flex; margin:20px 0; }
.copy-box input { flex:1; padding:10px; background:#222; border:1px solid #0ff; color:#0ff; }
.copy-box button { margin-left:10px; padding:10px 20px; background:#0ff; color:#000; border:none; cursor:pointer; border-radius:5px; }
.copy-box button:hover { background:#0cc; }
.loading { position:fixed; top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9); display:flex;flex-direction:column;align-items:center;justify-content:center; color:#fff; font-size:1.5rem; z-index:9999; }
.loading::after { content:""; margin-top:20px;width:40px;height:40px;border:5px solid #0ff;border-top-color:transparent;border-radius:50%;animation:spin 1s linear infinite; }
@keyframes spin { to{transform:rotate(360deg);} }
</style>
</head>
<body>
<div class="loading">Loading...</div>
<header>
  <h1>🌐 SK Hosting Panel</h1>
</header>

<div class="container">
  <form action="" method="post" enctype="multipart/form-data">
    <label for="fileUp">Choose file to upload:</label>
    <input type="file" name="fileUp" id="fileUp" required>
    <input type="submit" value="Upload File">
  </form>
  <?= $message ?>
  <?= $lastLink ?>
  <div class="logout">
    <a href="?logout=1">🚪 Logout (<?= htmlspecialchars($user) ?>)</a>
  </div>
</div>

<footer>
  &copy; <?= date("Y") ?> <span style="color:#0ff;">SK CYBER GANG</span> | All rights reserved.
</footer>

<script>
function copyLink() {
  let input = document.getElementById("fileLink");
  input.select();
  input.setSelectionRange(0, 99999);
  document.execCommand("copy");
  alert("✅ Link copied!");
}
window.addEventListener('load', ()=>{ setTimeout(()=>document.querySelector('.loading').style.display='none',1400); });
</script>
</body>
</html>