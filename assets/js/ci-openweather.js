function getData() {
    const requestOptions = {
        method: 'GET',
        redirect: 'follow'
    };

    fetch("http://api.openweathermap.org/data/2.5/forecast?q=Athens,GR&limit=1&appid=063f1e01c653468ba9b0d5726b9cee53", requestOptions)
        .then(response => response.text())
        .then(result => console.log(result))
        .catch(error => console.log('error', error));
}
