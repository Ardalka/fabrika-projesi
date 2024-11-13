function showDetails(cardNumber) {
    // Ana kartları gizle
    document.querySelector('.card-container').style.display = 'none'; 

    // Detay kartını göster
    const detailCard = document.getElementById('detail-card');
    detailCard.classList.add('show'); 

    const detailContent = document.getElementById('detail-content');
    if (cardNumber === 1) {
        detailContent.innerHTML = "<h2>Fabrika Hakkında</h2><p>Burada fabrika ile ilgili detaylı bilgiler yer alacaktır...</p>";
    } else if (cardNumber === 2) {
        detailContent.innerHTML = "<h2>Makine Hakkında</h2><p>Burada makine ile ilgili detaylı bilgiler yer alacaktır...</p>";
    } else if (cardNumber === 3) {
        detailContent.innerHTML = "<h2>Üretim Hakkında</h2><p>Burada üretim ile ilgili detaylı bilgiler yer alacaktır...</p>";
    }
}

function hideDetails() {
    // Ana kartları tekrar göster
    document.querySelector('.card-container').style.display = 'flex'; 

    // Detay kartını gizle
    const detailCard = document.getElementById('detail-card');
    detailCard.classList.remove('show'); 
}