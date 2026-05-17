<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/upload.php';
require_once __DIR__.'/includes/excel.php';
require_once __DIR__.'/includes/mailer.php';
session_start_safe();
pms_register_error_handler();

$action=$_POST['action']??'';

// ── AUTH ──────────────────────────────────────────────────────
if ($action==='login'){
    $ok=login(trim($_POST['username']??''),$_POST['password']??'');
    header($ok?'Location: index.php':'Location: index.php?page=login&err=1');exit;
}
if ($action==='logout'){logout();}

// ── FORGOT PASSWORD REQUEST ───────────────────────────────────
if ($action==='forgot_password'){
    $email=trim($_POST['email']??'');
    if($email){
        try{
            $s=db()->prepare('SELECT id,username,full_name FROM users WHERE email=? AND is_active=1 LIMIT 1');
            $s->execute([$email]);$user=$s->fetch();
            if($user){
                $token=bin2hex(random_bytes(32));
                $expires=date('Y-m-d H:i:s',time()+3600);
                db()->prepare('INSERT INTO password_resets(user_id,email,token,expires_at)VALUES(?,?,?,?)')->execute([$user['id'],$email,$token,$expires]);
                $link=(isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php?page=reset_password&token='.$token;
                mail_password_reset($email,$user['full_name']?:$user['username'],$link);
            }
        }catch(Throwable $e){mail_error('Forgot Password',$e->getMessage());}
    }
    header('Location: index.php?page=login&msg=reset_sent');exit;
}

// ── RESET PASSWORD ────────────────────────────────────────────
if ($action==='reset_password'){
    $token=trim($_POST['token']??'');$nw=$_POST['new_password']??'';$cf=$_POST['confirm_password']??'';
    if(!$token||strlen($nw)<6||$nw!==$cf){header('Location: index.php?page=reset_password&token='.urlencode($token).'&err=invalid');exit;}
    try{
        $s=db()->prepare('SELECT * FROM password_resets WHERE token=? AND used=0 AND expires_at>NOW() LIMIT 1');
        $s->execute([$token]);$row=$s->fetch();
        if(!$row){header('Location: index.php?page=login&err=token_expired');exit;}
        db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($nw,PASSWORD_BCRYPT,['cost'=>12]),$row['user_id']]);
        db()->prepare('UPDATE password_resets SET used=1 WHERE id=?')->execute([$row['id']]);
        audit('reset_password','users',$row['user_id'],'Password reset via email');
    }catch(Throwable $e){mail_error('Reset Password',$e->getMessage());}
    header('Location: index.php?page=login&msg=pw_reset_ok');exit;
}

