# 📧 Setup Email untuk Medic E-Commerce

## Status Email Saat Ini

✅ **Fitur yang Sudah Siap:**
1. **Invoice Email** - Otomatis dikirim setelah order berhasil (sudah diimplementasi di `OrderController::generateAndSendInvoice()`)
2. **Password Reset Email** - Email reset password dengan template kustom dalam Bahasa Indonesia
3. **Email Templates** - Template profesional dengan desain yang menarik

⚠️ **Yang Diperlukan:**
- Konfigurasi kredensial Gmail untuk mengirim email

---

## 🔧 Cara Setup Gmail SMTP

### Langkah 1: Persiapkan Akun Gmail

1. Login ke akun Gmail yang akan digunakan untuk mengirim email
2. **Penting:** Akun Gmail harus mengaktifkan 2-Step Verification terlebih dahulu

### Langkah 2: Buat App Password

1. Buka **Google Account Settings**: https://myaccount.google.com/
2. Pilih **Security** dari menu kiri
3. Cari bagian **"Signing in to Google"**
4. Klik **"2-Step Verification"**
   - Jika belum aktif, aktifkan terlebih dahulu
5. Scroll ke bawah dan cari **"App passwords"**
6. Klik **"App passwords"**
7. Pilih:
   - **Select app:** Mail
   - **Select device:** Other (Custom name)
   - Ketik: "Medic E-Commerce"
8. Klik **Generate**
9. **CATAT APP PASSWORD** (16 karakter tanpa spasi)
   - Contoh: `abcd efgh ijkl mnop`

### Langkah 3: Update File .env

Buka file `.env` di root project dan update bagian email:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com          # ← Ganti dengan email Gmail Anda
MAIL_PASSWORD=abcd efgh ijkl mnop           # ← Ganti dengan App Password dari langkah 2
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@medic-ecommerce.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Contoh lengkap:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=jasmine.medic@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@medic-ecommerce.com"
MAIL_FROM_NAME="Medic E-Commerce"
```

### Langkah 4: Test Email

Setelah konfigurasi, test email dengan perintah berikut:

```bash
php artisan tinker
```

Kemudian di dalam tinker, jalankan:

```php
Mail::raw('Test email dari Medic E-Commerce', function($message) {
    $message->to('email-anda@gmail.com')
            ->subject('Test Email');
});
```

Tekan `Ctrl+D` untuk keluar dari tinker.

Cek inbox email Anda. Jika email masuk, konfigurasi berhasil! ✅

---

## 📨 Cara Kerja Email di Sistem

### 1. Invoice Email
**Kapan dikirim:** Otomatis setelah customer melakukan checkout/order

**Isi email:**
- Ucapan terima kasih
- Detail order (order number, tanggal, total, metode pembayaran)
- PDF invoice sebagai attachment
- Instruksi untuk customer

**Template:** `resources/views/emails/invoice-notification.blade.php`

**Kode:** `app/Http/Controllers/Api/OrderController.php` line 146 & 242

### 2. Password Reset Email
**Kapan dikirim:** Ketika user klik "Forgot Password" di halaman login

**Isi email:**
- Link reset password (valid 60 menit)
- Peringatan jika tidak request reset password
- Instruksi alternatif jika tombol tidak berfungsi

**Template:** `resources/views/emails/password-reset.blade.php`

**Kode:** `app/Notifications/ResetPasswordNotification.php`

---

## 🔍 Troubleshooting

### Email tidak terkirim?

1. **Cek log error:**
   ```bash
   cat storage/logs/laravel.log
   ```

2. **Verifikasi kredensial:**
   - Pastikan `MAIL_USERNAME` adalah email Gmail yang benar
   - Pastikan `MAIL_PASSWORD` adalah **App Password**, bukan password Gmail biasa
   - App Password tidak boleh ada spasi (Laravel akan handle otomatis)

3. **Cek koneksi internet dan firewall:**
   - Port 587 harus terbuka
   - Coba ping `smtp.gmail.com`

4. **Gmail memblokir?**
   - Cek email notifikasi dari Google tentang "suspicious activity"
   - Pastikan 2-Step Verification sudah aktif
   - Pastikan menggunakan App Password, bukan password akun

### Email masuk ke spam?

1. **Setup SPF Record** (jika punya domain sendiri):
   ```
   v=spf1 include:_spf.google.com ~all
   ```

2. **Gunakan email `MAIL_FROM_ADDRESS` yang valid:**
   - Sebaiknya gunakan domain yang sama dengan `MAIL_USERNAME`
   - Atau gunakan no-reply@gmail.com

3. **Jangan kirim terlalu banyak email sekaligus:**
   - Gmail membatasi 500 email/hari untuk akun gratis
   - Gunakan delay jika kirim email massal

---

## 🎯 Checklist Final

Sebelum deploy ke production, pastikan:

- [ ] Gmail App Password sudah dibuat
- [ ] File `.env` sudah diupdate dengan kredensial yang benar
- [ ] Test email berhasil dikirim
- [ ] Invoice email terkirim setelah order placement
- [ ] Password reset email terkirim dan link berfungsi
- [ ] Email tidak masuk spam
- [ ] Template email terlihat bagus di mobile dan desktop

---

## 📞 Support

Jika mengalami masalah:
1. Cek log Laravel: `storage/logs/laravel.log`
2. Cek log queue: `php artisan queue:failed`
3. Test manual dengan `php artisan tinker`

**Catatan:** Karena `QUEUE_CONNECTION=sync`, email akan dikirim langsung tanpa antrian. Jika ingin menggunakan queue, ubah ke `database` dan jalankan `php artisan queue:work`.

---

## ✨ Fitur Email yang Tersedia

| Fitur | Status | File Template |
|-------|--------|---------------|
| Invoice Email | ✅ Siap | `resources/views/emails/invoice-notification.blade.php` |
| Password Reset | ✅ Siap | `resources/views/emails/password-reset.blade.php` |
| Team Invitation | ✅ Siap | `resources/views/emails/team-invitation.blade.php` (Jetstream) |

Semua email menggunakan desain profesional dengan Bahasa Indonesia! 🇮🇩
