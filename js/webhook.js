
async function sendContact(ev) {
    ev.preventDefault();

    const senderEmail = document
    .getElementById('emailInput').value;
    const senderMessage = document
    .getElementById('messageInput').value;

    const webhookBody = {
    embeds: [{
        title: 'Contact Form Submitted',
        fields: [
        { name: 'Sender', value: senderEmail },
        { name: 'Message', value: senderMessage }
        ]
    }],
    };

    const webhookUrl = 'https://discordapp.com/api/webhooks/989948088368132186/29ZUK8e-NgOkrvXjh7dy8hMJMWrzpJ2AOEIXTAgQOjJetn48gJ3exYw627USQCdlVUbB';

    const response = await fetch(webhookUrl, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(webhookBody),
    });

    if (response.ok) {
    alert('I have received your message!');
    } else {
    alert('There was an error! Try again later!');
    }
}