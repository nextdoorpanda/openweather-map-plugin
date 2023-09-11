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
        const weatherLocation = `${weatherCity}, ${weatherCountry}`;
        const weatherTitle = weatherData.list[0].weather[0].main;
        const weatherDescription = weatherData.list[0].weather[0].description;
        const weatherIconCode = weatherData.list[0].weather[0].icon;
        const weatherIcon = `http://openweathermap.org/img/w/${weatherIconCode}.png`;
        const weatherTemperatureValue = `${parseInt(weatherData.list[0].main.temp)}`;
        const weatherTemperatureDegrees = [{'standard': 'K'}, {'metric': 'C'}, {'imperial': 'F'}];
        const weatherTemperatureDegree = weatherTemperatureDegrees
            .filter( obj => weatherOptionsValues.unit in obj)
            .map(obj => obj[weatherOptionsValues.unit]);
        const weatherTemperature = `${weatherTemperatureValue}&deg;${weatherTemperatureDegree}`;

        const outputDiv = document.querySelectorAll('.weather-output');
        outputDiv.forEach( item => {
            item.style.padding = "20px";
            // item.style.width = "100%";
            item.innerHTML = setLayout( weatherIcon, weatherTitle, weatherLocation, weatherTemperature, weatherDescription );
        });

        function setLayout( weatherIcon, weatherTitle, weatherLocation, weatherTemperature, weatherDescription ) {
            return `
            <div class="weather-widget d-flex justify-content-center align-items-center">
                <div class="container-fluid">
                    <div class="row no-gutters">
                        <div class="col-md-3">
                            <div class="weather-component d-flex justify-content-center align-items-center">
                                <img src="${weatherIcon}" alt="${weatherTitle} icon"/>
                            </div>
                        </div>
        
                        <div class="col-md-9">
                            <div class="weather-component d-flex flex-column align-items-center justify-content-center">
                                <div class="weather-location">${weatherLocation}</div>
                                <div class="weather-details text-nowrap">${weatherTemperature}, ${weatherDescription}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;

        }

    }
})();