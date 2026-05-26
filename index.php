<?php
// ============================================================
//  index.php — PMS v4.0 Front Controller
// ============================================================
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/upload.php';
require_once __DIR__.'/includes/excel.php';
session_start_safe();

$action = $_POST['action'] ?? '';

// ── AUTH ─────────────────────────────────────────────────────
if ($action==='login') {
    $ok = login(trim($_POST['username']??''), $_POST['password']??'');
    header($ok ? 'Location: index.php' : 'Location: index.php?page=login&err=1');
    exit;
}
if ($action==='logout') { logout(); }

if ($action==='forgot_password') {
    $email = trim($_POST['email']??'');
    if ($email) send_password_reset($email);
    header('Location: index.php?page=login&info=reset_sent');
    exit;
}

if ($action==='do_reset_password') {
    $token = trim($_POST['token']??'');
    $pw    = $_POST['new_password']??'';
    $cf    = $_POST['confirm_password']??'';
    if (!$token || strlen($pw)<6) { header('Location: index.php?page=reset_password&token='.urlencode($token).'&err=short'); exit; }
    if ($pw !== $cf)               { header('Location: index.php?page=reset_password&token='.urlencode($token).'&err=mismatch'); exit; }
    if (!do_password_reset($token,$pw)) { header('Location: index.php?page=reset_password&token='.urlencode($token).'&err=invalid'); exit; }
    header('Location: index.php?page=login&info=pw_reset_done');
    exit;
}

// ── SAVE PLAN (unified: admin + developer) ───────────────────
if ($action==='save_plan') {
    require_login(); csrf_verify();
    $uid = current_user()['id'];
    $isDev = is_developer();
    $isAdm = is_admin();
    if (!$isAdm && !$isDev) { http_response_code(403); die('Forbidden'); }

    $id      = (int)($_POST['plan_id']??0);
    $name    = trim($_POST['plan_name']??'');
    $aaraji  = trim($_POST['aaraji_number']??'');
    $village = (int)($_POST['village_id']??0)?:null;
    $loc     = trim($_POST['google_location']??'');
    $notes   = trim($_POST['notes']??'');
    $contact = trim($_POST['contact_number']??'');
    $pu      = trim($_POST['price_unit']??'sq.ft');
    $pf      = fn($k)=>($_POST[$k]??'')!==''&&is_numeric($_POST[$k])?(float)$_POST[$k]:null;
    $p30=$pf('price_30ft');$p40=$pf('price_40ft');$p60=$pf('price_60ft');
    $p80=$pf('price_80ft');$p100=$pf('price_100ft');$ph=$pf('price_highway');
    $br=$pf('brokerage_rate'); $bn=trim($_POST['brokerage_notes']??'');

    if (!$name||!$aaraji) { header('Location: index.php?page='.($id?"edit&id=$id":'add').'&err=missing'); exit; }

    try { $up = handle_upload($_FILES['plan_file']??['error'=>UPLOAD_ERR_NO_FILE]); }
    catch (RuntimeException $e) { header('Location: index.php?page='.($id?"edit&id=$id":'add').'&err='.urlencode($e->getMessage())); exit; }

    try { $am = handle_upload($_FILES['approved_map']??['error'=>UPLOAD_ERR_NO_FILE]); }
    catch (RuntimeException $e) { header('Location: index.php?page='.($id?"edit&id=$id":'add').'&err='.urlencode($e->getMessage())); exit; }

    // Developer plans go pending; admin plans auto-approved
    $devStatus = $isDev && !$isAdm ? 'pending' : 'na';
    $isPlanDev = ($isDev && !$isAdm) ? 1 : (int)($_POST['is_developer_plan']??0);

    if ($id > 0) {
        $chk = db()->prepare('SELECT created_by,file_path,approved_map_path,dev_status FROM plans WHERE id=?');
        $chk->execute([$id]); $existing = $chk->fetch();
        if (!$existing || (!$isAdm && $existing['created_by']!=$uid)) { http_response_code(403); die('Forbidden'); }
        if ($up['path']) delete_upload($existing['file_path']);
        if ($am['path']) delete_upload($existing['approved_map_path']);

        $sql = 'UPDATE plans SET plan_name=?,aaraji_number=?,village_id=?,google_location=?,notes=?,contact_number=?,price_30ft=?,price_40ft=?,price_60ft=?,price_80ft=?,price_100ft=?,price_highway=?,price_unit=?,brokerage_rate=?,brokerage_notes=?,updated_by=?';
        $p   = [$name,$aaraji,$village,$loc,$notes,$contact,$p30,$p40,$p60,$p80,$p100,$ph,$pu,$br,$bn,$uid];
        if ($up['path']) { $sql.=',file_path=?,file_name=?,file_type=?'; array_push($p,$up['path'],$up['name'],$up['type']); }
        if ($am['path']) { $sql.=',approved_map_path=?,approved_map_name=?,approved_map_type=?'; array_push($p,$am['path'],$am['name'],$am['type']); }
        if ($isDev && !$isAdm) $sql.=',dev_status=\'pending\'';
        db()->prepare($sql.' WHERE id=?')->execute(array_merge($p,[$id]));
      
        // Chain documents — append new ones
        if (!empty($_FILES['chain_docs']['name'][0])) {
            try {
                $chainUploads = handle_multiple_uploads($_FILES['chain_docs']);
                $maxOrder = (int)db()->query(
                    "SELECT COALESCE(MAX(sort_order),0) FROM plan_chain_documents WHERE plan_id=$id"
                )->fetchColumn();
                $ins = db()->prepare(
                    'INSERT INTO plan_chain_documents
                     (plan_id,file_path,file_name,file_type,file_size,sort_order,uploaded_by)
                     VALUES (?,?,?,?,?,?,?)'
                );
                foreach ($chainUploads as $i => $cf) {
                    $ins->execute([$id,$cf['path'],$cf['name'],$cf['type'],$cf['size'],$maxOrder+$i+1,$uid]);
                }
            } catch (RuntimeException $e) {
                header('Location: index.php?page=edit&id='.$id.'&err='.urlencode($e->getMessage()));
                exit;
            }
        }
      
        audit('update','plans',$id,"Updated: $name");
        header('Location: index.php?msg=updated');
    } else {
        db()->prepare('INSERT INTO plans(plan_name,aaraji_number,village_id,google_location,file_path,file_name,file_type,approved_map_path,approved_map_name,approved_map_type,notes,is_developer_plan,contact_number,price_30ft,price_40ft,price_60ft,price_80ft,price_100ft,price_highway,price_unit,brokerage_rate,brokerage_notes,dev_status,created_by,updated_by)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute([$name,$aaraji,$village,$loc,$up['path'],$up['name'],$up['type'],$am['path'],$am['name'],$am['type'],$notes,$isPlanDev,$contact,$p30,$p40,$p60,$p80,$p100,$ph,$pu,$br,$bn,$devStatus,$uid,$uid]);
        $nid = (int)db()->lastInsertId();
        if (!empty($_FILES['chain_docs']['name'][0])) {
            try {
                $cu = handle_multiple_uploads($_FILES['chain_docs']);
                $ins = db()->prepare('INSERT INTO plan_chain_documents(plan_id,file_path,file_name,file_type,file_size,sort_order,uploaded_by)VALUES(?,?,?,?,?,?,?)');
                foreach ($cu as $i=>$cf) $ins->execute([$nid,$cf['path'],$cf['name'],$cf['type'],$cf['size'],$i+1,$uid]);
            } catch (RuntimeException) {}
        }
        audit('create','plans',$nid,"Created: $name");
        header('Location: index.php?msg='.($isDev&&!$isAdm?'dev_submitted':'created'));
    }
    exit;
}

// ── DELETE PLAN ───────────────────────────────────────────────
if ($action==='delete_plan') {
    require_login(); csrf_verify();
    $id = (int)($_POST['plan_id']??0); $uid = current_user()['id'];
    $r = db()->prepare('SELECT created_by,file_path,approved_map_path,plan_name FROM plans WHERE id=?'); $r->execute([$id]); $p=$r->fetch();
    if (!$p||(!is_admin()&&$p['created_by']!=$uid)) { http_response_code(403); die('Forbidden'); }
    delete_upload($p['file_path']); delete_upload($p['approved_map_path']);
    db()->prepare('DELETE FROM plans WHERE id=?')->execute([$id]);
    audit('delete','plans',$id,'Deleted: '.$p['plan_name']);
    header('Location: index.php?msg=deleted'); exit;
}

// ── DELETE CHAIN DOC ──────────────────────────────────────────
if ($action==='delete_chain_doc') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $did=(int)($_POST['doc_id']??0);$pid=(int)($_POST['plan_id']??0);
    $s=db()->prepare('SELECT file_path FROM plan_chain_documents WHERE id=? AND plan_id=?'); $s->execute([$did,$pid]); $d=$s->fetch();
    if ($d) { delete_upload($d['file_path']); db()->prepare('DELETE FROM plan_chain_documents WHERE id=?')->execute([$did]); }
    header('Location: index.php?page=edit&id='.$pid.'&msg=chain_deleted'); exit;
}

