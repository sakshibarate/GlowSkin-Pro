document.querySelectorAll('[data-confirm]').forEach(function(el){el.addEventListener('click',function(e){if(!confirm(el.getAttribute('data-confirm')||'Are you sure?'))e.preventDefault();});});
(function(){
 const input=document.getElementById('skin_photo'); if(!input)return;
 const wrap=document.getElementById('photo-preview-wrap'), img=document.getElementById('photo-preview'), name=document.getElementById('photo-name'), remove=document.getElementById('remove-photo');
 input.addEventListener('change',function(){const file=input.files&&input.files[0]; if(!file){wrap.hidden=true;name.textContent='';return;}
 const allowed=['image/jpeg','image/png','image/webp']; if(!allowed.includes(file.type)||file.size>5*1024*1024){alert('Choose a JPG, PNG or WebP image up to 5 MB.');input.value='';wrap.hidden=true;name.textContent='';return;}
 const reader=new FileReader();reader.onload=function(e){img.src=e.target.result;wrap.hidden=false;};reader.readAsDataURL(file);name.textContent=file.name+' · '+(file.size/1024/1024).toFixed(2)+' MB';});
 if(remove)remove.addEventListener('click',function(){input.value='';img.removeAttribute('src');wrap.hidden=true;name.textContent='A photo is required before submitting.';});
})();
