<?php
session_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
  <title>POS Giriş</title>
  <style>
  body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: url("resii.jpeg") center center / cover no-repeat;
    background-color: #000;  /* Resim boşluk bırakırsa siyah görünür */
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #333;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    user-select: none;
  }

  .login-box {
    width: 90%;
    max-width: 400px;
    padding: 40px 30px;
    background: rgba(255, 255, 255, 0.15); /* cam efekti */
    backdrop-filter: blur(12px);
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: 0 8px 30px rgba(0,0,0,0.4);
    text-align: center;
    animation: fadeIn 0.8s ease;
  }

  .login-box h2 {
    margin-bottom: 25px;
    font-size: 24px;
    font-weight: bold;
    color: #fff;
    text-shadow: 0 2px 6px rgba(0,0,0,0.3);
  }

  /* Input alanları */
  .login-box input {
    width: 100%;
    margin: 12px 0;
    padding: 15px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.4);
    font-size: 16px;
    background: rgba(255,255,255,0.2);
    color: #fff;
    outline: none;
    transition: all 0.3s;
  }

  .login-box input::placeholder {
    color: #ddd;
  }

  .login-box input:focus {
    background: rgba(255,255,255,0.3);
    border-color: #4e73df;
    box-shadow: 0 0 10px rgba(78,115,223,0.5);
  }

  /* Buton */
  .login-box button {
    width: 100%;
    margin-top: 20px;
    padding: 16px;
    border-radius: 12px;
    border: none;
    font-size: 18px;
    font-weight: bold;
    background: linear-gradient(135deg, #4e73df, #2e59d9);
    color: #fff;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
  }

  .login-box button:hover {
    background: linear-gradient(135deg, #2e59d9, #1e3ca3);
    transform: translateY(-2px);
  }

  /* Animasyon */
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Tablet uyumu */
  @media (max-width: 768px) {
    .login-box {
      padding: 30px 20px;
    }
    .login-box h2 {
      font-size: 22px;
    }
  }

  /* Mobil uyumu */
  @media (max-width: 480px) {
    body {
      background-attachment: scroll; /* Mobilde sabit arka plan bug yapar */
    }
    .login-box {
      width: 95%;
      padding: 25px 20px;
    }
    .login-box h2 {
      font-size: 20px;
    }
    .login-box input, .login-box button {
      font-size: 16px;
      padding: 12px;
    }
  }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>👨‍🍳 Hoşgeldiniz Personel Girişi</h2>
    <?php if (isset($_GET['error'])) echo "<p style='color:red'>" . htmlspecialchars($_GET['error']) . "</p>"; ?>
    <form method="post" action="verify.php">
      <input type="text" name="username" placeholder="Kullanıcı Adı" required>
      <input type="password" name="password" placeholder="Şifre" required>
      <button type="submit">Giriş Yap</button>
    </form>
  </div>
</body>
</html>