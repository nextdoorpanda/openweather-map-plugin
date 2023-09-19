(function () {
    'use strict';

    const requestOptions = {
        method: 'GET',
        redirect: 'follow'
    };

    const api_key = weatherOptionsValues.api_key;
    const location = weatherOptionsValues.location;
    const unit = weatherOptionsValues.unit;

    const queryURL = `http://api.openweathermap.org/data/2.5/forecast?q=${location}&cnt=1&units=${unit}&appid=${api_key}`;

    async function getData() {
        try {
            const response = await fetch(queryURL, requestOptions);

            if(!response.ok) {
                throw new Error("Didn't get any response.");
            }

            const data = await response.json();

            return data;
        } catch(error) {
            throw error;
        }
    }

    getData()
        .then(weatherData => {
            outputData(weatherData);
        })
        .catch(error => {
            console.error('Error:', error);
        });

    function outputData(weatherData) {
        const weatherCity = weatherData.city.name;
        const weatherCountry = weatherData.city.country;
        const weatherLocation = `${weatherCity}, ${weatherCountry}`;
        const weather = weatherData.list[0].weather[0];
        const weatherTitle = weather.main;
        const weatherDescription = weather.description;
        const weatherIconCode = weather.icon;
        const weatherIcon = `http://openweathermap.org/img/w/${weatherIconCode}.png`;
        const weatherTemperatureValue = `${parseInt(weatherData.list[0].main.temp, 10)}`;

        const weatherTemperatureDegrees = {
            standard: 'K',
            metric: '&deg;C',
            imperial: '&deg;F'
        };

        const weatherTemperatureDegree = weatherTemperatureDegrees[weatherOptionsValues.unit];
        const weatherTemperature = `${weatherTemperatureValue}${weatherTemperatureDegree}`;

        const outputDiv = document.querySelectorAll('.openweather-content-wrap');
        outputDiv.forEach( item => {
            item.querySelector('.openweather-icon').src = weatherIcon;
            item.querySelector('.openweather-icon').alt = `${weatherTitle} icon`;
            item.querySelector('.openweather-location').innerHTML = weatherLocation;
            item.querySelector('.openweather-details').innerHTML = `${weatherTemperature}, ${weatherDescription}`;
        });
    }
})();