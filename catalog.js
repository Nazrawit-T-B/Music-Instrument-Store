document.addEventListener('DOMContentLoaded', function() {
    
    document.querySelector('.nav-link[href="catalog.html"]').style.backgroundColor = 'rgba(78, 205, 196, 0.15)';
    
    const search = document.getElementById('searchCatalog');
    const cards = document.querySelectorAll('.subcategory-card');
    
    if (search) {
        search.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(term) || !term) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
