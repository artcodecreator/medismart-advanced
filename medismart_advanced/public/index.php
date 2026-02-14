<?php require_once __DIR__ . '/../includes/header.php'; ?>

<style>
/* Glass + gradient accents reused */
.hero-wrap { position: relative; border-radius: 22px; overflow: hidden; box-shadow: 0 18px 50px rgba(0,0,0,.18); }
.hero-bg { background: linear-gradient(135deg, rgba(99,102,241,.85), rgba(6,182,212,.85)); min-height: 360px; }
.hero-glass { background: rgba(255,255,255,.14); backdrop-filter: blur(10px) saturate(140%); -webkit-backdrop-filter: blur(10px) saturate(140%); border: 1px solid rgba(255,255,255,.35); border-radius: 18px; }
.feature-card { background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.35); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border-radius: 18px; transition: transform .15s ease, box-shadow .2s ease; will-change: transform; }
.feature-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,.15); }
.hero-illus { border-radius: 16px; overflow: hidden; border: 1px solid rgba(255,255,255,.35); box-shadow: 0 10px 30px rgba(0,0,0,.18); }

/* Typewriter headline */
.typewrite { border-right: 2px solid rgba(255,255,255,.85); white-space: nowrap; overflow: hidden; }

/* Counters */
.kpi { text-align:center; padding:1rem; border-radius:16px; background: rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.35); }
.kpi .num { font-size: clamp(1.8rem, 3vw, 2.4rem); font-weight: 700; }

/* Reveal on scroll */
.reveal { opacity: 0; transform: translateY(12px); transition: opacity .5s ease, transform .5s ease; }
.reveal.in { opacity: 1; transform: none; }

/* Button ripple */
.btn { position: relative; overflow: hidden; }
.btn::after { content:""; position:absolute; inset:auto; width:0; height:0; border-radius:50%; background:rgba(255,255,255,.35); transform:translate(-50%,-50%); pointer-events:none; }
.btn:active::after { width:200px; height:200px; left:var(--x); top:var(--y); transition: width .35s ease, height .35s ease; }

/* Tilt hint */
.tilt-shadow { box-shadow: 0 12px 30px rgba(0,0,0,.12); }

/* Chatbot glass */
#miniChat.card { border-radius: 18px; }
#miniChat .chat-log { background: rgba(255,255,255,.45); border: 1px solid rgba(255,255,255,.5); border-radius: 12px; padding:.5rem .75rem; }
</style>

<div class="hero-wrap mb-4">
  <div class="hero-bg p-4 p-lg-5 text-white">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <div class="hero-glass p-4 p-lg-5 reveal">
          <h1 class="display-6 fw-bold mb-2">MediSmart Online Pharmacy – VUID-YourVUIDHere</h1>
          <p class="lead mb-3">
            <span class="typewrite" id="tw" data-phrases='["Secure checkout","AI-assisted shopping","Verified prescriptions","Fast delivery"]'></span>
          </p>
          <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary btn-lg" href="<?php echo BASE_URL; ?>/public/browse.php">Start Shopping</a>
            <a class="btn btn-outline-primary btn-lg" href="<?php echo BASE_URL; ?>/public/register.php">Register</a>
            <a class="btn btn-secondary btn-lg" href="<?php echo BASE_URL; ?>/admin/login.php">Admin</a>
          </div>
        </div>
      </div>
      <div class="col-lg-5 reveal">
        <div class="hero-illus tilt-shadow" data-tilt>
          <img src="<?php echo BASE_URL; ?>/public/assets/med/hero_medicine.svg"
               alt="Medicines and pills on a table" class="w-100 h-100" style="object-fit: cover; max-height: 380px;">
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Popular Medicines -->
<div class="row g-3 mt-2 reveal">
  <div class="col-md-4">
    <div class="card feature-card h-100" data-tilt>
      <img src="<?php echo BASE_URL; ?>/public/assets/med/pills_pain.svg" class="card-img-top" alt="Pain relief tablets">
      <div class="card-body">
        <h6 class="mb-1">Pain Relief</h6>
        <div class="text-muted small mb-2">Tablets and fast relief packs.</div>
        <a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/public/browse.php?category=Pain%20Reliever">Browse</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card feature-card h-100" data-tilt>
      <img src="<?php echo BASE_URL; ?>/public/assets/med/antibiotic_capsules.svg" class="card-img-top" alt="Antibiotic capsules">
      <div class="card-body">
        <h6 class="mb-1">Antibiotics</h6>
        <div class="text-muted small mb-2">Prescription-only selections.</div>
        <a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/public/browse.php?category=Antibiotic">Browse</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card feature-card h-100" data-tilt>
      <img src="<?php echo BASE_URL; ?>/public/assets/med/allergy_bottle.svg" class="card-img-top" alt="Allergy medicine">
      <div class="card-body">
        <h6 class="mb-1">Allergy Relief</h6>
        <div class="text-muted small mb-2">Fast relief for seasonal allergies.</div>
        <a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/public/browse.php?category=Allergy%20Relief">Browse</a>
      </div>
    </div>
  </div>
</div>

