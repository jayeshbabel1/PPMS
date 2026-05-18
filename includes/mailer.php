<?php
// ============================================================
//  includes/mailer.php — Simple SMTP mailer (no Composer)
// ============================================================

function pms_mail(string $to, string $subject, string $htmlBody, string $replyTo = ''): bool {
    $method   = get_setting('mail_method',    'smtp');
    $host     = get_setting('mail_host',      '');
    $port     = (int)get_setting('mail_port', '587');
    $user     = get_setting('mail_user',      '');
    $pass     = get_setting('mail_pass',      '');
    $from     = get_setting('mail_from',      '');
    $fromName = get_setting('mail_from_name', APP_BRAND);

    if ($method === 'php' || empty($host)) {
        // Fallback: PHP mail()
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: $fromName <$from>\r\n";
        if ($replyTo) $headers .= "Reply-To: $replyTo\r\n";
        return mail($to, $subject, $htmlBody, $headers);
    }

    // SMTP via PHP streams
    $boundary = md5(uniqid((string)time()));
    $body = "--{$boundary}\r\n"
          . "Content-Type: text/html; charset=UTF-8\r\n"
          . "Content-Transfer-Encoding: base64\r\n\r\n"
          . chunk_split(base64_encode($htmlBody))."\r\n"
          . "--{$boundary}--\r\n";

    $headers  = "From: =?UTF-8?B?".base64_encode($fromName)."?= <{$from}>\r\n";
    $headers .= "To: <{$to}>\r\n";
    $headers .= "Subject: =?UTF-8?B?".base64_encode($subject)."?=\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
    if ($replyTo) $headers .= "Reply-To: <{$replyTo}>\r\n";
    $headers .= "Date: ".date('r')."\r\n";
    $headers .= "Message-ID: <".time()."@".(parse_url((string)($_SERVER['HTTP_HOST']??'pms.local'), PHP_URL_HOST)??'pms.local').">\r\n";

    // Connect
    $prefix = ($port === 465) ? 'ssl://' : '';
    $ctx = stream_context_create(['ssl' => ['verify_peer'=>false,'verify_peer_name'=>false]]);
    $conn = @stream_socket_client("{$prefix}{$host}:{$port}", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx);
    if (!$conn) throw new RuntimeException("SMTP connect failed: $errstr ($errno)");

    $smtp_read = function() use ($conn): string {
        $buf = '';
        while ($line = fgets($conn, 512)) {
            $buf .= $line;
            if ($line[3] === ' ') break; // end of multi-line
        }
        return $buf;
    };
    $smtp_cmd = function(string $cmd) use ($conn, $smtp_read): string {
        fwrite($conn, $cmd."\r\n");
        return $smtp_read();
    };

    $smtp_read(); // banner
    $smtp_cmd("EHLO ".(gethostname()?:'localhost'));

    if ($port === 587) {
        $smtp_cmd("STARTTLS");
        stream_socket_enable_crypto($conn, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $smtp_cmd("EHLO ".(gethostname()?:'localhost'));
    }

    if ($user && $pass) {
        $smtp_cmd("AUTH LOGIN");
        $smtp_cmd(base64_encode($user));
        $smtp_cmd(base64_encode($pass));
    }

    $smtp_cmd("MAIL FROM:<{$from}>");
    $smtp_cmd("RCPT TO:<{$to}>");
    $smtp_cmd("DATA");
    fwrite($conn, $headers."\r\n".$body."\r\n.\r\n");
    $smtp_read();
    $smtp_cmd("QUIT");
    fclose($conn);
    return true;
}

// ── Email template wrapper ─────────────────────────────────────
function email_template(string $title, string $content): string {
    $brand = APP_BRAND;
    $bg    = get_setting('theme_bg','#F3E3D0');
    $pri   = get_setting('theme_primary','#81A6C6');
    return <<<HTML
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>$title</title></head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.10)">
  <tr><td style="background:{$pri};padding:20px 30px">
    <h1 style="color:#ffffff;margin:0;font-size:1.3rem">{$brand}</h1>
  </td></tr>
  <tr><td style="padding:30px">
    <h2 style="color:#2C3A4A;margin-top:0">{$title}</h2>
    {$content}
    <p style="margin-top:30px;color:#999;font-size:.8rem;border-top:1px solid #eee;padding-top:15px">
      This is an automated message from {$brand}. Please do not reply directly to this email.
    </p>
  </td></tr>
</table>
</td></tr>
</table>
</body></html>
HTML;
}
