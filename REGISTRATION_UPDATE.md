# 📝 Update: Form Registrasi dengan Alamat Lengkap

## 🎯 Perubahan yang Dilakukan

Form registrasi sekarang **menambahkan field alamat lengkap** yang diperlukan untuk checkout, sehingga customer tidak perlu mengisi alamat lagi saat order.

---

## ✨ Field Baru di Form Registrasi:

1. **Phone Number** - Nomor telepon customer
2. **Address** - Alamat lengkap (textarea)
3. **City** - Kota
4. **Province** - Provinsi
5. **Postal Code** - Kode pos (max 5 digit)

---

## 📄 File yang Dimodifikasi:

### 1. **resources/js/Pages/Auth/Register.vue**
- Menambahkan 5 field baru di form
- Layout responsive dengan grid untuk City & Province
- Validasi required untuk semua field alamat
- Placeholder Indonesia untuk user guidance

### 2. **app/Actions/Fortify/CreateNewUser.php**
- Menambahkan validasi untuk field alamat
- Otomatis set `role` = 'customer' untuk registrasi baru
- Menyimpan semua data alamat ke database

---

## 🎨 Tampilan Form:

```
┌────────────────────────────────────┐
│ Name                               │
├────────────────────────────────────┤
│ Email                              │
├────────────────────────────────────┤
│ Phone Number                       │
├────────────────────────────────────┤
│ Address (textarea)                 │
│ Jl. Contoh No. 123, Kelurahan      │
├──────────────────┬─────────────────┤
│ City             │ Province        │
│ Jakarta          │ DKI Jakarta     │
├──────────────────┴─────────────────┤
│ Postal Code                        │
│ 12345                              │
├────────────────────────────────────┤
│ Password                           │
├────────────────────────────────────┤
│ Confirm Password                   │
├────────────────────────────────────┤
│ ☑ Terms & Privacy Policy           │
└────────────────────────────────────┘
```

---

## ✅ Benefit:

1. **Checkout Lebih Cepat** - Alamat sudah tersimpan saat registrasi
2. **Data Lengkap** - Customer profile lengkap dari awal
3. **UX Lebih Baik** - Tidak perlu isi alamat 2 kali
4. **Database Konsisten** - Semua customer punya alamat lengkap

---

## 🧪 Test Registrasi:

1. Buka halaman registrasi
2. Isi semua field termasuk alamat
3. Submit form
4. Login dengan akun baru
5. Langsung bisa checkout tanpa perlu isi alamat lagi!

---

## 📊 Validasi:

- **name**: Required, max 255 char
- **email**: Required, valid email, unique
- **password**: Required, min 8 char (sesuai Laravel Fortify rules)
- **phone**: Required, max 20 char
- **address**: Required, max 500 char
- **city**: Required, max 100 char
- **province**: Required, max 100 char
- **postal_code**: Required, max 10 char (5 digit untuk Indonesia)

Semua field di atas **wajib diisi** saat registrasi! 🎯
