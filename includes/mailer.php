<?php
// ============================================================
//  includes/mailer.php  — Email via SMTP (no Composer needed)
// ============================================================
require_once __DIR__.'/auth.php';

/**
 * Send an email using PHP's built-in mail() or a simple SMTP socket connection.
 * Settings read from app_settings table.
 */
function pms_mail(string $to, string $subject, string $bodyHtml, string $bodyText=''): bool {
    $host   = get_setting('mail_host','');
    $port   = (int)get_setting('mail_port','587');
    $user   = get_setting('mail_user','');
    $pass   = get_setting('mail_pass','');
    $from   = get_setting('mail_from', $user);
    $fromNm = get_setting('mail_from_name', APP_BRAND);
    $method = get_setting('mail_method','smtp'); // smtp | mail

    if (!$to) return false;
    if ($bodyText==='') $bodyText=strip_tags($bodyHtml);
    $boundary='PMS_BOUND_'.md5(time());

    if ($method==='mail' || $host==='') {
        // Native PHP mail()
        $headers  = "From: $fromNm <$from>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
        $body  = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n$bodyText\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n$bodyHtml\r\n";
        $body .= "--$boundary--";
        return @mail($to, $subject, $body, $headers);
    }

    // SMTP socket (no extensions required)
    try {
        $enc = ($port===465) ? 'ssl' : 'tcp';
        $ctx = stream_context_create(['ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
        $sock = ($enc==='ssl')
            ? @stream_socket_client("ssl://$host:$port", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx)
            : @stream_socket_client("tcp://$host:$port", $errno, $errstr, 10);
        if (!$sock) return false;

        $r = function() use ($sock) { return fgets($sock,512); };
        $w = function(string $cmd) use ($sock) { fwrite($sock,"$cmd\r\n"); };

        $r(); // greeting
        $w("EHLO ".gethostname()); smtp_read_all($sock);

        if ($port===587) { // STARTTLS
            $w('STARTTLS'); $r();
            stream_socket_enable_crypto($sock,true,STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $w("EHLO ".gethostname()); smtp_read_all($sock);
        }

        if ($user) {
            $w('AUTH LOGIN'); $r();
            $w(base64_encode($user)); $r();
            $w(base64_encode($pass)); $resp=$r();
            if (strpos($resp,'235')===false) { fclose($sock); return false; }
        }

        $w("MAIL FROM:<$from>"); $r();
        foreach(array_map('trim',explode(',',$to)) as $t) { $w("RCPT TO:<$t>"); $r(); }
        $w('DATA'); $r();

        $msg  = "From: $fromNm <$from>\r\n";
        $msg .= "To: $to\r\n";
        $msg .= "Subject: =?UTF-8?B?".base64_encode($subject)."?=\r\n";
        $msg .= "MIME-Version: 1.0\r\n";
        $msg .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n\r\n";
        $msg .= "--$boundary\r\n";
        $msg .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n$bodyText\r\n";
        $msg .= "--$boundary\r\n";
        $msg .= "Content-Type: text/html; charset=UTF-8\r\n\r\n$bodyHtml\r\n";
        $msg .= "--$boundary--\r\n";
        $w($msg."\r\n."); $r();
        $w('QUIT'); fclose($sock);
        return true;
    } catch (\Throwable $e) {
        error_log('PMS mailer error: '.$e->getMessage());
        return false;
    }
}

function smtp_read_all($sock): string {
    $out='';
    while ($line=fgets($sock,512)){$out.=$line;if($line[3]===' ')break;}
    return $out;
}

/**
 * Send error notification to admin email.
 */
function mail_error(string $context, string $message, string $trace=''): void {
    $adminEmail=get_setting('mail_admin_email','');
    $enabled=get_setting('mail_error_notify','0');
    if(!$adminEmail||$enabled!=='1') return;
    $html='<h2 style="color:#c00">PMS Application Error</h2>'.
          '<p><b>Context:</b> '.htmlspecialchars($context).'</p>'.
          '<p><b>Message:</b> '.htmlspecialchars($message).'</p>'.
          ($trace?'<pre style="background:#f4f4f4;padding:10px;font-size:12px">'.htmlspecialchars($trace).'</pre>':'').
          '<hr><p style="color:#888;font-size:12px">'.APP_BRAND.' — '.date('d M Y H:i:s').'</p>';
    pms_mail($adminEmail,'[PMS Error] '.$context,$html);
}

/**
 * Send password reset email.
 */
function mail_password_reset(string $toEmail, string $toName, string $resetLink): bool {
    $html='<div style="font-family:Arial,sans-serif;max-width:500px;margin:0 auto">'.
          '<h2 style="color:#81A6C6">'.APP_BRAND.'</h2>'.
          '<h3>Password Reset Request</h3>'.
          '<p>Hello '.htmlspecialchars($toName).',</p>'.
          '<p>You requested a password reset. Click the button below to set a new password:</p>'.
          '<p style="text-align:center;margin:24px 0"><a href="'.htmlspecialchars($resetLink).'" style="background:#81A6C6;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold">Reset Password</a></p>'.
          '<p style="color:#888;font-size:12px">This link expires in 1 hour. If you did not request this, ignore this email.</p>'.
          '<hr><p style="color:#aaa;font-size:11px">'.APP_BRAND.'</p></div>';
    return pms_mail($toEmail,'Password Reset — '.APP_NAME,$html);
}

/**
 * Global PHP error handler — sends email on fatal errors.
 */
function pms_register_error_handler(): void {
    set_exception_handler(function(\Throwable $e) {
        mail_error(
            get_class($e).' in '.$e->getFile().':'.$e->getLine(),
            $e->getMessage(),
            $e->getTraceAsString()
        );
        // Re-display a user-friendly message
        http_response_code(500);
        echo '<div style="font-family:Arial;padding:2rem;text-align:center"><h2>Something went wrong</h2><p>The administrator has been notified. Please try again later.</p></div>';
        exit;
    });

    register_shutdown_function(function() {
        $err=error_get_last();
        if ($err && in_array($err['type'],[E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR])) {
            mail_error('Fatal PHP Error in '.$err['file'].':'.$err['line'], $err['message']);
        }
    });
}