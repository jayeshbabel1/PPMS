<?php $errMap=['missing'=>'User, dates required.','userdata'=>'Username and password (6+ chars) required.','username'=>'Username: letters, numbers, underscore only.','exists'=>'Username already taken.'];
    if (isset($errMap[$err])): ?><div class="alert alert-danger"><?= $errMap[$err] ?></div><?php endif; ?>

    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
      <?php $ss=$subStats??[]; foreach([['[S]','si-blue',$ss['total']??0,'Total'],['[A]','si-green',$ss['active']??0,'Active'],['[B]','si-gray',$ss['basic_count']??0,'Basic'],['[+]','si-gold',$ss['advance_count']??0,'Advance']] as[$ic,$sc,$sv,$sl]): ?>
      <div class="stat-card"><div class="stat-icon <?= $sc ?>"><?= $ic ?></div><div><div class="stat-val"><?= (int)$sv ?></div><div class="stat-lbl"><?= $sl ?></div></div></div>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($upgradeReqs)): ?>
    <div class="card" style="margin-bottom:1.2rem">
      <div class="card-header"><h3><i class="bx bx-up-arrow-circle"></i> Pending Upgrade Requests</h3><span class="badge badge-red"><?= count($upgradeReqs) ?></span></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:9px">
        <?php foreach ($upgradeReqs as $req): ?>
        <div style="background:var(--gold-bg);border:1px solid #d4b090;border-radius:var(--r);padding:11px 13px">
          <div style="display:flex;align-items:flex-start;gap:9px;flex-wrap:wrap">
            <div style="flex:1;min-width:180px"><strong style="color:var(--t1)"><?= e($req['username']) ?></strong><?= $req['full_name']?' - '.e($req['full_name']):'' ?><div style="font-size:.75rem;color:var(--t2);margin-top:2px">Wants: <strong><?= ucfirst($req['request_plan']) ?></strong> (<?= $req['billing_cycle'] ?>) | Now: <?= ucfirst($req['current_plan']) ?></div><?php if ($req['message']): ?><div style="font-size:.72rem;color:var(--t3)"><?= e($req['message']) ?></div><?php endif; ?></div>
            <form method="POST" action="index.php" style="display:flex;gap:5px;align-items:flex-start;flex-wrap:wrap">
              <input type="hidden" name="action" value="review_upgrade"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><input type="hidden" name="request_id" value="<?= $req['id'] ?>">
              <input class="input" type="text" name="admin_note" placeholder="Note (optional)" style="width:140px;font-size:.77rem">
              <button type="submit" name="status" value="approved" class="btn btn-success btn-sm"><i class="bx bx-check-circle"></i> Approve</button>
              <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm"><i class="bx bx-x-circle"></i> Reject</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:1.2rem">
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card"><div class="card-header"><h3><i class="bx bx-user-plus"></i> Create User Account</h3></div><div class="card-body">
          <form method="POST" action="index.php"><input type="hidden" name="action" value="save_user"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
            <div class="form-field"><label>Username <span class="req">*</span></label><input class="input" type="text" name="new_username" required placeholder="letters_numbers" autocomplete="off"></div>
            <div class="form-field"><label>Full Name</label><input class="input" type="text" name="new_fullname" placeholder="e.g. Ramesh Patil"></div>
            <div class="form-field"><label>Email</label><input class="input" type="email" name="new_email" placeholder="user@example.com"></div>
            <div class="form-field"><label>Password <span class="req">*</span></label><input class="input" type="password" name="new_password" placeholder="Min 6 characters" autocomplete="new-password"></div>
            <div class="form-field"><label>Role</label>
              <select class="input" name="new_role">
                <option value="viewer">Viewer (subscription-gated)</option>
                <option value="developer">Developer (submit plans)</option>
                <option value="admin">Admin (full access)</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-user-plus"></i> Create Account</button>
          </form>
        </div></div>
        <div class="card"><div class="card-header"><h3><i class="bx bx-credit-card"></i> Assign Subscription</h3></div><div class="card-body">
          <form method="POST" action="index.php"><input type="hidden" name="action" value="save_subscription"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><input type="hidden" name="sub_id" value="0">
            <div class="form-field"><label>User <span class="req">*</span></label><select class="input" name="sub_user_id" required><option value="">-- Select --</option><?php foreach ($viewerUsers as $vu): ?><option value="<?= $vu['id'] ?>"><?= e($vu['username']) ?> (<?= e($vu['role']) ?>)<?= $vu['full_name']?' - '.e($vu['full_name']):'' ?></option><?php endforeach; ?></select></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div class="form-field"><label>Plan</label><select class="input" name="plan_type" id="planType" onchange="updateAmt()"><option value="basic">Basic</option><option value="advance">Advance</option></select></div>
              <div class="form-field"><label>Billing</label><select class="input" name="billing_cycle" id="billCycle" onchange="updateAmt()"><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div class="form-field"><label>Start <span class="req">*</span></label><input class="input" type="date" name="start_date" id="startDate" value="<?= date('Y-m-d') ?>" onchange="calcEnd()"></div>
              <div class="form-field"><label>End <span class="req">*</span></label><input class="input" type="date" name="end_date" id="endDate"></div>
            </div>
            <div class="form-field"><label>Amount (Rs)</label><input class="input" type="number" name="amount" id="amtField" step="0.01" min="0" placeholder="Auto-filled"></div>
            <div class="form-field"><label>Notes</label><input class="input" type="text" name="notes" placeholder="Payment ref etc."></div>
            <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-credit-card"></i> Assign Subscription</button>
          </form>
        </div></div>
        <div class="card"><div class="card-header"><h3><i class="bx bx-purchase-tag"></i> Pricing Reference</h3></div><div class="card-body">
          <table style="width:100%;border-collapse:collapse">
            <?php foreach([['Basic','Monthly',PLAN_BASIC_MONTHLY],['Basic','Yearly',PLAN_BASIC_YEARLY],['Advance','Monthly',PLAN_ADVANCE_MONTHLY],['Advance','Yearly',PLAN_ADVANCE_YEARLY]] as[$pl,$cy,$am]): ?>
            <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.8rem"><?= $pl ?></td><td style="font-size:.77rem;color:var(--t3)"><?= $cy ?></td><td style="text-align:right;font-weight:700;color:var(--gold-s);font-size:.82rem">Rs<?= number_format($am) ?></td></tr>
            <?php endforeach; ?>
          </table>
        </div></div>
      </div>
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card"><div class="card-header"><h3><i class="bx bx-group"></i> All Users</h3><span class="badge badge-blue"><?= count($allUsers) ?></span></div>
          <div class="table-wrap"><table>
            <thead><tr><th>User</th><th>Role</th><th>Plan</th><th>Expires</th></tr></thead>
            <tbody><?php foreach ($allUsers as $au): ?>
              <tr><td><strong style="color:var(--t1)"><?= e($au['username']) ?></strong><?= $au['full_name']?'<br><small style="color:var(--t3)">'.e($au['full_name']).'</small>':'' ?><?= $au['email']?'<br><small style="color:var(--t4)">'.e($au['email']).'</small>':'' ?></td>
              <td><span class="badge <?= $au['role']==='admin'?'badge-blue':($au['role']==='developer'?'badge-dev':'badge-gray') ?>"><?= ucfirst($au['role']) ?></span></td>
              <td><?php if ($au['role']==='admin'): ?><span class="badge badge-blue">Admin</span><?php elseif ($au['role']==='developer'): ?><span class="badge badge-dev">Developer</span><?php elseif ($au['plan_type']&&$au['sub_active']): ?><span class="badge <?= $au['plan_type']==='advance'?'badge-gold':'badge-green' ?>"><?= ucfirst($au['plan_type']) ?></span><?php else: ?><span class="badge badge-gray">None</span><?php endif; ?></td>
              <td style="font-size:.73rem;color:var(--t3)"><?= $au['end_date']?date('d M Y',strtotime($au['end_date'])):'--' ?></td></tr>
            <?php endforeach; ?></tbody>
          </table></div>
        </div>
        <div class="card"><div class="card-header"><h3><i class="bx bx-history"></i> Subscription History</h3><span class="badge badge-blue"><?= count($allSubs) ?></span></div>
          <?php if (empty($allSubs)): ?><div style="padding:1.2rem;text-align:center;color:var(--t3);font-size:.82rem">No subscriptions yet.</div>
          <?php else: ?><div class="table-wrap"><table>
            <thead><tr><th>User</th><th>Plan</th><th>Cycle</th><th>End</th><th>Rs</th><th>Status</th><th></th></tr></thead>
            <tbody><?php foreach ($allSubs as $sub): $isExp=strtotime($sub['end_date'])<time(); $act=$sub['is_active']&&!$isExp; ?>
              <tr><td><strong><?= e($sub['username']) ?></strong></td>
              <td><span class="badge <?= $sub['plan_type']==='advance'?'badge-gold':'badge-green' ?>"><?= ucfirst($sub['plan_type']) ?></span></td>
              <td style="font-size:.74rem;color:var(--t3)"><?= ucfirst($sub['billing_cycle']) ?></td>
              <td style="font-size:.73rem;color:<?= $isExp?'var(--red)':'var(--t2)' ?>"><?= date('d M Y',strtotime($sub['end_date'])) ?></td>
              <td style="font-weight:700;color:var(--gold-s);font-size:.75rem"><?= $sub['amount']?'Rs'.number_format((float)$sub['amount'],0):'--' ?></td>
              <td><?php if ($act): ?><span class="badge badge-green">Active</span><?php elseif ($isExp): ?><span class="badge badge-gray">Expired</span><?php else: ?><span class="badge badge-red"><i class="bx bx-toggle-left"></i> Off</span><?php endif; ?></td>
              <td><form method="POST" style="display:inline"><input type="hidden" name="action" value="toggle_subscription"><input type="hidden" name="sub_id" value="<?= $sub['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-secondary btn-sm"><?= $sub['is_active']?'Off':'On' ?></button></form>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_subscription"><input type="hidden" name="sub_id" value="<?= $sub['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button></form></td></tr>
            <?php endforeach; ?></tbody>
          </table></div><?php endif; ?>
        </div>
      </div>
    </div>
    <script>
    var prices={basic:{monthly:<?= PLAN_BASIC_MONTHLY ?>,yearly:<?= PLAN_BASIC_YEARLY ?>},advance:{monthly:<?= PLAN_ADVANCE_MONTHLY ?>,yearly:<?= PLAN_ADVANCE_YEARLY ?>}};
    function updateAmt(){var p=document.getElementById('planType').value,c=document.getElementById('billCycle').value,a=document.getElementById('amtField');if(a)a.value=prices[p][c];calcEnd();}
    function calcEnd(){var s=document.getElementById('startDate').value,c=document.getElementById('billCycle').value,e=document.getElementById('endDate');if(!s||!e)return;var d=new Date(s);c==='yearly'?d.setFullYear(d.getFullYear()+1):d.setMonth(d.getMonth()+1);d.setDate(d.getDate()-1);e.value=d.toISOString().split('T')[0];}
    updateAmt();
    </script>