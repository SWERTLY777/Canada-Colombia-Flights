<?php
// Mail configuration sourced from environment variables.
// No secrets are stored here; the file just maps env -> config array for MailService.

function env_val(string $key, $default = null) {
    $v = getenv($key);
    return ($v === false || $v === null || $v === '') ? $default : $v;
}

$transport   = env_val('MAIL_TRANSPORT', 'smtp');
$smtp_host   = env_val('SMTP_HOST');
$smtp_port   = (int) env_val('SMTP_PORT', 587);
$smtp_secure = env_val('SMTP_SECURE', 'tls'); // tls | ssl | null
$smtp_user   = env_val('SMTP_USER');
$smtp_pass   = env_val('SMTP_PASS');

$from_email  = env_val('MAIL_FROM', 'no-reply@localhost');
$from_name   = env_val('MAIL_FROM_NAME', 'App');
$reply_to    = env_val('MAIL_REPLY_TO');

$dkim_domain           = env_val('DKIM_DOMAIN');
$dkim_selector         = env_val('DKIM_SELECTOR');
$dkim_private_key_path = env_val('DKIM_PRIVATE_KEY_PATH');

return [
    'transport'   => $transport,

    // SMTP
    'smtp_host'   => $smtp_host,
    'smtp_port'   => $smtp_port,
    'smtp_secure' => $smtp_secure,
    'smtp_user'   => $smtp_user,
    'smtp_pass'   => $smtp_pass,

    // From / Reply-To
    'from_email'  => $from_email,
    'from_name'   => $from_name,
    'reply_to'    => $reply_to,

    // DKIM (optional)
    'dkim_domain'           => $dkim_domain,
    'dkim_selector'         => $dkim_selector,
    'dkim_private_key_path' => $dkim_private_key_path,
];
