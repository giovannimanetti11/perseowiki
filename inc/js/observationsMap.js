document.addEventListener("DOMContentLoaded", async function() {
    var map;
    const scientificNameElement = document.getElementById("scientificName");
    const scientificName = scientificNameElement ? scientificNameElement.getAttribute("data-scientific-name") : '';
    if (!scientificName) return;

    const loading = document.getElementById('loading');
    const vectorSource = new ol.source.Vector();
    const clusterSource = new ol.source.Cluster({
        distance: 15,
        source: vectorSource
    });

    const getColor = (size) => {
        if (size > 1000) {
            return '#800026';
        } else if (size > 500) {
            return '#BD0026';
        } else if (size > 200) {
            return '#E31A1C';
        } else if (size > 100) {
            return '#FC4E2A';
        } else if (size > 50) {
            return '#FD8D3C';
        } else if (size > 20) {
            return '#FEB24C';
        } else {
            return '#FED976';
        }
    }

    const getRadius = (size) => {
        if (size > 1000) {
            return 20;
        } else if (size > 500) {
            return 18;
        } else if (size > 200) {
            return 16;
        } else if (size > 100) {
            return 14;
        } else if (size > 50) {
            return 12;
        } else if (size > 20) {
            return 10;
        } else {
            return 8;
        }
    }

    const styleCache = {};
    const clusterStyleFunction = function(feature) {
        const size = feature.get('features').length;
        let style = styleCache[size];
        if (!style) {
            style = new ol.style.Style({
                image: new ol.style.Circle({
                    radius: getRadius(size),
                    stroke: new ol.style.Stroke({
                        color: '#0A2944'
                    }),
                    fill: new ol.style.Fill({
                        color: getColor(size)
                    })
                }),
                text: new ol.style.Text({
                    text: size.toString(),
                    fill: new ol.style.Fill({
                        color: '#0A2944'
                    })
                })
            });
            styleCache[size] = style;
        }
        return style;
    };

    const getTaxonKey = async (scientificName) => {
        const response = await fetch(`https://api.gbif.org/v1/species/match?name=${encodeURIComponent(scientificName)}`, {cache: "no-store"});
        const data = await response.json();
        return data.usageKey;
    };
    
    const getOccurrences = async (taxonKey, basisOfRecord, offset) => {
        let url = `https://api.gbif.org/v1/occurrence/search?taxon_key=${taxonKey}&limit=300&offset=${offset}&basisOfRecord=${basisOfRecord}&year=2023&hasCoordinate=true`;
        const response = await fetch(url, {cache: "no-store"});
        const jsonData = await response.json();
        return jsonData.results;
    };

    const renderOccurrences = (occurrences, basisOfRecord) => {
        occurrences.forEach(function (occurrence) {
            if (occurrence.decimalLatitude && occurrence.decimalLongitude) {
                const marker = new ol.Feature({
                    geometry: new ol.geom.Point(ol.proj.fromLonLat([occurrence.decimalLongitude, occurrence.decimalLatitude])),
                    title: occurrence.species,
                    count: 1,
                    record: basisOfRecord,
                    date: occurrence.eventDate
                });
                vectorSource.addFeature(marker);
            }
        });
    };

    const fetchRecords = async (taxonKey, basisOfRecord) => {
        const limit = 300;
        const maxRequests = 20;
        let isFirstBatch = true;
    
        const loadRecords = async (offset) => {
            const records = await getOccurrences(taxonKey, basisOfRecord, offset);
            renderOccurrences(records, basisOfRecord);
    
            // If it's the first batch of data, initialize the map
            if (isFirstBatch) {
                initMap();
                map.updateSize();
                isFirstBatch = false;
            }
    
            if (offset + limit < maxRequests * limit) {
                // Continue loading more records recursively
                loadRecords(offset + limit);
            }
        };
    
        await loadRecords(0);
    };


    const initMap = () => {
        if (!map) { 
            map = new ol.Map({
                layers: [
                    new ol.layer.Tile({
                        source: new ol.source.OSM()
                    }),
                    new ol.layer.Vector({
                        source: clusterSource,
                        style: clusterStyleFunction
                    })
                ],
                target: 'map',
                view: new ol.View({
                    center: [0, 0],
                    zoom: 2
                })
            });
        }
        
        const extent = vectorSource.getExtent();
        map.getView().fit(extent, map.getSize());
    };

    const renderMap = async () => {
        loading.style.display = "block";
        const taxonKey = await getTaxonKey(scientificName);
        if (!taxonKey) {
            loading.style.display = "none";
            return;
        }
        const basisOfRecord = 'HUMAN_OBSERVATION';
    
        document.getElementById('plant-name').textContent = scientificName;
    
        await fetchRecords(taxonKey, basisOfRecord);
        
        loading.style.display = "none";
    };
    

    renderMap();
});