// ── APPROVE/REJECT DEVELOPER PLAN ────────────────────────────
if ($action==='review_dev_plan') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $id=(int)($_POST['plan_id']??0); $status=$_POST['status']??''; $note=trim($_POST['admin_note']??'');
    if (!in_array($status,['approved','rejected'])) { header('Location: index.php?page=approvals'); exit; }
    $extra = $status==='approved' ? ',approved_by='.current_user()['id'].',approved_at=NOW()' : '';
    db()->prepare("UPDATE plans SET dev_status=?,dev_admin_note=?$extra WHERE id=?")->execute([$status,$note,$id]);
    audit('update','plans',$id,"Dev plan $status");
    header('Location: index.php?page=approvals&msg=reviewed'); exit;
}

// ── TOGGLE SPONSORED ─────────────────────────────────────────
if ($action==='toggle_sponsored') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $id=(int)($_POST['plan_id']??0); $lbl=trim($_POST['sponsored_label']??'Sponsored');
    $r=db()->prepare('SELECT is_sponsored FROM plans WHERE id=?'); $r->execute([$id]); $row=$r->fetch();
    if ($row) db()->prepare('UPDATE plans SET is_sponsored=?,sponsored_label=? WHERE id=?')->execute([$row['is_sponsored']?0:1,$lbl,$id]);
    header('Location: index.php?page=approvals&msg=updated'); exit;
}

// ── VILLAGES ─────────────────────────────────────────────────
if ($action==='save_village') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $nm=trim($_POST['village_name']??''); $teh=trim($_POST['tehsil']??''); $dist=trim($_POST['district']??''); $vid=(int)($_POST['village_id']??0);
    if (!$nm) { header('Location: index.php?page=villages&err=missing'); exit; }
    if ($vid>0) db()->prepare('UPDATE revenue_villages SET name=?,tehsil=?,district=? WHERE id=?')->execute([$nm,$teh,$dist,$vid]);
    else db()->prepare('INSERT INTO revenue_villages(name,tehsil,district)VALUES(?,?,?)')->execute([$nm,$teh,$dist]);
    header('Location: index.php?page=villages&msg=saved'); exit;
}
if ($action==='delete_village') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $vid=(int)($_POST['village_id']??0);
    db()->prepare('UPDATE plans SET village_id=NULL WHERE village_id=?')->execute([$vid]);
    db()->prepare('DELETE FROM revenue_villages WHERE id=?')->execute([$vid]);
    header('Location: index.php?page=villages&msg=deleted'); exit;
}

// ── DLC EXPORT ────────────────────────────────────────────────
if (($_GET['action']??'')==='export_dlc') {
    require_login();
    $vid=(int)($_GET['village']??0); $fy=trim($_GET['fy']??'');
    $w=['1=1'];$b=[];
    if ($vid){$w[]='d.village_id=?';$b[]=$vid;}
    if ($fy) {$w[]='d.financial_year=?';$b[]=$fy;}
    $ws=implode(' AND ',$w);
    $st=db()->prepare("SELECT d.*,v.name AS village_name,v.tehsil,v.district FROM dlc_rates d LEFT JOIN revenue_villages v ON v.id=d.village_id WHERE $ws ORDER BY v.name,d.financial_year DESC");
    $st->execute($b); $rows=$st->fetchAll();
    $xlsx=dlc_export_xlsx($rows);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="DLC_Rates_'.date('Ymd').'.xlsx"');
    header('Content-Length: '.strlen($xlsx));
    echo $xlsx; exit;
}

