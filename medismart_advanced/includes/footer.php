<?php
// includes/footer.php
?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<script>
// Minimal FAQ chatbot
document.addEventListener('DOMContentLoaded', () => {
  const el = document.querySelector('#miniChat');
  if (!el) return;
  const input = el.querySelector('input');
  const log = el.querySelector('.chat-log');
  const btn = el.querySelector('button');

  function append(sender, text) {
    const p = document.createElement('div');
    p.className = 'mb-1';
    p.textContent = sender + ': ' + text;
    log.appendChild(p);
    log.scrollTop = log.scrollHeight; // why: keep latest message visible
  }
  function reply(q) {
    let a = 'Please open a support ticket for a detailed response.';
    const s = q.toLowerCase();
    if (s.includes('fever')) a = 'Common fever reducers: Paracetamol, Ibuprofen. Always consult a physician.';
    if (s.includes('headache')) a = 'For mild headache: Disprin, Ibuprofen. Hydration helps.';
    if (s.includes('allergy')) a = 'For seasonal allergies: Cetirizine, Loratadine. Avoid triggers.';
    if (s.includes('delivery')) a = 'Delivery within 2-3 days for verified orders.';
    return a;
  }
  btn.addEventListener('click', () => {
    const q = input.value.trim();
    if (!q) return;
    append('You', q);
    input.value = '';
    append('Bot', reply(q));
  });
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { btn.click(); }
  });
});
</script>

</body>
</html>