// ── PLAN (UNIFIED — admin & developer) ───────────────────────
if ($action==='save_plan'){
    require_login();csrf_verify();
    $role=current_user()['role'];
    if($role==='viewer'){http_response_code(403);die('Forbidden');}
    $uid=current_user()['id'];
    $id=(int)($_POST['plan_id']??0);
    $isDev=($role==='developer');
    $name=trim($_POST['plan_name']??'');$aaraji=trim($_POST['aaraji_number']??'');
    $village=(int)($_POST['village_id']??0)?:null;$loc=trim($_POST['google_location']??'');$notes=trim($_POST['notes']??'');
    $contact=trim($_POST['contact_number']??'');
    $pu=trim($_POST['price_unit']??'sq.ft');
    $pn=fn($k)=>isset($_POST[$k])&&$_POST[$k]!==''&&is_numeric($_POST[$k])?(float)$_POST[$k]:null;
    if(!$name||!$aaraji){header('Location: index.php?page='.($id?"edit&id=$id":'add').'&err=missing');exit;}
    try{$up=handle_upload($_FILES['plan_file']??['error'=>UPLOAD_ERR_NO_FILE]);}
    catch(RuntimeException $e){header('Location: index.php?page='.($id?"edit&id=$id":'add').'&err='.urlencode($e->getMessage()));exit;}
    try{$am=handle_upload($_FILES['approved_map']??['error'=>UPLOAD_ERR_NO_FILE]);}
    catch(RuntimeException $e){header('Location: index.php?page='.($id?"edit&id=$id":'add').'&err='.urlencode($e->getMessage()));exit;}

    $devStatus='na';
    if($isDev) $devStatus='pending';

    if($id>0){
        $ex=db()->prepare('SELECT file_path,approved_map_path,created_by,is_developer_plan FROM plans WHERE id=?');$ex->execute([$id]);$row=$ex->fetch();
        if(!$row||(!(is_admin()||$row['created_by']==$uid))){http_response_code(403);die('Forbidden');}
        if($up['path']){delete_upload($row['file_path']);}
        if($am['path']){delete_upload($row['approved_map_path']);}
        $sql='UPDATE plans SET plan_name=?,aaraji_number=?,village_id=?,google_location=?,notes=?,contact_number=?,price_30ft=?,price_40ft=?,price_60ft=?,price_80ft=?,price_100ft=?,price_highway=?,price_unit=?,brokerage_rate=?,brokerage_notes=?,updated_by=?';
        $p=[$name,$aaraji,$village,$loc,$notes,$contact,$pn('price_30ft'),$pn('price_40ft'),$pn('price_60ft'),$pn('price_80ft'),$pn('price_100ft'),$pn('price_highway'),$pu,$pn('brokerage_rate'),trim($_POST['brokerage_notes']??''),$uid];
        if($up['path']){$sql.=',file_path=?,file_name=?,file_type=?';array_push($p,$up['path'],$up['name'],$up['type']);}
        if($am['path']){$sql.=',approved_map_path=?,approved_map_name=?,approved_map_type=?';array_push($p,$am['path'],$am['name'],$am['type']);}
        if($isDev&&!is_admin()){$sql.=',dev_status=\'pending\'';}// re-submit
        db()->prepare($sql.' WHERE id=?')->execute(array_merge($p,[$id]));
        // chain docs
        if(!empty($_FILES['chain_docs']['name'][0])){
            try{$cu=handle_multiple_uploads($_FILES['chain_docs']);
                $mo=(int)db()->query("SELECT COALESCE(MAX(sort_order),0) FROM plan_chain_documents WHERE plan_id=$id")->fetchColumn();
                $ins=db()->prepare('INSERT INTO plan_chain_documents(plan_id,file_path,file_name,file_type,file_size,sort_order,uploaded_by)VALUES(?,?,?,?,?,?,?)');
                foreach($cu as $i=>$cf)$ins->execute([$id,$cf['path'],$cf['name'],$cf['type'],$cf['size'],$mo+$i+1,$uid]);
            }catch(RuntimeException){}
        }
        audit('update','plans',$id,"Updated: $name");
        header('Location: index.php?msg=updated');
    } else {
        $is_dp=$isDev?1:0;
        $dstat=$isDev?'pending':'na';
        db()->prepare('INSERT INTO plans(plan_name,aaraji_number,village_id,google_location,file_path,file_name,file_type,notes,contact_number,approved_map_path,approved_map_name,approved_map_type,price_30ft,price_40ft,price_60ft,price_80ft,price_100ft,price_highway,price_unit,brokerage_rate,brokerage_notes,is_developer_plan,dev_status,created_by,updated_by)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute([$name,$aaraji,$village,$loc,$up['path'],$up['name'],$up['type'],$notes,$contact,$am['path'],$am['name'],$am['type'],$pn('price_30ft'),$pn('price_40ft'),$pn('price_60ft'),$pn('price_80ft'),$pn('price_100ft'),$pn('price_highway'),$pu,$pn('brokerage_rate'),trim($_POST['brokerage_notes']??''),$is_dp,$dstat,$uid,$uid]);
        $nid=(int)db()->lastInsertId();
        if(!empty($_FILES['chain_docs']['name'][0])){
            try{$cu=handle_multiple_uploads($_FILES['chain_docs']);
                $ins=db()->prepare('INSERT INTO plan_chain_documents(plan_id,file_path,file_name,file_type,file_size,sort_order,uploaded_by)VALUES(?,?,?,?,?,?,?)');
                foreach($cu as $i=>$cf)$ins->execute([$nid,$cf['path'],$cf['name'],$cf['type'],$cf['size'],$i+1,$uid]);
            }catch(RuntimeException){}
        }
        audit('create','plans',$nid,"Created: $name".($isDev?' [DEV]':''));
        header('Location: index.php?msg=created');
    }
    exit;
}

// ── DELETE PLAN ───────────────────────────────────────────────
if ($action==='delete_plan'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $id=(int)($_POST['plan_id']??0);
    $r=db()->prepare('SELECT file_path,approved_map_path,plan_name FROM plans WHERE id=?');$r->execute([$id]);$p=$r->fetch();
    if($p){delete_upload($p['file_path']);delete_upload($p['approved_map_path']);db()->prepare('DELETE FROM plans WHERE id=?')->execute([$id]);audit('delete','plans',$id,'Deleted: '.$p['plan_name']);}
    header('Location: index.php?msg=deleted');exit;
}

// ── APPROVE/REJECT DEVELOPER PLAN ────────────────────────────
if ($action==='review_dev_plan'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $id=(int)($_POST['plan_id']??0);$status=$_POST['status']??'';$note=trim($_POST['admin_note']??'');
    if(!in_array($status,['approved','rejected'])){header('Location: index.php?page=dev_approvals');exit;}
    $extra='';$params=[$status,$note,$id];
    if($status==='approved'){$extra=',approved_by=?,approved_at=NOW()';$params=[$status,$note,current_user()['id'],$id];}
    db()->prepare("UPDATE plans SET dev_status=?,dev_admin_note=?$extra WHERE id=?")->execute($params);
    audit('update','plans',$id,"Dev plan $status");
    header('Location: index.php?page=dev_approvals&msg=reviewed');exit;
}

// ── TOGGLE SPONSORED ─────────────────────────────────────────
if ($action==='toggle_sponsored'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $id=(int)($_POST['plan_id']??0);$lbl=trim($_POST['sponsored_label']??'Sponsored');
    $r=db()->prepare('SELECT is_sponsored FROM plans WHERE id=?');$r->execute([$id]);$row=$r->fetch();
    if($row){db()->prepare('UPDATE plans SET is_sponsored=?,sponsored_label=? WHERE id=?')->execute([$row['is_sponsored']?0:1,$lbl,$id]);}
    header('Location: index.php?page=dev_approvals&msg=updated');exit;
}

// ── DELETE CHAIN DOC ──────────────────────────────────────────
if ($action==='delete_chain_doc'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $did=(int)($_POST['doc_id']??0);$pid=(int)($_POST['plan_id']??0);
    $s=db()->prepare('SELECT file_path FROM plan_chain_documents WHERE id=? AND plan_id=?');$s->execute([$did,$pid]);$d=$s->fetch();
    if($d){delete_upload($d['file_path']);db()->prepare('DELETE FROM plan_chain_documents WHERE id=?')->execute([$did]);}
    header('Location: index.php?page=edit&id='.$pid.'&msg=chain_deleted');exit;
}

// ── VILLAGES ──────────────────────────────────────────────────
if ($action==='save_village'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $name=trim($_POST['village_name']??'');$teh=trim($_POST['tehsil']??'');$dist=trim($_POST['district']??'');$vid=(int)($_POST['village_id']??0);
    if(!$name){header('Location: index.php?page=villages&err=missing');exit;}
    if($vid>0) db()->prepare('UPDATE revenue_villages SET name=?,tehsil=?,district=? WHERE id=?')->execute([$name,$teh,$dist,$vid]);
    else db()->prepare('INSERT INTO revenue_villages(name,tehsil,district)VALUES(?,?,?)')->execute([$name,$teh,$dist]);
    header('Location: index.php?page=villages&msg=saved');exit;
}
if ($action==='delete_village'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $vid=(int)($_POST['village_id']??0);
    db()->prepare('UPDATE plans SET village_id=NULL WHERE village_id=?')->execute([$vid]);
    db()->prepare('DELETE FROM revenue_villages WHERE id=?')->execute([$vid]);
    header('Location: index.php?page=villages&msg=deleted');exit;
}

// ── DLC EXPORT ────────────────────────────────────────────────
if (($_GET['action']??'')==='export_dlc'){
    require_login();
    $vid=(int)($_GET['village']??0);$fy=trim($_GET['fy']??'');
    $w=['1=1'];$b=[];
    if($vid){$w[]='d.village_id=?';$b[]=$vid;}
    if($fy){$w[]='d.financial_year=?';$b[]=$fy;}
    $ws=implode(' AND ',$w);
    $st=db()->prepare("SELECT d.*,v.name AS village_name,v.tehsil,v.district FROM dlc_rates d LEFT JOIN revenue_villages v ON v.id=d.village_id WHERE $ws ORDER BY v.name,d.financial_year DESC");
    $st->execute($b);$rows=$st->fetchAll();
    $xlsx=dlc_export_xlsx($rows);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="DLC_Rates_'.date('Ymd').'.xlsx"');
    header('Content-Length: '.strlen($xlsx));
    echo $xlsx;exit;
}
// ── DLC IMPORT ────────────────────────────────────────────────
if ($action==='import_dlc_csv'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $f=$_FILES['dlc_csv']??null;
    if(!$f||$f['error']!==UPLOAD_ERR_OK){header('Location: index.php?page=dlc&err=nofile');exit;}
    $rows=dlc_import_csv($f['tmp_name']);$uid=current_user()['id'];$ok=0;$skip=0;
    foreach($rows as $row){
        $r=dlc_map_csv_row($row);
        $vn=$r['village_name']??'';$fy=$r['financial_year']??'';$ef=$r['effective_from']??'';
        if(!$vn||!$fy||!$ef){$skip++;continue;}
        $vs=db()->prepare('SELECT id FROM revenue_villages WHERE name=? LIMIT 1');$vs->execute([$vn]);$vr=$vs->fetch();
        if(!$vr){$skip++;continue;}
        $num=fn($k)=>isset($r[$k])&&$r[$k]!==''?(float)$r[$k]:null;
        try{db()->prepare('INSERT INTO dlc_rates(village_id,financial_year,effective_from,road_30ft,road_40ft,road_60ft,road_80ft,road_100ft,near_highway,notes,created_by)VALUES(?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE effective_from=VALUES(effective_from),road_30ft=VALUES(road_30ft),road_40ft=VALUES(road_40ft),road_60ft=VALUES(road_60ft),road_80ft=VALUES(road_80ft),road_100ft=VALUES(road_100ft),near_highway=VALUES(near_highway),notes=VALUES(notes)')->execute([$vr['id'],$fy,$ef,$num('road_30ft'),$num('road_40ft'),$num('road_60ft'),$num('road_80ft'),$num('road_100ft'),$num('near_highway'),$r['notes']??'',$uid]);$ok++;}catch(PDOException){$skip++;}
    }
    header("Location: index.php?page=dlc&msg=imported&ok=$ok&skip=$skip");exit;
}
// ── DLC SAVE/DELETE ───────────────────────────────────────────
if ($action==='save_dlc'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $did=(int)($_POST['dlc_id']??0);$vid=(int)($_POST['village_id']??0);
    $fy=trim($_POST['financial_year']??'');$ef=trim($_POST['effective_from']??'');
    $num=fn($k)=>isset($_POST[$k])&&$_POST[$k]!==''&&is_numeric($_POST[$k])?(float)$_POST[$k]:null;
    $notes=trim($_POST['notes']??'');$uid=current_user()['id'];
    if(!$vid||!$fy||!$ef){header('Location: index.php?page=dlc&err=missing');exit;}
    if($did>0) db()->prepare('UPDATE dlc_rates SET village_id=?,financial_year=?,effective_from=?,road_30ft=?,road_40ft=?,road_60ft=?,road_80ft=?,road_100ft=?,near_highway=?,notes=? WHERE id=?')->execute([$vid,$fy,$ef,$num('road_30ft'),$num('road_40ft'),$num('road_60ft'),$num('road_80ft'),$num('road_100ft'),$num('near_highway'),$notes,$did]);
    else db()->prepare('INSERT INTO dlc_rates(village_id,financial_year,effective_from,road_30ft,road_40ft,road_60ft,road_80ft,road_100ft,near_highway,notes,created_by)VALUES(?,?,?,?,?,?,?,?,?,?,?)')->execute([$vid,$fy,$ef,$num('road_30ft'),$num('road_40ft'),$num('road_60ft'),$num('road_80ft'),$num('road_100ft'),$num('near_highway'),$notes,$uid]);
    header('Location: index.php?page=dlc&msg=dlc_saved');exit;
}
if ($action==='delete_dlc'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    db()->prepare('DELETE FROM dlc_rates WHERE id=?')->execute([(int)($_POST['dlc_id']??0)]);
    header('Location: index.php?page=dlc&msg=dlc_deleted');exit;
}

// ── SUBSCRIPTIONS ─────────────────────────────────────────────
if ($action==='save_subscription'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $sid=(int)($_POST['sub_id']??0);$uid2=(int)($_POST['sub_user_id']??0);
    $plan=$_POST['plan_type']??'basic';$cycle=$_POST['billing_cycle']??'monthly';
    $start=trim($_POST['start_date']??'');$end=trim($_POST['end_date']??'');
    $amount=$_POST['amount']!==''?(float)$_POST['amount']:null;$notes=trim($_POST['notes']??'');$uid=current_user()['id'];
    if(!$uid2||!$start||!$end){header('Location: index.php?page=subscriptions&err=missing');exit;}
    if($sid>0) db()->prepare('UPDATE subscriptions SET user_id=?,plan_type=?,billing_cycle=?,start_date=?,end_date=?,amount=?,notes=? WHERE id=?')->execute([$uid2,$plan,$cycle,$start,$end,$amount,$notes,$sid]);
    else db()->prepare('INSERT INTO subscriptions(user_id,plan_type,billing_cycle,start_date,end_date,amount,is_active,notes,created_by)VALUES(?,?,?,?,?,?,1,?,?)')->execute([$uid2,$plan,$cycle,$start,$end,$amount,$notes,$uid]);
    header('Location: index.php?page=subscriptions&msg=sub_saved');exit;
}
if ($action==='toggle_subscription'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $sid=(int)($_POST['sub_id']??0);
    $r=db()->prepare('SELECT is_active FROM subscriptions WHERE id=?');$r->execute([$sid]);$row=$r->fetch();
    if($row) db()->prepare('UPDATE subscriptions SET is_active=? WHERE id=?')->execute([$row['is_active']?0:1,$sid]);
    header('Location: index.php?page=subscriptions&msg=sub_saved');exit;
}
if ($action==='delete_subscription'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    db()->prepare('DELETE FROM subscriptions WHERE id=?')->execute([(int)($_POST['sub_id']??0)]);
    header('Location: index.php?page=subscriptions&msg=sub_deleted');exit;
}
if ($action==='save_user'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $uname=trim($_POST['new_username']??'');$fname=trim($_POST['new_fullname']??'');
    $email=trim($_POST['new_email']??'');$pwd=$_POST['new_password']??'';
    $roleIn=$_POST['new_role']??'viewer';
    $role=in_array($roleIn,['admin','viewer','developer'])?$roleIn:'viewer';
    if(!$uname||strlen($pwd)<6){header('Location: index.php?page=subscriptions&err=userdata');exit;}
    if(!preg_match('/^[a-zA-Z0-9_]+$/',$uname)){header('Location: index.php?page=subscriptions&err=username');exit;}
    $chk=db()->prepare('SELECT id FROM users WHERE username=?');$chk->execute([$uname]);
    if($chk->fetch()){header('Location: index.php?page=subscriptions&err=exists');exit;}
    db()->prepare('INSERT INTO users(username,password,full_name,email,role)VALUES(?,?,?,?,?)')->execute([$uname,password_hash($pwd,PASSWORD_BCRYPT,['cost'=>12]),$fname,$email,$role]);
    header('Location: index.php?page=subscriptions&msg=user_created');exit;
}
if ($action==='request_upgrade'){
    require_login();csrf_verify();
    $uid=current_user()['id'];
    $rplan=$_POST['request_plan']??'advance';$cycle=$_POST['billing_cycle']??'monthly';$msg=trim($_POST['message']??'');
    $sub=get_active_subscription();$cur=$sub?$sub['plan_type']:'none';
    $ex=db()->prepare('SELECT id FROM upgrade_requests WHERE user_id=? AND status=?');$ex->execute([$uid,'pending']);
    if($ex->fetch()){header('Location: index.php?page=profile&err=already_requested');exit;}
    db()->prepare('INSERT INTO upgrade_requests(user_id,current_plan,request_plan,billing_cycle,message)VALUES(?,?,?,?,?)')->execute([$uid,$cur,$rplan,$cycle,$msg]);
    header('Location: index.php?page=profile&msg=upgrade_requested');exit;
}
if ($action==='review_upgrade'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $rid=(int)($_POST['request_id']??0);$status=$_POST['status']??'';$note=trim($_POST['admin_note']??'');
    if(!in_array($status,['approved','rejected'])){header('Location: index.php?page=subscriptions');exit;}
    $r=db()->prepare('SELECT * FROM upgrade_requests WHERE id=?');$r->execute([$rid]);$req=$r->fetch();
    if(!$req){header('Location: index.php?page=subscriptions');exit;}
    db()->prepare('UPDATE upgrade_requests SET status=?,admin_note=?,reviewed_by=?,reviewed_at=NOW() WHERE id=?')->execute([$status,$note,current_user()['id'],$rid]);
    if($status==='approved'){
        db()->prepare('UPDATE subscriptions SET is_active=0 WHERE user_id=?')->execute([$req['user_id']]);
        $start=date('Y-m-d');$cycle=$req['billing_cycle'];
        $ed=new DateTime($start);$cycle==='yearly'?$ed->modify('+1 year -1 day'):$ed->modify('+1 month -1 day');
        $end=$ed->format('Y-m-d');
        $amount=$req['request_plan']==='advance'?($cycle==='yearly'?PLAN_ADVANCE_YEARLY:PLAN_ADVANCE_MONTHLY):($cycle==='yearly'?PLAN_BASIC_YEARLY:PLAN_BASIC_MONTHLY);
        db()->prepare('INSERT INTO subscriptions(user_id,plan_type,billing_cycle,start_date,end_date,amount,is_active,notes,created_by)VALUES(?,?,?,?,?,?,1,?,?)')->execute([$req['user_id'],$req['request_plan'],$cycle,$start,$end,$amount,'Auto-approved',current_user()['id']]);
    }
    header('Location: index.php?page=subscriptions&msg=upgrade_reviewed');exit;
}

// ── CHANGE PASSWORD ───────────────────────────────────────────
if ($action==='change_password'){
    require_login();csrf_verify();
    $uid=current_user()['id'];$cur=$_POST['current_password']??'';$nw=$_POST['new_password']??'';$cf=$_POST['confirm_password']??'';
    $r=db()->prepare('SELECT password FROM users WHERE id=?');$r->execute([$uid]);$row=$r->fetch();
    if(!$row||!password_verify($cur,$row['password'])){header('Location: index.php?page=profile&err=wrongpw');exit;}
    if(strlen($nw)<6){header('Location: index.php?page=profile&err=short');exit;}
    if($nw!==$cf){header('Location: index.php?page=profile&err=mismatch');exit;}
    db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($nw,PASSWORD_BCRYPT,['cost'=>12]),$uid]);
    audit('change_password','users',$uid,'Password changed');
    header('Location: index.php?page=profile&msg=pwchanged');exit;
}