// ── DLC CSV IMPORT ────────────────────────────────────────────
if ($action==='import_dlc_csv') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $f=$_FILES['dlc_csv']??null;
    if (!$f||$f['error']!==UPLOAD_ERR_OK) { header('Location: index.php?page=dlc&err=nofile'); exit; }
    $rows=dlc_import_csv($f['tmp_name']); $uid=current_user()['id']; $ok=0; $skip=0;
    foreach ($rows as $row) {
        $r=dlc_map_csv_row($row);
        $vn=$r['village_name']??'';$fy=$r['financial_year']??'';$ef=$r['effective_from']??'';
        if (!$vn||!$fy||!$ef){$skip++;continue;}
        $vs=db()->prepare('SELECT id FROM revenue_villages WHERE name=? LIMIT 1');$vs->execute([$vn]);$vr=$vs->fetch();
        if (!$vr){$skip++;continue;}
        $num=fn($k)=>isset($r[$k])&&$r[$k]!==''?(float)$r[$k]:null;
        try {
            db()->prepare('INSERT INTO dlc_rates(village_id,financial_year,effective_from,road_30ft,road_40ft,road_60ft,road_80ft,road_100ft,near_highway,notes,created_by)VALUES(?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE effective_from=VALUES(effective_from),road_30ft=VALUES(road_30ft),road_40ft=VALUES(road_40ft),road_60ft=VALUES(road_60ft),road_80ft=VALUES(road_80ft),road_100ft=VALUES(road_100ft),near_highway=VALUES(near_highway),notes=VALUES(notes)')->execute([$vr['id'],$fy,$ef,$num('road_30ft'),$num('road_40ft'),$num('road_60ft'),$num('road_80ft'),$num('road_100ft'),$num('near_highway'),$r['notes']??'',$uid]);
            $ok++;
        } catch (PDOException) { $skip++; }
    }
    header("Location: index.php?page=dlc&msg=imported&ok=$ok&skip=$skip"); exit;
}

// ── SAVE DLC ─────────────────────────────────────────────────
if ($action==='save_dlc') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $did=(int)($_POST['dlc_id']??0); $vid=(int)($_POST['village_id']??0); $fy=trim($_POST['financial_year']??''); $ef=trim($_POST['effective_from']??'');
    $num=fn($k)=>($_POST[$k]??'')!==''&&is_numeric($_POST[$k])?(float)$_POST[$k]:null;
    $notes=trim($_POST['notes']??''); $uid=current_user()['id'];
    if (!$vid||!$fy||!$ef) { header('Location: index.php?page=dlc&err=missing'); exit; }
    if ($did>0) db()->prepare('UPDATE dlc_rates SET village_id=?,financial_year=?,effective_from=?,road_30ft=?,road_40ft=?,road_60ft=?,road_80ft=?,road_100ft=?,near_highway=?,notes=? WHERE id=?')->execute([$vid,$fy,$ef,$num('road_30ft'),$num('road_40ft'),$num('road_60ft'),$num('road_80ft'),$num('road_100ft'),$num('near_highway'),$notes,$did]);
    else db()->prepare('INSERT INTO dlc_rates(village_id,financial_year,effective_from,road_30ft,road_40ft,road_60ft,road_80ft,road_100ft,near_highway,notes,created_by)VALUES(?,?,?,?,?,?,?,?,?,?,?)')->execute([$vid,$fy,$ef,$num('road_30ft'),$num('road_40ft'),$num('road_60ft'),$num('road_80ft'),$num('road_100ft'),$num('near_highway'),$notes,$uid]);
    header('Location: index.php?page=dlc&msg=dlc_saved'); exit;
}
if ($action==='delete_dlc') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $did=(int)($_POST['dlc_id']??0);
    db()->prepare('DELETE FROM dlc_rates WHERE id=?')->execute([$did]);
    header('Location: index.php?page=dlc&msg=dlc_deleted'); exit;
}

