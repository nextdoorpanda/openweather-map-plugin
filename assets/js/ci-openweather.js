(function () {
    'use strict';

    const requestOptions = {
        method: 'GET',
        redirect: 'follow'
    };

    const queryURL = `http://api.openweathermap.org/data/2.5/forecast?q=Athens,GR&cnt=1&appid=063f1e01c653468ba9b0d5726b9cee53`;

    const getData = new Promise((resolve, reject) => {
        fetch(queryURL, requestOptions)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Didn't got any response");
                }
                return response.json();
            })
            .then(data => {
                resolve(data);
            })
            .catch(error => {
                reject(error);
            });
    });

    getData
        .then(weatherData => {
            outputData(weatherData);
        })
        .catch(error => {
            console.error('Error:', error);
        });

    function outputData(weatherData) {
        const weatherCity = weatherData.city.name;
        const weatherCountry = weatherData.city.country;
        const weatherTitle = weatherData.list[0].weather[0].main;
        const weatherDescription = weatherData.list[0].weather[0].description;
        const weatherIconCode = weatherData.list[0].weather[0].icon;
        const weatherIcon = `http://openweathermap.org/img/w/${weatherIconCode}.png`;
        const weatherIconImg = `<img src="${weatherIcon}" alt="${weatherTitle} icon"/>`;

        const outputDiv = document.querySelectorAll('.weather-output');
        outputDiv.forEach( item => {
            item.innerHTML = `${weatherCity} ${weatherCountry}`;
            item.innerHTML += '<br>';
            item.innerHTML +=  `${weatherDescription} ${weatherIconImg}`;
        });

    }
})();