    <?php $pwErrMap=['wrongpw'=>'Current password incorrect.','short'=>'New password must be 6+ characters.','mismatch'=>'Passwords do not match.'];
    if ($msg==='pwchanged'): ?><div class="alert alert-success">Password changed successfully.</div>
    <?php elseif (isset($pwErrMap[$err])): ?><div class="alert alert-danger"><?= $pwErrMap[$err] ?></div><?php endif; ?>
    <?php if ($msg==='upgrade_requested'): ?><div class="alert alert-success">Upgrade request submitted. Admin will review shortly.</div>
    <?php elseif ($err==='already_requested'): ?><div class="alert alert-warning">You already have a pending upgrade request.</div><?php endif; ?>

    <div class="two-col">
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
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card"><div class="card-header"><h3><i class="bx bx-credit-card"></i> My Subscription</h3></div><div class="card-body">
          <?php if ($mySub): ?>
          <div style="text-align:center;padding:.9rem 0;margin-bottom:1rem"><div style="font-size:2.2rem;margin-bottom:.4rem">[SUB]</div><div style="font-size:1.1rem;font-weight:700;color:var(--t1)"><?= ucfirst($mySub['plan_type']) ?> Plan</div><div style="font-size:.77rem;color:var(--t3);margin-top:2px"><?= ucfirst($mySub['billing_cycle']) ?> - Expires <?= date('d M Y',strtotime($mySub['end_date'])) ?></div></div>
          <?php else: ?><div style="text-align:center;padding:1.2rem;color:var(--t3)"><div style="font-size:2rem;margin-bottom:.4rem">[--]</div><div style="font-size:.84rem">No active subscription</div></div><?php endif; ?>
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
        <?php if (!empty($myRequests)): ?>
        <div class="card"><div class="card-header"><h3><i class="bx bx-up-arrow-circle"></i> My Upgrade Requests</h3></div><div class="card-body" style="display:flex;flex-direction:column;gap:7px">
          <?php foreach ($myRequests as $req): ?>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:9px 11px;display:flex;align-items:center;justify-content:space-between;gap:9px;flex-wrap:wrap">
            <div><div style="font-size:.81rem;font-weight:600;color:var(--t1)"><?= ucfirst($req['request_plan']) ?> - <?= ucfirst($req['billing_cycle']) ?></div><div style="font-size:.69rem;color:var(--t3)"><?= date('d M Y',strtotime($req['created_at'])) ?></div><?php if ($req['admin_note']): ?><div style="font-size:.71rem;color:var(--t2);margin-top:1px">Note: <?= e($req['admin_note']) ?></div><?php endif; ?></div>
            <span class="badge <?= $req['status']==='approved'?'badge-green':($req['status']==='rejected'?'badge-red':'badge-gold') ?>"><?= ucfirst($req['status']) ?></span>
          </div>
          <?php endforeach; ?>
        </div></div>
        <?php endif; ?>
      </div>
    </div>