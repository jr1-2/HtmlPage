document.addEventListener('DOMContentLoaded', function() {
    // عناصر النموذج
    const loginForm = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const loginBtn = document.getElementById('loginBtn');
    const loader = document.getElementById('loader');
    const btnText = document.querySelector('.btn-text');
    const successMessage = document.getElementById('successMessage');
    const errorAlert = document.getElementById('errorAlert');
    const errorText = document.getElementById('errorText');

    // تبديل رؤية كلمة المرور
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // تغيير أيقونة العين
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
        
        // إضافة تأثير حركي
        this.style.transform = 'scale(1.2)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 200);
    });

    // التحقق من صحة البيانات في الوقت الفعلي
    usernameInput.addEventListener('input', function() {
        validateField(this, validateUsername);
    });

    passwordInput.addEventListener('input', function() {
        validateField(this, validatePassword);
    });

    // التحقق من صحة اسم المستخدم
    function validateUsername(value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
        
        if (!value.trim()) {
            return { isValid: false, message: 'يرجى إدخال اسم المستخدم أو البريد الإلكتروني' };
        }
        
        if (value.length < 3) {
            return { isValid: false, message: 'يجب أن يكون اسم المستخدم 3 أحرف على الأقل' };
        }
        
        if (!emailRegex.test(value) && !usernameRegex.test(value)) {
            return { isValid: false, message: 'تنسيق اسم المستخدم أو البريد الإلكتروني غير صحيح' };
        }
        
        return { isValid: true, message: '' };
    }

    // التحقق من صحة كلمة المرور
    function validatePassword(value) {
        if (!value) {
            return { isValid: false, message: 'يرجى إدخال كلمة المرور' };
        }
        
        if (value.length < 6) {
            return { isValid: false, message: 'يجب أن تكون كلمة المرور 6 أحرف على الأقل' };
        }
        
        if (!/(?=.*[a-zA-Z])/.test(value)) {
            return { isValid: false, message: 'يجب أن تحتوي كلمة المرور على حرف واحد على الأقل' };
        }
        
        return { isValid: true, message: '' };
    }

    // التحقق من صحة الحقل وإظهار الرسائل
    function validateField(input, validator) {
        const container = input.closest('.input-container');
        const errorElement = container.parentNode.querySelector('.error-message');
        const result = validator(input.value);
        
        // إزالة الفئات السابقة
        container.classList.remove('error', 'success');
        
        if (input.value.trim() === '') {
            // حقل فارغ
            errorElement.textContent = '';
            errorElement.classList.remove('show');
            return true;
        } else if (!result.isValid) {
            // خطأ في التحقق
            container.classList.add('error');
            errorElement.textContent = result.message;
            errorElement.classList.add('show');
            return false;
        } else {
            // نجح التحقق
            container.classList.add('success');
            errorElement.textContent = '';
            errorElement.classList.remove('show');
            return true;
        }
    }

    // معالجة إرسال النموذج
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // التحقق من جميع الحقول
        const isUsernameValid = validateField(usernameInput, validateUsername);
        const isPasswordValid = validateField(passwordInput, validatePassword);
        
        if (!isUsernameValid || !isPasswordValid) {
            // هز النموذج في حالة وجود خطأ
            shakeForm();
            return;
        }
        
        // إظهار مؤشر التحميل
        showLoading();
        
        // محاكاة طلب AJAX
        simulateLogin();
    });

    // إرسال طلب تسجيل الدخول إلى الخادم
    function simulateLogin() {
        const formData = new FormData(loginForm);
        
        // إرسال طلب AJAX إلى PHP
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                showSuccess();
                // إعادة توجيه بعد 1.5 ثانية
                setTimeout(() => {
                    window.location.href = data.data.redirect_url || 'dashboard.php';
                }, 1500);
            } else {
                showError(data.message || 'حدث خطأ في تسجيل الدخول');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('خطأ في الشبكة:', error);
            showError('حدث خطأ في الاتصال بالخادم');
        });
    }

    // إظهار مؤشر التحميل
    function showLoading() {
        loginBtn.classList.add('loading');
        loginBtn.disabled = true;
    }

    // إخفاء مؤشر التحميل
    function hideLoading() {
        loginBtn.classList.remove('loading');
        loginBtn.disabled = false;
    }

    // إظهار رسالة النجاح
    function showSuccess() {
        hideMessages();
        successMessage.style.display = 'flex';
        successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // إظهار رسالة الخطأ
    function showError(message) {
        hideMessages();
        errorText.textContent = message;
        errorAlert.style.display = 'flex';
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // إخفاء الرسالة بعد 5 ثوان
        setTimeout(hideMessages, 5000);
    }

    // إخفاء جميع الرسائل
    function hideMessages() {
        successMessage.style.display = 'none';
        errorAlert.style.display = 'none';
    }

    // هز النموذج
    function shakeForm() {
        const loginBox = document.querySelector('.login-box');
        loginBox.classList.add('shake');
        setTimeout(() => {
            loginBox.classList.remove('shake');
        }, 500);
    }

    // معالجة أزرار الشبكات الاجتماعية
    const socialButtons = document.querySelectorAll('.social-btn');
    socialButtons.forEach(button => {
        button.addEventListener('click', function() {
            const platform = this.classList.contains('google') ? 'Google' : 'Facebook';
            
            // إضافة تأثير حركي
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            // محاكاة تسجيل الدخول بالشبكات الاجتماعية
            showError(`تسجيل الدخول بـ ${platform} غير متاح حالياً`);
        });
    });

    // تأثيرات إضافية للتفاعل
    
    // تأثير التركيز على الحقول
    const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('.input-container').style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.closest('.input-container').style.transform = 'scale(1)';
        });
    });

    // تأثير الكتابة على الحقول
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            const container = this.closest('.input-container');
            if (this.value.length > 0) {
                container.classList.add('has-content');
            } else {
                container.classList.remove('has-content');
            }
        });
    });

    // تأثير حركي للأزرار
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // كشف الضغط على مفتاح Enter
    document.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && (usernameInput.matches(':focus') || passwordInput.matches(':focus'))) {
            loginForm.dispatchEvent(new Event('submit'));
        }
    });

    // تحسين تجربة المستخدم - حفظ اسم المستخدم
    const rememberCheckbox = document.getElementById('remember');
    
    // استرجاع اسم المستخدم المحفوظ
    const savedUsername = localStorage.getItem('rememberedUsername');
    if (savedUsername) {
        usernameInput.value = savedUsername;
        rememberCheckbox.checked = true;
        usernameInput.dispatchEvent(new Event('input'));
    }

    // حفظ اسم المستخدم عند النجاح
    loginForm.addEventListener('submit', function() {
        if (rememberCheckbox.checked) {
            localStorage.setItem('rememberedUsername', usernameInput.value);
        } else {
            localStorage.removeItem('rememberedUsername');
        }
    });

    // تأثير تحميل الصفحة
    window.addEventListener('load', function() {
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.5s ease';
        
        setTimeout(() => {
            document.body.style.opacity = '1';
        }, 100);
    });

    // تأثير النقر على رابط "نسيت كلمة المرور"
    const forgotPasswordLink = document.querySelector('.forgot-password');
    forgotPasswordLink.addEventListener('click', function(e) {
        e.preventDefault();
        showError('ميزة استرداد كلمة المرور ستتوفر قريباً');
    });

    // تأثير النقر على رابط "إنشاء حساب جديد"
    const signupLink = document.querySelector('.signup-link a');
    signupLink.addEventListener('click', function(e) {
        e.preventDefault();
        showError('صفحة التسجيل ستتوفر قريباً');
    });
});