// ── SUBSCRIPTIONS ─────────────────────────────────────────────
if ($action==='save_subscription') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $sid=(int)($_POST['sub_id']??0); $uid2=(int)($_POST['sub_user_id']??0);
    $plan=$_POST['plan_type']??'basic'; $cycle=$_POST['billing_cycle']??'monthly';
    $start=trim($_POST['start_date']??''); $end=trim($_POST['end_date']??'');
    $amount=($_POST['amount']??'')!==''?(float)$_POST['amount']:null; $notes=trim($_POST['notes']??''); $uid=current_user()['id'];
    if (!$uid2||!$start||!$end) { header('Location: index.php?page=subscriptions&err=missing'); exit; }
    if ($sid>0) db()->prepare('UPDATE subscriptions SET user_id=?,plan_type=?,billing_cycle=?,start_date=?,end_date=?,amount=?,notes=? WHERE id=?')->execute([$uid2,$plan,$cycle,$start,$end,$amount,$notes,$sid]);
    else db()->prepare('INSERT INTO subscriptions(user_id,plan_type,billing_cycle,start_date,end_date,amount,is_active,notes,created_by)VALUES(?,?,?,?,?,?,1,?,?)')->execute([$uid2,$plan,$cycle,$start,$end,$amount,$notes,$uid]);
    header('Location: index.php?page=subscriptions&msg=sub_saved'); exit;
}
if ($action==='toggle_subscription') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $sid=(int)($_POST['sub_id']??0);
    $r=db()->prepare('SELECT is_active FROM subscriptions WHERE id=?'); $r->execute([$sid]); $row=$r->fetch();
    if ($row) db()->prepare('UPDATE subscriptions SET is_active=? WHERE id=?')->execute([$row['is_active']?0:1,$sid]);
    header('Location: index.php?page=subscriptions&msg=sub_saved'); exit;
}
if ($action==='delete_subscription') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $sid=(int)($_POST['sub_id']??0);
    db()->prepare('DELETE FROM subscriptions WHERE id=?')->execute([$sid]);
    header('Location: index.php?page=subscriptions&msg=sub_deleted'); exit;
}
if ($action==='save_user') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $uname=trim($_POST['new_username']??''); $fname=trim($_POST['new_fullname']??'');
    $email=strtolower(trim($_POST['new_email']??'')); $pwd=$_POST['new_password']??'';
    $roleIn=$_POST['new_role']??'viewer';
    $role=in_array($roleIn,['admin','viewer','developer'])?$roleIn:'viewer';
    if (!$uname||strlen($pwd)<6) { header('Location: index.php?page=subscriptions&err=userdata'); exit; }
    if (!preg_match('/^[a-zA-Z0-9_]+$/',$uname)) { header('Location: index.php?page=subscriptions&err=username'); exit; }
    $chk=db()->prepare('SELECT id FROM users WHERE username=?'); $chk->execute([$uname]);
    if ($chk->fetch()) { header('Location: index.php?page=subscriptions&err=exists'); exit; }
    db()->prepare('INSERT INTO users(username,password,full_name,email,role)VALUES(?,?,?,?,?)')->execute([$uname,password_hash($pwd,PASSWORD_BCRYPT,['cost'=>12]),$fname,$email,$role]);
    header('Location: index.php?page=subscriptions&msg=user_created'); exit;
}
if ($action==='request_upgrade') {
    require_login(); csrf_verify();
    $uid=current_user()['id']; $rplan=$_POST['request_plan']??'advance'; $cycle=$_POST['billing_cycle']??'monthly'; $msg=trim($_POST['message']??'');
    $sub=get_active_subscription(); $cur=$sub?$sub['plan_type']:'none';
    $ex=db()->prepare('SELECT id FROM upgrade_requests WHERE user_id=? AND status=?'); $ex->execute([$uid,'pending']);
    if ($ex->fetch()) { header('Location: index.php?page=profile&err=already_requested'); exit; }
    db()->prepare('INSERT INTO upgrade_requests(user_id,current_plan,request_plan,billing_cycle,message)VALUES(?,?,?,?,?)')->execute([$uid,$cur,$rplan,$cycle,$msg]);
    header('Location: index.php?page=profile&msg=upgrade_requested'); exit;
}
if ($action==='review_upgrade') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $rid=(int)($_POST['request_id']??0); $status=$_POST['status']??''; $note=trim($_POST['admin_note']??'');
    if (!in_array($status,['approved','rejected'])) { header('Location: index.php?page=subscriptions'); exit; }
    $r=db()->prepare('SELECT * FROM upgrade_requests WHERE id=?'); $r->execute([$rid]); $req=$r->fetch();
    if (!$req) { header('Location: index.php?page=subscriptions'); exit; }
    db()->prepare('UPDATE upgrade_requests SET status=?,admin_note=?,reviewed_by=?,reviewed_at=NOW() WHERE id=?')->execute([$status,$note,current_user()['id'],$rid]);
    if ($status==='approved') {
        db()->prepare('UPDATE subscriptions SET is_active=0 WHERE user_id=?')->execute([$req['user_id']]);
        $start=date('Y-m-d'); $cycle=$req['billing_cycle'];
        $ed=new DateTime($start); $cycle==='yearly'?$ed->modify('+1 year -1 day'):$ed->modify('+1 month -1 day');
        $end=$ed->format('Y-m-d');
        $amount=$req['request_plan']==='advance'?($cycle==='yearly'?PLAN_ADVANCE_YEARLY:PLAN_ADVANCE_MONTHLY):($cycle==='yearly'?PLAN_BASIC_YEARLY:PLAN_BASIC_MONTHLY);
        db()->prepare('INSERT INTO subscriptions(user_id,plan_type,billing_cycle,start_date,end_date,amount,is_active,notes,created_by)VALUES(?,?,?,?,?,?,1,?,?)')->execute([$req['user_id'],$req['request_plan'],$cycle,$start,$end,$amount,'Auto-approved upgrade',current_user()['id']]);
    }
    header('Location: index.php?page=subscriptions&msg=upgrade_reviewed'); exit;
}

// ── CHANGE PASSWORD ───────────────────────────────────────────
if ($action==='change_password') {
    require_login(); csrf_verify();
    $uid=current_user()['id']; $cur=$_POST['current_password']??''; $nw=$_POST['new_password']??''; $cf=$_POST['confirm_password']??'';
    $r=db()->prepare('SELECT password FROM users WHERE id=?'); $r->execute([$uid]); $row=$r->fetch();
    if (!$row||!password_verify($cur,$row['password'])) { header('Location: index.php?page=profile&err=wrongpw'); exit; }
    if (strlen($nw)<6) { header('Location: index.php?page=profile&err=short'); exit; }
    if ($nw!==$cf) { header('Location: index.php?page=profile&err=mismatch'); exit; }
    db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($nw,PASSWORD_BCRYPT,['cost'=>12]),$uid]);
    audit('change_password','users',$uid,'Password changed');
    header('Location: index.php?page=profile&msg=pwchanged'); exit;
}

// ── SETTINGS ─────────────────────────────────────────────────
if ($action==='save_settings') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $keys=['marquee_text','marquee_enabled','marquee_speed','footer_text',
           'mail_method','mail_host','mail_port','mail_user','mail_pass','mail_from','mail_from_name','mail_admin_email','mail_error_notify',
           'theme_primary','theme_bg','theme_surface','theme_border','theme_btn_text','theme_heading','theme_text','theme_sidebar_bg','theme_topbar_bg',
           // Bank / Payment settings
           'bank_name','bank_account_number','bank_branch','bank_ifsc','bank_account_type','bank_upi_id','bank_note'];
    foreach ($keys as $k) {
        $v = trim($_POST[$k]??'');
        if (in_array($k,['marquee_enabled','mail_error_notify'])) $v = isset($_POST[$k])?'1':'0';
        if ($k==='bank_ifsc') $v = strtoupper($v);
        set_setting($k,$v);
    }
    // Handle QR code removal
    if (isset($_POST['bank_qr_remove']) && $_POST['bank_qr_remove']==='1') {
        $oldQr = get_setting('bank_qr_path','');
        if ($oldQr) delete_upload($oldQr);
        set_setting('bank_qr_path','');
    }
    // Handle QR code upload
    if (!empty($_FILES['bank_qr_file']['name'])) {
        $qrFile = $_FILES['bank_qr_file'];
        if ($qrFile['error']===UPLOAD_ERR_OK) {
            $mime=(new finfo(FILEINFO_MIME_TYPE))->file($qrFile['tmp_name']);
            $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
            if (!isset($allowed[$mime])) {
                header('Location: index.php?page=settings&err='.urlencode('QR file must be JPG, PNG, or WEBP.'));
                exit;
            }
            if ($qrFile['size']>5*1024*1024) {
                header('Location: index.php?page=settings&err='.urlencode('QR image must be under 5MB.'));
                exit;
            }
            // Remove old QR if exists
            $oldQr = get_setting('bank_qr_path','');
            if ($oldQr) delete_upload($oldQr);
            $ext=$allowed[$mime];
            $safe=preg_replace('/[^a-z0-9_\-]/i','_',pathinfo($qrFile['name'],PATHINFO_FILENAME));
            $fname='qr_'.date('Ymd_His').'_'.$safe.'_'.bin2hex(random_bytes(4)).'.'.$ext;
            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR,0755,true);
            if (move_uploaded_file($qrFile['tmp_name'],UPLOAD_DIR.$fname)) {
                set_setting('bank_qr_path',UPLOAD_URL.$fname);
            }
        }
    }
    audit('update','app_settings',null,'Settings saved');
    header('Location: index.php?page=settings&msg=saved'); exit;
}

