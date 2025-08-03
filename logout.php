<?php
session_start();

// تنظيف جميع متغيرات الجلسة
$_SESSION = array();

// إذا كان هناك ملف تعريف ارتباط للجلسة، قم بحذفه
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// حذف كوكيز التذكر إن وجدت
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

// تدمير الجلسة
session_destroy();

// إعادة التوجيه إلى صفحة تسجيل الدخول
header('Location: login.html?logged_out=1');
exit();
?>