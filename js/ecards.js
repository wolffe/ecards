document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('ecard_consent')) {
        document.getElementById('ecard_send').disabled = true;

        document.getElementById('ecard_consent').addEventListener('click', () => {
            if (document.getElementById('ecard_consent').checked) {
                document.getElementById('ecard_send').disabled = false;
            } else {
                document.getElementById('ecard_send').disabled = true;
            }
        });
    }
});
