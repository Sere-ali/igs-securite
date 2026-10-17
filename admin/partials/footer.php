</div>
<script>
// Tableaux responsives : étiquette de colonne sur chaque cellule (affichage en cartes sur mobile)
document.querySelectorAll('.table-wrap table').forEach(function (t) {
  var head = t.querySelector('tr th') ? Array.prototype.map.call(t.querySelectorAll('tr:first-child th'), function (h) { return h.textContent.trim(); }) : [];
  t.classList.add('stack');
  t.querySelectorAll('tr').forEach(function (tr) {
    Array.prototype.forEach.call(tr.querySelectorAll('td'), function (td, i) { td.setAttribute('data-label', head[i] || ''); });
  });
});
</script>
</body></html>
