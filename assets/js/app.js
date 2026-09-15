/* Progressive enhancement only; HTML forms and field names are the backend contract. */
const params = new URLSearchParams(location.search);
// Insert sample text safely, including when these templates later receive server data.
function escapeHtml(value) {
  return String(value).replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[character]);
}

function skillMarkup(skills, checked = false) {
  return skills.map(skill => `<li class="rounded-full bg-emerald-50 px-3 py-2 text-sm text-emerald-800">${checked ? '<span aria-hidden="true">&#10003; </span>' : ''}${escapeHtml(skill)}</li>`).join('');
}

function portfolioMarkup(projects) {
  return projects.map(project => `<article class="overflow-hidden rounded-xl border border-slate-200">
    <div class="flex h-28 items-center justify-center bg-emerald-50 text-emerald-700"><i data-lucide="${escapeHtml(project.icon)}" class="h-12 w-12" aria-hidden="true"></i><span class="sr-only">Sample project illustration</span></div>
    <div class="p-4"><p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">${escapeHtml(project.type)}</p><h3 class="mt-2 font-bold">${escapeHtml(project.title)}</h3><p class="mt-2 text-sm leading-6 text-slate-600">${escapeHtml(project.description)}</p><ul class="mt-3 flex flex-wrap gap-2">${skillMarkup(project.tags)}</ul></div>
  </article>`).join('');
}

function reviewMarkup(reviews) {
  return reviews.map(review => `<article class="border-t border-slate-200 pt-5">
    <div class="flex flex-wrap items-center justify-between gap-2"><h3 class="font-bold">${escapeHtml(review.name)}</h3><p class="text-sm font-semibold text-emerald-700" aria-label="${review.rating} out of 5 stars">&#9733; ${review.rating}.0 / 5</p></div>
    <time datetime="${escapeHtml(review.date)}" class="mt-1 block text-xs text-slate-500">${escapeHtml(new Date(review.date + 'T00:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }))}</time><p class="mt-3 text-sm leading-6 text-slate-600">${escapeHtml(review.text)}</p>
  </article>`).join('');
}

function serviceMarkup(services) {
  return services.map(([id, service]) => `<article class="border-t border-slate-200 pt-4"><h3 class="font-bold">${escapeHtml(service.title)}</h3><p class="mt-2 text-sm leading-6 text-slate-600">${escapeHtml(service.description)}</p><p class="mt-3 font-bold">Rp${escapeHtml(service.price)} <span class="text-xs font-normal text-slate-500">/ package</span></p><a href="service.html?id=${encodeURIComponent(id)}" class="btn mt-4 w-full border-0 bg-emerald-600 text-white hover:bg-emerald-700">View service / Book</a></article>`).join('');
}

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
  const id = params.get('id') || 'design-01';
  const service = Object.hasOwn(catalog, id) ? catalog[id] : null;
  if (service) {
    const provider = providers[service.provider];
    const fields = {
      'service-title': service.title,
      'service-description': service.description,
      'provider-avatar': provider.initials,
      'provider-name': provider.name,
      'provider-zone': provider.location,
      'provider-rating': `\u2605 ${provider.rating} (${service.jobs} jobs for this service)`,
      'service-price': `Rp${service.price}`,
      'service-estimate': service.estimate,
    };
    Object.entries(fields).forEach(([field, value]) => { document.getElementById(field).textContent = value; });
    document.getElementById('provider-link').href = `profile.html?id=${encodeURIComponent(service.provider)}`;
    document.getElementById('service-skills').innerHTML = skillMarkup(service.skills);
    document.getElementById('service-portfolio').innerHTML = portfolioMarkup(service.portfolio);
    document.getElementById('service-reviews').innerHTML = reviewMarkup(service.reviews);
    document.title = `${service.title} | LOCALSKILL`;
    document.querySelector('[name="service_id"]').value = id;
  } else {
    document.querySelector('#service-detail').hidden = true;
    document.querySelector('#missing-service').hidden = false;
    document.title = 'Service unavailable | LOCALSKILL';
  }
}

if (document.querySelector('#student-profile')) {
  const id = params.get('id') || 'barikh';
  const provider = Object.hasOwn(providers, id) ? providers[id] : null;
  if (provider) {
    const fields = {
      'profile-name': provider.name, 'profile-avatar': provider.initials,
      'profile-role': provider.role, 'profile-location': provider.location,
      'profile-rating': `\u2605 ${provider.rating}`, 'profile-jobs': provider.jobs,
      'profile-bio': provider.bio,
    };
    Object.entries(fields).forEach(([field, value]) => { document.getElementById(field).textContent = value; });
    const services = Object.entries(catalog).filter(([, service]) => service.provider === id);
    document.getElementById('profile-skills').innerHTML = skillMarkup(provider.skills, true);
    document.getElementById('profile-portfolio').innerHTML = portfolioMarkup(services.flatMap(([, service]) => service.portfolio));
    document.getElementById('profile-reviews').innerHTML = reviewMarkup(services.flatMap(([, service]) => service.reviews));
    document.getElementById('profile-services').innerHTML = serviceMarkup(services);
    document.title = `${provider.name} | Student profile | LOCALSKILL`;
  } else {
    document.querySelector('#student-profile').hidden = true;
    document.querySelector('#missing-profile').hidden = false;
    document.title = 'Student profile unavailable | LOCALSKILL';
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
