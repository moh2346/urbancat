const $=(s,r=document)=>r.querySelector(s), $$=(s,r)=>[...r.querySelectorAll(s)];
// header shadow - supports both legacy .site-header and new .uc-header
const h=$('#siteHeader') || $('.uc-header'); if(h){addEventListener('scroll',()=>{scrollY>8?h.classList.add('is-scrolled'):h.classList.remove('is-scrolled')},{passive:true}); if(scrollY>8) h.classList.add('is-scrolled');}
// mobile - new uc-header + legacy fallback
const burger=$('#ucMenuButton') || $('#burger'), nav=$('#mobile-navigation') || $('#mobileNav'), closeNav=$('#closeNav');
function openNav(){if(!burger||!nav) return; burger.setAttribute('aria-expanded','true'); nav.hidden=false; requestAnimationFrame(()=>nav.classList.add('is-open')); document.body.style.overflow='hidden';}
function closeNavFn(){if(!burger||!nav) return; const wasOpen=nav.classList.contains('is-open'); burger.setAttribute('aria-expanded','false'); nav.classList.remove('is-open'); document.body.style.overflow=''; if(wasOpen) burger.focus(); const delay=matchMedia('(prefers-reduced-motion: reduce)').matches?0:220; setTimeout(()=>{if(!nav.classList.contains('is-open')) nav.hidden=true;}, delay);}
if(burger&&nav){
  burger.addEventListener('click',()=>nav.classList.contains('is-open')?closeNavFn():openNav());
  closeNav&&closeNav.addEventListener('click',closeNavFn);
  // close when link inside mobile nav selected
  [...nav.querySelectorAll('a')].forEach(a=>a.addEventListener('click',()=>closeNavFn()));
  // Escape
  addEventListener('keydown',e=>{if(e.key==='Escape' && nav.classList.contains('is-open')){e.preventDefault(); closeNavFn();}});
  // click outside
  document.addEventListener('click',e=>{
    if(!nav.classList.contains('is-open')) return;
    const header=$('.uc-header')||$('#siteHeader');
    if(header && header.contains(e.target)) return;
    if(burger.contains(e.target)) return;
    if(nav.contains(e.target)) return;
    closeNavFn();
  });
}
// back to top
const btt=$('#backToTop'); if(btt){addEventListener('scroll',()=>{scrollY>600?btt.classList.add('is-visible'):btt.classList.remove('is-visible')},{passive:true}); btt.addEventListener('click',()=>scrollTo({top:0,behavior:'smooth'}))}
// reveal
if(!matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window){const o=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('is-visible');o.unobserve(e.target)}}),{threshold:.12}); $$('.reveal').forEach(el=>o.observe(el))}else $$('.reveal').forEach(el=>el.classList.add('is-visible'))
// fav (localStorage fallback)
const FAV='uc_favs'; const getF=()=>{try{return JSON.parse(localStorage.getItem(FAV)||'[]')}catch{return[]}}; const setF=a=>localStorage.setItem(FAV,JSON.stringify(a));
function updFav(){const c=$('#favCount'); if(c) c.textContent=String(getF().length)}
updFav(); $$('[data-fav]').forEach(b=>{const id=b.dataset.fav; if(getF().includes(id)) b.classList.add('is-fav'); b.addEventListener('click',()=>{let a=getF(); const i=a.indexOf(id); i>=0?a.splice(i,1):a.push(id); setF(a); updFav(); b.classList.toggle('is-fav',a.includes(id))})})
// cart count via localStorage fallback too? server handles
// gallery
const main=$('[data-gallery-main]'); $$('[data-thumb]').forEach(t=>t.addEventListener('click',()=>{if(!main)return; main.src=t.src; main.alt=t.alt; $$('[data-thumb]').forEach(x=>x.classList.remove('is-active')); t.classList.add('is-active')}))
// faq
$$('[data-faq-btn]').forEach(b=>b.addEventListener('click',()=>{const it=b.closest('.faq-item'); const o=it.classList.contains('is-open'); it.classList.toggle('is-open',!o); b.setAttribute('aria-expanded',String(!o))}))
// image preview admin
$$('[data-image-preview]').forEach(inp=>inp.addEventListener('change',()=>{const wrap=document.querySelector(inp.dataset.previewTarget||'#preview'); if(!wrap)return; wrap.innerHTML=''; [...inp.files].forEach(f=>{const u=URL.createObjectURL(f); const img=document.createElement('img'); img.src=u; img.style.cssText='width:90px;height:90px;object-fit:cover;border-radius:10px'; wrap.appendChild(img)})}))
// social gallery lightbox
const lightbox=$('#galleryLightbox'), lbImg=$('#lightboxImg'), lbClose=$('#lightboxClose'), lbPrev=$('#lightboxPrev'), lbNext=$('#lightboxNext');
const galleryItems=[...$$('.social-gallery__item img')];
let currentIdx=0;
function openLb(idx){currentIdx=idx; if(!lightbox||!lbImg) return; const src=galleryItems[idx].src; const alt=galleryItems[idx].alt; lbImg.src=src; lbImg.alt=alt; lightbox.classList.add('is-open'); lightbox.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; lbClose.focus();}
function closeLb(){if(!lightbox) return; lightbox.classList.remove('is-open'); lightbox.setAttribute('aria-hidden','true'); document.body.style.overflow='';}
function navLb(dir){currentIdx=(currentIdx+dir+galleryItems.length)%galleryItems.length; const src=galleryItems[currentIdx].src; const alt=galleryItems[currentIdx].alt; lbImg.src=src; lbImg.alt=alt;}
$$('.social-gallery__item').forEach((el,idx)=>{el.addEventListener('click',()=>openLb(idx)); el.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' ') {e.preventDefault(); openLb(idx)}})});
if(lbClose) lbClose.addEventListener('click',closeLb);
if(lbPrev) lbPrev.addEventListener('click',()=>navLb(-1));
if(lbNext) lbNext.addEventListener('click',()=>navLb(1));
if(lightbox) lightbox.addEventListener('click',e=>{if(e.target===lightbox) closeLb();});
addEventListener('keydown',e=>{
 if(!lightbox||!lightbox.classList.contains('is-open')) return;
 if(e.key==='Escape') closeLb();
 if(e.key==='ArrowLeft') navLb(-1);
 if(e.key==='ArrowRight') navLb(1);
});