// ── UPDATE PROFILE EMAIL ──────────────────────────────────────
if ($action==='update_email'){
    require_login();csrf_verify();
    $uid=current_user()['id'];$email=trim($_POST['email']??'');
    db()->prepare('UPDATE users SET email=? WHERE id=?')->execute([$email,$uid]);
    header('Location: index.php?page=profile&msg=email_saved');exit;
}

// ── SETTINGS ─────────────────────────────────────────────────
if ($action==='save_settings'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $keys=['marquee_text','marquee_enabled','marquee_speed','footer_text',
           'mail_method','mail_host','mail_port','mail_user','mail_pass','mail_from','mail_from_name','mail_admin_email','mail_error_notify',
           'theme_primary','theme_bg','theme_surface','theme_border','theme_btn_text','theme_heading','theme_text','theme_sidebar_bg','theme_topbar_bg'];
    foreach($keys as $k){
        $v=trim($_POST[$k]??'');
        if(in_array($k,['marquee_enabled','mail_error_notify'])) $v=isset($_POST[$k])?'1':'0';
        set_setting($k,$v);
    }
    audit('update','app_settings',null,'Settings updated');
    header('Location: index.php?page=settings&msg=saved');exit;
}

// ── SAVE PERMISSIONS ─────────────────────────────────────────
if ($action==='save_permissions'){
    require_login();csrf_verify();if(!is_admin()){http_response_code(403);die('Forbidden');}
    $features=$_POST['features']??[];
    $stmt=db()->prepare('UPDATE permissions SET admin=?,developer=?,adv_viewer=?,bas_viewer=? WHERE feature=?');
    foreach($features as $feat=>$cols){
        $stmt->execute([
            isset($cols['admin'])?1:0,
            isset($cols['developer'])?1:0,
            isset($cols['adv_viewer'])?1:0,
            isset($cols['bas_viewer'])?1:0,
            $feat
        ]);
    }
    audit('update','permissions',null,'Permission matrix updated');
    header('Location: index.php?page=permissions&msg=saved');exit;
}

