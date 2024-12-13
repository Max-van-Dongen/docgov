<input type="text" id="scrape-text">
<button id="scrape-submit">Scrape!</button>
<button id="calc-relevancy">Calculate Relevancy</button>

<script>
    document.getElementById('scrape-submit').addEventListener('click', () => {
        const inputValue = document.getElementById('scrape-text').value;

        const url = `/scrape-data?q=${encodeURIComponent(inputValue)}`;

        fetch(url, {
            method: 'GET',
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response:', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    document.getElementById('calc-relevancy').addEventListener('click', () => {

        const url = `/calculate-relevancy`;

        fetch(url, {
            method: 'GET',
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response:', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>
