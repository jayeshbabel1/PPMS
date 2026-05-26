<?php
$bankName    = get_setting('bank_name','');
$bankAccNo   = get_setting('bank_account_number','');
$bankBranch  = get_setting('bank_branch','');
$bankIfsc    = get_setting('bank_ifsc','');
$bankAccType = get_setting('bank_account_type','');
$bankUpi     = get_setting('bank_upi_id','');
$bankNote    = get_setting('bank_note','');
$bankQr      = get_setting('bank_qr_path','');
$mutFee      = get_setting('mutation_fee','500');
$hasBankInfo = $bankName || $bankAccNo || $bankIfsc || $bankQr || $bankUpi;
?>

<?php if ($bankQr): ?>
<div id="mutQrLightbox" onclick="closeMutQrZoom()"
     style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.82);
            backdrop-filter:blur(6px);align-items:center;justify-content:center;cursor:zoom-out">
  <div onclick="event.stopPropagation()"
       style="position:relative;animation:qrZoomIn .22s cubic-bezier(.34,1.56,.64,1)">
    <button onclick="closeMutQrZoom()"
            style="position:absolute;top:-14px;right:-14px;width:32px;height:32px;border-radius:50%;
                   background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.3);
                   color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:1">
      <i class="bx bx-x" style="font-size:1.1rem"></i>
    </button>
    <img src="<?= e($bankQr) ?>" alt="QR"
         style="max-width:min(420px,88vw);max-height:80vh;object-fit:contain;border-radius:16px;
                background:#fff;padding:18px;box-shadow:0 32px 80px rgba(0,0,0,.6)">
    <div style="text-align:center;margin-top:10px;font-size:.74rem;color:rgba(255,255,255,.55);
                letter-spacing:.06em;text-transform:uppercase">
      Scan to Pay &nbsp;·&nbsp; Click anywhere to close
    </div>
  </div>
</div>
<?php endif; ?>

