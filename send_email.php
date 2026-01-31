<?php
/**
 * NG Pharma & Care - Contact Form Email Handler
 * 
 * KURULUM:
 * 1. Bu dosyayı hosting'inize yükleyin (HTML dosyalarıyla aynı klasöre)
 * 2. Aşağıdaki $to değişkenini kendi email adresinizle değiştirin
 * 3. script.js'de fetch URL'ini 'send_email.php' olarak ayarlayın
 * 4. Test edin!
 */

// CORS ayarları (sadece kendi domain'inizden gelen istekleri kabul etmek için)
// Production'da aşağıdaki satırı kendi domain'inizle değiştirin
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'Method not allowed'
    ]);
    exit;
}

// ==============================================
// BURAYA KENDİ EMAIL ADRESİNİZİ YAZIN
// ==============================================
$to = "ngpharmacareinfo@gmail.com";

// Form verilerini al ve temizle
$firstName = htmlspecialchars(trim($_POST['firstName'] ?? ''), ENT_QUOTES, 'UTF-8');
$lastName = htmlspecialchars(trim($_POST['lastName'] ?? ''), ENT_QUOTES, 'UTF-8');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
$company = htmlspecialchars(trim($_POST['company'] ?? ''), ENT_QUOTES, 'UTF-8');
$subject = htmlspecialchars(trim($_POST['subject'] ?? ''), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

// Konu çevirisi (Türkçe dropdown değerlerini İngilizce'ye çevir)
$subjectTranslations = [
    'Genel Sorgu' => 'General Inquiry',
    'Araştırma & Geliştirme' => 'Research & Development',
    'Sözleşmeli Üretim' => 'Contract Manufacturing',
    'Düzenleyici Danışmanlık' => 'Regulatory Consulting',
    'Ürün Bilgisi' => 'Product Information',
    'Ortaklık Fırsatları' => 'Partnership Opportunities',
    'Kariyer' => 'Careers',
    'Diğer' => 'Other'
];

$subjectEnglish = $subjectTranslations[$subject] ?? $subject;

// Zorunlu alanları kontrol et
if (empty($firstName) || empty($lastName) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Required fields are missing'
    ]);
    exit;
}

// Email formatını doğrula
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid email format'
    ]);
    exit;
}

// Email konusu
$emailSubject = "New Contact Form: " . $subjectEnglish;

// Email içeriği (HTML)
$emailBody = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #1a5f5f 0%, #0f3d3d 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
            background: #f8f9fa;
        }
        .field {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-left: 4px solid #1a5f5f;
            border-radius: 4px;
        }
        .label {
            font-weight: bold;
            color: #1a5f5f;
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            text-transform: uppercase;
        }
        .value {
            color: #333;
            font-size: 15px;
        }
        .message-box {
            background: white;
            padding: 20px;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
            margin-top: 10px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b6b6b;
            background: #1a1a1a;
            color: #ffffff;
        }
        .timestamp {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #6b6b6b;
            background: #e9ecef;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>📧 New Contact Form Submission</h1>
        </div>
        
        <div class='timestamp'>
            Received: " . date('F j, Y - g:i A') . "
        </div>
        
        <div class='content'>
            <div class='field'>
                <span class='label'>👤 Name</span>
                <span class='value'>$firstName $lastName</span>
            </div>
            
            <div class='field'>
                <span class='label'>📧 Email</span>
                <span class='value'><a href='mailto:$email' style='color: #1a5f5f;'>$email</a></span>
            </div>
            
            " . (!empty($phone) ? "
            <div class='field'>
                <span class='label'>📞 Phone</span>
                <span class='value'>$phone</span>
            </div>
            " : "") . "
            
            " . (!empty($company) ? "
            <div class='field'>
                <span class='label'>🏢 Company</span>
                <span class='value'>$company</span>
            </div>
            " : "") . "
            
            <div class='field'>
                <span class='label'>📋 Subject</span>
                <span class='value'>$subjectEnglish</span>
            </div>
            
            <div class='field'>
                <span class='label'>💬 Message</span>
                <div class='message-box'>$message</div>
            </div>
        </div>
        
        <div class='footer'>
            <p><strong>NG Pharma & Care</strong><br>
            This email was sent from the contact form on your website.</p>
        </div>
    </div>
</body>
</html>
";

// Plain text alternatifi (bazı email istemcileri için)
$emailBodyPlain = "
New Contact Form Submission
============================

Name: $firstName $lastName
Email: $email
" . (!empty($phone) ? "Phone: $phone\n" : "") . "
" . (!empty($company) ? "Company: $company\n" : "") . "
Subject: $subjectEnglish

Message:
--------
$message

---
Received: " . date('F j, Y - g:i A') . "
NG Pharma & Care
";

// Email headers
$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/html; charset=UTF-8";
$headers[] = "From: NG Pharma Website <noreply@ngpharma.com>"; // Kendi domain email'inizi kullanın
$headers[] = "Reply-To: $firstName $lastName <$email>";
$headers[] = "X-Mailer: PHP/" . phpversion();
$headers[] = "X-Priority: 1"; // High priority
$headers[] = "Importance: High";

// Email gönder
$mailSent = mail($to, $emailSubject, $emailBody, implode("\r\n", $headers));

if ($mailSent) {
    // Başarılı
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Email sent successfully'
    ]);
    
    // İsteğe bağlı: Form gönderimlerini log'a kaydet
    $logEntry = date('[Y-m-d H:i:s]') . " - New form submission from: $email ($firstName $lastName)\n";
    file_put_contents('contact_log.txt', $logEntry, FILE_APPEND);
    
} else {
    // Hata
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send email. Please try again later.'
    ]);
    
    // Hata log'u
    error_log("Contact form email failed for: $email");
}

// Güvenlik: Rate limiting (opsiyonel)
// Aynı IP'den çok fazla istek gelirse engelle
/*
$ip = $_SERVER['REMOTE_ADDR'];
$rateLimit = 5; // 5 dakikada maksimum form sayısı
$rateLimitFile = 'rate_limit_' . md5($ip) . '.txt';

if (file_exists($rateLimitFile)) {
    $lastSubmission = file_get_contents($rateLimitFile);
    $submissions = explode(',', $lastSubmission);
    $submissions = array_filter($submissions, function($time) {
        return $time > (time() - 300); // Son 5 dakika
    });
    
    if (count($submissions) >= $rateLimit) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'Too many requests. Please try again later.'
        ]);
        exit;
    }
    
    $submissions[] = time();
    file_put_contents($rateLimitFile, implode(',', $submissions));
} else {
    file_put_contents($rateLimitFile, time());
}
*/

?>