// ── SAVE PERMISSIONS ─────────────────────────────────────────
if ($action==='save_permissions') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $features = $_POST['features'] ?? [];
    foreach ($features as $feature) {
        $adm = isset($_POST['perm_admin'][$feature])   ? 1 : 0;
        $dev = isset($_POST['perm_developer'][$feature])? 1 : 0;
        $adv = isset($_POST['perm_adv'][$feature])      ? 1 : 0;
        $bas = isset($_POST['perm_bas'][$feature])      ? 1 : 0;
        // Admin always keeps full access
        db()->prepare('UPDATE permissions SET admin=?,developer=?,adv_viewer=?,bas_viewer=? WHERE feature=?')->execute([1,$dev,$adv,$bas,$feature]);
    }
    // Clear permission cache (force reload)
    audit('update','permissions',null,'Permissions updated');
    header('Location: index.php?page=permissions&msg=saved'); exit;
}

// ── SAVE MUTATION APPLICATION ─────────────────────────────────
if ($action==='save_mutation') {
    require_login(); csrf_verify();
    $uid = current_user()['id'];
    $aaraji  = trim($_POST['aaraji_number']??'');
    $village = (int)($_POST['village_id']??0)?:null;
    $txnNo   = trim($_POST['txn_number']??'');
    $txnDate = trim($_POST['txn_date']??'')?:null;
    $txnType = $_POST['txn_type']??'';
    $mutFee  = (float)get_setting('mutation_fee','500');

    if (!$aaraji) { header('Location: index.php?page=mutation_apply&err=missing'); exit; }

    // Upload registry
    try { $reg = handle_upload($_FILES['registry_file']??['error'=>UPLOAD_ERR_NO_FILE]); }
    catch (RuntimeException $e) { header('Location: index.php?page=mutation_apply&err=upload'); exit; }

    // Upload payment screenshot
    try { $pay = handle_upload($_FILES['payment_screenshot']??['error'=>UPLOAD_ERR_NO_FILE]); }
    catch (RuntimeException $e) { header('Location: index.php?page=mutation_apply&err=upload'); exit; }

    db()->prepare(
        'INSERT INTO mutation_applications
         (aaraji_number,village_id,registry_path,registry_name,txn_number,txn_date,txn_type,
          payment_screenshot_path,payment_screenshot_name,application_fee,submitted_by)
         VALUES(?,?,?,?,?,?,?,?,?,?,?)'
    )->execute([
        $aaraji,$village,
        $reg['path'],$reg['name'],
        $txnNo?:null,$txnDate,$txnType?:null,
        $pay['path'],$pay['name'],
        $mutFee,$uid
    ]);
    $mid = (int)db()->lastInsertId();

    // Insert transferees
    $names   = $_POST['trf_name']   ?? [];
    $genders = $_POST['trf_gender'] ?? [];
    $addrs   = $_POST['trf_address']?? [];
    $emails  = $_POST['trf_email']  ?? [];
    $aadhaars= $_POST['trf_aadhaar']?? [];
    $contacts= $_POST['trf_contact']?? [];
    $ins = db()->prepare(
        'INSERT INTO mutation_transferees
         (app_id,sort_order,full_name,gender,address,email,aadhaar_no,contact)
         VALUES(?,?,?,?,?,?,?,?)'
    );
    foreach ($names as $i=>$n) {
        if (trim($n)==='') continue;
        $ins->execute([
            $mid,$i+1,trim($n),
            $genders[$i]??'male',
            trim($addrs[$i]??''),
            trim($emails[$i]??'')?:null,
            trim(preg_replace('/\D/','',$aadhaars[$i]??''))?:null,
            trim($contacts[$i]??'')?:null,
        ]);
    }

    // Chain documents
    if (!empty($_FILES['chain_docs']['name'][0])) {
        try {
            $cus = handle_multiple_uploads($_FILES['chain_docs']);
            $cins = db()->prepare(
                'INSERT INTO mutation_chain_docs
                 (app_id,file_path,file_name,file_type,file_size,sort_order)
                 VALUES(?,?,?,?,?,?)'
            );
            foreach ($cus as $i=>$cf) {
                $cins->execute([$mid,$cf['path'],$cf['name'],$cf['type'],$cf['size'],$i+1]);
            }
        } catch (RuntimeException) {}
    }

    // Status log
    db()->prepare(
        'INSERT INTO mutation_status_log(app_id,old_status,new_status,note,changed_by)
         VALUES(?,?,?,?,?)'
    )->execute([$mid,null,'submitted','Application submitted',$uid]);

    audit('create','mutation_applications',$mid,'Mutation applied: '.$aaraji);
    header('Location: index.php?page=mutation&msg=mut_submitted'); exit;
}

