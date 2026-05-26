    <?php $pwErrMap=['wrongpw'=>'Current password incorrect.','short'=>'New password must be 6+ characters.','mismatch'=>'Passwords do not match.'];
    if ($msg==='pwchanged'): ?><div class="alert alert-success">Password changed successfully.</div>
    <?php elseif (isset($pwErrMap[$err])): ?><div class="alert alert-danger"><?= $pwErrMap[$err] ?></div><?php endif; ?>
    <?php if ($msg==='upgrade_requested'): ?><div class="alert alert-success">Upgrade request submitted. Admin will review shortly.</div>
    <?php elseif ($err==='already_requested'): ?><div class="alert alert-warning">You already have a pending upgrade request.</div><?php endif; ?>

    <?php
    $bankName    = get_setting('bank_name','');
    $bankAccNo   = get_setting('bank_account_number','');
    $bankBranch  = get_setting('bank_branch','');
    $bankIfsc    = get_setting('bank_ifsc','');
    $bankAccType = get_setting('bank_account_type','');
    $bankUpi     = get_setting('bank_upi_id','');
    $bankNote    = get_setting('bank_note','');
    $bankQr      = get_setting('bank_qr_path','');
    $hasBankInfo = $bankName || $bankAccNo || $bankIfsc || $bankQr || $bankUpi;
    ?>

    <!-- QR Zoom Lightbox -->
    <?php if ($bankQr): ?>
    <div id="qrLightbox"
         onclick="closeQrZoom()"
         style="display:none;position:fixed;inset:0;z-index:9999;
                background:rgba(0,0,0,.82);backdrop-filter:blur(6px);
                align-items:center;justify-content:center;cursor:zoom-out">
      <div onclick="event.stopPropagation()"
           style="position:relative;max-width:90vw;max-height:90vh;
                  animation:qrZoomIn .22s cubic-bezier(.34,1.56,.64,1)">
        <!-- Close button -->
        <button onclick="closeQrZoom()"
                style="position:absolute;top:-14px;right:-14px;width:32px;height:32px;
                       border-radius:50%;background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.3);
                       color:#fff;font-size:1rem;cursor:pointer;display:flex;align-items:center;
                       justify-content:center;backdrop-filter:blur(4px);z-index:1;
                       transition:background .15s"
                onmouseover="this.style.background='rgba(255,255,255,.28)'"
                onmouseout="this.style.background='rgba(255,255,255,.15)'">
          <i class="bx bx-x"></i>
        </button>
        <!-- Zoomed image -->
        <img src="<?= e($bankQr) ?>" alt="QR Code"
             style="max-width:min(420px,88vw);max-height:80vh;
                    object-fit:contain;border-radius:16px;
                    background:#fff;padding:18px;
                    box-shadow:0 32px 80px rgba(0,0,0,.6)">
        <!-- Label -->
        <div style="text-align:center;margin-top:10px;font-size:.74rem;
                    color:rgba(255,255,255,.55);letter-spacing:.06em;text-transform:uppercase">
          Scan to Pay &nbsp;·&nbsp; Click anywhere to close
        </div>
      </div>
    </div>
    <?php endif; ?>

    <style>
    @keyframes qrZoomIn {
      from { opacity:0; transform:scale(.7); }
      to   { opacity:1; transform:scale(1); }
    }
    .qr-thumb {
      cursor:zoom-in;
      transition:transform .18s,box-shadow .18s,opacity .18s;
    }
    .qr-thumb:hover {
      transform:scale(1.06);
      box-shadow:0 8px 28px rgba(0,0,0,.22);
      opacity:.92;
    }
    .qr-zoom-hint {
      font-size:.62rem;color:var(--t4);margin-top:5px;
      font-weight:600;text-transform:uppercase;letter-spacing:.05em;
      display:flex;align-items:center;justify-content:center;gap:3px;
    }
    </style>

    <div class="two-col">
      <!-- ── Left column ── -->
      <div style="display:flex;flex-direction:column;gap:1.1rem">

        <div class="card"><div class="card-header"><h3><i class="bx bx-user"></i> Account Info</h3></div><div class="card-body">
          <div style="display:flex;align-items:center;gap:11px;margin-bottom:1.1rem">
            <div style="width:44px;height:44px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--btn-text);font-weight:700;flex-shrink:0"><?= strtoupper(substr($user['username'],0,1)) ?></div>
            <div><div style="font-size:.95rem;font-weight:700;color:var(--t1)"><?= e($user['full_name']?:$user['username']) ?></div><div style="font-size:.73rem;color:var(--t3)"><?= e($user['username']) ?> - <?= ucfirst($user['role']) ?><?= $userEmail?' - '.e($userEmail):'' ?></div></div>
          </div>
          <table style="width:100%;border-collapse:collapse">
            <?php foreach([['Username',$user['username']],['Full Name',$user['full_name']?:'--'],['Email',$userEmail?:'--'],['Role',ucfirst($user['role'])]] as[$l,$v]): ?>
            <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.67rem;font-weight:700;color:var(--t3);text-transform:uppercase"><?= $l ?></td><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.81rem;color:var(--t1);text-align:right"><?= e($v) ?></td></tr>
            <?php endforeach; ?>
          </table>
        </div></div>

        <div class="card"><div class="card-header"><h3><i class="bx bx-lock-alt"></i> Change Password</h3></div><div class="card-body">
          <form method="POST" action="index.php"><input type="hidden" name="action" value="change_password"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
            <div class="form-field"><label>Current Password</label><input class="input" type="password" name="current_password" required placeholder="Current password"></div>
            <div class="form-field"><label>New Password</label><input class="input" type="password" name="new_password" required placeholder="Min 6 characters"></div>
            <div class="form-field"><label>Confirm Password</label><input class="input" type="password" name="confirm_password" required placeholder="Repeat new password"></div>
            <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-lock-alt"></i> Update Password</button>
          </form>
        </div></div>

      </div>

      <!-- ── Right column ── -->
      <div style="display:flex;flex-direction:column;gap:1.1rem">

        <div class="card"><div class="card-header"><h3><i class="bx bx-credit-card"></i> My Subscription</h3></div><div class="card-body">
          <?php if ($mySub): ?>
          <div style="text-align:center;padding:.9rem 0;margin-bottom:1rem">
            <div style="font-size:2.2rem;margin-bottom:.4rem"><?= $mySub['plan_type']==='advance'?'⭐':'✅' ?></div>
            <div style="font-size:1.1rem;font-weight:700;color:var(--t1)"><?= ucfirst($mySub['plan_type']) ?> Plan</div>
            <div style="font-size:.77rem;color:var(--t3);margin-top:2px"><?= ucfirst($mySub['billing_cycle']) ?> &middot; Expires <strong><?= date('d M Y',strtotime($mySub['end_date'])) ?></strong></div>
          </div>
          <?php else: ?>
          <div style="text-align:center;padding:1.2rem;color:var(--t3)">
            <div style="font-size:2rem;margin-bottom:.4rem">🔒</div>
            <div style="font-size:.84rem;font-weight:600;color:var(--t2)">No active subscription</div>
            <div style="font-size:.74rem;color:var(--t4);margin-top:3px">Subscribe to access plan details, DLC rates &amp; more</div>
          </div>
          <?php endif; ?>
          <?php if (!is_admin()&&!is_developer()&&(!$mySub||$mySub['plan_type']==='basic')): ?>
          <div class="divider"></div>
          <h4 style="font-size:.84rem;margin-bottom:.8rem;color:var(--t1)">Request Upgrade</h4>
          <form method="POST" action="index.php"><input type="hidden" name="action" value="request_upgrade"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div class="form-field"><label>Plan</label><select class="input" name="request_plan"><?php if (!$mySub): ?><option value="basic">Basic</option><?php endif; ?><option value="advance">Advance</option></select></div>
              <div class="form-field"><label>Billing</label><select class="input" name="billing_cycle"><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></div>
            </div>
            <div class="form-field"><label>Message (optional)</label><textarea class="input" name="message" style="min-height:50px" placeholder="Notes for admin..."></textarea></div>
            <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-send"></i> Submit Request</button>
          </form>
          <?php endif; ?>
        </div></div>

        <!-- ── Payment / Bank Details ── -->
        <?php if ($hasBankInfo): ?>
        <div class="card" style="border:1px solid rgba(200,149,108,.35);background:linear-gradient(135deg,var(--surface) 0%,var(--gold-bg) 100%)">
          <div class="card-header" style="border-bottom:1px solid rgba(200,149,108,.25)">
            <h3 style="color:var(--gold-s)"><i class="bx bx-credit-card-alt"></i> Payment Details</h3>
            <span style="font-size:.68rem;color:var(--t4);font-weight:400">Transfer subscription fee to activate your plan</span>
          </div>
          <div class="card-body" style="padding:0">

            <?php if ($bankQr): ?>
            <div style="display:flex;gap:0;flex-wrap:wrap">
              <!-- QR Code — click to zoom -->
              <div style="padding:16px 14px;border-right:1px solid rgba(200,149,108,.2);text-align:center;min-width:138px;flex-shrink:0;background:rgba(255,255,255,.5)">
                <img src="<?= e($bankQr) ?>"
                     alt="Payment QR Code"
                     class="qr-thumb"
                     onclick="openQrZoom()"
                     style="width:110px;height:110px;object-fit:contain;
                            border-radius:8px;border:1px solid var(--border);
                            background:#fff;padding:4px">
                <div class="qr-zoom-hint">
                  <i class="bx bx-zoom-in" style="font-size:.75rem"></i> Tap to zoom
                </div>
              </div>
              <!-- Bank detail rows -->
              <div style="flex:1;min-width:180px;padding:12px 14px">
                <table style="width:100%;border-collapse:collapse">
                  <?php
                  $brows=[];
                  if ($bankName)    $brows[]=['bx bx-buildings', 'Bank',        $bankName];
                  if ($bankAccNo)   $brows[]=['bx bx-hash',      'Account No.', $bankAccNo];
                  if ($bankAccType) $brows[]=['bx bx-category',  'Acc. Type',   $bankAccType];
                  if ($bankBranch)  $brows[]=['bx bx-map-pin',   'Branch',      $bankBranch];
                  if ($bankIfsc)    $brows[]=['bx bx-barcode',   'IFSC Code',   $bankIfsc];
                  if ($bankUpi)     $brows[]=['bx bx-mobile-alt','UPI / GPay',  $bankUpi];
                  foreach ($brows as [$ic,$lbl,$val]):
                  ?>
                  <tr>
                    <td style="padding:5px 4px 5px 0;border-bottom:1px solid rgba(200,149,108,.15);font-size:.65rem;font-weight:700;color:var(--t4);text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;width:36%">
                      <i class="<?= $ic ?>" style="margin-right:3px"></i><?= $lbl ?>
                    </td>
                    <td style="padding:5px 0 5px 6px;border-bottom:1px solid rgba(200,149,108,.15);font-size:.8rem;font-weight:600;color:var(--t1);font-family:'JetBrains Mono',monospace;text-align:right;word-break:break-all">
                      <?= e($val) ?>
                      <?php if (in_array($lbl,['Account No.','IFSC Code','UPI / GPay'])): ?>
                      <button type="button" onclick="copyText('<?= htmlspecialchars(addslashes($val),ENT_QUOTES) ?>',this)" title="Copy <?= $lbl ?>"
                              style="margin-left:5px;background:none;border:1px solid var(--border);border-radius:4px;cursor:pointer;padding:1px 5px;font-size:.65rem;color:var(--t3);vertical-align:middle">
                        <i class="bx bx-copy"></i>
                      </button>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </table>
              </div>
            </div>

            <?php else: ?>
            <div style="padding:12px 14px">
              <table style="width:100%;border-collapse:collapse">
                <?php
                $brows=[];
                if ($bankName)    $brows[]=['bx bx-buildings', 'Bank',        $bankName];
                if ($bankAccNo)   $brows[]=['bx bx-hash',      'Account No.', $bankAccNo];
                if ($bankAccType) $brows[]=['bx bx-category',  'Acc. Type',   $bankAccType];
                if ($bankBranch)  $brows[]=['bx bx-map-pin',   'Branch',      $bankBranch];
                if ($bankIfsc)    $brows[]=['bx bx-barcode',   'IFSC Code',   $bankIfsc];
                if ($bankUpi)     $brows[]=['bx bx-mobile-alt','UPI / GPay',  $bankUpi];
                foreach ($brows as [$ic,$lbl,$val]):
                ?>
                <tr>
                  <td style="padding:7px 0;border-bottom:1px solid rgba(200,149,108,.15);font-size:.65rem;font-weight:700;color:var(--t4);text-transform:uppercase;letter-spacing:.06em;width:36%">
                    <i class="<?= $ic ?>" style="margin-right:3px"></i><?= $lbl ?>
                  </td>
                  <td style="padding:7px 0 7px 6px;border-bottom:1px solid rgba(200,149,108,.15);font-size:.82rem;font-weight:600;color:var(--t1);font-family:'JetBrains Mono',monospace;text-align:right;word-break:break-all">
                    <?= e($val) ?>
                    <?php if (in_array($lbl,['Account No.','IFSC Code','UPI / GPay'])): ?>
                    <button type="button" onclick="copyText('<?= htmlspecialchars(addslashes($val),ENT_QUOTES) ?>',this)" title="Copy <?= $lbl ?>"
                            style="margin-left:5px;background:none;border:1px solid var(--border);border-radius:4px;cursor:pointer;padding:1px 5px;font-size:.65rem;color:var(--t3);vertical-align:middle">
                      <i class="bx bx-copy"></i>
                    </button>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </table>
            </div>
            <?php endif; ?>

            <?php if ($bankNote): ?>
            <div style="padding:10px 14px;background:rgba(200,149,108,.1);border-top:1px solid rgba(200,149,108,.2);display:flex;gap:8px;align-items:flex-start">
              <i class="bx bx-info-circle" style="color:var(--gold-s);font-size:1.1rem;flex-shrink:0;margin-top:1px"></i>
              <p style="font-size:.76rem;color:var(--t2);margin:0;line-height:1.6"><?= nl2br(e($bankNote)) ?></p>
            </div>
            <?php endif; ?>

          </div>
        </div>
        <?php endif; ?>

        <!-- My Upgrade Requests -->
        <?php if (!empty($myRequests)): ?>
        <div class="card"><div class="card-header"><h3><i class="bx bx-up-arrow-circle"></i> My Upgrade Requests</h3></div><div class="card-body" style="display:flex;flex-direction:column;gap:7px">
          <?php foreach ($myRequests as $req): ?>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:9px 11px;display:flex;align-items:center;justify-content:space-between;gap:9px;flex-wrap:wrap">
            <div>
              <div style="font-size:.81rem;font-weight:600;color:var(--t1)"><?= ucfirst($req['request_plan']) ?> &middot; <?= ucfirst($req['billing_cycle']) ?></div>
              <div style="font-size:.69rem;color:var(--t3)"><?= date('d M Y',strtotime($req['created_at'])) ?></div>
              <?php if ($req['admin_note']): ?><div style="font-size:.71rem;color:var(--t2);margin-top:1px">Note: <?= e($req['admin_note']) ?></div><?php endif; ?>
            </div>
            <span class="badge <?= $req['status']==='approved'?'badge-green':($req['status']==='rejected'?'badge-red':'badge-gold') ?>"><?= ucfirst($req['status']) ?></span>
          </div>
          <?php endforeach; ?>
        </div></div>
        <?php endif; ?>

      </div>
    </div>

    <script>
    /* ── QR Zoom lightbox ── */
    function openQrZoom() {
      var lb = document.getElementById('qrLightbox');
      if (!lb) return;
      lb.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
    function closeQrZoom() {
      var lb = document.getElementById('qrLightbox');
      if (!lb) return;
      lb.style.display = 'none';
      document.body.style.overflow = '';
    }
    /* Close on Escape key */
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeQrZoom();
    });

    /* ── Copy to clipboard ── */
    function copyText(text, btn) {
      navigator.clipboard.writeText(text).then(function() {
        var icon = btn.querySelector('i');
        if (icon) icon.className = 'bx bx-check';
        btn.style.color = 'var(--green)';
        btn.style.borderColor = 'var(--green)';
        setTimeout(function() {
          if (icon) icon.className = 'bx bx-copy';
          btn.style.color = '';
          btn.style.borderColor = '';
        }, 1800);
      }).catch(function() {
        var ta = document.createElement('textarea');
        ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
      });
    }
    </script>
