    const selectCity = document.getElementById('activity_city');
    const selectLocation = document.getElementById('activity_location');
    const divLocationDetail = document.getElementById('location_detail');

    let macarte = null;
    // Fonction d'initialisation de la carte
    function initMap(lat, lon) {
        // Créer l'objet "macarte" et l'insèrer dans l'élément HTML qui a l'ID "map"
        macarte = L.map('map').setView([lat, lon], 11);
        let marker = L.marker([lat, lon]).addTo(macarte);
        // Leaflet ne récupère pas les cartes (tiles) sur un serveur par défaut. Nous devons lui préciser où nous souhaitons les récupérer. Ici, openstreetmap.fr
        L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
            // Il est toujours bien de laisser le lien vers la source des données
            attribution: 'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
            minZoom: 1,
            maxZoom: 20
        }).addTo(macarte);
    }

    selectCity.addEventListener('change', function(element) {
        let cityId = selectCity.value;

        if (cityId != null)
        {
            function createNode(element) {
                return document.createElement(element);
            }

            function append(parent, el) {
                return parent.appendChild(el);
            }

            const url = `http://localhost/sortir/public/api/location/city/${cityId}`;

            fetch(url)
                .then((resp) => resp.json())
                .then(function (resp) {
                    let locations = resp;
                    console.log(selectLocation)
                    console.log(locations)
                    selectLocation.innerHTML = ''
                    let placeHolder = createNode('option');
                    placeHolder.innerHTML = '-Veuillez choisir un lieu-'
                    append(selectLocation, placeHolder);
                    return locations.map(function (location) {
                        let newOption = createNode('option');
                        newOption.innerHTML = location.name
                        newOption.value = location.id
                        append(selectLocation, newOption);
                    })
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    })

    selectLocation.addEventListener('change', function () {
        let locationId = selectLocation.value;
        let lat = 48.852969;
        let lon = 2.349903;
        if (locationId != null)
        {
            function createNode(element) {
                return document.createElement(element);
            }

            function append(parent, el) {
                return parent.appendChild(el);
            }

            const url2 = `http://localhost/sortir/public/api/location/${locationId}`;

            fetch(url2)
                .then((resp2) => resp2.json())
                .then(function (resp2) {
                    let locationDetails = resp2;
                    console.log(divLocationDetail)
                    console.log(locationDetails)
                    divLocationDetail.innerHTML = ""
                    return locationDetails.map(function (locationDetail) {
                        let pStreet = createNode('p');
                        let pZipCode = createNode('p');
                        let pLongitude = createNode('p');
                        let pLatitude = createNode('p');
                        // pStreet.innerHTML = location.street
                        pStreet.innerHTML = 'Rue : ' + locationDetail.street + '<br>'
                        pZipCode.innerHTML = 'Code postale : ' + locationDetail.city.zipCode + '<br>'
                        pLongitude.innerHTML = 'Longitude : ' + locationDetail.longitude + '<br>'
                        pLatitude.innerHTML = 'Latitude : ' + locationDetail.latitude + '<br>'

                        append(divLocationDetail, pStreet);
                        append(divLocationDetail, pZipCode);
                        append(divLocationDetail, pLongitude);
                        append(divLocationDetail, pLatitude);
                        lat = locationDetail.latitude;
                        lon = locationDetail.longitude;
                    let divMap = document.getElementById('map')

                    divMap.innerHTML = ''
                    initMap(lat, lon)
                    })

                })

                .catch(function (error) {
                    console.log(error);
                });

    }
})
