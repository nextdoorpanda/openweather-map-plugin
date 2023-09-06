(function () {
    'use strict';

    const requestOptions = {
        method: 'GET',
        redirect: 'follow'
    };

    // fetch("./key.txt")
    //     .then(response => response.text())
    //     .then(apiKey => {
    //         apiKey = apiKey.trim();
    //         console.log(apiKey);
    //         const queryURL = `http://api.openweathermap.org/data/2.5/forecast?q=Athens,GR&limit=1&appid=${apiKey}`;
    //
    //         // fetch(queryURL, requestOptions)
    //         //     .then(response => response.json())
    //         //     .then(data => console.log(data))
    //         //     .catch(error => console.log('error', error));
    //
    //     })
    //     .catch(error => console.log('error', error));

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
            const outputDiv = document.querySelector('#weather-output');
            outputDiv.innerHTML = JSON.stringify(weatherData);
        })
        .catch(error => {
            console.error('Error:', error);
        });
})();