// ── UPDATE MUTATION STATUS ────────────────────────────────────
if ($action==='update_mutation_status') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $mid    = (int)($_POST['mutation_id']??0);
    $status = $_POST['new_status']??'';
    $note   = trim($_POST['status_note']??'');
    $valid  = ['submitted','processing','demand_note_generated','demand_note_paid','assigned_to_user','disposed'];
    if (!$mid||!in_array($status,$valid)) { header('Location: index.php?page=mutation'); exit; }

    $r = db()->prepare('SELECT status FROM mutation_applications WHERE id=?');
    $r->execute([$mid]); $old = $r->fetchColumn();

    $sql = 'UPDATE mutation_applications SET status=?,status_note=?';
    $p   = [$status,$note?:null];

    // Optional assigned file upload
    if ($status==='assigned_to_user' && !empty($_FILES['assigned_file']['name'])) {
        try {
            $af = handle_upload($_FILES['assigned_file']);
            if ($af['path']) { $sql.=',assigned_file_path=?,assigned_file_name=?'; array_push($p,$af['path'],$af['name']); }
        } catch (RuntimeException) {}
    }

    db()->prepare($sql.' WHERE id=?')->execute(array_merge($p,[$mid]));
    db()->prepare(
        'INSERT INTO mutation_status_log(app_id,old_status,new_status,note,changed_by)VALUES(?,?,?,?,?)'
    )->execute([$mid,$old,$status,$note?:null,current_user()['id']]);

    audit('update','mutation_applications',$mid,"Status → $status");
    header('Location: index.php?page=mutation_view&id='.$mid.'&msg=saved'); exit;
}

// ── VERIFY MUTATION PAYMENT ───────────────────────────────────
if ($action==='verify_mutation_payment') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $mid = (int)($_POST['mutation_id']??0);
    db()->prepare('UPDATE mutation_applications SET payment_verified=1 WHERE id=?')->execute([$mid]);
    audit('update','mutation_applications',$mid,'Payment verified');
    header('Location: index.php?page=mutation_view&id='.$mid.'&msg=saved'); exit;
}

// ── DELETE MUTATION ───────────────────────────────────────────
if ($action==='delete_mutation') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    $mid = (int)($_POST['mutation_id']??0);
    $r   = db()->prepare('SELECT registry_path,payment_screenshot_path,assigned_file_path FROM mutation_applications WHERE id=?');
    $r->execute([$mid]); $row=$r->fetch();
    if ($row) {
        delete_upload($row['registry_path']);
        delete_upload($row['payment_screenshot_path']);
        delete_upload($row['assigned_file_path']);
        $docs = db()->prepare('SELECT file_path FROM mutation_chain_docs WHERE app_id=?');
        $docs->execute([$mid]);
        foreach ($docs->fetchAll() as $d) delete_upload($d['file_path']);
        db()->prepare('DELETE FROM mutation_applications WHERE id=?')->execute([$mid]);
    }
    audit('delete','mutation_applications',$mid,'Mutation deleted');
    header('Location: index.php?page=mutation&msg=deleted'); exit;
}

// ── TEST EMAIL ────────────────────────────────────────────────
if ($action==='test_email') {
    require_login(); csrf_verify(); if (!is_admin()) { http_response_code(403); die('Forbidden'); }
    require_once __DIR__.'/includes/mailer.php';
    $to = get_setting('mail_admin_email','');
    if (!$to) { header('Location: index.php?page=settings&err=no_email'); exit; }
    try {
        pms_mail($to,'PMS Test Email',email_template('Test Email','<p>This is a test email from PMS. Your email configuration is working correctly.</p>'));
        header('Location: index.php?page=settings&msg=email_sent');
    } catch (Throwable $e) {
        header('Location: index.php?page=settings&err='.urlencode($e->getMessage()));
    }
    exit;
}

// ── ROUTING ──────────────────────────────────────────────────
$page = $_GET['page'] ?? 'home';
 $validPages = ['home','login','add','edit','view','villages','profile','dlc','subscriptions','settings','permissions','approvals','reset_password','mutation','mutation_apply','mutation_view'];
if (!in_array($page,$validPages)) $page = 'home';
if (!in_array($page,['login','reset_password']) && !is_logged_in()) { header('Location: index.php?page=login'); exit; }
if ($page==='login' && is_logged_in()) { header('Location: index.php'); exit; }
if ($page==='approvals' && !is_admin()) { header('Location: index.php'); exit; }
if ($page==='permissions' && !is_admin()) { header('Location: index.php'); exit; }

// ── PAGE DATA ─────────────────────────────────────────────────
$pd = [];

