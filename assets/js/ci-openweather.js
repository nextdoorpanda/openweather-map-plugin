function getDeviceLocation() {
    const options = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    };

    return new Promise((resolve, reject) => {
        function success(pos) {
            const crd = pos.coords;
            const latitude = crd.latitude;
            const longitude = crd.longitude;

            resolve({ latitude, longitude });
        }

        function error(err) {
            console.warn(`ERROR(${err.code}): ${err.message}`);
            reject(err);
        }

        navigator.geolocation.getCurrentPosition(success, error, options);
    });
}

getDeviceLocation()
    .then((location) => {
        console.log("Latitude:", location.latitude);
        console.log("Longitude:", location.longitude);
    })
    .catch((error) => {
        console.error(error);
    });



