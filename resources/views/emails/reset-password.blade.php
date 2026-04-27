<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keamanan Akun - Portal Monitoring</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none;">
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; padding: 50px 20px;">
        <tr>
            <td align="center">
                
                <!-- Main Card -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    
                    <!-- Logo Section -->
                    <tr>
                        <td align="center" style="padding: 40px 0 30px 0;">
                            <img src="{{ $message->embed(public_path('images/logo-balmon-lampung.jpg')) }}" alt="Balmon Lampung" style="height: 75px; max-width: 100%; display: block; border: 0;">
                        </td>
                    </tr>

                    <!-- Elegant Divider -->
                    <tr>
                        <td align="center" style="padding: 0 40px;">
                            <hr style="border: 0; border-top: 1px solid #f3f4f6; margin: 0;">
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 40px;">
                            <h1 style="color: #111827; font-size: 22px; margin: 0 0 20px 0; font-weight: bold; text-align: center;">Permintaan Reset Kata Sandi</h1>
                            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin: 0 0 32px 0; text-align: center;">
                                Halo <strong>{{ $name }}</strong>,<br><br>
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun Portal Monitoring Anda. Jika ini adalah Anda, silakan klik tombol di bawah ini untuk melanjutkan.
                            </p>
                            
                            <!-- Action Button -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}" style="background-color: #2563eb; color: #ffffff; padding: 14px 36px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 16px; display: inline-block;">Atur Ulang Kata Sandi</a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Alert Box -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 32px;">
                                <tr>
                                    <td style="background-color: #f9fafb; padding: 16px 20px; border-radius: 6px; border: 1px solid #f3f4f6; text-align: center;">
                                        <p style="color: #6b7280; font-size: 14px; line-height: 1.5; margin: 0;">
                                            Tautan ini akan kedaluwarsa dalam <strong>15 menit</strong>. Jika Anda tidak meminta pengaturan ulang kata sandi, abaikan email ini dengan aman.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>

                <!-- Footer Section -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td align="center" style="padding: 30px 20px; color: #9ca3af; font-size: 12px; line-height: 1.6;">
                            <p style="margin: 0 0 16px 0;">
                                Jika tombol di atas tidak berfungsi, salin dan tempel tautan ini ke browser Anda:<br>
                                <a href="{{ $url }}" style="color: #3b82f6; text-decoration: none; word-break: break-all;">{{ $url }}</a>
                            </p>
                            <p style="margin: 0;">
                                <strong style="color: #6b7280;">Balai Monitor Spektrum Frekuensi Radio Kelas II Lampung</strong><br>
                                Kementerian Komunikasi dan Digital (KOMDIGI)<br>
                                &copy; {{ date('Y') }} Hak Cipta Dilindungi.
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>
