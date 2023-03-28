const Polly = new AWS.Polly({
    apiVersion: '2016-06-10',
    region: 'eu-west-1',
    credentials: {
      accessKeyId: awsCredentials.accessKeyId,
      secretAccessKey: awsCredentials.secretAccessKey
    }
});

  

function synthesizeSpeech(text, language) {
    const params = {
        OutputFormat: 'mp3',
        Text: text,
        VoiceId: language === 'it' ? 'Carla' : 'Joanna',
        TextType: 'text',
        SampleRate: '22050'
    };

    Polly.synthesizeSpeech(params, (err, data) => {
        if (err) {
            return;
        }
    
        if (data.AudioStream instanceof ArrayBuffer || data.AudioStream instanceof Uint8Array) {
            const audioBlob = new Blob([data.AudioStream], {type: 'audio/mpeg'});
            const audioUrl = URL.createObjectURL(audioBlob);
            const audio = new Audio(audioUrl);
            audio.play();
        }
    });
}



window.onload = function() {
    document.querySelectorAll('.fa-volume-up').forEach((element) => {
        element.addEventListener('click', () => {
            const text = element.getAttribute('data-text');
            const language = element.getAttribute('data-language') || 'it';
            synthesizeSpeech(text, language);
        });
    });
};





