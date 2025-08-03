<?php
session_start();

// إعدادات قاعدة البيانات (يمكن تخصيصها حسب الحاجة)
$db_host = 'localhost';
$db_name = 'login_system';
$db_user = 'root';
$db_pass = '';

// بيانات اختبارية للمستخدمين (في التطبيق الحقيقي، ستكون في قاعدة البيانات)
$demo_users = [
    'admin' => [
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'email' => 'admin@example.com',
        'name' => 'المدير'
    ],
    'user@example.com' => [
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'email' => 'user@example.com', 
        'name' => 'مستخدم تجريبي'
    ]
];

// دالة للاستجابة بصيغة JSON
function sendResponse($success, $message, $data = null) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// دالة تنظيف البيانات
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// دالة التحقق من صحة البريد الإلكتروني
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// دالة التحقق من صحة اسم المستخدم
function validateUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

// التحقق من أن الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'طريقة الطلب غير مسموحة');
}

// التحقق من وجود البيانات المطلوبة
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    sendResponse(false, 'البيانات المطلوبة غير موجودة');
}

// تنظيف البيانات
$username = sanitizeInput($_POST['username']);
$password = sanitizeInput($_POST['password']);
$remember = isset($_POST['remember']) ? true : false;

// التحقق من صحة البيانات
$errors = [];

// التحقق من اسم المستخدم
if (empty($username)) {
    $errors[] = 'يرجى إدخال اسم المستخدم أو البريد الإلكتروني';
} elseif (strlen($username) < 3) {
    $errors[] = 'يجب أن يكون اسم المستخدم 3 أحرف على الأقل';
} elseif (!validateEmail($username) && !validateUsername($username)) {
    $errors[] = 'تنسيق اسم المستخدم أو البريد الإلكتروني غير صحيح';
}

// التحقق من كلمة المرور
if (empty($password)) {
    $errors[] = 'يرجى إدخال كلمة المرور';
} elseif (strlen($password) < 6) {
    $errors[] = 'يجب أن تكون كلمة المرور 6 أحرف على الأقل';
}

// إذا كانت هناك أخطاء، إرسال الأخطاء
if (!empty($errors)) {
    sendResponse(false, implode(', ', $errors));
}

// محاولة تسجيل الدخول
$user_found = false;
$user_data = null;

// البحث في البيانات التجريبية
foreach ($demo_users as $key => $user) {
    if ($key === $username || $user['email'] === $username) {
        if (password_verify($password, $user['password'])) {
            $user_found = true;
            $user_data = $user;
            $user_data['username'] = $key;
            break;
        }
    }
}

// في التطبيق الحقيقي، ستكون هذه استعلامات قاعدة البيانات
/*
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // البحث عن المستخدم
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $username);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $user_found = true;
        $user_data = $user;
    }
    
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    sendResponse(false, 'حدث خطأ في النظام، يرجى المحاولة لاحقاً');
}
*/

if (!$user_found) {
    // تسجيل محاولة دخول فاشلة
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'غير معروف';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'غير معروف';
    $timestamp = date('Y-m-d H:i:s');
    
    error_log("Failed login attempt - IP: $ip, Username: $username, Time: $timestamp, User Agent: $user_agent");
    
    sendResponse(false, 'اسم المستخدم أو كلمة المرور غير صحيحة');
}

// نجح تسجيل الدخول
$_SESSION['user_id'] = $user_data['username'];
$_SESSION['user_name'] = $user_data['name'];
$_SESSION['user_email'] = $user_data['email'];
$_SESSION['login_time'] = time();

// إعداد الكوكيز للتذكر
if ($remember) {
    $remember_token = bin2hex(random_bytes(32));
    setcookie('remember_token', $remember_token, time() + (30 * 24 * 60 * 60), '/', '', true, true); // 30 يوم
    
    // في التطبيق الحقيقي، احفظ الرمز في قاعدة البيانات
    $_SESSION['remember_token'] = $remember_token;
}

// تسجيل عملية دخول ناجحة
$ip = $_SERVER['REMOTE_ADDR'] ?? 'غير معروف';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'غير معروف';
$timestamp = date('Y-m-d H:i:s');

error_log("Successful login - IP: $ip, Username: $username, Time: $timestamp");

// إرسال استجابة النجاح
sendResponse(true, 'تم تسجيل الدخول بنجاح', [
    'user_name' => $user_data['name'],
    'redirect_url' => 'dashboard.php'
]);
?>