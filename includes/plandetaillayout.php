 <?php if (!$isBasic): ?>
    <!-- ── No subscription ── -->
    <div style="max-width:520px;margin:3rem auto;text-align:center">
      <div class="card">
        <div class="card-body" style="padding:3rem 2rem">
          <div style="font-size:3rem;margin-bottom:1rem">🔒</div>
          <h2 style="font-size:1.2rem;color:var(--t1);margin-bottom:.5rem">Subscription Required</h2>
          <p style="font-size:.85rem;color:var(--t3);margin-bottom:1.5rem;line-height:1.7">
            You need an active subscription to view plan details.<br>
            Please contact the administrator to activate your plan.
          </p>
          <a href="index.php" class="btn btn-ghost btn-md">← Back to Dashboard</a>
        </div>
      </div>
    </div>
    <?php else: ?>

    <!-- ── Subscription plan banner ── -->
    <?php if (!is_admin()): ?>
    <?php $curSub = get_active_subscription();
      ?>
    <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;margin-bottom:1.2rem;
                border-radius:8px;border:1px solid <?= $isAdvance ? 'rgba(217,119,6,0.4)' : 'rgba(5,150,105,0.4)' ?>;
                background:<?= $isAdvance ? 'rgba(217,119,6,0.08)' : 'rgba(5,150,105,0.08)' ?>">
      <span style="font-size:1.1rem"><?= $isAdvance ? '⭐' : '✅' ?></span>
      <div>
        <span style="font-size:.78rem;font-weight:700;color:<?= $isAdvance ? 'var(--gold-s)' : '#34d399' ?>">
          <?= $isAdvance ? 'Advance Plan' : 'Basic Plan' ?> Active
        </span>
        <span style="font-size:.72rem;color:var(--t4);margin-left:8px">
          · Expires <?= $curSub ? date('d M Y', strtotime($curSub['end_date'])) : '—' ?>
        </span>
      </div>
      <?php if (!$isAdvance): ?>
      <span style="margin-left:auto;font-size:.72rem;color:var(--t4)">
        Upgrade to Advance for chain docs &amp; DLC access
      </span>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Action bar ── -->
    <div style="display:flex;gap:10px;margin-bottom:1.5rem;align-items:center;flex-wrap:wrap">
      <a href="index.php" class="btn btn-ghost btn-sm"><i class="bx bx-arrow-back"></i> Back</a>
      <?php if (is_admin()): ?>
      <a href="index.php?page=edit&id=<?= $plan['id'] ?>" class="btn btn-secondary btn-sm"><i class="bx bx-edit"></i> Edit</a>
      <?php endif; ?>
      <?php if ($plan['google_location'] && $isBasic): ?>
      <a href="<?= e($plan['google_location']) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm"><i class="bx bx-map-pin"></i> Open in Maps</a>
      <?php endif; ?>
      <?php if ($plan['file_path'] && $isAdvance): ?>
      <a href="<?= e($plan['file_path']) ?>" download="<?= e($plan['file_name']) ?>" class="btn btn-secondary btn-sm"><i class="bx bx-download"></i> Download Plan</a>
      <?php endif; ?>
      <?php if (is_admin()): ?>
      <form method="POST" style="margin-left:auto" onsubmit="return confirm('Permanently delete this plan?')">
        <input type="hidden" name="action" value="delete_plan">
        <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
        <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i> Delete</button>
      </form>
      <?php endif; ?>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem" class="view-layout">

      <!-- ── Left column ── -->
      <div>

        <!-- Plan image (Basic+) -->
        <?php if ($plan['file_type'] === 'image' && $plan['file_path'] && $isBasic): ?>
        <div class="card" style="margin-bottom:1.2rem;overflow:hidden">
          <img src="<?= e($plan['file_path']) ?>" alt="Plan"
               style="width:100%;display:block;max-height:360px;object-fit:cover">
          <?php if ($isAdvance): ?>
          <div style="padding:10px 14px;border-top:1px solid var(--line);text-align:right">
            <a href="<?= e($plan['file_path']) ?>" download="<?= e($plan['file_name']) ?>"
               class="btn btn-secondary btn-sm"><i class="bx bx-image-download"></i> Download Image</a>
          </div>
          <?php else: ?>
          <div style="padding:8px 14px;border-top:1px solid var(--line);
                      font-size:.72rem;color:var(--t4)">
            🔒 Download requires Advance plan
          </div>
          <?php endif; ?>
        </div>

        <?php elseif ($plan['file_type'] === 'pdf' && $plan['file_path']): ?>
        <div class="card" style="margin-bottom:1.2rem">
          <div style="padding:2rem;text-align:center;background:var(--slate)">
            <div style="font-size:3rem;margin-bottom:.8rem">📄</div>
            <p style="font-size:.85rem;color:var(--t2);margin-bottom:1rem"><?= e($plan['file_name']) ?></p>
            <?php if ($isAdvance): ?>
            <a href="<?= e($plan['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm"><i class="bx bx-file-pdf"></i> Open PDF</a>
            <a href="<?= e($plan['file_path']) ?>" download="<?= e($plan['file_name']) ?>"
               class="btn btn-ghost btn-sm" style="margin-left:8px"><i class="bx bx-download"></i> Download</a>
            <?php else: ?>
            <div style="font-size:.78rem;color:var(--t4);padding:8px 14px;border-radius:6px;
                        background:var(--navy);border:1px solid var(--line);display:inline-block">
              🔒 PDF access requires Advance plan
            </div>
            <?php endif; ?>
          </div>
        </div>

        <?php elseif (!$plan['file_path']): ?>
        <div class="card" style="margin-bottom:1.2rem">
          <div style="padding:3rem;text-align:center;background:var(--slate)">
            <div style="font-size:3rem;margin-bottom:.6rem"><i class="bx bx-map"></i></div>
            <p style="font-size:.82rem;color:var(--t4)">No file uploaded</p>
          </div>
        </div>
        <?php endif; ?>
        
         <!-- Approved map (dev plans) -->
        <?php if ($isDeveloperPlan&&$plan['approved_map_path']): ?>
        <div class="card" style="margin-bottom:1.1rem;overflow:hidden">
          <div style="background:var(--green-bg);border-bottom:1px solid var(--border);padding:6px 12px;font-size:.72rem;font-weight:700;color:var(--green)">APPROVED PLAN MAP</div>
          <?php if ($plan['approved_map_type']==='image'): ?>
          <img src="<?= e($plan['approved_map_path']) ?>" alt="Approved Map" class="zoomable" onclick="openZoom(this.src)" style="width:100%;display:block;max-height:260px;object-fit:contain;background:var(--surface2)">
          <?php else: ?>
          <div style="padding:1.5rem;text-align:center;background:var(--surface2)"><div style="font-size:2rem;margin-bottom:.5rem">[PDF]</div><p style="font-size:.82rem;margin-bottom:.7rem"><?= e($plan['approved_map_name']) ?></p></div>
          <?php endif; ?>
          <div style="padding:7px 12px;background:var(--green-bg);border-top:1px solid var(--border);text-align:right"><a href="<?= e($plan['approved_map_path']) ?>" download class="btn btn-success btn-sm">Download Approved Map</a></div>
        </div>
        <?php endif; ?>
       
     
        
        
        
        <!-- Map (Basic+) -->
        <?php if ($plan['google_location'] && $isBasic): ?>
        <?php $emb = embedUrl($plan['google_location']); ?>
        <div class="map-embed">
          <?php if ($emb): ?>
          <iframe src="<?= e($emb) ?>" allowfullscreen loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"></iframe>
          <?php else: ?>
          <div style="height:180px;display:flex;flex-direction:column;align-items:center;
                      justify-content:center;gap:8px;background:var(--slate);border-radius:var(--radius)">
            <span style="font-size:2rem">📍</span>
            <a href="<?= e($plan['google_location']) ?>" target="_blank" rel="noopener"
               style="font-size:.8rem;color:var(--blue-s)"><i class="bx bx-map-pin"></i> Open in Google Maps</a>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        
       
        
      </div>

      <!-- ── Right column ── -->
      <div>
        <!-- Plan info (always visible to Basic+) -->
        <div class="card" style="margin-bottom:1.2rem">
          <div class="card-header">
            <h3><i class="bx bx-info-circle"></i> Plan Information</h3>
                  </div>
          <div class="card-body">
            <div style="margin-bottom:1.2rem">
              <div style="font-size:1.15rem;font-weight:700;color:var(--t1);margin-bottom:3px">
                <?= e($plan['plan_name']) ?>
              </div>
            </div>
            <table style="width:100%;border-collapse:collapse">
              <?php $rows = [
                ['Aaraji Number',   $plan['aaraji_number']],
                ['Revenue Village', $plan['village_name'] ?: '—'],
                ['Tehsil',          $plan['tehsil'] ?: '—'],
                ['District',        $plan['district'] ?: '—'],
              ];
              if (is_admin()) $rows = array_merge($rows, [
                ['File Type',       $plan['file_type'] ? strtoupper($plan['file_type']) : '—'],
                ['Registered By',   $plan['created_by_name'] ?: '—'],
                ['Registered On',   date('d M Y', strtotime($plan['created_at']))],
              ]);
              foreach ($rows as [$lbl,$val]): ?>
              <tr>
                <td style="padding:7px 0;border-bottom:1px solid var(--line);
                           font-size:.67rem;font-weight:700;color:var(--t4);
                           text-transform:uppercase;letter-spacing:.08em;width:38%"><?= e($lbl) ?></td>
                <td style="padding:7px 0;border-bottom:1px solid var(--line);
                           font-size:.82rem;color:var(--t1);
                           font-family:'JetBrains Mono',monospace;text-align:right"><?= e($val) ?></td>
              </tr>
              <?php endforeach; ?>
            </table>
            <?php if ($plan['notes'] && is_admin()): ?>
            <div style="margin-top:1rem">
              <div style="font-size:.67rem;font-weight:700;color:var(--t4);
                          text-transform:uppercase;letter-spacing:.08em;margin-bottom:5px">Notes</div>
              <p style="font-size:.83rem;color:var(--t2);line-height:1.7"><?= nl2br(e($plan['notes'])) ?></p>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <!-- DLC Rates card -->
        <?php
        // Developer plans: show DLC to ALL subscribed users (basic+)
        // Admin/Aaraji plans: show DLC to Advance users only
        
        $canSeeDlc = $isDeveloperPlan ? $isBasic : $isAdvance;
        ?>

        <?php if (!empty($planDlc) && $canSeeDlc): ?>
        <div class="card" style="margin-bottom:1.2rem">
          <div class="card-header">
            <div style="display:flex;align-items:center;gap:8px">
              <i class="bx bx-bar-chart-alt-2" style="font-size:1.1rem;color:var(--gold-s)"></i>
              <h3>DLC Rates</h3>
              <?php if ($isDeveloperPlan): ?>
              <span style="font-size:.65rem;background:#e8f0fe;color:#4a5fca;border:1px solid #c0c8f0;
                           border-radius:4px;padding:1px 7px;font-weight:700">Govt. Rate</span>
              <?php endif; ?>
            </div>
            <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap">
              <span class="badge badge-gold">FY <?= e($planDlc['financial_year']) ?></span>
              <span style="font-size:.68rem;color:var(--t3)">
                <i class="bx bx-calendar"></i>
                Eff. <?= date('d M Y', strtotime($planDlc['effective_from'])) ?>
              </span>
            </div>
          </div>
          <div class="card-body" style="padding:0">
            <?php if ($plan['village_name']): ?>
            <div style="padding:10px 14px 0;font-size:.75rem;color:var(--t3)">
              <i class="bx bx-building-house"></i>
              Government DLC rates for
              <strong style="color:var(--t1)"><?= e($plan['village_name']) ?></strong>
              <?php if (!empty($plan['tehsil'])): ?>
              — <?= e($plan['tehsil']) ?>
              <?php endif; ?>
            </div>
            <?php endif; ?>

            <table style="width:100%;border-collapse:collapse;margin-top:8px">
              <thead>
                <tr style="background:var(--surface2)">
                  <th style="padding:7px 14px;font-size:.65rem;font-weight:700;color:var(--t3);
                             text-align:left;text-transform:uppercase;letter-spacing:.06em;
                             border-bottom:1px solid var(--line)">Road Width</th>
                  <th style="padding:7px 14px;font-size:.65rem;font-weight:700;color:var(--t3);
                             text-align:right;text-transform:uppercase;letter-spacing:.06em;
                             border-bottom:1px solid var(--line)">Rs / sq.m</th>
                  <th style="padding:7px 14px;font-size:.65rem;font-weight:700;color:var(--t3);
                             text-align:right;text-transform:uppercase;letter-spacing:.06em;
                             border-bottom:1px solid var(--line)">Rs / sq.ft</th>
                </tr>
              </thead>
              <tbody>
              <?php
              $dlcRows = [
                ['30 ft Road',   $planDlc['road_30ft']],
                ['40 ft Road',   $planDlc['road_40ft']],
                ['60 ft Road',   $planDlc['road_60ft']],
                ['80 ft Road',   $planDlc['road_80ft']],
                ['100 ft Road',  $planDlc['road_100ft']],
                ['Near Highway', $planDlc['near_highway']],
              ];
              $dlcHasAny = false;
              foreach ($dlcRows as [$lbl, $val]):
                if ($val === null) continue;
                $dlcHasAny = true;
                $sqft = round($val / 10.76, 2);
              ?>
              <tr style="border-bottom:1px solid var(--surface2)">
                <td style="padding:9px 14px;font-size:.8rem;font-weight:500;color:var(--t2)">
                  <i class="bx bx-road" style="font-size:.85rem;color:var(--t3)"></i>
                  <?= e($lbl) ?>
                </td>
                <td style="padding:9px 14px;font-size:.84rem;font-weight:700;
                           color:var(--gold-s);text-align:right;font-family:'JetBrains Mono',monospace">
                  Rs <?= number_format((float)$val, 2) ?>
                </td>
                <td style="padding:9px 14px;font-size:.78rem;font-weight:600;
                           color:var(--t3);text-align:right;font-family:'JetBrains Mono',monospace">
                  Rs <?= number_format($sqft, 2) ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$dlcHasAny): ?>
              <tr>
                <td colspan="3" style="padding:14px;text-align:center;font-size:.82rem;color:var(--t4)">
                  No DLC rates entered for this village yet.
                </td>
              </tr>
              <?php endif; ?>
              </tbody>
            </table>

            <div style="padding:8px 14px 10px;font-size:.68rem;color:var(--t4);
                        border-top:1px solid var(--surface2);display:flex;
                        align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px">
              <span><i class="bx bx-info-circle"></i> Sq.ft rate = Sq.m rate ÷ 10.76</span>
              <?php if ($isDeveloperPlan): ?>
              <span style="background:var(--surface2);border:1px solid var(--line);
                           border-radius:4px;padding:2px 8px;font-size:.65rem">
                <i class="bx bx-buildings"></i> Developer Plan
              </span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <?php elseif (!empty($plan['village_name']) && empty($planDlc) && $canSeeDlc): ?>
        <!-- Village has no DLC data yet -->
        <div class="card" style="margin-bottom:1.2rem">
          <div class="card-header">
            <h3><i class="bx bx-bar-chart-alt-2"></i> DLC Rates</h3>
          </div>
          <div style="padding:1.3rem 1.4rem;display:flex;align-items:center;gap:12px">
            <i class="bx bx-data" style="font-size:1.8rem;color:var(--t4)"></i>
            <div>
              <div style="font-size:.83rem;font-weight:600;color:var(--t2)">
                No DLC rates available for
                <strong><?= e($plan['village_name']) ?></strong>
              </div>
              <div style="font-size:.72rem;color:var(--t4);margin-top:2px">
                <?php if (is_admin()): ?>
                <a href="index.php?page=dlc">Add DLC rates for this village →</a>
                <?php else: ?>
                DLC rates not yet entered by admin for this village.
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <?php elseif (!$canSeeDlc && !$isDeveloperPlan): ?>
        <!-- Locked for basic users on admin plans -->
        <div class="card" style="margin-bottom:1.2rem">
          <div class="card-header">
            <h3><i class="bx bx-bar-chart-alt-2"></i> DLC Rates</h3>
          </div>
          <div style="padding:1.3rem 1.4rem;display:flex;align-items:center;gap:12px;
                      background:var(--surface2)">
            <i class="bx bx-lock-alt" style="font-size:1.6rem;color:var(--t3)"></i>
            <div>
              <div style="font-size:.83rem;font-weight:600;color:var(--t2)">
                Advance Plan Required
              </div>
              <div style="font-size:.72rem;color:var(--t4);margin-top:2px">
                Upgrade to Advance plan to view DLC rates for this village.
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
        
       
          <!-- Pricing (dev plans) -->
        <?php if ($isDeveloperPlan): $hasPricing=array_filter(['price_30ft','price_40ft','price_60ft','price_80ft','price_100ft','price_highway'],fn($f)=>$plan[$f]!==null); ?>
        <?php if ($hasPricing): ?>
        <div class="card" style="margin-bottom:1.1rem">
          <div class="card-header"><h3><i class="bx bx-purchase-tag"></i> Pricing (per <?= e($plan['price_unit']) ?>)</h3></div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <?php foreach([['30 ft','price_30ft'],['40 ft','price_40ft'],['60 ft','price_60ft'],['80 ft','price_80ft'],['100 ft','price_100ft'],['Highway','price_highway']] as[$lbl,$fld]):
              if ($plan[$fld]===null) continue; ?>
              <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.79rem"><?= $lbl ?></td><td style="padding:5px 0;border-bottom:1px solid var(--surface2);text-align:right;font-weight:700;color:var(--primary-d);font-size:.82rem">Rs <?= number_format((float)$plan[$fld],2) ?>/<?= e($plan['price_unit']) ?></td></tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($plan['brokerage_rate']): ?>
        <div class="card" style="margin-bottom:1.1rem"><div class="card-header"><h3><i class="bx bx-transfer"></i> Brokerage</h3></div><div class="card-body"><div style="font-size:1.2rem;font-weight:700;color:var(--gold-s);margin-bottom:.4rem"><?= number_format((float)$plan['brokerage_rate'],2) ?>%</div><?php if ($plan['brokerage_notes']): ?><p style="font-size:.81rem;color:var(--t2)"><?= e($plan['brokerage_notes']) ?></p><?php endif; ?></div></div>
        <?php endif; ?>
        	<?php endif; ?>
        
        
        <!-- Chain Documents (Advance+ only) -->
        <?php if ($isAdvance): ?>
        <?php if (!empty($chainDocs)): ?>
        <div class="card">
          <div class="card-header">
            <h3>🔗 Chain Documents</h3>
            <span class="badge badge-blue"><?= count($chainDocs) ?> file<?= count($chainDocs)!==1?'s':'' ?></span>
          </div>
          <div class="card-body" style="display:flex;flex-direction:column;gap:10px">
            <?php foreach ($chainDocs as $idx => $doc): ?>
            <div style="display:flex;align-items:center;gap:12px;background:var(--slate);
                        border:1px solid var(--line);border-radius:10px;padding:11px 14px">
              <div style="width:42px;height:42px;flex-shrink:0;border-radius:7px;
                          background:var(--navy2);border:1px solid var(--line2);
                          display:flex;align-items:center;justify-content:center;
                          font-size:1.2rem;overflow:hidden">
                <?php if ($doc['file_type']==='image'): ?>
                <img src="<?= e($doc['file_path']) ?>" alt=""
                     style="width:100%;height:100%;object-fit:cover;border-radius:6px">
                <?php else: ?>📄<?php endif; ?>
              </div>
              <div style="flex:1;min-width:0">
                <div style="font-size:.82rem;font-weight:600;color:var(--t1);
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                  <?= e($doc['file_name']) ?>
                </div>
                <div style="font-size:.68rem;color:var(--t4);font-family:'JetBrains Mono',monospace;margin-top:2px">
                  <?= strtoupper($doc['file_type']) ?>
                  <?= $doc['file_size'] ? ' · '.round($doc['file_size']/1024).' KB' : '' ?>
                  · Doc #<?= $idx+1 ?>
                </div>
              </div>
              <div style="display:flex;gap:6px;flex-shrink:0">
                <a href="<?= e($doc['file_path']) ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="bx bx-link-external"></i></a>
                <a href="<?= e($doc['file_path']) ?>" download="<?= e($doc['file_name']) ?>"
                   class="btn btn-secondary btn-sm"><i class="bx bx-download"></i></a>
                <?php if (is_admin()): ?>
                <form method="POST" onsubmit="return confirm('Delete this chain document?')">
                  <input type="hidden" name="action"     value="delete_chain_doc">
                  <input type="hidden" name="doc_id"     value="<?= $doc['id'] ?>">
                  <input type="hidden" name="plan_id"    value="<?= $plan['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
                  <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
                </form>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php else: ?>
        <div class="card">
          <div class="card-header"><h3>🔗 Chain Documents</h3></div>
          <div style="padding:1.5rem;text-align:center;color:var(--t4);font-size:.82rem">
            No chain documents attached.
            <?php if(is_admin()): ?>
            <a href="index.php?page=edit&id=<?= $plan['id'] ?>" style="color:var(--blue-s);margin-left:6px">Add →</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <!-- Locked chain docs for basic users -->
        <div class="card">
          <div class="card-header"><h3>🔗 Chain Documents</h3></div>
          <div style="padding:1.5rem;display:flex;align-items:center;gap:12px">
            <span style="font-size:1.4rem">🔒</span>
            <div>
              <div style="font-size:.83rem;font-weight:600;color:var(--t2)">Advance Plan Required</div>
              <div style="font-size:.72rem;color:var(--t4)">Upgrade to view &amp; download chain documents</div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; /* end isBasic check */ ?>