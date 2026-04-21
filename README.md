# 🛒 Purchase Request API - Backend

<div align="center">

![Build Status](https://img.shields.io/badge/build-passing-brightgreen)
![Laravel](https://img.shields.io/badge/Laravel-12.0+-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![License](https://img.shields.io/badge/license-MIT-purple)
![API](https://img.shields.io/badge/API-RESTful-green)
![Frontend](https://img.shields.io/badge/Frontend-Flutter-blue)

> API RESTful متقدمة لإدارة طلبات الشراء - جزء Backend من المشروع الكامل

[التثبيت](#-التثبيت) • [API Endpoints](#-api-endpoints) • [الأمان](#-الأمان) • [Frontend](#-frontend)

</div>

---

## 📱 المشروع الكامل (Full Stack)

| الجزء | التقنية | الرابط |
|------|--------|--------|
| **Frontend** | Flutter 3.8+ | [purchase_request_manager](https://github.com/BadrAbdu11ah/purchase_request_manager) |
| **Backend** | Laravel 12 | [purchase-request-api](https://github.com/BadrAbdu11ah/purchase-request-api) ← أنت هنا |

---

## 🎯 المميزات الرئيسية

- ✅ **المصادقة الآمنة** - Token-based Authentication
- ✅ **التحكم بالصلاحيات** - Admin, Purchasing Officer, Store Keeper
- ✅ **إدارة الطلبات والمنتجات والفئات**
- ✅ **Dashboard وإحصائيات**
- ✅ **معمارية نظيفة** - Clean Architecture + Repository Pattern
- ✅ **معالجة أخطاء شاملة**
- ✅ **اختبارات شاملة** - Unit & Feature Tests

---

## 📋 المتطلبات

| المتطلب | الإصدار |
|--------|--------|
| **PHP** | 8.2+ |
| **Composer** | 2.0+ |
| **MySQL** | 8.0+ |
| **Node.js** | 16.0+ (اختياري) |

---

## 📥 التثبيت

### 1️⃣ استنساخ المشروع

```bash
git clone https://github.com/BadrAbdu11ah/purchase-request-api.git
cd purchase-request-api
```

### 2️⃣ تثبيت الاعتماديات

```bash
composer install
npm install  # اختياري
```

### 3️⃣ إعداد البيئة

```bash
cp .env.example .env
php artisan key:generate
```

### 4️⃣ إعداد قاعدة البيانات

```bash
# تحديث بيانات الاتصال في .env أولاً
php artisan migrate --seed
```

### 5️⃣ تشغيل الخادم

```bash
php artisan serve
```

**الخادم سيكون متاح على:** `http://localhost:8000`

---

## 🔐 متغيرات البيئة (.env)

```env
APP_NAME="Purchase Request API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=purchase_request_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## 📡 API Endpoints

### 🔐 المصادقة

```http
POST   /api/auth/register         # تسجيل
POST   /api/auth/login            # دخول
GET    /api/auth/me               # بيانات المستخدم
POST   /api/auth/logout           # خروج
```

### 📦 الطلبات

```http
GET    /api/orders                # جميع الطلبات
POST   /api/orders/store          # إنشاء طلب
GET    /api/orders/{id}           # تفاصيل الطلب
POST   /api/orders/{id}/update-status  # تحديث الحالة
```

### 🏷️ الفئات والمنتجات

```http
GET    /api/categories            # الفئات
GET    /api/products              # المنتجات
POST   /api/products              # إنشاء منتج
PUT    /api/products/{id}         # تحديث المنتج
DELETE /api/products/{id}         # حذف المنتج
```

### 📈 Dashboard

```http
GET    /api/dashboard/statistics  # الإحصائيات
GET    /api/dashboard/orders-report
```

---

## 🧪 الاختبارات

```bash
# تشغيل الاختبارات
php artisan test

# مع التغطية
php artisan test --coverage

# اختبار محدد
php artisan test tests/Feature/OrderTest.php
```

---

## 🔐 الأمان

- ✅ تشفير كلمات المرور (bcrypt)
- ✅ CORS/CSRF Protection
- ✅ Input Validation
- ✅ Rate Limiting
- ✅ SQL Injection Prevention
- ✅ Secure Token Authentication

---

## 🛠 أوامر مفيدة

```bash
# توليد Model مع Migration و Controller
php artisan make:model ModelName -mcr

# تشغيل الهجرات
php artisan migrate

# إعادة تعيين قاعدة البيانات
php artisan migrate:refresh --seed

# عرض جميع المسارات
php artisan route:list

# مسح الـ Cache
php artisan optimize:clear
```

---

## 📊 قاعدة البيانات

الجداول الرئيسية:
- **users** - جدول المستخدمين
- **roles** - جدول الأدوار
- **orders** - جدول الطلبات
- **order_items** - تفاصيل الطلبات
- **products** - جدول المنتجات
- **categories** - جدول الفئات

---

## 📱 Frontend

للمزيد من التفاصيل حول التطبيق الكامل وواجهة المستخدم:

👉 **[اضغط هنا لعرض Frontend README](https://github.com/BadrAbdu11ah/purchase_request_manager)**

---

## 📧 التواصل

- 📬 البريد: Badrhaje2@gmail.com
- 💼 LinkedIn: [Badr Haje](https://www.linkedin.com/in/badr-haje-21073a39b)
- 🐙 GitHub: [@BadrAbdu11ah](https://github.com/BadrAbdu11ah)

---

## 📝 الترخيص

هذا المشروع مرخص تحت رخصة MIT - انظر [LICENSE](LICENSE) للمزيد.

---

<div align="center">

**⭐ إذا أعجبك المشروع، لا تنسَ إضافة نجمة!**

[Back to top ⬆️](#-purchase-request-api---backend)

</div>
