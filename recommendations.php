<?php
require 'config.php';
$skin=$_GET['skin']??'All';$allowed=['All','Dry','Oily','Combination','Balanced'];if(!in_array($skin,$allowed,true))$skin='All';
$sql="SELECT * FROM products WHERE active=1";$params=[];if($skin!=='All'){$sql.=" AND (skin_type='All' OR skin_type=?)";$params[]=$skin;}$sql.=" ORDER BY category, name";
$s=db()->prepare($sql);$s->execute($params);$products=$s->fetchAll();
$page_title='Routine recommendations';require 'includes/header.php';?>
<div class="container"><div class="page-heading"><div class="eyebrow">Routine library</div><h1>Recommendations, without the overwhelm.</h1><p>Explore sample product categories and general usage notes. Product names are examples for this academic demo; availability, ingredients and suitability should be verified before use.</p></div>
<div class="admin-links"><?php foreach($allowed as $type):?><a href="recommendations.php?skin=<?=urlencode($type)?>" style="<?=($skin===$type?'background:#bd766f;color:#fff':'')?>"><?=e($type==='All'?'All profiles':$type.' skin')?></a><?php endforeach;?></div>
<?php if($products):?><div class="product-grid"><?php foreach($products as $p):?><article class="product-card"><div class="product-art"><?= $p['category']==='Cleanser'?'◉':($p['category']==='Moisturiser'?'♧':($p['category']==='Sunscreen'?'☼':'✦')) ?></div><div class="product-body"><div class="product-meta"><?=e($p['category'])?> · <?=e($p['skin_type'])?></div><h3><?=e($p['name'])?></h3><p><?=e($p['description'])?></p><p class="helper"><?=e($p['usage_note'])?></p></div></article><?php endforeach;?></div><?php else:?><div class="empty-state">No recommendations have been added for this profile yet.</div><?php endif;?>
<div class="section"><div class="side-note"><h3>Product safety note</h3><p>Patch testing does not guarantee that a product will never irritate your skin. If you have a known allergy, persistent rash, severe acne or another concern, ask a qualified professional before changing your routine.</p></div></div></div>
<?php require 'includes/footer.php';?>