<style>
@keyframes qrZoomIn{from{opacity:0;transform:scale(.7)}to{opacity:1;transform:scale(1)}}
.mut-step{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);margin-bottom:1.2rem;overflow:hidden;box-shadow:var(--sh)}
.mut-step-hdr{padding:.85rem 1.2rem;background:var(--surface2);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px}
.mut-step-num{width:26px;height:26px;border-radius:50%;background:var(--primary);color:var(--btn-text);font-size:.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.mut-step-hdr h3{font-size:.9rem;font-weight:700;color:var(--t1);margin:0}
.transferee-block{background:var(--surface2);border:1px solid var(--border);border-radius:var(--r-lg);padding:1rem;margin-bottom:.9rem;position:relative}
.transferee-block .remove-btn{position:absolute;top:10px;right:10px}
.qr-thumb-mut{cursor:zoom-in;transition:transform .18s,box-shadow .18s}
.qr-thumb-mut:hover{transform:scale(1.05);box-shadow:0 8px 28px rgba(0,0,0,.2)}
</style>

<div style="display:flex;align-items:center;gap:10px;margin-bottom:1.4rem;flex-wrap:wrap">
  <a href="index.php?page=mutation" class="btn btn-ghost btn-sm"><i class="bx bx-arrow-back"></i> Back</a>
  <div>
    <h2 style="font-size:1rem;font-weight:700;color:var(--t1);margin:0">Apply for Mutation</h2>
    <p style="font-size:.75rem;color:var(--t3);margin:2px 0 0">Fill all sections below and submit your application</p>
  </div>
</div>

<?php if ($err==='missing'): ?><div class="alert alert-danger">Please fill all required fields.</div><?php endif; ?>
<?php if ($err==='upload'): ?><div class="alert alert-danger">File upload failed. Check size/type and try again.</div><?php endif; ?>

<form method="POST" action="index.php" enctype="multipart/form-data" id="mutForm">
  <input type="hidden" name="action" value="save_mutation">
  <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">

  <!-- STEP 1: Transferees -->
  <div class="mut-step">
    <div class="mut-step-hdr">
      <div class="mut-step-num">1</div>
      <div>
        <h3>Transferee Details</h3>
        <span style="font-size:.72rem;color:var(--t3)">Person(s) to whom the property is being transferred</span>
      </div>
    </div>
    <div class="mut-step-body" style="padding:1.2rem">
      <div id="transfereeList">
        <div class="transferee-block" id="transferee_1">
          <div style="font-size:.72rem;font-weight:700;color:var(--primary-d);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.8rem">
            <i class="bx bx-user"></i> Transferee #1
          </div>
          <div class="form-grid">
            <div class="form-field">
              <label>Full Name <span class="req">*</span></label>
              <input class="input" type="text" name="trf_name[]" required placeholder="Full legal name">
            </div>
            <div class="form-field">
              <label>Gender <span class="req">*</span></label>
              <select class="input" name="trf_gender[]" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="form-field">
              <label>Contact Number</label>
              <input class="input" type="text" name="trf_contact[]" placeholder="+91 98765 43210">
            </div>
            <div class="form-field">
              <label>Email Address</label>
              <input class="input" type="email" name="trf_email[]" placeholder="email@example.com">
            </div>
            <div class="form-field">
              <label>Aadhaar Number</label>
              <input class="input" type="text" name="trf_aadhaar[]" placeholder="XXXX XXXX XXXX" maxlength="14" oninput="fmtAadhaar(this)">
            </div>
            <div class="form-field fg-full">
              <label>Address <span class="req">*</span></label>
              <textarea class="input" name="trf_address[]" required placeholder="Full residential address..." style="min-height:60px"></textarea>
            </div>
          </div>
        </div>
      </div>
      <button type="button" onclick="addTransferee()" class="btn btn-secondary btn-sm">
        <i class="bx bx-user-plus"></i> Add Another Transferee
      </button>
    </div>
  </div>

  <!-- STEP 2: Property Details -->
  <div class="mut-step">
    <div class="mut-step-hdr">
      <div class="mut-step-num">2</div>
      <div>
        <h3>Property Details</h3>
        <span style="font-size:.72rem;color:var(--t3)">Aaraji number, registry and supporting documents</span>
      </div>
    </div>
    <div class="mut-step-body" style="padding:1.2rem">
      <div class="form-grid">
        <div class="form-field">
          <label>Plan / Aaraji Number <span class="req">*</span></label>
          <input class="input" type="text" name="aaraji_number" required placeholder="e.g. ARJ/2024/0012">
        </div>
        <div class="form-field">
          <label>Revenue Village</label>
          <select class="input" name="village_id">
            <option value="">-- Select Village --</option>
            <?php foreach ($villagesAll as $v): ?>
            <option value="<?= $v['id'] ?>"><?= e($v['name']) ?><?= $v['tehsil']?' - '.e($v['tehsil']):'' ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-field">
        <label>Upload Registry Document <span class="req">*</span></label>
        <div class="upload-zone" style="max-width:480px">
          <input type="file" name="registry_file" accept="image/*,.pdf" required id="regFile" onchange="previewUpload(this,'regPrev','regName')">
          <div style="font-size:2rem;margin-bottom:.4rem">📄</div>
          <p>Click or drag to upload Registry</p>
          <small>PDF, JPG, PNG, WEBP — Max <?= MAX_FILE_MB ?>MB</small>
        </div>
        <div id="regPrev" style="display:none;margin-top:7px;align-items:center;gap:8px;background:var(--primary-bg);border:1px solid #a0bcd4;border-radius:var(--r);padding:7px 11px;font-size:.79rem;color:var(--primary-d)">
          <i class="bx bx-check-circle"></i> <span id="regName"></span>
        </div>
      </div>

      <div class="form-field">
        <label>Chain Documents <span style="font-weight:400;font-size:.68rem;color:var(--t3);text-transform:none;letter-spacing:0">(optional — multiple files allowed)</span></label>
        <div class="upload-zone" id="mutChainZone" style="max-width:480px">
          <input type="file" name="chain_docs[]" id="mutChainInput" accept="image/*,.pdf" multiple>
          <div style="font-size:2rem;margin-bottom:.4rem;color:var(--primary-d)"><i class="bx bx-folder-plus"></i></div>
          <p>Click or drag files here</p>
          <small>Multiple files allowed — PDF / Images</small>
        </div>
        <div id="mutChainList" style="display:none;flex-direction:column;gap:5px;margin-top:8px"></div>
      </div>
    </div>
  </div>

  <!-- STEP 3: Payment -->
  <div class="mut-step">
    <div class="mut-step-hdr">
      <div class="mut-step-num">3</div>
      <div>
        <h3>Application Fee &amp; Payment</h3>
        <span style="font-size:.72rem;color:var(--t3)">Pay the mutation fee and submit proof of payment</span>
      </div>
    </div>
    <div class="mut-step-body" style="padding:1.2rem">

      <!-- Fee banner -->
      <div style="display:flex;align-items:center;gap:14px;background:linear-gradient(135deg,var(--gold-bg),#fff);
                  border:1px solid rgba(200,149,108,.4);border-radius:var(--r-lg);padding:14px 18px;margin-bottom:1.2rem">
        <div style="font-size:2rem">💰</div>
        <div>
          <div style="font-size:.7rem;font-weight:700;color:var(--t3);text-transform:uppercase;letter-spacing:.06em">Mutation Application Fee</div>
          <div style="font-size:1.6rem;font-weight:700;color:var(--gold-s)">Rs <?= number_format((float)$mutFee,2) ?></div>
          <div style="font-size:.7rem;color:var(--t4);margin-top:1px">One-time non-refundable fee</div>
        </div>
      </div>

      <!-- Bank details -->
      <?php if ($hasBankInfo): ?>
      <div style="background:linear-gradient(135deg,var(--surface),var(--gold-bg));border:1px solid rgba(200,149,108,.35);
                  border-radius:var(--r-lg);margin-bottom:1.2rem;overflow:hidden">
        <div style="padding:9px 14px;background:rgba(200,149,108,.12);border-bottom:1px solid rgba(200,149,108,.2);
                    font-size:.71rem;font-weight:700;color:var(--gold-s);text-transform:uppercase;letter-spacing:.05em">
          <i class="bx bx-credit-card-alt"></i> Pay to Bank Account
        </div>
        <div style="display:flex;flex-wrap:wrap">
          <?php if ($bankQr): ?>
          <div style="padding:14px;border-right:1px solid rgba(200,149,108,.2);text-align:center;flex-shrink:0;background:rgba(255,255,255,.5)">
            <img src="<?= e($bankQr) ?>" alt="QR" class="qr-thumb-mut" onclick="openMutQrZoom()"
                 style="width:100px;height:100px;object-fit:contain;border-radius:8px;border:1px solid var(--border);background:#fff;padding:3px">
            <div style="font-size:.6rem;color:var(--t4);margin-top:4px;font-weight:600;text-transform:uppercase;
                        letter-spacing:.04em;display:flex;align-items:center;justify-content:center;gap:2px">
              <i class="bx bx-zoom-in"></i> Tap to zoom
            </div>
          </div>
          <?php endif; ?>
          <div style="flex:1;min-width:200px;padding:12px 14px">
            <table style="width:100%;border-collapse:collapse">
              <?php
              $brows=[];
              if ($bankName)    $brows[]=['bx bx-buildings','Bank',       $bankName];
              if ($bankAccNo)   $brows[]=['bx bx-hash',     'Account No.',$bankAccNo];
              if ($bankAccType) $brows[]=['bx bx-category', 'Acc. Type',  $bankAccType];
              if ($bankBranch)  $brows[]=['bx bx-map-pin',  'Branch',     $bankBranch];
              if ($bankIfsc)    $brows[]=['bx bx-barcode',  'IFSC',       $bankIfsc];
              if ($bankUpi)     $brows[]=['bx bx-mobile-alt','UPI / GPay',$bankUpi];
              foreach ($brows as [$ic,$lbl,$val]):
              ?>
              <tr>
                <td style="padding:4px 0;border-bottom:1px solid rgba(200,149,108,.12);font-size:.63rem;font-weight:700;
                           color:var(--t4);text-transform:uppercase;letter-spacing:.05em;width:34%;white-space:nowrap">
                  <i class="<?= $ic ?>"></i> <?= $lbl ?>
                </td>
                <td style="padding:4px 0 4px 8px;border-bottom:1px solid rgba(200,149,108,.12);font-size:.78rem;
                           font-weight:600;color:var(--t1);font-family:'JetBrains Mono',monospace;text-align:right;word-break:break-all">
                  <?= e($val) ?>
                  <?php if (in_array($lbl,['Account No.','IFSC','UPI / GPay'])): ?>
                  <button type="button" onclick="copyText('<?= htmlspecialchars(addslashes($val),ENT_QUOTES) ?>',this)"
                          style="margin-left:4px;background:none;border:1px solid var(--border);border-radius:4px;
                                 cursor:pointer;padding:1px 5px;font-size:.63rem;color:var(--t3);vertical-align:middle">
                    <i class="bx bx-copy"></i>
                  </button>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
        <?php if ($bankNote): ?>
        <div style="padding:8px 14px;background:rgba(200,149,108,.1);border-top:1px solid rgba(200,149,108,.2);display:flex;gap:7px;align-items:flex-start">
          <i class="bx bx-info-circle" style="color:var(--gold-s);flex-shrink:0;margin-top:1px"></i>
          <p style="font-size:.74rem;color:var(--t2);margin:0;line-height:1.6"><?= nl2br(e($bankNote)) ?></p>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Payment proof -->
      <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px">
        <div style="font-size:.71rem;font-weight:700;color:var(--t3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">
          <i class="bx bx-receipt"></i> Payment Proof
        </div>
        <div class="form-grid">
          <div class="form-field">
            <label>Transaction Number / UTR</label>
            <input class="input" type="text" name="txn_number" placeholder="e.g. UTR123456789">
          </div>
          <div class="form-field">
            <label>Transaction Date</label>
            <input class="input" type="date" name="txn_date" value="<?= date('Y-m-d') ?>">
          </div>
          <div class="form-field">
            <label>Transaction Type</label>
            <select class="input" name="txn_type">
              <option value="">-- Select --</option>
              <option value="UPI">UPI</option>
              <option value="IMPS">IMPS</option>
              <option value="NEFT">NEFT</option>
              <option value="RTGS">RTGS</option>
              <option value="CHEQUE">Cheque</option>
              <option value="DD">Demand Draft (DD)</option>
              <option value="OTHER">Other</option>
            </select>
          </div>
          <div class="form-field">
            <label>Payment Screenshot <span class="req">*</span></label>
            <div class="upload-zone" style="padding:.8rem">
              <input type="file" name="payment_screenshot" accept="image/*,.pdf" required id="payFile" onchange="previewUpload(this,'payPrev','payName')">
              <div style="font-size:1.5rem;margin-bottom:.3rem">📸</div>
              <p style="font-size:.77rem">Upload screenshot / receipt</p>
              <small>Image or PDF — Max <?= MAX_FILE_MB ?>MB</small>
            </div>
            <div id="payPrev" style="display:none;margin-top:6px;align-items:center;gap:8px;background:var(--green-bg);border:1px solid #9acc9a;border-radius:var(--r);padding:7px 11px;font-size:.79rem;color:var(--green)">
              <i class="bx bx-check-circle"></i> <span id="payName"></span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:.5rem">
    <a href="index.php?page=mutation" class="btn btn-ghost btn-md"><i class="bx bx-x"></i> Cancel</a>
    <button type="submit" class="btn btn-primary btn-md" style="min-width:180px">
      <i class="bx bx-send"></i> Submit Application
    </button>
  </div>
</form>

<script>
var trfCount=1;
function fmtAadhaar(inp){var v=inp.value.replace(/\D/g,'').substring(0,12);inp.value=v.replace(/(\d{4})(?=\d)/g,'$1 ').trim();}
function addTransferee(){
  trfCount++;
  var tpl=document.createElement('div');
  tpl.className='transferee-block';tpl.id='transferee_'+trfCount;
  tpl.innerHTML='<button type="button" class="btn btn-danger btn-sm remove-btn" onclick="removeTransferee('+trfCount+')" style="position:absolute;top:10px;right:10px"><i class="bx bx-x"></i></button>'+
    '<div style="font-size:.72rem;font-weight:700;color:var(--primary-d);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.8rem"><i class="bx bx-user"></i> Transferee #'+trfCount+'</div>'+
    '<div class="form-grid">'+
    '<div class="form-field"><label>Full Name <span class="req">*</span></label><input class="input" type="text" name="trf_name[]" required placeholder="Full legal name"></div>'+
    '<div class="form-field"><label>Gender <span class="req">*</span></label><select class="input" name="trf_gender[]" required><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>'+
    '<div class="form-field"><label>Contact Number</label><input class="input" type="text" name="trf_contact[]" placeholder="+91 98765 43210"></div>'+
    '<div class="form-field"><label>Email Address</label><input class="input" type="email" name="trf_email[]" placeholder="email@example.com"></div>'+
    '<div class="form-field"><label>Aadhaar Number</label><input class="input" type="text" name="trf_aadhaar[]" placeholder="XXXX XXXX XXXX" maxlength="14" oninput="fmtAadhaar(this)"></div>'+
    '<div class="form-field fg-full"><label>Address <span class="req">*</span></label><textarea class="input" name="trf_address[]" required placeholder="Full residential address..." style="min-height:60px"></textarea></div>'+
    '</div>';
  document.getElementById('transfereeList').appendChild(tpl);
}
function removeTransferee(id){var el=document.getElementById('transferee_'+id);if(el)el.remove();}
function previewUpload(input,prevId,nameId){
  var p=document.getElementById(prevId),n=document.getElementById(nameId);
  if(!input.files||!input.files[0]){if(p)p.style.display='none';return;}
  if(p)p.style.display='flex';if(n)n.textContent=input.files[0].name;
}
var mutCI=document.getElementById('mutChainInput'),mutCL=document.getElementById('mutChainList');
function fmtB(b){return b<1024?b+' B':b<1048576?(b/1024).toFixed(1)+' KB':(b/1048576).toFixed(1)+' MB';}
function renderMutChain(files){
  if(!mutCL)return;mutCL.innerHTML='';mutCL.style.display=files.length?'flex':'none';
  for(var i=0;i<files.length;i++){(function(f,idx){
    var r=document.createElement('div');r.style.cssText='display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:7px 11px';
    r.innerHTML='<span>'+(f.type.startsWith('image/')?'🖼️':'📄')+'</span><div style="flex:1;min-width:0"><div style="font-size:.78rem;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+f.name+'</div><div style="font-size:.67rem;color:var(--t3)">'+fmtB(f.size)+' · #'+(idx+1)+'</div></div>';
    mutCL.appendChild(r);
  })(files[i],i);}
}
if(mutCI)mutCI.addEventListener('change',function(){renderMutChain(this.files);});
var mutCZ=document.getElementById('mutChainZone');
if(mutCZ){
  mutCZ.addEventListener('dragover',function(e){e.preventDefault();this.classList.add('drag-over');});
  mutCZ.addEventListener('dragleave',function(){this.classList.remove('drag-over');});
  mutCZ.addEventListener('drop',function(e){e.preventDefault();this.classList.remove('drag-over');if(mutCI){try{var dt=new DataTransfer();for(var i=0;i<e.dataTransfer.files.length;i++)dt.items.add(e.dataTransfer.files[i]);mutCI.files=dt.files;}catch(err){}renderMutChain(mutCI.files);}});
}
function openMutQrZoom(){var lb=document.getElementById('mutQrLightbox');if(lb){lb.style.display='flex';document.body.style.overflow='hidden';}}
function closeMutQrZoom(){var lb=document.getElementById('mutQrLightbox');if(lb){lb.style.display='none';document.body.style.overflow='';}}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeMutQrZoom();});
function copyText(text,btn){
  navigator.clipboard.writeText(text).then(function(){
    var i=btn.querySelector('i');if(i)i.className='bx bx-check';btn.style.color='var(--green)';btn.style.borderColor='var(--green)';
    setTimeout(function(){if(i)i.className='bx bx-copy';btn.style.color='';btn.style.borderColor='';},1800);
  }).catch(function(){var ta=document.createElement('textarea');ta.value=text;ta.style.position='fixed';ta.style.opacity='0';document.body.appendChild(ta);ta.select();document.execCommand('copy');document.body.removeChild(ta);});
}
</script>