<!-- KPIs -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3 reveal"><div class="kpi"><div class="num" data-count="5000">0</div><div class="small text-muted">Happy Customers</div></div></div>
  <div class="col-6 col-md-3 reveal"><div class="kpi"><div class="num" data-count="1200">0</div><div class="small text-muted">Products</div></div></div>
  <div class="col-6 col-md-3 reveal"><div class="kpi"><div class="num" data-count="98">0</div><div class="small text-muted">Cities Served</div></div></div>
  <div class="col-6 col-md-3 reveal"><div class="kpi"><div class="num" data-count="24">0</div><div class="small text-muted">Support Hrs</div></div></div>
</div>

<!-- Features -->
<div class="row g-3 mb-4">
  <div class="col-md-4 reveal">
    <div class="feature-card p-3 h-100" data-tilt>
      <h5 class="mb-1">Verified Medicines</h5>
      <div class="text-muted small">Prescription upload and admin verification.</div>
    </div>
  </div>
  <div class="col-md-4 reveal">
    <div class="feature-card p-3 h-100" data-tilt>
      <h5 class="mb-1">Secure Checkout</h5>
      <div class="text-muted small">Card (demo) or COD with CSRF + hashing.</div>
    </div>
  </div>
  <div class="col-md-4 reveal">
    <div class="feature-card p-3 h-100" data-tilt>
      <h5 class="mb-1">Smart Suggestions</h5>
      <div class="text-muted small">AI-style recommendations and risk scoring.</div>
    </div>
  </div>
</div>

<!-- Chatbot -->
<div id="miniChat" class="card shadow-sm hero-glass reveal">
  <div class="card-header">Mini Health Chatbot</div>
  <div class="card-body">
    <div class="chat-log small mb-2" style="height:160px; overflow:auto;"></div>
    <div class="input-group">
      <input class="form-control" placeholder="Ask about fever, allergies, delivery...">
      <button class="btn btn-primary" type="button">Ask</button>
    </div>
  </div>
</div>

<script>
// Button ripple
document.addEventListener('click', e => {
  const b = e.target.closest('.btn'); if (!b) return;
  const r = b.getBoundingClientRect();
  b.style.setProperty('--x', (e.clientX - r.left) + 'px');
  b.style.setProperty('--y', (e.clientY - r.top) + 'px');
});

// Typewriter
(function(){
  const el = document.getElementById('tw'); if (!el) return;
  const phrases = JSON.parse(el.dataset.phrases || '[]'); let i=0, j=0, erase=false;
  function tick(){
    if (!phrases.length) return;
    const full = phrases[i];
    el.textContent = full.slice(0, j) + (j%2===0?'':''); // caret handled by CSS
    if (!erase) { j++; if (j>full.length+6) erase=true; }
    else { j--; if (j===0){ erase=false; i=(i+1)%phrases.length; } }
    setTimeout(tick, erase ? 35 : 70);
  }
  tick();
})();

// Tilt on hover (no external lib)
function addTilt(el){
  const max = 8;
  el.addEventListener('mousemove', (e) => {
    const r = el.getBoundingClientRect();
    const x = (e.clientX - r.left) / r.width * 2 - 1;
    const y = (e.clientY - r.top) / r.height * 2 - 1;
    el.style.transform = `rotateX(${-y*max}deg) rotateY(${x*max}deg) translateZ(0)`;
  });
  el.addEventListener('mouseleave', () => {
    el.style.transform = 'rotateX(0) rotateY(0)';
  });
}
document.querySelectorAll('[data-tilt]').forEach(addTilt);

// Reveal on scroll
const io = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('in'); });
},{ threshold:.1 });
document.querySelectorAll('.reveal').forEach(n => io.observe(n));

// Counters
function animateCount(el){
  const target = parseInt(el.dataset.count || '0',10); let cur = 0;
  const step = Math.max(1, Math.floor(target/80));
  const t = setInterval(() => {
    cur += step; if (cur >= target){ cur = target; clearInterval(t); }
    el.textContent = cur.toLocaleString();
  }, 16);
}
const counterIO = new IntersectionObserver(es => {
  es.forEach(e => { if (e.isIntersecting){ animateCount(e.target); counterIO.unobserve(e.target); } });
});
document.querySelectorAll('.kpi .num').forEach(n => counterIO.observe(n));

// Chatbot enhancements
document.addEventListener('DOMContentLoaded', () => {
  const el = document.querySelector('#miniChat'); if (!el) return;
  const input = el.querySelector('input'); const log = el.querySelector('.chat-log'); const btn = el.querySelector('button');
  function append(sender, text){ const p=document.createElement('div'); p.className='mb-1'; p.textContent=sender+': '+text; log.appendChild(p); log.scrollTop=log.scrollHeight; }
  function reply(q){
    let a='Please open a support ticket for a detailed response.';
    const s=q.toLowerCase();
    if (s.includes('fever')) a='Common fever reducers: Paracetamol, Ibuprofen. Always consult a physician.';
    if (s.includes('headache')) a='For mild headache: Disprin, Ibuprofen. Hydration helps.';
    if (s.includes('allergy')) a='For seasonal allergies: Cetirizine, Loratadine. Avoid triggers.';
    if (s.includes('delivery')) a='Delivery within 2-3 days for verified orders.';
    return a;
  }
  btn.addEventListener('click',()=>{ const q=input.value.trim(); if(!q) return; append('You', q); input.value=''; setTimeout(()=>append('Bot', reply(q)), 150); });
  input.addEventListener('keydown', e => { if (e.key==='Enter') btn.click(); });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
