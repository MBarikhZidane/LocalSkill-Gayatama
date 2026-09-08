/* Progressive enhancement only; HTML forms and field names are the backend contract. */
const params = new URLSearchParams(location.search);
const catalog = {
  "design-01": ["Design consultation", "A one-hour design consultation with a mood board and visual direction for your campus project.", "BK", "Barikh K.", "Design Faculty \u00b7 0.8 km away", "4.9 (18 jobs)", "150.000"],
  "tutoring-01": ["Peer tutoring", "A one-hour peer study session to review course concepts and prepare for exams.", "AR", "Aditya R.", "Library zone \u00b7 0.4 km away", "5.0 (24 jobs)", "75.000"],
  "slides-01": ["Presentation slides design", "A ten-slide presentation with clear layouts, consistent typography, and one revision.", "JS", "Jamie S.", "Library zone \u00b7 0.6 km away", "4.9 (8 jobs)", "50.000"],
  "photography-01": ["Graduation and event photography", "A one-hour graduation or campus event shoot with 20 edited photos.", "NP", "Nadia P.", "Arts building \u00b7 1.0 km away", "4.9 (12 jobs)", "200.000"],
  "graphic-01": ["Graphic design", "A campus event poster and matching social media graphic with one revision.", "CM", "Clara M.", "Design studio \u00b7 1.2 km away", "4.8 (32 jobs)", "100.000"],
  "photocopy-01": ["Photocopy services", "Print or photocopy essays, journals, and thesis pages: 20 A4 black-and-white printed sides per package.", "RS", "Rina S.", "Library zone - 0.3 km away", "4.9 (16 jobs)", "10.000"]
};
const search = document.querySelector('#skill-search');
if (search) {
  const query = (params.get('q') || '').trim(), category = params.get('category') || '';
  search.elements.q.value = query;
  search.elements.category.value = category;
  let count = 0;
  document.querySelectorAll('[data-service-id]').forEach(card => {
    card.hidden = !card.textContent.toLowerCase().includes(query.toLowerCase()) || Boolean(category && card.dataset.category !== category);
    if (!card.hidden) count++;
  });
  document.querySelector('#result-count').textContent = `${count} ${count === 1 ? 'skill' : 'skills'} nearby`;
  document.querySelector('#empty-results').hidden = count > 0;
}
if (document.querySelector('#service-detail')) {
  const id = params.get('id') || 'design-01', service = Object.hasOwn(catalog, id) ? catalog[id] : null;
  if (service) {
    ['service-title', 'service-description', 'provider-avatar', 'provider-name', 'provider-zone', 'provider-rating', 'service-price'].forEach((field, index) => {
      document.getElementById(field).textContent = (index === 5 ? '★ ' : index === 6 ? 'Rp' : '') + service[index];
    });
    document.title = `${service[0]} | LOCALSKILL`;
    document.querySelector('[name="service_id"]').value = id;
  } else {
    document.querySelector('#service-detail').hidden = true;
    document.querySelector('#missing-service').hidden = false;
  }
}
document.querySelectorAll('input[type="date"]').forEach(input => {
  const today = new Date();
  input.min = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
});
document.querySelectorAll('form[data-demo]').forEach(form => {
  // Enable only after this listener exists: no accidental credential submission if JS fails.
  const button = form.querySelector('button[type="submit"], button:not([type])');
  form.addEventListener('submit', event => {
    event.preventDefault();
    form.querySelector('[data-feedback]').textContent = 'Preview validated. Nothing was sent or saved. This action becomes available when the backend is connected.';
    if (form.elements.password) form.elements.password.value = '';
  });
  button.disabled = false;
});
if (window.lucide) window.lucide.createIcons();
