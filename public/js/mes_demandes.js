// mes_demandes.js – fetch data, animate counts, live search

document.addEventListener('DOMContentLoaded', () => {
  const pendingCountEl = document.getElementById('count-pending');
  const acceptedCountEl = document.getElementById('count-accepted');
  const rejectedCountEl = document.getElementById('count-rejected');
  const tableBody = document.getElementById('requests-body');
  const searchInput = document.getElementById('search-input');

  // Helper for count-up animation
  function animateCount(el, target) {
    let start = 0;
    const duration = 1000; // ms
    const stepTime = Math.max(Math.floor(duration / target), 16);
    const timer = setInterval(() => {
      start += 1;
      el.textContent = start;
      if (start >= target) clearInterval(timer);
    }, stepTime);
  }

  // Render rows
  function renderRows(data) {
    tableBody.innerHTML = '';
    data.forEach(row => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${row.id}</td>
        <td>${row.name}</td>
        <td><span class="status-badge status-${row.status}">${row.status.charAt(0).toUpperCase() + row.status.slice(1)}</span></td>
      `;
      tableBody.appendChild(tr);
    });
  }

  // Fetch data from API
  async function loadData(query = '') {
    try {
      const resp = await fetch(`api/get_requests.php?search=${encodeURIComponent(query)}`);
      const json = await resp.json();
      const {counts, requests} = json;
      animateCount(pendingCountEl, counts.pending);
      animateCount(acceptedCountEl, counts.accepted);
      animateCount(rejectedCountEl, counts.rejected);
      renderRows(requests);
    } catch (e) {
      console.error('Failed to load data', e);
    }
  }

  // Initial load
  loadData();

  // Live search
  let debounceTimer;
  searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      loadData(searchInput.value.trim());
    }, 300);
  });
});
