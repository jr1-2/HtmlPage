<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'مستخدم';
$user_email = $_SESSION['user_email'] ?? '';
$login_time = $_SESSION['login_time'] ?? time();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            direction: rtl;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            color: #333;
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .welcome-card h2 {
            color: #333;
            font-size: 32px;
            margin-bottom: 15px;
        }

        .welcome-card p {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-title {
            color: #333;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .stat-value {
            color: #667eea;
            font-size: 24px;
            font-weight: bold;
        }

        .login-info {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }

        .login-info h3 {
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            .user-info {
                width: 100%;
                justify-content: space-between;
            }

            .welcome-card {
                padding: 30px 20px;
            }

            .welcome-card h2 {
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1><i class="fas fa-tachometer-alt"></i> لوحة التحكم</h1>
        <div class="user-info">
            <div class="user-avatar">
                <?php echo mb_substr($user_name, 0, 1); ?>
            </div>
            <span>مرحباً، <?php echo htmlspecialchars($user_name); ?></span>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                تسجيل الخروج
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <h2>مرحباً بك في لوحة التحكم</h2>
            <p>تم تسجيل دخولك بنجاح إلى النظام</p>
            <i class="fas fa-check-circle" style="font-size: 64px; color: #27ae60;"></i>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-title">الملف الشخصي</div>
                <div class="stat-value">مكتمل</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-title">أمان الحساب</div>
                <div class="stat-value">محمي</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-title">وقت الجلسة</div>
                <div class="stat-value" id="sessionTime">--</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-title">الإشعارات</div>
                <div class="stat-value">0</div>
            </div>
        </div>

        <div class="login-info">
            <h3><i class="fas fa-info-circle"></i> معلومات تسجيل الدخول</h3>
            <div class="info-item">
                <span class="info-label">اسم المستخدم:</span>
                <span class="info-value"><?php echo htmlspecialchars($_SESSION['user_id']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">البريد الإلكتروني:</span>
                <span class="info-value"><?php echo htmlspecialchars($user_email); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">وقت تسجيل الدخول:</span>
                <span class="info-value"><?php echo date('Y-m-d H:i:s', $login_time); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">عنوان IP:</span>
                <span class="info-value"><?php echo $_SERVER['REMOTE_ADDR'] ?? 'غير معروف'; ?></span>
            </div>
        </div>
    </div>

    <script>
        // حساب وقت الجلسة
        const loginTime = <?php echo $login_time; ?> * 1000;
        
        function updateSessionTime() {
            const now = new Date().getTime();
            const elapsed = Math.floor((now - loginTime) / 1000);
            
            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;
            
            const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            document.getElementById('sessionTime').textContent = timeString;
        }
        
        // تحديث الوقت كل ثانية
        setInterval(updateSessionTime, 1000);
        updateSessionTime();

        // تأثيرات إضافية
        document.addEventListener('DOMContentLoaded', function() {
            // تأثير تحميل البطاقات
            const cards = document.querySelectorAll('.stat-card, .welcome-card, .login-info');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>