if ($page==='home') {
    $q=trim($_GET['q']??'');$vf=(int)($_GET['village']??0);$tf=$_GET['type']??'';
    $tab=$_GET['tab']??'admin'; // 'admin' or 'developer'
    $pp=12;$cp=max(1,(int)($_GET['p']??1));$off=($cp-1)*$pp;

    $w=['1=1'];$b=[];
    
     $w[]="(p.is_developer_plan = 0 OR (p.is_developer_plan = 1 AND p.dev_status = 'approved'))"; 
    if ($q) { $w[]='(p.plan_name LIKE ? OR p.aaraji_number LIKE ? OR v.name LIKE ?)'; $b=array_merge($b,["%$q%","%$q%","%$q%"]); }
    if ($vf) { $w[]='p.village_id=?'; $b[]=$vf; }
    if ($tf==='image') $w[]="p.file_type='image'";
    elseif ($tf==='pdf') $w[]="p.file_type='pdf'";
    elseif ($tf==='location') $w[]="p.google_location IS NOT NULL AND p.google_location!=''";
    $ws=implode(' AND ',$w);

    $cs=db()->prepare("SELECT COUNT(*) FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id WHERE $ws");$cs->execute($b);$total=(int)$cs->fetchColumn();
    $st=db()->prepare("SELECT p.*,v.name AS village_name,v.tehsil,u.username AS dev_username,u.full_name AS dev_fullname FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id LEFT JOIN users u ON u.id=p.created_by WHERE $ws ORDER BY p.created_at DESC LIMIT $pp OFFSET $off");$st->execute($b);$plans=$st->fetchAll();

    $villagesAll=db()->query('SELECT * FROM revenue_villages ORDER BY name')->fetchAll();
    $stats=db()->query("SELECT COUNT(*) AS total,count(id) AS admin_plans,SUM(is_developer_plan=1 AND dev_status='approved') AS dev_plans,SUM(google_location IS NOT NULL AND google_location!='') AS located,COUNT(DISTINCT village_id) AS villages FROM plans")->fetch();
    $sponsoredPlans=[];
    try { $sp=db()->query("SELECT p.*,v.name AS village_name,u.full_name AS dev_fullname FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id LEFT JOIN users u ON u.id=p.created_by WHERE p.is_sponsored=1 AND p.is_developer_plan=1 AND p.dev_status='approved' ORDER BY p.updated_at DESC LIMIT 6"); $sponsoredPlans=$sp->fetchAll(); } catch(Throwable){}
    $pd=compact('plans','total','pp','cp','q','vf','tf','tab','villagesAll','stats','off','sponsoredPlans');
}
if ($page==='add'||$page==='edit') {
    $villagesAll=db()->query('SELECT * FROM revenue_villages ORDER BY name')->fetchAll();
    $editPlan=null;$chainDocs=[];
    if ($page==='edit') {
        $id=(int)($_GET['id']??0);$uid=current_user()['id'];
        $s=db()->prepare('SELECT p.*,v.name AS village_name FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id WHERE p.id=?');$s->execute([$id]);$editPlan=$s->fetch();
        if (!$editPlan||(!is_admin()&&$editPlan['created_by']!=$uid)){header('Location: index.php');exit;}
        $cd=db()->prepare('SELECT * FROM plan_chain_documents WHERE plan_id=? ORDER BY sort_order ASC');$cd->execute([$id]);$chainDocs=$cd->fetchAll();
    }
    $pd=compact('villagesAll','editPlan','chainDocs');
}
if ($page==='view') {
    $id=(int)($_GET['id']??0);$uid=current_user()['id'];
    $s=db()->prepare("SELECT p.*,v.name AS village_name,v.tehsil,v.district,u.username AS created_by_name,u.full_name AS dev_fullname FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id LEFT JOIN users u ON u.id=p.created_by WHERE p.id=? AND (p.is_developer_plan=0 OR p.dev_status='approved' OR p.created_by=? OR ?=1)");
    $isAdm=is_admin()?1:0;$s->execute([$id,$uid,$isAdm]);$plan=$s->fetch();
    if (!$plan){header('Location: index.php');exit;}
    $cd=db()->prepare('SELECT * FROM plan_chain_documents WHERE plan_id=? ORDER BY sort_order ASC');$cd->execute([$id]);$chainDocs=$cd->fetchAll();
    $planDlc=null;
    if ($plan['village_id']){$ds=db()->prepare('SELECT * FROM dlc_rates WHERE village_id=? ORDER BY financial_year DESC LIMIT 1');$ds->execute([$plan['village_id']]);$planDlc=$ds->fetch()?:null;}
    $pd=compact('plan','chainDocs','planDlc');
}
if ($page==='villages') {
    $villages=db()->query('SELECT v.*,COUNT(p.id) AS plan_count FROM revenue_villages v LEFT JOIN plans p ON p.village_id=v.id GROUP BY v.id ORDER BY v.name')->fetchAll();
    $pd=compact('villages');
}
if ($page==='dlc') {
    $villagesAll=db()->query('SELECT * FROM revenue_villages ORDER BY name')->fetchAll();
    $now=new DateTime();
    $fyDefault=($now->format('m')>=4)?$now->format('Y').'-'.substr(($now->format('Y')+1),2):($now->format('Y')-1).'-'.substr($now->format('Y'),2);
    $editDlc=null;$filterVid=(int)($_GET['village']??0);$filterFy=$_GET['fy']??$fyDefault;
    if (isset($_GET['edit_id'])){$s=db()->prepare('SELECT d.*,v.name AS village_name FROM dlc_rates d LEFT JOIN revenue_villages v ON v.id=d.village_id WHERE d.id=?');$s->execute([(int)$_GET['edit_id']]);$editDlc=$s->fetch()?:null;}
    $w=['1=1'];$b=[];
    if ($filterVid){$w[]='d.village_id=?';$b[]=$filterVid;}
    if ($filterFy){$w[]='d.financial_year=?';$b[]=$filterFy;}
    $ws=implode(' AND ',$w);
    $dl=db()->prepare("SELECT d.*,v.name AS village_name,v.tehsil FROM dlc_rates d LEFT JOIN revenue_villages v ON v.id=d.village_id WHERE $ws ORDER BY v.name,d.financial_year DESC");$dl->execute($b);$dlcList=$dl->fetchAll();
    $fyList=db()->query('SELECT DISTINCT financial_year FROM dlc_rates ORDER BY financial_year DESC')->fetchAll(PDO::FETCH_COLUMN);
    $pd=compact('villagesAll','editDlc','dlcList','filterVid','filterFy','fyList','fyDefault');
}
if ($page==='subscriptions') {
    if (!is_admin()){header('Location: index.php');exit;}
    $allUsers=db()->query('SELECT u.*,s.plan_type,s.billing_cycle,s.end_date,s.is_active AS sub_active FROM users u LEFT JOIN subscriptions s ON s.id=(SELECT id FROM subscriptions WHERE user_id=u.id AND is_active=1 AND end_date>=CURDATE() ORDER BY end_date DESC LIMIT 1) ORDER BY u.username')->fetchAll();
    $allSubs=db()->query('SELECT s.*,u.username,u.full_name FROM subscriptions s LEFT JOIN users u ON u.id=s.user_id ORDER BY s.created_at DESC')->fetchAll();
    $subStats=db()->query("SELECT COUNT(*) AS total,SUM(is_active=1 AND end_date>=CURDATE()) AS active,SUM(plan_type='basic' AND is_active=1 AND end_date>=CURDATE()) AS basic_count,SUM(plan_type='advance' AND is_active=1 AND end_date>=CURDATE()) AS advance_count FROM subscriptions")->fetch();
    $viewerUsers=db()->query('SELECT id,username,full_name,email,role FROM users ORDER BY username')->fetchAll();
    $upgradeReqs=db()->query("SELECT r.*,u.username,u.full_name FROM upgrade_requests r LEFT JOIN users u ON u.id=r.user_id WHERE r.status='pending' ORDER BY r.created_at DESC")->fetchAll();
    $pd=compact('allUsers','allSubs','subStats','viewerUsers','upgradeReqs');
}
if ($page==='profile') {
    $uid=current_user()['id'];$mySub=get_active_subscription();
    $myRequests=db()->prepare("SELECT * FROM upgrade_requests WHERE user_id=? ORDER BY created_at DESC LIMIT 5");$myRequests->execute([$uid]);$myRequests=$myRequests->fetchAll();
    // Load user email
    $uRow=db()->prepare('SELECT email FROM users WHERE id=?');$uRow->execute([$uid]);$uRow=$uRow->fetch();
    $userEmail=$uRow['email']??'';
    $pd=compact('mySub','myRequests','userEmail');
}
if ($page==='settings') {
    if (!is_admin()){header('Location: index.php');exit;}
    $settingsKeys=['marquee_text','marquee_enabled','marquee_speed','footer_text',
                   'mail_method','mail_host','mail_port','mail_user','mail_pass','mail_from','mail_from_name','mail_admin_email','mail_error_notify',
                   'theme_primary','theme_bg','theme_surface','theme_border','theme_btn_text','theme_heading','theme_text','theme_sidebar_bg','theme_topbar_bg',
                   // Bank / Payment settings
                   'bank_name','bank_account_number','bank_branch','bank_ifsc','bank_account_type','bank_upi_id','bank_note','bank_qr_path'];
    $S=[];
    foreach ($settingsKeys as $k) $S[$k]=get_setting($k,'');
    $pd=compact('S');
}
if ($page==='permissions') {
    if (!is_admin()){header('Location: index.php');exit;}
    $perms=db()->query("SELECT * FROM permissions ORDER BY `group`,id")->fetchAll();
    $groups=[];
    foreach ($perms as $p) { $groups[$p['group']][]=$p; }
    $pd=compact('perms','groups');
}
if ($page==='mutation') {
    $filterStatus = $_GET['status']??'';
    $w=['1=1'];$b=[];
    if ($filterStatus){$w[]='m.status=?';$b[]=$filterStatus;}
    $uid=current_user()['id'];
    if (!is_admin()) { $w[]='m.submitted_by=?'; $b[]=$uid; }
    $ws=implode(' AND ',$w);
    $st=db()->prepare(
        "SELECT m.*,v.name AS village_name,
                GROUP_CONCAT(t.full_name ORDER BY t.sort_order SEPARATOR '||') AS transferee_names
         FROM mutation_applications m
         LEFT JOIN revenue_villages v ON v.id=m.village_id
         LEFT JOIN mutation_transferees t ON t.app_id=m.id
         WHERE $ws
         GROUP BY m.id
         ORDER BY m.created_at DESC"
    );
    $st->execute($b);
    $mutations = $st->fetchAll();
    $pd = compact('mutations','filterStatus');
}