// ── ROUTING ──────────────────────────────────────────────────
$page=$_GET['page']??'home';
$validPages=['home','login','add','edit','view','villages','profile','dlc','subscriptions','settings','dev_approvals','permissions','forgot_password','reset_password'];
if(!in_array($page,$validPages)) $page='home';
if(!in_array($page,['login','forgot_password','reset_password'])&&!is_logged_in()){header('Location: index.php?page=login');exit;}
if($page==='login'&&is_logged_in()){header('Location: index.php');exit;}
if($page==='dev_approvals'&&!is_admin()){header('Location: index.php');exit;}
if($page==='settings'&&!is_admin()){header('Location: index.php');exit;}

// ── PAGE DATA ─────────────────────────────────────────────────
$pd=[];

if($page==='home'){
    $q=trim($_GET['q']??'');$vf=(int)($_GET['village']??0);$tf=$_GET['type']??'';
    $pp=12;$cp=max(1,(int)($_GET['p']??1));$off=($cp-1)*$pp;
    $role=is_logged_in()?current_user()['role']:'';
    $w=['1=1'];$b=[];
    // Viewers see: admin plans always; dev plans only if approved
    if($role==='viewer') $w[]="(p.is_developer_plan=0 OR p.dev_status='approved')";
    elseif($role==='developer') $w[]="(p.is_developer_plan=0 OR p.dev_status='approved' OR p.created_by=".current_user()['id'].")";
    if($q){$w[]='(p.plan_name LIKE ? OR p.aaraji_number LIKE ? OR v.name LIKE ?)';$b=array_merge($b,["%$q%","%$q%","%$q%"]);}
    if($vf){$w[]='p.village_id=?';$b[]=$vf;}
    if($tf==='image'){$w[]="p.file_type='image'";}elseif($tf==='pdf'){$w[]="p.file_type='pdf'";}elseif($tf==='location'){$w[]="p.google_location IS NOT NULL AND p.google_location!=''";}elseif($tf==='dev'){$w[]="p.is_developer_plan=1";}
    $ws=implode(' AND ',$w);
    $cs=db()->prepare("SELECT COUNT(*) FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id WHERE $ws");$cs->execute($b);$total=(int)$cs->fetchColumn();
    $st=db()->prepare("SELECT p.*,v.name AS village_name,v.tehsil FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id WHERE $ws ORDER BY p.created_at DESC LIMIT $pp OFFSET $off");$st->execute($b);$plans=$st->fetchAll();
    $villagesAll=db()->query('SELECT * FROM revenue_villages ORDER BY name')->fetchAll();
    $stats=db()->query("SELECT COUNT(*) AS total,SUM(file_type='image') AS images,SUM(file_type='pdf') AS pdfs,SUM(google_location IS NOT NULL AND google_location!='') AS located,COUNT(DISTINCT village_id) AS villages,SUM(is_developer_plan=1 AND dev_status='approved') AS dev_plans FROM plans")->fetch();
    $sponsoredPlans=[];
    try{$sp=db()->query("SELECT p.*,v.name AS village_name,u.full_name AS dev_fullname FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id LEFT JOIN users u ON u.id=p.created_by WHERE p.is_sponsored=1 AND p.dev_status='approved' ORDER BY p.updated_at DESC LIMIT 6");$sponsoredPlans=$sp->fetchAll();}catch(Throwable){}
    $pd=compact('plans','total','pp','cp','q','vf','tf','villagesAll','stats','off','sponsoredPlans');
}
if($page==='add'||$page==='edit'){
    if(is_viewer()){header('Location: index.php');exit;}
    $villagesAll=db()->query('SELECT * FROM revenue_villages ORDER BY name')->fetchAll();
    $editPlan=null;$chainDocs=[];
    if($page==='edit'){
        $id=(int)($_GET['id']??0);$uid=current_user()['id'];
        $s=db()->prepare('SELECT p.*,v.name AS village_name FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id WHERE p.id=?');$s->execute([$id]);$editPlan=$s->fetch();
        if(!$editPlan||(!(is_admin()||$editPlan['created_by']==$uid))){header('Location: index.php');exit;}
        $cd=db()->prepare('SELECT * FROM plan_chain_documents WHERE plan_id=? ORDER BY sort_order ASC');$cd->execute([$id]);$chainDocs=$cd->fetchAll();
    }
    $pd=compact('villagesAll','editPlan','chainDocs');
}
if($page==='view'){
    $id=(int)($_GET['id']??0);$uid=is_logged_in()?current_user()['id']:0;
    $s=db()->prepare('SELECT p.*,v.name AS village_name,v.tehsil,v.district,u.username AS created_by_name,u.full_name AS dev_fullname FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id LEFT JOIN users u ON u.id=p.created_by WHERE p.id=?');$s->execute([$id]);$plan=$s->fetch();
    if(!$plan){header('Location: index.php');exit;}
    $cd=db()->prepare('SELECT * FROM plan_chain_documents WHERE plan_id=? ORDER BY sort_order ASC');$cd->execute([$id]);$chainDocs=$cd->fetchAll();
    $planDlc=null;
    if($plan['village_id']){$ds=db()->prepare('SELECT * FROM dlc_rates WHERE village_id=? ORDER BY financial_year DESC LIMIT 1');$ds->execute([$plan['village_id']]);$planDlc=$ds->fetch()?:null;}
    $pd=compact('plan','chainDocs','planDlc');
}
if($page==='villages'){
    $villages=db()->query('SELECT v.*,COUNT(p.id) AS plan_count FROM revenue_villages v LEFT JOIN plans p ON p.village_id=v.id GROUP BY v.id ORDER BY v.name')->fetchAll();
    $pd=compact('villages');
}
if($page==='dlc'){
    $villagesAll=db()->query('SELECT * FROM revenue_villages ORDER BY name')->fetchAll();
    $now=new DateTime();$fyDefault=($now->format('m')>=4)?$now->format('Y').'-'.substr(($now->format('Y')+1),2):($now->format('Y')-1).'-'.substr($now->format('Y'),2);
    $editDlc=null;$filterVid=(int)($_GET['village']??0);$filterFy=$_GET['fy']??$fyDefault;
    if(isset($_GET['edit_id'])){$s=db()->prepare('SELECT d.*,v.name AS village_name FROM dlc_rates d LEFT JOIN revenue_villages v ON v.id=d.village_id WHERE d.id=?');$s->execute([(int)$_GET['edit_id']]);$editDlc=$s->fetch()?:null;}
    $w=['1=1'];$b=[];if($filterVid){$w[]='d.village_id=?';$b[]=$filterVid;}if($filterFy){$w[]='d.financial_year=?';$b[]=$filterFy;}
    $ws=implode(' AND ',$w);
    $dl=db()->prepare("SELECT d.*,v.name AS village_name,v.tehsil FROM dlc_rates d LEFT JOIN revenue_villages v ON v.id=d.village_id WHERE $ws ORDER BY v.name,d.financial_year DESC");$dl->execute($b);$dlcList=$dl->fetchAll();
    $fyList=db()->query('SELECT DISTINCT financial_year FROM dlc_rates ORDER BY financial_year DESC')->fetchAll(PDO::FETCH_COLUMN);
    $pd=compact('villagesAll','editDlc','dlcList','filterVid','filterFy','fyList','fyDefault');
}
if($page==='subscriptions'){
    if(!is_admin()){header('Location: index.php');exit;}
    $allUsers=db()->query('SELECT u.*,s.plan_type,s.billing_cycle,s.end_date,s.is_active AS sub_active FROM users u LEFT JOIN subscriptions s ON s.id=(SELECT id FROM subscriptions WHERE user_id=u.id AND is_active=1 AND end_date>=CURDATE() ORDER BY end_date DESC LIMIT 1) ORDER BY u.username')->fetchAll();
    $allSubs=db()->query('SELECT s.*,u.username,u.full_name FROM subscriptions s LEFT JOIN users u ON u.id=s.user_id ORDER BY s.created_at DESC')->fetchAll();
    $subStats=db()->query("SELECT COUNT(*) AS total,SUM(is_active=1 AND end_date>=CURDATE()) AS active,SUM(plan_type='basic' AND is_active=1 AND end_date>=CURDATE()) AS basic_count,SUM(plan_type='advance' AND is_active=1 AND end_date>=CURDATE()) AS advance_count FROM subscriptions")->fetch();
    $viewerUsers=db()->query('SELECT id,username,full_name FROM users ORDER BY username')->fetchAll();
    $upgradeReqs=db()->query("SELECT r.*,u.username,u.full_name FROM upgrade_requests r LEFT JOIN users u ON u.id=r.user_id WHERE r.status='pending' ORDER BY r.created_at DESC")->fetchAll();
    $pd=compact('allUsers','allSubs','subStats','viewerUsers','upgradeReqs');
}
if($page==='profile'){
    $uid=current_user()['id'];$mySub=get_active_subscription();
    $myRequests=db()->prepare("SELECT * FROM upgrade_requests WHERE user_id=? ORDER BY created_at DESC LIMIT 5");$myRequests->execute([$uid]);$myRequests=$myRequests->fetchAll();
    $userRow=db()->prepare('SELECT email FROM users WHERE id=?');$userRow->execute([$uid]);$userRow=$userRow->fetch();
    $pd=compact('mySub','myRequests','userRow');
}
if($page==='settings'){
    $sMarqueeText=get_setting('marquee_text','');$sMarqueeEnabled=get_setting('marquee_enabled','1');$sMarqueeSpeed=get_setting('marquee_speed','60');$sFooterText=get_setting('footer_text','');
    $sMail=['method'=>get_setting('mail_method','smtp'),'host'=>get_setting('mail_host',''),'port'=>get_setting('mail_port','587'),'user'=>get_setting('mail_user',''),'pass'=>get_setting('mail_pass',''),'from'=>get_setting('mail_from',''),'from_name'=>get_setting('mail_from_name',APP_BRAND),'admin_email'=>get_setting('mail_admin_email',''),'error_notify'=>get_setting('mail_error_notify','0')];
    $sTheme=get_theme_vars();
    $pd=compact('sMarqueeText','sMarqueeEnabled','sMarqueeSpeed','sFooterText','sMail','sTheme');
}
if($page==='dev_approvals'){
    $pending=db()->query("SELECT p.*,v.name AS village_name,u.username AS dev_name,u.full_name AS dev_fullname FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id LEFT JOIN users u ON u.id=p.created_by WHERE p.is_developer_plan=1 AND p.dev_status='pending' ORDER BY p.created_at ASC")->fetchAll();
    $approved=db()->query("SELECT p.*,v.name AS village_name,u.username AS dev_name FROM plans p LEFT JOIN revenue_villages v ON v.id=p.village_id LEFT JOIN users u ON u.id=p.created_by WHERE p.is_developer_plan=1 AND p.dev_status='approved' ORDER BY p.created_at DESC LIMIT 50")->fetchAll();
    $pendingCount=count($pending);
    $pd=compact('pending','approved','pendingCount');
}
if($page==='permissions'){
    $permRows=[];
    try{$permRows=db()->query('SELECT * FROM permissions ORDER BY `group`,id')->fetchAll();}catch(Throwable){}
    $pd=compact('permRows');
}
if($page==='forgot_password'||$page==='reset_password'){
    $resetToken=trim($_GET['token']??'');
    $pd=compact('resetToken');
}

