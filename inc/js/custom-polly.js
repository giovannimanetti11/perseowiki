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
            console.error('Error in synthesizeSpeech:', err);
            return;
        }

        if (data.AudioStream instanceof ArrayBuffer || data.AudioStream instanceof Uint8Array) {
            const audioBlob = new Blob([data.AudioStream], { type: 'audio/mpeg' });
            const audioUrl = URL.createObjectURL(audioBlob);
            const audio = new Audio(audioUrl);
            audio.play();
        }
    });
}

function handleClick(event) {
    // Check if the clicked element or any of its parents has the 'voice-icon' class.
    let target = event.target;
    while (target !== document && !(target.classList && target.classList.contains('voice-icon'))) {
        target = target.parentNode;
    }

    if (target !== document) {
        const text = target.getAttribute('data-text');
        const language = target.getAttribute('data-language') || 'it';
        synthesizeSpeech(text, language);
    }
}

function init() {
    document.addEventListener('click', handleClick);
}

document.addEventListener('DOMContentLoaded', init);