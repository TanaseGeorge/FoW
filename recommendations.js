document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('recommendationForm');
    const recommendationsContainer = document.getElementById('recommendations');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        fetch('get_recommendations.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRecommendations(data.recommendations);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while getting recommendations.');
        });
    });

    function displayRecommendations(recommendations) {
        recommendationsContainer.innerHTML = '';
        
        recommendations.forEach(rec => {
            const recElement = document.createElement('div');
            recElement.className = 'recommendation-item';
            
            recElement.innerHTML = `
                <h3>${rec.title}</h3>
                <p>${rec.description}</p>
                <p class="details">
                    <strong>Type:</strong> ${rec.type}<br>
                    <strong>Price Range:</strong> ${rec.priceRange}<br>
                    ${rec.brand ? `<strong>Recommended Brand:</strong> ${rec.brand}` : ''}
                </p>
            `;
            
            recommendationsContainer.appendChild(recElement);
        });
    }
}); 