extract($pd);
$user=is_logged_in()?current_user():['id'=>0,'username'=>'','full_name'=>'','role'=>''];
$msg=$_GET['msg']??'';$err=$_GET['err']??'';$csrfTok=is_logged_in()?csrf_token():'';
$themeVars=get_theme_vars();

function e(mixed $v): string { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
function fmtSqm(?float $v): string { return $v!==null?'Rs.'.number_format($v,2):'--'; }
function fmtSqft(?float $v): string { return $v!==null?'Rs.'.number_format($v/10.76,2):'--'; }
function embedUrl(?string $url): string {
    if(!$url) return '';
    if(str_contains($url,'google.com/maps')||str_contains($url,'maps.google.com')){
        if(preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/',$url,$m)) return "https://maps.google.com/maps?q={$m[1]},{$m[2]}&z=15&output=embed";
        if(preg_match('/[?&]q=([^&]+)/',$url,$m)) return 'https://maps.google.com/maps?q='.urlencode(urldecode($m[1])).'&output=embed';
    }
    return '';
}
function currentFY(): string {
    $now=new DateTime();
    return ($now->format('m')>=4)?$now->format('Y').'-'.substr(($now->format('Y')+1),2):($now->format('Y')-1).'-'.substr($now->format('Y'),2);
}

include __DIR__.'/includes/layout.php';