if ($page==='mutation_apply') {
    $villagesAll = db()->query('SELECT * FROM revenue_villages ORDER BY name')->fetchAll();
    $pd = compact('villagesAll');
}

if ($page==='mutation_view') {
    $mid = (int)($_GET['id']??0);
    $uid = current_user()['id'];
    $s = db()->prepare(
        "SELECT m.*,v.name AS village_name FROM mutation_applications m
         LEFT JOIN revenue_villages v ON v.id=m.village_id
         WHERE m.id=? AND (? OR m.submitted_by=?)"
    );
    $s->execute([$mid, is_admin()?1:0, $uid]);
    $mutApp = $s->fetch();
    if (!$mutApp) { header('Location: index.php?page=mutation'); exit; }
    $ts = db()->prepare('SELECT * FROM mutation_transferees WHERE app_id=? ORDER BY sort_order');
    $ts->execute([$mid]); $transferees = $ts->fetchAll();
    $cs = db()->prepare('SELECT * FROM mutation_chain_docs WHERE app_id=? ORDER BY sort_order');
    $cs->execute([$mid]); $mutChainDocs = $cs->fetchAll();
    $pd = compact('mutApp','transferees','mutChainDocs');
}
if ($page==='approvals') {
    $pending=db()->query("SELECT p.*,v.name AS village_name,u.username AS dev_name,u.full_name AS dev_fullname FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id LEFT JOIN users u ON u.id=p.created_by WHERE p.is_developer_plan=1 AND p.dev_status='pending' ORDER BY p.created_at ASC")->fetchAll();
    $approved=db()->query("SELECT p.*,v.name AS village_name,u.username AS dev_name FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id LEFT JOIN users u ON u.id=p.created_by WHERE p.is_developer_plan=1 AND p.dev_status='approved' ORDER BY p.created_at DESC LIMIT 60")->fetchAll();
    $pd=compact('pending','approved');
}
if ($page==='reset_password') {
    $token=trim($_GET['token']??'');
    $resetData=$token?validate_reset_token($token):null;
    $pd=compact('token','resetData');
}

extract($pd);
$user=current_user();$msg=$_GET['msg']??'';$err=$_GET['err']??'';$csrfTok=csrf_token();
$gMarqueeText=get_setting('marquee_text','');
$gMarqueeEnabled=get_setting('marquee_enabled','1');
$gMarqueeSpeed=get_setting('marquee_speed','60');
$gFooterText=get_setting('footer_text','PMS By Mingosoft Technologies');

function e(mixed $v): string { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
function fmtSqm(?float $v): string  { return $v!==null?'Rs.'.number_format($v,2):'--'; }
function fmtSqft(?float $v): string { return $v!==null?'Rs.'.number_format($v/10.76,2).'':'--'; }
function embedUrl(?string $url): string {
    if (!$url) return '';
    if (str_contains($url,'google.com/maps')||str_contains($url,'maps.google.com')){
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/',$url,$m)) return "https://maps.google.com/maps?q={$m[1]},{$m[2]}&z=15&output=embed";
        if (preg_match('/[?&]q=([^&]+)/',$url,$m)) return 'https://maps.google.com/maps?q='.urlencode(urldecode($m[1])).'&output=embed';
    }
    return '';
}
function currentFY() {
    $year  = (int)date('Y');
    $month = (int)date('m');

    if ($month >= 4) {
        return $year . '-' . substr(($year + 1), -2);
    } else {
        return ($year - 1) . '-' . substr($year, -2);
    }
}

include __DIR__.'/includes/layout.php';
