<?php if ($msg==='saved'): ?><div class="alert alert-success">Settings saved successfully.</div><?php endif; ?>
    <?php if ($msg==='email_sent'): ?><div class="alert alert-success">Test email sent successfully!</div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger">Error: <?= e($err) ?></div><?php endif; ?>

    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="save_settings">
      <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">

      <!-- Marquee -->
      <div class="card" style="margin-bottom:1.1rem">
        <div class="card-header"><h3><i class="bx bx-broadcast"></i> Scrolling Announcement (Marquee)</h3></div>
        <div class="card-body">
          <div class="form-field"><label>Marquee Text</label><textarea class="input" name="marquee_text" placeholder="Enter announcement text..."><?= e($S['marquee_text']??'') ?></textarea></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem">
            <div class="form-field"><label>Enable Marquee</label>
              <div style="display:flex;align-items:center;gap:10px;padding:8px 0"><input type="checkbox" name="marquee_enabled" value="1" <?= ($S['marquee_enabled']??'1')==='1'?'checked':'' ?> style="width:16px;height:16px;cursor:pointer;accent-color:var(--primary)"><label style="font-size:.83rem;color:var(--t2);text-transform:none;letter-spacing:0;cursor:pointer">Show marquee on all pages</label></div>
            </div>
            <div class="form-field"><label>Scroll Speed (seconds)</label><input class="input" type="number" name="marquee_speed" min="10" max="300" step="5" value="<?= e($S['marquee_speed']??'60') ?>" placeholder="60"><small style="color:var(--t4);font-size:.7rem">Lower = faster. Default: 60</small></div>
          </div>
          <div style="background:var(--primary);color:var(--btn-text);padding:5px 0;border-radius:var(--r);overflow:hidden;margin-top:.5rem"><span style="display:inline-block;padding:0 16px;font-size:.8rem"><?= e($S['marquee_text']??'Preview text') ?></span></div>
        </div>
      </div>

      <!-- Footer -->
      <div class="card" style="margin-bottom:1.1rem">
        <div class="card-header"><h3><i class="bx bx-text"></i> Footer Text</h3></div>
        <div class="card-body">
          <div class="form-field"><label>Footer Text (shown in sidebar below Sign Out)</label><textarea class="input" name="footer_text" placeholder="PMS By Mingosoft Technologies &amp;copy; 2025"><?= e($S['footer_text']??'') ?></textarea><small style="color:var(--t4);font-size:.7rem">HTML allowed (e.g. &amp;amp;copy; for copyright)</small></div>
        </div>
      </div>

      <!-- Email -->
      <div class="card" style="margin-bottom:1.1rem">
        <div class="card-header"><h3><i class="bx bx-envelope"></i> Email Configuration (SMTP)</h3></div>
        <div class="card-body">
          <div class="form-grid">
            <div class="form-field"><label>Mail Method</label><select class="input" name="mail_method"><option value="smtp" <?= ($S['mail_method']??'')==='smtp'?'selected':'' ?>>SMTP</option><option value="php" <?= ($S['mail_method']??'')==='php'?'selected':'' ?>>PHP mail()</option></select></div>
            <div class="form-field"><label>SMTP Host</label><input class="input" type="text" name="mail_host" placeholder="smtp.gmail.com" value="<?= e($S['mail_host']??'') ?>"></div>
            <div class="form-field"><label>SMTP Port</label><input class="input" type="number" name="mail_port" placeholder="587" value="<?= e($S['mail_port']??'587') ?>"><small style="color:var(--t4);font-size:.7rem">587 (TLS) or 465 (SSL)</small></div>
            <div class="form-field"><label>SMTP Username</label><input class="input" type="text" name="mail_user" placeholder="user@gmail.com" value="<?= e($S['mail_user']??'') ?>"></div>
            <div class="form-field"><label>SMTP Password</label><input class="input" type="password" name="mail_pass" placeholder="App password" value="<?= e($S['mail_pass']??'') ?>"></div>
            <div class="form-field"><label>From Email</label><input class="input" type="email" name="mail_from" placeholder="noreply@yoursite.com" value="<?= e($S['mail_from']??'') ?>"></div>
            <div class="form-field"><label>From Name</label><input class="input" type="text" name="mail_from_name" placeholder="PMS System" value="<?= e($S['mail_from_name']??'PMS System') ?>"></div>
            <div class="form-field"><label>Admin Email (for error notifications)</label><input class="input" type="email" name="mail_admin_email" placeholder="admin@yoursite.com" value="<?= e($S['mail_admin_email']??'') ?>"></div>
          </div>
          <div class="form-field"><label>Error Notification</label>
            <div style="display:flex;align-items:center;gap:10px;padding:8px 0"><input type="checkbox" name="mail_error_notify" value="1" <?= ($S['mail_error_notify']??'0')==='1'?'checked':'' ?> style="width:16px;height:16px;cursor:pointer;accent-color:var(--primary)"><label style="font-size:.83rem;color:var(--t2);text-transform:none;letter-spacing:0;cursor:pointer">Send email to admin when PHP errors occur</label></div>
          </div>
        </div>
      </div>

      <!-- Theme -->
      <div class="card" style="margin-bottom:1.1rem">
        <div class="card-header"><h3><i class="bx bx-palette"></i> Color Theme Customization</h3></div>
        <div class="card-body">
          <div class="alert alert-info" style="font-size:.77rem">Changes apply immediately after saving. Default theme: #81A6C6 / #F3E3D0 / #D2C4B4</div>
          <?php $themeFields=[
            ['theme_primary',   'Primary / Button Color (accent)'],
            ['theme_bg',        'Page Background Color'],
            ['theme_surface',   'Card / Surface Color (white areas)'],
            ['theme_border',    'Border / Divider Color'],
            ['theme_btn_text',  'Button Text Color'],
            ['theme_heading',   'Heading Text Color'],
            ['theme_text',      'Body Text Color'],
            ['theme_sidebar_bg','Sidebar Background Color'],
            ['theme_topbar_bg', 'Top Bar Background Color'],
          ];
          foreach ($themeFields as [$key,$label]):
            $val=$S[$key]??'#81A6C6';
          ?>
          <div class="color-row">
            <label><?= $label ?></label>
            <input type="color" value="<?= e($val) ?>" oninput="document.getElementById('<?= $key ?>_text').value=this.value" onchange="document.getElementById('<?= $key ?>_text').value=this.value">
            <input class="input" type="text" name="<?= $key ?>" id="<?= $key ?>_text" value="<?= e($val) ?>" placeholder="#81A6C6" maxlength="7" oninput="syncColor(this)" style="max-width:120px">
          </div>
          <?php endforeach; ?>
          <div style="margin-top:.8rem;display:flex;gap:9px;flex-wrap:wrap">
            <button type="button" onclick="resetTheme()" class="btn btn-ghost btn-sm"><i class="bx bx-reset"></i> Reset to Default</button>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:9px;flex-wrap:wrap">
        <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Save All Settings</button>
        <a href="index.php" class="btn btn-ghost btn-md"><i class="bx bx-x"></i> Cancel</a>
      </div>
    </form>

    <!-- Test email button (separate form) -->
    <div style="margin-top:1rem">
      <form method="POST" action="index.php" style="display:inline">
        <input type="hidden" name="action" value="test_email">
        <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
        <button type="submit" class="btn btn-secondary btn-sm"><i class="bx bx-envelope"></i> Send Test Email</button>
      </form>
      <span style="font-size:.74rem;color:var(--t3);margin-left:8px">Sends test email to the admin email address above</span>
    </div>