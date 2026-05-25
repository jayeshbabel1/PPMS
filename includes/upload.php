<?php
require_once __DIR__.'/config.php';
function handle_upload(array $file): array {
    if ($file['error']===UPLOAD_ERR_NO_FILE) return ['path'=>null,'name'=>null,'type'=>null];
    if ($file['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Upload error: '.$file['error']);
    if ($file['size']>MAX_FILE_MB*1024*1024) throw new RuntimeException('File exceeds '.MAX_FILE_MB.'MB.');
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $allowed=['image/jpeg'=>['jpg','image'],'image/png'=>['png','image'],'image/webp'=>['webp','image'],'application/pdf'=>['pdf','pdf']];
    if (!isset($allowed[$mime])) throw new RuntimeException('File type not allowed: '.$mime);
    [$ext,$cat]=$allowed[$mime];
    $safe=preg_replace('/[^a-z0-9_\-]/i','_',pathinfo($file['name'],PATHINFO_FILENAME));
    $fname=date('Ymd_His').'_'.$safe.'_'.bin2hex(random_bytes(4)).'.'.$ext;
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR,0755,true);
    if (!move_uploaded_file($file['tmp_name'],UPLOAD_DIR.$fname)) throw new RuntimeException('Failed to save file.');
    return ['path'=>UPLOAD_URL.$fname,'name'=>$file['name'],'type'=>$cat];
}
function handle_multiple_uploads(array $ff): array {
    $out=[]; $n=is_array($ff['name'])?count($ff['name']):0;
    for ($i=0;$i<$n;$i++) {
        $f=['name'=>$ff['name'][$i],'tmp_name'=>$ff['tmp_name'][$i],'error'=>$ff['error'][$i],'size'=>$ff['size'][$i]];
        if ($f['error']===UPLOAD_ERR_NO_FILE) continue;
        if ($f['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Upload error on "'.$f['name'].'"');
        if ($f['size']>MAX_FILE_MB*1024*1024) throw new RuntimeException('"'.$f['name'].'" exceeds '.MAX_FILE_MB.'MB.');
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        $allowed=['image/jpeg'=>['jpg','image'],'image/png'=>['png','image'],'image/webp'=>['webp','image'],'application/pdf'=>['pdf','pdf']];
        if (!isset($allowed[$mime])) throw new RuntimeException('"'.$f['name'].'" type not allowed.');
        [$ext,$cat]=$allowed[$mime];
        $safe=preg_replace('/[^a-z0-9_\-]/i','_',pathinfo($f['name'],PATHINFO_FILENAME));
        $fname='ch_'.date('Ymd_His').'_'.$safe.'_'.bin2hex(random_bytes(4)).'.'.$ext;
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR,0755,true);
        if (!move_uploaded_file($f['tmp_name'],UPLOAD_DIR.$fname)) throw new RuntimeException('Failed to save "'.$f['name'].'"');
        $out[]=['path'=>UPLOAD_URL.$fname,'name'=>$f['name'],'type'=>$cat,'size'=>$f['size']];
    }
    return $out;
}
function delete_upload(?string $path): void {
    if ($path && file_exists(__DIR__.'/../'.$path)) @unlink(__DIR__.'/../'.$path);
}