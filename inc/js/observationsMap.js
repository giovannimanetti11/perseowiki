document.addEventListener("DOMContentLoaded", async function() {
    var map;
    const scientificNameElement = document.getElementById("scientificName");
    const scientificName = scientificNameElement.getAttribute("data-scientific-name");
    console.log("Scientific Name: ", scientificName);

    const recordCount = document.getElementById('record-count');
    const loading = document.getElementById('loading');
    const vectorSource = new ol.source.Vector();

    const getTaxonKey = async (scientificName) => {
        console.log("Fetching taxon key...");
        const response = await fetch(`https://api.gbif.org/v1/species/match?name=${encodeURIComponent(scientificName)}`);
        const data = await response.json();
        console.log("Fetched taxon key: ", data.usageKey);
        return data.usageKey;
    };

    const getOccurrences = async (taxonKey, basisOfRecord, offset) => {
        console.log("Fetching occurrences...");
        let url = `https://api.gbif.org/v1/occurrence/search?taxon_key=${taxonKey}&limit=300&offset=${offset}&basisOfRecord=${basisOfRecord}`;
        const response = await fetch(url);
        const jsonData = await response.json();
        console.log(`Fetched ${jsonData.results.length} records from ${url}`);
        return jsonData.results;
    };

    const renderOccurrences = (occurrences, basisOfRecord) => {
        console.log("Rendering occurrences...");

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
        const occurrences = [];

        for (let i = 0; i < 3; i++) {
            const offset = i * 300;
            const records = await getOccurrences(taxonKey, basisOfRecord, offset);
            occurrences.push(...records);
        }

        renderOccurrences(occurrences, basisOfRecord);
        console.log("Finished fetching and rendering occurrences");

        initMap();
    };

    const initMap = () => {
        console.log("Initializing map...");
        map = new ol.Map({
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                }),
                new ol.layer.Vector({
                    source: vectorSource
                })
            ],
            target: 'map'
        });
    
        const extent = vectorSource.getExtent();
        map.getView().fit(extent, map.getSize());
    
        console.log("Initialized map");
    };

    const renderMap = async () => {
        console.log("Rendering map...");
        loading.style.display = "block";
        const taxonKey = await getTaxonKey(scientificName);
        console.log("Scientific Name: ", scientificName);
        console.log("Taxon Key: ", taxonKey);
        const basisOfRecord = 'HUMAN_OBSERVATION';

        await fetchRecords(taxonKey, basisOfRecord);

        await new Promise(resolve => {
            setTimeout(() => {
                map.updateSize();
                resolve();
            }, 1000);
        });

        loading.style.display = "none";
    };

    renderMap();
});
