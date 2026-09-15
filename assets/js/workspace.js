(() => {
  const query = new URLSearchParams(location.search);
  const pageName = location.pathname.split('/').pop().replace('.html', '');
  const $ = selector => document.querySelector(selector);
  const safe = escapeHtml;
  const prefix = 'localskill.workspace.v1.';
  const memory = new Map();
  const volatileKeys = new Set();
  const statusNames = { 'in-progress': 'In progress', completed: 'Completed', cancelled: 'Cancelled' };
  const findOrder = id => workspaceOrders.find(order => order.id === id);
  const isPurchase = order => order.buyer === workspaceUser;
  const counterpart = order => providers[isPurchase(order) ? order.provider : order.buyer];
  const eligible = order => order && isPurchase(order) && order.status === 'completed';
  const dateLabel = date => new Date(date + 'T00:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
  const textValid = (value, min, max) => typeof value === 'string' && value.trim().length >= min && value.length <= max;

  function storageWarning() {
    $('#storage-notice').textContent = 'Demo only. Browser storage is unavailable or full. Changes stay on this page only and may be lost when you leave. Nothing is sent or published.';
  }

  function read(key, fallback) {
    if (volatileKeys.has(key)) return memory.get(key) ?? fallback;
    try {
      const value = localStorage.getItem(prefix + key);
      return value === null ? fallback : JSON.parse(value);
    } catch {
      storageWarning();
      return memory.get(key) ?? fallback;
    }
  }

  function save(key, value) {
    memory.set(key, value);
    try {
      localStorage.setItem(prefix + key, JSON.stringify(value));
      volatileKeys.delete(key);
      return 'Saved in this browser only.';
    } catch {
      volatileKeys.add(key);
      storageWarning();
      return 'Kept on this page only; browser storage is unavailable.';
    }
  }

  function writtenReviews() {
    const stored = read('reviews', []);
    const reviews = [...workspaceSeedReviews];
    if (Array.isArray(stored)) stored.forEach(review => {
      if (review && eligible(findOrder(review.orderId)) && Number.isInteger(review.rating) && review.rating >= 1 && review.rating <= 5 && textValid(review.text, 10, 1000) && /^\d{4}-\d{2}-\d{2}$/.test(review.date) && !reviews.some(item => item.orderId === review.orderId)) reviews.push(review);
    });
    return reviews;
  }

  function savedMessages() {
    const stored = read('messages', []);
    return Array.isArray(stored) ? stored.filter(message => message && findOrder(message.orderId) && textValid(message.text, 1, 1000) && typeof message.time === 'string' && Number.isFinite(Date.parse(message.time))) : [];
  }

  function profileDraft() {
    const stored = read('profile', null);
    const draft = structuredClone(workspaceDefaultProfile);
    if (!stored || typeof stored !== 'object') return draft;
    for (const [key, max] of [['name', 80], ['role', 100], ['bio', 1000]]) {
      if (textValid(stored[key], 1, max)) draft[key] = stored[key];
    }
    for (const name of workspaceVerifiedSkills) {
      const level = stored.levels?.[name];
      if (Number.isInteger(level) && level >= 0 && level <= 100 && level % 5 === 0) draft.levels[name] = level;
    }
    if (Array.isArray(stored.portfolio)) draft.portfolio = stored.portfolio.filter(project => project && textValid(project.title, 1, 100) && textValid(project.type, 1, 60) && textValid(project.description, 10, 500)).slice(0, 12).map(project => ({ title: project.title, type: project.type, description: project.description, icon: 'panels-top-left', tags: [] }));
    return draft;
  }

  document.querySelectorAll('[data-workspace-nav]').forEach(link => {
    if (link.dataset.workspaceNav === pageName) {
      link.setAttribute('aria-current', 'page');
      link.classList.add('bg-emerald-600', 'text-white', 'border-0');
    }
  });

  if ($('#order-list')) {
    const view = query.get('view') === 'providing' ? 'providing' : 'buying';
    const requestedStatus = query.get('status') || 'all';
    const status = requestedStatus === 'all' || Object.hasOwn(statusNames, requestedStatus) ? requestedStatus : 'all';
    const reviews = writtenReviews();
    const orders = workspaceOrders.filter(order => (view === 'buying' ? isPurchase(order) : order.provider === workspaceUser) && (status === 'all' || order.status === status));
    $('#orders-heading').textContent = view === 'buying' ? "Services I'm Buying" : "Services I'm Providing";
    $('#order-filter').elements.view.value = view;
    $('#order-filter').elements.status.value = status;
    $('#orders-clear').href = `orders.html?view=${view}`;
    document.querySelectorAll('[data-order-view]').forEach(link => {
      const active = link.dataset.orderView === view;
      link.className = active ? 'btn border-0 bg-emerald-600 text-white' : 'btn';
      if (active) link.setAttribute('aria-current', 'page');
    });
    $('#orders-count').textContent = `${orders.length} ${orders.length === 1 ? 'order' : 'orders'} shown · Sample history`;
    $('#orders-empty').hidden = orders.length !== 0;
    $('#order-list').innerHTML = orders.map(order => {
      const service = catalog[order.service];
      const person = counterpart(order);
      const reviewed = reviews.some(review => review.orderId === order.id);
      const received = workspaceReceivedReviews.some(review => review.orderId === order.id);
      const reviewAction = eligible(order)
        ? `<a href="reviews.html?order=${order.id}" class="btn btn-sm border-0 bg-emerald-600 text-white">${reviewed ? 'View your review' : 'Write a review'}</a>`
        : order.status === 'completed'
          ? `<a href="reviews.html#received-title" class="btn btn-sm">${received ? 'View customer review' : 'Customer feedback'}</a>`
          : '<p class="text-xs text-slate-500">Reviews unlock after completion.</p>';
      return `<article data-order-id="${order.id}" class="rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex flex-wrap items-start justify-between gap-4"><div><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">${order.id}</p><h3 class="mt-2 text-xl font-bold">${safe(service.title)}</h3></div><span class="rounded-full bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800">${statusNames[order.status]}</span></div>
        <p class="mt-3 text-sm text-slate-600">${isPurchase(order) ? 'Provider' : 'Customer'}: <a href="profile.html?id=${isPurchase(order) ? order.provider : order.buyer}" class="font-semibold text-emerald-700 underline">${safe(person.name)}</a> · ${safe(person.location)}</p>
        <p class="mt-3 leading-6 text-slate-600">${safe(order.brief)}</p>
        <div class="mt-4 flex flex-wrap gap-x-8 gap-y-2 text-sm"><p class="font-bold">Rp${service.price} / package</p><p>${order.status === 'completed' ? 'Completed' : 'Scheduled'}: <time datetime="${order.date}">${dateLabel(order.date)}</time></p></div>
        <ol aria-label="Order progress" class="mt-5 flex flex-wrap gap-2 text-xs">${['Find', 'Match', 'Book', 'Complete', 'Review'].map((step, index) => {
          const current = order.status === 'completed' ? (reviewed || received ? 4 : 3) : 2;
          return `<li ${index === current ? 'aria-current="step"' : ''} class="rounded-full px-3 py-2 ${index === current ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600'}">${index + 1} ${step}</li>`;
        }).join('')}</ol>
        <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5"><a href="messages.html?order=${order.id}" class="btn btn-sm">Message ${isPurchase(order) ? 'provider' : 'customer'}</a><a href="service.html?id=${order.service}" class="btn btn-sm">View service</a>${reviewAction}</div>
      </article>`;
    }).join('');
  }

  if ($('#conversation-list')) {
    const selected = findOrder(query.get('order'));
    $('#conversation-list').innerHTML = workspaceOrders.map(order => `<a href="messages.html?order=${order.id}" ${selected?.id === order.id ? 'aria-current="page"' : ''} class="rounded-xl border p-4 ${selected?.id === order.id ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:bg-slate-50'}"><span class="block font-bold">${safe(counterpart(order).name)}</span><span class="mt-1 block text-xs font-semibold text-emerald-700">${isPurchase(order) ? 'Provider · Buying' : 'Customer · Providing'}</span><span class="mt-2 block text-sm text-slate-600">${safe(catalog[order.service].title)}</span><span class="mt-1 block text-xs text-slate-500">${order.id} · ${statusNames[order.status]}</span></a>`).join('');
    if (query.has('order') && !selected) $('#conversation-empty-text').textContent = 'This conversation is unavailable. Choose one of your sample orders.';
    if (selected) {
      $('#conversation').hidden = false;
      $('#conversation-empty').hidden = true;
      $('#conversation-name').textContent = counterpart(selected).name;
      $('#conversation-role').textContent = isPurchase(selected) ? 'Your provider · Buying' : 'Your customer · Providing';
      $('#conversation-order').textContent = `${selected.id} · ${catalog[selected.service].title} · ${statusNames[selected.status]}`;
      $('#conversation-service').href = `service.html?id=${selected.service}`;
      const messageNode = (text, author, time, own) => {
        const node = document.createElement('li');
        node.className = `max-w-[90%] rounded-2xl p-4 ${own ? 'self-end bg-emerald-100 text-emerald-950' : 'self-start border border-slate-200 bg-white'}`;
        node.innerHTML = `<p class="text-xs font-bold">${safe(author)}</p><p class="mt-2 whitespace-pre-wrap text-sm leading-6">${safe(text)}</p><p class="mt-2 text-xs text-slate-500">${safe(time)}</p>`;
        return node;
      };
      $('#chat-log').append(messageNode(selected.brief, isPurchase(selected) ? 'You · Sample' : counterpart(selected).name, 'Sample conversation', isPurchase(selected)), messageNode(selected.message, counterpart(selected).name, 'Sample reply', false));
      savedMessages().filter(message => message.orderId === selected.id).forEach(message => $('#chat-log').append(messageNode(message.text, 'You · Local demo', new Date(message.time).toLocaleString('en-GB'), true)));
      $('#chat-log').scrollTop = $('#chat-log').scrollHeight;
      const form = $('#message-form');
      form.addEventListener('submit', event => {
        event.preventDefault();
        const input = form.elements.message;
        const text = input.value.trim();
        input.setCustomValidity(text ? '' : 'Please write a message, not just spaces.');
        if (!form.reportValidity()) return;
        const messages = savedMessages();
        if (messages.filter(message => message.orderId === selected.id).length >= 100) {
          $('#message-feedback').textContent = 'This demo conversation has reached its 100-message limit.';
          return;
        }
        const message = { orderId: selected.id, text, time: new Date().toISOString() };
        const result = save('messages', [...messages, message]);
        $('#chat-log').append(messageNode(text, 'You · Local demo', new Date(message.time).toLocaleString('en-GB'), true));
        $('#chat-log').scrollTop = $('#chat-log').scrollHeight;
        input.value = '';
        input.focus();
        $('#message-feedback').textContent = `${result} Not delivered to another person.`;
      });
      form.elements.message.addEventListener('input', () => form.elements.message.setCustomValidity(''));
      form.querySelector('button').disabled = false;
    }
  }

  if ($('#review-form')) {
    const form = $('#review-form');
    function renderReviews() {
      const reviews = writtenReviews();
      const remaining = workspaceOrders.filter(order => eligible(order) && !reviews.some(review => review.orderId === order.id));
      const selectedValue = form.elements.order_id.value;
      form.elements.order_id.innerHTML = '<option value="">Choose an eligible order</option>' + remaining.map(order => `<option value="${order.id}">${order.id} · ${safe(catalog[order.service].title)}</option>`).join('');
      if (remaining.some(order => order.id === selectedValue)) form.elements.order_id.value = selectedValue;
      form.querySelector('button').disabled = remaining.length === 0;
      $('#review-eligibility').textContent = remaining.length ? `${remaining.length} completed purchase available to review. Only your own completed purchases are eligible.` : 'All eligible purchases have been reviewed. Your next review unlocks when another purchase is completed.';
      function cards(items, received = false) {
        return items.map(review => {
          const order = findOrder(review.orderId);
          return `<article class="border-t border-slate-200 pt-4"><h3 class="font-bold">${safe(catalog[order.service].title)}</h3><p class="mt-1 text-sm text-slate-600">${received ? 'From' : 'For'} ${safe(counterpart(order).name)} · ${order.id}</p><p class="mt-3 font-semibold text-emerald-700" aria-label="${review.rating} out of 5 stars">&#9733; ${review.rating} / 5</p><p class="mt-3 whitespace-pre-wrap text-sm leading-6 text-slate-600">${safe(review.text)}</p><p class="mt-3 text-xs text-slate-500"><time datetime="${review.date}">${dateLabel(review.date)}</time> · ${received || workspaceSeedReviews.some(seed => seed.orderId === order.id) ? 'Sample review' : 'Local demo review'}</p></article>`;
        }).join('') || '<p class="text-sm text-slate-500">No reviews yet.</p>';
      }
      $('#written-reviews').innerHTML = cards(reviews);
      $('#received-reviews').innerHTML = cards(workspaceReceivedReviews, true);
    }
    form.addEventListener('submit', event => {
      event.preventDefault();
      const order = findOrder(form.elements.order_id.value);
      const rating = Number(form.elements.rating.value);
      const text = form.elements.review.value.trim();
      form.elements.review.setCustomValidity(textValid(text, 10, 1000) ? '' : 'Please write 10–1000 characters about your experience.');
      if (!form.reportValidity()) return;
      const existing = writtenReviews();
      if (!eligible(order) || existing.some(review => review.orderId === order.id) || !Number.isInteger(rating) || rating < 1 || rating > 5) {
        $('#review-feedback').textContent = 'This order is not eligible or already has your review.';
        renderReviews();
        return;
      }
      const review = { orderId: order.id, rating, text, date: new Date().toISOString().slice(0, 10) };
      $('#review-feedback').textContent = `${save('reviews', [...existing, review])} Your review is not published.`;
      form.reset();
      renderReviews();
    });
    form.elements.review.addEventListener('input', () => form.elements.review.setCustomValidity(''));
    renderReviews();
    if (query.has('order')) {
      const order = findOrder(query.get('order'));
      if (!eligible(order)) $('#review-eligibility').textContent = 'This order cannot be reviewed. Choose one of your completed purchases.';
      else if (writtenReviews().some(review => review.orderId === order.id)) $('#review-eligibility').textContent = 'You already reviewed this order. Find it under Reviews I\'ve written.';
      else form.elements.order_id.value = order.id;
    }
  }

  if ($('#skill-profile-form')) {
    let draft = profileDraft();
    const form = $('#skill-profile-form');
    const portfolioForm = $('#portfolio-form');
    function renderSummary() {
      $('#owner-name').textContent = draft.name;
      $('#owner-role').textContent = draft.role;
      $('#owner-bio').textContent = draft.bio;
    }
    function renderPortfolio() {
      $('#owner-portfolio').innerHTML = draft.portfolio.map((project, index) => `<article class="rounded-xl border border-slate-200 p-5"><p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">${safe(project.type)}</p><h3 class="mt-2 text-lg font-bold">${safe(project.title)}</h3><p class="mt-3 whitespace-pre-wrap text-sm leading-6 text-slate-600">${safe(project.description)}</p><button type="button" data-remove-project="${index}" aria-label="Remove ${safe(project.title)}" class="btn btn-sm mt-4">Remove from draft</button></article>`).join('') || '<p class="text-sm text-slate-500">No projects in your draft yet. Add your first project below.</p>';
    }
    form.elements.display_name.value = draft.name;
    form.elements.professional_title.value = draft.role;
    form.elements.bio.value = draft.bio;
    workspaceVerifiedSkills.forEach(skill => {
      const input = form.elements[skill.toLowerCase()];
      input.value = draft.levels[skill];
      const output = document.querySelector(`[data-level="${skill}"]`);
      output.textContent = `${input.value}%`;
      const progress = document.querySelector(`[data-progress="${skill}"]`);
      progress.value = input.value;
      input.addEventListener('input', () => { output.textContent = `${input.value}%`; progress.value = input.value; });
    });
    renderSummary();
    renderPortfolio();
    form.addEventListener('submit', event => {
      event.preventDefault();
      for (const name of ['display_name', 'professional_title', 'bio']) {
        const field = form.elements[name];
        field.setCustomValidity(field.value.trim() ? '' : 'Please enter text, not just spaces.');
      }
      if (!form.reportValidity()) return;
      // Re-read the portfolio so saving profile fields preserves newer portfolio edits.
      draft = profileDraft();
      draft.name = form.elements.display_name.value.trim();
      draft.role = form.elements.professional_title.value.trim();
      draft.bio = form.elements.bio.value.trim();
      workspaceVerifiedSkills.forEach(skill => { draft.levels[skill] = Number(form.elements[skill.toLowerCase()].value); });
      $('#profile-feedback').textContent = `${save('profile', draft)} Public profile and verification are unchanged.`;
      renderSummary();
    });
    for (const name of ['display_name', 'professional_title', 'bio']) form.elements[name].addEventListener('input', () => form.elements[name].setCustomValidity(''));
    portfolioForm.addEventListener('submit', event => {
      event.preventDefault();
      for (const name of ['project_title', 'project_type', 'project_description']) {
        const field = portfolioForm.elements[name];
        field.setCustomValidity(field.value.trim().length >= (name === 'project_description' ? 10 : 1) ? '' : 'Please add a meaningful project description and title.');
      }
      if (!portfolioForm.reportValidity()) return;
      draft = profileDraft();
      if (draft.portfolio.length >= 12) {
        $('#portfolio-feedback').textContent = 'Your demo portfolio can hold 12 projects. Remove a project before adding another.';
        return;
      }
      draft.portfolio.push({ title: portfolioForm.elements.project_title.value.trim(), type: portfolioForm.elements.project_type.value.trim(), description: portfolioForm.elements.project_description.value.trim(), icon: 'panels-top-left', tags: [] });
      $('#portfolio-feedback').textContent = save('profile', draft);
      portfolioForm.reset();
      renderPortfolio();
    });
    for (const name of ['project_title', 'project_type', 'project_description']) portfolioForm.elements[name].addEventListener('input', () => portfolioForm.elements[name].setCustomValidity(''));
    $('#owner-portfolio').addEventListener('click', event => {
      const button = event.target.closest('[data-remove-project]');
      if (!button) return;
      const index = Number(button.dataset.removeProject);
      draft.portfolio.splice(index, 1);
      $('#portfolio-feedback').textContent = `Project removed. ${save('profile', draft)}`;
      renderPortfolio();
      const nextButton = $('#owner-portfolio').querySelector('button');
      (nextButton || document.querySelector('details summary')).focus();
    });
    form.querySelector('button').disabled = false;
    portfolioForm.querySelector('button').disabled = false;
  }
})();
