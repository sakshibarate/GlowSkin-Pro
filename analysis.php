<?php
require 'config.php'; require_login();
$questions=[
 'skin_feel'=>['label'=>'How does your skin usually feel a few hours after cleansing?','options'=>['Tight or flaky','Comfortable','Shiny across most areas','Shiny in some areas, dry in others']],
 'main_concern'=>['label'=>'What would you most like to focus on?','options'=>['Dryness','Oiliness or visible pores','Uneven-looking tone','Breakouts','Dullness','Redness or sensitivity','No major concern']],
 'sensitivity'=>['label'=>'How does your skin react to new products?','options'=>['Often stings or becomes red','Sometimes reacts','Rarely reacts']],
 'texture'=>['label'=>'How would you describe your skin texture?','options'=>['Mostly smooth','Rough or flaky patches','Visible pores or shine','Mixed depending on area']],
 'routine'=>['label'=>'How often do you use sunscreen during daytime?','options'=>['Most days','Sometimes','Rarely or never']],
 'age_range'=>['label'=>'Which age range are you in?','options'=>['Under 18','18–24','25–34','35–44','45+']],
 'climate'=>['label'=>'Which environment best describes where you spend most of your time?','options'=>['Hot or humid','Hot or dry','Cold or dry','Mixed or temperate']],
 'routine_length'=>['label'=>'How would you describe your current skincare routine?','options'=>['No regular routine','One or two basic products','Three or more products','I am still figuring it out']],
 'breakout_frequency'=>['label'=>'How often do you notice breakouts?','options'=>['Rarely','Occasionally','Frequently','Prefer not to say']]
];
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $answers=[];
 foreach($questions as $key=>$q){$v=$_POST[$key]??'';if(!in_array($v,$q['options'],true)){$error='Please answer every question using the available options.';break;}$answers[$key]=$v;}
 if(!$error && empty($_POST['consent']))$error='Please confirm the image and educational-guidance consent.';
 $photoPath='';
 if(!$error){
  if(!isset($_FILES['skin_photo']) || $_FILES['skin_photo']['error']!==UPLOAD_ERR_OK){$error='Please upload a clear skin photo (JPG, PNG or WebP, up to 5 MB).';}
  elseif($_FILES['skin_photo']['size']>5*1024*1024){$error='The photo is larger than 5 MB. Please choose a smaller image.';}
  else {
   $tmp=$_FILES['skin_photo']['tmp_name']; $info=@getimagesize($tmp); $mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp);
   $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
   if(!$info || !isset($allowed[$mime])) $error='Unsupported image. Please upload a valid JPG, PNG or WebP photo.';
   elseif($info[0]<160 || $info[1]<160) $error='Please use a photo at least 160 × 160 pixels.';
   else {
    $dir=__DIR__.'/uploads/skin_photos'; if(!is_dir($dir) && !mkdir($dir,0750,true) && !is_dir($dir))$error='Photo storage could not be prepared. Contact the administrator.';
    else {$name=bin2hex(random_bytes(20)).'.'.$allowed[$mime]; if(!move_uploaded_file($tmp,$dir.'/'.$name))$error='The photo could not be saved. Please try again.';else $photoPath='uploads/skin_photos/'.$name;}
   }
  }
 }
 if(!$error){
  $feel=$answers['skin_feel'];$texture=$answers['texture'];
  $dry=($feel==='Tight or flaky'?2:0)+($texture==='Rough or flaky patches'?1:0);
  $oily=($feel==='Shiny across most areas'?2:0)+($texture==='Visible pores or shine'?1:0);
  $combo=($feel==='Shiny in some areas, dry in others'?3:0)+($texture==='Mixed depending on area'?2:0);
  $skin=$dry>$oily&&$dry>=$combo?'Dry':($oily>$dry&&$oily>=$combo?'Oily':(($combo>0 || ($dry===$oily && $dry>0))?'Combination':'Balanced'));
  $concern=$answers['main_concern'];$summary="Questionnaire suggests a $skin skin-feel profile. Main focus: $concern. Photo uploaded for reference; automated image diagnosis is not performed by this prototype.";
  try {$s=db()->prepare("INSERT INTO analyses(user_id,skin_type,main_concern,sensitivity,answers_json,summary,image_path) VALUES(?,?,?,?,?,?,?)");$s->execute([$_SESSION['user_id'],$skin,$concern,$answers['sensitivity'],json_encode($answers,JSON_UNESCAPED_UNICODE),$summary,$photoPath]);$id=(int)db()->lastInsertId();redirect('result.php?id='.$id);}
  catch(Throwable $ex){if($photoPath && is_file(__DIR__.'/'.$photoPath))@unlink(__DIR__.'/'.$photoPath);$error='Could not save your assessment. Make sure the database migration in README.md has been applied.';}
 }
}
$page_title='Skin profile assessment';require 'includes/header.php';?>
<div class="container"><div class="page-heading"><div class="eyebrow">Personal skin profile · Photo + questionnaire</div><h1>Build your skin-care profile.</h1><p>Upload a clear photo and answer the questions below. GlowSkin Pro combines your questionnaire answers with a saved photo reference in one report. This academic prototype does not diagnose skin conditions or claim to detect acne, pigmentation or other conditions from an image.</p></div>
<?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<div class="form-layout"><form class="form-card" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<section class="upload-panel"><div class="eyebrow">Step 01 · Photo upload</div><h2>Add a skin photo</h2><p class="helper">Use a clear, well-lit photo without filters or heavy makeup where possible. JPG, PNG or WebP · maximum 5 MB. The image is saved with your account assessment.</p><label class="upload-drop" for="skin_photo"><span class="upload-symbol">＋</span><strong>Choose a photo</strong><span class="helper">Click to browse your device</span><input id="skin_photo" name="skin_photo" type="file" accept="image/jpeg,image/png,image/webp" required></label><div id="photo-preview-wrap" class="photo-preview-wrap" hidden><img id="photo-preview" alt="Selected skin photo preview"><button class="btn btn-small" type="button" id="remove-photo">Remove photo</button></div><p id="photo-name" class="helper" aria-live="polite"></p></section>
<div class="eyebrow question-section-label">Step 02 · Your questionnaire</div>
<?php foreach($questions as $key=>$q):?><fieldset class="field question-field" style="border:0;padding:0"><legend><?=e($q['label'])?></legend><div class="radio-list"><?php foreach($q['options'] as $opt):?><label class="radio-option"><input type="radio" name="<?=e($key)?>" value="<?=e($opt)?>" required <?=((($_POST[$key]??'')===$opt)?'checked':'')?>> <?=e($opt)?></label><?php endforeach;?></div></fieldset><?php endforeach;?>
<div class="field check-row"><input type="checkbox" name="consent" value="1" required><label class="legal">I consent to saving this photo with my assessment. I understand this provides educational guidance, does not diagnose a condition, and I should not upload a photo of another person without permission.</label></div><button class="btn btn-primary" type="submit">Create my skin profile →</button></form>
<aside class="side-note"><div class="eyebrow">A better assessment starts with context</div><h3>What your report includes</h3><ul><li>Your questionnaire-based skin-feel profile and selected concerns.</li><li>Your uploaded photo saved as a reference for your personal report.</li><li>A starter routine and general product-category suggestions.</li><li>Your assessment stored in your account history.</li><li>Clear limitations: no clinical diagnosis or validated AI image recognition is claimed.</li></ul><p class="legal">Photo privacy: use your own image, avoid including other people, and only test with photos you are comfortable storing in this local academic demo.</p></aside></div></div>
<?php require 'includes/footer.php';?>